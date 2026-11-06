jQuery(document).ready(function ($) {
    $('#jwt-login-plugin #add_code').click(function () {
        $('#auth_codes').append($('#code_line').html());

    });

    $('#jwt-login-plugin input[name="redirect"]').on('change', function () {
        if (~~$(this).val() === 9) {
            $('#jwt-login-plugin #redirect_url').show();
        } else {
            $('#jwt-login-plugin #redirect_url').hide();
        }
    });

    $('#jwt-login-plugin input[name="require_register_auth"]').on('change', function () {
        if (~~$(this).val() === 0) {
            $('#jwt-login-plugin #require_register_auth_alert').show();
        } else {
            $('#jwt-login-plugin #require_register_auth_alert').hide();
        }
    });

    $('#jwt-login-plugin input[name="require_delete_auth"]').on('change', function () {
        if (~~$(this).val() === 0) {
            $('#jwt-login-plugin #require_delete_auth_alert').show();
        } else {
            $('#jwt-login-plugin #require_delete_auth_alert').hide();
        }
    });

    $('#jwt-login-plugin .generated-code').hover(function () {
        $(this).find('.copy-button').show();
    }, function () {
        $(this).find('.copy-button').hide();
    });

    $('#jwt-login-plugin .generated-code .btn').on('click', function (e) {
        e.preventDefault();
        var simpleJWTCopyText = $(this).closest('.generated-code').find('.code').html();
        simpleJWTCopyText = simpleJWTCopyText.trim().replace(/&amp;/gmi, '&');
        simpleJWTCopyText = simpleJWTCopyText.replace(/<b>|<\/b>| /gmi, '');
        var tempJWTLoginCopyInput = $("<input >");
        $("body").append(tempJWTLoginCopyInput);
        tempJWTLoginCopyInput.val(simpleJWTCopyText, '&').select();
        document.execCommand("copy");
        tempJWTLoginCopyInput.remove();
        tempJWTLoginCopyInput = null;
    });


}(jQuery));

function jwt_login_remove_auth_line(a_element) {

    jQuery(a_element).closest('.auth_row').remove();
}

function showDecryptionKey() {
    var elementType = 'text';
    if (jQuery('#decryption_key').attr('type') === 'text') {
        elementType = 'password';
    }
    jQuery('#decryption_key').attr('type', elementType);
}

