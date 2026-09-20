document.addEventListener('DOMContentLoaded', function () {

    document
        .querySelectorAll('[data-confirm]')
        .forEach(function (form) {

            form.addEventListener('submit', function (event) {

                const message =
                    form.getAttribute('data-confirm');

                if (!message) {
                    return;
                }

                if (!window.confirm(message)) {
                    event.preventDefault();
                }
            });

        });

});