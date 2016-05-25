function editFormatter(value, row) {
    var html = '';
    html += '<a href="' + actions.edit.url + '/' + row.id + '" class="edit">';
    html += '<span class="fa fa-pencil fa-2x"></span>';
    html += '</a>';
    return html;
}