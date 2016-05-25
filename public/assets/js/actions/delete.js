$(document).off("click", ".delete").on("click", ".delete", function(e) {
    e.preventDefault();
    var _token = $('input[name=_token]').val();
    var id = $(this).data('id');
    var row = $(this).closest('tr');
    BootstrapDialog.show({
        type: BootstrapDialog.TYPE_DANGER,
        title: actions.delete.title,
        message: actions.delete.message,
        buttons: [{
            label: __('Delete'),
            cssClass: 'btn-danger',
            action: function(dialog){
                $.ajax({
                    url: actions.delete.url + '/' + id,
                    type: 'DELETE',
                    data: {
                        '_token': _token
                    },
                    success: function(response) {
                        if (response.status == 'OK'){
                            $('.alert-success').hide();
                            $('.delete-alert').empty().html(response.alert);
                            $('#delete-alert').show();
                            row.remove();
                        }
                    },
                });
                dialog.close();
            }
        }, {
            label: __('Cancel'),
            cssClass: 'btn-success',
            action: function(dialog){
                dialog.close();
            }
        }]
    });
});