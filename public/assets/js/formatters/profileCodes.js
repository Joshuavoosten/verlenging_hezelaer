function profileCodesFormatter(value, row){
    var html = '';
    var labels = value.split(" ");
    $.each(labels, function (index,value) {
        html += '<span class="label label-custom">'+value+'</span>';
    });
    return html;
}