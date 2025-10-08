<div class="mt-4">
    <form id="form-section-b-1">
        <input type="hidden" id="b-1-edit-id" name="policy_holder_id" value="">
        <div class="row mb-3">
            <label class="col-sm-12 mb-3">Entity Type: </label>
            <div class="col-sm-12 chek-bxsz">
                <div class="form-check form-check-inline">
                    <input class="form-check-input section-b-1-type" name="type" type="radio" value="Individual" id="stts-individual"  > <label for="stts-individual"> Individual </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input section-b-1-type" name="type" type="radio" value="Corporate" id="stts-corporate" checked> <label for="stts-corporate"> Corporate </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input section-b-1-type" name="type" type="radio" value="Trust" id="stts-trustz" > <label for="stts-trustz"> Trust </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input section-b-1-type" name="type" type="radio" value="Foundation" id="stts-foundation" > <label for="stts-foundation"> Foundation </label>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-12">
                <div class="row section-b-1-individual-fields d-none">
                    <div class="col-md-4 mb-2 mb-md-0">
                        <label class="form-label col-form-label">First Name  </label>
                        <input type="text" class="form-control section-b-1-first-name" name="first_name" placeholder="First name" value="">
                    </div>
                    <div class="col-md-4 mb-2 mb-md-0">
                        <label class="form-label col-form-label">Middle Name </label>
                        <input type="text" class="form-control section-b-1-middle-name" name="middle_name" placeholder="Middle name" value="">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label col-form-label">Last Name</label>
                        <input type="text" class="form-control section-b-1-last-name" name="last_name" placeholder="Last name" value="">
                    </div>
                </div>
                <div class="section-b-1-entity-field">
                    <label class="form-label col-form-label">Full Name </label>
                    <input type="text" class="form-control section-b-1-policyholder-name" id="name" name="name" value="" >
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-lg-12 col-xl-6">
                <div class="row">
                    <label class="col-sm-12  section-b-1-place-label">Place Of Birth: </label>
                    <div class="col-sm-12">
                        <input type="text" class="form-control section-b-1-place-birth" name="place_of_birth" id="place_of_birth" value="" >
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-xl-6">
                <div class="row">
                    <label class="col-sm-12  section-b-1-date-label">Date Of Birth: </label>
                    <div class="col-sm-12">
                        <input type="text" class="form-control section-b-1-date-birth" readonly  name="dob" id="dob" value="" >
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-12">Residential/ Registered Address: </label>
            <div class="col-sm-12">
                <input type="text" class="form-control section-b-1-address" name="address_line_1" id="address_line_1" value="" >
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-lg-12 col-xl-12 col-xxl-4">
                <div class="row">   
                    <label class="col-sm-12">Country: </label>
                    <div class="col-sm-12">
                        <select name="country" id="country" class="form-control section-b-1-country">
                            <option value=""></option>
                            @foreach (Helper::allCountries() as $cntry)
                                <option value="{{ $cntry }}"  > {{ $cntry }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-xl-12 col-xxl-4 ">
                <div class="row">
                    <label class="col-sm-12">City: </label>
                    <div class="col-sm-12">
                        <input type="text" name="city" id="city" class="form-control section-b-1-city" value="" >
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-xl-12 col-xxl-4">
                <div class="row">
                    <label class="col-sm-12">Postcode/ZIP: </label>
                    <div class="col-sm-12">
                        <input type="text" class="form-control section-b-1-zip" name="zipcode" id="zipcode" value="" >
                    </div>
                </div>
            </div>
        </div>        
       
        <div class="row mb-3 section-b-1-individual-status d-none">
            <label class="col-sm-12 col-form-label">Marital Status: </label>
            <div class="col-sm-12 chek-bxsz">
                <div class="form-check form-check-inline">
                    <input class="form-check-input section-b-1-marital-status" name="marital_status" type="radio" value="single" id="marital-single" checked> <label for="marital-single"> Single </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input section-b-1-marital-status" name="marital_status" type="radio" value="married" id="marital-married" > <label for="marital-married"> Married </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input section-b-1-marital-status" name="marital_status" type="radio" value="divorced" id="marital-divorced" > <label for="marital-divorced"> Divorced </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input section-b-1-marital-status" name="marital_status" type="radio" value="separated" id="marital-separated"> <label for="marital-separated"> Separated </label>
                </div>
            </div>
        </div>

        <div class="row mb-3 section-b-1-entity-status">
            <label class="col-sm-12 mb-3">Entity Status: </label>
            <div class="col-sm-12">
                <div class="row chek-bxsz">
                    <div class="col-xl-6 radio-hor">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input section-b-1-entity-status" name="entity_status" type="radio" value="corporation" id="entity-corp" checked> <label for="entity-corp"> Corporation </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input section-b-1-entity-status" name="entity_status" type="radio" value="llc" id="entity-llc"> <label for="entity-llc" > LLC </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input section-b-1-entity-status" name="entity_status" type="radio" value="trust" id="entity-trust"> <label for="entity-trust" > Trust </label>
                        </div>
                    </div>
                    <div class="col-xl-6 radio-hor">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input section-b-1-entity-status" name="entity_status" type="radio" value="partnership" id="entity-partnership"> <label for="entity-partnership" > Partnership </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input section-b-1-entity-status" name="entity_status" type="radio" value="foundation" id="entity-foundation"> <label for="entity-foundation" > Foundation </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input section-b-1-entity-status" name="entity_status" type="radio" value="other" id="entity-other"> <label for="entity-other"> Other </label>
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <input type="text" class="form-control section-b-1-entity-status-other d-none" name="entity_status_other" placeholder="Other (specify)">
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-lg-6">
                <div class="row">
                    <label class="col-sm-12 mb-3">Nationality/ Country of Registration:  </label>
                    <div class="col-sm-12">
                        <select class="form-control section-b-1-nationality" name="national_country_of_registration">
                            <option value=""></option>
                            @foreach (Helper::allCountries() as $cntry)
                                <option value="{{ $cntry }}" > {{ $cntry }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row ">
                    <label class="col-sm-12 mb-3">Gender:  </label>
                    <div class="col-sm-12 chek-bxsz">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input section-b-1-gender" id="stts-male" type="radio" name="gender" value="male" checked>
                            <label for="stts-male"> Male </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input section-b-1-gender" id="stts-female" type="radio" name="gender" value="female">
                            <label for="stts-female"> Female </label> 
                        </div>
                   </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-12 mb-3">Country of Legal Residence/ Domicile:  </label>
            <div class="col-sm-12">
                <select class="form-control section-b-1-legal-residence" name="country_of_legal_residence">
                    <option value=""></option>
                    @foreach (Helper::allCountries() as $cntry)
                        <option value="{{ $cntry }}"  > {{ $cntry }} </option>
                    @endforeach
                </select>
            </div>
        </div>

        <label class="col-sm-12 mb-3">Countries of Tax Residence:  </label>
        <div class="row mb-3 section-b-1-country-tax-residence-row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-10">
                        <select class="form-control section-b-1-country-tax-residence" name="all_countries[]">
                            <option value=""></option>
                            @foreach (Helper::allCountries() as $cntry)
                                <option value="{{ $cntry }}" > {{ $cntry }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-success section-b-1-add">+</button>
                        <button type="button" class="btn btn-danger section-b-1-remove">-</button>
                    </div>
                </div>
            </div>

        </div>

        <div class="row mb-3">
            <label class="col-sm-12 mb-3">Passport Number(s) & Country(ies) of Issuance:  </label>
            <div class="col-sm-12">
                <div class="row align-items-end section-b-1-passport-row">
                    <div class="col-sm-5 mb-2">
                        <input type="text" class="form-control section-b-1-passport" name="passport_number[]" placeholder="Passport Number" value="">
                    </div>
                    <div class="col-sm-5 mb-2">
                        <select class="form-control section-b-1-passport-issue-country" name="country_of_issuance[]">
                            <option value=""></option>
                            @foreach (Helper::allCountries() as $cntry)
                                <option value="{{ $cntry }}" > {{ $cntry }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2 mb-2">
                        <button type="button" class="btn btn-success section-b-1-passport-add">+</button>
                        <button type="button" class="btn btn-danger section-b-1-passport-remove">-</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-lg-12 col-xl-12 col-xxl-6">
                <div class="row">
                    <label class="col-sm-12 mb-3">Tax Identification Number (TIN):  </label>
                    <div class="col-sm-12">
                        <div class="row align-items-end section-b-1-tin-row">
                            <div class="col-sm-9 mb-2">
                                <input type="text" class="form-control section-b-1-tin" name="tin[]" placeholder="TIN" value="">
                            </div>
                            <div class="col-sm-3 mb-2">
                                <button type="button" class="btn btn-success section-b-1-tin-add">+</button>
                                <button type="button" class="btn btn-danger section-b-1-tin-remove">-</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-xl-12 col-xxl-6">
                <div class="row">
                    <label class="col-sm-12 mb-3">Legal Entity Identifier (LEI) or Other:  </label>
                    <div class="col-sm-12">
                        <input type="text" class="form-control section-b-1-lei" name="lei" value="" >
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <label class="col-sm-12 mb-3">Contact Number:</label>
                <div class="col-sm-12">
                    <input type="hidden" class="form-control section-b-1-contact-number-dial_code" name="dial_code" value="" >
                    <input type="tel" class="form-control section-b-1-contact-number" name="phone_number" value="" >
                </div>
            </div>
            <div class="col-6">
                <label class="col-sm-12 mb-3">E-Mail:  </label>
                <div class="col-sm-12">
                    <input type="email" class="form-control section-b-1-email" name="email" value="" >
                </div>
            </div>
        </div>

        <div class="mb-3 float-end">
            @if(request()->route()->getName() != 'cases.view')
            {{-- <button type="button" data-type="draft" class="btn btn-primary save-draft">Save Draft</button> --}}
            <button type="submit" data-type="next" data-next="section-b-2" class="btn btn-primary save-next me-3">Save & Next</button>
            <button type="submit" class="btn btn-primary float-end me-2" name="add_more_a1" value="1">Save & Add New</button>
            @endif
        </div>
    </form>
</div>