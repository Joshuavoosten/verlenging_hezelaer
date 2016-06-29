function booleanFormatter(value, row){
    var html = '';
    if (value == 1 || value == 'TRUE'){
        html += '<span class="fa fa-check fa-2x" style="color: #C0C0C0"></span>';
    }
    return html;
}