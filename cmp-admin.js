jQuery(document).ready(function($) {
    $('#cmp-settings-form').on('submit', function(e) {
        e.preventDefault();
        
        var cmp_message = $('#cmp_message').val();
        var nonce = cmp_ajax_obj.nonce;
        
        $.ajax({
            type: 'POST',
            url: cmp_ajax_obj.ajax_url,
            data: {
                action: 'cmp_save_message',
                cmp_message: cmp_message,
                nonce: nonce
            },
            success: function(response) {
                if(response.success) {
                    $('#cmp-message').text('Wiadomość zapisana: ' + response.data.message);
                } else {
                    $('#cmp-message').text('Wystąpił błąd przy zapisie.');
                }
            },
            error: function() {
                $('#cmp-message').text('Błąd AJAX.');
            }
        });
    });
});
