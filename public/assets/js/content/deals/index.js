$(function(){
    // Table
    $('#table_deals').bootstrapTable({
        url: '/deals/json',
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
});