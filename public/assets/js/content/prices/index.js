$(function(){
    // Profile Code
    $('#rate').change(function(){
         $('#table_prices').bootstrapTable('refresh');
    });
    // Profile Code
    $('#code').change(function(){
         $('#table_prices').bootstrapTable('refresh');
    });
    // Table
    $('#table_prices').bootstrapTable({
        url: '/prices/json',
        queryParams: function(params){
            return {
                limit: params.limit,
                offset: params.offset,
                order: params.order,
                search: params.search,
                sort: params.sort,
                rate: $('#rate').val(),
                code: $('#code').val()
            };
        }
    });
});