<?php

namespace App\Http\Controllers;

use App\Models\Introducer;
use App\Models\PolicyIntroducer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IntroducerController extends Controller
{
    protected $title = 'Introducers';
    protected $view = 'introducers.';

    public function __construct()
    {
        $this->middleware('permission:introducers.index')->only(['index', 'ajax']);
        $this->middleware('permission:introducers.create')->only(['create']);
        $this->middleware('permission:introducers.store')->only(['store']);
        $this->middleware('permission:introducers.edit')->only(['edit']);
        $this->middleware('permission:introducers.update')->only(['update']);
        $this->middleware('permission:introducers.show')->only(['show']);
        $this->middleware('permission:introducers.destroy')->only(['destroy']);
    }

    public function index()
    {
        if (request()->ajax()) {
            return $this->ajax();
        }

        $title = $this->title;
        $subTitle = 'Manage introducers here';

        return view($this->view . 'index', compact('title', 'subTitle'));
    }

    public function ajax()
    {
        $query = Introducer::query();

        if (request()->filled('filter_type')) {
            $query->where('type', request('filter_type'));
        }

        if (request()->filled('filter_status')) {
            $query->where('status', request('filter_status'));
        }

        if (request()->filled('filter_name')) {
            $query->where(function ($builder) {
                $filter = request('filter_name');
                $likeFilter = "%{$filter}%";

                $builder->where('name', 'LIKE', $likeFilter)
                    ->orWhere('email', 'LIKE', $likeFilter)
                    ->orWhere('contact_number', 'LIKE', $likeFilter)
                    ->orWhereRaw("CONCAT(dial_code, contact_number) LIKE ?", [$likeFilter])
                    ->orWhereRaw("CONCAT(dial_code, ' ', contact_number) LIKE ?", [$likeFilter])
                    ->orWhereRaw("CONCAT('+', dial_code, ' ', contact_number) LIKE ?", [$likeFilter]);
            });
        }

        return datatables()
        ->eloquent($query)
        ->addColumn('full_name', function ($row) {
            return $row->name;
        })
        ->addColumn('contact_number', function ($row) {
            return $row->dial_code . ' ' . $row->contact_number;
        })
        ->editColumn('type', function ($row) {
            return ucfirst($row->type);
        })
        ->editColumn('status', function ($row) {
            return ucfirst($row->status);
        })
        ->addColumn('action', function ($row) {
            $html = '<ul>';

            if (auth()->user()->can('introducers.edit')) {
                $html .= '<li><a href="' . route('introducers.edit', encrypt($row->id)) . '"> Edit </a></li>';
            }

            if (auth()->user()->can('introducers.show')) {
                $html .= '<li><a href="' . route('introducers.show', encrypt($row->id)) . '"> View </a></li>';
            }
            
            $html .= '</ul>';
            return $html;
        })
        ->addIndexColumn()
        ->rawColumns(['action'])
        ->make(true);
    }

    public function create(): View
    {
        $title = $this->title;
        $subTitle = 'Add New Introducer';

        return view($this->view . 'create', compact('title', 'subTitle'));
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'type' => 'required|in:Entity,Individual,entity,individual',
            'name' => 'nullable|string',
            'full_name' => 'nullable|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'email' => 'nullable|email',
            'dial_code' => 'nullable|string',
            'contact_number' => 'nullable|string',

            'contact_person_first_name' => 'array',
            'contact_person_first_name.*' => 'nullable|string',
            'contact_person_middle_name' => 'array',
            'contact_person_middle_name.*' => 'nullable|string',
            'contact_person_last_name' => 'array',
            'contact_person_last_name.*' => 'nullable|string',
            'contact_person_email' => 'array',
            'contact_person_email.*' => 'nullable|email|distinct',
            'contact_person_phone_number_dial_code' => 'array',
            'contact_person_phone_number_dial_code.*' => 'nullable|string',
            'contact_person_phone_number' => 'array',
            'contact_person_phone_number.*' => 'nullable|string',
        ];

        if (strtolower($request->input('type')) === 'entity') {
            $rules['contact_person_first_name.0'] = 'required|string';
            $rules['contact_person_last_name.0'] = 'required|string';
            $rules['contact_person_email.0'] = 'required|email';
            $rules['contact_person_phone_number.0'] = 'required|string';
        }

        $data = $request->validate($rules);

        if (strtolower($data['type']) == 'entity') {
            $data['name'] = isset($data['full_name']) ? $data['full_name'] : '';
        }
        if (array_key_exists('full_name', $data)) {
            unset($data['full_name']);
        }

        $introducer = Introducer::create([
            'type' => $data['type'],
            'name' => $data['name'] ?? null,
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'email' => $data['email'] ?? null,
            'dial_code' => $data['dial_code'] ?? null,
            'contact_number' => $data['contact_number'] ?? null,
        ]);

        $firsts = $request->input('contact_person_first_name', []);
        $middles = $request->input('contact_person_middle_name', []);
        $lasts = $request->input('contact_person_last_name', []);
        $emails = $request->input('contact_person_email', []);
        $dials = $request->input('contact_person_phone_number_dial_code', []);
        $phones = $request->input('contact_person_phone_number', []);

        foreach ($firsts as $idx => $first) {
            $anyFilled = ($first ?? '') !== ''
                || (($lasts[$idx] ?? '') !== '')
                || (($emails[$idx] ?? '') !== '')
                || (($phones[$idx] ?? '') !== '');
            if (!$anyFilled) continue;

            $introducer->contacts()->create([
                'name' => $first ?? null,
                'middle_name' => $middles[$idx] ?? null,
                'last_name' => $lasts[$idx] ?? null,
                'email' => $emails[$idx] ?? null,
                'dial_code' => $dials[$idx] ?? null,
                'contact_number' => $phones[$idx] ?? null,
            ]);
        }

        return redirect()->route('introducers.index')->with('success', 'Introducer created');
    }

    public function edit($id): View
    {
        $title = $this->title;
        $subTitle = 'Edit Introducer';
        $introducer = Introducer::find(decrypt($id));
        $introducer->load('contacts');

        return view($this->view . 'edit', compact('title', 'subTitle', 'introducer'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $rules = [
            'type' => 'required|in:Entity,Individual,entity,individual',
            'name' => 'nullable|string',
            'full_name' => 'nullable|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'email' => 'nullable|email',
            'dial_code' => 'nullable|string',
            'contact_number' => 'nullable|string',

            'contact_person_first_name' => 'array',
            'contact_person_first_name.*' => 'nullable|string',
            'contact_person_middle_name' => 'array',
            'contact_person_middle_name.*' => 'nullable|string',
            'contact_person_last_name' => 'array',
            'contact_person_last_name.*' => 'nullable|string',
            'contact_person_email' => 'array',
            'contact_person_email.*' => 'nullable|email|distinct',
            'contact_person_phone_number_dial_code' => 'array',
            'contact_person_phone_number_dial_code.*' => 'nullable|string',
            'contact_person_phone_number' => 'array',
            'contact_person_phone_number.*' => 'nullable|string',
        ];

        if (strtolower($request->input('type')) === 'entity') {
            $rules['contact_person_first_name.0'] = 'required|string';
            $rules['contact_person_last_name.0'] = 'required|string';
            $rules['contact_person_email.0'] = 'required|email';
            $rules['contact_person_phone_number.0'] = 'required|string';
        }

        $data = $request->validate($rules);

        if (strtolower($data['type']) == 'entity') {
            $data['name'] = isset($data['full_name']) ? $data['full_name'] : '';
        }
        if (array_key_exists('full_name', $data)) {
            unset($data['full_name']);
        }

        $introducer = Introducer::find(decrypt($id));
        $introducer->update([
            'type' => $data['type'],
            'name' => $data['name'] ?? null,
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'email' => $data['email'] ?? null,
            'dial_code' => $data['dial_code'] ?? null,
            'contact_number' => $data['contact_number'] ?? null,
        ]);

        $introducer->contacts()->delete();
        $firsts = $request->input('contact_person_first_name', []);
        $middles = $request->input('contact_person_middle_name', []);
        $lasts = $request->input('contact_person_last_name', []);
        $emails = $request->input('contact_person_email', []);
        $dials = $request->input('contact_person_phone_number_dial_code', []);
        $phones = $request->input('contact_person_phone_number', []);

        foreach ($firsts as $idx => $first) {
            $anyFilled = ($first ?? '') !== ''
                || (($lasts[$idx] ?? '') !== '')
                || (($emails[$idx] ?? '') !== '')
                || (($phones[$idx] ?? '') !== '');
            if (!$anyFilled) continue;

            $introducer->contacts()->create([
                'name' => $first ?? null,
                'middle_name' => $middles[$idx] ?? null,
                'last_name' => $lasts[$idx] ?? null,
                'email' => $emails[$idx] ?? null,
                'dial_code' => $dials[$idx] ?? null,
                'contact_number' => $phones[$idx] ?? null,
            ]);
        }

        return redirect()->route('introducers.index')->with('success', 'Introducer updated');
    }

    public function show($id): View
    {
        $title = $this->title;
        $subTitle = 'View Introducer';
        $introducer = Introducer::find(decrypt($id));
        $introducer->load('contacts');

        return view($this->view . 'view', compact('title', 'subTitle', 'introducer'));
    }
}


