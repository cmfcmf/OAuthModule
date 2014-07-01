(function($) {
    $(function($) {
        $('.cmfcmfoauthmodule-passwordToggle').click(function() {
            $(this).next().attr('type', 'text');
        });

        $('.cmfcmfoauthmodule-consumersecret').keyup(function() {
            var provider = ($(this).attr('id').split('secret'))[1];
            handleKeyOrSecretChange(provider);
        });

        $('.cmfcmfoauthmodule-consumerkey').keyup(function() {
            var provider = ($(this).attr('id').split('key'))[1];
            handleKeyOrSecretChange(provider);
        });

        $('.cmfcmfoauthmodule-registrationProvider').change(function() {
            var provider = ($(this).attr('id').split('registrationProvider'))[1];
            handleLoginAndRegistrationToggle(provider);
        });

        $('.cmfcmfoauthmodule-registrationProvider').each(function() {
            var provider = ($(this).attr('id').split('registrationProvider'))[1];
            handleLoginAndRegistrationToggle(provider);
            handleKeyOrSecretChange(provider);
        });

        function handleKeyOrSecretChange(provider) {
            if ($('#secret' + provider).val() === "" || $('#key' + provider).val() === "") {
                $('#loginProvider' + provider).attr('disabled', '');
                $('#registrationProvider' + provider).attr('disabled', '');
                $('#loginProvider' + provider).removeAttr('checked', 'checked');
                $('#registrationProvider' + provider).removeAttr('checked', 'checked');
            } else {
                $('#registrationProvider' + provider).removeAttr('disabled', '');

                if (!$('#registrationProvider' + provider).prop('checked')) {
                    $('#loginProvider' + provider).removeAttr('disabled', '');
                }
            }
        }

        function handleLoginAndRegistrationToggle(provider) {
            if ($('#registrationProvider' + provider).prop('checked')) {
                $('#loginProvider' + provider).attr('checked', 'checked');
                $('#loginProvider' + provider).attr('disabled', '');
            } else {
                $('#loginProvider' + provider).removeAttr('disabled');
            }
        }
    });
})(jQuery);