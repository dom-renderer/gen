<script>
$(document).ready(function () {

    $(document).on('click', '.s1ss1contperadd', function () {
        let parent = $(this).parent().parent().clone();
        let index = $('#s1ss1contper > div.row').length;
        
        $(parent).find('.contact_person_id').attr('name', `contact_person_id[${index}]`).val('');
        $(parent).find('.contact_person_first_name').attr('name', `contact_person_first_name[${index}]`).val(null);
        $(parent).find('.contact_person_middle_name').attr('name', `contact_person_middle_name[${index}]`).val(null);
        $(parent).find('.contact_person_last_name').attr('name', `contact_person_last_name[${index}]`).val(null);
        $(parent).find('.contact_person_email').attr('name', `contact_person_email[${index}]`).val(null);
        $(parent).find('.contact_person_phone_number').remove();

        $(parent).find('label.error').remove();
        $(parent).find('div.iti').html(`
            <input type="hidden" class="contact_person_phone_number_dial_code sa1edcp1_pn" id="contact_person_phone_number_dial_code_${index}" name="contact_person_phone_number_dial_code[${index}]" value="">
            <input type="tel" class="form-control contact_person_phone_number sa1edcp1_dc" name="contact_person_phone_number[${index}]" id="contact_person_phone_number" value="">
        `);

        $(parent).insertAfter('#s1ss1contper > div.row:last');

        let thisDynamicSAInput = $(parent).find(`[name="contact_person_phone_number[${index}]"]`)[0];
        let initIso = (typeof itisa1 !== 'undefined' && itisa1 && itisa1.s && itisa1.s.iso2) ? itisa1.s.iso2 : 'ch';
        let thisDynamicSAInputA = window.intlTelInput(thisDynamicSAInput, {
            initialCountry: initIso,
            preferredCountries: ["us", "gb", "ca", "hk", "ch", "ae"],
            separateDialCode: true,
            nationalMode: false,
            utilsScript: "{{ asset('assets/js/intel-tel-2.min.js') }}"
        });

        const updateDial = function() {
            const dc = thisDynamicSAInputA && thisDynamicSAInputA.s ? thisDynamicSAInputA.s.dialCode : '';
            $(thisDynamicSAInput).closest('.row').find('.contact_person_phone_number_dial_code').val(dc);
        };
        thisDynamicSAInput.addEventListener("countrychange", updateDial);
        thisDynamicSAInput.addEventListener('keyup', updateDial);
        updateDial();
    });


    $(document).on('click', '.s1ss1contperrem', function () {
        let length = $('#s1ss1contper > div.row').length;
        
        if (length >= 2) {
            $(this).parent().parent().remove();
        }
    });

});
</script>