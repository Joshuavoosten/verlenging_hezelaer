function dealDetailFormatter(index, row) {
    var html = '';
    html += '<table id="table_deal_'+index+'" class="table table-striped">';
    html += '<tr>';
    html += '<th data-field="ean">EAN</th>';
    html += '<th data-field="code">'+__('Code')+'</th>';
    html += '<th data-field="super_contract_number">'+__('Super Contract Number')+'</th>';
    html += '<th data-field="syu_normal">SYU '+__('normal')+'</th>';
    html += '<th data-field="syu_low"">SYU '+__('low')+'</th>';
    html += '<th data-field="end_agreement" data-align="center">'+__('End Agreement')+'</th>';
    html += '<th data-field="cadr_street">'+__('Street')+'</th>';
    html += '<th data-field="cadr_nr">'+__('Address Number')+'</th>';
    html += '<th data-field="cadr_nr_conn">'+__('Address Addition')+'</th>';
    html += '<th data-field="cadr_zip">'+__('Zipcode')+'</th>';
    html += '<th data-field="cadr_city">'+__('City')+'</th>';
    html += '<th data-field="price_normal" data-align="right">'+__('Price')+' '+__('normal')+'</th>';
    html += '<th data-field="price_low" data-align="right">'+__('Price')+' '+__('low')+'</th>';
    /*
    html += '<th data-field="estimate_price_1_year" data-align="right">'+__('Estimate Price')+' 1 ('+__('year')+')</th>';
    html += '<th data-field="estimate_saving_1_year" data-align="right">'+__('Estimate Saving')+' 1 ('+__('year')+')</th>';
    html += '<th data-field="estimate_price_2_year" data-align="right">'+__('Estimate Price')+' 2 ('+__('year')+')</th>';
    html += '<th data-field="estimate_saving_2_year" data-align="right">'+__('Estimate Saving')+' 2 ('+__('year')+')</th>';
    html += '<th data-field="estimate_price_3_year" data-align="right">'+__('Estimate Price')+' 3 ('+__('year')+')</th>';
    html += '<th data-field="estimate_saving_3_year" data-align="right">'+__('Estimate Saving')+' 3 ('+__('year')+')</th>';
    */
    html += '</tr>';
    $.each(row.deals, function (key, value) {
        html += '<tr>';
        html += '<td>'+value.ean+'</td>';
        html += '<td>'+value.code+'</td>';
        html += '<td>'+value.super_contract_number+'</td>';
        html += '<td>'+value.syu_normal+'</td>';
        html += '<td>'+value.syu_low+'</td>';
        html += '<td>'+value.end_agreement+'</td>';
        html += '<td>'+value.cadr_street+'</td>';
        html += '<td>'+value.cadr_nr+'</td>';
        html += '<td>'+value.cadr_nr_conn+'</td>';
        html += '<td>'+value.cadr_zip+'</td>';
        html += '<td>'+value.cadr_city+'</td>';
        html += '<td>'+value.price_normal+'</td>';
        html += '<td>'+value.price_low+'</td>';
        /*
        html += '<td>'+value.estimate_price_1_year+'</td>';
        html += '<td>'+value.estimate_saving_1_year+'</td>';
        html += '<td>'+value.estimate_price_2_year+'</td>';
        html += '<td>'+value.estimate_saving_2_year+'</td>';
        html += '<td>'+value.estimate_price_3_year+'</td>';
        html += '<td>'+value.estimate_saving_3_year+'</td>';
        */
        html += '</tr>';
    });
    html += '<table>';
    return html;
}