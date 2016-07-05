function campaignSegmentFormatter(value, row){
    var html = '';
    if (value == 'Zakelijk'){
        html += __('Business');
    } else {
        html += __('Consumer');
    }
    return html;
}