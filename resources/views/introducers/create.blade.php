@extends('layouts.app', ['title' => $title, 'subTitle' => $subTitle])

@push('css')
<style>
    .card {
        border: none;
    }
    div.iti--inline-dropdown {
		min-width: 100%!important;
	}
	.iti__selected-flag {
		height: 32px!important;
	}
	.iti--show-flags {
		width: 100%!important;
	}  
	label.error {
		color: red;
	}
	#section-a-1-phone_number,
	.contact_person_phone_number{
		font-family: "Hind Vadodara",-apple-system,BlinkMacSystemFont,"Segoe UI","Helvetica Neue",Arial,sans-serif;
		font-size: 15px;
	}
    .sub-title h2 {
        padding: 1rem;
    }
    .card-body .form-control {
        color: var(--black);
        font-size: 18px;
        line-height: normal;
        padding: 6px 15px;
        margin-top: 15px;
        border: 1px solid #D9D9D9;
        background-color: #F5F5F5;
        border-radius: 6px;
        -webkit-border-radius: 6px;
        -moz-border-radius: 6px;
        -ms-border-radius: 6px;
        -o-border-radius: 6px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="sub-title">
                <h2>Add New Introducer</h2>
            </div>
            <div class="card-body">
                <form id="introducerForm" method="POST" action="{{ route('introducers.store') }}">
                    @csrf

                    <div class="mb-5">
                        <label class="form-label fw-bold">Please select type of Introducer   </label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" id="section-a-1-entity" value="Entity">
                                <label class="form-check-label" for="section-a-1-entity">Entity</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" id="section-a-1-individual" value="Individual" checked>
                                <label class="form-check-label" for="section-a-1-individual">Individual</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="d-none" id="s1ss1ent">
                            <label class="form-label fw-bold"> Introducer Name <span> (Entity) </span>  </label>
                            <input type="text" class="form-control" name="full_name" placeholder="Please enter full name" value="{{ old('full_name') }}">
                        </div>
                        <div class="row" id="s1ss1ind">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">  <span> First Name </span>  </label>
                                <input type="text" class="form-control" name="name" placeholder="First Name" value="{{ old('name') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">  <span> Middle Name </span>  </label>
                                <input type="text" class="form-control" name="middle_name" placeholder="Middle Name" value="{{ old('middle_name') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">  <span> Last Name </span>  </label>
                                <input type="text" class="form-control" name="last_name" placeholder="Last Name" value="{{ old('last_name') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-5 row">
                        <div class=" col-lg-12 col-md-12 col-xl-6">
                            <label class="form-label fw-bold">Email  </label>
                            <input type="email" class="form-control" name="email" placeholder="Please enter email address" value="{{ old('email') }}">
                        </div>
                        <div class="col-lg-12 col-md-12 col-xl-6">
                            <label class="form-label fw-bold mb-4">Contact Number  </label>
                            <input type="hidden" id="section-a-1-dial_code" name="dial_code" value="{{ old('dial_code') }}">
                            <input type="tel" class="form-control" name="contact_number" id="section-a-1-phone_number" value="{{ old('contact_number') }}">
                        </div>
                    </div>

                    <div class="mb-2 row d-none">
                        <div class="row">
                            <div class="col-md-4 col-lg-6 col-xl-4">
                                <label class="form-label fw-bold">  <span> First Name </span>  </label>
                            </div>
                            <div class="col-md-4 col-lg-6 col-xl-4">
                                <label class="form-label fw-bold">  <span> Middle Name </span>  </label>
                            </div>
                            <div class="col-md-4 col-lg-6 col-xl-4">
                                <label class="form-label fw-bold">  <span> Last Name </span>  </label>
                            </div>
                            <div class="col-md-4 col-lg-6 col-xl-4">
                                <label class="form-label fw-bold">  <span> Email </span>  </label>
                            </div>
                            <div class="col-md-4 col-lg-6 col-xl-4">
                                <label class="form-label fw-bold">  <span> Phone Number </span>  </label>
                            </div>
                            <div class="col-md-4 col-lg-6 col-xl-4">
                                <label class="form-label fw-bold">  <span> Action </span>  </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5 row d-none dpend" id="s1ss1contper">
                        <div class="row align-items-end g-2">
                            <div class="col-md-4 col-lg-6 col-xl-4">
                                <input type="text" class="form-control contact_person_first_name" name="contact_person_first_name[0]" placeholder="First Name" value="{{ old('contact_person_first_name.0') }}">
                            </div>
                            <div class="col-md-4 col-md-4 col-lg-6 col-xl-4">
                                <input type="text" class="form-control contact_person_middle_name" name="contact_person_middle_name[0]" placeholder="Middle Name" value="{{ old('contact_person_middle_name.0') }}">
                            </div>
                            <div class="col-md-4 col-lg-6 col-xl-4">
                                <input type="text" class="form-control contact_person_last_name" name="contact_person_last_name[0]" placeholder="Last Name" value="{{ old('contact_person_last_name.0') }}">
                            </div>
                            <div class="col-md-4 col-lg-6 col-xl-4">
                                <input type="email" class="form-control contact_person_email" name="contact_person_email[0]" placeholder="Email" value="{{ old('contact_person_email.0') }}">
                            </div>
                            <div class="col-md-4 col-lg-6 col-xl-4">
                                <input type="hidden" class="contact_person_phone_number_dial_code" id="contact_person_phone_number_dial_code_0" name="contact_person_phone_number_dial_code[0]" value="{{ old('contact_person_phone_number_dial_code.0') }}">
                                <input type="tel" class="form-control contact_person_phone_number" name="contact_person_phone_number[0]" id="contact_person_phone_number" value="{{ old('contact_person_phone_number.0') }}">
                            </div>
                            <div class="col-md-4 col-lg-6 col-xl-4 d-flex gap-1">
                                <button type="button" class="btn btn-success btn-sm s1ss1contperadd"> + </button>
                                <button type="button" class="btn btn-danger btn-sm s1ss1contperrem"> - </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Introducer</button>
                    <a href="{{ route('introducers.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/intel-tel.css') }}">
@endpush

@push('js')
<script src="{{ asset('assets/js/jquery-validate.min.js') }}"></script>
<script src="{{ asset('assets/js/intel-tel.js') }}"></script>
<script>
$(document).ready(function() {
    function toggleEntityFields() {
        if ($('input[name="type"]:checked').val() === 'Entity') {
            $('#s1ss1ent').removeClass('d-none');
            $('#s1ss1ind').addClass('d-none');
            $('#s1ss1contper').removeClass('d-none');
        } else {
            $('#s1ss1ent').addClass('d-none');
            $('#s1ss1ind').removeClass('d-none');
            $('#s1ss1contper').addClass('d-none');
        }
    }

    $('input[name="type"]').on('change', toggleEntityFields);
    toggleEntityFields();

    const mainPhoneInput = document.querySelector('#section-a-1-phone_number');
    const mainIti = window.intlTelInput(mainPhoneInput, {
        initialCountry: "{{ Helper::$defaulDialCode }}",
        separateDialCode:true,
        nationalMode:false,
        preferredCountries: ["us", "gb", "ca", "hk", "ch", "ae"],
        utilsScript: "{{ asset('assets/js/intel-tel-2.min.js') }}"
    });
    mainPhoneInput.addEventListener('countrychange', function() {
        if (mainIti.isValidNumber()) {
            $('#section-a-1-dial_code').val(mainIti.s.dialCode);
        }
    });
    mainPhoneInput.addEventListener('keyup', function() {
        if (mainIti.isValidNumber()) {
            $('#section-a-1-dial_code').val(mainIti.s.dialCode);
        }
    });

    function initContactIntlTel(el, dialHidden) {
        const iti = window.intlTelInput(el, {
            initialCountry: "{{ Helper::$defaulDialCode }}",
            separateDialCode:true,
            nationalMode:false,
            preferredCountries: ["us", "gb", "ca", "hk", "ch", "ae"],
            utilsScript: "{{ asset('assets/js/intel-tel-2.min.js') }}"
        });
        el.addEventListener('countrychange', function() {
            if (iti.isValidNumber()) {
                $(dialHidden).val(iti.s.dialCode);
            }
        });
        el.addEventListener('keyup', function() {
            if (iti.isValidNumber()) {
                $(dialHidden).val(iti.s.dialCode);
            }
        });
        return iti;
    }

    initContactIntlTel(document.querySelector('#contact_person_phone_number'), '#contact_person_phone_number_dial_code_0');

    $(document).on('click', '.s1ss1contperadd', function() {
        const container = $('#s1ss1contper');
        const count = container.find('.row').length;
        const idx = count;
        const row = $(
            '<div class="row align-items-end g-2 mt-2">'
            + '<div class=" col-md-4 col-lg-6 col-xl-4">'
            + '<input type="text" class="form-control contact_person_first_name" name="contact_person_first_name['+idx+']" placeholder="First Name">'
            + '</div>'
            + '<div class=" col-md-4 col-lg-6 col-xl-4">'
            + '<input type="text" class="form-control contact_person_middle_name" name="contact_person_middle_name['+idx+']" placeholder="Middle Name">'
            + '</div>'
            + '<div class=" col-md-4 col-lg-6 col-xl-4">'
            + '<input type="text" class="form-control contact_person_last_name" name="contact_person_last_name['+idx+']" placeholder="Last Name">'
            + '</div>'
            + '<div class=" col-md-4 col-lg-6 col-xl-4">'
            + '<input type="email" class="form-control contact_person_email" name="contact_person_email['+idx+']" placeholder="Email">'
            + '</div>'
            + '<div class=" col-md-4 col-lg-6 col-xl-4">'
            + '<input type="hidden" class="contact_person_phone_number_dial_code" id="contact_person_phone_number_dial_code_'+idx+'" name="contact_person_phone_number_dial_code['+idx+']" value="">'
            + '<input type="tel" class="form-control contact_person_phone_number" name="contact_person_phone_number['+idx+']" id="contact_person_phone_number">'
            + '</div>'
            + '<div class=" col-md-4 col-lg-6 col-xl-4 d-flex gap-1">'
            + '<button type="button" class="btn btn-success btn-sm s1ss1contperadd"> + </button>'
            + '<button type="button" class="btn btn-danger btn-sm s1ss1contperrem"> - </button>'
            + '</div>'
            + '</div>'
        );
        container.append(row);
        const telInput = row.find('.contact_person_phone_number')[0];
        initContactIntlTel(telInput, '#contact_person_phone_number_dial_code_'+idx);
    });

    $(document).on('click', '.s1ss1contperrem', function() {
        const rows = $('#s1ss1contper .row');
        if (rows.length > 1) {
            $(this).closest('.row').remove();
        }
    });

    $('#introducerForm').validate({
        rules: {
            email: { required: true, email: true },
            contact_number: { required: true },

            // If Individual, require first/middle/last; else require full_name
            name: {
                required: function() { return $('input[name="type"]:checked').val() === 'Individual'; }
            },
            middle_name: {
                required: function() { return $('input[name="type"]:checked').val() === 'Individual'; }
            },
            last_name: {
                required: function() { return $('input[name="type"]:checked').val() === 'Individual'; }
            },
            full_name: {
                required: function() { return $('input[name="type"]:checked').val() === 'Entity'; }
            },

            'contact_person_first_name[0]': {
                required: function() { return $('input[name="type"]:checked').val() === 'Entity'; }
            },
            'contact_person_last_name[0]': {
                required: function() { return $('input[name="type"]:checked').val() === 'Entity'; }
            },
            'contact_person_email[0]': {
                required: function() { return $('input[name="type"]:checked').val() === 'Entity'; },
                email: true
            },
            'contact_person_phone_number[0]': {
                required: function() { return $('input[name="type"]:checked').val() === 'Entity'; }
            }
        },
        errorPlacement: function(error, element) {
            if (element.attr('id') === 'section-a-1-phone_number') {
                error.insertAfter(element.parent());
            } else {
                error.appendTo(element.parent());
            }
        },
        submitHandler: function (form) {
            $('#section-a-1-dial_code').val(mainIti.s.dialCode);
            // Ensure each contact person's dial code is copied to hidden field before submit
            $('.contact_person_phone_number').each(function(){
                const nameAttr = $(this).attr('name');
                const match = nameAttr ? nameAttr.match(/\[(\d+)\]/) : null;
                if (match && match[1] !== undefined) {
                    const idx = match[1];
                    const itiInst = window.intlTelInputGlobals && window.intlTelInputGlobals.getInstance(this);
                    if (itiInst) {
                        const dial = itiInst.getSelectedCountryData().dialCode;
                        $('#contact_person_phone_number_dial_code_'+idx).val(dial);
                    }
                }
            });

            const emails = [];
            let dup = false;
            $('.contact_person_email').each(function(){
                const v = ($(this).val()||'').trim().toLowerCase();
                if (!v) return;
                if (emails.includes(v)) { dup = true; return false; }
                emails.push(v);
            });
            if (dup) {
                alert('Contact person emails must be unique.');
                return false;
            }
            form.submit();
        }
    });

    $('input[name="type"]').on('change', function(){
        $('#introducerForm').valid();
    });
});
</script>
@endpush


