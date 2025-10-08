<script>

    const submission = (section, form, submitButton) => {

        if ("{{ request()->route()->getName() }}" == 'cases.view') {
            return false;            
        }
                
        $.ajax({
            url: "{{ route('case.submission') }}",
            method: 'POST',
            data: {
                policy: currentCaseId,
                section: section,
                data: form,
                save: $(submitButton).data('type')
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                showSavingStatus('saving');
            },
            success: function(response) {
                showSavingStatus('saved', response.timestamp || (new Date().toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' })));

                if ('type' in response && response.type == 'save' && 'next_section_url' in response && response.next_section_url != '' && response.next_section_url != null) {
                    window.location.href = response.next_section_url;
                } else if ('type' in response && response.type == 'save' && 'next_section' in response && response.next_section != '' && response.next_section != null) {
                    var nextSection = $(submitButton).data('next');
                    var currentSection = section;

                    if (!nextSection && !currentSection) return;

                    $('.policy-dropdown-item[data-section="' + currentSection + '"]').removeClass('active');
                    $('.policy-dropdown-item[data-section="' + currentSection + '"]').parent().parent().css('display', 'none');

                    $('.policy-dropdown-item[data-section="' + nextSection + '"]').addClass('active');
                    $('.policy-dropdown-item[data-section="' + nextSection + '"]').parent().parent().css('display', 'block');

                    $(`#${currentSection}`).addClass('d-none');
                    $(`#${nextSection}`).removeClass('d-none');                        
                    
                } else if ('type' in response && response.type == 'save-and-add') {
                    if (response.next_section == 'section-g-1') {
                        $('#form-section-g-1')[0].reset();
                    } else if (response.next_section == 'section-g-2') {
                        $('#form-section-g-2')[0].reset();
                    }
                } else {
                    window.location.href = "{{ route('cases.index') }}";
                }
            },
            error: function(xhr) {
                showSavingStatus('error');
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorList = '';

                    $.each(errors, function(key, messages) {
                        messages.forEach(function(message) {
                            errorList += `<li>${message}</li>`;
                        });
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Errors',
                        html: `<ul style="text-align: left;">${errorList}</ul>`
                    });
                } else if (xhr.status === 403) {
                    Swal.fire({
                        icon: 'error',
                        title: 'You do not have authorization to submit this case'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Please try again.'
                    });
                }
            },
            complete: function (response) {
                $('#id-for-edit-a-1').val(null);
                if (section == 'section-c-1') {
                    resetForm();
                }

                if (section == 'section-d-1') {
                    $('#d-1-edit-id').val('');
                    $('#d-1-insured-life').val(null).trigger('change');
                    $('#form-section-d-1')[0].reset();
                }

                if (section == 'section-a-1') {
                    resetIntroForm();
                }

                if (section == 'section-g-1') {
                    refreshCommunicationAccordion()
                }

                if (section == 'section-g-2') {
                    refreshCaseFileNotesAccordion()
                }
            }
        });
    }

    /**
     * Handle form submission for section A-1
     **/
        $('#form-section-a-1').validate({
            rules : {
                introducer_id: {
                    required: true
                },
                section_a_1_name: {
                    required: function() {
                        return $('input[name="section_a_1_entity"]:checked').val() === 'Entity';
                    }
                },
                section_a_1_first_name: {
                    required: function() {
                        return $('input[name="section_a_1_entity"]:checked').val() === 'Individual';
                    }
                },
                section_a_1_middle_name: {
                    required: function() {
                        return $('input[name="section_a_1_entity"]:checked').val() === 'Individual';
                    }
                },
                section_a_1_last_name: {
                    required: function() {
                        return $('input[name="section_a_1_entity"]:checked').val() === 'Individual';
                    }
                },
                section_a_1_email: {
                    required: true
                },
                section_a_1_phone: {
                    required: true
                },
                'contact_person_first_name[0]': {
                    required: function() {
                        return $('input[name="section_a_1_entity"]:checked').val() === 'Entity';
                    }
                },
                'contact_person_middle_name[0]': {
                    required: function() {
                        return $('input[name="section_a_1_entity"]:checked').val() === 'Entity';
                    }
                },
                'contact_person_last_name[0]': {
                    required: function() {
                        return $('input[name="section_a_1_entity"]:checked').val() === 'Entity';
                    }
                },
                'contact_person_email[0]': {
                    required: function() {
                        return $('input[name="section_a_1_entity"]:checked').val() === 'Entity';
                    }
                },
                'contact_person_phone_number[0]': {
                    required: function() {
                        return $('input[name="section_a_1_entity"]:checked').val() === 'Entity';
                    }
                },
            },
            errorPlacement: function(error, element) {
                if (element.attr('id') === 'section-a-1-phone_number') {
                    error.insertAfter(element.parent());
                } else {
                    error.appendTo(element.parent());
                }
            },
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
                    const keys = name.match(/[^\[\]]+/g);

                    keys.reduce((acc, key, i) => {
                        if (i === keys.length - 1) {
                            acc[key] = value;
                        } else {
                            acc[key] = acc[key] || {};
                        }
                        return acc[key];
                    }, formData);
                });

                let actionType = event.originalEvent.submitter;
                
                submission('section-a-1', formData, actionType);
            }
        });

    /**
     * Handle form submission for section A-1
     **/

    /**
     * Handle form submission for section A-2
     **/

        (function initA2Validation(){
            function isAdvisorApplicable(){ return $('input[name="advisor_applicable"]:checked').val() === 'applicable'; }
            function isIdfApplicable(){ return $('input[name="idf_applicable"]:checked').val() === 'applicable'; }
            function isIdfMgrApplicable(){ return $('input[name="idf_manager_applicable"]:checked').val() === 'applicable'; }
            function isCustodianApplicable(){ return $('input[name="custodian_applicable"]:checked').val() === 'applicable'; }

            function addRulesForItem($item, group, applicableFn){
                const applyReq = applicableFn || function(){ return true; };
                const $type = $item.find('.entity-type-select');
                const $first = $item.find(`input[name^="${group}"][name$="[first_name]"]`);
                const $middle = $item.find(`input[name^="${group}"][name$="[middle_name]"]`);
                const $last = $item.find(`input[name^="${group}"][name$="[last_name]"]`);
                const $name = $item.find(`input[name^="${group}"][name$="[name]"]`);
                const $notes = $item.find(`textarea[name^="${group}"][name$="[notes]"]`);

                if ($type.length) {
                    $type.rules('add', { required: function(){ return applyReq(); } });
                }

                if ($first.length) {
                    $first.rules('add', { required: function(){ return applyReq() && ($type.val() === 'Individual'); } });
                }
                if ($middle.length) {
                    $middle.rules('add', { required: function(){ return applyReq() && ($type.val() === 'Individual'); } });
                }
                if ($last.length) {
                    $last.rules('add', { required: function(){ return applyReq() && ($type.val() === 'Individual'); } });
                }
                if ($name.length) {
                    $name.rules('add', { required: function(){ return applyReq() && ($type.val() !== 'Individual'); } });
                }
                if ($notes.length) {
                    $notes.rules('add', { required: function(){ return applyReq(); } });
                }
            }

            function applyRules(){
                $('.policyholder-item').each(function(){ addRulesForItem($(this), 'policy_holder'); });
                $('.insured-life-item').each(function(){ addRulesForItem($(this), 'insured_life'); });
                $('.beneficiary-item').each(function(){ addRulesForItem($(this), 'beneficiary'); });
                $('.advisor-item').each(function(){ addRulesForItem($(this), 'advisor', isAdvisorApplicable); });
                $('.idf-item').each(function(){ addRulesForItem($(this), 'idf', isIdfApplicable); });
                $('.idf-manager-item').each(function(){ addRulesForItem($(this), 'idf_manager', isIdfMgrApplicable); });
                $('.custodian-item').each(function(){ addRulesForItem($(this), 'custodian', isCustodianApplicable); });
            }

            const validator = $('#form-section-a-2').validate({
                ignore: [],
                errorPlacement: function(error, element) {
                    if (element.attr('id') === 'section-a-1-phone_number') {
                        error.insertAfter(element.parent());
                    } else {
                        error.appendTo(element.parent());
                    }
                },
                submitHandler: function(form, event) {
                    event.preventDefault();

                    let formData = {};
                    $(form).serializeArray().forEach(({ name, value }) => {
                        const keys = name.match(/[^\[\]]+/g);

                        keys.reduce((acc, key, i) => {
                            if (i === keys.length - 1) {
                                acc[key] = value;
                            } else {
                                acc[key] = acc[key] || {};
                            }
                            return acc[key];
                        }, formData);
                    });
                    
                    let actionType = event.originalEvent.submitter;
                    
                    submission('section-a-2', formData, actionType);
                }
            });

            applyRules();

            $(document).on('change', 'input[name="advisor_applicable"], input[name="idf_applicable"], input[name="idf_manager_applicable"], input[name="custodian_applicable"]', function(){
                applyRules();
            });

            $(document).on('click', '.add-policyholder', function(){
                setTimeout(function(){
                    addRulesForItem($('.policyholder-item').last(), 'policy_holder');
                }, 0);
            });
            $(document).on('click', '.add-insured-life', function(){
                setTimeout(function(){
                    addRulesForItem($('.insured-life-item').last(), 'insured_life');
                }, 0);
            });
            $(document).on('click', '.add-beneficiary', function(){
                setTimeout(function(){
                    addRulesForItem($('.beneficiary-item').last(), 'beneficiary');
                }, 0);
            });
            $(document).on('click', '.add-advisor', function(){
                setTimeout(function(){
                    addRulesForItem($('.advisor-item').last(), 'advisor', isAdvisorApplicable);
                }, 0);
            });
            $(document).on('click', '.add-idf', function(){
                setTimeout(function(){
                    addRulesForItem($('.idf-item').last(), 'idf', isIdfApplicable);
                }, 0);
            });
            $(document).on('click', '.add-idf-manager', function(){
                setTimeout(function(){
                    addRulesForItem($('.idf-manager-item').last(), 'idf_manager', isIdfMgrApplicable);
                }, 0);
            });
            $(document).on('click', '.add-custodian', function(){
                setTimeout(function(){
                    addRulesForItem($('.custodian-item').last(), 'custodian', isCustodianApplicable);
                }, 0);
            });
        })();        

    /**
     * Handle form submission for section A-2
     **/

    $(document).ready(function() {
        $(document).on('change', '.entity-type-select', function() {
            const entityType = $(this).val();
            const container = $(this).closest('.row').find('.col-md-12');
            const individualFields = container.find('.individual-name-fields');
            const entityField = container.find('.entity-name-field');
            
            if (entityType.toLowerCase() === 'individual') {
                individualFields.show();
                entityField.hide();
            } else {
                individualFields.hide();
                entityField.show();
            }
        });

        $(document).on('change', 'input[name="advisor_applicable"]', function() {
            const isApplicable = $(this).val() === 'applicable';
            const container = $('.advisor-container');
            if (isApplicable) {
                container.css({'opacity': '1', 'pointer-events': 'auto'});
            } else {
                container.css({'opacity': '0.5', 'pointer-events': 'none'});
            }
        });

        $(document).on('change', 'input[name="idf_applicable"]', function() {
            const isApplicable = $(this).val() === 'applicable';
            const container = $('.idf-container');
            if (isApplicable) {
                container.css({'opacity': '1', 'pointer-events': 'auto'});
            } else {
                container.css({'opacity': '0.5', 'pointer-events': 'none'});
            }
        });

        $(document).on('change', 'input[name="idf_manager_applicable"]', function() {
            const isApplicable = $(this).val() === 'applicable';
            const container = $('.idf-manager-container');
            if (isApplicable) {
                container.css({'opacity': '1', 'pointer-events': 'auto'});
            } else {
                container.css({'opacity': '0.5', 'pointer-events': 'none'});
            }
        });

        $(document).on('change', 'input[name="custodian_applicable"]', function() {
            const isApplicable = $(this).val() === 'applicable';
            const container = $('.custodian-container');
            if (isApplicable) {
                container.css({'opacity': '1', 'pointer-events': 'auto'});
            } else {
                container.css({'opacity': '0.5', 'pointer-events': 'none'});
            }
        });

        let policyholderCount = {{ \App\Models\PolicyHolder::where('policy_id', $policyId)->count() }} + 1;
        let insuredLifeCount = {{ \App\Models\PolicyInsuredLifeInformation::where('policy_id', $policyId)->count() }} + 1;
        let beneficiaryCount = {{ \App\Models\PolicyBeneficiary::where('policy_id', $policyId)->count() }} + 1;
        let advisorCount = {{ \App\Models\InvestmentAdvisor::where('policy_id', $policyId)->count() }} + 1;
        let idfCount = {{ \App\Models\InvestmentDedicatedFund::where('policy_id', $policyId)->where('user_type', 'name')->count() }} + 1;
        let idfManagerCount = {{ \App\Models\InvestmentDedicatedFund::where('policy_id', $policyId)->where('user_type', 'manager')->count() }} + 1;
        let custodianCount = {{ \App\Models\Custodian::where('policy_id', $policyId)->count() }} + 1;

        $(document).on('click', '.add-policyholder', function() {
            policyholderCount++;
            const newItem = createPolicyholderItem(policyholderCount);

            $('.policyholders-container').append(newItem);

            $('.policyholders-container').find('.entity-type-select').last().select2({
                theme: 'classic',
                width: '100%',
                placeholder: 'Select entity type'
            });

            updateRemoveButtons('.policyholder-item');
        });

        $(document).on('change', '.section-b-1-type', function() {
            const isIndividual = $('.section-b-1-type:checked').val() === 'Individual';
            if (isIndividual) {
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
        }).trigger('change');



        $(document).on('click', '.section-b-1-passport-add', function() {
            const row = `
                <div class="row align-items-end section-b-1-passport-row mt-2">
                    <div class="col-sm-5 mb-2">
                        <input type="text" class="form-control section-b-1-passport" name="passport_number[]" placeholder="Passport Number" value="">
                    </div>
                    <div class="col-sm-5 mb-2">
                        <select class="form-control section-b-1-passport-issue-country" name="country_of_issuance[]">
                            <option value=""></option>
                            ${countryHtml}
                        </select>
                    </div>
                    <div class="col-sm-2 mb-2">
                        <button type="button" class="btn btn-success section-b-1-passport-add">+</button>
                        <button type="button" class="btn btn-danger section-b-1-passport-remove">-</button>
                    </div>
                </div>`;
            $(this).closest('.section-b-1-passport-row').parent().append(row);
        });

        $(document).on('click', '.section-b-1-passport-remove', function() {
            const container = $(this).closest('.section-b-1-passport-row').parent();
            if (container.find('.section-b-1-passport-row').length > 1) {
                $(this).closest('.section-b-1-passport-row').remove();
            }
        });

        $(document).on('click', '.section-b-1-tin-add', function() {
            const row = `
                <div class="row align-items-end section-b-1-tin-row mt-2">
                    <div class="col-sm-9 mb-2">
                        <input type="text" class="form-control section-b-1-tin" name="tin[]" placeholder="TIN" value="">
                    </div>
                    <div class="col-sm-3 mb-2">
                        <button type="button" class="btn btn-success section-b-1-tin-add">+</button>
                        <button type="button" class="btn btn-danger section-b-1-tin-remove">-</button>
                    </div>
                </div>`;
            $(this).closest('.section-b-1-tin-row').parent().append(row);
        });

        $(document).on('click', '.section-b-1-tin-remove', function() {
            const container = $(this).closest('.section-b-1-tin-row').parent();
            if (container.find('.section-b-1-tin-row').length > 1) {
                $(this).closest('.section-b-1-tin-row').remove();
            }
        });

        $(document).on('click', '.remove-policyholder', function() {
            if ($('.policyholder-item').length === 1) {
                Swal.fire({
                    icon: 'info',
                    text: 'There must be at least one policyholder in the policy.',
                    confirmButtonText: 'Ok'
                });
                return;
            }

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'Are you sure you want to remove policyholder? as it is irreversible process',
                allowOutsideClick: false, 
                allowEscapeKey: false,
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'No, keep it',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).closest('.policyholder-item').remove();
                    updateRemoveButtons('.policyholder-item');
                }
            });
        });

        $(document).on('click', '.add-insured-life', function() {
            insuredLifeCount++;
            const newItem = createInsuredLifeItem(insuredLifeCount);
            $('.insured-lives-container').append(newItem);

            $('.insured-lives-container').find('.entity-type-select').last().select2({
                theme: 'classic',
                width: '100%',
                placeholder: 'Select entity type'
            });

            updateRemoveButtons('.insured-life-item');
        });

        $(document).on('click', '.remove-insured-life', function() {
            if ($('.insured-life-item').length === 1) {
                Swal.fire({
                    icon: 'info',
                    title: 'Cannot Remove Last Policyholder',
                    text: 'There must be at least one insured life in the policy.',
                    confirmButtonText: 'Ok'
                });
                return;
            }

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'Are you sure you want to remove insured life? as it is irreversible process',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'No, keep it',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).closest('.insured-life-item').remove();
                    updateRemoveButtons('.insured-life-item');
                }
            });
        });

        $(document).on('click', '.add-beneficiary', function() {
            beneficiaryCount++;
            const newItem = createBeneficiaryItem(beneficiaryCount);
            $('.beneficiaries-container').append(newItem);

            $('.beneficiaries-container').find('.entity-type-select').last().select2({
                theme: 'classic',
                width: '100%',
                placeholder: 'Select entity type'
            });

            updateRemoveButtons('.beneficiary-item');
        });

        $(document).on('click', '.remove-beneficiary', function() {
            if ($('.beneficiary-item').length === 1) {
                Swal.fire({
                    icon: 'info',
                    text: 'There must be at least one beneficiary in the policy.',
                    confirmButtonText: 'Ok'
                });
                return;
            }

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'Are you sure you want to remove beneficiary? as it is irreversible process',
                allowOutsideClick: false, 
                allowEscapeKey: false,
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'No, keep it',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).closest('.beneficiary-item').remove();
                    updateRemoveButtons('.beneficiary-item');
                }
            });
        });

        $(document).on('click', '.add-advisor', function() {
            advisorCount++;
            const newItem = createAdvisorItem(advisorCount);
            $('.advisors-container').append(newItem);

            $('.advisors-container').find('.entity-type-select').last().select2({
                theme: 'classic',
                width: '100%',
                placeholder: 'Select entity type'
            });
            
            updateRemoveButtons('.advisor-item');
        });

        $(document).on('click', '.remove-advisor', function() {
            if ($('.advisor-item').length === 1) {
                Swal.fire({
                    icon: 'info',
                    text: 'There must be at least one advisor in the policy.',
                    confirmButtonText: 'Ok'
                });
                return;
            }

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'Are you sure you want to remove advisor? as it is irreversible process',
                allowOutsideClick: false, 
                allowEscapeKey: false,
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'No, keep it',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).closest('.advisor-item').remove();
                    updateRemoveButtons('.advisor-item');
                }
            });
        });

        $(document).on('click', '.add-idf', function() {
            idfCount++;
            const newItem = createIdfItem(idfCount);
            $('.idfs-container').append(newItem);

            $('.idfs-container').find('.entity-type-select').last().select2({
                theme: 'classic',
                width: '100%',
                placeholder: 'Select entity type'
            });

            updateRemoveButtons('.idf-item');
        });

        $(document).on('click', '.remove-idf', function() {
            if ($('.idf-item').length === 1) {
                Swal.fire({
                    icon: 'info',
                    text: 'There must be at least one IDF name in the policy.',
                    confirmButtonText: 'Ok'
                });
                return;
            }

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'Are you sure you want to remove IDF names? as it is irreversible process',
                allowOutsideClick: false, 
                allowEscapeKey: false,
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'No, keep it',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).closest('.idf-item').remove();
                    updateRemoveButtons('.idf-item');
                }
            });
        });

        $(document).on('click', '.add-idf-manager', function() {
            idfManagerCount++;
            const newItem = createIdfManagerItem(idfManagerCount);
            $('.idf-managers-container').append(newItem);

            $('.idf-managers-container').find('.entity-type-select').last().select2({
                theme: 'classic',
                width: '100%',
                placeholder: 'Select entity type'
            });

            updateRemoveButtons('.idf-manager-item');
        });

        $(document).on('click', '.remove-idf-manager', function() {
            if ($('.idf-manager-item').length === 1) {
                Swal.fire({
                    icon: 'info',
                    text: 'There must be at least one IDF manager in the policy.',
                    confirmButtonText: 'Ok'
                });
                return;
            }

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'Are you sure you want to remove IDF manager? as it is irreversible process',
                allowOutsideClick: false, 
                allowEscapeKey: false,
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'No, keep it',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).closest('.idf-manager-item').remove();
                    updateRemoveButtons('.idf-manager-item');
                }
            });
        });

        $(document).on('click', '.add-custodian', function() {
            custodianCount++;
            const newItem = createCustodianItem(custodianCount);
            $('.custodians-container').append(newItem);

            $('.custodians-container').find('.entity-type-select').last().select2({
                theme: 'classic',
                width: '100%',
                placeholder: 'Select entity type'
            });

            updateRemoveButtons('.custodian-item');
        });

        $(document).on('click', '.remove-custodian', function() {
            if ($('.custodian-item').length === 1) {
                Swal.fire({
                    icon: 'info',
                    text: 'There must be at least one custodian in the policy.',
                    confirmButtonText: 'Ok'
                });
                return;
            }

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'Are you sure you want to remove custodian? as it is irreversible process',
                allowOutsideClick: false, 
                allowEscapeKey: false,
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'No, keep it',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).closest('.custodian-item').remove();
                    updateRemoveButtons('.custodian-item');
                }
            });
        });

        function updateRemoveButtons(selector) {
            const items = $(selector);
            items.find('.remove-policyholder, .remove-insured-life, .remove-beneficiary, .remove-advisor, .remove-idf, .remove-idf-manager, .remove-custodian').hide();
            if (items.length > 1) {
                items.find('.remove-policyholder, .remove-insured-life, .remove-beneficiary, .remove-advisor, .remove-idf, .remove-idf-manager, .remove-custodian').show();
            }
        }

        function createPolicyholderItem(count) {
            return `
                <div class="policyholder-item mb-3 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold">Policyholder ${count > 2 ? (count - 1) : count}</span>
                        <button type="button" class="btn btn-sm btn-danger remove-policyholder">Remove</button>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-3">
                            <label class="form-label">Entity Type</label>
                            <select class="form-control entity-type-select" name="policy_holder[${count-1}][type]">
                                <option value="">Select Type</option>
                                <option value="individual">Individual</option>
                                <option value="corporate">Corporate</option>
                                <option value="trust">Trust</option>
                                <option value="foundation">Foundation</option>
                            </select>
                        </div>
                        <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-9">
                            <div class="individual-name-fields" style="display: none;" >
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="policy_holder[${count-1}][first_name]">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Middle Name(s)</label>
                                        <input type="text" class="form-control" name="policy_holder[${count-1}][middle_name]">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="policy_holder[${count-1}][last_name]">
                                    </div>
                                </div>
                            </div>
                            <div class="entity-name-field"  >
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="policy_holder[${count-1}][name]">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="2" name="policy_holder[${count-1}][notes]"></textarea>
                        </div>
                    </div>
                </div>
            `;
        }

        function createInsuredLifeItem(count) {
            return `
                <div class="insured-life-item mb-3 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold">Insured Life ${count > 2 ? (count - 1) : count}</span>
                        <button type="button" class="btn btn-sm btn-danger remove-insured-life">Remove</button>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-3">
                            <label class="form-label">Entity Type</label>
                            <select class="form-control entity-type-select" name="insured_life[${count-1}][entity_type]">
                                <option value="">Select Type</option>
                                <option value="individual">Individual</option>
                                <option value="corporate">Corporate</option>
                                <option value="trust">Trust</option>
                                <option value="foundation">Foundation</option>
                            </select>
                        </div>
                        <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-9">
                            <div class="individual-name-fields" style="display: none;" >
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="insured_life[${count-1}][first_name]">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Middle Name(s)</label>
                                        <input type="text" class="form-control" name="insured_life[${count-1}][middle_name]">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="insured_life[${count-1}][last_name]">
                                    </div>
                                </div>
                            </div>
                            <div class="entity-name-field"  >
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="insured_life[${count-1}][name]">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="2" name="insured_life[${count-1}][notes]"></textarea>
                        </div>
                    </div>
                </div>
            `;
        }

        function createBeneficiaryItem(count) {
            return `
                <div class="beneficiary-item mb-3 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold">Beneficiary ${count > 2 ? (count - 1) : count}</span>
                        <button type="button" class="btn btn-sm btn-danger remove-beneficiary">Remove</button>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-3">
                            <label class="form-label">Entity Type</label>
                            <select class="form-control entity-type-select" name="beneficiary[${count-1}][entity_type]">
                                <option value="">Select Type</option>
                                <option value="individual">Individual</option>
                                <option value="corporate">Corporate</option>
                                <option value="trust">Trust</option>
                                <option value="foundation">Foundation</option>
                            </select>
                        </div>
                        <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-9">
                            <div class="individual-name-fields" style="display: none;" >
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="beneficiary[${count-1}][first_name]">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Middle Name(s)</label>
                                        <input type="text" class="form-control" name="beneficiary[${count-1}][middle_name]">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="beneficiary[${count-1}][last_name]">
                                    </div>
                                </div>
                            </div>
                            <div class="entity-name-field"  >
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="beneficiary[${count-1}][name]">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="2" name="beneficiary[${count-1}][notes]"></textarea>
                        </div>
                    </div>
                </div>
            `;
        }

        function createAdvisorItem(count) {
            return `
                <div class="advisor-item mb-3 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold">Investment Advisor ${count > 2 ? (count - 1) : count}</span>
                        <button type="button" class="btn btn-sm btn-danger remove-advisor">Remove</button>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-3">
                            <label class="form-label">Entity Type</label>
                            <select class="form-control entity-type-select" name="advisor[${count-1}][entity_type]">
                                <option value="">Select Type</option>
                                <option value="Individual">Individual</option>
                                <option value="Corporate">Corporate</option>
                                <option value="Trust">Trust</option>
                                <option value="Foundation">Foundation</option>
                            </select>
                        </div>
                        <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-9">
                            <div class="individual-name-fields" style="display: none;" >
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="advisor[${count-1}][first_name]">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Middle Name(s)</label>
                                        <input type="text" class="form-control" name="advisor[${count-1}][middle_name]">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="advisor[${count-1}][last_name]">
                                    </div>
                                </div>
                            </div>
                            <div class="entity-name-field"  >
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="advisor[${count-1}][name]">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="2" name="advisor[${count-1}][notes]"></textarea>
                        </div>
                    </div>
                </div>
            `;
        }

        function createIdfItem(count) {
            return `
                <div class="idf-item mb-3 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold">IDF Name ${count > 2 ? (count - 1) : count}</span>
                        <button type="button" class="btn btn-sm btn-danger remove-idf">Remove</button>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-3">
                            <label class="form-label">Entity Type</label>
                            <select class="form-control entity-type-select" name="idf[${count-1}][entity_type]">
                                <option value="">Select Type</option>
                                <option value="Individual">Individual</option>
                                <option value="Corporate">Corporate</option>
                                <option value="Trust">Trust</option>
                                <option value="Foundation">Foundation</option>
                            </select>
                        </div>
                        <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-9">
                            <div class="individual-name-fields" style="display: none;" >
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="idf[${count-1}][first_name]">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Middle Name(s)</label>
                                        <input type="text" class="form-control" name="idf[${count-1}][middle_name]">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="idf[${count-1}][last_name]">
                                    </div>
                                </div>
                            </div>
                            <div class="entity-name-field"  >
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="idf[${count-1}][name]">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="2" name="idf[${count-1}][notes]"></textarea>
                        </div>
                    </div>
                </div>
            `;
        }

        function createIdfManagerItem(count) {
            return `
                <div class="idf-manager-item mb-3 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold">IDF Manager ${count > 2 ? (count - 1) : count}</span>
                        <button type="button" class="btn btn-sm btn-danger remove-idf-manager">Remove</button>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-3">
                            <label class="form-label">Entity Type</label>
                            <select class="form-control entity-type-select" name="idf_manager[${count-1}][entity_type]">
                                <option value="">Select Type</option>
                                <option value="Individual">Individual</option>
                                <option value="Corporate">Corporate</option>
                                <option value="Trust">Trust</option>
                                <option value="Foundation">Foundation</option>
                            </select>
                        </div>
                        <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-9">
                            <div class="individual-name-fields" style="display: none;" >
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="idf_manager[${count-1}][first_name]">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Middle Name(s)</label>
                                        <input type="text" class="form-control" name="idf_manager[${count-1}][middle_name]">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="idf_manager[${count-1}][last_name]">
                                    </div>
                                </div>
                            </div>
                            <div class="entity-name-field"  >
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="idf_manager[${count-1}][name]">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="2" name="idf_manager[${count-1}][notes]"></textarea>
                        </div>
                    </div>
                </div>
            `;
        }

        function createCustodianItem(count) {
            return `
                <div class="custodian-item mb-3 p-3 ">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold">Custodian Bank ${count > 2 ? (count - 1) : count}</span>
                        <button type="button" class="btn btn-sm btn-danger remove-custodian">Remove</button>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-3">
                            <label class="form-label">Entity Type</label>
                            <select class="form-control entity-type-select" name="custodian[${count-1}][entity_type]">
                                <option value="">Select Type</option>
                                <option value="Individual">Individual</option>
                                <option value="Corporate">Corporate</option>
                                <option value="Trust">Trust</option>
                                <option value="Foundation">Foundation</option>
                            </select>
                        </div>
                        <div class="col-md-12 col-xl-6 col-lg-6 col-xxl-9">
                            <div class="individual-name-fields" style="display: none;" >
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="custodian[${count-1}][first_name]">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Middle Name(s)</label>
                                        <input type="text" class="form-control" name="custodian[${count-1}][middle_name]">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="custodian[${count-1}][last_name]">
                                    </div>
                                </div>
                            </div>
                            <div class="entity-name-field">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="custodian[${count-1}][name]">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="2" name="custodian[${count-1}][notes]"></textarea>
                        </div>
                    </div>
                </div>
            `;
        }
    });


    
    /**
     * Handle form submission for section D-1
     **/

        // Dynamic passports add/remove for section C-1
        $(document).on('click', '.section-c-1-passport-add', function() {
            const row = `
                <div class="row align-items-end section-c-1-passport-row mt-2">
                    <div class="col-sm-5 mb-2">
                        <input type="text" class="form-control c1-passport" name="passport_number[]" placeholder="Passport Number">
                    </div>
                    <div class="col-sm-5 mb-2">
                        <select class="form-control c1-issuance-country" name="country_of_issuance[]">
                            <option value=""></option>
                            ${countryHtml}
                        </select>
                    </div>
                    <div class="col-sm-2 mb-2">
                        <button type="button" class="btn btn-success section-c-1-passport-add">+</button>
                        <button type="button" class="btn btn-danger section-c-1-passport-remove ms-2">-</button>
                    </div>
                </div>`;
            $(this).closest('.section-c-1-passport-row').parent().append(row);
        });

        $(document).on('click', '.section-c-1-passport-remove', function() {
            const container = $(this).closest('.section-c-1-passport-row').parent();
            if (container.find('.section-c-1-passport-row').length > 1) {
                $(this).closest('.section-c-1-passport-row').remove();
            }
        });


    
    /**
     * Handle form submission for section E-1
     **/

        $(document).on('click', '.section-d-1-passport-add', function() {
            const row = `
                <div class="row align-items-end section-d-1-passport-row mt-2">
                    <div class="col-sm-5 mb-2">
                        <input type="text" class="form-control d1-passport" name="passport_number[]" placeholder="Passport Number">
                    </div>
                    <div class="col-sm-5 mb-2">
                        <select class="form-control d1-issuance-country" name="country_of_issuance[]">
                            <option value=""></option>
                            ${countryHtml}
                        </select>
                    </div>
                    <div class="col-sm-2 mb-2">
                        <button type="button" class="btn btn-success section-d-1-passport-add">+</button>
                        <button type="button" class="btn btn-danger section-d-1-passport-remove ms-2">-</button>
                    </div>
                </div>`;
            $(this).closest('.section-d-1-passport-row').parent().append(row);
        });

        $(document).on('click', '.section-d-1-passport-remove', function() {
            const container = $(this).closest('.section-d-1-passport-row').parent();
            if (container.find('.section-d-1-passport-row').length > 1) {
                $(this).closest('.section-d-1-passport-row').remove();
            }
        });

        $('#form-section-e-1').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
                    const keys = name.match(/[^\[\]]+/g);

                    keys.reduce((acc, key, i) => {
                        if (i === keys.length - 1) {
                            acc[key] = value;
                        } else {
                            acc[key] = acc[key] || {};
                        }
                        return acc[key];
                    }, formData);
                });
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-e-1', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section E-1
     **/  
    
    /**
     * Handle form submission for section E-2
     **/

        $('#form-section-e-2').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
                    const keys = name.match(/[^\[\]]+/g);

                    keys.reduce((acc, key, i) => {
                        if (i === keys.length - 1) {
                            acc[key] = value;
                        } else {
                            acc[key] = acc[key] || {};
                        }
                        return acc[key];
                    }, formData);
                });
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-e-2', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section E-2
     **/

    /**
     * Handle form submission for section E-3
     **/

        $('#form-section-e-3').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
                    const keys = name.match(/[^\[\]]+/g);

                    keys.reduce((acc, key, i) => {
                        if (i === keys.length - 1) {
                            acc[key] = value;
                        } else {
                            acc[key] = acc[key] || {};
                        }
                        return acc[key];
                    }, formData);
                });
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-e-3', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section E-3
     **/

    /**
     * Handle form submission for section E-4
     **/

        $('#form-section-e-4').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
                    const keys = name.match(/[^\[\]]+/g);

                    keys.reduce((acc, key, i) => {
                        if (i === keys.length - 1) {
                            acc[key] = value;
                        } else {
                            acc[key] = acc[key] || {};
                        }
                        return acc[key];
                    }, formData);
                });
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-e-4', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section E-4
     **/  
    
    /**
     * Handle form submission for section F-1
     **/

        $('#form-section-f-1').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formDataObj = {};
                var formDataArray = $(form).serializeArray();

                $.each(formDataArray, function(_, field) {
                    if (formDataObj[field.name]) {
                        if (!Array.isArray(formDataObj[field.name])) {
                            formDataObj[field.name] = [formDataObj[field.name]];
                        }
                        formDataObj[field.name].push(field.value);
                    } else {
                        formDataObj[field.name] = field.value;
                    }
                });
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-f-1', formDataObj, actionType);
            }
        });        

    /**
     * Handle form submission for section F-1
     **/  
    
    /**
     * Handle form submission for section F-2
     **/

        $('#form-section-f-2').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
                    const keys = name.match(/[^\[\]]+/g);

                    keys.reduce((acc, key, i) => {
                        if (i === keys.length - 1) {
                            acc[key] = value;
                        } else {
                            acc[key] = acc[key] || {};
                        }
                        return acc[key];
                    }, formData);
                });
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-f-2', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section F-2
     **/


    /**
     * Handle form submission for section F-3
     **/

        $('#form-section-f-3').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
                    const keys = name.match(/[^\[\]]+/g);

                    keys.reduce((acc, key, i) => {
                        if (i === keys.length - 1) {
                            acc[key] = value;
                        } else {
                            acc[key] = acc[key] || {};
                        }
                        return acc[key];
                    }, formData);
                });
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-f-3', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section F-3
     **/
    
    /**
     * Handle form submission for section F-4
     **/

        $('#form-section-f-4').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
                    const keys = name.match(/[^\[\]]+/g);

                    keys.reduce((acc, key, i) => {
                        if (i === keys.length - 1) {
                            acc[key] = value;
                        } else {
                            acc[key] = acc[key] || {};
                        }
                        return acc[key];
                    }, formData);
                });
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-f-4', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section F-4
     **/  
    
     
    /**
     * Handle form submission for section F-5
     **/

        $('#form-section-f-5').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
                    const keys = name.match(/[^\[\]]+/g);

                    keys.reduce((acc, key, i) => {
                        if (i === keys.length - 1) {
                            acc[key] = value;
                        } else {
                            acc[key] = acc[key] || {};
                        }
                        return acc[key];
                    }, formData);
                });
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-f-5', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section F-4
     **/  
    
     
    /**
     * Handle form submission for section F-6
     **/

        $('#form-section-f-6').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
                    const keys = name.match(/[^\[\]]+/g);

                    keys.reduce((acc, key, i) => {
                        if (i === keys.length - 1) {
                            acc[key] = value;
                        } else {
                            acc[key] = acc[key] || {};
                        }
                        return acc[key];
                    }, formData);
                });
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-f-6', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section F-6
     **/     
    
     
    /**
     * Handle form submission for section F-7
     **/

        $('#form-section-f-7').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
                    const keys = name.match(/[^\[\]]+/g);

                    keys.reduce((acc, key, i) => {
                        if (i === keys.length - 1) {
                            acc[key] = value;
                        } else {
                            acc[key] = acc[key] || {};
                        }
                        return acc[key];
                    }, formData);
                });
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-f-7', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section F-7
     **/    
    
    function isAllEmpty(obj) {
        if (typeof obj !== 'object' || obj === null) {
            return obj === null || obj === '';
        }

        return Object.values(obj).every(isAllEmpty);
    }     
     
    /**
     * Handle form submission for section G-1
     **/

        $('#form-section-g-1').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
                    const keys = name.match(/[^\[\]]+/g);

                    keys.reduce((acc, key, i) => {
                        if (i === keys.length - 1) {
                            acc[key] = value;
                        } else {
                            acc[key] = acc[key] || {};
                        }
                        return acc[key];
                    }, formData);
                });

                if (isAllEmpty(formData)) {
                    Swal.fire('Error', 'Please fill out at least one field before saving', 'error')
                    return false;
                }
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-g-1', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section G-1
     **/    
    
    /**
     * Handle form submission for section G-2
     **/

        $('#form-section-g-2').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
                    const keys = name.match(/[^\[\]]+/g);

                    keys.reduce((acc, key, i) => {
                        if (i === keys.length - 1) {
                            acc[key] = value;
                        } else {
                            acc[key] = acc[key] || {};
                        }
                        return acc[key];
                    }, formData);
                });
                
                if (isAllEmpty(formData)) {
                    Swal.fire('Error', 'Please fill out at least one field before saving', 'error')
                    return false;
                }

                let actionType = event.originalEvent.submitter;
                
                submission('section-g-2', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section G-2
     **/         


    /**
     * Handle form submission for section G-1
     **/

     $('#form-section-g-1').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = $(form).serializeArray().reduce((acc, item) => {
                    acc[item.name] = item.value;
                    return acc;
                }, {});
                
                let actionType = event.originalEvent.submitter;
                
                $.ajax({
                    url: "{{ route('case.submission') }}",
                    method: 'POST',
                    data: {
                        policy: currentCaseId,
                        section: 'section-g-1',
                        data: formData,
                        save: $(actionType).data('type')
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        showSavingStatus('saving');
                    },
                    success: function(response) {
                        showSavingStatus('saved', response.timestamp || (new Date().toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' })));

                        if (response.type === 'save-and-add') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Communication entry saved successfully. You can now add another entry.',
                                allowOutsideClick: true, 
                                allowEscapeKey: true
                            }).then(() => {
                                $('#form-section-g-1')[0].reset();
                                refreshCommunicationAccordion();
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message || 'Saved successfully.',
                                allowOutsideClick: true, 
                                allowEscapeKey: true
                            }).finally(() => {
                                if ('type' in response && response.type == 'save' && 'next_section' in response && response.next_section != '') {
                                    var nextSection = $(actionType).data('next');
                                    var currentSection = 'section-g-1';

                                    if (!nextSection && !currentSection) return;

                                    $('.policy-dropdown-item[data-section="' + currentSection + '"]').removeClass('active');
                                    $('.policy-dropdown-item[data-section="' + currentSection + '"]').parent().parent().css('display', 'none');

                                    $('.policy-dropdown-item[data-section="' + nextSection + '"]').addClass('active');
                                    $('.policy-dropdown-item[data-section="' + nextSection + '"]').parent().parent().css('display', 'block');

                                    $(`#${currentSection}`).addClass('d-none');
                                    $(`#${nextSection}`).removeClass('d-none');                        
                                    
                                } else if ('type' in response && response.type == 'draft') {
                                    window.location.href = "{{ route('cases.index') }}";
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        showSavingStatus('error');
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorList = '';

                            $.each(errors, function(key, messages) {
                                messages.forEach(function(message) {
                                    errorList += `<li>${message}</li>`;
                                });
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Errors',
                                html: `<ul style="text-align: left;">${errorList}</ul>`
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong. Please try again.'
                            });
                        }
                    }
                });
            }
        });

        // Dynamic passports for section B-2
        $(document).on('click', '.section-b-2-passport-add', function() {
            const row = `
                <div class="row align-items-end section-b-2-passport-row mt-2">
                    <div class="col-sm-5 mb-2">
                        <input type="text" class="form-control secction-b-2-passport" name="passport_number[]" placeholder="Passport Number" value="">
                    </div>
                    <div class="col-sm-5 mb-2">
                        <select class="form-control secction-b-2-passport-issue-country" name="country_of_issuance[]">
                            <option value=""></option>
                            ${countryHtml}
                        </select>
                    </div>
                    <div class="col-sm-2 mb-2">
                        <button type="button" class="btn btn-success section-b-2-passport-add">+</button>
                        <button type="button" class="btn btn-danger section-b-2-passport-remove">-</button>
                    </div>
                </div>`;
            $(this).closest('.section-b-2-passport-row').parent().append(row);
        });

        $(document).on('click', '.section-b-2-passport-remove', function() {
            const container = $(this).closest('.section-b-2-passport-row').parent();
            if (container.find('.section-b-2-passport-row').length > 1) {
                $(this).closest('.section-b-2-passport-row').remove();
            }
        });

    /**
     * Handle form submission for section G-1
     **/

    /**
     * Handle form submission for section G-2
     **/

     $('#form-section-g-2').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = $(form).serializeArray().reduce((acc, item) => {
                    acc[item.name] = item.value;
                    return acc;
                }, {});
                
                let actionType = event.originalEvent.submitter;
                
                $.ajax({
                    url: "{{ route('case.submission') }}",
                    method: 'POST',
                    data: {
                        policy: currentCaseId,
                        section: 'section-g-2',
                        data: formData,
                        save: $(actionType).data('type')
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        showSavingStatus('saving');
                    },
                    success: function(response) {
                        showSavingStatus('saved', response.timestamp || (new Date().toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' })));

                        if (response.type === 'save-and-add') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Case file note saved successfully. You can now add another note.',
                                allowOutsideClick: true, 
                                allowEscapeKey: true
                            }).then(() => {
                                $('#form-section-g-2')[0].reset();
                                refreshCaseFileNotesAccordion();
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message || 'Saved successfully.',
                                allowOutsideClick: true, 
                                allowEscapeKey: true
                            }).finally(() => {
                                if ('type' in response && response.type == 'save' && 'next_section' in response && response.next_section != '') {
                                    var nextSection = $(actionType).data('next');
                                    var currentSection = 'section-g-2';

                                    if (!nextSection && !currentSection) return;

                                    $('.policy-dropdown-item[data-section="' + currentSection + '"]').removeClass('active');
                                    $('.policy-dropdown-item[data-section="' + currentSection + '"]').parent().parent().css('display', 'none');

                                    $('.policy-dropdown-item[data-section="' + nextSection + '"]').addClass('active');
                                    $('.policy-dropdown-item[data-section="' + nextSection + '"]').parent().parent().css('display', 'block');

                                    $(`#${currentSection}`).addClass('d-none');
                                    $(`#${nextSection}`).removeClass('d-none');                        
                                    
                                } else if ('type' in response && response.type == 'draft') {
                                    window.location.href = "{{ route('cases.index') }}";
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        showSavingStatus('error');
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorList = '';

                            $.each(errors, function(key, messages) {
                                messages.forEach(function(message) {
                                    errorList += `<li>${message}</li>`;
                                });
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Errors',
                                html: `<ul style="text-align: left;">${errorList}</ul>`
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong. Please try again.'
                            });
                        }
                    }
                });
            }
        });        

    /**
     * Handle form submission for section G-2
     **/

    /**
     * Handle custom downloadable document (H-1) modal actions
     **/

        $(document).on('click', '#upload-custom-form-btn', function(){
            const modalForm = document.getElementById('custom-form-upload');
            if (!modalForm) return;
            const formData = new FormData(modalForm);

            formData.append('policy', currentCaseId);
            formData.append('section', 'section-h-1');
            formData.append('save', 'save');

            $.ajax({
                url: "{{ route('case.submission') }}",
                method: 'POST',
                processData: false,
                contentType: false,
                data: formData,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                beforeSend: function () { showSavingStatus('saving'); },
                success: function(){
                    showSavingStatus('saved');
                    $('#customFormUploadModal').modal('hide');
                    window.location.reload();
                },
                error: function(){ showSavingStatus('error'); }
            });
        });

        $(document).on('click', '.delete-downloadable', function(){
            const id = $(this).data('id');
            if (!id) return;
            Swal.fire({ title: 'Delete this document?', icon: 'warning', showCancelButton: true }).then((res)=>{
                if (!res.isConfirmed) return;
                let fd = new FormData();
                fd.append('policy', currentCaseId);
                fd.append('section', 'section-h-1');
                fd.append('save', 'save');
                fd.append('action', 'delete');
                fd.append('doc_id', id);
                $.ajax({
                    url: "{{ route('case.submission') }}",
                    method: 'POST', processData: false, contentType: false, data: fd,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(){ window.location.reload(); },
                    error: function(){ Swal.fire('Error','Delete failed','error'); }
                });
            });
        });

        $(document).on('click', '.remove-uploaded-file', function(){
            const id = $(this).data('doc-id');
            if (!id) return;
            Swal.fire({
                icon: 'warning',
                title: 'Remove uploaded file?',
                text: 'This will delete the uploaded file permanently.',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it',
                cancelButtonText: 'Cancel'
            }).then((res)=>{
                if (!res.isConfirmed) return;
                $.ajax({
                    url: "{{ route('downloadable-documents.remove-file') }}",
                    method: 'POST',
                    data: { doc_id: id },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    beforeSend: function(){ showSavingStatus && showSavingStatus('saving'); },
                    success: function(){ showSavingStatus && showSavingStatus('saved'); window.location.reload(); },
                    error: function(){ showSavingStatus && showSavingStatus('error'); Swal.fire('Error','Failed to remove file','error'); }
                });
            });
        });

        $(document).on('change', '.h1-upload-existing', function(){
            const file = this.files && this.files[0];
            if (!file) return;
            const docId = $(this).data('doc-id');
            let fd = new FormData();
            fd.append('policy', currentCaseId);
            fd.append('section', 'section-h-1');
            fd.append('save', 'save');
            fd.append('doc_id', docId);
            fd.append('file', file);
            $.ajax({
                url: "{{ route('case.submission') }}",
                method: 'POST',
                processData: false,
                contentType: false,
                data: fd,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                beforeSend: function () { showSavingStatus('saving'); },
                success: function(){ showSavingStatus('saved'); window.location.reload(); },
                error: function(){ showSavingStatus('error'); Swal.fire('Error','Upload failed','error'); }
            });
        });

        $(document).on('submit', '#addNewFormForm', function(e){
            e.preventDefault();
            const $input = $('#formName');
            const title = ($input.val() || '').trim();
            $('#formNameError').text('');
            $input.removeClass('is-invalid');

            if (!title) {
                $input.addClass('is-invalid');
                $('#formNameError').text('The form name field is required.');
                return;
            }

            $.ajax({
                url: "{{ route('downloadable-documents.store') }}",
                method: 'POST',
                data: { title: title },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                beforeSend: function(){ showSavingStatus && showSavingStatus('saving'); },
                success: function(resp){
                    showSavingStatus && showSavingStatus('saved');
                    $('#addNewFormModal').modal('hide');
                    window.location.reload();
                },
                error: function(xhr){
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const msg = xhr.responseJSON.errors.title ? xhr.responseJSON.errors.title[0] : 'Validation error';
                        $('#formNameError').text(msg);
                        $('#formName').addClass('is-invalid');
                    } else {
                        Swal.fire('Error', 'Unable to add form. Please try again.', 'error');
                    }
                }
            });
        });

        let h1SortEnabled = false;
        function ensureSortableLoaded(callback){
            if ($.fn.sortable) { callback(); return; }
            const script = document.createElement('script');
            script.src = '/assets/js/jquery-ui.min.js';
            script.onload = callback;
            document.body.appendChild(script);
        }

        $(document).on('click', '#toggleSortMode', function(){
            const $btn = $(this);
            h1SortEnabled = !h1SortEnabled;
            if (h1SortEnabled) {
                $btn.addClass('btn-warning').removeClass('btn-secondary').text('Sorting Enabled');
                $('.drag-handle').show();
                ensureSortableLoaded(function(){
                    const $container = $('#sortable-forms-container .col-xl-12');
                    try { $container.sortable('destroy'); } catch(e){}
                    $container.sortable({
                        items: '.sortable-item',
                        handle: '.drag-handle',
                        axis: 'y',
                        helper: 'clone',
                        tolerance: 'pointer',
                        cancel: 'input,button,a,select,textarea,label',
                        placeholder: 'sortable-placeholder',
                        forcePlaceholderSize: true,
                        start: function(e, ui){ ui.placeholder.height(ui.helper.outerHeight()); },
                        update: function(){
                            const items = [];
                            $container.find('.sortable-item').each(function(idx){
                                items.push({ id: $(this).data('id'), ordering: idx + 1 });
                            });
                            $.ajax({
                                url: "{{ route('downloadable-documents.update-ordering') }}",
                                method: 'POST',
                                data: { items: items },
                                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                success: function(){},
                                error: function(){ Swal.fire('Error','Failed to save order','error'); }
                            });
                        }
                    });
                });
            } else {
                $btn.removeClass('btn-warning').addClass('btn-secondary').text('Sort Forms');
                $('.drag-handle').hide();
                const $container = $('#sortable-forms-container .col-xl-12');
                try { $container.sortable('destroy'); } catch(e){}
            }
        });

    /**
     * Handle form submission for section H-2
     **/

        $('#form-section-h-2').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = new FormData(form);
                let actionType = event.originalEvent.submitter;
                
                formData.append('policy', currentCaseId)
                formData.append('section', 'section-h-2')
                formData.append('save', $(actionType).data('type'))

                $.ajax({
                    url: "{{ route('case.submission') }}",
                    method: 'POST',
                    processData: false,
                    contentType: false,
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        showSavingStatus('saving');
                    },
                    success: function(response) {
                        showSavingStatus('saved', response.timestamp || (new Date().toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' })));

                        if ('type' in response && response.type == 'save' && 'next_section' in response && response.next_section != '' && response.next_section != null) {
                            var nextSection = $(submitButton).data('next');
                            var currentSection = section;

                            if (!nextSection && !currentSection) return;

                            $('.policy-dropdown-item[data-section="' + currentSection + '"]').removeClass('active');
                            $('.policy-dropdown-item[data-section="' + currentSection + '"]').parent().parent().css('display', 'none');

                            $('.policy-dropdown-item[data-section="' + nextSection + '"]').addClass('active');
                            $('.policy-dropdown-item[data-section="' + nextSection + '"]').parent().parent().css('display', 'block');

                            $(`#${currentSection}`).addClass('d-none');
                            $(`#${nextSection}`).removeClass('d-none');
                            
                        }
                    },
                    error: function(xhr) {
                        showSavingStatus('error');
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorList = '';

                            $.each(errors, function(key, messages) {
                                messages.forEach(function(message) {
                                    errorList += `<li>${message}</li>`;
                                });
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Errors',
                                html: `<ul style="text-align: left;">${errorList}</ul>`
                            });
                        } else if (xhr.status === 403) {
                            Swal.fire({
                                icon: 'error',
                                title: 'You do not have authorization to submit this case'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong. Please try again.'
                            });
                        }
                    }
                });                
            }
        });        

    /**
     * Handle form submission for section H-2
     **/

    $(document).on('change', 'input[name="section_a_1_entity"]', function() {
        const selectedType = $(this).val();
        
        if (selectedType === 'Entity') {
            $('#s1ss1ent').removeClass('d-none');
            $('#s1ss1ind').addClass('d-none');
            $('#s1ss1contperheader').removeClass('d-none');
            $('#s1ss1contper').removeClass('d-none');
        } else {
            $('#s1ss1ent').addClass('d-none');
            $('#s1ss1ind').removeClass('d-none');
            $('#s1ss1contperheader').addClass('d-none');
            $('#s1ss1contper').addClass('d-none');
        }
    });

    $(document).on('change', '.section-b-1-type', function() {
        const selectedType = $(this).val();
        
        if (selectedType === 'Individual') {
            $('.section-b-1-individual-fields').removeClass('d-none');
            $('.section-b-1-entity-field').addClass('d-none');
            $('.section-b-1-place-label').text('Place Of Birth: ');
            $('.section-b-1-date-label').text('Date Of Birth: ');
        } else {
            $('.section-b-1-individual-fields').addClass('d-none');
            $('.section-b-1-entity-field').removeClass('d-none');
            $('.section-b-1-place-label').text('Establishment: ');
            $('.section-b-1-date-label').text('Establishment: ');
        }
    });

    $(document).on('change', '.section-c-1-type', function() {
        const selectedType = $(this).val();
        if (selectedType === 'Individual') {
            $('.section-c-1-individual-fields').removeClass('d-none');
            $('.section-c-1-entity-field').addClass('d-none');
            $('.c1-place-label').text('Place Of Birth: ');
            $('.c1-date-label').text('Date Of Birth: ');
            $('.c1-address-label').text('Registered Address: ');
            $('.c1-individual-extra').removeClass('d-none');
        } else {
            $('.section-c-1-individual-fields').addClass('d-none');
            $('.section-c-1-entity-field').removeClass('d-none');
            $('.c1-place-label').text('Establishment: ');
            $('.c1-date-label').text('Establishment: ');
            $('.c1-address-label').text('Residential Address: ');
            $('.c1-individual-extra').addClass('d-none');
        }
    });

    $(document).on('change', '.section-d-1-type', function() {
        const selectedType = $(this).val();
        if (selectedType === 'Individual') {
            $('.section-d-1-individual-fields').removeClass('d-none');
            $('.section-d-1-entity-field').addClass('d-none');
            $('.d1-place-label').text('Place Of Birth: ');
            $('.d1-date-label').text('Date Of Birth: ');
            $('.d1-address-label').text('Registered Address: ');
            $('.d1-individual-extra').removeClass('d-none');
        } else {
            $('.section-d-1-individual-fields').addClass('d-none');
            $('.section-d-1-entity-field').removeClass('d-none');
            $('.d1-place-label').text('Establishment: ');
            $('.d1-date-label').text('Establishment: ');
            $('.d1-address-label').text('Residential Address: ');
            $('.d1-individual-extra').addClass('d-none');
        }
    });

    $(document).ready(function() {
        $('input[name="section_a_1_entity"]:checked').trigger('change');
        $('.section-b-1-type:checked').trigger('change');
        $('.section-c-1-type:checked').trigger('change');
        $('.section-d-1-type:checked').trigger('change');
    });
    

</script>