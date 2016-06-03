$(function() {
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