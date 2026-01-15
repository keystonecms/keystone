$(document).ready(function () {
    'use strict';

    $("form[id*=form]").submit(function (e) {
        e.preventDefault();

        const $form   = $(this);
        const formID  = $form.attr('id');
        const url     = $form.attr('action');
        const data    = $form.serialize();
        const reload  = $form.data('reload') === true;

        clearAlerts();

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: data,

            success: function (response) {
                handleResponse(response, reload, formID);
            },

            error: function (xhr) {
                let response;

                try {
                    response = xhr.responseJSON ?? JSON.parse(xhr.responseText);
                } catch (e) {
                    response = {
                        status: 'error',
                        message: 'Unexpected error occurred'
                    };
                }

                handleResponse(response, false, formID);
            }
        });
    });

    function handleResponse(response, reload, formID) {
   
        console.log("token "+response.csrfToken);

        if (response.csrfToken) {
            $('input[name="_csrf_token"]').val(response.csrfToken);
        }

        if (!response || !response.status) {
            showError('Invalid server response');
            return;
        }

        if (response.status === 'error') {
            showError(response.message);
            return;
        }

        if (response.status === 'success') {
            showSuccess(response.message);
            
        // redirect support
        if (response.redirect) {
            setTimeout(function () {
                window.location.href = response.redirect;
            }, 300);
            return;
        }


            if (reload) {
                setTimeout(function () {
                    location.reload();
                }, 1000);
            } else {
                $('#' + formID).trigger('reset');
            }
        }
    }

    function showError(message) {
        $('.alert.alert-danger')
            .removeClass('d-none')
            .addClass('d-block')
            .html(message);
    }

    function showSuccess(message) {
        $('.alert.alert-success')
            .removeClass('d-none')
            .addClass('d-block')
            .html(message);
    }

    function clearAlerts() {
        $('.alert')
            .removeClass('d-block')
            .addClass('d-none')
            .html('');
    }
});
