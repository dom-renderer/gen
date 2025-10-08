<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

class PolicyHolderController extends Controller
{
    protected $title = 'Policyholders';
    protected $view = 'policy-holders.';

    public function __construct()
    {
        $this->middleware('permission:policy-holders.index')->only(['index', 'ajax']);
        $this->middleware('permission:policy-holders.create')->only(['create']);
        $this->middleware('permission:policy-holders.store')->only(['store']);
        $this->middleware('permission:policy-holders.edit')->only(['edit']);
        $this->middleware('permission:policy-holders.update')->only(['update']);
        $this->middleware('permission:policy-holders.show')->only(['show']);
        $this->middleware('permission:policy-holders.destroy')->only(['destroy']);
    }

    public function index()
    {
        if (request()->ajax()) {
            return $this->ajax();
        }

        $title = $this->title;
        $subTitle = 'Manage policyholders here';

        return view($this->view . 'index', compact('title', 'subTitle'));
    }

    public function ajax()
    {
        $query = Customer::query();

        if (request()->filled('filter_type')) {
            $query->where('type', request('filter_type'));
        }

        if (request()->filled('filter_status')) {
            $query->where('status', request('filter_status'));
        }

        if (request()->filled('filter_gender')) {
            $query->where('gender', request('filter_gender'));
        }

        if (request()->filled('filter_name')) {
            $query->where(function ($builder) {
                $filter = request('filter_name');
                $likeFilter = "%{$filter}%";

                $builder->where('name', 'LIKE', $likeFilter)
                    ->orWhere('email', 'LIKE', $likeFilter)
                    ->orWhere('phone_number', 'LIKE', $likeFilter)
                    ->orWhereRaw("CONCAT(dial_code, phone_number) LIKE ?", [$likeFilter])
                    ->orWhereRaw("CONCAT(dial_code, ' ', phone_number) LIKE ?", [$likeFilter])
                    ->orWhereRaw("CONCAT('+', dial_code, ' ', phone_number) LIKE ?", [$likeFilter]);
            });
        }

        return datatables()
        ->eloquent($query)
        ->addColumn('full_name', function ($row) {
            return $row->name;
        })
        ->addColumn('phone_number', function ($row) {
            return $row->full_phone;
        })
        ->editColumn('type', function ($row) {
            return ucfirst($row->type);
        })
        ->editColumn('gender', function ($row) {
            return ucfirst($row->gender);
        })
        ->editColumn('status', function ($row) {
            return ucfirst($row->status);
        })
        ->editColumn('address', function ($row) {
            $address = $row->address_line_1;

            if ($row->city) {
                $address .= ' ' . $row->city;
            }

            if ($row->zipcode) {
                $address .= ' ' . $row->zipcode;
            }

            if ($row->country) {
                $address .= ' ' . $row->country;
            }            

            return $address;
        })
        ->addColumn('action', function ($row) {
            $html = '';
            
            if (auth()->user()->can('policy-holders.show')) {
                $html .= '<ul>
                    <li><a href="' . route('policy-holders.show', encrypt($row->id)) . '"> View </a></li>
                </ul>';
            }
            
            if (auth()->user()->can('policy-holders.edit')) {
                $html .= '<ul>
                    <li><a href="' . route('policy-holders.edit', encrypt($row->id)) . '"> Edit </a></li>
                </ul>';
            }
            
            if (auth()->user()->can('policy-holders.destroy')) {
                $html .= '<ul>
                    <li><a class="delete-btn" data-id="' . $row->id . '" > Delete </a></li>
                </ul>';
            }
            
            $html .= '';
            return $html;
        })
        ->addIndexColumn()
        ->rawColumns(['action'])
        ->make(true);
    }

    public function create()
    {
        $title = $this->title;
        $subTitle = 'Add New Policyholder';
        $countries = Country::pluck('name', 'id');
        
        return view($this->view . 'create', compact('title', 'subTitle', 'countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:individual,entity',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email',
            'dial_code' => 'required|string|max:10',
            'phone_number' => ['required', 'regex:/^[0-9]+$/', 'max:15', 'unique:customers,phone_number'],
            'gender' => 'required_if:type,individual',
            'dob' => 'required|date|before:today',
            'place_of_birth' => 'required|string',
            'address_line_1' => 'required|string',
            'address_line_2' => 'nullable|string',
            'zipcode' => 'required|string|max:20',
            'status' => 'required|string',
            'status_name' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->only([
                'type', 'name', 'email',
                'dial_code', 'phone_number', 'gender', 'dob', 'place_of_birth',
                'address_line_1', 'address_line_2', 'country',
                'city', 'zipcode', 'status', 'status_name'
            ]);

            $customer = Customer::create($data);

            \DB::table('model_has_roles')->insert([
                'role_id' => 2,
                'model_type' => 'App\Models\Customer',
                'model_id' => $customer->id
            ]);

            DB::commit();
            return redirect()->route('policy-holders.index')->with('success', 'Policyholder created successfully.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error creating policyholder: ' . $e->getMessage());
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Something went wrong.');
        }
    }

    public function show(string $id)
    {
        $policyHolder = Customer::with(['country', 'state', 'city'])->findOrFail(decrypt($id));

        $title = $this->title;
        $subTitle = 'Policyholder Details';
        return view($this->view . 'view', compact('title', 'subTitle', 'policyHolder'));
    }

    public function edit(string $id)
    {
        $policyHolder = Customer::findOrFail(decrypt($id));
        $title = $this->title;
        $subTitle = 'Edit Policyholder';
        $countries = Country::pluck('name', 'id');
        $states = State::where('country_id', $policyHolder->country_id)->pluck('name', 'id');
        $cities = City::where('state_id', $policyHolder->state_id)->pluck('name', 'id');

        return view($this->view . 'edit', compact('title', 'subTitle', 'policyHolder', 'countries', 'states', 'cities'));
    }

    public function update(Request $request, string $id)
    {
        $policyHolder = Customer::findOrFail(decrypt($id));
        
        $request->validate([
            'type' => 'required|in:individual,entity',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,' . $policyHolder->id,
            'dial_code' => 'required|string|max:10',
            'phone_number' => ['required', 'regex:/^[0-9]+$/', 'max:15', 'unique:customers,phone_number,' . $policyHolder->id],
            'gender' => 'required_if:type,individual',
            'dob' => 'required|date|before:today',
            'place_of_birth' => 'required|string',
            'address_line_1' => 'required|string',
            'address_line_2' => 'nullable|string',
            'zipcode' => 'required|string|max:20',
            'status' => 'required|string',
            'status_name' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->only([
                'type', 'name', 'email',
                'dial_code', 'phone_number', 'gender', 'dob', 'place_of_birth',
                'address_line_1', 'address_line_2', 'country',
                'city', 'zipcode', 'status', 'status_name'
            ]);

            $policyHolder->update($data);

            DB::commit();
            return redirect()->route('policy-holders.index')->with('success', 'Policyholder updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Something went wrong.');
        }
    }

    public function destroy(string $id)
    {
        $policyHolder = Customer::findOrFail($id);
        $policyHolder->delete();
        return response()->json(['success' => 'Policyholder deleted successfully.']);
    }
}
