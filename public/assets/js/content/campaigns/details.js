$(function(){
    var campaign_id = $('input[name=campaign_id]').val();
    // Table
    $('#table_customers_without_saving').bootstrapTable({
        url: '/campaigns/details/json/customers_without_saving/'+campaign_id,
        queryParams: function(params){
            return {
                limit: params.limit,
                offset: params.offset,
                order: params.order,
                search: params.search,
                sort: params.sort
            };
        }
    });
    $('#table_customers_with_savings').bootstrapTable({
        url: '/campaigns/details/json/customers_with_savings/'+campaign_id,
        queryParams: function(params){
            return {
                limit: params.limit,
                offset: params.offset,
                order: params.order,
                search: params.search,
                sort: params.sort
            };
        }
    });
    $('#table_customers_with_current_offer').bootstrapTable({
        url: '/campaigns/details/json/customers_with_current_offer/'+campaign_id,
        queryParams: function(params){
            return {
                limit: params.limit,
                offset: params.offset,
                order: params.order,
                search: params.search,
                sort: params.sort
            };
        }
    });
    // Checkbox
    // $(document).off('click', '.deal_active');
    $(document).on('click', '.deal_active', function(){
        var _token = $('input[name=_token]').val();
        var id = $(this).data('id');
        var active = ($(this).is(':checked') ? 1 : 0);
        $.ajax({
            url: '/deals/active/'+id,
            type: 'POST',
            data: {
                '_token': _token,
                'active': active
            },
            success: function(response) {
                // Do Nothing
            },
        });
    });
});