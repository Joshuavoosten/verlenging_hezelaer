function campaignSegmentFormatter(value, row){
    var html = '';
    if (value == 'FALSE'){
        html += __('Business');
    } else {
        html += __('Consumer');
    }
    return html;
}