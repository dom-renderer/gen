<script>
$(document).ready(function() {
    $('[data-bs-toggle="tooltip"]').tooltip();

    $(document).on('click', '.tooltip-edit', function () {
        let elementId = $(this).data('element');
        $('#tooltip_element_id').val(elementId);

        $.get("{{ url('tooltips') }}/" + elementId, function (data) {
            $('#tooltip_content').val(data?.content ?? '');
            new bootstrap.Modal('#tooltipModal').show();
        });
    });

    $('#tooltipForm').on('submit', function (e) {
        e.preventDefault();
        $.post("{{ route('tooltips.update') }}", $(this).serialize(), function (res) {
            if(res.success){
                let icons = $('.tooltip-edit[data-element="'+$('#tooltip_element_id').val()+'"]');

                icons.each(function() {
                    let icon = $(this);

                    let tipInstance = bootstrap.Tooltip.getInstance(icon[0]);

                    if (tipInstance) {
                        tipInstance._config.title = res.content;
                    } else {
                        icon.attr('title', res.content).tooltip();
                    }
                });

                bootstrap.Modal.getInstance(document.getElementById('tooltipModal')).hide();
            }
        });
    });

    $('.delete-doc-perm').on('click', function (e) {
        e.preventDefault();

        const url = $(this).attr('href');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This will delete the document and associated document with this form.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });

});
</script>

<script>
    const inputsa1 = document.querySelector('#section-a-1-phone_number');

    const itisa1 = window.intlTelInput(inputsa1, {
        preferredCountries: ["us", "gb", "ca", "hk", "ch", "ae"],
        separateDialCode:true,
        nationalMode:false,
        utilsScript: "{{ asset('assets/js/intel-tel-2.min.js') }}"
    });

    inputsa1.addEventListener("countrychange", function() {
        if (itisa1.isValidNumber()) {
            $('#section-a-1-dial_code').val(itisa1.s.dialCode);
        }
    });

    inputsa1.addEventListener('keyup', () => {
        if (itisa1.isValidNumber()) {
            $('#section-a-1-dial_code').val(itisa1.s.dialCode);
        }
    });

    const inputcp1 = document.querySelector('.sa1edcp1_pn');

    const inputcp1Int = window.intlTelInput(inputcp1, {
        preferredCountries: ["us", "gb", "ca", "hk", "ch", "ae"],
        separateDialCode:true,
        nationalMode:false,
        utilsScript: "{{ asset('assets/js/intel-tel-2.min.js') }}"
    });

    inputcp1.addEventListener("countrychange", function() {
        if (inputcp1Int.isValidNumber()) {
            $('.sa1edcp1_dc').val(inputcp1Int.s.dialCode);
        }
    });

    inputcp1.addEventListener('keyup', () => {
        if (inputcp1Int.isValidNumber()) {
            $('.sa1edcp1_dc').val(inputcp1Int.s.dialCode);
        }
    });
</script>

<script>
    const inputsd1 = document.querySelector('#d-1-phone_number');

    if (inputsd1) {
        const itisd1 = window.intlTelInput(inputsd1, {
            preferredCountries: ["us", "gb", "ca", "hk", "ch", "ae"],
            separateDialCode:true,
            nationalMode:false,
            utilsScript: "{{ asset('assets/js/intel-tel-2.min.js') }}"
        });

        inputsd1.addEventListener("countrychange", function() {
            if (itisd1.isValidNumber()) {
                $('#d-1-dial_code').val(itisd1.s.dialCode);
            }
        });

        inputsd1.addEventListener('keyup', () => {
            if (itisd1.isValidNumber()) {
                $('#d-1-dial_code').val(itisd1.s.dialCode);
            }
        });

        window.__d1_getDialCode = function(){ return itisd1 && itisd1.s ? itisd1.s.dialCode : ''; };
    }
</script>

<script>
    let autoSaveTimers = {};
    let isSaving = {};
    let lastSavedData = {};

    function showSavingStatus(status, message = '') {
        const container = $('#saving-container');

        switch (status) {
            case 'saving':
                container.html(`
            <div class="state-item">
                <div class="save-indicator saving">
                    <div class="spinner"></div>
                    <span>Saving...</span>
                </div>
            </div>
        `);
                break;
            case 'saved':
                container.html(`
            <div class="state-item">
                <div class="save-indicator saved">
                    <svg class="checkmark" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                    </svg>
                    <span>Changes saved at ${message}</span>
                </div>
            </div>
        `);
                break;
            case 'error':
                container.html(`
            <div class="state-item">
                <div class="save-indicator error">
                    <svg class="error-icon" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.58L19 8l-9 9z"/>
                        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" fill="#d93025"/>
                    </svg>
                    <span>Unable to save</span>
                </div>
            </div>
        `);
                break;
            case 'unauthorized':
                container.html(`
            <div class="state-item">
                <div class="save-indicator error">
                    <svg class="error-icon" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.58L19 8l-9 9z"/>
                        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" fill="#d93025"/>
                    </svg>
                    <span>You do not have authorization to submit this case</span>
                </div>
            </div>
        `);
                break;
        }
    }

    function collectFormData(sectionId) {
        const formData = {};
        const form = $(`#form-${sectionId}`);

        if (form.length) {

            if (sectionId == 'section-a-2' || sectionId == 'section-f-3' || sectionId == 'section-f-4' || sectionId ==
                'section-f-5' || sectionId == 'section-f-6' || sectionId == 'section-e-1') {
                $(form).serializeArray().forEach(({
                    name,
                    value
                }) => {
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
            } else if (sectionId == 'section-a-1') {

                $(form).serializeArray().forEach(({
                    name,
                    value
                }) => {
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

                formData.section_a_1_dial_code = itisa1.s.dialCode;
                

            } else if (sectionId == 'section-b-1' || sectionId == 'section-b-2' || sectionId == 'section-c-1' || sectionId == 'section-d-1') {
                $(form).serializeArray().forEach(({ name, value }) => {
                    const keys = name.match(/[^\[\]]+/g);

                    keys.reduce((acc, key, i) => {
                        if (i === keys.length - 1) {
                            if (Array.isArray(acc[key])) {
                                acc[key].push(value);
                            } else if (acc[key]) {
                                acc[key] = [acc[key], value];
                            } else {
                                acc[key] = value;
                            }
                        } else {
                            acc[key] = acc[key] || {};
                        }
                        return acc[key];
                    }, formData);
                });
            } else {
                form.find('input, select, textarea').each(function() {
                    const $field = $(this);
                    const fieldName = $field.attr('name');
                    const fieldType = $field.attr('type');

                    if (fieldName) {
                        let value = '';

                        if (fieldType === 'radio') {
                            value = form.find(`input[name="${fieldName}"]:checked`).val() || '';
                        } else if (fieldType === 'checkbox') {
                            value = $field.is(':checked') ? $field.val() : '';
                        } else {
                            value = $field.val() || '';
                        }

                        formData[fieldName] = value;
                    }
                });

                form.find('select').each(function() {
                    const $select = $(this);
                    const selectId = $select.attr('id');
                    if (selectId && $select.hasClass('select2-hidden-accessible')) {
                        const select2Value = $select.select2('val');
                        if (select2Value) {
                            formData[`${selectId}_select2`] = select2Value;
                        }
                    }
                });
            }
        }

        return formData;
    }

    function hasDataChanged(sectionId) {
        const currentData = JSON.stringify(collectFormData(sectionId));
        const lastData = JSON.stringify(lastSavedData[sectionId] || {});
        return currentData !== lastData;
    }

    function performAutoSave(sectionId) {
        if ("{{ request()->route()->getName() }}" == 'cases.view') {
            return false;            
        }

        if (isSaving[sectionId] || !hasDataChanged(sectionId)) {
            return;
        }

        isSaving[sectionId] = true;
        showSavingStatus('saving');

        const formData = collectFormData(sectionId);
        const policyId = currentCaseId;

        $.ajax({
            url: '{{ route('case.auto-save') }}',
            type: 'POST',
            data: {
                policy: policyId,
                section: sectionId,
                data: formData,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    lastSavedData[sectionId] = formData;
                    showSavingStatus('saved', response.timestamp);
                } else {
                    showSavingStatus('error');
                }
            },
            error: function(xhr) {

                if (xhr.status === 403) {
                    showSavingStatus('unauthorized');
                    Swal.fire({
                        icon: 'error',
                        title: 'You do not have authorization to submit this case'
                    });
                } else {
                    showSavingStatus('error');
                }
            },
            complete: function() {
                isSaving[sectionId] = false;

                if (sectionId == 'section-g-1') {
                    refreshCommunicationAccordion()
                }

                if (sectionId == 'section-g-2') {
                    refreshCaseFileNotesAccordion()
                }
            }
        });
    }

    function refreshCommunicationAccordion() {
        $.ajax({
            url: "{{ route('case.get-communications') }}",
            method: 'GET',
            data: {
                policy: currentCaseId
            },
            success: function(response) {
                if (response.html) {
                    $('#communication-entries-container').html(response.html);
                } else {
                    $('#communication-entries-container').html('');
                }
            },
            error: function() {
                console.log('Failed to refresh communication accordion');
            }
        });
    }

    function refreshCaseFileNotesAccordion() {
        $.ajax({
            url: "{{ route('case.get-case-file-notes') }}",
            method: 'GET',
            data: {
                policy: currentCaseId
            },
            success: function(response) {
                if (response.html) {
                    $('#case-file-notes-container').html(response.html);
                } else {
                    $('#case-file-notes-container').html('');                    
                }
            },
            error: function() {
                console.log('Failed to refresh case file notes accordion');
            }
        });
    }

    function initAutoSave(sectionId, autoSaveDelay = 3000) {
        if (!autoSaveTimers[sectionId]) {
            autoSaveTimers[sectionId] = null;
        }
        if (!isSaving[sectionId]) {
            isSaving[sectionId] = false;
        }
        if (!lastSavedData[sectionId]) {
            lastSavedData[sectionId] = {};
        }

        $(document).on('input change',
            `#form-${sectionId} input, #form-${sectionId} select, #form-${sectionId} textarea`,
            function() {
                if (autoSaveTimers[sectionId]) {
                    clearTimeout(autoSaveTimers[sectionId]);
                }

                autoSaveTimers[sectionId] = setTimeout(function() {
                    performAutoSave(sectionId);
                }, autoSaveDelay);
            });

        $(document).on('select2:select select2:unselect', `#form-${sectionId} select`, function() {
            if (autoSaveTimers[sectionId]) {
                clearTimeout(autoSaveTimers[sectionId]);
            }

            autoSaveTimers[sectionId] = setTimeout(function() {
                performAutoSave(sectionId);
            }, autoSaveDelay);
        });

        lastSavedData[sectionId] = collectFormData(sectionId);
    }

    $(document).ready(function() {

         $('#form-section-b-1 select').select2({
            theme: 'classic',
            width: '100%',
            placeholder: 'Select Country'
         });

         $('#form-section-b-2 select').select2({
            theme: 'classic',
            width: '100%',
            placeholder: 'Select Country'
         });

         $('#form-section-c-1 select').select2({
            theme: 'classic',
            width: '100%',
            placeholder: 'Select Country'
         });

         $('#form-section-d-1 select').select2({
            theme: 'classic',
            width: '100%',
            placeholder: 'Select Country'
         });

        const sections = [

        ];

        // sections.forEach(function(sectionId) {
        //     if ($(`#form-${sectionId}`).length) {
        //         initAutoSave(sectionId);
        //     }
        // });
    });

    function triggerAutoSave(sectionId) {
        if (hasDataChanged(sectionId)) {
            performAutoSave(sectionId);
        }
    }

    function clearAutoSaveTimer(sectionId) {
        if (autoSaveTimers[sectionId]) {
            clearTimeout(autoSaveTimers[sectionId]);
            autoSaveTimers[sectionId] = null;
        }
    }
</script>

<script>
    let currentCaseId = "{{ $policy?->id }}";

    $(document).ready(function() {
        // $('.each-options').click(function() {
        //     $('.case-section').addClass('d-none');

        //     var section = $(this).data('section');
        //     $(`#${section}`).removeClass('d-none');
        // });

        $('input[name="section_a_1_entity"]').on('click', function () {
            if ($(this).val() == 'Entity') {
                $('#s1ss1ind').addClass('d-none');
                $('#s1ss1ent').removeClass('d-none');
                $('#s1ss1contper').removeClass('d-none');
                $('#s1ss1contper').prev().removeClass('d-none');
            } else {
                $('#s1ss1ind').removeClass('d-none');
                $('#s1ss1ent').addClass('d-none');
                $('#s1ss1contper').addClass('d-none');
                $('#s1ss1contper').prev().addClass('d-none');
            }
        });

        $('#section-a-1-introducer-select').select2({
            allowClear: true,
            placeholder: 'Select Introducer',
            theme: 'classic',
            width: '100%',
            ajax: {
                url: "{{ route('introducer-list') }}",
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        searchQuery: params.term,
                        page: params.page || 1,
                        _token: '{{ csrf_token() }}'
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: $.map(data.items, function(item) { return { id: item.id, text: item.text }; }),
                        pagination: { more: data.pagination.more }
                    };
                },
                cache: true
            }
        }).on('select2:select', function(e){
            const id = e.params.data.id;
            if (id === 'ADD_NEW_INTRODUCER') {
                $('#form-section-a-1')[0].reset();
                $('input[name="section_a_1_entity"][value="Individual"]').prop('checked', true).trigger('click');
                if (typeof itisa1 !== 'undefined' && itisa1) {
                    itisa1.setCountry('ch');
                    $('#section-a-1-dial_code').val(itisa1.s.dialCode || '41');
                }
                $('#s1ss1contper').addClass('d-none').prev().addClass('d-none');
                return;
            }
            $.ajax({
                url: "{{ route('introducer-details') }}",
                type: 'POST',
                data: { introducer_id: id, _token: '{{ csrf_token() }}' },
                success: function(res){
                    const d = res.introducer || {};
                    const typeCap = (d.type || '').charAt(0).toUpperCase() + (d.type || '').slice(1);
                    if (typeCap === 'Entity') {
                        $('input[name="section_a_1_entity"][value="Entity"]').prop('checked', true).trigger('click');
                        $('input[name="section_a_1_name"]').val(d.name || '');
                    } else {
                        $('input[name="section_a_1_entity"][value="Individual"]').prop('checked', true).trigger('click');
                        $('input[name="section_a_1_first_name"]').val(d.name || '');
                        $('input[name="section_a_1_middle_name"]').val(d.middle_name || '');
                        $('input[name="section_a_1_last_name"]').val(d.last_name || '');
                    }
                    $('input[name="section_a_1_email"]').val(d.email || '');
                    $('input[name="section_a_1_phone"]').val(d.contact_number || '');
                    $('#section-a-1-dial_code').val(d.dial_code || '');
                    if (typeof itisa1 !== 'undefined' && itisa1) {
                        const iso = d.dial_iso2 || 'ch';
                        itisa1.setCountry(iso);
                        $('#section-a-1-dial_code').val(itisa1.s.dialCode || d.dial_code || '');
                    }

                    if ((d.type || '') === 'entity') {
                        const contacts = res.contacts || [];
                        $('#s1ss1contper').removeClass('d-none');
                        $('#s1ss1contper').prev().removeClass('d-none');
                        $('#s1ss1contper').empty();
                        contacts.forEach(function(c, idx){
                            const row = `
                                <div class="row">
                                    <div class="col-md-2">
                                        <input type="hidden" class="contact_person_id" name="contact_person_id[${idx}]" value="${c.id || ''}">
                                        <input type="text" class="form-control contact_person_first_name" name="contact_person_first_name[${idx}]" placeholder="First Name" value="${c.name || ''}">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control contact_person_middle_name" name="contact_person_middle_name[${idx}]" placeholder="Middle Name" value="${c.middle_name || ''}">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control contact_person_last_name" name="contact_person_last_name[${idx}]" placeholder="Last Name" value="${c.last_name || ''}">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="email" class="form-control contact_person_email" name="contact_person_email[${idx}]" placeholder="Email" value="${c.email || ''}">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="hidden" class="contact_person_phone_number_dial_code" id="contact_person_phone_number_dial_code_${idx}" name="contact_person_phone_number_dial_code[${idx}]" value="${c.dial_code || ''}">
                                        <input type="tel" class="form-control contact_person_phone_number" name="contact_person_phone_number[${idx}]" id="contact_person_phone_number_${idx}" value="${c.contact_number || ''}">
                                    </div>
                                    <div class="col-md-1 row">
                                        <button type="button" class="btn btn-success btn-sm s1ss1contperadd"> + </button>
                                        <button type="button" class="btn btn-danger btn-sm s1ss1contperrem"> - </button>
                                    </div>
                                </div>`;
                            $('#s1ss1contper').append(row);

                            const inputEl = document.getElementById(`contact_person_phone_number_${idx}`);
                            if (inputEl && window.intlTelInput) {
                                const iti = window.intlTelInput(inputEl, {
                                    initialCountry: (c.dial_iso2 || 'ch'),
                                    preferredCountries: ["us", "gb", "ca", "hk", "ch", "ae"],
                                    separateDialCode: true,
                                    nationalMode: false,
                                    utilsScript: "{{ asset('assets/js/intel-tel-2.min.js') }}"
                                });
                                const updateDial = function() {
                                    const dc = iti && iti.s ? iti.s.dialCode : '';
                                    $(inputEl).closest('.row').find('.contact_person_phone_number_dial_code').val(dc);
                                };
                                inputEl.addEventListener('countrychange', updateDial);
                                inputEl.addEventListener('keyup', updateDial);
                                updateDial();
                            }
                        });
                        if (contacts.length === 0) {
                            $('#s1ss1contper').html($('#s1ss1contper').html());
                        }
                    }
                }
            });
        }).on('select2:clear', function(){
            $('#form-section-a-1')[0].reset();
            if (typeof itisa1 !== 'undefined' && itisa1) {
                itisa1.setCountry('ch');
                $('#section-a-1-dial_code').val(itisa1.s.dialCode || '41');
            }
        });
    });
</script>
{{-- Section C --}}
<script>
    const countryHtml = `{!! Helper::allCountriesHTML() !!}`;
    $(document).ready(function() {
        $(document).on('click', '.section-b-1-add', function() {
            let newRow = `<div class="row mb-3 section-b-1-country-tax-residence-row">
            <div class="col-sm-10">
                <select class="form-control section-b-1-country-tax-residence" name="all_countries[]">
                    <option value=""></option>
                    ${countryHtml}
                </select>
            </div>
            <div class="col-sm-2">
                <button type="button" class="btn btn-success section-b-1-add">+</button>
                <button type="button" class="btn btn-danger section-b-1-remove">-</button>
            </div>
        </div>`;
            $('.section-b-1-country-tax-residence-row:last').after(newRow);

            $('.section-b-1-country-tax-residence-row:last .section-b-1-country-tax-residence').select2({
                theme: 'classic',
                width: '100%',
                placeholder: 'Select Country'
            });
        });

        $(document).on('click', '.section-b-1-remove', function() {
            if ($('.section-b-1-country-tax-residence-row').length > 1) {
                $(this).closest('.section-b-1-country-tax-residence-row').remove();
            }
        });
    });
</script>
{{-- Section D --}}
<script>
    $(document).ready(function() {
        $(document).on('click', '.section-b-2-add', function() {
            let newRow = `<div class="row mb-3 section-b-2-country-tax-residence-row">
            <div class="col-sm-10">
                <select class="form-control section-b-2-country-tax-residence" name="all_countries[]">
                    <option value=""></option>
                    ${countryHtml}
                </select>
            </div>
            <div class="col-sm-2">
                <button type="button" class="btn btn-success section-b-2-add">+</button>
                <button type="button" class="btn btn-danger section-b-2-remove">-</button>
            </div>
        </div>`;
            $('.section-b-2-country-tax-residence-row:last').after(newRow);

            $('.section-b-2-country-tax-residence-row:last .section-b-2-country-tax-residence').select2({
                theme: 'classic',
                width: '100%',
                placeholder: 'Select Country'
            });
        });

        $(document).on('click', '.section-b-2-remove', function() {
            if ($('.section-b-2-country-tax-residence-row').length > 1) {
                $(this).closest('.section-b-2-country-tax-residence-row').remove();
            }
        });
    });
</script>
{{-- Section D --}}
<script>
    $(document).ready(function() {
        $(document).on('click', '.section-c-1-add', function() {
            let newRow = `<div class="row section-c-1-country-tax-residence-row mb-2 mt-3">
                <div class="col-sm-10">
                        <select class="form-control section-c-1-country-tax-residence" name="all_countries[]">
                        <option value=""></option>
                        ${countryHtml}
                    </select>
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-success section-c-1-add">+</button>
                    <button type="button" class="btn btn-danger section-c-1-remove">-</button>
                </div>
            </div>`;
            $('.section-c-1-country-tax-residence-row:last').after(newRow);

            $('.section-c-1-country-tax-residence-row:last .section-c-1-country-tax-residence').select2({
                theme: 'classic',
                width: '100%',
                placeholder: 'Select Country'
            });
        });

        $(document).on('click', '.section-c-1-remove', function() {
            if ($('.section-c-1-country-tax-residence-row').length > 1) {
                $(this).closest('.section-c-1-country-tax-residence-row').remove();
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
    $(document).on('click', '.section-d-1-add', function() {
        let newRow = `<div class="row section-d-1-country-tax-residence-row">
            <div class="col-sm-9">
                <select class="form-control section-d-1-country-tax-residence" name="all_countries[]">
                    <option value=""></option>
                    ${countryHtml}
                </select>
            </div>
            <div class="col-sm-3 mb-3">
                <button type="button" class="btn btn-success section-d-1-add">+</button>
                <button type="button" class="btn btn-danger section-d-1-remove ms-2">-</button>
            </div>
        </div>`;
        $('.section-d-1-country-tax-residence-row:last').after(newRow);

            $('.section-d-1-country-tax-residence-row:last .section-d-1-country-tax-residence').select2({
                theme: 'classic',
                width: '100%',
                placeholder: 'Select Country'
            });
    });

    $(document).on('click', '.section-d-1-remove', function() {
        if ($('.section-d-1-country-tax-residence-row').length > 1) {
            $(this).closest('.section-d-1-country-tax-residence-row').remove();
        }
    });
});
</script>
<script>
    $(document).ready(function() {

        $('#section-b-2-country_id, #section-b-2-country_issuance, #section-b-2-country_legal_residence, .section-b-2-countries-tax')
            .select2({
                allowClear: true,
                placeholder: 'Select country',
                width: '100%',
                theme: 'classic',
                ajax: {
                    url: "{{ route('country-list') }}",
                    type: "POST",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            searchQuery: params.term,
                            page: params.page || 1,
                            _token: "{{ csrf_token() }}"
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: $.map(data.items, function(item) {
                                return {
                                    id: item.id,
                                    text: item.text
                                };
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                }
            });

        $('#section-b-2-state_id').select2({
            allowClear: true,
            placeholder: 'Select state',
            theme: 'classic',
            width: '100%',
            ajax: {
                url: "{{ route('city-list') }}",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        searchQuery: params.term,
                        page: params.page || 1,
                        state_id: $('#country_id').val(),
                        _token: "{{ csrf_token() }}"
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: $.map(data.items, function(item) {
                            return {
                                id: item.id,
                                text: item.text
                            };
                        }),
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            }
        });

        $('#section-b-2-city_id').select2({
            allowClear: true,
            placeholder: 'Select city',
            theme: 'classic',
            width: '100%',
            ajax: {
                url: "{{ route('city-list') }}",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        searchQuery: params.term,
                        page: params.page || 1,
                        state_id: $('#state_id').val(),
                        _token: "{{ csrf_token() }}"
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: $.map(data.items, function(item) {
                            return {
                                id: item.id,
                                text: item.text
                            };
                        }),
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            }
        });

        $(document).on('click', '.section-b-2-add-tax', function() {
            var row = $(this).closest('.section-b-2-tax-residence-row').clone();
            row.find('select').val('').trigger('change');
            $('#section-b-2-tax-residence-wrapper').append(row);
        });

        $(document).on('click', '.section-b-2-remove-tax', function() {
            if ($('.section-b-2-tax-residence-row').length > 1) {
                $(this).closest('.section-b-2-tax-residence-row').remove();
            }
        });



    });
</script>
{{-- Section E 1 --}}
<script>
    $(document).ready(function() {
        $('#section-e-1-type').select2({
            allowClear: true,
            placeholder: 'Select Type',
            theme: 'classic',
            width: '100%'
        }).on('change', function() {
            let selectedValue = $('option:selected', this).val();

            $('.all-cbox').addClass('d-none');
            if (selectedValue) {
                $(`#container-for-ommit-documents-e1-${selectedValue}`).removeClass('d-none');
            }
        });
    });
</script>
{{-- Section F 1 --}}
<script>
    $(document).ready(function() {
        $('#s-f-1-purpose').select2({
            placeholder: 'Select Purpose',
            theme: 'classic',
            width: '100%'
        });

        $('#sg2-date').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('#sg1date').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('#f3d1').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('#f3d2').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('#f3d3').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        function initExpiryPicker($input){
            $input.datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                minDate: +1,
                yearRange: '-0:+50'
            });
        }

        initExpiryPicker($('.expiry-date-input'));

        $(document).on('change', '.has-expiry-checkbox', function(){
            const checked = $(this).is(':checked');
            const $container = $(this).closest('.col-4, .col-md-4, .col-sm-4');
            const $input = $container.find('.expiry-date-input').first();
            if (checked) {
                $input.removeClass('d-none');
                if (!$input.data('datepicker')) initExpiryPicker($input);
            } else {
                $input.addClass('d-none').val('');
            }
        });

        let currentUpload = { recordId: '', docId: '', dtType: '' };

        $(document).on('click', '.open-upload-modal', function(){
            const btn = $(this);
            currentUpload.recordId = btn.data('record-id') || '';
            currentUpload.docId = btn.data('doc-id');
            currentUpload.dtType = btn.data('dt-type');

            const hasExpiry = parseInt(btn.data('has-expiry')) === 1;
            const expiry = btn.data('expiry') || '';

            $('#kyc-modal-file').val('');
            $('#kyc-modal-has-expiry').prop('checked', hasExpiry);
            const $exp = $('#kyc-modal-expiry');
            $exp.val(expiry);
            if (!$exp.data('datepicker')) initExpiryPicker($exp);
            if (hasExpiry) { $exp.removeClass('d-none'); } else { $exp.addClass('d-none').val(''); }

            new bootstrap.Modal('#kycUploadModal').show();
        });

        $(document).on('change', '#kyc-modal-has-expiry', function(){
            if ($(this).is(':checked')) {
                $('#kyc-modal-expiry').removeClass('d-none');
            } else {
                $('#kyc-modal-expiry').addClass('d-none').val('');
            }
        });

        $(document).on('click', '#kyc-modal-upload-btn', function(){
            const fileEl = $('#kyc-modal-file')[0];
            const file = fileEl && fileEl.files && fileEl.files[0];
            if (!file) { Swal.fire('Error', 'Please select a file', 'error'); return; }

            if (file.size > 10 * 1024 * 1024) {
                Swal.fire('Error', 'File size must be less than 10 MB', 'error');
                return;
            }
            
            let formData = new FormData();
            formData.append('file', file);
            formData.append('doc_id', currentUpload.docId);
            formData.append('policy_id', currentCaseId);
            formData.append('dt_type', currentUpload.dtType);
            if (currentUpload.recordId) formData.append('record_id', currentUpload.recordId);
            const modalHas = $('#kyc-modal-has-expiry').is(':checked');
            formData.append('has_expiry_date', modalHas ? 1 : 0);
            if (modalHas) {
                formData.append('expiry_date', $('#kyc-modal-expiry').val() || '');
            }
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            
            $.ajax({
                url: "{{ route('upload-document') }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({ title: 'Uploading...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                },
                success: function(response) {
                    Swal.close();
                    if (response.status === 'success') {
                        bootstrap.Modal.getInstance(document.getElementById('kycUploadModal')).hide();
                        Swal.fire('Success', 'File uploaded successfully', 'success');
                        const vid = currentUpload.recordId ? `#view\\[${currentUpload.recordId}\\]\\[${currentUpload.docId}\\]` : `#view\\[${currentUpload.docId}\\]`;
                        $(vid).removeClass('d-none').attr('href', response.url).attr('target', '_blank');
                        const chk = currentUpload.recordId ? `#doc-${currentUpload.recordId}-${currentUpload.docId}` : `#doc-${currentUpload.docId}`;
                        $(chk).attr('checked', true);
                    } else {
                        Swal.fire('Error', response.message || 'Something went wrong', 'error');
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire('Error', 'Server error while uploading file', 'error');
                }
            });
        });

        // Toggle COI other textbox on selection
        $(document).on('change', '.coi-fee-option-select', function() {
            var $select = $(this);
            var $other = $select.closest('td').find('.coi-fee-option-other');
            if ($select.val() === 'Other (specify)') {
                $other.removeClass('d-none');
            } else {
                $other.addClass('d-none');
                $other.val('');
            }
        });

        $('#dob').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('.secction-b-2-date-birth').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('.section-f-7-date-1').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('.section-f-7-date-2').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('.section-f-7-date-3').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('#c1_date_of_birth').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('#d-1-dob').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('#policy_holder_id').select2({
            placeholder: 'Select Policyholder',
            allowClear: true,
            theme: 'classic',
            width: '100%',
            ajax: {
                url: "{{ route('holder-list') }}",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        policy_id: "{{ $policy->id ?? '' }}",
                        searchQuery: params.term,
                        page: params.page || 1,
                        _token: "{{ csrf_token() }}",
                        roles: ['policy-holder'],
                        addNewOption: 1,
                        includeUserData: true
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;

                    return {
                        results: $.map(data.items, function(item) {
                            return {
                                id: item.id,
                                text: item.text,
                                user: item.user
                            };
                        }),
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            },
            templateResult: function(data) {
                if (data.loading) {
                    return data.text;
                }

                var $result = $('<span></span>');
                $result.text(data.text);
                return $result;
            }
        }).on('change', function() {
            var selectedData = $('#policy_holder_id').select2('data');
            if (selectedData.length > 0) {
                var userData = selectedData[0].user;
                var shouldEmpty = false;
                
                if (userData) {
                    $('#stts-entity').attr('checked', userData.type === 'entity');
                    $('#stts-individual').attr('checked', userData.type === 'individual');

                    $('.section-b-1-policyholder-name').val(userData.name || '');
                    $('.section-b-1-controlling-person').val(userData.name || '');
                    $('.section-b-1-place-birth').val(userData.place_of_birth || '');
                    $('.section-b-1-date-birth').val(userData.dob || '');
                    $('.section-b-1-country').val(userData.country || '')
                    $('.section-b-1-city').val(userData.city || '');
                    $('.section-b-1-zip').val(userData.zipcode || '');
                    $('.section-b-1-address').val(userData.address_line_1 || '');

                    if (userData.status === 'other') {
                        $('.section-b-1-other-status').val(userData.other_status || '');
                        $('.section-b-1-other-status').removeClass('d-none');
                    } else {
                        $('.section-b-1-other-status').addClass('d-none');
                        $('.section-b-1-other-status').val('');
                        $(`#stts-${userData.status}`).attr('checked', true);
                    }

                    $('#stts-male').attr('checked', userData.gender === 'male');
                    $('#stts-female').attr('checked', userData.gender === 'female');    

                    $('.section-b-1-nationality').val(userData.national_country_of_registration);
                    $('.section-b-1-legal-residence').val(userData.country_of_legal_residence);
                    $('.section-b-1-passport').val(userData.passport_number);
                    $('.section-b-1-passport-issue-country').val(userData.country_of_issuance);
                    $('.section-b-1-tin').val(userData.tin);
                    $('.section-b-1-lei').val(userData.lei);
                    $('.section-b-1-email').val(userData.email);
                } else {
                    shouldEmpty = true;
                }

            } else {
                shouldEmpty = true;
            }

            if (shouldEmpty) {
                $('#stts-entity').prop('checked', true);

                $('.section-b-1-policyholder-name').val('');
                $('.section-b-1-controlling-person').val('');
                $('.section-b-1-place-birth').val('');
                $('.section-b-1-date-birth').val('');
                $('.section-b-1-country').val('');
                $('.section-b-1-city').val('');
                $('.section-b-1-zip').val('');
                $('.section-b-1-address').val('');

                $(`#stts-single`).attr('checked', true);
                $('.section-b-1-other-status').addClass('d-none').val('');

                $('#stts-male').attr('checked', true);

                $('.section-b-1-nationality').val(null);
                $('.section-b-1-legal-residence').val(null);
                $('.section-b-1-passport').val(null);
                $('.section-b-1-passport-issue-country').val(null);
                $('.section-b-1-tin').val(null);
                $('.section-b-1-lei').val(null);
                $('.section-b-1-email').val(null);
            }
        });

        $(document).on('click', '.section-b-1-entity-status input[type="radio"]', function() {
            if ($(this).val() === 'other') {
                $('.section-b-1-entity-status-other').removeClass('d-none');
            } else {
                $('.section-b-1-entity-status-other').addClass('d-none').val('');
            }
        });

        $(document).on('change', '.doc-upl', function() {
            let fileInput = $(this);
            let file = fileInput[0].files[0];

            let nameMatch = fileInput.attr('name').match(/file\[(\d+)\]\[(\d+)\]/);
            let recordId = nameMatch ? nameMatch[1] : '';
            let docId = nameMatch ? nameMatch[2] : fileInput.attr('name').match(/\d+/)[0];
            let viewBtn = $('#view\\[' + recordId + '\\]\\[' + docId + '\\]');
            let chckBox = $('#doc-' + recordId + '-' + docId);
            let dtType = fileInput.data('type');
            
            if (!file) return;

            if (file.size > 10 * 1024 * 1024) {
                Swal.fire('Error', 'File size must be less than 10 MB', 'error');
                fileInput.val('');
                return;
            }

            let formData = new FormData();
            formData.append('file', file);
            formData.append('doc_id', docId);
            formData.append('policy_id', currentCaseId);
            formData.append('dt_type', dtType);
            formData.append('record_id', recordId);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: "{{ route('upload-document') }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Uploading...',
                        text: 'Please wait while the file is uploading',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.status === 'success') {
                        Swal.fire('Success', 'File uploaded successfully', 'success');
                        viewBtn.removeClass('d-none').attr('href', response.url).attr(
                            'target', '_blank');

                        if (chckBox) {
                            chckBox.attr('checked', true)
                        }
                    } else {
                        Swal.fire('Error', response.message || 'Something went wrong',
                            'error');
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire('Error', 'Server error while uploading file', 'error');
                }
            });
        });


    });
</script>
<script>
$(document).ready(function () {
    $('.policy-dropdown-toggle').on('click', function (e) {
        $('.policy-dropdown-toggle').removeClass('active');
        $(this).addClass('active');
        
        e.preventDefault();
        e.stopPropagation();

        let $parentLi = $(this).closest('.child-dropdown');
        let $submenu = $parentLi.find('> .policy-dropdown-menu');

        $('.child-dropdown').not($parentLi).find('> .policy-dropdown-menu').slideUp();

        $submenu.stop(true, true).slideToggle();
    });

    // $('.each-options').on('click', function (e) {
    //     e.preventDefault();
    //     let section = $(this).data('section');

    //     $('.each-options').removeClass('active');
    //     $(this).addClass('active');
    // });
});
</script>
<script>
let currentEditId = null;
let introProfileEditId = null;

$(document).ready(function() {
    $('#save-add-new').click(function() {
        saveInsuredLife('add_new');
    });

    $(document).on('click', '.insured-life-link', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        $('.case-section').addClass('d-none');
        $('#section-c-1').removeClass('d-none');
        
        $('.each-options').removeClass('active');
        $('.policy-dropdown-item[data-section="section-c-1"]').addClass('active');
        
        // $(this).closest('.policy-dropdown-submenu').slideUp();
    });

    loadInsuredLivesSidebar();

});


function saveIntroducerProfile(action) {
    const form = $('#form-section-a-1')[0];
    
    const formData = new FormData(form);
    formData.append('policy', "{{ $policy->id ?? '' }}");
    formData.append('section', 'section-a-1');
    formData.append('save', action === 'next' ? 'next' : 'draft');
    formData.append('_token', '{{ csrf_token() }}');
    
    if (introProfileEditId) {
        formData.append('id', introProfileEditId);
    }
        
    $.ajax({
        url: '{{ route("case.submission") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.next_section) {
                if (action === 'add_new') {
                    resetIntroForm();
                    loadIntroducersSidebar();
                }
            } else {
                loadIntroducersSidebar();
            }
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                Object.keys(xhr.responseJSON.errors).forEach(key => {
                    const field = $(`[name="${key}"]`);
                    field.addClass('is-invalid');
                    field.siblings('.invalid-feedback').remove();
                    field.after(`<div class="invalid-feedback">${xhr.responseJSON.errors[key][0]}</div>`);
                });
            }
        }
    });
}

function loadInsuredLives() {
    const policyId = "{{ $policy->id ?? '' }}";
    if (!policyId) return;
    
    $.ajax({
        url: '{{ route("case.getInsuredLives") }}',
        method: 'POST',
        data: {
            policy_id: policyId,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            $('#insured-life-accordion').html(response.html);
        }
    });

    loadInsuredLivesSidebar();
}

function loadInsuredLivesSidebar() {
    const policyId = "{{ $policy->id ?? '' }}";
    if (!policyId) return;
    
    $.ajax({
        url: '{{ route("case.getInsuredLivesSidebar") }}',
        method: 'POST',
        data: {
            policy_id: policyId,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            $('.insured-lives-submenu').html(response.html);
            
            if (response.html && response.html.trim() !== '') {
                // $('.policy-dropdown-toggle[data-section="insured"]').parent().find('.policy-dropdown-submenu').show();
            }
        }
    });
}

function loadPolicyHoldersSidebar() {
    const policyId = "{{ $policy->id ?? '' }}";
    if (!policyId) return;
    
    $.ajax({
        url: '{{ route("case.getPolicyHoldersSidebar") }}',
        method: 'POST',
        data: {
            policy_id: policyId,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            $('.policyholders-submenu').html(response.html);
        }
    });
}

loadPolicyHoldersSidebar()

// function editPolicyHolderFromSidebar(id) {
//     const policyId = "{{ $policy->id ?? '' }}";
//     if (!policyId || !id) return;

//     $.ajax({
//         url: '{{ route("case.getPolicyHolder") }}',
//         method: 'POST',
//         data: {
//             policy_id: policyId,
//             policy_holder_id: id,
//             _token: '{{ csrf_token() }}'
//         },
//         success: function(res) {
//             if (res && res.data) {
//                 $('#b-1-edit-id').val(res.data.id || '');
//                 $('.section-b-1-type[value="'+(res.data.type || 'Individual')+'"]').prop('checked', true).trigger('change');
//                 $('.section-b-1-policyholder-name').val(res.data.name || '');
//                 $('.section-b-1-place-birth').val(res.data.place_of_birth || '');
//                 $('.section-b-1-date-birth').val(res.data.dob || '');
//                 $('.section-b-1-country').val(res.data.country || '').trigger('change');
//                 $('.section-b-1-city').val(res.data.city || '');
//                 $('.section-b-1-zip').val(res.data.zipcode || '');
//                 $('.section-b-1-address').val(res.data.address_line_1 || '');
//                 $('.section-b-1-status[value="'+(res.data.status || 'single')+'"]').prop('checked', true);
//                 $('.section-b-1-nationality').val(res.data.national_country_of_registration || '').trigger('change');
//                 $('.section-b-1-gender[value="'+(res.data.gender || 'male')+'"]').prop('checked', true);
//                 $('.section-b-1-legal-residence').val(res.data.country_of_legal_residence || '').trigger('change');

//                 const passports = Array.isArray(res.data.passport_number) ? res.data.passport_number : (res.data.passport_number ? String(res.data.passport_number).split(',') : []);
//                 const issuances = Array.isArray(res.data.country_of_issuance) ? res.data.country_of_issuance : (res.data.country_of_issuance ? String(res.data.country_of_issuance).split(',') : []);
//                 let rowsHtml = '';
//                 if (passports.length === 0) {
//                     rowsHtml = `
//                         <div class="row align-items-end section-b-1-passport-row">
//                             <div class="col-sm-5 mb-2">
//                                 <input type="text" class="form-control section-b-1-passport" name="passport_number[]" placeholder="Passport Number" value="">
//                             </div>
//                             <div class="col-sm-5 mb-2">
//                                 <select class="form-control section-b-1-passport-issue-country" name="country_of_issuance[]">
//                                     <option value=""></option>
//                                     ${countryHtml}
//                                 </select>
//                             </div>
//                             <div class="col-sm-2 mb-2">
//                                 <button type="button" class="btn btn-success section-b-1-passport-add">+</button>
//                                 <button type="button" class="btn btn-danger section-b-1-passport-remove">-</button>
//                             </div>
//                         </div>`;
//                 } else {
//                     passports.forEach(function(p, idx) {
//                         const selected = issuances[idx] || '';
//                         rowsHtml += `
//                             <div class="row align-items-end section-b-1-passport-row">
//                                 <div class="col-sm-5 mb-2">
//                                     <input type="text" class="form-control section-b-1-passport" name="passport_number[]" placeholder="Passport Number" value="${p || ''}">
//                                 </div>
//                                 <div class="col-sm-5 mb-2">
//                                     <select class="form-control section-b-1-passport-issue-country" name="country_of_issuance[]">
//                                         <option value=""></option>
//                                         ${countryHtml}
//                                     </select>
//                                 </div>
//                                 <div class="col-sm-2 mb-2">
//                                     <button type="button" class="btn btn-success section-b-1-passport-add">+</button>
//                                     <button type="button" class="btn btn-danger section-b-1-passport-remove">-</button>
//                                 </div>
//                             </div>`;
//                     });
//                 }

//                 $('.section-b-1-passport-row').parent().html(rowsHtml);

//                 $('.section-b-1-passport-issue-country').each(function(i, el){
//                     const v = issuances[i] || '';
//                     if (v) $(el).val(v).trigger('change');
//                 });

//                 $('.section-b-1-tin').val(res.data.tin || '');
//                 $('.section-b-1-lei').val(res.data.lei || '');
//                 $('.section-b-1-email').val(res.data.email || '');

//                 if (res.tax_html && res.tax_html.length) {
//                     const options = res.tax_html.map(o => `<div class="row mt-2"><div class="col-sm-7"><select class="form-control section-b-1-country-tax-residence" name="all_countries[]">${o}</select></div><div class="col-sm-5"><button type="button" class="btn btn-success section-b-1-add">+</button> <button type="button" class="btn btn-danger section-b-1-remove">-</button></div></div>`).join('');
//                     $('.section-b-1-country-tax-residence-row').html(`
//                         <label class="col-sm-12 col-form-label">Countries of Tax Residence:  </label>
//                         <div class="col-sm-12">${options}</div>
//                     `);
//                 }

//                 window.currentPolicyHolderEditId = id;

//                 $('.policy-dropdown-item[data-section="section-b-1"]').addClass('active');
//                 $('#section-b-1').removeClass('d-none');
//             }
//         }
//     });
// }

// function deletePolicyHolderFromSidebar(id) {
//     const policyId = "{{ $policy->id ?? '' }}";
//     if (!policyId || !id) return;

//     Swal.fire({
//         title: 'Are you sure?',
//         text: 'This will delete the policyholder record.',
//         icon: 'warning',
//         showCancelButton: true,
//         confirmButtonText: 'Yes, delete it!',
//         cancelButtonText: 'Cancel'
//     }).then((result) => {
//         if (result.isConfirmed) {
//             $.ajax({
//                 url: '{{ route("case.deletePolicyHolder") }}',
//                 method: 'POST',
//                 data: {
//                     policy_id: policyId,
//                     policy_holder_id: id,
//                     _token: '{{ csrf_token() }}'
//                 },
//                 success: function() {
//                     loadPolicyHoldersSidebar();
//                 }
//             });
//         }
//     });
// }
function saveInsuredLife(action) {
    const form = $('#form-section-c-1')[0];
    
    const formData = new FormData();
    formData.append('policy', "{{ $policy->id ?? '' }}");
    formData.append('section', 'section-c-1');
    formData.append('save', action === 'next' ? 'next' : 'draft');
    formData.append('_token', '{{ csrf_token() }}');
    
    const data = {
        controlling_person_name: $('#c1_controlling_person_name').val(),
        place_of_birth: $('#c1_place_of_birth').val(),
        date_of_birth: $('#c1_date_of_birth').val(),
        address: $('#c1address').val(),
        country: $('#c1country').val(),
        city: $('#c1city').val(),
        zip: $('#c1zip').val(),
        status: $('.c1sts:checked').val(),
        smoker_status: $('.c1-smsts:checked').val(),
        nationality: $('#c1nationality').val(),
        gender: $('.c1-gndr:checked').val(),
        country_of_legal_residence: $('#c1country_of_legal_residence').val(),
        passport_number: $('#section-c-1 .section-c-1-passport-row input[name="passport_number[]"]').map(function(){ return $(this).val(); }).get(),
        country_of_issuance: $('#section-c-1 .section-c-1-passport-row select[name="country_of_issuance[]"]').map(function(){ return $(this).val(); }).get(),
        relationship_to_policyholder: $('#c1relationship_to_policyholder').val(),
        email: $('#c1email').val(),
        all_countries: $('#section-c-1 [name="all_countries[]"]').map(function() {
            return $(this).val();
        }).get()
    };
    
    if (currentEditId) {
        data.id = currentEditId;
    }
    
    formData.append('data', JSON.stringify(data));
        
    $.ajax({
        url: '{{ route("case.submission") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.next_section) {
                if (action === 'add_new') {
                    resetForm();
                    loadInsuredLivesSidebar();
                    // loadInsuredLives();
                }
            } else {
                // loadInsuredLives();
                loadInsuredLivesSidebar();
            }
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                Object.keys(xhr.responseJSON.errors).forEach(key => {
                    const field = $(`[name="${key}"]`);
                    field.addClass('is-invalid');
                    field.siblings('.invalid-feedback').remove();
                    field.after(`<div class="invalid-feedback">${xhr.responseJSON.errors[key][0]}</div>`);
                });
            }
        }
    });
}

function resetForm() {
    $('#form-section-c-1')[0].reset();
    $('#c1country').val(null).trigger('change');
    $('#c1country_of_legal_residence').val(null).trigger('change');
    // reset passports block
    $('.section-c-1-passport-row').parent().html(`
        <div class="row align-items-end section-c-1-passport-row">
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
        </div>
    `);
    currentEditId = null;
    $('#tempc1taxbox').html(`
        <label for="c1countries_of_tax_residence" class="form-label">
            Countries of Tax Residence 
        </label>
            <div class="row section-c-1-country-tax-residence-row mb-3  mt-3">
                <div class="col-sm-10">
                <select class="form-control section-c-1-country-tax-residence" name="all_countries[]">
                    <option value=""></option>
                    ${countryHtml}
                </select>
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-success section-c-1-add">+</button>
                    <button type="button" class="btn btn-danger section-c-1-remove">-</button>
                </div>
            </div>
    `);    
    // $('#save-add-new').text('Save & Add New');

    $('.section-c-1-country-tax-residence-row:last .section-c-1-country-tax-residence').select2({
        theme: 'classic',
        width: '100%',
        placeholder: 'Select Country'
    });
}

function resetIntroForm() {
    $('#form-section-a-1')[0].reset();
    introProfileEditId = null;

    $('#s1ss1contper').html(`
        <div class="row">
            <div class="col-md-2">
                <input type="text" class="form-control contact_person_first_name" name="contact_person_first_name[0]" placeholder="First Name">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control contact_person_middle_name" name="contact_person_middle_name[0]" placeholder="Middle Name" value="">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control contact_person_last_name" name="contact_person_last_name[0]" placeholder="Last Name" value="">
            </div>
            <div class="col-md-2">
                <input type="email" class="form-control contact_person_email" name="contact_person_email[0]" placeholder="Email" value="">
            </div>
            <div class="col-md-3">
                <input type="hidden" class="contact_person_phone_number_dial_code" id="contact_person_phone_number_dial_code_0" name="contact_person_phone_number_dial_code[0]" value="">
                <input type="tel" class="form-control contact_person_phone_number" name="contact_person_phone_number[0]" id="contact_person_phone_number" value="">
            </div>
            <div class="col-md-1 row">
                <button type="button" class="btn btn-success btn-sm s1ss1contperadd"> + </button>
                <button type="button" class="btn btn-danger btn-sm s1ss1contperrem"> - </button>
            </div>
        </div>
    `);
}

function editInsuredLife(id) {
    const policyId = "{{ $policy->id ?? '' }}";
    
    $.ajax({
        url: '{{ route("case.getInsuredLife") }}',
        method: 'POST',
        data: {
            policy_id: policyId,
            insured_life_id: id,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.data) {
                const data = response.data;
                currentEditId = data.id;
                
                $('#c1_place_of_birth').val(data.place_of_birth);
                $('#c1_date_of_birth').val(data.date_of_birth);
                $('#c1address').val(data.address);
                $('#c1country').val(data.country).trigger('change');
                $('#c1city').val(data.city);
                $('#c1zip').val(data.zip);
                $(`input.c1sts[value="${data.status}"]`).prop('checked', true);
                $(`input.c1-smsts[value="${data.smoker_status}"]`).prop('checked', true);
                $('#c1nationality').val(data.nationality);
                $(`input.c1-gndr[value="${data.gender}"]`).prop('checked', true);
                $('#c1country_of_legal_residence').val(data.country_of_legal_residence).trigger('change');
                // populate passports
                const passports = Array.isArray(data.passport_number) ? data.passport_number : (data.passport_number ? String(data.passport_number).split(',') : []);
                const issuances = Array.isArray(data.country_of_issuance) ? data.country_of_issuance : (data.country_of_issuance ? String(data.country_of_issuance).split(',') : []);
                let rowsHtml = '';
                if (passports.length === 0) {
                    rowsHtml = `
                        <div class="row align-items-end section-c-1-passport-row">
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
                } else {
                    passports.forEach(function(p, idx) {
                        rowsHtml += `
                            <div class="row align-items-end section-c-1-passport-row ${idx>0 ? 'mt-2' : ''}">
                                <div class="col-sm-5 mb-2">
                                    <input type="text" class="form-control c1-passport" name="passport_number[]" placeholder="Passport Number" value="${p || ''}">
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
                    });
                }
                $('.section-c-1-passport-row').parent().html(rowsHtml);
                $('.c1-issuance-country').each(function(i, el){
                    const v = issuances[i] || '';
                    if (v) $(el).val(v).trigger('change');
                });
                $('#c1relationship_to_policyholder').val(data.relationship_to_policyholder);
                $('#c1email').val(data.email);
                                
                // $('#save-add-new').text('Update & Add New');
                
                if (response.tax && response.tax.length > 0) {
                    $('#tempc1taxbox').empty();

                    $('#tempc1taxbox').append(`
                        <label for="c1countries_of_tax_residence" class="form-label">
                            Countries of Tax Residence 
                        </label>
                    `);

                    response.tax_html.forEach(function(tax) {
                        $('#tempc1taxbox').append(`
                            <div class="row section-c-1-country-tax-residence-row mb-3 mt-3">
                                <div class="col-sm-10">
                                    <select class="form-control section-c-1-country-tax-residence" name="all_countries[]">
                                        <option value=""></option>
                                        ${tax}
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <button type="button" class="btn btn-success section-c-1-add">+</button>
                                    <button type="button" class="btn btn-danger section-c-1-remove">-</button>
                                </div>
                            </div>
                        `);

                        $('.section-c-1-country-tax-residence-row:last .section-c-1-country-tax-residence').select2({
                            theme: 'classic',
                            width: '100%',
                            placeholder: 'Select Country'
                        });
                    });

                } else {
                    $('#tempc1taxbox').html(`
                        <label for="c1countries_of_tax_residence" class="form-label">
                            Countries of Tax Residence 
                        </label>
                            <div class="row section-c-1-country-tax-residence-row mb-2 mt-3">
                                <div class="col-sm-10">
                                    <select class="form-control section-c-1-country-tax-residence" name="all_countries[]">
                                        <option value=""></option>
                                        ${countryHtml}
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <button type="button" class="btn btn-success section-c-1-add">+</button>
                                    <button type="button" class="btn btn-danger section-c-1-remove">-</button>
                                </div>
                            </div>
                    `);

                        $('.section-c-1-country-tax-residence-row:last .section-c-1-country-tax-residence').select2({
                            theme: 'classic',
                            width: '100%',
                            placeholder: 'Select Country'
                        });
                }


                $('html, body').animate({
                    scrollTop: $('#form-section-c-1').offset().top - 100
                }, 500);
            }
        }
    });
}

function deleteInsuredLife(id) {
    const policyId = "{{ $policy->id ?? '' }}";
    
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to delete this insured life record?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, keep it',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("case.deleteInsuredLife") }}',
                method: 'POST',
                data: {
                    policy_id: policyId,
                    insured_life_id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status) {
                        Swal.fire('Deleted!', 'Insured life record has been deleted.', 'success');

                        if ($(`#ins-life-reco-${id}`).length) {
                            $(`#ins-life-reco-${id}`).remove();
                        }
                        
                        loadInsuredLivesSidebar();
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                }
            });
        }
    });
}

// function editInsuredLifeFromSidebar(id) {
//     $('.case-section').addClass('d-none');
//     $('#section-c-1').removeClass('d-none');
    
//     $('.each-options').removeClass('active');
//     $('.policy-dropdown-item[data-section="section-c-1"]').addClass('active');
    
//     editInsuredLife(id);
// }

// function deleteInsuredLifeFromSidebar(id) {
//     deleteInsuredLife(id);
// }
</script>
<script>

$(document).ready(function() {
	// refreshBeneficiariesAccordion();
	$('.d-1-save-add-new').on('click', function() { 
        saveBeneficiary('save-and-add'); 
    });

	$('#d-1-insured-life').select2({
		allowClear: true,
		placeholder: 'Select policyholder',
		theme: 'classic',
		width: '100%',
		ajax: {
			url: "{{ route('insured-list') }}",
			type: "POST",
			dataType: 'json',
			delay: 250,
			data: function(params) {
				return {
					searchQuery: params.term,
					page: params.page || 1,
					policy_id: "{{ $policy->id }}",
					_token: "{{ csrf_token() }}"
				};
			},
			processResults: function(data, params) {
				params.page = params.page || 1;
				return {
					results: $.map(data.items, function(item) {
						return {
							id: item.id,
							text: item.text
						};
					}),
					pagination: {
						more: data.pagination.more
					}
				};
			},
			cache: true
		}
	});


    $(document).on('click', '.beneficiary-link', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        $('.case-section').addClass('d-none');
        $('#section-d-1').removeClass('d-none');
        
        $('.each-options').removeClass('active');
        $('.policy-dropdown-item[data-section="section-d-1"]').addClass('active');
        
        // $(this).closest('.policy-dropdown-submenu').slideUp();
    });

    loadBeneficiariesSidebar();
});

function resetD1Form() {
	$('#d-1-edit-id').val('');
	$('#d-1-insured-life').val(null).trigger('change');
	$('#d-1-country').val(null).trigger('change');
	$('#d-1-legal-residence').val(null).trigger('change');

    $('.section-d-1-passport-row').parent().html(`
        <div class="row align-items-end section-d-1-passport-row">
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
        </div>
    `);
	$('#form-section-d-1')[0].reset();
    $('#tempd1taxbox').html(`
        <label for="d1countries_of_tax_residence" class="form-label">
            Countries of Tax Residence 
        </label>
            <div class="row section-d-1-country-tax-residence-row">
                <div class="col-sm-9">
                    <select class="form-control section-d-1-country-tax-residence" name="all_countries[]">
                        <option value=""></option>
                        ${countryHtml}
                    </select>
                </div>
                <div class="col-sm-3">
                    <button type="button" class="btn btn-success section-d-1-add">+</button>
                    <button type="button" class="btn btn-danger section-d-1-remove">-</button>
                </div>
            </div>
    `);    

    $('.section-d-1-country-tax-residence-row:last .section-d-1-country-tax-residence').select2({
        theme: 'classic',
        width: '100%',
        placeholder: 'Select Country'
    });
}

function buildD1Payload() {
	return {
		insured_life_id: $('#d-1-insured-life').val(),
		name: $('#d-1-name').val(),
		place_of_birth: $('#d-1-place-of-birth').val(),
		date_of_birth: $('#d-1-dob').val(),
		address: $('#d-1-address').val(),
		country: $('#d-1-country').val(),
		city: $('#d-1-city').val(),
		zip: $('#d-1-zip').val(),
		status: $('input[name="d-1-status"]:checked').val(),
		smoker_status: $('input[name="d-1-smoker"]:checked').val(),
		nationality: $('#d-1-nationality').val(),
		gender: $('input[name="d-1-gender"]:checked').val(),
		country_of_legal_residence: $('#d-1-legal-residence').val(),
        passport_number: $('#section-d-1 .section-d-1-passport-row input[name="passport_number[]"]').map(function(){ return $(this).val(); }).get(),
        country_of_issuance: $('#section-d-1 .section-d-1-passport-row select[name="country_of_issuance[]"]').map(function(){ return $(this).val(); }).get(),
		relationship_to_policyholder: $('#d-1-relationship').val(),
		email: $('#d-1-email').val(),
        dial_code: $('#d-1-dial_code').val(),
        phone_number: $('#d-1-phone_number').val(),
		beneficiary_death_benefit_allocation: $('#d-1-allocation').val(),
		designation_of_beneficiary: $('input[name="d-1-designation"]:checked').val(),
		id: $('#d-1-edit-id').val() || undefined,
        all_countries: $('#section-d-1 [name="all_countries[]"]').map(function() {
            return $(this).val();
        }).get()
	};
}

function saveBeneficiary(saveType) {
	const formEl = document.getElementById('form-section-d-1');

	$.ajax({
		url: "{{ route('case.submission') }}",
		method: 'POST',
		data: {
			policy: {{ $policy->id ?? 'null' }},
			section: 'section-d-1',
			data: buildD1Payload(),
			save: saveType
		},
		headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
		success: function(resp) {
			if (resp.type === 'save-and-add') { resetD1Form(); loadBeneficiariesSidebar(); }
			else if (resp.type === 'save') {
				// refreshBeneficiariesAccordion();
				$('.policy-dropdown-item[data-section="section-d-1"]').removeClass('active').parent().parent().hide();
				$('.policy-dropdown-item[data-section="section-e-1"]').addClass('active').parent().parent().show();
				$('#section-d-1').addClass('d-none');
				$('#section-e-1').removeClass('d-none');
				$('#d-1-insured-life').val(null).trigger('change');
                loadBeneficiariesSidebar();
			}
		},
		error: function() {}
	});
}

function refreshBeneficiariesAccordion() {
	$.ajax({
		url: "{{ route('case.getBeneficiaries') }}",
		method: 'POST',
		data: { policy_id: {{ $policy->id ?? 'null' }}, _token: '{{ csrf_token() }}' },
		success: function(res) { $('#d-1-beneficiaries-accordion').html(res.html); }
	});
}

function loadBeneficiariesSidebar() {
    const policyId = "{{ $policy->id ?? '' }}";
    if (!policyId) return;
    
    $.ajax({
        url: '{{ route("case.getBeneficiariesSidebar") }}',
        method: 'POST',
        data: {
            policy_id: policyId,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            $('.beneficiaries-submenu').html(response.html);
            
            if (response.html && response.html.trim() !== '') {

            }
        }
    });
}

// function editBeneficiaryFromSidebar(id) {
//     $('.case-section').addClass('d-none');
//     $('#section-d-1').removeClass('d-none');
    
//     $('.each-options').removeClass('active');
//     $('.policy-dropdown-item[data-section="section-d-1"]').addClass('active');
    
//     d1EditBeneficiary(id);
// }

// function deleteBeneficiaryFromSidebar(id) {
//     d1DeleteBeneficiary(id);
// }

function d1EditBeneficiary(id) {
	$.ajax({
		url: "{{ route('case.getBeneficiary') }}",
		method: 'POST',
		data: { policy_id: {{ $policy->id ?? 'null' }}, beneficiary_id: id, _token: '{{ csrf_token() }}' },
		success: function(res) {
			if (res.data) {
				$('#d-1-edit-id').val(res.data.id);

                if (res.data.insured) {
                    let newOption = new Option(res.data.insured.name, res.data.insured.id, true, true);
                    $('#d-1-insured-life').append(newOption).trigger('change');
                }
                
				$('#d-1-name').val(res.data.name);
				$('#d-1-place-of-birth').val(res.data.place_of_birth);
				$('#d-1-dob').val(res.data.date_of_birth);
				$('#d-1-address').val(res.data.address);
				$('#d-1-country').val(res.data.country).trigger('change');
				$('#d-1-city').val(res.data.city);
				$('#d-1-zip').val(res.data.zip);
				$(`input[name="d-1-status"][value="${res.data.status}"]`).prop('checked', true);
				$(`input[name="d-1-smoker"][value="${res.data.smoker_status}"]`).prop('checked', true);
				$('#d-1-nationality').val(res.data.nationality);
				$(`input[name="d-1-gender"][value="${res.data.gender}"]`).prop('checked', true);
				$('#d-1-legal-residence').val(res.data.country_of_legal_residence).trigger('change');
                const passports = Array.isArray(res.data.passport_number) ? res.data.passport_number : (res.data.passport_number ? String(res.data.passport_number).split(',') : []);
                const issuances = Array.isArray(res.data.country_of_issuance) ? res.data.country_of_issuance : (res.data.country_of_issuance ? String(res.data.country_of_issuance).split(',') : []);
                let rowsHtml = '';
                if (passports.length === 0) {
                    rowsHtml = `
                        <div class="row align-items-end section-d-1-passport-row">
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
                } else {
                    passports.forEach(function(p, idx) {
                        rowsHtml += `
                            <div class="row align-items-end section-d-1-passport-row ${idx>0 ? 'mt-2' : ''}">
                                <div class="col-sm-5 mb-2">
                                    <input type="text" class="form-control d1-passport" name="passport_number[]" placeholder="Passport Number" value="${p || ''}">
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
                    });
                }
                $('.section-d-1-passport-row').parent().html(rowsHtml);
                $('.d1-issuance-country').each(function(i, el){
                    const v = issuances[i] || '';
                    if (v) $(el).val(v).trigger('change');
                });
				$('#d-1-relationship').val(res.data.relationship_to_policyholder);
				$('#d-1-email').val(res.data.email);
				$('#d-1-allocation').val(res.data.beneficiary_death_benefit_allocation);
				$(`input[name="d-1-designation"][value="${res.data.designation_of_beneficiary}"]`).prop('checked', true);

                if (res.tax && res.tax.length > 0) {
                    $('#tempd1taxbox').empty();

                    $('#tempd1taxbox').append(`
                        <label for="d1countries_of_tax_residence" class="form-label">
                            Countries of Tax Residence 
                        </label>
                    `);

                    res.tax_html.forEach(function(tax) {
                        $('#tempd1taxbox').append(`
                            <div class="row section-d-1-country-tax-residence-row">
                                <div class="col-sm-9">
                                    <select class="form-control section-d-1-country-tax-residence" name="all_countries[]">
                                        <option value=""></option>
                                        ${tax}
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <button type="button" class="btn btn-success section-d-1-add">+</button>
                                    <button type="button" class="btn btn-danger section-d-1-remove">-</button>
                                </div>
                            </div>
                        `);

                        $('.section-d-1-country-tax-residence-row:last .section-d-1-country-tax-residence').select2({
                            theme: 'classic',
                            width: '100%',
                            placeholder: 'Select Country'
                        });
                    });

                } else {
                    $('#tempd1taxbox').html(`
                        <label for="d1countries_of_tax_residence" class="form-label">
                            Countries of Tax Residence 
                        </label>
                            <div class="row section-d-1-country-tax-residence-row">
                                <div class="col-sm-9">
                                    <select class="form-control section-d-1-country-tax-residence" name="all_countries[]">
                                        <option value=""></option>
                                        ${countryHtml}
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <button type="button" class="btn btn-success section-d-1-add">+</button>
                                    <button type="button" class="btn btn-danger section-d-1-remove">-</button>
                                </div>
                            </div>
                    `);

                        $('.section-d-1-country-tax-residence-row:last .section-d-1-country-tax-residence').select2({
                            theme: 'classic',
                            width: '100%',
                            placeholder: 'Select Country'
                        });
                }

				$('html, body').animate({ scrollTop: $('#form-section-d-1').offset().top - 100 }, 400);
			}
		}
	});
}

function d1DeleteBeneficiary(id) {
    const policyId = "{{ $policy->id ?? '' }}";
    
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to delete this beneficiary record?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, keep it',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("case.deleteBeneficiary") }}',
                method: 'POST',
                data: {
                    policy_id: policyId,
                    beneficiary_id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status) {
                        Swal.fire('Deleted!', 'Beneficiary record has been deleted.', 'success');

                        if ($(`#bene-reco-${id}`).length) {
                            $(`#bene-reco-${id}`).remove();
                        }
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                }
            });
        }
    });
}


function deleteCommunication(id) {
    const policyId = "{{ $policy->id ?? '' }}";
    
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to delete this communication?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, keep it',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("case.delete-communication") }}',
                method: 'POST',
                data: {
                    policy_id: policyId,
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status) {
                        Swal.fire('Deleted!', 'Communication record has been deleted.', 'success');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                },
                complete: function () {
                    refreshCommunicationAccordion();
                }
            });
        }
    });
}

function deleteCaseFileNote(id) {
    const policyId = "{{ $policy->id ?? '' }}";
    
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to delete this note?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, keep it',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("case.delete-note") }}',
                method: 'POST',
                data: {
                    policy_id: policyId,
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status) {
                        Swal.fire('Deleted!', 'Case file note record has been deleted.', 'success');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                },
                complete: function () {
                    refreshCaseFileNotesAccordion();
                }
            });
        }
    });
}
</script>

<script>
        function loadIntroducersSidebar() {
            const policyId = "{{ $policy->id ?? '' }}";
            if (!policyId) return;
            
            $.ajax({
                url: '{{ route("case.getIntroducersSidebar") }}',
                method: 'POST',
                data: {
                    policy_id: policyId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('.introducers-submenu').html(response.html);
                    
                    if (response.html && response.html.trim() !== '') {
                        // $('.policy-dropdown-toggle[data-section="insured"]').parent().find('.policy-dropdown-submenu').show();
                    }
                }
            });
        }

        function editIntroducer(id) {
            const policyId = "{{ $policy->id ?? '' }}";
            
            $.ajax({
                url: '{{ route("case.getIntroducer") }}',
                method: 'POST',
                data: {
                    policy_id: policyId,
                    introducer_id: id,
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function () {

                    const contactHtml = `
                        <div class="row">
                            <div class="col-md-2">
                                <input type="hidden" class="contact_person_id" name="contact_person_id[$0]"">
                                <input type="text" class="form-control contact_person_first_name" name="contact_person_first_name[0]" placeholder="First Name" value="">
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control contact_person_middle_name" name="contact_person_middle_name[0]" placeholder="Middle Name" value="">
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control contact_person_last_name" name="contact_person_last_name[0]" placeholder="Last Name" value="">
                            </div>
                            <div class="col-md-2">
                                <input type="email" class="form-control contact_person_email" name="contact_person_email[0]" placeholder="Email" value="">
                            </div>
                            <div class="col-md-3">
                                <input type="hidden" class="contact_person_phone_number_dial_code" id="contact_person_phone_number_dial_code_0" name="contact_person_phone_number_dial_code[0]" value="">
                                <input type="tel" class="form-control contact_person_phone_number" name="contact_person_phone_number[0]" id="contact_person_phone_number_0" value="">
                            </div>
                            <div class="col-md-1 row">
                                <button type="button" class="btn btn-success btn-sm s1ss1contperadd"> + </button>
                                <button type="button" class="btn btn-danger btn-sm s1ss1contperrem"> - </button>
                            </div>
                        </div>
                    `;
                    $('#s1ss1contper').html(contactHtml);

                    let thisAppendedPNTemp = $(`[name="contact_person_phone_number[0]"]`)[0];

                    let thisAppendedDCTemp = window.intlTelInput(thisAppendedPNTemp, {
                        initialCountry: "{{  Helper::getIso2ByDialCode($introducer['dial_code'] ?? null)  }}",
                        preferredCountries: ["us", "gb", "ca", "hk", "ch", "ae"],
                        separateDialCode: true,
                        nationalMode: false,
                        utilsScript: "{{ asset('assets/js/intel-tel-2.min.js') }}"
                    });

                    thisAppendedPNTemp.addEventListener("countrychange", function() {
                        if (thisAppendedDCTemp.isValidNumber()) {
                            $(thisAppendedPNTemp).closest('.row').find('.contact_person_phone_number_dial_code').val(thisAppendedDCTemp.s.dialCode);
                        }
                    });

                    thisAppendedPNTemp.addEventListener('keyup', () => {
                        if (thisAppendedDCTemp.isValidNumber()) {
                            $(thisAppendedPNTemp).closest('.row').find('.contact_person_phone_number_dial_code').val(thisAppendedDCTemp.s.dialCode);
                        }
                    });

                },
                success: function(response) {
                    if (response.data) {
                        const data = response.data;
                        introProfileEditId = data.id;
                        
                        $(`input[name="section_a_1_entity"][value="${data.type.charAt(0).toUpperCase() + data.type.slice(1)}"]`).prop('checked', true);
                        $('#id-for-edit-a-1').val(id);
                        
                        if (data.type === 'entity') {
                            $('#s1ss1ind').addClass('d-none');
                            $('#s1ss1ent').removeClass('d-none');
                            $('#s1ss1contper').removeClass('d-none');
                            $('#s1ss1contper').prev().removeClass('d-none');
                            $('input[name="section_a_1_name"]').val(data.name);
                        } else {
                            $('#s1ss1ind').removeClass('d-none');
                            $('#s1ss1ent').addClass('d-none');
                            $('#s1ss1contper').addClass('d-none');
                            $('#s1ss1contper').prev().addClass('d-none');
                            $('input[name="section_a_1_first_name"]').val(data.name);
                            $('input[name="section_a_1_middle_name"]').val(data.middle_name);
                            $('input[name="section_a_1_last_name"]').val(data.last_name);
                        }
                        
                        $('input[name="section_a_1_email"]').val(data.email);
                        $('input[name="section_a_1_phone"]').val(data.contact_number);
                        $('#section-a-1-dial_code').val(data.dial_code);
                        if (typeof itisa1 !== 'undefined' && itisa1) {
                            const iso = data.dial_iso2 || 'ch';
                            itisa1.setCountry(iso);
                            $('#section-a-1-dial_code').val(itisa1.s.dialCode || data.dial_code || '');
                        }
                        
                        if (data.type === 'entity' && response.contact_persons && response.contact_persons.length > 0) {
                            $('#s1ss1contper').empty();
                            response.contact_persons.forEach(function(contact, index) {
                                const contactHtml = `
                                    <div class="row">
                                        <div class="col-md-2">
                                            <input type="hidden" class="contact_person_id" name="contact_person_id[${index}]" value="${contact.id || ''}">
                                            <input type="text" class="form-control contact_person_first_name" name="contact_person_first_name[${index}]" placeholder="First Name" value="${contact.name || ''}">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control contact_person_middle_name" name="contact_person_middle_name[${index}]" placeholder="Middle Name" value="${contact.middle_name || ''}">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control contact_person_last_name" name="contact_person_last_name[${index}]" placeholder="Last Name" value="${contact.last_name || ''}">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="email" class="form-control contact_person_email" name="contact_person_email[${index}]" placeholder="Email" value="${contact.email || ''}">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="hidden" class="contact_person_phone_number_dial_code" id="contact_person_phone_number_dial_code_${index}" name="contact_person_phone_number_dial_code[${index}]" value="${contact.dial_code || ''}">
                                            <input type="tel" class="form-control contact_person_phone_number" name="contact_person_phone_number[${index}]" id="contact_person_phone_number_${index}" value="${contact.contact_number || ''}">
                                        </div>
                                        <div class="col-md-1 row">
                                            <button type="button" class="btn btn-success btn-sm s1ss1contperadd"> + </button>
                                            <button type="button" class="btn btn-danger btn-sm s1ss1contperrem"> - </button>
                                        </div>
                                    </div>
                                `;
                                $('#s1ss1contper').append(contactHtml);

                                let thisAppendedPN = $(`[name="contact_person_phone_number[${index}]"]`)[0];
                                let initIso = contact.dial_iso2 || (itisa1 && itisa1.s && itisa1.s.iso2) || 'ch';
                                let thisAppendedDC = window.intlTelInput(thisAppendedPN, {
                                    initialCountry: initIso,
                                    preferredCountries: ["us", "gb", "ca", "hk", "ch", "ae"],
                                    separateDialCode: true,
                                    nationalMode: false,
                                    utilsScript: "{{ asset('assets/js/intel-tel-2.min.js') }}"
                                });

                                const updateDial = function() {
                                    const dc = thisAppendedDC && thisAppendedDC.s ? thisAppendedDC.s.dialCode : '';
                                    $(thisAppendedPN).closest('.row').find('.contact_person_phone_number_dial_code').val(dc);
                                };
                                thisAppendedPN.addEventListener("countrychange", updateDial);
                                thisAppendedPN.addEventListener('keyup', updateDial);
                                updateDial();
                            });
                        }
                        
                        $('html, body').animate({
                            scrollTop: $('#form-section-a-1').offset().top - 100
                        }, 500);
                    }
                }
            });
        }

        function deleteIntroducer(id) {
            const policyId = "{{ $policy->id ?? '' }}";
            
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to delete this introducer record?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("case.deleteIntroducer") }}',
                        method: 'POST',
                        data: {
                            policy_id: policyId,
                            introducer_id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire('Deleted!', 'Introducer record has been deleted.', 'success');
                                loadIntroducersSidebar();
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                        }
                    });
                }
            });
        }

        function editIntroducerFromSidebar(id) {
            $('.case-section').addClass('d-none');
            $('#section-a-1').removeClass('d-none');
            
            $('.each-options').removeClass('active');
            $('.policy-dropdown-item[data-section="section-a-1"]').addClass('active');
            
            editIntroducer(id);
        }

        function deleteIntroducerFromSidebar(id) {
            deleteIntroducer(id);
        }


    $(document).ready(function () {
        loadIntroducersSidebar();
    });


(function() {
    function toggleSteps() {
        var v = document.getElementById('admin-fee-type')?.value;
        var show = v === 'step-amount' || v === 'step-year' || v === 'step-flat' || v === 'layered';
        var container = document.getElementById('admin-fee-steps-container');
        if (container) {
            if (show) container.classList.remove('d-none'); else container.classList.add('d-none');
        }
    }

    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'admin-fee-type') {
            toggleSteps();
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('admin-add-row')) {
            e.preventDefault();
            var tbody = document.getElementById('admin-fee-steps-body');
            if (!tbody) return;
            var idx = tbody.querySelectorAll('tr').length;
            var tr = document.createElement('tr');
            tr.innerHTML = '<td class="text-center">' + (idx + 1) + '</td>' +
                '<td><input type="number" step="0.01" min="0" class="form-control" name="admin_fee[steps][' + idx + '][from]"></td>' +
                '<td><input type="number" step="0.01" min="0" class="form-control" name="admin_fee[steps][' + idx + '][to]"></td>' +
                '<td><input type="number" step="0.01" min="0" class="form-control" name="admin_fee[steps][' + idx + '][rate]"></td>' +
                '<td class="text-center"><button type="button" class="btn btn-danger btn-sm admin-remove-row">-</button></td>';
            tbody.appendChild(tr);
        }
        if (e.target && e.target.classList.contains('admin-remove-row')) {
            e.preventDefault();
            var row = e.target.closest('tr');
            var tbody = row && row.parentElement;
            if (tbody && tbody.querySelectorAll('tr').length > 1) {
                row.remove();
                Array.from(tbody.querySelectorAll('tr')).forEach(function(r, i){
                    r.querySelector('td').textContent = (i+1);
                    ['from','to','rate'].forEach(function(key){
                        var input = r.querySelector('input[name^="admin_fee[steps]"][name$="['+key+']"]');
                        if (input) input.name = 'admin_fee[steps]['+i+']['+key+']';
                    });
                });
            }
        }
    });

    document.addEventListener('DOMContentLoaded', toggleSteps);
})();    


$(document).ready(function () {
    $('.entity-type-select').select2({
            placeholder: 'Select entity type',
            theme: 'classic',
            width: '100%',
    });    
});
</script>