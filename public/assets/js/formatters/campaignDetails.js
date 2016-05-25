function campaignDetailsFormatter(value, row){
    var html = '';
    html += '<a href="campaigns/details/'+row.id+'">';
    html += row.name;
    html += '</a>';
    return html;
}