function emailFormatter(value, row){
    var html = '';
    if (value){
        html += '<a href="mailto:' + value + '">' + value + '</a>';
    }
    return html;
}