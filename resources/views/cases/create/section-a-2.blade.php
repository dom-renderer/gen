<form id="form-section-a-2" class="k-pro">
    <div class="key-parties-container">

        <div class="role-section mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5>Policyholder </h5>
                <button type="button" class="btn btn-sm btn-success text-white add-policyholder">+ Add Policyholder</button>
            </div>
            <div class="policyholders-container">
                @foreach ($keyRolesA as $kr)
                    <div class="policyholder-item mb-3 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">Policyholder {{ $loop->iteration }}</span>
                            <button type="button" class="btn btn-sm text-white btn-danger remove-policyholder" >Remove</button>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-3">
                                <label class="form-label mb-3">Entity Type</label>
                                <select class="form-control entity-type-select" name="policy_holder[{{ $loop->iteration - 1 }}][type]">
                                    <option value="">Select Type</option>
                                    <option value="individual" {{ $kr['entity_type'] == 'individual' ? 'selected' : '' }}>Individual</option>
                                    <option value="corporate" {{ $kr['entity_type'] == 'corporate' ? 'selected' : '' }}>Corporate</option>
                                    <option value="trust" {{ $kr['entity_type'] == 'trust' ? 'selected' : '' }}>Trust</option>
                                    <option value="foundation" {{ $kr['entity_type'] == 'foundation' ? 'selected' : '' }}>Foundation</option>
                                </select>
                            </div>
                            <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-9">
                                <input type="hidden" name="policy_holder[{{ $loop->iteration - 1 }}][id]" value="{{ $kr['id'] ?? '' }}">
                                <div class="individual-name-fields" style="@if($kr['entity_type'] != 'individual') display: none; @endif">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label">First Name</label>
                                            <input type="text" class="form-control" name="policy_holder[{{ $loop->iteration - 1 }}][first_name]" value="{{ $kr['first_name'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Middle Name(s)</label>
                                            <input type="text" class="form-control" name="policy_holder[{{ $loop->iteration - 1 }}][middle_name]" value="{{ $kr['middle_name'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" class="form-control" name="policy_holder[{{ $loop->iteration - 1 }}][last_name]" value="{{ $kr['last_name'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="entity-name-field" style="@if($kr['entity_type'] == 'individual') display: none; @endif">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" name="policy_holder[{{ $loop->iteration - 1 }}][name]" value="{{ $kr['full_name'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" rows="2" name="policy_holder[{{ $loop->iteration - 1 }}][notes]">{{ $kr['notes'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="role-section mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5>Insured Life </h5>
                <button type="button" class="btn btn-sm btn-success text-white add-insured-life">+ Add Insured Life</button>
            </div>
            <div class="insured-lives-container">
                @foreach($keyRolesB as $kr)
                <div class="insured-life-item mb-3 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold">Insured Life {{ $loop->iteration }}</span>
                        <button type="button" class="btn btn-sm btn-danger remove-insured-life text-white" >Remove</button>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-3">
                            <label class="form-label mb-3">Entity Type</label>
                            <select class="form-control entity-type-select" name="insured_life[{{ $loop->iteration }}][entity_type]">
                                <option value="">Select Type</option>
                                <option value="individual" {{ $kr['entity_type'] == 'individual' ? 'selected' : '' }}>Individual</option>
                                <option value="corporate" {{ $kr['entity_type'] == 'corporate' ? 'selected' : '' }}>Corporate</option>
                                <option value="trust" {{ $kr['entity_type'] == 'trust' ? 'selected' : '' }}>Trust</option>
                                <option value="foundation" {{ $kr['entity_type'] == 'foundation' ? 'selected' : '' }}>Foundation</option>
                            </select>
                        </div>
                        <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-9">
                            <input type="hidden" name="insured_life[{{ $loop->iteration }}][id]" value="{{ $kr['id'] ?? '' }}">
                            <div class="individual-name-fields" style="@if($kr['entity_type'] != 'individual') display: none; @endif">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="insured_life[{{ $loop->iteration }}][first_name]" value="{{ $kr['first_name'] ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Middle Name(s)</label>
                                        <input type="text" class="form-control" name="insured_life[{{ $loop->iteration }}][middle_name]" value="{{ $kr['middle_name'] ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="insured_life[{{ $loop->iteration }}][last_name]" value="{{ $kr['last_name'] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="entity-name-field" style="@if($kr['entity_type'] == 'individual') display: none; @endif">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="insured_life[{{ $loop->iteration }}][name]" value="{{ $kr['full_name'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="2" name="insured_life[{{ $loop->iteration }}][notes]">{{ $kr['notes'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="role-section mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5>Beneficiary </h5>
                <button type="button" class="btn btn-sm btn-success add-beneficiary text-white">+ Add Beneficiary</button>
            </div>
            <div class="beneficiaries-container">
                @foreach($keyRolesC as $kr)
                <div class="beneficiary-item mb-3 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold">Beneficiary {{ $loop->iteration }}</span>
                        <button type="button" class="btn btn-sm btn-danger remove-beneficiary text-white" >Remove</button>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-3">
                            <label class="form-label mb-3">Entity Type</label>
                            <select class="form-control entity-type-select" name="beneficiary[{{ $loop->iteration }}][entity_type]">
                                <option value="">Select Type</option>
                                <option value="individual" {{ $kr['entity_type'] == 'individual' ? 'selected' : '' }}>Individual</option>
                                <option value="corporate" {{ $kr['entity_type'] == 'corporate' ? 'selected' : '' }}>Corporate</option>
                                <option value="trust" {{ $kr['entity_type'] == 'trust' ? 'selected' : '' }}>Trust</option>
                                <option value="foundation" {{ $kr['entity_type'] == 'foundation' ? 'selected' : '' }}>Foundation</option>
                            </select>
                        </div>
                        <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-9">
                            <input type="hidden" name="beneficiary[{{ $loop->iteration }}][id]" value="{{ $kr['id'] ?? '' }}">
                            <div class="individual-name-fields" style="@if($kr['entity_type'] != 'individual') display: none; @endif">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="beneficiary[{{ $loop->iteration }}][first_name]" value="{{ $kr['first_name'] ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Middle Name(s)</label>
                                        <input type="text" class="form-control" name="beneficiary[{{ $loop->iteration }}][middle_name]" value="{{ $kr['middle_name'] ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="beneficiary[{{ $loop->iteration }}][last_name]" value="{{ $kr['last_name'] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="entity-name-field" style="@if($kr['entity_type'] == 'individual') display: none; @endif">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="beneficiary[{{ $loop->iteration }}][name]" value="{{ $kr['full_name'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="2" name="beneficiary[{{ $loop->iteration }}][notes]">{{ $kr['notes'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="role-section mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2 manage-res-tls">
                <h5>Investment Advisor/Manager</h5>
                @php 
                    $invAdvManExistsApplicable = $policy->investment_advisor_manager_applicable;
                @endphp
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check " name="advisor_applicable"  id="advisor_applicable" value="applicable" {{ ($invAdvManExistsApplicable ? 'applicable' : '') == 'applicable' ? 'checked' : '' }}>
                    <label class="btn btn-outline-success mt-green" for="advisor_applicable">Applicable</label>
                    <input type="radio" class="btn-check" name="advisor_applicable" id="advisor_not_applicable" value="not_applicable" {{ (!$invAdvManExistsApplicable ? 'not_applicable' : '') == 'not_applicable' ? 'checked' : '' }}>
                    <label class="btn btn-outline-danger mt-rd" 
                     for="advisor_not_applicable">Not Applicable</label>
                    <div class="d-flex justify-content-between align-items-center ms-3">
                        <span></span>
                        <button type="button" class="btn btn-sm btn-success add-advisor text-white">+ Add Investment Advisor</button>
                    </div>
                </div>
            </div>
            <div class="advisor-container" style="{{ ($invAdvManExistsApplicable ? 1 : 0) == 0 ? 'opacity: 0.5; pointer-events: none;' : '' }}">
              
                <div class="advisors-container">
                    @foreach($keyRolesD as $kr)
                    <div class="advisor-item mb-3 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">Investment Advisor {{ $loop->iteration }}</span>
                            <button type="button" class="btn btn-sm btn-danger remove-advisor text-white" >Remove</button>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-3">
                                <label class="form-label mb-3">Entity Type</label>
                                <select class="form-control entity-type-select" name="advisor[{{ $loop->iteration }}][entity_type]">
                                    <option value="">Select Type</option>
                                    <option value="Individual" {{ $kr['type'] == 'Individual' ? 'selected' : '' }}>Individual</option>
                                    <option value="Corporate" {{ $kr['type'] == 'Corporate' ? 'selected' : '' }}>Corporate</option>
                                    <option value="Trust" {{ $kr['type'] == 'Trust' ? 'selected' : '' }}>Trust</option>
                                    <option value="Foundation" {{ $kr['type'] == 'Foundation' ? 'selected' : '' }}>Foundation</option>
                                </select>
                            </div>
                            <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-9">
                                <input type="hidden" name="advisor[{{ $loop->iteration }}][id]" value="{{ $kr['id'] ?? '' }}">
                                <div class="individual-name-fields" style="@if($kr['type'] != 'Individual') display: none; @endif">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label">First Name</label>
                                            <input type="text" class="form-control" name="advisor[{{ $loop->iteration }}][first_name]" value="{{ $kr['first_name'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Middle Name(s)</label>
                                            <input type="text" class="form-control" name="advisor[{{ $loop->iteration }}][middle_name]" value="{{ $kr['middle_name'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" class="form-control" name="advisor[{{ $loop->iteration }}][last_name]" value="{{ $kr['last_name'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="entity-name-field" style="@if($kr['type'] == 'Individual') display: none; @endif">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" name="advisor[{{ $loop->iteration }}][name]" value="{{ $kr['name'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" rows="2" name="advisor[{{ $loop->iteration }}][notes]">{{ $kr['notes'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="role-section mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2 manage-res-tls">
                <h5>IDF Name</h5>
                @php 
                    $idfApplicable = $policy->idf_name_applicable;
                @endphp
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="idf_applicable" id="idf_applicable" value="applicable" {{ ($idfApplicable ? 'applicable' : '') == 'applicable' ? 'checked' : '' }}>
                    <label class="btn btn-outline-success mt-green" for="idf_applicable">Applicable</label>
                    <input type="radio" class="btn-check" name="idf_applicable" id="idf_not_applicable" value="not_applicable" {{ (!$idfApplicable ? 'not_applicable' : '') == 'not_applicable' ? 'checked' : '' }}>
                    <label class="btn btn-outline-danger mt-rd" for="idf_not_applicable">Not Applicable</label>
                    <div class="d-flex justify-content-between align-items-center ms-3">
                        <span></span>
                        <button type="button" class="btn btn-sm btn-success add-idf text-white">+ Add IDF Name</button>
                    </div>
                </div>
            </div>
            <div class="idf-container" style="{{ ($idfApplicable ? 1 : 0) == 0 ? 'opacity: 0.5; pointer-events: none;' : '' }}">
                
                <div class="idfs-container">
                    @foreach ($keyRolesE as $kr)
                    <div class="idf-item mb-3 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">IDF Name {{ $loop->iteration }}</span>
                            <button type="button" class="btn btn-sm btn-danger remove-idf text-white" >Remove</button>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-3">
                                <label class="form-label mb-3">Entity Type</label>
                                <select class="form-control entity-type-select" name="idf[{{ $loop->iteration }}][entity_type]">
                                    <option value="">Select Type</option>
                                    <option value="Individual" {{ $kr['type'] == 'Individual' ? 'selected' : '' }}>Individual</option>
                                    <option value="Corporate" {{ $kr['type'] == 'Corporate' ? 'selected' : '' }}>Corporate</option>
                                    <option value="Trust" {{ $kr['type'] == 'Trust' ? 'selected' : '' }}>Trust</option>
                                    <option value="Foundation" {{ $kr['type'] == 'Foundation' ? 'selected' : '' }}>Foundation</option>
                                </select>
                            </div>
                            <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-9">
                                <input type="hidden" name="idf[{{ $loop->iteration }}][id]" value="{{ $kr['id'] ?? '' }}">
                                <div class="individual-name-fields" style="@if($kr['type'] != 'Individual') display: none; @endif">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label">First Name</label>
                                            <input type="text" class="form-control" name="idf[{{ $loop->iteration }}][first_name]" value="{{ $kr['first_name'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Middle Name(s)</label>
                                            <input type="text" class="form-control" name="idf[{{ $loop->iteration }}][middle_name]" value="{{ $kr['middle_name'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" class="form-control" name="idf[{{ $loop->iteration }}][last_name]" value="{{ $kr['last_name'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="entity-name-field" style="@if($kr['type'] == 'Individual') display: none; @endif">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" name="idf[{{ $loop->iteration }}][name]" value="{{ $kr['name'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" rows="2" name="idf[{{ $loop->iteration }}][notes]">{{ $kr['notes'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="role-section mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2 manage-res-tls">
                <h5>IDF Manager </h5>
                @php 
                    $idfMgrApplicable = $policy->idf_manager_applicable;
                @endphp
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="idf_manager_applicable" id="idf_manager_applicable" value="applicable" {{ ($idfMgrApplicable ? 'applicable' : '') == 'applicable' ? 'checked' : '' }}>
                    <label class="btn btn-outline-success mt-green" for="idf_manager_applicable">Applicable</label>
                    <input type="radio" class="btn-check" name="idf_manager_applicable" id="idf_manager_not_applicable" value="not_applicable" {{ (!$idfMgrApplicable ? 'not_applicable' : '') == 'not_applicable' ? 'checked' : '' }}>
                    <label class="btn btn-outline-danger mt-rd"for="idf_manager_not_applicable">Not Applicable</label>
                     <div class="d-flex justify-content-between align-items-center ms-3">
                        <span></span>
                        <button type="button" class="btn btn-sm btn-success add-idf-manager text-white">+ Add IDF Manager</button>
                    </div>
                </div>
            </div>
            <div class="idf-manager-container" style="{{ ($idfMgrApplicable ? 1 : 0) == 0 ? 'opacity: 0.5; pointer-events: none;' : '' }}">
               
                <div class="idf-managers-container">
                    @foreach ($keyRolesF as $kr)
                    <div class="idf-manager-item mb-3 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">IDF Manager {{ $loop->iteration }}</span>
                            <button type="button" class="btn btn-sm btn-danger remove-idf-manager text-white" >Remove</button>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-3">
                                <label class="form-label mb-3">Entity Type</label>
                                <select class="form-control entity-type-select" name="idf_manager[{{ $loop->iteration }}][entity_type]">
                                    <option value="">Select Type</option>
                                    <option value="Individual" {{ $kr['type'] == 'Individual' ? 'selected' : '' }}>Individual</option>
                                    <option value="Corporate" {{ $kr['type'] == 'Corporate' ? 'selected' : '' }}>Corporate</option>
                                    <option value="Trust" {{ $kr['type'] == 'Trust' ? 'selected' : '' }}>Trust</option>
                                    <option value="Foundation" {{ $kr['type'] == 'Foundation' ? 'selected' : '' }}>Foundation</option>
                                </select>
                            </div>
                            <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-9">
                                <input type="hidden" name="idf_manager[{{ $loop->iteration }}][id]" value="{{ $kr['id'] ?? '' }}">
                                <div class="individual-name-fields" style="@if($kr['type'] != 'Individual') display: none; @endif">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label">First Name</label>
                                            <input type="text" class="form-control" name="idf_manager[{{ $loop->iteration }}][first_name]" value="{{ $kr['first_name'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Middle Name(s)</label>
                                            <input type="text" class="form-control" name="idf_manager[{{ $loop->iteration }}][middle_name]" value="{{ $kr['middle_name'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" class="form-control" name="idf_manager[{{ $loop->iteration }}][last_name]" value="{{ $kr['last_name'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="entity-name-field" style="@if($kr['type'] == 'Individual') display: none; @endif">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" name="idf_manager[{{ $loop->iteration }}][name]" value="{{ $kr['name'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" rows="2" name="idf_manager[{{ $loop->iteration }}][notes]">{{ $kr['notes'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="role-section mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2 manage-res-tls">
                <h5>Custodian Bank</h5>
                @php 
                    $custodianApplicable = $policy->custodian_applicable;
                @endphp
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="custodian_applicable" id="custodian_applicable" value="applicable" {{ ($custodianApplicable ? 'applicable' : '') == 'applicable' ? 'checked' : '' }}>
                    <label class="btn btn-outline-success mt-green" for="custodian_applicable">Applicable</label>
                    <input type="radio" class="btn-check" name="custodian_applicable" id="custodian_not_applicable" value="not_applicable" {{ (!$custodianApplicable ? 'not_applicable' : '') == 'not_applicable' ? 'checked' : '' }}>
                    <label class="btn btn-outline-danger mt-rd" for="custodian_not_applicable">Not Applicable</label>
                    <div class="d-flex justify-content-between align-items-center ms-3">
                        <span></span>
                        <button type="button" class="btn btn-sm btn-success add-custodian text-white">+ Add Custodian Bank</button>
                    </div>
                </div>
            </div>
            <div class="custodian-container" style="{{ ($custodianApplicable ? 1 : 0) == 0 ? 'opacity: 0.5; pointer-events: none;' : '' }}">
                
                <div class="custodians-container">
                    @foreach ($keyRolesG as $kr)                        
                    <div class="custodian-item mb-3 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">Custodian Bank {{ $loop->iteration }}</span>
                            <button type="button" class="btn btn-sm btn-danger remove-custodian text-white" >Remove</button>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-3">
                                <label class="form-label mb-3">Entity Type</label>
                                <select class="form-control entity-type-select" name="custodian[{{ $loop->iteration }}][entity_type]">
                                    <option value="">Select Type</option>
                                    <option value="Individual" {{ $kr['type'] == 'Individual' ? 'selected' : '' }}>Individual</option>
                                    <option value="Corporate" {{ $kr['type'] == 'Corporate' ? 'selected' : '' }}>Corporate</option>
                                    <option value="Trust" {{ $kr['type'] == 'Trust' ? 'selected' : '' }}>Trust</option>
                                    <option value="Foundation" {{ $kr['type'] == 'Foundation' ? 'selected' : '' }}>Foundation</option>
                                </select>
                            </div>
                            <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-9">
                                <input type="hidden" name="custodian[{{ $loop->iteration }}][id]" value="{{ $kr['id'] ?? '' }}">
                                <div class="individual-name-fields" style="@if($kr['type'] != 'Individual') display: none; @endif">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label">First Name</label>
                                            <input type="text" class="form-control" name="custodian[{{ $loop->iteration }}][first_name]" value="{{ $kr['first_name'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Middle Name(s)</label>
                                            <input type="text" class="form-control" name="custodian[{{ $loop->iteration }}][middle_name]" value="{{ $kr['middle_name'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" class="form-control" name="custodian[{{ $loop->iteration }}][last_name]" value="{{ $kr['last_name'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="entity-name-field" style="@if($kr['type'] == 'Individual') display: none; @endif">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" name="custodian[{{ $loop->iteration }}][name]" value="{{ $kr['name'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" rows="2" name="custodian[{{ $loop->iteration }}][notes]">{{ $kr['notes'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

<div class="mb-3 float-end">
    @if(request()->route()->getName() != 'cases.view')
    <button type="submit" data-type="next" data-next="section-b-1" class="btn btn-primary save-next">Save & Next</button>
    @endif
</div>
</form>
