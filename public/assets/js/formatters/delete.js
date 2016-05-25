function deleteFormatter(value, row) {
    var html = '';
    html += '<a href="#" class="delete" data-id="' + row.id + '">';
    html += '<span class="fa fa-times fa-2x"></span>';
    html += '</a>';
    return html;
}