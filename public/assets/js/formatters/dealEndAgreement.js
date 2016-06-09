function dealEndAgreementFormatter(value, row){
    var html = '';
    var dates = value.split(",");
    $.each(dates, function (index,value) {
        html += value+'<br />';
    });
    return html;
}