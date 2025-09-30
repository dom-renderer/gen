<?php

namespace App\Helpers;

use App\Models\PolicyHolder;
use App\Models\PolicyInsuredLifeInformation;
use \Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Customer;
use App\Models\Document;
use App\Models\User;
use Carbon\Carbon;

class Helper {

    public static $status = ['DRAFT', 'ACTIVE', 'SURRENDERED', 'TERMINATED', '1035 EXCHANGE', 'LAPSE', 'PROSPECT', 'UNDER REVIEW'];

    public static $defaulDialCode = 'us';

    public static $individualStates = ['single' => 'Single', 'married' => 'Married', 'divorced' => 'Divorced', 'separated' => 'Separated', 'other' => 'Other'];
    public static $entityStatuses = [ 'corporation' => 'Corporation', 'llc' => 'LLC', 'trust' => 'Trust', 'partnership' => 'Partnership', 'foundation' => 'Foundation', 'other' => 'Other'];

    public static function title ($title = '') {
        if (!empty($title)) {
            return $title;
        } else if ($name = DB::table('settings')->first()?->name) {
            return $name;
        } else {
            return env('APP_NAME', '');
        }
    }

    public static function logo () {
        if ($name = DB::table('settings')->first()?->logo) {
            return url("settings-media/{$name}");
        } else {
            return url('assets/images/logo.png');
        }
    }

    public static function favicon () {
        if ($name = DB::table('settings')->first()?->favicon) {
            return url("settings-media/{$name}");
        } else {
            return url('assets/images/favicon.ico');
        }
    }

    public static function bgcolor ($bg = null) {
        if (!empty($bg)) {
            return $bg;
        } else if ($color = DB::table('settings')->first()?->theme_color) {
            return $color;
        } else {
            return '#3a082f';
        }
    }

    public function getCountries(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $limit = 10;
    
        $query = Country::query();
    
        if (!empty($queryString)) {
            $query->where('name', 'LIKE', "%{$queryString}%");
        }
    
        $data = $query->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name
            ];
        });

        return response()->json([
            'items' => $response->reverse()->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public function getStatesByCountry(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $limit = 10;
    
        $query = State::query()
        ->where('country_id', request('country_id'));
    
        if (!empty($queryString)) {
            $query->where('name', 'LIKE', "%{$queryString}%");
        }
    
        $data = $query->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name
            ];
        });

        return response()->json([
            'items' => $response->reverse()->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public function getCitiesByState(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $limit = 10;
    
        $query = City::query()
        ->when(is_numeric($request->state_id) && $request->state_id > 0, function ($innerBuilder) {
            $innerBuilder->where('state_id', request('state_id'));
        })
        ->when(is_numeric($request->country_id) && $request->country_id > 0, function ($innerBuilder) {
            $innerBuilder->where('country_id', request('country_id'));
        });
    
        if (!empty($queryString)) {
            $query->where('name', 'LIKE', "%{$queryString}%");
        }
    
        $data = $query->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name
            ];
        });

        return response()->json([
            'items' => $response->reverse()->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public function getDocuments(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $limit = 10;
    
        $query = Document::query()
        ->where('status', $request->status);
    
        if (!empty($queryString)) {
            $query->where('title', 'LIKE', "%{$queryString}%");
        }
    
        $data = $query->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->title
            ];
        });

        return response()->json([
            'items' => $response->reverse()->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }
    
    public function getUsers(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $roles = $request->input('roles', null);
        $page = $request->input('page', 1);
        $includeUserData = $request->input('includeUserData', false);
        $limit = 10;
    
        $query = User::query();
    
        if (!empty($queryString)) {
            $query->where('name', 'LIKE', "%{$queryString}%");
        }

        if (!empty($roles)) {
            if (is_string($roles) && $roles == '*') {
                $query->whereHas('roles');
            } else {
                $roles = is_string($roles) ? explode(',', $roles) : (is_array($roles) ? $roles : []);
                $query->whereHas('roles', fn  ($builder) => $builder->whereIn('name', $roles));
            }
        }

        $data = $query->orderBy('name', 'ASC')->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) use ($includeUserData) {
            $result = [
                'id' => $item->id,
                'text' => $item->name
            ];
            
            if ($includeUserData) {
                $result['user'] = $item;
            }
            
            return $result;
        });

        return response()->json([
            'items' => $response->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public function getIntroducers(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $limit = 10;

        $query = \App\Models\Introducer::query();

        if (!empty($queryString)) {
            $query->where(function ($builder) use ($queryString) {
                $builder->where('name', 'LIKE', "%{$queryString}%")
                    ->orWhere('email', 'LIKE', "%{$queryString}%")
                    ->orWhere('contact_number', 'LIKE', "%{$queryString}%");
            });
        }

        $data = $query->orderBy('name', 'ASC')->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name
            ];
        });

        if ($page == 1) {
            $response->prepend([
                'id' => 'ADD_NEW_INTRODUCER',
                'text' => 'Add New Introducer'
            ]);
        }

        return response()->json([
            'items' => $response->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public function getIntroducerDetails(Request $request)
    {
        $request->validate([
            'introducer_id' => 'required|integer'
        ]);

        $introducer = \App\Models\Introducer::with('contacts')->find($request->introducer_id);
        if (!$introducer) {
            return response()->json(['status' => false], 404);
        }

        $introIso2 = self::getIso2ByDialCode($introducer->dial_code ?? null);
        $contacts = $introducer->contacts->map(function($c){
            $c->dial_iso2 = Helper::getIso2ByDialCode($c->dial_code ?? null);
            return $c;
        });

        return response()->json([
            'status' => true,
            'introducer' => array_merge($introducer->toArray(), ['dial_iso2' => $introIso2]),
            'contacts' => $contacts
        ]);
    }

    public function getInsureds(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $policy = $request->input('policy_id', null);
        $limit = 10;
    
        $query = PolicyInsuredLifeInformation::query();
    
        if (!empty($policy)) {
            $query->where('policy_id', $policy);
        } else {
            $query->where('policy_id', 'xyz');
        }

        if (!empty($queryString)) {
            $query->where('name', 'LIKE', "%{$queryString}%");
        }

        $data = $query->orderBy('name', 'ASC')->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) {
            $result = [
                'id' => $item->id,
                'text' => $item->name
            ];
            
            return $result;
        });

        return response()->json([
            'items' => $response->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public function getHolders(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $addNewOption = $request->input('addNewOption', 0);
        $includeUserData = $request->input('includeUserData', false);
        $limit = 10;

        if ($request->filled('policy_id')) {
            $query = PolicyHolder::where('policy_id', $request->policy_id)
            ->where(function ($builder) use ($queryString) {
                $builder->where('name', 'LIKE', "%{$queryString}%")
                ->orWhere('first_name', 'LIKE', "%{$queryString}%")
                ->orWhere('middle_name', 'LIKE', "%{$queryString}%")
                ->orWhere('last_name', 'LIKE', "%{$queryString}%");
            });
        } else {
            $query = PolicyHolder::where('id', 0);
        }

        $data = $query->orderBy('name', 'ASC')->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) use ($includeUserData) {
            $result = [
                'id' => $item->id,
                'text' => $item->type == 'Individual' ? ($item->first_name . ' ' . $item->middle_name . ' ' . $item->last_name) : ($item->name)
            ];
            
            if ($includeUserData) {
                $result['user'] = $item;
                $result['alternate_dial_code_iso'] = Helper::getIso2ByDialCode($item->alternate_dial_code);
            }
            
            return $result;
        });

        if ($addNewOption === '1' && $page == 1) {
            if ($response->count() > 0) {
                $response->push([
                    'id' => 'ADD_NEW_USER',
                    'text' => 'Add New Policyholder'
                ])->unique();
            } else {
                $response->push([
                    'id' => 'ADD_NEW_USER',
                    'text' => 'Add New Policyholder'
                ]);
            }
        }

        return response()->json([
            'items' => $response->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public static function getIso2ByDialCode($dialCode = null) {
        if (empty(trim($dialCode))) {
            $dialCode = '41';
        }

        $dialCode = trim(str_replace('+', '', $dialCode));
        return strtolower(Country::select('iso2')->where('phonecode', "+{$dialCode}")->orWhere('phonecode', $dialCode)->first()->iso2 ?? 'ch');
    }

    private static function formatTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        return sprintf('%02dh %02dm %02ds', $hours, $minutes, $remainingSeconds);
    }

    public static function number_format($number)
    {
        return '$' . number_format($number, 2);
    }

    public static function generateCaseNumber() {
        $nextId = DB::table('policies')->max('id') + 1;
        return 'CASE-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }

    public static function allCountries() {
        return Country::select('name')->pluck('name')->toArray();
    }

    public static function allCountriesHTML() {
        $html = '';

        foreach (self::allCountries() as $cntry) {
           $html .= '<option value="' . $cntry . '" > ' . $cntry . ' </option>';
        }

        return $html;
    }

    public static function silentDcrypt($value)
    {
        if (is_null($value)) {
            return null;
        }

        try {
            if (base64_encode(base64_decode($value, true)) === $value) {
                return \Illuminate\Support\Facades\Crypt::decrypt($value);
            } else {
                return $value;
            }
        } catch (\Exception $e) {
            return $value;
        }
    }

    public static function getCompletion($data)
    {
        $trueCount = count(array_filter($data, fn($value) => $value === true));
        $percentage = ($trueCount / count($data)) * 100;
        return round($percentage, -1);
    }
}