<?php

namespace App\Http\Controllers;

use App\Models\PolicyInsuredLifeInformation;
use App\Models\PolicyCountryOfTaxResidence;
use App\Models\PolicyFeeSummaryExternal;
use App\Models\PolicyFeeSummaryInternal;
use App\Models\InvestmentDedicatedFund;
use App\Models\PolicyEconomicProfile;
use App\Models\DownloadableDocument;
use App\Models\PolicyCommunication;
use App\Models\UploadableDocument;
use App\Models\PolicyCaseFileNote;
use App\Models\PolicyBeneficiary;
use App\Models\InvestmentAdvisor;
use App\Models\PolicyController;
use App\Models\PolicyIntroducer;
use App\Models\PolicyDocument;
use App\Models\PolicyPremium;
use App\Models\PolicyOnGoing;
use App\Models\PolicyManager;
use App\Models\PolicyHolder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\Liklihood;
use App\Models\Custodian;
use App\Jobs\MailboxJob;
use App\Helpers\Helper;
use App\Models\Tooltip;
use App\Models\Policy;
use App\Models\User;

class CaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:cases.index')->only(['index', 'ajax']);
        $this->middleware('permission:cases.create')->only(['create']);
        $this->middleware('permission:cases.submission')->only(['autoSave', 'store', 'submission']);
        $this->middleware('permission:cases.edit')->only(['edit']);
        $this->middleware('permission:cases.case-manager')->only(['caseManager']);
    }

    public function create(Request $request, $id = null)
    {
        $user = auth()->id();
        $policy = null;

        if (!empty($id)) {
            try {
                $id = decrypt($id);
                $id = explode('***', $id);

                if (count($id) == 3 && $id[2] == 'sha-2') {
                    if (!empty($id[1]) && Policy::find($id[1])) {
                        $policy = Policy::find($id[1]);
                    } else {
                        $policy = new Policy();
                        $policy->opening_date = now();
                        $policy->added_by = $user;
                        $policy->liklihood = Liklihood::first()->id ?? null;
                        $policy->save();

                        foreach (User::role('client-service-team')->get() as $user) {
                            PolicyManager::create([
                                'policy_id' => $policy->id,
                                'manager_id' => $user->id
                            ]);
                        }

                        MailboxJob::dispatch($policy, []);

                        session()->put(['new_policy' => [
                            'user_id' => $user,
                            'policy_id' => $policy->id
                        ]]);

                        return redirect()->route('cases.create', encrypt($user . '***' . $policy->id . '***sha-2'));
                    }
                }

            } catch (\Exception $e) {
                return redirect()->route('cases.index');
            }
        } else {
            return redirect()->route('cases.index');
        }

        $title = 'Case Management';
        $subTitle = 'Add New Case';

        $progressA = 0;
        $progressB = 0;
        $progressC = 0;
        $progressD = 0;
        $progressE = 0;
        $progressF = 0;
        $progressG = 0;
        $progressH = 0;

        return view('cases.create.index', compact('title', 'subTitle', 'policy',
        'progressA','progressB','progressC','progressD','progressE','progressF','progressG','progressH'
        ));
    }

    public function edit(Request $request, $id) {

        if (!empty($id)) {
            try {
                $id = decrypt($id);
                $policy = Policy::find($id);
            } catch (\Exception $e) {
                return redirect()->route('cases.index');
            }
        } else {
            return redirect()->route('cases.index');
        }

        $title = 'Case Management';
        $subTitle = 'Edit Case';

        $progressA = Helper::getCompletion([
            PolicyIntroducer::where('policy_id', $id)->exists(),
            PolicyHolder::where('policy_id', $id)->exists(),
            PolicyInsuredLifeInformation::where('policy_id', $id)->exists(),
            PolicyBeneficiary::where('policy_id', $id)->exists(),
            $policy->investment_advisor_manager_applicable ? InvestmentAdvisor::where('policy_id', $id)->exists() : true,
            $policy->idf_name_applicable ? InvestmentDedicatedFund::where('policy_id', $id)->where('user_type', 'name')->exists() : true,
            $policy->idf_manager_applicable ? InvestmentDedicatedFund::where('policy_id', $id)->where('user_type', 'manager')->exists() : true,
            $policy->custodian_applicable ? Custodian::where('policy_id', $id)->exists() : true
        ]);

        $completionFlags = [];

        try {
            $policyWithRelations = Policy::with(['holders'])->find($policy->id);

            if ($policyWithRelations && $policyWithRelations->holders) {
                foreach ($policyWithRelations->holders as $holder) {
                    $type = strtolower(trim((string) $holder->type));

                    $completionFlags[] = !empty($type);

                    if ($type === 'individual') {
                        $completionFlags[] = !empty(trim((string) $holder->first_name));
                        $completionFlags[] = !empty(trim((string) $holder->middle_name));
                        $completionFlags[] = !empty(trim((string) $holder->last_name));
                    } else {
                        $completionFlags[] = !empty(trim((string) $holder->name));
                    }

                    $completionFlags[] = !empty(trim((string) $holder->place_of_birth));
                    $completionFlags[] = !empty($holder->dob);

                    $completionFlags[] = !empty(trim((string) $holder->country));
                    $completionFlags[] = !empty(trim((string) $holder->city));
                    $completionFlags[] = !empty(trim((string) $holder->zipcode));
                    $completionFlags[] = !empty(trim((string) $holder->address_line_1));

                    $statusValue = strtolower(trim((string) $holder->status));
                    $completionFlags[] = !empty($statusValue);
                    if ($statusValue === 'other') {
                        $completionFlags[] = !empty(trim((string) $holder->status_name));
                    }

                    $completionFlags[] = !empty(trim((string) $holder->national_country_of_registration));

                    $completionFlags[] = !empty(trim((string) $holder->gender));

                    $completionFlags[] = !empty(trim((string) $holder->country_of_legal_residence));

                    $taxCount = \App\Models\PolicyCountryOfTaxResidence::where('policy_id', $policyWithRelations->id)
                        ->where('eloquent', PolicyHolder::class)
                        ->where('eloquent_id', $holder->id)
                        ->count();
                    $completionFlags[] = $taxCount > 0;

                    $completionFlags[] = !empty(trim((string) $holder->passport_number));
                    $completionFlags[] = !empty(trim((string) $holder->country_of_issuance));

                    $completionFlags[] = !empty(trim((string) $holder->tin));
                    $completionFlags[] = !empty(trim((string) $holder->lei));

                    $completionFlags[] = !empty(trim((string) $holder->email));
                }
            }
        } catch (\Throwable $e) {

        }

        $progressB = !empty($completionFlags) ? Helper::getCompletion($completionFlags) : 0;

        $cFlags = [];
        try {
            $insuredLives = PolicyInsuredLifeInformation::where('policy_id', $policy->id)->get();
            foreach ($insuredLives as $life) {
                $cFlags[] = !empty(trim((string) $life->name));
                $cFlags[] = !empty(trim((string) $life->place_of_birth));
                $cFlags[] = !empty($life->date_of_birth);
                $cFlags[] = !empty(trim((string) $life->address));
                $cFlags[] = !empty(trim((string) $life->country));
                $cFlags[] = !empty(trim((string) $life->city));
                $cFlags[] = !empty(trim((string) $life->zip));
                $cFlags[] = !empty(trim((string) $life->status));
                $cFlags[] = !empty(trim((string) $life->smoker_status));
                $cFlags[] = !empty(trim((string) $life->nationality));
                $cFlags[] = !empty(trim((string) $life->gender));
                $cFlags[] = !empty(trim((string) $life->country_of_legal_residence));
                $cFlags[] = PolicyCountryOfTaxResidence::where('policy_id', $policy->id)
                    ->where('eloquent', PolicyInsuredLifeInformation::class)
                    ->where('eloquent_id', $life->id)->count() > 0;
                $cFlags[] = !empty(trim((string) $life->passport_number));
                $cFlags[] = !empty(trim((string) $life->country_of_issuance));
                $cFlags[] = !empty(trim((string) $life->relationship_to_policyholder));
                $cFlags[] = !empty(trim((string) $life->email));
            }
        } catch (\Throwable $e) {}
        $progressC = !empty($cFlags) ? Helper::getCompletion($cFlags) : 0;

        $dFlags = [];
        try {
            $beneficiaries = PolicyBeneficiary::where('policy_id', $policy->id)->get();
            foreach ($beneficiaries as $bene) {
                $dFlags[] = !empty($bene->insured_life_id);
                $dFlags[] = !empty(trim((string) $bene->name));
                $dFlags[] = !empty(trim((string) $bene->place_of_birth));
                $dFlags[] = !empty($bene->date_of_birth);
                $dFlags[] = !empty(trim((string) $bene->address));
                $dFlags[] = !empty(trim((string) $bene->country));
                $dFlags[] = !empty(trim((string) $bene->city));
                $dFlags[] = !empty(trim((string) $bene->zip));
                $dFlags[] = !empty(trim((string) $bene->status));
                $dFlags[] = !empty(trim((string) $bene->smoker_status));
                $dFlags[] = !empty(trim((string) $bene->nationality));
                $dFlags[] = !empty(trim((string) $bene->gender));
                $dFlags[] = !empty(trim((string) $bene->country_of_legal_residence));
                $dFlags[] = PolicyCountryOfTaxResidence::where('policy_id', $policy->id)
                    ->where('eloquent', PolicyBeneficiary::class)
                    ->where('eloquent_id', $bene->id)->count() > 0;
                $dFlags[] = !empty(trim((string) $bene->passport_number));
                $dFlags[] = !empty(trim((string) $bene->country_of_issuance));
                $dFlags[] = !empty(trim((string) $bene->relationship_to_policyholder));
                $dFlags[] = !empty(trim((string) $bene->email));
                $dFlags[] = isset($bene->beneficiary_death_benefit_allocation) && $bene->beneficiary_death_benefit_allocation > 0;
                $dFlags[] = !empty(trim((string) $bene->designation_of_beneficiary));
                $dFlags[] = !empty(trim((string) $bene->dial_code));
                $dFlags[] = !empty(trim((string) $bene->phone_number));
            }
        } catch (\Throwable $e) {}
        $progressD = !empty($dFlags) ? Helper::getCompletion($dFlags) : 0;

        $eFlags = [];
        try {
            $docs = PolicyDocument::where('policy_id', $policy->id)->get();
            foreach ($docs as $doc) {
                $eFlags[] = (int)($doc->uploaded ?? 0) === 1;
                $eFlags[] = isset($doc->has_expiry_date);
                if (($doc->has_expiry_date ?? 0) == 1) {
                    $eFlags[] = !empty($doc->expiry_date);
                }
            }
        } catch (\Throwable $e) {}
        $progressE = !empty($eFlags) ? Helper::getCompletion($eFlags) : 0;

        $fFlags = [];
        try {
            $eco = PolicyEconomicProfile::where('policy_id', $policy->id)->first();
            if ($eco) {
                $fFlags[] = !empty($eco->purpose_of_policy_and_structure);
                $fFlags[] = !empty(trim((string) $eco->additional_details));
                $fFlags[] = !empty(trim((string) $eco->estimated_networth));
                $fFlags[] = !empty(trim((string) $eco->source_of_wealth_for_policy));
                $fFlags[] = !empty(trim((string) $eco->distribution_strategy_during_policy_lifetime));
                $fFlags[] = !empty(trim((string) $eco->distribution_strategy_post_death_payout));
                $fFlags[] = !empty(trim((string) $eco->known_triggers_for_policy_exit_or_surrender));
            }

            $prem = PolicyPremium::where('policy_id', $policy->id)->first();
            if ($prem) {
                $fFlags[] = ($prem->proposed_premium_amount ?? 0) > 0;
                $fFlags[] = ($prem->final_premium_amount ?? 0) > 0;
                $fFlags[] = !empty(trim((string) $prem->premium_frequency));
            }

            $fsi = PolicyFeeSummaryInternal::where('policy_id', $policy->id)->first();
            if ($fsi) {
                $fFlags[] = true;
            }

            $fse = PolicyFeeSummaryExternal::where('policy_id', $policy->id)->first();
            if ($fse) {
                $fFlags[] = true;
            }

            $ongoing = PolicyOnGoing::where('policy_id', $policy->id)->first();
            if ($ongoing) {
                $fFlags[] = !empty(trim((string) $ongoing->portfolio_change));
                $fFlags[] = !empty($ongoing->date_of_change_portfolio);
                $fFlags[] = !empty(trim((string) $ongoing->idf_change));
                $fFlags[] = !empty($ongoing->date_of_change_idf);
                $fFlags[] = !empty(trim((string) $ongoing->transfer_change));
                $fFlags[] = !empty($ongoing->date_of_change_transfer);
                $fFlags[] = !empty(trim((string) $ongoing->decision));
            }
        } catch (\Throwable $e) {}
        $progressF = !empty($fFlags) ? Helper::getCompletion($fFlags) : 0;

        $gFlags = [];
        try {
            $comms = PolicyCommunication::where('policy_id', $policy->id)->get();
            foreach ($comms as $c) {
                $gFlags[] = !empty($c->date);
                $gFlags[] = !empty(trim((string) $c->type));
                $gFlags[] = !empty(trim((string) $c->summary_of_discussion));
                $gFlags[] = !empty(trim((string) $c->action_taken_or_next_step));
            }

            $notes = PolicyCaseFileNote::where('policy_id', $policy->id)->get();
            foreach ($notes as $n) {
                $gFlags[] = !empty($n->date);
                $gFlags[] = !empty(trim((string) $n->noted_by));
                $gFlags[] = !empty(trim((string) $n->notes));
            }
        } catch (\Throwable $e) {}
        $progressG = !empty($gFlags) ? Helper::getCompletion($gFlags) : 0;

        $progressH = Arr::random(range(10, 100, 10));

        return view('cases.create.index', compact('title', 'subTitle', 'policy', 
            'progressA','progressB','progressC','progressD','progressE','progressF','progressG','progressH'
        ));
    }

    public function show(Request $request, $id) {

        if (!empty($id)) {
            try {
                $id = decrypt($id);
                $policy = Policy::find($id);
            } catch (\Exception $e) {
                return redirect()->route('cases.index');
            }
        } else {
            return redirect()->route('cases.index');
        }

        $title = 'Case Management';
        $subTitle = 'View Case';

        $progressA = Helper::getCompletion([
            PolicyIntroducer::where('policy_id', $id)->exists(),
            PolicyHolder::where('policy_id', $id)->exists(),
            PolicyInsuredLifeInformation::where('policy_id', $id)->exists(),
            PolicyBeneficiary::where('policy_id', $id)->exists(),
            $policy->investment_advisor_manager_applicable ? InvestmentAdvisor::where('policy_id', $id)->exists() : true,
            $policy->idf_name_applicable ? InvestmentDedicatedFund::where('policy_id', $id)->where('user_type', 'name')->exists() : true,
            $policy->idf_manager_applicable ? InvestmentDedicatedFund::where('policy_id', $id)->where('user_type', 'manager')->exists() : true,
            $policy->custodian_applicable ? Custodian::where('policy_id', $id)->exists() : true
        ]);
        $completionFlags = [];

        try {
            $policyWithRelations = Policy::with(['holders'])->find($policy->id);

            if ($policyWithRelations && $policyWithRelations->holders) {
                foreach ($policyWithRelations->holders as $holder) {
                    $type = strtolower(trim((string) $holder->type));

                    $completionFlags[] = !empty($type);

                    if ($type === 'individual') {
                        $completionFlags[] = !empty(trim((string) $holder->first_name));
                        $completionFlags[] = !empty(trim((string) $holder->middle_name));
                        $completionFlags[] = !empty(trim((string) $holder->last_name));
                    } else {
                        $completionFlags[] = !empty(trim((string) $holder->name));
                    }

                    $completionFlags[] = !empty(trim((string) $holder->place_of_birth));
                    $completionFlags[] = !empty($holder->dob);

                    $completionFlags[] = !empty(trim((string) $holder->country));
                    $completionFlags[] = !empty(trim((string) $holder->city));
                    $completionFlags[] = !empty(trim((string) $holder->zipcode));
                    $completionFlags[] = !empty(trim((string) $holder->address_line_1));

                    $statusValue = strtolower(trim((string) $holder->status));
                    $completionFlags[] = !empty($statusValue);
                    if ($statusValue === 'other') {
                        $completionFlags[] = !empty(trim((string) $holder->status_name));
                    }

                    $completionFlags[] = !empty(trim((string) $holder->national_country_of_registration));

                    $completionFlags[] = !empty(trim((string) $holder->gender));

                    $completionFlags[] = !empty(trim((string) $holder->country_of_legal_residence));

                    $taxCount = PolicyCountryOfTaxResidence::where('policy_id', $policyWithRelations->id)
                        ->where('eloquent', PolicyHolder::class)
                        ->where('eloquent_id', $holder->id)
                        ->count();
                    $completionFlags[] = $taxCount > 0;

                    $completionFlags[] = !empty(trim((string) $holder->passport_number));
                    $completionFlags[] = !empty(trim((string) $holder->country_of_issuance));

                    $completionFlags[] = !empty(trim((string) $holder->tin));
                    $completionFlags[] = !empty(trim((string) $holder->lei));

                    $completionFlags[] = !empty(trim((string) $holder->email));
                }
            }
        } catch (\Throwable $e) {

        }

        $progressB = !empty($completionFlags) ? Helper::getCompletion($completionFlags) : 0;

        $cFlags = [];
        try {
            $insuredLives = PolicyInsuredLifeInformation::where('policy_id', $policy->id)->get();
            foreach ($insuredLives as $life) {
                $cFlags[] = !empty(trim((string) $life->name));
                $cFlags[] = !empty(trim((string) $life->place_of_birth));
                $cFlags[] = !empty($life->date_of_birth);
                $cFlags[] = !empty(trim((string) $life->address));
                $cFlags[] = !empty(trim((string) $life->country));
                $cFlags[] = !empty(trim((string) $life->city));
                $cFlags[] = !empty(trim((string) $life->zip));
                $cFlags[] = !empty(trim((string) $life->status));
                $cFlags[] = !empty(trim((string) $life->smoker_status));
                $cFlags[] = !empty(trim((string) $life->nationality));
                $cFlags[] = !empty(trim((string) $life->gender));
                $cFlags[] = !empty(trim((string) $life->country_of_legal_residence));
                $cFlags[] = PolicyCountryOfTaxResidence::where('policy_id', $policy->id)
                    ->where('eloquent', PolicyInsuredLifeInformation::class)
                    ->where('eloquent_id', $life->id)->count() > 0;
                $cFlags[] = !empty(trim((string) $life->passport_number));
                $cFlags[] = !empty(trim((string) $life->country_of_issuance));
                $cFlags[] = !empty(trim((string) $life->relationship_to_policyholder));
                $cFlags[] = !empty(trim((string) $life->email));
            }
        } catch (\Throwable $e) {}
        $progressC = !empty($cFlags) ? Helper::getCompletion($cFlags) : 0;

        $dFlags = [];
        try {
            $beneficiaries = PolicyBeneficiary::where('policy_id', $policy->id)->get();
            foreach ($beneficiaries as $bene) {
                $dFlags[] = !empty($bene->insured_life_id);
                $dFlags[] = !empty(trim((string) $bene->name));
                $dFlags[] = !empty(trim((string) $bene->place_of_birth));
                $dFlags[] = !empty($bene->date_of_birth);
                $dFlags[] = !empty(trim((string) $bene->address));
                $dFlags[] = !empty(trim((string) $bene->country));
                $dFlags[] = !empty(trim((string) $bene->city));
                $dFlags[] = !empty(trim((string) $bene->zip));
                $dFlags[] = !empty(trim((string) $bene->status));
                $dFlags[] = !empty(trim((string) $bene->smoker_status));
                $dFlags[] = !empty(trim((string) $bene->nationality));
                $dFlags[] = !empty(trim((string) $bene->gender));
                $dFlags[] = !empty(trim((string) $bene->country_of_legal_residence));
                $dFlags[] = PolicyCountryOfTaxResidence::where('policy_id', $policy->id)
                    ->where('eloquent', PolicyBeneficiary::class)
                    ->where('eloquent_id', $bene->id)->count() > 0;
                $dFlags[] = !empty(trim((string) $bene->passport_number));
                $dFlags[] = !empty(trim((string) $bene->country_of_issuance));
                $dFlags[] = !empty(trim((string) $bene->relationship_to_policyholder));
                $dFlags[] = !empty(trim((string) $bene->email));
                $dFlags[] = isset($bene->beneficiary_death_benefit_allocation) && $bene->beneficiary_death_benefit_allocation > 0;
                $dFlags[] = !empty(trim((string) $bene->designation_of_beneficiary));
                $dFlags[] = !empty(trim((string) $bene->dial_code));
                $dFlags[] = !empty(trim((string) $bene->phone_number));
            }
        } catch (\Throwable $e) {}
        $progressD = !empty($dFlags) ? Helper::getCompletion($dFlags) : 0;

        $eFlags = [];
        try {
            $docs = PolicyDocument::where('policy_id', $policy->id)->get();
            foreach ($docs as $doc) {
                $eFlags[] = (int)($doc->uploaded ?? 0) === 1;
                $eFlags[] = isset($doc->has_expiry_date);
                if (($doc->has_expiry_date ?? 0) == 1) {
                    $eFlags[] = !empty($doc->expiry_date);
                }
            }
        } catch (\Throwable $e) {}
        $progressE = !empty($eFlags) ? Helper::getCompletion($eFlags) : 0;

        $fFlags = [];
        try {
            $eco = PolicyEconomicProfile::where('policy_id', $policy->id)->first();
            if ($eco) {
                $fFlags[] = !empty($eco->purpose_of_policy_and_structure);
                $fFlags[] = !empty(trim((string) $eco->additional_details));
                $fFlags[] = !empty(trim((string) $eco->estimated_networth));
                $fFlags[] = !empty(trim((string) $eco->source_of_wealth_for_policy));
                $fFlags[] = !empty(trim((string) $eco->distribution_strategy_during_policy_lifetime));
                $fFlags[] = !empty(trim((string) $eco->distribution_strategy_post_death_payout));
                $fFlags[] = !empty(trim((string) $eco->known_triggers_for_policy_exit_or_surrender));
            }

            $prem = PolicyPremium::where('policy_id', $policy->id)->first();
            if ($prem) {
                $fFlags[] = ($prem->proposed_premium_amount ?? 0) > 0;
                $fFlags[] = ($prem->final_premium_amount ?? 0) > 0;
                $fFlags[] = !empty(trim((string) $prem->premium_frequency));
            }

            $fsi = PolicyFeeSummaryInternal::where('policy_id', $policy->id)->first();
            if ($fsi) {
                $fFlags[] = true;
            }

            $fse = PolicyFeeSummaryExternal::where('policy_id', $policy->id)->first();
            if ($fse) {
                $fFlags[] = true;
            }

            $ongoing = PolicyOnGoing::where('policy_id', $policy->id)->first();
            if ($ongoing) {
                $fFlags[] = !empty(trim((string) $ongoing->portfolio_change));
                $fFlags[] = !empty($ongoing->date_of_change_portfolio);
                $fFlags[] = !empty(trim((string) $ongoing->idf_change));
                $fFlags[] = !empty($ongoing->date_of_change_idf);
                $fFlags[] = !empty(trim((string) $ongoing->transfer_change));
                $fFlags[] = !empty($ongoing->date_of_change_transfer);
                $fFlags[] = !empty(trim((string) $ongoing->decision));
            }
        } catch (\Throwable $e) {}
        $progressF = !empty($fFlags) ? Helper::getCompletion($fFlags) : 0;

        $gFlags = [];
        try {
            $comms = PolicyCommunication::where('policy_id', $policy->id)->get();
            foreach ($comms as $c) {
                $gFlags[] = !empty($c->date);
                $gFlags[] = !empty(trim((string) $c->type));
                $gFlags[] = !empty(trim((string) $c->summary_of_discussion));
                $gFlags[] = !empty(trim((string) $c->action_taken_or_next_step));
            }

            $notes = PolicyCaseFileNote::where('policy_id', $policy->id)->get();
            foreach ($notes as $n) {
                $gFlags[] = !empty($n->date);
                $gFlags[] = !empty(trim((string) $n->noted_by));
                $gFlags[] = !empty(trim((string) $n->notes));
            }
        } catch (\Throwable $e) {}
        $progressG = !empty($gFlags) ? Helper::getCompletion($gFlags) : 0;

        $progressH = Arr::random(range(10, 100, 10));

        return view('cases.create.index', compact('title', 'subTitle', 'policy', 
            'progressA','progressB','progressC','progressD','progressE','progressF','progressG','progressH'
         ));
    }

    public function index(Request $request) 
    {
        if ($request->ajax()) {
            return $this->ajax();
        }

        $title = 'Case Management';
        $subTitle = 'Cases';

        return view('cases.index', compact('title', 'subTitle'));
    }

    public function ajax() {
        $policy = Policy::with(['liklihoodr', 'introducers.intro', 'holders', 'idfs']);

        if (request()->filled('filter_case')) {
            $policy->where('policy_number', 'LIKE', "%" . request('filter_case') . "%");
        }

        if (request()->filled('filter_opened')) {
            $policy->where('opening_date', date('Y-m-d', strtotime(request('filter_opened'))));
        }

        if (request()->filled('filter_holder')) {
            $policy->whereHas('holders', fn ($builder) => $builder->where('name', 'LIKE', '%' . request('filter_holder') . '%'));
        }

        if (request()->filled('filter_introducer')) {
            $policy->whereHas('introducers.intro', fn ($builder) => $builder->where('name', 'LIKE', '%' . request('filter_introducer') . '%'));
        }
        
        if (request()->filled('filter_status')) {
            $policy->where('status', request('filter_status'));
        }

        return datatables()
        ->eloquent($policy)
        ->editColumn('opening_date', fn ($row) => date('Y-m-d', strtotime($row->opening_date)))
        ->addColumn('theholder', fn ($row) => isset($row?->holders[0]?->id) ? (
            $row?->holders[0]?->type == 'individual' ? ($row?->holders[0]?->first_name . ' ' . $row?->holders[0]?->middle_name . ' ' . $row?->holders[0]?->last_name) : (
                $row?->holders[0]?->name
            )
        ) : 'N/A')
        ->addColumn('introducer', fn ($row) => isset($row->introducers[0]->id) && ($row->introducers[0]->intro[0]->id) ? (
            $row->introducers[0]->intro[0]->type == 'individual' ? ($row->introducers[0]->intro[0]->name . ' ' . $row->introducers[0]->intro[0]->middle_name . ' ' . $row->introducers[0]->intro[0]->last_name) : (
                $row->introducers[0]->intro[0]->name
            )            
        ) : 'N/A')
        ->addColumn('idfmgr', fn ($row) => isset($row->idfs[0]->id) ? (
            $row?->idfs[0]?->type == 'individual' ? ($row?->idfs[0]?->first_name . ' ' . $row?->idfs[0]?->middle_name . ' ' . $row?->idfs[0]?->last_name) : (
                $row?->idfs[0]?->name
            )
        ) : 'N/A')
        ->editColumn('status', function ($row) {

            $html = "<select class='me-2 change-status' data-id='".$row->id."' data-last-selected='".$row->status."'>";

            foreach (Helper::$status as $st) {
                $html .= "<option value='{$st}' ".($row->status == $st ? 'selected' : '')."> " . ($st) . " </option>";
            }

            $html .= "</select>";

            return $html;
        })
        ->editColumn('liklihood_c', function ($row) {

            $html = "<select class='me-2 change-liklihood' data-id='".$row->id."' data-last-selected='".$row->liklihoodr."'>";

            foreach (Liklihood::get() as $lh) {
                $html .= "<option value='{$lh->id}' ".($row->liklihood == $lh->id ? 'selected' : '')."> " . ($lh->name) . " </option>";
            }

            $html .= "</select>";

            return $html;
        })
        ->editColumn('action', function ($row) {
            $html = '<ul>';

            if (auth()->user()->can('cases.view')) {
                $html .= '
                    <li><a href="' . route('cases.view', encrypt($row->id)) . '"> <i class="fa fa-eye" aria-hidden="true"></i> </a></li>
                ';
            }

            if (auth()->user()->can('cases.edit')) {
                $html .= '
                    <li><a href="' . route('cases.edit', encrypt($row->id)) . '"> <i class="fa fa-pencil" aria-hidden="true"></i></a></li>
                ';
            }

            if (auth()->user()->can('cases.case-manager')) {
                $html .= '
                    <li><a href="' . route('cases.case-manager', encrypt($row->id)) . '">  <i class="fa fa-user" aria-hidden="true"></i> </a></li>
                ';
            }

            $html .= '</ul>';

            return $html;
        })
        ->addIndexColumn()
        ->rawColumns(['status', 'action', 'liklihood_c'])
        ->toJson();
    }

    public function submission(\App\Services\PolicyService $service) 
    {
        $submission = $service->submit(request());

        if (isset($submission['errors'])) {
            return response()->json($submission, 422);
        }

        $submission['timestamp'] = now()->format('H:i:s');

        if (isset($submission['next_section'])) {
            $submission['next_section_url'] = route('cases.edit', encrypt(request('policy'))) . '?s=' . encrypt($submission['next_section']);
        }

        return response()->json($submission);
    }

    public function caseManagement (Request $request, $id) {
        if (!empty($id)) {
            try {
                $id = decrypt($id);
                $policy = Policy::find($id);
            } catch (\Exception $e) {
                return redirect()->route('cases.index');
            }
        } else {
            return redirect()->route('cases.index');
        }

        if ($request->method() == 'POST') {
            $toKeep = [];

            if (is_array($request->managers)) {
                foreach ($request->managers as $manager) {
                    $toKeep[] = PolicyManager::updateOrCreate([
                        'manager_id' => $manager,
                        'policy_id' => $policy->id
                    ])->id;
                }
            }

            if (!empty($toKeep)) {
                PolicyManager::where('policy_id', $policy->id)->whereNotIn('id', $toKeep)->delete();
            } else {
                PolicyManager::where('policy_id', $policy->id)->delete();
            }

            MailboxJob::dispatch($policy, [
                'title' => 'Case Manager Changed'
            ]);

            return redirect()->route('cases.index');
        }

        $title = 'Case Management';
        $subTitle = 'Manage Case Manager';

        return view('cases.manager', compact('title', 'subTitle', 'policy'));
    }

    public function getDocs(Request $request) {
        $html = '<input type="hidden" name="adding" value="1" />';

        foreach (\App\Models\Document::where('status', $request->status)->get() as $status) {
            $html .= '
            <div class="mb-2 row">
                <div class="col-8">
                    <input type="checkbox" name="documents[]" id="doc-' . $status->id . '" value="' . $status->id . '"> &nbsp;&nbsp;&nbsp;
                    <label for="doc-' . $status->id . '">' . $status->title . '</label> 
                </div>
                <div class="col-4">

                </div>
            </div>
            ';
        }

        return response()->json([
            'html' => $html
        ]);
    }

    public function autoSave(\App\Services\PolicyService $service) 
    {
        $request = request();
        
        $request->merge(['save' => 'draft']);
        
        $submission = $service->submit($request, true);

        if (isset($submission['errors'])) {
            return response()->json($submission, 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Auto-saved successfully',
            'timestamp' => now()->format('H:i:s')
        ]);
    }

    public function uploadDoc(Request $request) {

        if ($request->has('dt_type')) {

            $request->validate([
                'file' => 'required|file|max:10240',
                'policy_id' => 'required|integer',
                'doc_id' => 'required|integer'
            ]);

            $folder = 'kyc-docs';

            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($folder)) {
                \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($folder);
            }

            $filename = time().'_'.$request->file->getClientOriginalName();
            $path = \Illuminate\Support\Facades\Storage::disk('public')->putFileAs($folder, $request->file, $filename);
            $shouldCheck = false;

            $query = PolicyDocument::where('policy_id', $request->policy_id)
                ->where('document_id', $request->doc_id);

            if ($request->filled('record_id')) {
                $query->where('record_id', $request->record_id)->where('document_type', $request->dt_type);
            } elseif ($request->filled('dt_type')) {
                $query->where('document_type', $request->dt_type);
            }

            $hasExpiryDate = $request->has('has_expiry_date') ? (int)$request->input('has_expiry_date') : 1;
            $expiryDate = $hasExpiryDate ? ($request->input('expiry_date') ?: null) : null;

            if ($query->exists()) {
                $query->update([
                    'document' => $filename,
                    'uploaded' => 1,
                    'has_expiry_date' => $hasExpiryDate,
                    'expiry_date' => $expiryDate,
                ]);
            } else {
                PolicyDocument::create([
                    'document_id' => $request->doc_id,
                    'policy_id' => $request->policy_id,
                    'document_type' => $request->dt_type,
                    'record_id' => $request->record_id,
                    'document' => $filename,
                    'uploaded' => 1,
                    'has_expiry_date' => $hasExpiryDate,
                    'expiry_date' => $expiryDate,
                ]);

                $shouldCheck = true;
            }


            return response()->json([
                'status' => 'success',
                'url' => asset('storage/' . $path),
                'check' => $shouldCheck
            ]);

        } else {
            $request->validate([
                'file' => 'required|file|max:10240',
                'doc_id' => 'required|integer'
            ]);

            $folder = 'kyc-docs';

            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($folder)) {
                \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($folder);
            }

            $filename = time().'_'.$request->file->getClientOriginalName();
            $path = \Illuminate\Support\Facades\Storage::disk('public')->putFileAs($folder, $request->file, $filename);
            $shouldCheck = false;

            $query = UploadableDocument::where('downloadable_document_id', $request->doc_id);

            $hasExpiryDate = $request->has('has_expiry_date') ? (int)$request->input('has_expiry_date') : 1;
            $expiryDate = $hasExpiryDate ? ($request->input('expiry_date') ?: null) : null;

            if ($query->exists()) {
                $query->update([
                    'file' => $filename,
                    'has_expiry_date' => $hasExpiryDate,
                    'expiry_date' => $expiryDate,
                ]);
            } else {
                UploadableDocument::create([
                    'downloadable_document_id' => $request->doc_id,
                    'file' => $filename,
                    'has_expiry_date' => $hasExpiryDate,
                    'expiry_date' => $expiryDate,
                ]);

                $shouldCheck = true;
            }

            return response()->json([
                'status' => 'success',
                'url' => asset('storage/' . $path),
                'check' => $shouldCheck
            ]);
        }
    }

    public function getCommunications(Request $request) {
        $request->validate([
            'policy' => 'required|integer'
        ]);

        $g1Data = PolicyCommunication::where('policy_id', $request->policy)->latest()->get();
        
        $html = '';
        if($g1Data && $g1Data->count() > 0) {
            $html .= '<div class="mb-4">
                <h5>Previous Communication Entries</h5>
                <div class="accordion" id="communicationAccordion">';
            
            foreach($g1Data as $index => $communication) {
                $html .= '<div class="accordion-item">
                    <h2 class="accordion-header" id="heading'.$index.'">
                        <button class="accordion-button '.($index > 0 ? 'collapsed' : '').'" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$index.'" aria-expanded="'.($index == 0 ? 'true' : 'false').'" aria-controls="collapse'.$index.'">
                            '.($communication->type ?: 'Communication').' - '.($communication->date ? \Carbon\Carbon::parse($communication->date)->format('M d, Y') : 'No Date').'
                        </button>
                    </h2>
                    <div id="collapse'.$index.'" class="accordion-collapse collapse '.($index == 0 ? 'show' : '').'" aria-labelledby="heading'.$index.'" data-bs-parent="#communicationAccordion">
                        <div class="accordion-body">
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Communication Date:</strong></div>
                                <div class="col-sm-9">'.($communication->date ? \Carbon\Carbon::parse($communication->date)->format('M d, Y H:i') : 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Communication Type:</strong></div>
                                <div class="col-sm-9">'.($communication->type ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Contact Person(s):</strong></div>
                                <div class="col-sm-9">'.($communication->contact_person_involved ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Summary of Discussion:</strong></div>
                                <div class="col-sm-9">'.($communication->summary_of_discussion ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Action Taken/Next Steps:</strong></div>
                                <div class="col-sm-9">'.($communication->action_taken_or_next_step ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Internal Owner(s):</strong></div>
                                <div class="col-sm-9">'.($communication->internal_owners ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <button type="button" class="btn btn-primary btn-sm" onclick="deleteCommunication('.$communication->id.')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>';
            }
            
            $html .= '</div></div>';
        }

        return response()->json([
            'html' => $html
        ]);
    }

    public function getCaseFileNotes(Request $request) {
        $request->validate([
            'policy' => 'required|integer'
        ]);

        $g2Data = PolicyCaseFileNote::where('policy_id', $request->policy)->latest()->get();
        
        $html = '';
        if($g2Data && $g2Data->count() > 0) {
            $html .= '<div class="mb-4">
                <h5>Previous Case File Notes</h5>
                <div class="accordion" id="caseFileNotesAccordion">';
            
            foreach($g2Data as $index => $note) {
                $html .= '<div class="accordion-item">
                    <h2 class="accordion-header" id="heading'.$index.'">
                        <button class="accordion-button '.($index > 0 ? 'collapsed' : '').'" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$index.'" aria-expanded="'.($index == 0 ? 'true' : 'false').'" aria-controls="collapse'.$index.'">
                            '.($note->noted_by ?: 'Note').' - '.($note->date ? \Carbon\Carbon::parse($note->date)->format('M d, Y') : 'No Date').'
                        </button>
                    </h2>
                    <div id="collapse'.$index.'" class="accordion-collapse collapse '.($index == 0 ? 'show' : '').'" aria-labelledby="heading'.$index.'" data-bs-parent="#caseFileNotesAccordion">
                        <div class="accordion-body">
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Date of Note:</strong></div>
                                <div class="col-sm-9">'.($note->date ? \Carbon\Carbon::parse($note->date)->format('M d, Y H:i') : 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Note By:</strong></div>
                                <div class="col-sm-9">'.($note->noted_by ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Note(s):</strong></div>
                                <div class="col-sm-9">'.($note->notes ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <button type="button" class="btn btn-primary btn-sm" onclick="deleteCaseFileNote('.$note->id.')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>';
            }
            
            $html .= '</div></div>';
        }

        return response()->json([
            'html' => $html
        ]);
    }

    public function getBeneficiaries(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer'
        ]);

        $beneficiaries = PolicyBeneficiary::where('policy_id', $request->policy_id)->get();

        $html = '';
        if ($beneficiaries && $beneficiaries->count() > 0) {
            $html .= '<div class="mb-4">'
                .'<div class="accordion" id="beneficiariesAccordion">';

            foreach($beneficiaries as $index => $b) {
                $html .= '<div class="accordion-item" id="bene-reco-' . $b->id . '">'
                    .'<h2 class="accordion-header" id="bHeading'.$index.'">'
                    .'<button class="accordion-button '.($index>0?'collapsed':'').'" type="button" data-bs-toggle="collapse" data-bs-target="#bCollapse'.$index.'" aria-expanded="'.($index==0?'true':'false').'" aria-controls="bCollapse'.$index.'">'
                    .($b->name ?: 'Beneficiary').' - '.($b->date_of_birth ? \Carbon\Carbon::parse($b->date_of_birth)->format('M d, Y') : 'No Date')
                    .'</button>'
                    .'</h2>'
                    .'<div id="bCollapse'.$index.'" class="accordion-collapse collapse '.($index==0?'show':'').'" aria-labelledby="bHeading'.$index.'" data-bs-parent="#beneficiariesAccordion">'
                    .'<div class="accordion-body">'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>Name:</strong></div><div class="col-sm-9">'.($b->name ?: 'N/A').'</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>Address:</strong></div><div class="col-sm-9">'.($b->address ?: 'N/A').'</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>Country:</strong></div><div class="col-sm-9">'.($b->country ?: 'N/A').'</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>City:</strong></div><div class="col-sm-9">'.($b->city ?: 'N/A').'</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>ZIP:</strong></div><div class="col-sm-9">'.($b->zip ?: 'N/A').'</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>Status:</strong></div><div class="col-sm-9">'.(ucfirst($b->status) ?: 'N/A').'</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>Smoker:</strong></div><div class="col-sm-9">'.(ucfirst(str_replace('-', ' ', $b->smoker_status)) ?: 'N/A').'</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>Allocation:</strong></div><div class="col-sm-9">'.number_format($b->beneficiary_death_benefit_allocation,2).'%</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>Designation:</strong></div><div class="col-sm-9">'.ucfirst($b->designation_of_beneficiary).'</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>Email:</strong></div><div class="col-sm-9">'.($b->email ?: 'N/A').'</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>Action:</strong></div><div class="col-sm-9">'
                    .'<button type="button" class="btn btn-primary btn-sm" onclick="d1EditBeneficiary('.$b->id.')">Edit</button>&nbsp;&nbsp;'
                    .'<button type="button" class="btn btn-primary btn-sm" onclick="d1DeleteBeneficiary('.$b->id.')">Delete</button>'
                    .'</div></div>'
                    .'</div></div></div>';
            }

            $html .= '</div></div>';
        }

        return response()->json(['html' => $html]);
    }

    public function getBeneficiary(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer',
            'beneficiary_id' => 'required|integer'
        ]);

        $beneficiary = PolicyBeneficiary::where('id', $request->beneficiary_id)->first();

        $tax = PolicyCountryOfTaxResidence::where('eloquent', PolicyBeneficiary::class)->where('eloquent_id', $request->beneficiary_id)->where('policy_id', $request->policy_id)->pluck('country')->toArray();
        $html = [];

        foreach ($tax as $tx) {
            foreach (Helper::allCountries() as $cntry) {
                if (!isset($html[$tx])) {
                    $html[$tx] = '<option value="' . $cntry . '" ' . ($cntry == $tx ? 'selected' : '') . ' > ' . $cntry . ' </option>';
                }  else {
                    $html[$tx] .= '<option value="' . $cntry . '" ' . ($cntry == $tx ? 'selected' : '') . ' > ' . $cntry . ' </option>';
                }
            }
        }

        return response()->json([
            'data' => $beneficiary,
            'tax' => $tax,
            'tax_html' => array_values($html)
        ]);
    }

    public function deleteBeneficiary(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer',
            'beneficiary_id' => 'required|integer'
        ]);

        PolicyBeneficiary::query()->delete();
        return response()->json(['status' => true]);
    }

    public function getInsuredLives(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer'
        ]);

        $insuredLives = PolicyInsuredLifeInformation::where('policy_id', $request->policy_id)->get();
        
        $html = '';
        if($insuredLives && $insuredLives->count() > 0) {
            $html .= '<div class="mb-4">
                <div class="accordion" id="insuredLifeAccordion">';
            
            foreach($insuredLives as $index => $insuredLife) {
                $html .= '<div class="accordion-item" id="ins-life-reco-' . $insuredLife->id . '">
                    <h2 class="accordion-header" id="heading'.$index.'">
                        <button class="accordion-button '.($index > 0 ? 'collapsed' : '').'" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$index.'" aria-expanded="'.($index == 0 ? 'true' : 'false').'" aria-controls="collapse'.$index.'">
                            '.($insuredLife->name ?: 'Insured Life').' - '.($insuredLife->date_of_birth ? \Carbon\Carbon::parse($insuredLife->date_of_birth)->format('M d, Y') : 'No Date').'
                        </button>
                    </h2>
                    <div id="collapse'.$index.'" class="accordion-collapse collapse '.($index == 0 ? 'show' : '').'" aria-labelledby="heading'.$index.'" data-bs-parent="#insuredLifeAccordion">
                        <div class="accordion-body">
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Name:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->name ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Place of Birth:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->place_of_birth ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Date of Birth:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->date_of_birth ? \Carbon\Carbon::parse($insuredLife->date_of_birth)->format('M d, Y') : 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Address:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->address ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Country:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->country ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>City:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->city ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Postcode/ZIP:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->zip ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Status:</strong></div>
                                <div class="col-sm-9">'.(ucfirst($insuredLife->status) ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Smoker Status:</strong></div>
                                <div class="col-sm-9">'.(ucfirst(str_replace('-', ' ', $insuredLife->smoker_status)) ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Nationality:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->nationality ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Gender:</strong></div>
                                <div class="col-sm-9">'.(ucfirst($insuredLife->gender) ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Country of Legal Residence:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->country_of_legal_residence ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Passport Number:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->passport_number ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Country of Issuance:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->country_of_issuance ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Relationship to Policyholder:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->relationship_to_policyholder ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Email:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->email ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Action:</strong></div>
                                <div class="col-sm-9">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="editInsuredLife('.$insuredLife->id.')">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="deleteInsuredLife('.$insuredLife->id.')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
            }
            
            $html .= '</div></div>';
        }

        return response()->json([
            'html' => $html
        ]);
    }

    public function getInsuredLife(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer',
            'insured_life_id' => 'required|integer'
        ]);

        $insuredLife = PolicyInsuredLifeInformation::where('policy_id', $request->policy_id)
            ->where('id', $request->insured_life_id)
            ->first();

        $tax = PolicyCountryOfTaxResidence::where('eloquent', PolicyInsuredLifeInformation::class)->where('eloquent_id', $request->insured_life_id)->where('policy_id', $request->policy_id)->pluck('country')->toArray();
        $html = [];

        foreach ($tax as $tx) {
            foreach (Helper::allCountries() as $cntry) {
                if (!isset($html[$tx])) {
                    $html[$tx] = '<option value="' . $cntry . '" ' . ($cntry == $tx ? 'selected' : '') . ' > ' . $cntry . ' </option>';
                }  else {
                    $html[$tx] .= '<option value="' . $cntry . '" ' . ($cntry == $tx ? 'selected' : '') . ' > ' . $cntry . ' </option>';
                }
            }
        }

        return response()->json([
            'data' => $insuredLife,
            'tax' => $tax,
            'tax_html' => array_values($html)
        ]);
    }

    public function deleteInsuredLife(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer',
            'insured_life_id' => 'required|integer'
        ]);

        PolicyInsuredLifeInformation::where('policy_id', $request->policy_id)
            ->where('id', $request->insured_life_id)
            ->delete();

        return response()->json(['status' => true]);
    }

    public function getInsuredLivesSidebar(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer'
        ]);

        $insuredLives = PolicyInsuredLifeInformation::where('policy_id', $request->policy_id)->get();
        
        $html = '';
        if($insuredLives && $insuredLives->count() > 0) {
            foreach($insuredLives as $insuredLife) {
                $displayName = $insuredLife->entity_type == 'individual' ? ($insuredLife->first_name . ' ' . $insuredLife->middle_name . ' ' . $insuredLife->last_name) : $insuredLife->full_name;
                $html .= '<li class="insured-life-item" data-id="'.$insuredLife->id.'">
                    <a class="insured-life-link" href="#" data-section="section-c-1" data-insured-id="'.$insuredLife->id.'">
                        <span class="insured-name">'.$displayName.'</span>
                        <div class="insured-actions">
                                <button type="button" class="btn btn-sm btn-outline-primary edit-insured" onclick="editInsuredLifeFromSidebar('.$insuredLife->id.')" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20h9" />
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z" />
                                </svg>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-insured" onclick="deleteInsuredLifeFromSidebar('.$insuredLife->id.')" title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6" />
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                <line x1="10" y1="11" x2="10" y2="17" />
                                <line x1="14" y1="11" x2="14" y2="17" />
                                </svg>
                            </button>
                        </div>
                    </a>
                </li>';
            }
        }

        return response()->json([
            'html' => $html
        ]);
    }

    public function getBeneficiariesSidebar(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer'
        ]);

        $beneficiaties = PolicyBeneficiary::where('policy_id', $request->policy_id)->get();
        
        $html = '';
        if($beneficiaties && $beneficiaties->count() > 0) {
            foreach($beneficiaties as $bene) {
                $displayName = $bene->entity_type == 'individual' ? ($bene->first_name . ' ' . $bene->middle_name . ' ' . $bene->last_name) : $bene->full_name;
                $html .= '<li class="beneficiary-item" data-id="'.$bene->id.'">
                    <a class="beneficiary-link" href="#" data-section="section-c-1" data-beneficiary-id="'.$bene->id.'">
                        <span class="beneficiary-name">'.$displayName.'</span>
                        <div class="beneficiary-actions">
                                <button type="button" class="btn btn-sm btn-outline-primary edit-beneficiary" onclick="editBeneficiaryFromSidebar('.$bene->id.')" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20h9" />
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z" />
                                </svg>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-beneficiary" onclick="deleteBeneficiaryFromSidebar('.$bene->id.')" title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6" />
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                <line x1="10" y1="11" x2="10" y2="17" />
                                <line x1="14" y1="11" x2="14" y2="17" />
                                </svg>
                            </button>
                        </div>
                    </a>
                </li>';
            }
        }

        return response()->json([
            'html' => $html
        ]);
    }

    public function getPolicyControllersSidebar(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer'
        ]);

        $controllers = PolicyController::where('policy_id', $request->policy_id)->get();
        
        $html = '';
        if($controllers && $controllers->count() > 0) {
            foreach($controllers as $controller) {
                $displayName = $controller->entity_type == 'individual' ? ($controller->first_name . ' ' . $controller->middle_name . ' ' . $controller->last_name) : $controller->full_name;
                $html .= '<li class="policycontroller-item" data-id="'.$controller->id.'">
                    <a class="policycontroller-link" href="#" data-section="section-b-2" data-policycontroller-id="'.$controller->id.'">
                        <span class="policycontroller-name">'.$displayName.'</span>
                        <div class="policycontroller-actions">
                                <button type="button" class="btn btn-sm btn-outline-primary edit-policycontroller" onclick="editPolicyControllerFromSidebar('.$controller->id.')" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20h9" />
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z" />
                                </svg>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-policycontroller" onclick="deletePolicyControllerFromSidebar('.$controller->id.')" title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6" />
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                <line x1="10" y1="11" x2="10" y2="17" />
                                <line x1="14" y1="11" x2="14" y2="17" />
                                </svg>
                            </button>
                        </div>
                    </a>
                </li>';
            }
        }

        return response()->json([
            'html' => $html
        ]);
    }

    public function getPolicyController(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer',
            'policy_controller_id' => 'required|integer'
        ]);

        $controller = PolicyController::where('policy_id', $request->policy_id)
            ->where('id', $request->policy_controller_id)
            ->first();

        if (!$controller) {
            return response()->json(['status' => false, 'message' => 'Policy controller not found'], 404);
        }

        $tax = \App\Models\PolicyCountryOfTaxResidence::where('eloquent', PolicyController::class)
            ->where('eloquent_id', $request->policy_controller_id)
            ->where('policy_id', $request->policy_id)
            ->pluck('country')
            ->toArray();

        return response()->json([
            'status' => true,
            'data' => $controller,
            'tax' => $tax
        ]);
    }

    public function deletePolicyController(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer',
            'policy_controller_id' => 'required|integer'
        ]);

        \App\Models\PolicyCountryOfTaxResidence::where('policy_id', $request->policy_id)
            ->where('eloquent', PolicyController::class)
            ->where('eloquent_id', $request->policy_controller_id)
            ->delete();

        PolicyController::where('policy_id', $request->policy_id)
            ->where('id', $request->policy_controller_id)
            ->delete();

        return response()->json(['status' => true]);
    }

    public function getPolicyHoldersSidebar(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer'
        ]);

        $holders = PolicyHolder::where('policy_id', $request->policy_id)->get();
        
        $html = '';
        if($holders && $holders->count() > 0) {
            foreach($holders as $holder) {
                $displayName = $holder->entity_type == 'individual' ? ($holder->first_name . ' ' . $holder->middle_name . ' ' . $holder->last_name) : $holder->full_name;
                $html .= '<li class="policyholder-item" data-id="'.$holder->id.'">
                    <a class="policyholder-link" href="#" data-section="section-b-1" data-policyholder-id="'.$holder->id.'">
                        <span class="policyholder-name">'.$displayName.'</span>
                        <div class="policyholder-actions">
                                <button type="button" class="btn btn-sm btn-outline-primary edit-policyholder" onclick="editPolicyHolderFromSidebar('.$holder->id.')" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20h9" />
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z" />
                                </svg>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-policyholder" onclick="deletePolicyHolderFromSidebar('.$holder->id.')" title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6" />
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                <line x1="10" y1="11" x2="10" y2="17" />
                                <line x1="14" y1="11" x2="14" y2="17" />
                                </svg>
                            </button>
                        </div>
                    </a>
                </li>';
            }
        }

        return response()->json([
            'html' => $html
        ]);
    }

    public function getPolicyHolder(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer',
            'policy_holder_id' => 'required|integer'
        ]);

        $holder = PolicyHolder::where('policy_id', $request->policy_id)
            ->where('id', $request->policy_holder_id)
            ->first();

        if (!$holder) {
            return response()->json(['status' => false, 'message' => 'Policyholder not found'], 404);
        }

        $tax = \App\Models\PolicyCountryOfTaxResidence::where('eloquent', PolicyHolder::class)
            ->where('eloquent_id', $request->policy_holder_id)
            ->where('policy_id', $request->policy_id)
            ->pluck('country')
            ->toArray();
        $html = [];

        foreach ($tax as $tx) {
            foreach (Helper::allCountries() as $cntry) {
                if (!isset($html[$tx])) {
                    $html[$tx] = '<option value="' . $cntry . '" ' . ($cntry == $tx ? 'selected' : '') . ' > ' . $cntry . ' </option>';
                }  else {
                    $html[$tx] .= '<option value="' . $cntry . '" ' . ($cntry == $tx ? 'selected' : '') . ' > ' . $cntry . ' </option>';
                }
            }
        }

        return response()->json([
            'status' => true,
            'data' => $holder,
            'tax' => $tax,
            'tax_html' => array_values($html)
        ]);
    }

    public function deletePolicyHolder(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer',
            'policy_holder_id' => 'required|integer'
        ]);

        \App\Models\PolicyCountryOfTaxResidence::where('policy_id', $request->policy_id)
            ->where('eloquent', PolicyHolder::class)
            ->where('eloquent_id', $request->policy_holder_id)
            ->delete();

        PolicyHolder::where('policy_id', $request->policy_id)
            ->where('id', $request->policy_holder_id)
            ->delete();

        return response()->json(['status' => true]);
    }

    public function getIntroducersSidebar(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer'
        ]);

        $introducers = PolicyIntroducer::with(['intro' => fn ($builder) => $builder->withTrashed() ])->where('policy_id', $request->policy_id)->get();
        
        $html = '';
        if($introducers && $introducers->count() > 0) {
            foreach($introducers as $bene) {
                $displayName = ($bene->intro->type == 'entity' ? ($bene->intro->name) : ($bene->intro->name . ' ' . $bene->intro->middle_name . ' ' . $bene->intro->last_name));
                $html .= '<li class="introducer-item" data-id="'.$bene->id.'">
                    <a class="introducer-link" href="#" data-section="section-a-1" data-introducer-id="'.$bene->id.'">
                        <span class="introducer-name">'.$displayName.'</span>
                        <div class="introducer-actions">
                                <button type="button" class="btn btn-sm btn-outline-primary edit-introducer" onclick="editIntroducerFromSidebar('.$bene->id.')" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20h9" />
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z" />
                                </svg>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-introducer" onclick="deleteIntroducerFromSidebar('.$bene->id.')" title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6" />
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                <line x1="10" y1="11" x2="10" y2="17" />
                                <line x1="14" y1="11" x2="14" y2="17" />
                                </svg>
                            </button>
                        </div>
                    </a>
                </li>';
            }
        }

        return response()->json([
            'html' => $html
        ]);
    }

    public function getIntroducer(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer',
            'introducer_id' => 'required|integer'
        ]);

        $pi = PolicyIntroducer::where('policy_id', $request->policy_id)
            ->where('id', $request->introducer_id)
            ->first();

        if (!$pi) {
            return response()->json(['status' => false, 'message' => 'Introducer not found']);
        }

        $introducer = \App\Models\Introducer::find($pi->introducer_id);
        if (!$introducer) {
            return response()->json(['status' => false, 'message' => 'Introducer not found']);
        }

        $linkedIds = \App\Models\PolicyIntroducerContactPerson::where('introducer_id', $pi->id)
            ->pluck('introducer_contact_person_id')->filter()->values();
        $contactPersons = [];
        if ($linkedIds->count() > 0) {
            $contactPersons = \App\Models\IntroducerContactPerson::whereIn('id', $linkedIds)->get();
        }

        $introIso2 = Helper::getIso2ByDialCode($introducer->dial_code ?? null);
        $contactsWithIso2 = collect($contactPersons)->map(function($c){
            $c->dial_iso2 = Helper::getIso2ByDialCode($c->dial_code ?? null);
            return $c;
        });

        return response()->json([
            'status' => true,
            'data' => array_merge($introducer->toArray(), ['dial_iso2' => $introIso2]),
            'contact_persons' => $contactsWithIso2
        ]);
    }

    public function deleteIntroducer(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer',
            'introducer_id' => 'required|integer'
        ]);

        \App\Models\PolicyIntroducerContactPerson::where('introducer_id', $request->introducer_id)->delete();
        
        PolicyIntroducer::where('policy_id', $request->policy_id)
            ->where('id', $request->introducer_id)
            ->delete();

        return response()->json(['status' => true]);
    }

    public function deleteCommunication(Request $request) {
        PolicyCommunication::where('id', $request->id)->delete();

        return response()->json(['status' => true]);
    }

    public function deleteNote(Request $request) {
        PolicyCaseFileNote::where('id', $request->id)->delete();

        return response()->json(['status' => true]);
    }

    public function caseStatusChange(Request $request) {
        $policy = Policy::find($request->id);
        $policy->status = $request->status;
        $policy->save();

        MailboxJob::dispatch($policy, [
            'title' => 'Policy status changed to ' . $request->status
        ]);

        return response()->json(['status' => 'success', 'message' => 'Policy status successfully']);
    }

    public function caseLiklihoodChange(Request $request) {
        $order = Policy::find($request->id);
        $order->liklihood = $request->status;
        $order->save();

        return response()->json(['status' => 'success', 'message' => 'Policy liklihood probability successfully']);
    }

    public function tooltipShow($element)
    {
        $tooltip = Tooltip::firstWhere('element_id', $element);
        return response()->json($tooltip);
    }

    public function tooltipUpdate(Request $request)
    {
        $request->validate([
            'element_id' => 'required|string',
            'content' => 'nullable|string'
        ]);

        $tooltip = Tooltip::updateOrCreate(
            ['element_id' => $request->element_id],
            ['content' => $request->input('content', '')]
        );

        return response()->json(['success' => true, 'content' => $tooltip->content]);
    }

    public function deleteDocument(Request $request, $id) {
        
        try {
            $id = decrypt($id);

            DownloadableDocument::find($id)->delete();
            UploadableDocument::where('downloadable_document_id', $id)->delete();

        } catch (\Exception $e) {

        }

        return redirect()->back();
    }

    /**
     * Remove only the uploaded file for a given downloadable document (H-1)
     */
    public function removeDownloadableDocumentFile(Request $request)
    {
        $request->validate([
            'doc_id' => 'required|integer'
        ]);

        $ud = DownloadableDocument::where('id', $request->doc_id)->first();
        if (!$ud) {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        }

        try {
            if (!empty($ud->file)) {
                $path = storage_path("app/public/customized-form/{$ud->file}");
                if (is_file($path)) { @unlink($path); }
            }

            $ud->file = null;
            $ud->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Unable to remove file'], 500);
        }
    }

    /**
     * Store a new downloadable document form
     */
    public function storeDownloadableDocument(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:downloadable_documents,title'
        ], [
            'title.unique' => 'A form with this name already exists.'
        ]);

        try {
            $maxOrdering = DownloadableDocument::max('ordering') ?? 0;
            
            $document = DownloadableDocument::create([
                'title' => $request->title,
                'ordering' => $maxOrdering + 1,
                'added_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Form added successfully!',
                'document' => $document
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding form: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the ordering of downloadable documents
     */
    public function updateDownloadableDocumentsOrdering(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:downloadable_documents,id',
            'items.*.ordering' => 'required|integer'
        ]);

        try {
            foreach ($request->items as $item) {
                DownloadableDocument::where('id', $item['id'])
                    ->update([
                        'ordering' => $item['ordering'],
                        'updated_by' => auth()->id()
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Forms reordered successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating order: ' . $e->getMessage()
            ], 500);
        }
    }
}
