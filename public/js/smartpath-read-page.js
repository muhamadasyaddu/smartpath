(function () {
    'use strict';

    document.querySelectorAll('[data-read-page]').forEach(function (button) {
        const status = document.getElementById(button.dataset.readStatus);
        const text = button.dataset.readText;
        const label = button.querySelector('[data-read-label]');
        const defaultLabel = label ? label.textContent.trim() : 'Dengar Panduan';
        let currentUtterance = null;

        function resetButton() {
            button.setAttribute('aria-pressed', 'false');
            if (label) {
                label.textContent = defaultLabel;
            }
        }

        button.addEventListener('click', function () {
            if (!('speechSynthesis' in window)) {
                if (status) {
                    status.textContent = 'Browser ini tidak mendukung fitur suara.';
                }
                return;
            }

            if (window.speechSynthesis.speaking) {
                if (currentUtterance) {
                    currentUtterance.stoppedByUser = true;
                }
                window.speechSynthesis.cancel();
                resetButton();
                if (status) {
                    status.textContent = 'Panduan suara dihentikan.';
                }
                return;
            }

            const utterance = new SpeechSynthesisUtterance(text);
            currentUtterance = utterance;
            utterance.lang = 'id-ID';
            utterance.rate = 1;
            utterance.onend = function () {
                resetButton();
                if (status) {
                    status.textContent = 'Panduan suara selesai.';
                }
            };
            utterance.onerror = function (event) {
                resetButton();
                if (utterance.stoppedByUser || event.error === 'canceled') {
                    return;
                }
                if (status) {
                    status.textContent = 'Panduan suara tidak dapat dijalankan. Silakan coba lagi.';
                }
            };

            button.setAttribute('aria-pressed', 'true');
            if (label) {
                label.textContent = 'Hentikan Panduan';
            }
            if (status) {
                status.textContent = 'Panduan suara sedang dibacakan.';
            }

            window.speechSynthesis.cancel();
            window.speechSynthesis.speak(utterance);
        });
    });
})();
