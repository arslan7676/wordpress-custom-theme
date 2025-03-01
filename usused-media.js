jQuery(document).ready(function($) {
    $('.delete-media').on('click', function() {
        var attachmentId = $(this).data('id');
        $.post(ajax_object.ajax_url, {
            action: 'delete_unused_media',
            id: attachmentId,
            nonce: ajax_object.nonce
        }, function(response) {
            if (response.success) {
                location.reload();
            }
        });
    });
});