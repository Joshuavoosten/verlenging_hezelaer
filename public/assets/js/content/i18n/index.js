// Actions
var actions = {
    edit: {
        url: '/i18n/edit'
    },
    delete: {
        title: __('Delete I18n'),
        message: __('Are you sure u want to delete this I18n?'),
        url: '/i18n/delete'
    }
}
$(function(){
    // Source Language
    $('#source_language').change(function(){
         $('#table_i18n').bootstrapTable('refresh');
    });
    // Destination Language
    $('#destination_language').change(function(){
         $('#table_i18n').bootstrapTable('refresh');
    });
    // Website
    $('#website').change(function(){
         $('#table_i18n').bootstrapTable('refresh');
    });
    // CMS
    $('#cms').change(function(){
         $('#table_i18n').bootstrapTable('refresh');
    });
    // JavaScript
    $('#javascript').change(function(){
         $('#table_i18n').bootstrapTable('refresh');
    });
    // Table
    $('#table_i18n').bootstrapTable({
        url: '/i18n/json',
        queryParams: function(params){
            return {
                limit: params.limit,
                offset: params.offset,
                order: params.order,
                search: params.search,
                sort: params.sort,
                source_language: $('#source_language').val(),
                destination_language: $('#destination_language').val(),
                website: $('#website:checked').val(),
                cms: $('#cms:checked').val(),
                javascript: $('#javascript:checked').val()
            };
        }
    });
});