// Actions
var actions = {
    edit: {
        url: '/campaigns/edit'
    },
    delete: {
        title: __('Delete Campaign'),
        message: __('Are you sure u want to delete this campaign?'),
        url: '/campaigns/delete'
    }
}
$(function(){
    // Table
    $('#table_campaigns_scheduled').bootstrapTable({
        url: '/campaigns/json/scheduled',
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
    $('#table_campaigns_sent').bootstrapTable({
        url: '/campaigns/json/sent',
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