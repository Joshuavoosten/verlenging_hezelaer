function current_agreement() {
    var current_agreement = $('#current_agreement').val();
    if (current_agreement == 'Vast contract') {
        $('#current_expiration_date').prop('disabled', false);
    } else {
        $('#current_expiration_date').prop('disabled', true);
        $('#current_expiration_date').val('');
    }
}
$(function(){
    // Profile Codes
    $('#current_profile_codes').chosen();
    // Current Agreement
    $(document).on('change', '#current_agreement', function(){
        current_agreement();
    });
    current_agreement();
});