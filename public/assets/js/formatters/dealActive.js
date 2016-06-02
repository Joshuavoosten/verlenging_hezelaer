function dealActiveFormatter(value, row){
    if (row.campaign_status > 1 || row.status > 2) {
        switch (value) {
            case 0:
                return '<i class="fa fa-lg fa-times" style="color: red"></i>';
            case 1:
                return '<i class="fa fa-lg fa-check" style="color: green"></i>';
        }
    } else {
        return '<input type="checkbox" name="active['+row.id+']" class="deal_active" data-id="'+row.id+'" '+(value == 1 ? 'checked' : '')+' value="1" />';
    }
}