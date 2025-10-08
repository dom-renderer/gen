<form id="form-section-b-2">
    <input type="hidden" id="b-2-edit-id" name="policy_controller_id" value="">

    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-md-4 mb-2 mb-md-0">
                    <label class="form-label fw-bold">First Name </label>
                    <input type="text" class="form-control secction-b-2-first-name" name="first_name" placeholder="First name" value="{{ isset($polhol['id']) ? ($polhol['first_name'] ?? '') : '' }}">
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <label class="form-label fw-bold">Middle Name </label>
                    <input type="text" class="form-control secction-b-2-middle-name" name="middle_name" placeholder="Middle name" value="{{ isset($polhol['id']) ? ($polhol['middle_name'] ?? '') : '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Last Name </label>
                    <input type="text" class="form-control secction-b-2-last-name" name="last_name" placeholder="Last name" value="{{ isset($polhol['id']) ? ($polhol['last_name'] ?? '') : '' }}">
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-lg-6">
            <div class="row">
                <label class="col-sm-12">Place Of Birth:  </label>
                <div class="col-sm-12">
                    <input type="text" class="form-control secction-b-2-place-birth" name="place_of_birth" id="place_of_bi`rth" value="{{ isset($polhol['id']) ? ($polhol['place_of_birth']) : '' }}" >
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <label class="col-sm-12">Date Of Birth:  </label>
                <div class="col-sm-12">
                    <input type="text" class="form-control secction-b-2-date-birth" readonly  name="dob" id="dob-b-2" value="{{ isset($polhol['id']) ? ($polhol['date_of_birth']) : '' }}" >
                </div>
            </div>
        </div>
         
    </div>
    <div class="row mb-3">
        <label class="col-sm-12">Residential Address:  </label>
        <div class="col-sm-12">
            <input type="text" class="form-control secction-b-2-address" name="address_line_1" id="address_line_1" value="{{ isset($polhol['id']) ? ($polhol['address_line_1']) : '' }}" >
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-lg-12 col-xl-12 col-xxl-4">
            <label class="col-sm-12">Country:  </label>
            <div class="col-sm-12 mt-3">
                <select name="country" id="country" class="form-control secction-b-2-country">
                    <option value=""></option>
                    @foreach (Helper::allCountries() as $cntry)
                        <option value="{{ $cntry }}" @if((isset($polhol['id']) ? ($polhol['country']) : '') == $cntry) selected @endif > {{ $cntry }} </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-12 col-xl-12 col-xxl-4">
            <div class="row mb-3">
                <label class="col-sm-12">City:  </label>
                <div class="col-sm-12">
                    <input type="text" name="city" id="city" class="form-control secction-b-2-city" value="{{ isset($polhol['id']) ? ($polhol['city']) : '' }}" >
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-xl-12 col-xxl-4">
            <div class="row mb-3">
                <label class="col-sm-12">Postcode/ ZIP:  </label>
                <div class="col-sm-12">
                    <input type="text" class="form-control secction-b-2-zip" name="zipcode" id="zipcode" value="{{ isset($polhol['id']) ? ($polhol['zipcode']) : '' }}" >
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-xl-6 col-6">
            <div class="row chek-bxsz">
                <label class="col-sm-12 mb-3">Status:  </label>
                <div class="col-sm-12 radio-hor">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input secction-b-2-status" name="status" type="radio" value="single" id="stts-single-b-2" checked> <label for="stts-single-b-2"> Single </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input secction-b-2-status" name="status" type="radio" value="married" id="stts-married-b-2" @if(isset($polhol['id']) && $polhol['personal_status'] == 'married') checked @endif> <label for="stts-married-b-2"> Married </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input secction-b-2-status" name="status" type="radio" value="divorced" id="stts-divorced-b-2" @if(isset($polhol['id']) && $polhol['personal_status'] == 'divorced') checked @endif> <label for="stts-divorced-b-2"> Divorced </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input secction-b-2-status" name="status" type="radio" value="separated" id="stts-separated-b-2"> <label for="stts-separated-b-2" @if(isset($polhol['id']) && $polhol['personal_status'] == 'separated') checked @endif> Separated </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-6">
            <div class="row mb-3 ">
                <div class="col-lg-12 chek-bxsz">
                    <label class="col-sm-12  mb-3">Smoker Status:  </label>
                    <div class="col-sm-12 radio-hor">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input secction-b-2-smoker-status" name="smoker_status" type="radio" value="smoker" id="stts-smoker" checked> <label for="stts-smoker"> Smoker </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input secction-b-2-smoker-status" name="smoker_status" type="radio" value="non-smoker" id="stts-non-smoker" @if(isset($polhol['id']) && $polhol['smoker_status'] == 'non-smoker') checked @endif> <label for="stts-non-smoker"> Non-Smoker </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-lg-6">
            <div class="row">
                <label class="col-sm-12">Nationality:  </label>
                <div class="col-sm-12">
                    <input type="text" class="form-control secction-b-2-nationality" name="national_country_of_registration" value="{{ isset($polhol['id']) ? ($polhol['national_country_of_registration']) : '' }}" >
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <label class="col-sm-12">Gender:  </label>
                <div class="col-sm-12 chek-bxsz  mt-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input secction-b-2-gender" id="stts-male" type="radio" name="gender" value="male" checked>
                        <label for="stts-male"> Male </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input secction-b-2-gender" id="stts-female" type="radio" name="gender" value="female" @if(isset($polhol['id']) && $polhol['gender'] == 'female') checked @endif>
                        <label for="stts-female"> Female </label>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <div class="row mb-3">
        <label class="col-sm-12 mb-3">Country of Legal Residence:  </label>
        <div class="col-sm-12">
            <select class="form-control secction-b-2-legal-residence" name="country_of_legal_residence">
                <option value=""></option>
                @foreach (Helper::allCountries() as $cntry)
                    <option value="{{ $cntry }}" @if((isset($polhol['id']) ? ($polhol['country_of_legal_residence']) : '') == $cntry) selected @endif > {{ $cntry }} </option>
                @endforeach
            </select>
        </div>
    </div>

    <label class="col-sm-12  mb-3">Countries of Tax Residence:  </label>
    @forelse(\App\Models\PolicyCountryOfTaxResidence::where('policy_id', $policy->id)->where('eloquent', \App\Models\PolicyController::class)->where('eloquent_id', isset($polhol['id']) ? $polhol['id'] : null)->get() as $rowA)
            <div class="row mb-3 section-b-2-country-tax-residence-row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-sm-10">
                            <select class="form-control section-b-2-country-tax-residence" name="all_countries[]">
                                <option value=""></option>
                                @foreach (Helper::allCountries() as $cntry)
                                    <option value="{{ $cntry }}" @if($rowA->country == $cntry) selected @endif > {{ $cntry }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-success section-b-2-add">+</button>
                            <button type="button" class="btn btn-danger section-b-2-remove">-</button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="row mb-3 section-b-2-country-tax-residence-row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-sm-10">
                            <select class="form-control section-b-2-country-tax-residence" name="all_countries[]">
                                <option value=""></option>
                                @foreach (Helper::allCountries() as $cntry)
                                    <option value="{{ $cntry }}" > {{ $cntry }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-success section-b-2-add">+</button>
                            <button type="button" class="btn btn-danger section-b-2-remove">-</button>
                        </div>
                    </div>
                </div>
            </div>
    @endforelse
    <div class="row mb-3">
        <label class="col-sm-12">Passport Number(s) & Country(ies) of Issuance:  </label>
        <div class="col-sm-12">
            @php
                $passports = isset($polhol['id']) && !empty($polhol['passport_number']) ? json_decode($polhol['passport_number']) : [''];
                $issuances = isset($polhol['id']) && !empty($polhol['country_of_issuance']) ? json_decode($polhol['country_of_issuance']) : [''];
            @endphp
            @foreach ($passports as $idx => $pp)
            <div class="row align-items-end section-b-2-passport-row {{ $idx>0 ? 'mt-2' : '' }}">
                <div class="col-sm-5 mb-2">
                    <input type="text" class="form-control secction-b-2-passport" name="passport_number[]" placeholder="Passport Number" value="{{ trim($pp) }}">
                </div>
                <div class="col-sm-5 mb-2">
                    <select class="form-control secction-b-2-passport-issue-country" name="country_of_issuance[]">
                        <option value=""></option>
                        @foreach (Helper::allCountries() as $cntry)
                            <option value="{{ $cntry }}" @if(($issuances[$idx] ?? '') == $cntry) selected @endif> {{ $cntry }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 mb-2">
                    <button type="button" class="btn btn-success section-b-2-passport-add">+</button>
                    <button type="button" class="btn btn-danger section-b-2-passport-remove">-</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="row mb-3">
        <label class="col-sm-12">Relationship to Policyholder  </label>
        <div class="col-sm-12">
            <input type="text" name="relationship_to_policyholder" class="form-control" value="{{ isset($polhol['id']) ? ($polhol['relationship_to_policyholder']) : '' }}" >
        </div>
    </div>
    <div class="row mb-3">
        <label class="col-sm-12">E-Mail:  </label>
        <div class="col-sm-12">
            <input type="email" class="form-control secction-b-2-email" name="email" value="{{ isset($polhol['id']) ? ($polhol['email']) : '' }}" >
        </div>
    </div>

    <div class="mb-3 float-end">
        @if(request()->route()->getName() != 'cases.view')
        {{-- <button type="submit" data-type="draft" class="btn btn-primary save-draft">Save Draft</button> --}}
        <button type="submit" data-type="next" data-next="section-c-1" class="btn btn-primary save-next ">Save & Next</button>
        @endif
    </div>    
</form>