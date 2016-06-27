$(function(){
    var campaign_id = $('input[name=campaign_id]').val();
    // Table
    $('#table_campaign_prices').bootstrapTable({
        url: '/campaigns/details/json/campaign_prices/'+campaign_id,
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
    // Enable, Disable
    $(document).on('click', '.button-toggle', function(){
        var _token = $('input[name=_token]').val();
        var campaign_id = $(this).data('campaign-id');
        var has_saving = $(this).data('has-saving');
        var active = $(this).data('active');
        $.ajax({
            url: '/campaign/customer/toggle/'+campaign_id,
            type: 'POST',
            data: {
                '_token': _token,
                'has_saving': has_saving,
                'active': active
            },
            success: function(response) {
                if (has_saving){
                    $('#table_customers_with_savings').bootstrapTable('refresh');
                } else {
                    $('#table_customers_without_saving').bootstrapTable('refresh');
                } 
            },
        });
    });
    // Checkbox
    $(document).on('click', '.campaign_customer_active', function(){
        var _token = $('input[name=_token]').val();
        var id = $(this).data('id');
        var active = ($(this).is(':checked') ? 1 : 0);
        $.ajax({
            url: '/campaign/customer/active/'+id,
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