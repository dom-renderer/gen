@extends('layouts.app', ['title' => $title, 'subTitle' => $subTitle, 'select2' => true])
@push('css')
<style>
    .container-fluid {
        max-width: 100%;
        padding: 0 20px;
    }
    .cards-wrapper {
        padding-top: 0;
    }
   .policy-dropdown-menu.policy-dropdown-submenu.sub-icon {
        padding-right: 25px;
        padding-left: 8px !important;
    }
    .modal-open .header {
        z-index: 9;
    }
    li .policy-dropdown-menu .policy-dropdown-item {
        background-color: transparent !important;
        font-family: 'SourceSansProRegular';
        font-size: 16px;
        font-style: normal;
        line-height: normal;
        padding: 2px 9px;
        text-transform: inherit;
    }

    .policy-dropdown-menu.policy-dropdown-submenu.sub-icon .policy-dropdown-item.policy-dropdown-toggle {
        justify-content: inherit;
        gap: 5px;
    }
    
</style>
@endpush

@php
$currentSection = Helper::silentDcrypt(request('s'));
$availableSections = \App\Services\PolicyService::$sections;
$availableSections = array_merge($availableSections, ['section-ia-1', 'section-idfm-1', 'section-idf-1', 'section-cb-1']);

if (!in_array($currentSection, $availableSections)) {
    $currentSection = 'section-a-1';
}

$menuMap = [
    'intro' => ['section-a-1', 'section-a-2'],
    'policyholders' => ['section-b-1', 'section-b-2'],
    'insured' => ['section-c-1'],
    'beneficiary' => ['section-d-1'],
    'investment-advisor' => ['section-ia-1'],
    'idf-manager' => ['section-idfm-1'],
    'insurance-dedicated-fund' => ['section-idf-1'],
    'custodian-bank' => ['section-cb-1'],
    'kyc' => ['section-e-1', 'section-e-2', 'section-e-3', 'section-e-4'],
    'policy' => ['section-f-1', 'section-f-2', 'section-f-3', 'section-f-4', 'section-f-5', 'section-f-6', 'section-f-7'],
    'section-forms' => ['section-h-1', 'section-h-2'],
    'communications' => ['section-g-1', 'section-g-2'],
];

$isInMenu = function ($menuKey) use ($menuMap, $currentSection) {
    return in_array($currentSection, $menuMap[$menuKey] ?? []);
};

@endphp

@section('content')
    <div class="row">
        <div class="col-xxl-3 col-lg-5">
            <ul class="policy-dropdown-menu policy-dropdown-submenu sub-icon" style="display: block;">
                <li class="dropdown child-dropdown">
                    <a href="#" class="policy-dropdown-item policy-dropdown-toggle {{ $isInMenu('intro') ? 'active' : '' }}" data-bs-toggle="dropdown"
                        aria-expanded="false"> 
                        <div class="doughnut completed-{{ $progressA }}"></div>
                        <span>Introductory Information</span>
                    
                    </a>
                    <ul class="policy-dropdown-menu policy-dropdown-submenu" style="display: {{ $isInMenu('intro') ? 'block' : 'none' }};">
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-a-1') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-a-1' ? 'active' : '' }}" data-section="section-a-1" >Introducer Profile</a></li>
                        <li class="introducers-submenu">
                        </li>
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-a-2') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-a-2' ? 'active' : '' }}" data-section="section-a-2">Key Parties & Roles</a></li>
                    </ul>
                </li>
                <li class="dropdown child-dropdown">
                    <a href="#" class="policy-dropdown-item policy-dropdown-toggle {{ $isInMenu('policyholders') ? 'active' : '' }}" data-bs-toggle="dropdown" data-section="policyholders">
                        <div class="doughnut completed-{{ $progressB }}"></div>
                        Policyholders
                        Information
                    
                    </a>
                    <ul class="policy-dropdown-menu policy-dropdown-submenu" style="display: {{ $isInMenu('policyholders') ? 'block' : 'none' }};">
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-b-1') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-b-1' ? 'active' : '' }}" data-section="section-b-1">Policyholder(s) Profile</a></li>
                        <li class="policyholders-submenu"></li>
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-b-2') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-b-2' ? 'active' : '' }}" data-section="section-b-2">Controlling Person(s) Profile</a></li>
                        <li class="policycontrollers-submenu"></li>
                    </ul>
                </li>
                <li class="dropdown child-dropdown">
                    <a href="#" class="policy-dropdown-item policy-dropdown-toggle {{ $isInMenu('insured') ? 'active' : '' }}" data-bs-toggle="dropdown" data-section="insured">
                        <div class="doughnut completed-{{ $progressC }}"></div>
                        Insured Life
                        Information
                    
                    </a>
                    <ul class="policy-dropdown-menu policy-dropdown-submenu" style="display: {{ $isInMenu('insured') ? 'block' : 'none' }};">
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-c-1') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-c-1' ? 'active' : '' }}" data-section="section-c-1"> Insured Life Profile</a></li>
                        <li class="insured-lives-submenu">
                        </li>
                    </ul>
                </li>
                <li class="dropdown child-dropdown">
                    <a href="#" class="policy-dropdown-item policy-dropdown-toggle {{ $isInMenu('beneficiary') ? 'active' : '' }}" data-bs-toggle="dropdown" data-section="beneficiary">
                        <div class="doughnut completed-{{ $progressD }}"></div>
                        Beneficiary
                        Information
                    </a>
                    <ul class="policy-dropdown-menu policy-dropdown-submenu" style="display: {{ $isInMenu('beneficiary') ? 'block' : 'none' }};">
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-d-1') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-d-1' ? 'active' : '' }}" data-section="section-d-1">Beneficiary Profile</a></li>
                        <li class="beneficiaries-submenu"></li>
                    </ul>
                </li>
                <li class="dropdown child-dropdown">
                    <a href="#" class="policy-dropdown-item policy-dropdown-toggle {{ $isInMenu('investment-advisor') ? 'active' : '' }}" data-bs-toggle="dropdown" data-section="investment-advisor">Investment Advisor</a>
                    <ul class="policy-dropdown-menu policy-dropdown-submenu" style="display: {{ $isInMenu('investment-advisor') ? 'block' : 'none' }};">
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-ia-1') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-ia-1' ? 'active' : '' }}" data-section="section-ia-1">Investment Advisor Profile</a></li>
                    </ul>
                </li>
                <li class="dropdown child-dropdown">
                    <a href="#" class="policy-dropdown-item policy-dropdown-toggle {{ $isInMenu('idf-manager') ? 'active' : '' }}" data-bs-toggle="dropdown" data-section="idf-manager">IDF Manager</a>
                    <ul class="policy-dropdown-menu policy-dropdown-submenu" style="display: {{ $isInMenu('idf-manager') ? 'block' : 'none' }};">
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-idfm-1') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-idfm-1' ? 'active' : '' }}" data-section="section-idfm-1">IDF Manager Profile</a></li>
                    </ul>
                </li>
                <li class="dropdown child-dropdown">
                    <a href="#" class="policy-dropdown-item policy-dropdown-toggle {{ $isInMenu('insurance-dedicated-fund') ? 'active' : '' }}" data-bs-toggle="dropdown" data-section="insurance-dedicated-fund">Insurance Dedicated Fund</a>
                    <ul class="policy-dropdown-menu policy-dropdown-submenu" style="display: {{ $isInMenu('insurance-dedicated-fund') ? 'block' : 'none' }};">
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-idf-1') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-idf-1' ? 'active' : '' }}" data-section="section-idf-1">IDF Profile</a></li>
                    </ul>
                </li>
                <li class="dropdown child-dropdown">
                    <a href="#" class="policy-dropdown-item policy-dropdown-toggle {{ $isInMenu('custodian-bank') ? 'active' : '' }}" data-bs-toggle="dropdown" data-section="custodian-bank">Custodian Bank</a>
                    <ul class="policy-dropdown-menu policy-dropdown-submenu" style="display: {{ $isInMenu('custodian-bank') ? 'block' : 'none' }};">
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-cb-1') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-cb-1' ? 'active' : '' }}" data-section="section-cb-1">Custodian Bank Profile</a></li>
                    </ul>
                </li>
                <li class="dropdown child-dropdown">
                    <a href="#" class="policy-dropdown-item policy-dropdown-toggle {{ $isInMenu('kyc') ? 'active' : '' }}" data-bs-toggle="dropdown" data-section="kyc">
                        <div class="doughnut completed-{{ $progressE }}"></div>
                        KYC Requirements &amp;
                        Checklists
                    
                    </a>
                    <ul class="policy-dropdown-menu policy-dropdown-submenu"  style="display: {{ $isInMenu('kyc') ? 'block' : 'none' }};">
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-e-1') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-e-1' ? 'active' : '' }}" data-section="section-e-1">Policyholder Required Documents</a></li>
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-e-2') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-e-2' ? 'active' : '' }}" data-section="section-e-2">Controlling Person Required Documents</a></li>
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-e-3') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-e-3' ? 'active' : '' }}" data-section="section-e-3">Insured Life/ves Required Documents</a></li>
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-e-4') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-e-4' ? 'active' : '' }}" data-section="section-e-4">Beneficiary/ies Required Documents</a></li>
                    </ul>
                </li>
                <li class="dropdown child-dropdown">
                    <a href="#" class="policy-dropdown-item policy-dropdown-toggle {{ $isInMenu('policy') ? 'active' : '' }}" data-bs-toggle="dropdown" data-section="policy">
                        <div class="doughnut completed-{{ $progressF }}"></div>
                        Policy Information
                        
                    </a>
                    <ul class="policy-dropdown-menu policy-dropdown-submenu"  style="display: {{ $isInMenu('policy') ? 'block' : 'none' }};">
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-f-1') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-f-1' ? 'active' : '' }}" data-section="section-f-1">Economic Profile</a></li>
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-f-2') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-f-2' ? 'active' : '' }}" data-section="section-f-2">Premium</a></li>
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-f-3') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-f-3' ? 'active' : '' }}" data-section="section-f-3">Fee Summary (Internal)</a></li>
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-f-4') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-f-4' ? 'active' : '' }}" data-section="section-f-4">Fee Summary (External)</a></li>
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-f-5') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-f-5' ? 'active' : '' }}" data-section="section-f-5">Investment Profile (Inception)</a></li>
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-f-6') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-f-6' ? 'active' : '' }}" data-section="section-f-6">Investment Profile (On-Going)</a></li>
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-f-7') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-f-7' ? 'active' : '' }}" data-section="section-f-7">Investment Profile (Investment Notes)</a></li>
                    </ul>
                </li>
                <li class="dropdown child-dropdown">
                    <a href="#" class="policy-dropdown-item policy-dropdown-toggle {{ $isInMenu('section-forms') ? 'active' : '' }}" data-bs-toggle="dropdown" data-section="section-forms">
                        <div class="doughnut completed-{{ $progressG }}"></div> 
                        Forms
                    </a>
                    <ul class="policy-dropdown-menu policy-dropdown-submenu"  style="display: {{ $isInMenu('section-forms') ? 'block' : 'none' }};">
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-h-1') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-h-1' ? 'active' : '' }}" data-section="section-h-1"> Downloadable Forms </a></li>
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-h-2') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-h-2' ? 'active' : '' }}" data-section="section-h-2"> Upload Forms </a></li>
                    </ul>
                </li>
                <li class="dropdown child-dropdown">
                    <a href="#" class="policy-dropdown-item policy-dropdown-toggle {{ $isInMenu('communications') ? 'active' : '' }}" data-bs-toggle="dropdown" data-section="communications">Communications &amp;
                        Lifecycle
                    </a>
                    <ul class="policy-dropdown-menu policy-dropdown-submenu"  style="display: {{ $isInMenu('communications') ? 'block' : 'none' }};">
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-g-1') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-g-1' ? 'active' : '' }}" data-section="section-g-1">Communications</a></li>
                        <li><a href="{{ request()->url() . '?s=' . encrypt('section-g-2') }}" class="policy-dropdown-item each-options {{ $currentSection === 'section-g-2' ? 'active' : '' }}" data-section="section-g-2">Case File Notes</a></li>
                    </ul>
                </li>
            </ul>

        </div>

        <div class="col-xxl-9 new-case col-lg-7 ">

            <div class="sub-title" style="margin-bottom: 40px;">
                <h2>
                    {{  request()->segment(2) == 'create' ? 'Add New Case' : 'Edit Case'  }} / Policy Number {{ Helper::generateCaseNumber() }}
                    <div id="saving-container" style="float:right;">                        
                    </div>
                </h2>
            </div>

            <div class="new-case-main" style="padding-left: 0px!important;">
            <div class="case-section {{ $currentSection === 'section-a-1' ? '' : 'd-none' }}" id="section-a-1">
                @php
                    $introducer = isset($policy->introducers[0]) ? $policy->introducers[0] : [
                        'type' => null,
                        'entity_type' => null,
                        'full_name' => null,
                        'name' => null,
                        'email' => null,
                        'dial_code' => null,
                        'contact_number' => null
                    ];
                @endphp
                @include('cases.create.section-a-1', ['introducer' => $introducer])
            </div>
            <div class="case-section {{ $currentSection === 'section-a-2' ? '' : 'd-none' }}" id="section-a-2">
                @php
                    $keyRolesA = $policy->holders()->count() > 0 ? $policy->holders()->get()->toArray() : [[
                        'type' => null,
                        'entity_type' => null,
                        'full_name' => null,
                        'name' => null,
                        'first_name' => null,
                        'middle_name' => null,
                        'last_name' => null,
                        'notes' => null
                    ]];

                    $keyRolesB = \App\Models\PolicyInsuredLifeInformation::where('policy_id', $policy->id)->count() > 0 ? \App\Models\PolicyInsuredLifeInformation::where('policy_id', $policy->id)->get()->toArray() : [[
                        'type' => null,
                        'entity_type' => null,
                        'full_name' => null,
                        'name' => null,
                        'first_name' => null,
                        'middle_name' => null,
                        'last_name' => null,
                        'notes' => null
                    ]];
                    
                    $keyRolesC = \App\Models\PolicyBeneficiary::where('policy_id', $policy->id)->count() > 0 ? \App\Models\PolicyBeneficiary::where('policy_id', $policy->id)->get()->toArray() : [[
                        'type' => null,
                        'entity_type' => null,
                        'full_name' => null,
                        'name' => null,
                        'first_name' => null,
                        'middle_name' => null,
                        'last_name' => null,
                        'notes' => null
                    ]];

                    $keyRolesD = \App\Models\InvestmentAdvisor::where('policy_id', $policy->id)->count() > 0 ? \App\Models\InvestmentAdvisor::where('policy_id', $policy->id)->get()->toArray() : [[
                        'type' => null,
                        'entity_type' => null,
                        'full_name' => null,
                        'name' => null,
                        'first_name' => null,
                        'middle_name' => null,
                        'last_name' => null,
                        'notes' => null,
                        'applicable' => 'applicable'
                    ]];

                    $keyRolesE = \App\Models\InvestmentDedicatedFund::where('policy_id', $policy->id)->where('user_type', 'name')->count() > 0 ? \App\Models\InvestmentDedicatedFund::where('policy_id', $policy->id)->where('user_type', 'name')->get()->toArray() : [[
                        'type' => null,
                        'entity_type' => null,
                        'full_name' => null,
                        'name' => null,
                        'first_name' => null,
                        'middle_name' => null,
                        'last_name' => null,
                        'notes' => null,
                        'applicable' => 'applicable'
                    ]];

                    $keyRolesF = \App\Models\InvestmentDedicatedFund::where('policy_id', $policy->id)->where('user_type', 'manager')->count() > 0 ? \App\Models\InvestmentDedicatedFund::where('policy_id', $policy->id)->where('user_type', 'manager')->get()->toArray() : [[
                        'type' => null,
                        'entity_type' => null,
                        'full_name' => null,
                        'name' => null,
                        'first_name' => null,
                        'middle_name' => null,
                        'last_name' => null,
                        'notes' => null,
                        'applicable' => 'applicable'
                    ]];

                    $keyRolesG = \App\Models\Custodian::where('policy_id', $policy->id)->count() ? \App\Models\Custodian::where('policy_id', $policy->id)->get()->toArray() : [[
                        'type' => null,
                        'entity_type' => null,
                        'full_name' => null,
                        'name' => null,
                        'first_name' => null,
                        'middle_name' => null,
                        'last_name' => null,
                        'notes' => null,
                        'applicable' => 'applicable'
                    ]];
                @endphp
                @include('cases.create.section-a-2', [
                    'keyRolesA' => $keyRolesA,
                    'keyRolesB' => $keyRolesB,
                    'keyRolesC' => $keyRolesC,
                    'keyRolesD' => $keyRolesD,
                    'keyRolesE' => $keyRolesE,
                    'keyRolesF' => $keyRolesF,
                    'keyRolesG' => $keyRolesG,
                    'policy' => $policy
                ])
            </div>
            <div class="case-section {{ $currentSection === 'section-b-1' ? '' : 'd-none' }}" id="section-b-1">
                @php
                    $polhol = \App\Models\PolicyHolder::where('policy_id', $policy->id)->first();

                    if ($polhol) {
                        $polhol = $polhol->toArray();
                    } else {
                        $polhol = [];
                    }
                @endphp

                @include('cases.create.section-b-1', ['polhol', 'polhol'])
            </div>
            <div class="case-section {{ $currentSection === 'section-b-2' ? '' : 'd-none' }}" id="section-b-2">
                @php
                    $polhol = \App\Models\PolicyController::where('policy_id', $policy->id)->first();

                    if ($polhol) {
                        $polhol = $polhol->toArray();
                    } else {
                        $polhol = [];
                    }
                @endphp

                @include('cases.create.section-b-2', ['polhol', 'polhol'])
            </div>
            <div class="case-section {{ $currentSection === 'section-c-1' ? '' : 'd-none' }}" id="section-c-1">
                @include('cases.create.section-c-1')
            </div>
            <div class="case-section {{ $currentSection === 'section-d-1' ? '' : 'd-none' }}" id="section-d-1">
                @include('cases.create.section-d-1')
            </div>
            <div class="case-section {{ $currentSection === 'section-ia-1' ? '' : 'd-none' }}" id="section-ia-1">
                @php
                    $investmentAdvisors = $policy->investmentadv()->get();
                @endphp
                @include('cases.create.section-ia-1', ['investmentAdvisors' => $investmentAdvisors])
            </div>
            <div class="case-section {{ $currentSection === 'section-idfm-1' ? '' : 'd-none' }}" id="section-idfm-1">
                @php
                    $idfManagers = $policy->idfrole()->where('user_type', 'manager')->get();
                @endphp
                @include('cases.create.section-idfm-1', ['idfManagers' => $idfManagers])
            </div>
            <div class="case-section {{ $currentSection === 'section-idf-1' ? '' : 'd-none' }}" id="section-idf-1">
                @php
                    $idfNames = $policy->idfrole()->where('user_type', 'name')->get();
                @endphp
                @include('cases.create.section-idf-1', ['idfNames' => $idfNames])
            </div>
            <div class="case-section {{ $currentSection === 'section-cb-1' ? '' : 'd-none' }}" id="section-cb-1">
                @php
                    $custodianBanks = $policy->custodians()->get();
                @endphp
                @include('cases.create.section-cb-1', ['custodianBanks' => $custodianBanks])
            </div>
            <div class="case-section {{ $currentSection === 'section-e-1' ? '' : 'd-none' }}" id="section-e-1">
                @php
                    $policyHolders = $policy->holders()->get();
                    $dArrByRecord = [];
                    foreach ($policyHolders as $ph) {
                        $dArrByRecord[$ph->id] = \App\Models\PolicyDocument::where('policy_id', $policy->id)
                        ->where('document_type', 'policy-holder')
                        ->where('record_id', $ph->id)
                        ->get(['document_id', 'uploaded', 'document', 'has_expiry_date', 'expiry_date'])
                        ->keyBy('document_id')
                        ->map(function ($item) {
                            return [
                                'uploaded' => $item->uploaded,
                                'document' => $item->document,
                                'expiry_date' => $item->expiry_date
                            ];
                        })
                        ->toArray();
                    }
                @endphp

                @include('cases.create.section-e-1', ['dArrByRecord' => $dArrByRecord, 'policyHolders' => $policyHolders])
            </div>
            <div class="case-section {{ $currentSection === 'section-e-2' ? '' : 'd-none' }}" id="section-e-2">
                @php
                    $dArr = \App\Models\PolicyDocument::where('policy_id', $policy->id)
                    ->where('document_type', 'controlling-person')
                    ->get(['document_id', 'uploaded', 'document', 'has_expiry_date', 'expiry_date'])
                    ->keyBy('document_id')
                    ->map(function ($item) {
                        return [
                            'uploaded' => $item->uploaded,
                            'document' => $item->document,
                            'expiry_date' => $item->expiry_date
                        ];
                    })
                    ->toArray();
                @endphp

                @include('cases.create.section-e-2', ['dArr' => $dArr])
            </div>
            <div class="case-section {{ $currentSection === 'section-e-3' ? '' : 'd-none' }}" id="section-e-3">
                @php
                    $insuredLives = $policy->insuredlives()->get();
                    $dArrByRecord = [];
                    foreach ($insuredLives as $il) {
                        $dArrByRecord[$il->id] = \App\Models\PolicyDocument::where('policy_id', $policy->id)
                        ->where('document_type', 'insured-life')
                        ->where('record_id', $il->id)
                    ->get(['document_id', 'uploaded', 'document', 'has_expiry_date', 'expiry_date'])
                        ->keyBy('document_id')
                        ->map(function ($item) {
                            return [
                                'uploaded' => $item->uploaded,
                                'document' => $item->document,
                                'expiry_date' => $item->expiry_date
                            ];
                        })
                        ->toArray();
                    }
                @endphp

                @include('cases.create.section-e-3', ['dArrByRecord' => $dArrByRecord, 'insuredLives' => $insuredLives])
            </div>
            <div class="case-section {{ $currentSection === 'section-e-4' ? '' : 'd-none' }}" id="section-e-4">
                @php
                    $beneficiaries = $policy->beneficiaries()->get();
                    $dArrByRecord = [];
                    foreach ($beneficiaries as $bf) {
                        $dArrByRecord[$bf->id] = \App\Models\PolicyDocument::where('policy_id', $policy->id)
                        ->where('document_type', 'beneficiary')
                        ->where('record_id', $bf->id)
                        ->get(['document_id', 'uploaded', 'document'])
                        ->keyBy('document_id')
                        ->map(function ($item) {
                            return [
                                'uploaded' => $item->uploaded,
                                'document' => $item->document,
                                'expiry_date' => $item->expiry_date
                            ];
                        })
                        ->toArray();
                    }
                @endphp

                @include('cases.create.section-e-4', ['dArrByRecord' => $dArrByRecord, 'beneficiaries' => $beneficiaries])
            </div>
            <div class="case-section {{ $currentSection === 'section-f-1' ? '' : 'd-none' }}" id="section-f-1">
                @php
                    $f1Data = \App\Models\PolicyEconomicProfile::where('policy_id', $policy->id)->first();
                @endphp
                @include('cases.create.section-f-1', ['f1Data' => $f1Data])
            </div>
            <div class="case-section {{ $currentSection === 'section-f-2' ? '' : 'd-none' }}" id="section-f-2">
                @php
                    $f2Data = \App\Models\PolicyPremium::where('policy_id', $policy->id)->first();
                @endphp
                @include('cases.create.section-f-2', ['f2Data' => $f2Data])
            </div>
            <div class="case-section {{ $currentSection === 'section-f-3' ? '' : 'd-none' }}" id="section-f-3">
                @php
                    $f3Data = \App\Models\PolicyFeeSummaryInternal::with('items')->where('policy_id', $policy->id)->first();
                @endphp
                @include('cases.create.section-f-3', ['f3Data' => $f3Data, 'policy_id' => $policy->id])
            </div>
            <div class="case-section {{ $currentSection === 'section-f-4' ? '' : 'd-none' }}" id="section-f-4">
                @php
                    $f4Data = \App\Models\PolicyFeeSummaryExternal::where('policy_id', $policy->id)->first();
                @endphp
                @include('cases.create.section-f-4', ['f4Data' => $f4Data])
            </div>
            <div class="case-section {{ $currentSection === 'section-f-5' ? '' : 'd-none' }}" id="section-f-5">
                @php
                    $f5Data = \App\Models\PolicyInception::where('policy_id', $policy->id)->first();
                @endphp
                @include('cases.create.section-f-5', ['f5Data' => $f5Data])
            </div>
            <div class="case-section {{ $currentSection === 'section-f-6' ? '' : 'd-none' }}" id="section-f-6">
                @php
                    $f6Data = \App\Models\PolicyOnGoing::where('policy_id', $policy->id)->first();
                @endphp
                @include('cases.create.section-f-6', ['f6Data' => $f6Data])
            </div>
            <div class="case-section {{ $currentSection === 'section-f-7' ? '' : 'd-none' }}" id="section-f-7">
                @php
                    $f7Data = \App\Models\PolicyInvestmentNote::where('policy_id', $policy->id)->first();
                @endphp
                @include('cases.create.section-f-7', ['f7Data' => $f7Data])
            </div>
            <div class="case-section {{ $currentSection === 'section-g-1' ? '' : 'd-none' }}" id="section-g-1">
                @php
                    $g1Data = \App\Models\PolicyCommunication::where('policy_id', $policy->id)->latest()->get();
                @endphp
                @include('cases.create.section-g-1', ['g1Data' => $g1Data])
            </div>
            <div class="case-section {{ $currentSection === 'section-g-2' ? '' : 'd-none' }}" id="section-g-2">
                @php
                    $g2Data = \App\Models\PolicyCaseFileNote::where('policy_id', $policy->id)->latest()->get();
                @endphp
                @include('cases.create.section-g-2', ['g2Data' => $g2Data])
            </div>
            <div class="case-section {{ $currentSection === 'section-h-1' ? '' : 'd-none' }}" id="section-h-1">
                @php
                    $h1Data = \App\Models\DownloadableDocument::orderBy('ordering')->orderBy('id')->get();
                @endphp
                @include('cases.create.section-h-1', ['h1Data' => $h1Data])
            </div>
            <div class="case-section {{ $currentSection === 'section-h-2' ? '' : 'd-none' }}" id="section-h-2">
                @include('cases.create.section-h-2', ['h2Data' => $h1Data,'policy_id' => $policy->id])
            </div>
            </div>
        </div>

        <div class="col-md-12 mt-2 pding">
            <a href="{{ route('cases.index') }}" class="btn btn-primary"> Back </a>
        </div>
    </div>



<div class="modal fade" id="tooltipModal" tabindex="-1" aria-hidden="true" style="z-index:9999!important;">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="tooltipForm">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="element_id" id="tooltip_element_id">
          <div class="mb-3">
            <textarea name="content" id="tooltip_content" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
            <button class="btn btn-primary" type="submit">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>


 <div class="modal fade" id="addNewFormModal" tabindex="-1" aria-labelledby="addNewFormModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNewFormModalLabel">Add New Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addNewFormForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="formName" class="form-label">Form Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="formName" name="title" required>
                        <div class="invalid-feedback" id="formNameError"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Form</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/intel-tel.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}">
   
@include('cases.create.style')

@endpush

@push('js')
<script src="{{ asset('assets/js/jquery-validate.min.js') }}"></script>
<script src="{{ asset('assets/js/intel-tel.js') }}"></script>
<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>

@include('cases.create.initial-script', [
    'policy'  => $policy ?? null,
])

@include('cases.create.script', [
    'introducer' => $introducer ?? [],
    'policyId' => $policy->id ?? null
])

@include('cases.create.second-script', [

])
@include('cases.create.upload-modal')
@include('cases.common-script')
@endpush
