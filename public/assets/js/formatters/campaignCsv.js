function campaignCsvFormatter(value, row){
    if (row.count_customers == 0) {
        // return null;
    }
    var html = '';
    html += '<a href="campaigns/csv/'+row.id+'">';
    html += '<i class="fa fa-file-text-o fa-2x" aria-hidden="true" style="color: #76999B"></i>';
    html += '</a>';
    return html;
}