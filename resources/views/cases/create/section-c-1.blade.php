<form id="form-section-c-1">
    <input type="hidden" id="c-1-edit-id" name="id" value="">
    <div class="row mb-3">
        <label class="col-sm-12 mb-3"> Entity Type  </label>
        <div class="col-sm-12 chek-bxsz">
            <div class="form-check form-check-inline">
                <input class="form-check-input section-c-1-type" name="c1_type" type="radio" value="Individual" id="c1-type-individual">
                <label for="c1-type-individual"> Individual </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input section-c-1-type" name="c1_type" type="radio" value="Corporate" id="c1-type-corporate" checked>
                <label for="c1-type-corporate"> Corporate </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input section-c-1-type" name="c1_type" type="radio" value="Trust" id="c1-type-trust">
                <label for="c1-type-trust"> Trust </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input section-c-1-type" name="c1_type" type="radio" value="Foundation" id="c1-type-foundation">
                <label for="c1-type-foundation"> Foundation </label>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="row section-c-1-individual-fields d-none">
                <div class="col-md-4 mb-2 mb-md-0">
                    <label class="form-label fw-bold">First Name </label>
                    <input type="text" class="form-control section-c-1-first-name" name="first_name" placeholder="First name" value="">
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <label class="form-label fw-bold">Middle Name </label>
                    <input type="text" class="form-control section-c-1-middle-name" name="middle_name" placeholder="Middle name" value="">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Last Name </label>
                    <input type="text" class="form-control section-c-1-last-name" name="last_name" placeholder="Last name" value="">
                </div>
            </div>
            <div class="section-c-1-entity-field">
                <label for="c1_controlling_person_name" class="form-label fw-bold">Full Name </label>
                <input type="text" class="form-control section-c-1-full-name" id="c1_controlling_person_name" name="controlling_person_name" value="">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-lg-12 col-xxl-6 mb-3">
            <label for="c1_place_of_birth" class="form-label c1-place-label">Establishment </label>
            <input type="text" class="form-control" id="c1_place_of_birth" name="place_of_birth" >
        </div>
        <div class="col-xl-6 col-lg-12 col-xxl-6 mb-3">
            <label for="c1_date_of_birth" class="form-label c1-date-label">Establishment </label>
            <input type="text" class="form-control" id="c1_date_of_birth" name="date_of_birth" readonly >
        </div>
    </div>
    
    <div class="row">
        <div class="col-12 mb-3">
            <label for="c1address" class="form-label c1-address-label">Residential Address </label>
            <input type="text" class="form-control" id="c1address" name="address" >
        </div>
    </div>
    
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-xxl-4 mb-3">
            <label for="c1country" class="form-label mb-3">Country </label>
            <select  class="form-control" id="c1country" name="country" >
                <option value=""></option>
                @foreach (Helper::allCountries() as $cntry)
                    <option value="{{ $cntry }}"> {{ $cntry }} </option>
                @endforeach
            </select>
        </div>
        <div class="col-xl-6 col-lg-6 col-xxl-4 mb-3">
            <label for="c1city" class="form-label">City </label>
            <input type="text" class="form-control" id="c1city" name="city" >
        </div>
        <div class="col-xl-6 col-lg-6 col-xxl-4 mb-3">
            <label for="c1zip" class="form-label">Postcode/ZIP </label>
            <input type="text" class="form-control" id="c1zip" name="zip" >
        </div>
    </div>
    
    <div class="row c1-individual-extra d-none">
        <div class="col-md-6 mb-3">
            <label class="form-label mb-3">Status </label>
            <div class="form-check">
                <input class="form-check-input c1sts" type="radio" name="status" id="c1status_single" value="single" checked >
                <label class="form-check-label" for="c1status_single">Single</label>
            </div>
            <div class="form-check">
                <input class="form-check-input c1sts" type="radio" name="status" id="c1status_married" value="married" >
                <label class="form-check-label" for="c1status_married">Married</label>
            </div>
            <div class="form-check">
                <input class="form-check-input c1sts" type="radio" name="status" id="c1status_divorced" value="divorced" >
                <label class="form-check-label" for="c1status_divorced">Divorced</label>
            </div>
            <div class="form-check">
                <input class="form-check-input c1sts" type="radio" name="status" id="c1status_separated" value="separated" >
                <label class="form-check-label" for="c1status_separated">Separated</label>
            </div>
        </div>
    </div>
    
    <div class="row c1-individual-extra d-none">
        <div class="col-md-6 mb-3">
            <label for="c1nationality" class="form-label">Nationality </label>
            <input type="text" class="form-control" id="c1nationality" name="nationality" >
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Gender </label>
            <div class="form-check">
                <input class="form-check-input c1-gndr" type="radio" name="gender" id="c1gender_male" value="male" checked >
                <label class="form-check-label" for="c1gender_male">Male</label>
            </div>
            <div class="form-check">
                <input class="form-check-input c1-gndr" type="radio" name="gender" id="c1gender_female" value="female" >
                <label class="form-check-label" for="c1gender_female">Female</label>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-xxl-12 mb-3">
            <label for="c1country_of_legal_residence" class="form-label mb-3">Country of Legal Residence </label>
            <select class="form-control" id="c1country_of_legal_residence" name="country_of_legal_residence">
                <option value=""></option>
                @foreach (Helper::allCountries() as $cntry)
                    <option value="{{ $cntry }}" > {{ $cntry }} </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-lg-12 col-xxl-12 mb-3" id="tempc1taxbox">
            <label for="c1countries_of_tax_residence" class="form-label mb-3">Countries of Tax Residence </label>
            <div class="row section-c-1-country-tax-residence-row mb-2">
                <div class="col-sm-10">
                    <select class="form-control section-c-1-country-tax-residence" name="all_countries[]">
                        <option value=""></option>
                        @foreach (Helper::allCountries() as $cntry)
                            <option value="{{ $cntry }}" > {{ $cntry }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-success section-c-1-add">+</button>
                    <button type="button" class="btn btn-danger section-c-1-remove ms-2">-</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Passport Number(s) & Country(ies) of Issuance </label>
            <div class="row align-items-end section-c-1-passport-row">
                <div class="col-sm-5 mb-2">
                    <input type="text" class="form-control c1-passport" name="passport_number[]" placeholder="Passport Number">
                </div>
                <div class="col-sm-5 mb-2">
                    <select class="form-control c1-issuance-country" name="country_of_issuance[]">
                        <option value=""></option>
                        @foreach (Helper::allCountries() as $cntry)
                            <option value="{{ $cntry }}"> {{ $cntry }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 mb-2">
                    <button type="button" class="btn btn-success section-c-1-passport-add">+</button>
                    <button type="button" class="btn btn-danger section-c-1-passport-remove ms-2">-</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-xxl-6 mb-3 c1-individual-extra d-none">
            <label for="c1relationship_to_policyholder" class="form-label">Relationship to Policyholder </label>
            <input type="text" class="form-control" id="c1relationship_to_policyholder" name="relationship_to_policyholder" >
        </div>
        <div class="col-xl-12 col-lg-12 col-xxl-6 mb-3">
            <label for="c1email" class="form-label">E-Mail </label>
            <input type="email" class="form-control" id="c1email" name="email" >
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            @if(request()->route()->getName() != 'cases.view')
            <button type="submit" data-type="next" data-next="section-d-1" class="btn btn-primary save-next float-end">Save & Next</button>
            <button type="submit" class="btn btn-primary float-end me-2" name="add_more_a1" value="1">Save & Add New</button>
            @endif
        </div>
    </div>
</form>

<!-- <div class="mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">                    
                    <h4 class="card-title">Saved Insured Life Information</h4>
                </div>
                <div class="card-body">
                    <div id="insured-life-accordion" class="mb-4"></div>
                </div>
            </div>
        </div>
    </div>
</div> -->