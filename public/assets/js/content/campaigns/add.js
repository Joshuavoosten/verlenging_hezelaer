function under_an_agent() {
    var current_under_an_agent = $('#current_under_an_agent').val();
    if (current_under_an_agent == 'S'){
        $('#content_current_agents').show();
        $('#current_agents').chosen();
    } else {
        $('#content_current_agents').hide();
    }
}
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
    $(document).on('change', '#current_under_an_agent', function() {
        under_an_agent();
    });
    under_an_agent();
    // Current Agreement
    $(document).on('change', '#current_agreement', function(){
        current_agreement();
    });
    current_agreement();
});