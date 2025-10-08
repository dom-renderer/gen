<form id="form-section-a-1">
    <input type="hidden" name="id" id="id-for-edit-a-1">
    <div class="mb-5">
        <label class="form-label fw-bold mb-3">Select Introducer</label>
        <select class="form-control" id="section-a-1-introducer-select" name="introducer_id"></select>
    </div>

    <div class="mb-5">
        <label class="form-label fw-bold mb-2">Please select type of Introducer   </label>
        <div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="section_a_1_entity" id="section-a-1-entity" value="Entity">
                <label class="form-check-label mb-3" for="section-a-1-entity">Entity</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="section_a_1_entity" id="section-a-1-individual" value="Individual" checked>
                <label class="form-check-label" for="section-a-1-individual">Individual</label>
            </div>
        </div>
    </div>

    <div class="mb-5">
        <div class="row d-none" id="s1ss1ent">
            <label class="form-label fw-bold"> Introducer Name <span> (Entity)  </span>  </label>
            <input type="text" class="form-control" name="section_a_1_name" placeholder="Please enter full name" value="">
        </div>
        <div class="row" id="s1ss1ind">
            <div class="col-md-4 col-lg-6 col-xl-4">
                <label class="form-label fw-bold">  <span> First Name </span>   </label>
                <input type="text" class="form-control" name="section_a_1_first_name" placeholder="First Name" value="">
            </div>
            <div class="col-md-4 col-lg-6 col-xl-4">
                <label class="form-label fw-bold">  <span> Middle Name </span>   </label>
                <input type="text" class="form-control" name="section_a_1_middle_name" placeholder="Middle Name" value="">
            </div>
            <div class="col-md-4 col-lg-12 col-xl-4">
                <label class="form-label fw-bold">  <span> Last Name </span>  </label>
                <input type="text" class="form-control" name="section_a_1_last_name" placeholder="Last Name" value="">
            </div>
        </div>
    </div>

    <div class="mb-5 row">
        <div class=" col-lg-12 col-md-12 col-xl-6">
            <label class="form-label fw-bold">Email  </label>
            <input type="email" class="form-control" name="section_a_1_email" placeholder="Please enter email address" value="">
        </div>
        <div class="col-lg-12 col-md-12 col-xl-6 wid-mng">
            <label class="form-label fw-bold d-block">Contact Number  </label>
            <input type="hidden" id="section-a-1-dial_code" name="section_a_1_dial_code" value="">
            <input type="tel" class="form-control" name="section_a_1_phone" id="section-a-1-phone_number" value="">
        </div>
    </div>

    <div class="mb-2 row d-none" id="s1ss1contperheader">
        <div class="sub-title" style="padding: 10px 0px 20px 0px;">
            Contact Persons
        </div>
        {{-- <div class="row">
            <div class="col-md-2">
                <label class="form-label fw-bold">  <span> First Name </span>   </label>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">  <span> Middle Name </span>   </label>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">  <span> Last Name </span>  </label>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">  <span> Email </span>  </label>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">  <span> Phone Number </span>  </label>
            </div>
            <div class="col-md-1">
                <label class="form-label fw-bold">  <span> Action </span>  </label>
            </div>
        </div> --}}
    </div>

    <div class="mb-5 row d-none" id="s1ss1contper">
        <div class="row align-items-center">
            <div class="col-md-2 col-xxl-2 col-xl-4 col-lg-4 ">
                <input type="hidden" class="contact_person_id" name="contact_person_id[0]" value="">
                <input type="text" class="form-control contact_person_first_name" name="contact_person_first_name[0]" placeholder="First Name">
            </div>
            <div class="col-md-2 col-xxl-2 col-xl-4 col-lg-4 ">
                <input type="text" class="form-control contact_person_middle_name" name="contact_person_middle_name[0]" placeholder="Middle Name" value="">
            </div>
            <div class="col-md-2 col-xxl-2 col-xl-4 col-lg-4 ">
                <input type="text" class="form-control contact_person_last_name" name="contact_person_last_name[0]" placeholder="Last Name" value="">
            </div>
            <div class="col-md-2 col-xxl-2 col-xl-4 col-lg-4 ">
                <input type="email" class="form-control contact_person_email" name="contact_person_email[0]" placeholder="Email" value="">
            </div>
            <div class="col-md-3 col-xxl-2 col-xl-4 col-lg-4 mb-3">
                <input type="hidden" class="contact_person_phone_number_dial_code sa1edcp1_dc" id="contact_person_phone_number_dial_code_0" name="contact_person_phone_number_dial_code[0]" value="">
                <input type="tel" class="form-control contact_person_phone_number sa1edcp1_pn" name="contact_person_phone_number[0]" id="contact_person_phone_number" value="">
            </div>
            <div class="col-md-1 col-xxl-2 col-xl-4 col-lg-4 butn-plus mb-3">
                <button type="button" class="btn btn-success btn-sm s1ss1contperadd text-white"> + </button>
                <button type="button" class="btn btn-danger btn-sm s1ss1contperrem text-white"> - </button>
            </div>
        </div>
    </div>

    <div class="mb-3 float-end">
        @if(request()->route()->getName() != 'cases.view')
        {{-- <button type="submit" data-type="draft" class="btn btn-primary save-draft">Save Draft</button> --}}
        <button type="submit" data-type="next" data-next="section-a-1" class="btn btn-primary save-next float-end">Save & Next</button>
        <button type="submit" class="btn btn-primary float-end me-2" name="add_more_a1" value="1" id="save-add-new-int-prof">Save & Add New</button>

        @endif
    </div>

</form>