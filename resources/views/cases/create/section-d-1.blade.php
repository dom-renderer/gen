<form id="form-section-d-1">
	<input type="hidden" id="d-1-edit-id" name="id" value="">

	<div class="row mb-3">
		<label class="col-sm-12 mb-3"> Entity Type  </label>
		<div class="col-sm-12 chek-bxsz">
			<div class="form-check form-check-inline">
				<input class="form-check-input section-d-1-type" name="d1_type" type="radio" value="Individual" id="d1-type-individual">
				<label for="d1-type-individual"> Individual </label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input section-d-1-type" name="d1_type" type="radio" value="Corporate" id="d1-type-corporate" checked>
				<label for="d1-type-corporate"> Corporate </label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input section-d-1-type" name="d1_type" type="radio" value="Trust" id="d1-type-trust">
				<label for="d1-type-trust"> Trust </label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input section-d-1-type" name="d1_type" type="radio" value="Foundation" id="d1-type-foundation">
				<label for="d1-type-foundation"> Foundation </label>
			</div>
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-12">
			<div class="row section-d-1-individual-fields d-none">
				<div class="col-md-4 mb-2 mb-md-0">
					<label class="form-label fw-bold">First Name </label>
					<input type="text" class="form-control section-d-1-first-name" name="first_name" placeholder="First name" value="">
				</div>
				<div class="col-md-4 mb-2 mb-md-0">
					<label class="form-label fw-bold">Middle Name </label>
					<input type="text" class="form-control section-d-1-middle-name" name="middle_name" placeholder="Middle name" value="">
				</div>
				<div class="col-md-4">
					<label class="form-label fw-bold">Last Name </label>
					<input type="text" class="form-control section-d-1-last-name" name="last_name" placeholder="Last name" value="">
				</div>
			</div>
			<div class="section-d-1-entity-field">
				<label class="form-label fw-bold">Full Name </label>
				<input type="text" class="form-control section-d-1-full-name" name="full_name" value="">
			</div>
		</div>
	</div>

	<div class="row">		
		<div class="col-xl-6 col-lg-6 col-xxl-6 mb-3">
			<label class="form-label d1-place-label">Establishment </label>
			<input type="text" class="form-control" id="d-1-place-of-birth" name="place_of_birth" >
		</div>
		<div class="col-xl-6 col-lg-6 col-xxl-6 mb-3">
			<label class="form-label d1-date-label">Establishment </label>
			<input type="text" class="form-control" id="d-1-dob" name="date_of_birth"  readonly>
		</div>
	</div>
	<div class="row">
		<div class="col-xl-12 col-lg-12 col-xxl-12 mb-3">
			<label class="form-label d1-address-label">Residential Address </label>
			<input type="text" class="form-control" id="d-1-address" name="address" >
		</div>
	</div>

	<div class="row">
		<div class="col-xl-12 col-lg-12 col-xxl-4 mb-3">
			<label class="form-label mb-3">Country </label>
            <select  class="form-control" id="d-1-country" name="country">
                <option value=""></option>
                @foreach (Helper::allCountries() as $cntry)
                    <option value="{{ $cntry }}"> {{ $cntry }} </option>
                @endforeach
            </select>
		</div>
		<div class="col-xl-12 col-lg-12 col-xxl-4 mb-3">
			<label class="form-label">City </label>
			<input type="text" class="form-control" id="d-1-city" name="city" >
		</div>
		<div class="col-xl-12 col-lg-12 col-xxl-4 mb-3">
			<label class="form-label">Postcode/ ZIP </label>
			<input type="text" class="form-control" id="d-1-zip" name="zip" >
		</div>
	</div>

	<div class="row d1-individual-extra d-none">
		<div class="col-md-12 mb-3">
			<label class="form-label">Status </label>
			<div class="form-check"><input class="form-check-input" type="radio" name="d-1-status" id="d-1-status-single" value="single" checked><label class="form-check-label" for="d-1-status-single">Single</label></div>
			<div class="form-check"><input class="form-check-input" type="radio" name="d-1-status" id="d-1-status-married" value="married"><label class="form-check-label" for="d-1-status-married">Married</label></div>
			<div class="form-check"><input class="form-check-input" type="radio" name="d-1-status" id="d-1-status-divorced" value="divorced"><label class="form-check-label" for="d-1-status-divorced">Divorced</label></div>
			<div class="form-check"><input class="form-check-input" type="radio" name="d-1-status" id="d-1-status-separated" value="separated"><label class="form-check-label" for="d-1-status-separated">Separated</label></div>
		</div>
	</div>

	<div class="row d1-individual-extra d-none">
		<div class="col-md-6 mb-3">
			<label class="form-label">Nationality </label>
			<input type="text" class="form-control" id="d-1-nationality" name="nationality" >
		</div>
		<div class="col-md-6 mb-3">
			<label class="form-label">Gender </label>
			<div class="form-check"><input class="form-check-input" type="radio" name="d-1-gender" id="d-1-gender-male" value="male" checked><label class="form-check-label" for="d-1-gender-male">Male</label></div>
			<div class="form-check"><input class="form-check-input" type="radio" name="d-1-gender" id="d-1-gender-female" value="female"><label class="form-check-label" for="d-1-gender-female">Female</label></div>
		</div>
	</div>

	<div class="row">
		<div class="col-xl-12 col-lg-12 col-xxl-6 mb-3">
			<label class="form-label mb-3">Country of Legal Residence </label>
            <select class="form-control" id="d-1-legal-residence" name="country_of_legal_residence">
                <option value=""></option>
                @foreach (Helper::allCountries() as $cntry)
                    <option value="{{ $cntry }}" > {{ $cntry }} </option>
                @endforeach
            </select>
		</div>
		<div class="col-xl-12 col-lg-12 col-xxl-6 mb-1" id="tempd1taxbox">
            <label for="c1countries_of_tax_residence" class="form-label mb-3">Countries of Tax Residence </label>
            <div class="row section-d-1-country-tax-residence-row">
                <div class="col-sm-9">
					<select class="form-control section-d-1-country-tax-residence" name="all_countries[]" >
						<option value=""></option>
						@foreach (Helper::allCountries() as $cntry)
							<option value="{{ $cntry }}" > {{ $cntry }} </option>
						@endforeach
					</select>
                </div>
                <div class="col-sm-3 mb-3">
                    <button type="button" class="btn btn-success section-d-1-add">+</button>
                    <button type="button" class="btn btn-danger section-d-1-remove ms-2">-</button>
                </div>
            </div>
		</div>
	</div>

	<div class="row">
		<div class="col-12 mb-3">
			<label class="form-label">Passport Number(s) & Country(ies) of Issuance </label>
			<div class="row align-items-end section-d-1-passport-row">
				<div class="col-sm-6 mb-2">
					<input type="text" class="form-control d1-passport" name="passport_number[]" placeholder="Passport Number">
				</div>
				<div class="col-sm-4 mb-2">
					<select class="form-control d1-issuance-country" name="country_of_issuance[]">
						<option value=""></option>
						@foreach (Helper::allCountries() as $cntry)
							<option value="{{ $cntry }}"> {{ $cntry }} </option>
						@endforeach
					</select>
				</div>
				<div class="col-sm-2 mb-2">
					<button type="button" class="btn btn-success section-d-1-passport-add">+</button>
					<button type="button" class="btn btn-danger section-d-1-passport-remove ms-2">-</button>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xl-12 col-lg-12 col-xxl-6 mb-3 d1-individual-extra d-none">
			<label class="form-label">Relationship to Policyholder </label>
			<input type="text" class="form-control" name="relationship_to_policyholder" id="d-1-relationship" >
		</div>
		
	</div>

	<div class="row">
		<div class="col-xl-12 col-lg-12 col-xxl-6 mb-3">
			<label class="form-label">E-Mail </label>
			<input type="email" class="form-control" name="email" id="d-1-email" >
		</div>
		<div class="col-xl-12 col-lg-12 col-xxl-6 mb-3 w-inpt">
			<label class="form-label d-block mb-3 ">Contact Number </label>
			<input type="hidden" class="w-100" id="d-1-dial_code" name="dial_code" value="">
			<input type="tel" class="form-control" name="phone_number" id="d-1-phone_number" value="">
		</div>
	</div>

	<div class="row">
		<div class="col-xl-12 col-lg-12 col-xxl-6 mb-3">
			<label class="form-label ">Death Benefit Allocation (%) </label>
			<input type="number" class="form-control" id="d-1-allocation" name="beneficiary_death_benefit_allocation" min="0" max="100" step="0.01" >
		</div>
		<div class="col-xl-12 col-lg-12 col-xxl-6 mb-3">
			<label class=" form-label d-block mb-3">Designation </label>
			<div class="form-check form-check form-check-inline"><input class="form-check-input" type="radio" name="d-1-designation" id="d-1-designation-revocable" value="revocable" checked><label class="form-check-label" for="d-1-designation-revocable">Revocable</label></div>
			<div class="form-check form-check form-check-inline"><input class="form-check-input" type="radio" name="d-1-designation" id="d-1-designation-irrevocable" value="irrevocable"><label class="form-check-label" for="d-1-designation-irrevocable">Irrevocable</label></div>
		</div>
	</div>

    <div class="row">
        <div class="col-12">
			@if(request()->route()->getName() != 'cases.view')
			<button type="submit" data-type="next" data-next="section-e-1" class="btn btn-primary save-next float-end same-kyc-btn">Save & Next</button>
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
					<h4 class="card-title">Saved Beneficiary Information</h4>
				</div>
                <div class="card-body">
					<div id="d-1-beneficiaries-accordion" class="mb-4"></div>
                </div>
            </div>
        </div>
    </div>
</div> -->