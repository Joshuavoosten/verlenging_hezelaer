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
    // Under An Agent
    $('#current_under_an_agent').chosen();
    // Current Agreement
    $(document).on('change', '#current_agreement', function(){
        current_agreement();
    });
    current_agreement();
});