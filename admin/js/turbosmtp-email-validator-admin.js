(function ($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

    $(document).on("click", "#turbosmtp-disconnect", function (evt) {

        evt.preventDefault();
        if (confirm(turbosmtpEmailValidator.disconnect_account_confirm_message)) {
            $.post(turbosmtpEmailValidator.ajax_disconnect_url, {}, function (response) {

                if (response.success) {
                    window.location.reload();
                } else {
                    alert(response.data.message);
                }
            });
        }

    });

    $(document).on("click", ".tsev-login-form-password-field #showPassword", function () {
        $(".tsev-login-form-password-field #showPassword").hide();
        $(".tsev-login-form-password-field #hidePassword").show();
        $(".tsev-login-form-password-field #consumer_secret").prop('type', 'text');
    });

    $(document).on("click", ".tsev-login-form-password-field #hidePassword", function () {
        $(".tsev-login-form-password-field #showPassword").show();
        $(".tsev-login-form-password-field #hidePassword").hide();
        $(".tsev-login-form-password-field #consumer_secret").prop('type', 'password');
    });

    $(document).on('click', '.turbosmtp-show-details', function () {
        let button = $(this);
        let emailId = button.data('id');

        button.prop('disabled', true).text('Loading...');

        $.ajax({
            url: turbosmtpEmailValidator.ajax_get_email_details_url,
            type: 'POST',
            data: {
                email_id: emailId,
            },
            success: function (response) {
                if (response.success) {
                    $('#turbosmtp-modal .turbosmtp-modal-content').html(response.data.html);
                    $('#turbosmtp-modal').fadeIn();
                } else {
                    alert(response.data.message || 'Error loading details.');
                }
            },
            error: function () {
                alert('AJAX request failed.');
            },
            complete: function () {
                button.prop('disabled', false).text('Show details');
            }
        });
    });

    $(document).on('click', '.turbosmtp-modal-close', function () {
        $('#turbosmtp-modal').fadeOut();
    });


})(jQuery);
