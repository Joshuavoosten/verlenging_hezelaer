// Actions
var actions = {
    edit: {
        url: '/users/edit'
    },
    delete: {
        title: __('Delete User'),
        message: __('Are you sure u want to delete this user?'),
        url: '/users/delete'
    }
}
$(function(){
    // Table
    $('#table_users').bootstrapTable({
        url: '/users/json',
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