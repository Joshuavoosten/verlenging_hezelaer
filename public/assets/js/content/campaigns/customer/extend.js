function estimate_saving(){
    var token = $('#token').val();
    var years = $(".form_end_agreement:checked").val();
    var renewable_resource = $(".form_renewable_resource:checked").val();
    $.ajax({
        type: 'GET',
        url: '/verleng/jaarbesparing/'+token,
        data: {
            years: years,
            renewable_resource: renewable_resource
        },
        dataType: 'json',
        success: function (response) {
            if (response.estimate_saving > 0) {
                $('.estimate_saving').empty().html(response.estimate_saving_format);
                $('.content_estimate_saving').show();
            } else {
                $('.content_estimate_saving').hide();
            }
        }
    });
}
$(function() {
    // End Agreement
    $(".form_end_agreement").click(function(){
        estimate_saving();
    });
    // Renewable Resource
    $(".form_renewable_resource").click(function(){
        estimate_saving();
    });
    // Estimate Saving
    estimate_saving();
    // Tooltip - Payment
    $(".tooltip-payment").mouseover(function(){
        $(this).find('.holderText').css('display', 'block');
        $(this).mouseout(function() {
            $(this).find('.holderText').css('display', 'none');
        });
    });
    // Sign
    $('.form_sign_name').keyup(function(){
        var form_sign_name = $(this).val();
        $('.sign_name').empty().html(form_sign_name);
    });
    $('.form_sign_function').keyup(function(){
        var form_sign_function = $(this).val();
        $('.sign_function').empty().html(form_sign_function);
    });
});