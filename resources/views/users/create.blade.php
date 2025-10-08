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
	#phone_number{
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
                <h2>Add New User</h2>
            </div>
            <div class="card-body">
                <form id="userForm" method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3 row">
                        <div class="col-12">
                            <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label> <br>
                            <input type="hidden" name="dial_code" id="dial_code">
                            <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                            @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="profile" class="form-label">Profile Image</label>
                        <input type="file" class="form-control @error('profile') is-invalid @enderror" id="profile" name="profile" accept="image/*">
                        @error('profile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="roles" class="form-label">Roles <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('roles') is-invalid @enderror" id="roles" name="roles[]" multiple required>
                            @foreach($roles as $id => $name)
                                <option value="{{ $id }}" {{ (collect(old('roles'))->contains($id)) ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('roles')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Create User</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/intel-tel.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
@endpush

@push('js')
<script src="{{ asset('assets/js/jquery-validate.min.js') }}"></script>
<script src="{{ asset('assets/js/intel-tel.js') }}"></script>
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script>
$(document).ready(function() {

    $('#roles').select2({
        placeholder: 'Select roles',
        theme: 'classic',
        width: '100%'
    });

     $('#status').select2({
        placeholder: 'Select status',
        theme: 'classic',
        width: '100%'
    });

    const input = document.querySelector('#phone_number');
    const errorMap = ["Phone number is invalid.", "Invalid country code", "Too short", "Too long"];
    const iti = window.intlTelInput(input, {
        initialCountry: "{{  Helper::$defaulDialCode  }}",
        separateDialCode:true,
        nationalMode:false,
        preferredCountries: ["us", "gb", "ca", "hk", "ch", "ae"],
        utilsScript: "{{ asset('assets/js/intel-tel-2.min.js') }}"
    });
    input.addEventListener("countrychange", function() {
        if (iti.isValidNumber()) {
            $('#dial_code').val(iti.s.dialCode);
        }
    });
    input.addEventListener('keyup', () => {
        if (iti.isValidNumber()) {
            $('#dial_code').val(iti.s.dialCode);
        }
    });

    $('#userForm').validate({
        rules: {
            name: { required: true },
            email: { required: true, email: true },
            phone_number: { required: true },
            status: { required: true },
            roles: { required: true }
        },
        errorPlacement: function(error, element) {
            if (element.attr('id') === 'phone_number') {
                error.insertAfter(element.parent());
            } else {
                error.appendTo(element.parent());
            }
        },
        submitHandler: function (form) {
            $('#dial_code').val(iti.s.dialCode);
            $('body').find('.LoaderSec').removeClass('d-none');
            form.submit();
        }
    });
});
</script>
@endpush
