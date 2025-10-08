<script>
    function submitCommonForm(section, formId, action = 'save') {
        if ("{{ request()->route()->getName() }}" == 'cases.view') {
            return false;
        }
        
        const form = document.getElementById(formId);
        const formData = new FormData(form);
        const data = {};

        for (let [key, value] of formData.entries()) {

            const isArray = key.endsWith('[]') || key.endsWith('[');
            const cleanKey = key.replace(/\[\]?$|\[$/, '');

            if (isArray) {
                if (!data[cleanKey]) {
                    data[cleanKey] = [];
                }
                data[cleanKey].push(value);
            } else {
                if (data[cleanKey] !== undefined) {

                    if (!Array.isArray(data[cleanKey])) {
                        data[cleanKey] = [data[cleanKey]];
                    }
                    data[cleanKey].push(value);
                } else {
                    data[cleanKey] = value;
                }
            }
        }

        return new Promise((resolve, reject) => {
            $.ajax({
                url: "{{ route('case.submission') }}",
                method: 'POST',
                data: {
                    policy: currentCaseId,
                    section: section,
                    data: data,
                    save: action
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    showSavingStatus('saving');
                },
                success: function(response) {
                    showSavingStatus('saved', response.timestamp || (new Date().toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' })));                    
                    resolve(response);

                    if ('type' in response && response.type == 'save' && 'next_section_url' in response && response.next_section_url != '' && response.next_section_url != null) {
                        window.location.href = response.next_section_url;
                    } else {
                        window.location.reload();
                    }
                    
                },
                error: function(xhr) {
                    showSavingStatus('error');
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        displayFormErrors(xhr.responseJSON.errors);
                    }
                    reject(xhr);
                }
            });
        });
    }

    function editCommonRecord(type, recordId) {
        let url, dataField;
            
        if (type === 'PolicyHolder') {
            url = "{{ route('case.getPolicyHolder') }}";
            dataField = 'policy_holder_id';
        } else if (type === 'PolicyController') {
            url = "{{ route('case.getPolicyController') }}";
            dataField = 'policy_controller_id';
        } else if (type === 'PolicyInsuredLifeInformation') {
            url = "{{ route('case.getInsuredLife') }}";
            dataField = 'insured_life_id';
        } else if (type === 'PolicyBeneficiary') {
            url = "{{ route('case.getBeneficiary') }}";
            dataField = 'beneficiary_id';
        }
        
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                policy_id: currentCaseId,
                [dataField]: recordId
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.data) {
                    populateFormFromData(response.data, type);
                }
            },
            error: function(xhr) {
                console.error('Error fetching record:', xhr);
            }
        });
    }

    function deleteCommonRecord(type, recordId, recordName) {
        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to delete "${recordName}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                let url, dataField, formId, loadFunction;
                
                if (type === 'PolicyHolder') {
                    url = "{{ route('case.deletePolicyHolder') }}";
                    dataField = 'policy_holder_id';
                    formId = 'form-section-b-1';
                    loadFunction = loadPolicyholdersSidebar;
                } else if (type === 'PolicyController') {
                    url = "{{ route('case.deletePolicyController') }}";
                    dataField = 'policy_controller_id';
                    formId = 'form-section-b-2';
                } else if (type === 'PolicyInsuredLifeInformation') {
                    url = "{{ route('case.deleteInsuredLife') }}";
                    dataField = 'insured_life_id';
                    formId = 'form-section-c-1';
                    loadFunction = loadInsuredLivesSidebar;
                } else if (type === 'PolicyBeneficiary') {
                    url = "{{ route('case.deleteBeneficiary') }}";
                    dataField = 'beneficiary_id';
                    formId = 'form-section-d-1';
                    loadFunction = loadBeneficiariesSidebar;
                }
                
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        policy_id: currentCaseId,
                        [dataField]: recordId
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire('Deleted!', 'Record has been deleted.', 'success');
                            loadFunction();
                            resetForm(formId);
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Failed to delete record.', 'error');
                    }
                });
            }
        });
    }

    function loadPolicyholdersSidebar() {
        $.ajax({
            url: "{{ route('case.getPolicyHoldersSidebar') }}",
            method: 'POST',
            data: {
                policy_id: currentCaseId
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('.policyholders-submenu').html(response.html);
            }
        });
    }

    function populateFormFromData(data, type) {
        data = data || {};

        if (type === 'PolicyHolder') {
            $(`input[name="type"][value="${ucwords(data.entity_type || '')}"]`).prop('checked', !!data.entity_type);
            toggleEntityTypeFields();

            $('#name').val(data.full_name || '');
            $('input[name="first_name"]').val(data.first_name || '');
            $('input[name="middle_name"]').val(data.middle_name || '');
            $('input[name="last_name"]').val(data.last_name || '');
            $('#place_of_birth').val(data.place_of_birth || '');
            $('#dob').val(data.date_of_birth || '');
            $('#country').val(data.country || '').trigger('change');
            $('#city').val(data.city || '');
            $('#zipcode').val(data.zipcode || '');
            $('#address_line_1').val(data.address_line_1 || '');

            if (data.personal_status) {
                if (['single', 'married', 'divorced', 'separated'].includes(data.personal_status)) {
                    $(`input[name="marital_status"][value="${data.personal_status}"]`).prop('checked', true);
                } else {
                    $(`input[name="entity_status"][value="${data.personal_status}"]`).prop('checked', true);
                }
            } else {
                $('input[name="marital_status"], input[name="entity_status"]').prop('checked', false);
            }

            $('input[name="entity_status_other"]').val(data.personal_status_other || '').toggleClass('d-none', !data.personal_status_other);
            $('select[name="national_country_of_registration"]').val(data.national_country_of_registration || '');
            $(`input[name="gender"][value="${data.gender || ''}"]`).prop('checked', !!data.gender);
            $('select[name="country_of_legal_residence"]').val(data.country_of_legal_residence || '').trigger('change');
            
            if (data.passport_number) populatePassportData(data.passport_number, data.country_of_issuance);
            else populatePassportData('', '');

            if (data.tin) populateTinData(data.tin); else populateTinData('');

            $('input[name="lei"]').val(data.lei || '');
            $('input[name="phone_number"]').val(data.phone_number || '');
            $('input[name="dial_code"]').val(data.dial_code || '');
            $('input[name="email"]').val(data.email || '');
            if (data.all_countries) populateTaxResidenceData(data.all_countries);
            else populateTaxResidenceData([]);

            $('#b-1-edit-id').val(data.id || '');
        }

        else if (type === 'PolicyController') {
            $('input[name="first_name"]').val(data.first_name || '');
            $('input[name="middle_name"]').val(data.middle_name || '');
            $('input[name="last_name"]').val(data.last_name || '');
            $('#place_of_birth').val(data.place_of_birth || '');
            $('#dob-b-2').val(data.date_of_birth || '');
            $('#country').val(data.country || '').trigger('change');
            $('#city').val(data.city || '');
            $('#zipcode').val(data.zipcode || '');
            $('#address_line_1').val(data.address_line_1 || '');
            $(`input[name="status"][value="${data.personal_status || ''}"]`).prop('checked', !!data.personal_status);
            $(`input[name="smoker_status"][value="${data.smoker_status || ''}"]`).prop('checked', !!data.smoker_status);
            $('input[name="national_country_of_registration"]').val(data.national_country_of_registration || '');
            $(`input[name="gender"][value="${data.gender || ''}"]`).prop('checked', !!data.gender);
            $('select[name="country_of_legal_residence"]').val(data.country_of_legal_residence || '').trigger('change');
            if (data.passport_number) populatePassportDataB2(data.passport_number, data.country_of_issuance);
            else populatePassportDataB2('', '');
            $('input[name="relationship_to_policyholder"]').val(data.notes || '');
            $('input[name="email"]').val(data.email || '');
            if (data.all_countries) populateTaxResidenceDataB2(data.all_countries);
            else populateTaxResidenceDataB2([]);
        }

        else if (type === 'PolicyInsuredLifeInformation') {
            $(`input[name="c1_type"][value="${ucwords(data.entity_type || '')}"]`).prop('checked', !!data.entity_type);
            $('#c1_controlling_person_name').val(data.full_name || '');
            $('input[name="first_name"]').val(data.first_name || '');
            $('input[name="middle_name"]').val(data.middle_name || '');
            $('input[name="last_name"]').val(data.last_name || '');
            $('#c1_place_of_birth').val(data.place_of_birth || '');
            $('#c1_date_of_birth').val(data.date_of_birth || '');
            $('#c1address').val(data.address_line_1 || '');
            $('#c1country').val(data.country || '').trigger('change');
            $('#c1city').val(data.city || '');
            $('#c1zip').val(data.zipcode || '');
            $(`input[name="status"][value="${data.personal_status || ''}"]`).prop('checked', !!data.personal_status);
            $(`input[name="smoker_status"][value="${data.smoker_status || ''}"]`).prop('checked', !!data.smoker_status);
            $('#c1nationality').val(data.national_country_of_registration || '');
            $(`input[name="gender"][value="${data.gender || ''}"]`).prop('checked', !!data.gender);
            $('#c1country_of_legal_residence').val(data.country_of_legal_residence || '').trigger('change');
            if (data.passport_number) populatePassportDataC1(data.passport_number, data.country_of_issuance);
            else populatePassportDataC1('', '');
            $('#c1relationship_to_policyholder').val(data.relationship_to_policyholder || '');
            $('#c1email').val(data.email || '');
            if (data.all_countries) populateTaxResidenceDataC1(data.all_countries);
            else populateTaxResidenceDataC1([]);

            $('#c-1-edit-id').val(data.id || '');
            toggleEntityTypeFieldsC1();
        }

        else if (type === 'PolicyBeneficiary') {
            $(`input[name="d1_type"][value="${ucwords(data.entity_type || '')}"]`).prop('checked', !!data.entity_type);
            $('input[name="full_name"]').val(data.full_name || '');
            $('input[name="first_name"]').val(data.first_name || '');
            $('input[name="middle_name"]').val(data.middle_name || '');
            $('input[name="last_name"]').val(data.last_name || '');
            $('#d-1-place-of-birth').val(data.place_of_birth || '');
            $('#d-1-dob').val(data.date_of_birth || '');
            $('#d-1-address').val(data.address_line_1 || '');
            $('#d-1-country').val(data.country || '').trigger('change');
            $('#d-1-city').val(data.city || '');
            $('#d-1-zip').val(data.zipcode || '');
            $(`input[name="d-1-status"][value="${data.personal_status || ''}"]`).prop('checked', !!data.personal_status);
            $(`input[name="d-1-smoker"][value="${data.smoker_status || ''}"]`).prop('checked', !!data.smoker_status);
            $('#d-1-nationality').val(data.nationality || '');
            $(`input[name="d-1-gender"][value="${data.gender || ''}"]`).prop('checked', !!data.gender);
            $('#d-1-legal-residence').val(data.country_of_legal_residence || '').trigger('change');
            if (data.passport_number) populatePassportDataD1(data.passport_number, data.country_of_issuance);
            else populatePassportDataD1('', '');
            $('#d-1-relationship').val(data.relationship_to_policyholder || '');
            $('#d-1-email').val(data.email || '');
            $('#d-1-dial_code').val(data.dial_code || '');
            $('#d-1-phone_number').val(data.phone_number || '');
            $('#d-1-allocation').val(data.beneficiary_death_benefit_allocation || '');
            $(`input[name="d-1-designation"][value="${data.designation_of_beneficiary || ''}"]`).prop('checked', !!data.designation_of_beneficiary);
            if (data.countries_of_tax_residence) populateTaxResidenceDataD1(data.countries_of_tax_residence);
            else populateTaxResidenceDataD1([]);

            $('#d-1-edit-id').val(data.id || '');
            toggleEntityTypeFieldsD1();
        }
    }

    function ucwords(str) {
    return str.replace(/\b\w/g, function(char) {
        return char.toUpperCase();
    });
    }

    function populatePassportData(passportNumbers, countries) {
        const passportContainer = $('.section-b-1-passport-row').parent();
        passportContainer.find('.section-b-1-passport-row').not(':first').remove();
        
        const passports = Array.isArray(passportNumbers) ? passportNumbers : JSON.parse(passportNumbers || '[]');
        const countriesData = Array.isArray(countries) ? countries : JSON.parse(countries || '[]');
        
        passports.forEach((passport, index) => {
            if (index > 0) {
                const newRow = $('.section-b-1-passport-row:first').clone();
                passportContainer.append(newRow);
            }
            const row = $('.section-b-1-passport-row').eq(index);
            row.find('input[name="passport_number[]"]').val(passport);
            row.find('select[name="country_of_issuance[]"]').val(countriesData[index] || '');
        });
    }

    function populateTinData(tins) {
        const tinContainer = $('.section-b-1-tin-row').parent();
        tinContainer.find('.section-b-1-tin-row').not(':first').remove();
        
        const tinData = Array.isArray(tins) ? tins : JSON.parse(tins || '[]');
        
        tinData.forEach((tin, index) => {
            if (index > 0) {
                const newRow = $('.section-b-1-tin-row:first').clone();
                tinContainer.append(newRow);
            }
            const row = $('.section-b-1-tin-row').eq(index);
            row.find('input[name="tin[]"]').val(tin);
        });
    }

    function populateTaxResidenceData(countries) {
        const taxContainer = $('.section-b-1-country-tax-residence-row').parent();
        taxContainer.find('.section-b-1-country-tax-residence-row').not(':first').remove();
        
        countries.forEach((country, index) => {
            if (index > 0) {
                const newRow = $('.section-b-1-country-tax-residence-row:first').clone();
                taxContainer.append(newRow);
            }
            const row = $('.section-b-1-country-tax-residence-row').eq(index);
            row.find('select[name="all_countries[]"]').val(country);
        });
    }

    function populatePassportDataB2(passportNumbers, countries) {
        const passportContainer = $('.section-b-2-passport-row').parent();
        passportContainer.find('.section-b-2-passport-row').not(':first').remove();
        
        const passports = Array.isArray(passportNumbers) ? passportNumbers : JSON.parse(passportNumbers || '[]');
        const countriesData = Array.isArray(countries) ? countries : JSON.parse(countries || '[]');
        
        passports.forEach((passport, index) => {
            if (index > 0) {
                const newRow = $('.section-b-2-passport-row:first').clone();
                passportContainer.append(newRow);
            }
            const row = $('.section-b-2-passport-row').eq(index);
            row.find('input[name="passport_number[]"]').val(passport);
            row.find('select[name="country_of_issuance[]"]').val(countriesData[index] || '');
        });
    }

    function populateTaxResidenceDataB2(countries) {
        const taxContainer = $('.section-b-2-country-tax-residence-row').parent();
        taxContainer.find('.section-b-2-country-tax-residence-row').not(':first').remove();
        
        countries.forEach((country, index) => {
            if (index > 0) {
                const newRow = $('.section-b-2-country-tax-residence-row:first').clone();
                taxContainer.append(newRow);
            }
            const row = $('.section-b-2-country-tax-residence-row').eq(index);
            row.find('select[name="all_countries[]"]').val(country);
        });
    }

    function populatePassportDataC1(passportNumbers, countries) {
        const passportContainer = $('.section-c-1-passport-row').parent();
        passportContainer.find('.section-c-1-passport-row').not(':first').remove();
        
        const passports = Array.isArray(passportNumbers) ? passportNumbers : JSON.parse(passportNumbers || '[]');
        const countriesData = Array.isArray(countries) ? countries : JSON.parse(countries || '[]');
        
        passports.forEach((passport, index) => {
            if (index > 0) {
                const newRow = $('.section-c-1-passport-row:first').clone();
                passportContainer.append(newRow);
            }
            const row = $('.section-c-1-passport-row').eq(index);
            row.find('input[name="passport_number[]"]').val(passport);
            row.find('select[name="country_of_issuance[]"]').val(countriesData[index] || '');
        });
    }

    function populateTaxResidenceDataC1(countries) {
        const taxContainer = $('.section-c-1-country-tax-residence-row').parent();
        taxContainer.find('.section-c-1-country-tax-residence-row').not(':first').remove();
        
        countries.forEach((country, index) => {
            if (index > 0) {
                const newRow = $('.section-c-1-country-tax-residence-row:first').clone();
                taxContainer.append(newRow);
            }
            const row = $('.section-c-1-country-tax-residence-row').eq(index);
            row.find('select[name="all_countries[]"]').val(country);
        });
    }

    function populatePassportDataD1(passportNumbers, countries) {
        const passportContainer = $('.section-d-1-passport-row').parent();
        passportContainer.find('.section-d-1-passport-row').not(':first').remove();
        
        const passports = Array.isArray(passportNumbers) ? passportNumbers : JSON.parse(passportNumbers || '[]');
        const countriesData = Array.isArray(countries) ? countries : JSON.parse(countries || '[]');
        
        passports.forEach((passport, index) => {
            if (index > 0) {
                const newRow = $('.section-d-1-passport-row:first').clone();
                passportContainer.append(newRow);
            }
            const row = $('.section-d-1-passport-row').eq(index);
            row.find('input[name="passport_number[]"]').val(passport);
            row.find('select[name="country_of_issuance[]"]').val(countriesData[index] || '');
        });
    }

    function populateTaxResidenceDataD1(countries) {

        countries = Array.isArray(countries) ? countries : JSON.parse(countries || '[]');

        const taxContainer = $('.section-d-1-country-tax-residence-row').parent();
        taxContainer.find('.section-d-1-country-tax-residence-row').not(':first').remove();        
        
        countries.forEach((country, index) => {
            if (index > 0) {
                const newRow = $('.section-d-1-country-tax-residence-row:first').clone();
                taxContainer.append(newRow);
            }
            const row = $('.section-d-1-country-tax-residence-row').eq(index);
            row.find('select[name="all_countries[]"]').val(country);
        });
    }

    function toggleEntityTypeFieldsC1() {
        const selectedType = $('input[name="c1_type"]:checked').val();
        
        if (selectedType === 'Individual') {
            $('.section-c-1-individual-fields').removeClass('d-none');
            $('.section-c-1-entity-field').addClass('d-none');
            $('.c1-individual-extra').removeClass('d-none');
            $('.c1-place-label').text('Place Of Birth');
            $('.c1-date-label').text('Date Of Birth');
            $('.c1-address-label').text('Residential Address');
        } else {
            $('.section-c-1-individual-fields').addClass('d-none');
            $('.section-c-1-entity-field').removeClass('d-none');
            $('.c1-individual-extra').addClass('d-none');
            $('.c1-place-label').text('Establishment');
            $('.c1-date-label').text('Establishment');
            $('.c1-address-label').text('Registered Address');
        }
    }

    function toggleEntityTypeFieldsD1() {
        const selectedType = $('input[name="d1_type"]:checked').val();

        if (selectedType === 'Individual') {
            $('.section-d-1-individual-fields').removeClass('d-none');
            $('.section-d-1-entity-field').addClass('d-none');
            $('.d1-individual-extra').removeClass('d-none');
            $('.d1-place-label').text('Place Of Birth');
            $('.d1-date-label').text('Date Of Birth');
            $('.d1-address-label').text('Residential Address');
        } else {
            $('.section-d-1-individual-fields').addClass('d-none');
            $('.section-d-1-entity-field').removeClass('d-none');
            $('.d1-individual-extra').addClass('d-none');
            $('.d1-place-label').text('Establishment');
            $('.d1-date-label').text('Establishment');
            $('.d1-address-label').text('Registered Address');
        }
    }

    function resetForm(formId) {
        document.getElementById(formId).reset();
        $('#b-1-edit-id').val('');
        
        if (formId === 'form-section-b-1') {
            toggleEntityTypeFields();
            $('.section-b-1-passport-row').not(':first').remove();
            $('.section-b-1-tin-row').not(':first').remove();
            $('.section-b-1-country-tax-residence-row').not(':first').remove();
        } else if (formId === 'form-section-b-2') {
            $('.section-b-2-passport-row').not(':first').remove();
            $('.section-b-2-country-tax-residence-row').not(':first').remove();
        } else if (formId === 'form-section-c-1') {
            toggleEntityTypeFieldsC1();
            $('.section-c-1-passport-row').not(':first').remove();
            $('.section-c-1-country-tax-residence-row').not(':first').remove();
        } else if (formId === 'form-section-d-1') {
            toggleEntityTypeFieldsD1();
            $('.section-d-1-passport-row').not(':first').remove();
            $('.section-d-1-country-tax-residence-row').not(':first').remove();
        }
    }

    function toggleEntityTypeFields() {
        const selectedType = $('input[name="type"]:checked').val();
        
        if (selectedType === 'Individual') {
            $('.section-b-1-individual-fields').removeClass('d-none');
            $('.section-b-1-entity-field').addClass('d-none');
            $('.section-b-1-individual-status').removeClass('d-none');
            $('.section-b-1-entity-status').addClass('d-none');
            $('.section-b-1-place-label').text('Place Of Birth:');
            $('.section-b-1-date-label').text('Date Of Birth:');
        } else {
            $('.section-b-1-individual-fields').addClass('d-none');
            $('.section-b-1-entity-field').removeClass('d-none');
            $('.section-b-1-individual-status').addClass('d-none');
            $('.section-b-1-entity-status').removeClass('d-none');
            $('.section-b-1-place-label').text('Place Of Registration:');
            $('.section-b-1-date-label').text('Date Of Registration:');
        }
    }

    function displayFormErrors(errors) {
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        Object.keys(errors).forEach(field => {
            const element = $(`[name="${field}"]`);
            element.addClass('is-invalid');
            element.after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
        });
    }

    function editPolicyHolderFromSidebar(holderId) {
        editCommonRecord('PolicyHolder', holderId);
    }

    function deletePolicyHolderFromSidebar(holderId) {
        const holderName = $(`.policyholder-item[data-id="${holderId}"] .policyholder-name`).text();
        deleteCommonRecord('PolicyHolder', holderId, holderName);
    }

    function editPolicyControllerFromSidebar(controllerId) {
        editCommonRecord('PolicyController', controllerId);
    }

    function deletePolicyControllerFromSidebar(controllerId) {
        const controllerName = $(`.policycontroller-item[data-id="${controllerId}"] .policycontroller-name`).text();
        deleteCommonRecord('PolicyController', controllerId, controllerName);
    }

    function loadPolicyControllersSidebar() {
        $.ajax({
            url: "{{ route('case.getPolicyControllersSidebar') }}",
            method: 'POST',
            data: {
                policy_id: currentCaseId
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('.policycontrollers-submenu').html(response.html);
            }
        });
    }

    function editInsuredLifeFromSidebar(insuredLifeId) {
        editCommonRecord('PolicyInsuredLifeInformation', insuredLifeId);
    }

    function deleteInsuredLifeFromSidebar(insuredLifeId) {
        const insuredName = $(`.insured-life-item[data-id="${insuredLifeId}"] .insured-name`).text();
        deleteCommonRecord('PolicyInsuredLifeInformation', insuredLifeId, insuredName);
    }

    function editBeneficiaryFromSidebar(id) {
        editCommonRecord('PolicyBeneficiary', id);
    }

    function deleteBeneficiaryFromSidebar(id) {
        const benName = $(`.beneficiary-item[data-id="${id}"] .beneficiary-name`).text();
        deleteCommonRecord('PolicyBeneficiary', id, benName);
    }

    function loadInsuredLivesSidebar() {
        $.ajax({
            url: "{{ route('case.getInsuredLivesSidebar') }}",
            method: 'POST',
            data: {
                policy_id: currentCaseId
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('.insured-lives-submenu').html(response.html);
            }
        });
    }

    function loadBeneficiariesSidebar() {
        $.ajax({
            url: "{{ route('case.getBeneficiariesSidebar') }}",
            method: 'POST',
            data: {
                policy_id: currentCaseId
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('.beneficiaries-submenu').html(response.html);
            }
        });
    }

    $(document).ready(function() {
        loadPolicyholdersSidebar();
        loadInsuredLivesSidebar();
        loadBeneficiariesSidebar();


    /**
     * Handle form submission for section B-1
     **/

        $('#form-section-b-1').validate({
            rules: {
                first_name: {
                    required: function() {
                        return $('.section-b-1-type:checked').val() === 'Individual';
                    }
                },
                middle_name: {
                    required: function() {
                        return $('.section-b-1-type:checked').val() === 'Individual';
                    }
                },
                last_name: {
                    required: function() {
                        return $('.section-b-1-type:checked').val() === 'Individual';
                    }
                },
                name: {
                    required: function() {
                        return $('.section-b-1-type:checked').val() !== 'Individual';
                    }
                },
                country: {
                    required: true
                },
                city: {
                    required: true
                },
                zipcode: {
                    required: true
                },
                address_line_1: {
                    required: true
                },
                status: {
                    required: true
                },
                national_country_of_registration: {
                    required: true
                },
                gender: {
                    required: true
                },
                country_of_legal_residence: {
                    required: true
                },
                phone_number: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                }
            },
            errorPlacement: function(error, element) {
                error.appendTo(element.parent());
            },
            submitHandler: function(form, event) {
                event.preventDefault();

                const submitButton = $(document.activeElement);
                const action = submitButton.data('type') || 'save';
                
                submitCommonForm('section-b-1', 'form-section-b-1', action)
                    .then(function(response) {
                        if (response.type === 'save-and-add') {
                            resetForm('form-section-b-1');
                            loadPolicyholdersSidebar();
                        } else if (response.next_section) {
                            const nextSection = submitButton.data('next');
                            if (nextSection) {
                                $('.case-section').addClass('d-none');
                                $(`#${nextSection}`).removeClass('d-none');
                                $('.policy-dropdown-item').removeClass('active');
                                $(`.policy-dropdown-item[data-section="${nextSection}"]`).addClass('active');
                            }
                        }
                        loadPolicyholdersSidebar();
                    })
                    .catch(function(error) {
                        console.error('Form submission failed:', error);
                    });
            }
        });

    /**
     * Handle form submission for section B-1
     **/ 

    /**
     * Handle form submission for section B-2
     **/

        $('#form-section-b-2').validate({
            rules: {
                first_name: {
                    required: true
                },
                middle_name: {
                    required: true
                },
                last_name: {
                    required: true
                },
                place_of_birth: {
                    required: true
                },
                dob: {
                    required: true
                },
                address_line_1: {
                    required: true
                },
                country: {
                    required: true
                },
                city: {
                    required: true
                },
                zipcode: {
                    required: true
                },
                status: {
                    required: true
                },
                national_country_of_registration: {
                    required: true
                },
                gender: {
                    required: true
                },
                country_of_legal_residence: {
                    required: true
                },
                'all_countries[]': {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                relationship_to_policyholder: {
                    required: true
                }
            },
            errorPlacement: function(error, element) {
                error.appendTo(element.parent());
            },
            submitHandler: function(form, event) {
                event.preventDefault();

                const submitButton = $(document.activeElement);
                const action = submitButton.data('type') || 'save';
                
                submitCommonForm('section-b-2', 'form-section-b-2', action)
                    .then(function(response) {

                    })
                    .catch(function(error) {
                        console.error('Form submission failed:', error);
                    });
            }
        });        

    /**
     * Handle form submission for section B-2
     **/
        
    /**
     * Handle form submission for section C-1
     **/

        $('#form-section-c-1').validate({
            rules: {
                first_name: {
                    required: function() { return $('.section-c-1-type:checked').val() === 'Individual'; }
                },
                middle_name: {
                    required: function() { return $('.section-c-1-type:checked').val() === 'Individual'; }
                },
                last_name: {
                    required: function() { return $('.section-c-1-type:checked').val() === 'Individual'; }
                },
                controlling_person_name: {
                    required: function() { return $('.section-c-1-type:checked').val() !== 'Individual'; }
                },
                place_of_birth: { required: true },
                date_of_birth: { required: true },
                address: { required: true },
                country: { required: true },
                city: { required: true },
                zip: { required: true },
                country_of_legal_residence: { required: true },
                'all_countries[]': { required: true },
                email: { required: true, email: true },
                status: { required: function(){ return $('.section-c-1-type:checked').val() === 'Individual'; } },
                nationality: { required: function(){ return $('.section-c-1-type:checked').val() === 'Individual'; } },
                gender: { required: function(){ return $('.section-c-1-type:checked').val() === 'Individual'; } }
            },
            errorPlacement: function(error, element) {
                error.appendTo(element.parent());
            },
            submitHandler: function(form, event) {
                event.preventDefault();

                const submitButton = $(document.activeElement);
                const action = submitButton.data('type') || 'save';
                
                submitCommonForm('section-c-1', 'form-section-c-1', action)
                    .then(function(response) {
                        if (response.type === 'save-and-add') {
                            resetForm('form-section-c-1');
                            loadInsuredLivesSidebar();
                        } else if (response.next_section) {
                            const nextSection = submitButton.data('next');
                            if (nextSection) {
                                $('.case-section').addClass('d-none');
                                $(`#${nextSection}`).removeClass('d-none');
                                $('.policy-dropdown-item').removeClass('active');
                                $(`.policy-dropdown-item[data-section="${nextSection}"]`).addClass('active');
                            }
                        }
                        loadInsuredLivesSidebar();
                    })
                    .catch(function(error) {
                        console.error('Form submission failed:', error);
                    });
            }
        });        

    /**
     * Handle form submission for section C-1
     **/


    /**
     * Handle form submission for section D-1
     **/

        $('#form-section-d-1').validate({
            rules: {
                first_name: {
                    required: function() { return $('.section-d-1-type:checked').val() === 'Individual'; }
                },
                middle_name: {
                    required: function() { return $('.section-d-1-type:checked').val() === 'Individual'; }
                },
                last_name: {
                    required: function() { return $('.section-d-1-type:checked').val() === 'Individual'; }
                },
                full_name: {
                    required: function() { return $('.section-d-1-type:checked').val() !== 'Individual'; }
                },
                place_of_birth: { required: true },
                date_of_birth: { required: true },
                address: { required: true },
                country: { required: true },
                city: { required: true },
                zip: { required: true },
                country_of_legal_residence: { required: true },
                'all_countries[]': { required: true },
                email: { required: true, email: true },
                phone_number: { required: true },
                beneficiary_death_benefit_allocation: { required: true, number: true, min: 0, max: 100 },
                'd-1-status': { required: function(){ return $('.section-d-1-type:checked').val() === 'Individual'; } },
                nationality: { required: function(){ return $('.section-d-1-type:checked').val() === 'Individual'; } },
                'd-1-gender': { required: function(){ return $('.section-d-1-type:checked').val() === 'Individual'; } }
            },
            errorPlacement: function(error, element) {
                error.appendTo(element.parent());
            },
            submitHandler: function(form, event) {
                event.preventDefault();

                const submitButton = $(document.activeElement);
                const action = submitButton.data('type') || 'save';
                
                submitCommonForm('section-d-1', 'form-section-d-1', action)
                    .then(function(response) {
                        if (response.type === 'save-and-add') {
                            resetForm('form-section-d-1');
                            loadBeneficiariesSidebar();
                        } else if (response.next_section) {
                            const nextSection = submitButton.data('next');
                            if (nextSection) {
                                $('.case-section').addClass('d-none');
                                $(`#${nextSection}`).removeClass('d-none');
                                $('.policy-dropdown-item').removeClass('active');
                                $(`.policy-dropdown-item[data-section="${nextSection}"]`).addClass('active');
                            }
                        }
                        loadBeneficiariesSidebar();
                    })
                    .catch(function(error) {
                        console.error('Form submission failed:', error);
                    });
            }
        });        

    /**
     * Handle form submission for section D-1
     **/    


    });
</script>
