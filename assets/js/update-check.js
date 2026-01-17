$('#form-dry-run').on('submit', function (e) {
    e.preventDefault();

    $.post(this.action, $(this).serialize(), function (response) {
        $('#check-list').empty();

        response.checks.forEach(check => {
            $('#check-list').append(
                `<li>${check.ok ? '✔' : '✖'} ${check.message}</li>`
            );
        });

        if (response.status === 'success') {
            $('#preflight-result').removeClass('d-none');
        }
    });
});
