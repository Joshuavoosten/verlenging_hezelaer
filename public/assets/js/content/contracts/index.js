$(function(){
    // Table
    $('#table_contracts').bootstrapTable({
        url: '/contracts/json',
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