/*
|--------------------------------------------------------------------------
| SmartPath Voice - Pembaca Suara Per Elemen
|--------------------------------------------------------------------------
|
| Perilaku:
| - Tombol "Dengar Panduan" / "Screen Reader" yang sudah ada pada tiap
|   halaman kini menjadi saklar Mode Baca Per Elemen (bukan baca semua).
| - Saat mode aktif:
|     * Pindah fokus (Tab / Shift+Tab)  -> elemen fokus dibacakan.
|     * Klik / ketuk elemen             -> elemen itu saja yang dibacakan.
|     * Tautan, tombol, dan ringkasan   -> ketuk pertama membacakan,
|       ketuk kedua (dalam 8 detik) mengaktifkan. Tombol keyboard Enter
|       tetap langsung mengaktifkan karena fokusnya sudah dibacakan.
|     * Kolom isian, pilihan, dan peta  -> tetap berfungsi normal.
| - Kata sandi tidak pernah dibacakan nilainya.
| - Escape menghentikan suara. Alt+V menyalakan/mematikan mode.
| - Status mode disimpan di localStorage agar berlanjut antar halaman.
|
| Tidak bergantung pada library lain dan tidak mengubah tata letak.
*/
(function () {
    'use strict';

    if (window.__smartpathVoiceLoaded) {
        return;
    }
    window.__smartpathVoiceLoaded = true;

    var STORAGE_KEY = 'smartpath_voice_mode';
    var ARM_WINDOW_MS = 8000;
    var DEDUPE_MS = 650;
    var MAX_CHARS = 520;

    /*
     * Tombol lama yang sebelumnya membacakan seluruh halaman.
     * Klik pada tombol-tombol ini kini dialihkan menjadi saklar mode.
     */
    var TOGGLE_SELECTOR = [
        '#homeScreenReaderButton',
        '#detailScreenReaderButton',
        '#forgotScreenReaderButton',
        '#btnReadPage',
        '#btn-read-page',
        '#nearby-screen-reader',
        '#smartpath-screen-reader',
        '[data-read-page]',
        '[data-voice-toggle]'
    ].join(',');

    var INTERACTIVE_SELECTOR = [
        'a[href]',
        'button',
        'input:not([type="hidden"])',
        'select',
        'textarea',
        'summary',
        '[role="button"]',
        '[role="link"]',
        '[role="tab"]',
        '[role="menuitem"]',
        '[role="checkbox"]',
        '[role="radio"]',
        '[role="switch"]',
        '.leaflet-marker-icon',
        '[tabindex]:not([tabindex="-1"])'
    ].join(',');

    var BLOCK_SELECTOR = [
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'p', 'li', 'td', 'th', 'dt', 'dd',
        'figcaption', 'blockquote', 'label',
        'img', '[data-voice-label]',
        'article', 'section[aria-label]', 'section[aria-labelledby]',
        'nav[aria-label]', 'aside[aria-label]', 'figure'
    ].join(',');

    var active = false;
    var voices = [];
    var lastSpoken = { el: null, time: 0 };
    var armed = { el: null, time: 0 };
    var lastPointer = { x: 0, y: 0, target: null };
    var inputSource = 'pointer';
    var hovered = null;
    var liveRegion = null;
    var toggles = [];
    var pointerClickGuard = { el: null, time: 0 };

    /* ------------------------------------------------------------------
     * Util
     * ---------------------------------------------------------------- */

    function safeGet(key) {
        try { return window.localStorage.getItem(key); } catch (e) { return null; }
    }

    function safeSet(key, value) {
        try { window.localStorage.setItem(key, value); } catch (e) { /* diabain */ }
    }

    function clean(text) {
        return String(text || '').replace(/\s+/g, ' ').trim();
    }

    function truncate(text) {
        var value = clean(text);
        if (value.length <= MAX_CHARS) {
            return value;
        }
        var cut = value.slice(0, MAX_CHARS);
        var space = cut.lastIndexOf(' ');
        return (space > 200 ? cut.slice(0, space) : cut) + ', dan seterusnya';
    }

    function isHidden(el) {
        if (!el || el.nodeType !== 1) { return true; }
        if (el.closest('[aria-hidden="true"], [hidden], [data-voice-skip]')) { return true; }
        var style = window.getComputedStyle(el);
        return style.display === 'none' || style.visibility === 'hidden';
    }

    function textOf(el) {
        if (!el) { return ''; }
        var clone = el.cloneNode(true);
        clone.querySelectorAll(
            '[aria-hidden="true"], script, style, svg, i, .sr-only-ignore, [data-voice-skip]'
        ).forEach(function (n) { n.remove(); });
        return clean(clone.textContent);
    }

    function textFromIds(ids) {
        if (!ids) { return ''; }
        return clean(ids.split(/\s+/).map(function (id) {
            var node = document.getElementById(id);
            return node ? textOf(node) : '';
        }).join('. '));
    }

    /* ------------------------------------------------------------------
     * Suara
     * ---------------------------------------------------------------- */

    function supported() {
        return 'speechSynthesis' in window && 'SpeechSynthesisUtterance' in window;
    }

    function loadVoices() {
        if (!supported()) { return; }
        voices = window.speechSynthesis.getVoices() || [];
    }

    function pickVoice() {
        if (!voices.length) { loadVoices(); }
        for (var i = 0; i < voices.length; i++) {
            if (/^id([-_]ID)?$/i.test(voices[i].lang)) { return voices[i]; }
        }
        for (var j = 0; j < voices.length; j++) {
            if (/^id/i.test(voices[j].lang)) { return voices[j]; }
        }
        return null;
    }

    function chunk(text) {
        /*
         * Chrome menghentikan ucapan panjang secara diam-diam.
         * Teks dipecah per kalimat agar selalu selesai dibacakan.
         */
        var parts = text.match(/[^.!?;:]+[.!?;:]?/g) || [text];
        var result = [];
        var buffer = '';

        parts.forEach(function (part) {
            if ((buffer + part).length > 170 && buffer) {
                result.push(buffer.trim());
                buffer = part;
            } else {
                buffer += part;
            }
        });

        if (buffer.trim()) { result.push(buffer.trim()); }
        return result;
    }

    function stopSpeech() {
        if (supported()) { window.speechSynthesis.cancel(); }
    }

    function speak(text) {
        if (!supported() || !text) { return; }

        stopSpeech();

        var voice = pickVoice();

        chunk(text).forEach(function (segment) {
            var utterance = new SpeechSynthesisUtterance(segment);
            utterance.lang = 'id-ID';
            utterance.rate = 0.95;
            utterance.pitch = 1;
            utterance.volume = 1;
            if (voice) { utterance.voice = voice; }
            window.speechSynthesis.speak(utterance);
        });

        if (liveRegion) { liveRegion.textContent = text; }
    }

    /* ------------------------------------------------------------------
     * Deskripsi elemen
     * ---------------------------------------------------------------- */

    function accessibleName(el) {
        var name = clean(el.getAttribute('aria-label'));
        if (name) { return name; }

        name = textFromIds(el.getAttribute('aria-labelledby'));
        if (name) { return name; }

        if (el.labels && el.labels.length) {
            name = clean(Array.prototype.map.call(el.labels, textOf).join(' '));
            if (name) { return name; }
        }

        var tag = el.tagName.toLowerCase();

        if (tag === 'img') {
            return clean(el.getAttribute('alt'));
        }

        if (tag === 'input' && ['button', 'submit', 'reset'].indexOf(el.type) !== -1) {
            name = clean(el.value);
            if (name) { return name; }
        }

        name = textOf(el);
        if (name) { return name; }

        name = clean(el.getAttribute('title')) || clean(el.getAttribute('placeholder'));
        return name;
    }

    function describedBy(el) {
        var text = textFromIds(el.getAttribute('aria-describedby'));
        var error = textFromIds(el.getAttribute('aria-errormessage'));
        var parts = [];
        if (error && el.getAttribute('aria-invalid') === 'true') {
            parts.push('Kesalahan: ' + error);
        }
        if (text) { parts.push(text); }
        return parts.join('. ');
    }

    function describeControl(el) {
        var tag = el.tagName.toLowerCase();
        var type = (el.getAttribute('type') || 'text').toLowerCase();
        var name = accessibleName(el);
        var parts = [];

        if (tag === 'input' || tag === 'textarea' || tag === 'select') {
            var kind;

            if (tag === 'textarea') { kind = 'kolom isian teks panjang'; }
            else if (tag === 'select') { kind = 'daftar pilihan'; }
            else if (type === 'password') { kind = 'kolom kata sandi'; }
            else if (type === 'email') { kind = 'kolom isian email'; }
            else if (type === 'tel') { kind = 'kolom isian nomor telepon'; }
            else if (type === 'number') { kind = 'kolom isian angka'; }
            else if (type === 'search') { kind = 'kolom pencarian'; }
            else if (type === 'file') { kind = 'tombol pilih berkas'; }
            else if (type === 'checkbox') { kind = 'kotak centang'; }
            else if (type === 'radio') { kind = 'pilihan'; }
            else if (type === 'submit' || type === 'button' || type === 'reset') { kind = 'tombol'; }
            else if (type === 'date') { kind = 'kolom tanggal'; }
            else { kind = 'kolom isian teks'; }

            parts.push((name || 'Tanpa nama') + ', ' + kind);

            if (type === 'checkbox' || type === 'radio') {
                parts.push(el.checked ? 'dipilih' : 'tidak dipilih');
            } else if (tag === 'select') {
                var option = el.options && el.options[el.selectedIndex];
                var label = option ? clean(option.textContent) : '';
                parts.push(label ? 'terpilih ' + label : 'belum ada pilihan');
            } else if (type === 'file') {
                var count = el.files ? el.files.length : 0;
                parts.push(count ? count + ' berkas dipilih' : 'belum ada berkas');
            } else if (
                type !== 'password'
                && type !== 'submit'
                && type !== 'button'
                && type !== 'reset'
            ) {
                var value = clean(el.value);
                parts.push(value ? 'berisi ' + value : 'masih kosong');
            }

            if (el.required || el.getAttribute('aria-required') === 'true') {
                parts.push('wajib diisi');
            }
            if (el.readOnly) { parts.push('hanya baca'); }
            if (el.disabled) { parts.push('tidak aktif'); }
        } else {
            var role = el.getAttribute('role');
            var kindText = 'tombol';

            if (tag === 'a' || role === 'link') { kindText = ''; }
            else if (tag === 'summary') { kindText = 'ringkasan yang dapat dibuka'; }
            else if (role === 'tab') { kindText = 'tab'; }
            else if (role === 'menuitem') { kindText = 'item menu'; }
            else if (el.classList.contains('leaflet-marker-icon')) { kindText = 'penanda peta'; }

            parts.push((name || 'Tanpa nama') + ', ' + kindText);

            var expanded = el.getAttribute('aria-expanded');
            if (expanded === 'true') { parts.push('terbuka'); }
            if (expanded === 'false') { parts.push('tertutup'); }

            var pressed = el.getAttribute('aria-pressed');
            if (pressed === 'true') { parts.push('aktif'); }
            if (pressed === 'false') { parts.push('tidak aktif'); }

            if (el.getAttribute('aria-current')) { parts.push('halaman saat ini'); }
            if (el.disabled || el.getAttribute('aria-disabled') === 'true') {
                parts.push('tidak aktif');
            }
        }

        var hint = describedBy(el);
        if (hint) { parts.push(hint); }

        return truncate(parts.join('. '));
    }

    function describeBlock(el) {
        var tag = el.tagName.toLowerCase();

        if (tag === 'img') {
            var alt = clean(el.getAttribute('alt'));
            return alt ? 'Gambar: ' + alt : 'Gambar dekoratif';
        }

        var custom = clean(el.getAttribute('data-voice-label'));
        if (custom) { return truncate(custom); }

        var label = clean(el.getAttribute('aria-label'))
            || textFromIds(el.getAttribute('aria-labelledby'));
        var body = textOf(el);

        if (/^h[1-6]$/.test(tag)) {
            return truncate( body);
        }

        if (label && body && label !== body && (tag === 'section' || tag === 'nav' || tag === 'aside')) {
            return truncate(label + '. ' + body);
        }

        return truncate(body || label);
    }

    /*
     * Menentukan elemen yang dibacakan berdasarkan elemen yang disentuh.
     */
    function resolveTarget(node) {
        if (!node || node.nodeType !== 1) {
            node = node && node.parentElement;
        }
        if (!node || !document.body.contains(node)) { return null; }
        if (node.closest('[data-voice-skip]')) { return null; }

        /*
         * Kontrol dalam <label> dibacakan sebagai kontrolnya.
         */
        var label = node.closest('label');
        if (label) {
            var control = label.control
                || label.querySelector('input:not([type="hidden"]), select, textarea');
            if (control) { return { el: control, kind: 'control' }; }
        }

        var interactive = node.closest(INTERACTIVE_SELECTOR);
        if (interactive && !interactive.matches('[tabindex]:not(a):not(button):not(input):not(select):not(textarea):not(summary):not([role]):not(.leaflet-marker-icon)')) {
            return { el: interactive, kind: 'control' };
        }

        var block = node.closest(BLOCK_SELECTOR);
        if (block && !isHidden(block)) {
            return { el: block, kind: 'block' };
        }

        return null;
    }

    function insideMapSurface(node) {
        var map = node.closest && node.closest('.leaflet-container');
        if (!map) { return false; }
        return !node.closest('.leaflet-marker-icon, .leaflet-control, .leaflet-popup');
    }

    /* ------------------------------------------------------------------
     * Penyorotan visual
     * ---------------------------------------------------------------- */

    function injectStyle() {
        if (document.getElementById('smartpath-voice-style')) { return; }

        var style = document.createElement('style');
        style.id = 'smartpath-voice-style';
        style.textContent = [
            '.sp-voice-hover{outline:3px dashed #047857!important;outline-offset:3px!important;}',
            '.sp-voice-current{outline:3px solid #047857!important;outline-offset:3px!important;',
            'background-color:rgba(16,185,129,.10)!important;}',
            '.sp-voice-live{position:absolute!important;width:1px!important;height:1px!important;',
            'margin:-1px!important;padding:0!important;overflow:hidden!important;',
            'clip:rect(0,0,0,0)!important;white-space:nowrap!important;border:0!important;}',
            '#sp-voice-floating{position:fixed;right:16px;bottom:16px;z-index:9999;',
            'display:inline-flex;align-items:center;gap:8px;border-radius:9999px;',
            'border:1px solid #065f46;background:#047857;color:#fff;padding:10px 16px;',
            'font:600 14px/1.2 Inter,system-ui,sans-serif;box-shadow:0 8px 24px rgba(0,0,0,.2);cursor:pointer;}',
            '#sp-voice-floating:hover{background:#065f46;}',
            '#sp-voice-floating[aria-pressed="true"]{background:#064e3b;}'
        ].join('');
        document.head.appendChild(style);
    }

    var currentMark = null;

    function mark(el) {
        if (currentMark) { currentMark.classList.remove('sp-voice-current'); }
        currentMark = el;
        if (el) { el.classList.add('sp-voice-current'); }
    }

    function clearHover() {
        if (hovered) { hovered.classList.remove('sp-voice-hover'); hovered = null; }
    }

    /* ------------------------------------------------------------------
     * Pembacaan elemen
     * ---------------------------------------------------------------- */

    function announce(target, force) {
        if (!target || !target.el) { return false; }

        var now = Date.now();
        if (
            !force
            && lastSpoken.el === target.el
            && now - lastSpoken.time < DEDUPE_MS
        ) {
            return false;
        }

        lastSpoken = { el: target.el, time: now };

        var text = target.kind === 'control'
            ? describeControl(target.el)
            : describeBlock(target.el);

        if (!text) { return false; }

        mark(target.el);
        speak(text);
        return true;
    }

    function needsDoubleActivate(el) {
        var tag = el.tagName.toLowerCase();
        if (tag === 'a' || tag === 'summary') { return true; }
        if (tag === 'button') { return true; }
        if (tag === 'input') {
            return ['submit', 'button', 'reset', 'image'].indexOf(el.type) !== -1;
        }
        var role = el.getAttribute('role');
        return role === 'button' || role === 'link' || role === 'tab' || role === 'menuitem';
    }

    function speakAfterChange(el) {
        window.setTimeout(function () {
            announce({ el: el, kind: 'control' }, true);
        }, 30);
    }

    /* ------------------------------------------------------------------
     * Saklar mode
     * ---------------------------------------------------------------- */

    function labelSpan(button) {
        return button.querySelector('[data-read-label], span');
    }

    function paintToggles() {
        toggles.forEach(function (button) {
            var span = labelSpan(button);

            if (!button.dataset.voiceOriginalLabel && span) {
                button.dataset.voiceOriginalLabel = clean(span.textContent);
            }
            if (!button.dataset.voiceOriginalAria) {
                button.dataset.voiceOriginalAria = button.getAttribute('aria-label') || '';
            }

            button.setAttribute('aria-pressed', active ? 'true' : 'false');

            if (span) {
                span.textContent = active
                    ? 'Matikan Suara'
                    : (button.dataset.voiceOriginalLabel || 'Dengar Panduan');
            }

            button.setAttribute(
                'aria-label',
                active
                    ? 'Mode baca per elemen aktif. Tekan untuk mematikan.'
                    : 'Aktifkan mode baca per elemen. Setelah aktif, ketuk bagian halaman untuk mendengarnya.'
            );
        });
    }

    function setActive(next, silent) {
        active = !!next;
        safeSet(STORAGE_KEY, active ? '1' : '0');
        paintToggles();

        if (!active) {
            stopSpeech();
            mark(null);
            clearHover();
            armed = { el: null, time: 0 };
            if (!silent) {
                if (liveRegion) { liveRegion.textContent = 'Mode baca per elemen dimatikan.'; }
            }
            return;
        }

        if (!silent) {
            speak(
                'Mode baca per elemen aktif. '
                + 'Ketuk atau arahkan fokus ke bagian halaman untuk mendengarnya. '
                + 'Untuk membuka tautan atau menekan tombol, ketuk dua kali. '
                + 'Tekan Escape untuk menghentikan suara.'
            );
        }
    }

    function collectToggles() {
        toggles = Array.prototype.slice.call(document.querySelectorAll(TOGGLE_SELECTOR));

        if (!toggles.length && supported()) {
            var floating = document.createElement('button');
            floating.type = 'button';
            floating.id = 'sp-voice-floating';
            floating.setAttribute('data-voice-toggle', '');
            floating.setAttribute('aria-pressed', 'false');
            floating.innerHTML = '<span>Dengar Panduan</span>';
            document.body.appendChild(floating);
            toggles = [floating];
        }
    }

    /* ------------------------------------------------------------------
     * Event
     * ---------------------------------------------------------------- */

    function onPointerDown(event) {
        inputSource = 'pointer';
        lastPointer = { x: event.clientX, y: event.clientY, target: event.target };
    }

    function onKeyDown(event) {
        if (event.altKey && (event.key === 'v' || event.key === 'V')) {
            event.preventDefault();
            setActive(!active);
            return;
        }

        if (event.key === 'Escape') {
            stopSpeech();
            return;
        }

        if (['Tab', 'ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'Home', 'End'].indexOf(event.key) !== -1) {
            inputSource = 'keyboard';
        }
    }

    function onClickCapture(event) {
        var toggle = event.target.closest && event.target.closest(TOGGLE_SELECTOR);

        if (toggle) {
            /*
             * Abaikan klik hasil seret (tombol melayang bisa dipindah).
             */
            var dx = Math.abs(event.clientX - lastPointer.x);
            var dy = Math.abs(event.clientY - lastPointer.y);
            event.preventDefault();
            event.stopImmediatePropagation();

            if (event.detail !== 0 && (dx > 6 || dy > 6)) { return; }
            if (!supported()) {
                if (liveRegion) {
                    liveRegion.textContent = 'Browser ini tidak mendukung fitur suara.';
                }
                return;
            }

            setActive(!active);
            return;
        }

        if (!active) { return; }

        var node = event.target;

        if (insideMapSurface(node)) { return; }

        var target = resolveTarget(node);
        if (!target) { return; }

        var el = target.el;
        var isKeyboardClick = event.detail === 0;

        if (target.kind === 'control') {
            var tag = el.tagName.toLowerCase();
            var type = (el.getAttribute('type') || '').toLowerCase();

            /*
             * Kotak centang dan pilihan: biarkan berubah, lalu bacakan hasilnya.
             * Klik sintetis dari <label> ke <input> diabaikan agar tidak ganda.
             */
            if (tag === 'input' && (type === 'checkbox' || type === 'radio')) {
                var now = Date.now();
                if (pointerClickGuard.el === el && now - pointerClickGuard.time < 120) { return; }
                pointerClickGuard = { el: el, time: now };
                speakAfterChange(el);
                return;
            }

            if (tag === 'input' || tag === 'textarea' || tag === 'select') {
                announce(target, false);
                return;
            }

            if (needsDoubleActivate(el) && !isKeyboardClick) {
                var current = Date.now();
                var isArmed = armed.el === el && current - armed.time < ARM_WINDOW_MS;

                if (!isArmed) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    armed = { el: el, time: current };

                    var spoken = describeControl(el);
                    lastSpoken = { el: el, time: current };
                    mark(el);
                    speak(spoken + '. Klik sekali lagi untuk mengaktifkan.');
                    return;
                }

                armed = { el: null, time: 0 };
                stopSpeech();
                return;
            }

            announce(target, false);
            return;
        }

        announce(target, false);
    }

    function onFocusIn(event) {
        if (!active) { return; }
        if (event.target.closest && event.target.closest(TOGGLE_SELECTOR)) { return; }
        if (insideMapSurface(event.target)) { return; }

        var target = resolveTarget(event.target);
        if (!target) { return; }

        /*
         * Fokus akibat klik mouse/sentuhan dibacakan oleh handler klik.
         * Kolom isian tetap dibacakan saat difokuskan.
         * Elemen non-interaktif yang dapat difokus (tabindex) hanya
         * dibacakan ketika fokus berpindah lewat keyboard.
         */
        var tag = target.el.tagName.toLowerCase();
        var isField = tag === 'input' || tag === 'textarea' || tag === 'select';

        if (target.kind !== 'control' && inputSource !== 'keyboard') { return; }
        if (inputSource === 'pointer' && !isField) { return; }

        announce(target, false);
    }

    function onChange(event) {
        if (!active) { return; }
        var el = event.target;
        if (!el || !el.tagName) { return; }

        var tag = el.tagName.toLowerCase();
        var type = (el.getAttribute('type') || '').toLowerCase();

        if (tag === 'select' || type === 'file') {
            announce({ el: el, kind: 'control' }, true);
        }
    }

    function onMouseOver(event) {
        if (!active) { return; }
        var target = resolveTarget(event.target);

        clearHover();

        if (target && target.el !== currentMark) {
            hovered = target.el;
            hovered.classList.add('sp-voice-hover');
        }
    }

    function onMouseOut() {
        clearHover();
    }

    /* ------------------------------------------------------------------
     * Inisialisasi
     * ---------------------------------------------------------------- */

    function init() {
        injectStyle();

        liveRegion = document.createElement('div');
        liveRegion.className = 'sp-voice-live';
        liveRegion.setAttribute('role', 'status');
        liveRegion.setAttribute('aria-live', 'polite');
        document.body.appendChild(liveRegion);

        loadVoices();
        if (supported() && 'onvoiceschanged' in window.speechSynthesis) {
            window.speechSynthesis.onvoiceschanged = loadVoices;
        }

        collectToggles();

        document.addEventListener('pointerdown', onPointerDown, true);
        document.addEventListener('keydown', onKeyDown, true);
        document.addEventListener('click', onClickCapture, true);
        document.addEventListener('focusin', onFocusIn, true);
        document.addEventListener('change', onChange, true);
        document.addEventListener('mouseover', onMouseOver, true);
        document.addEventListener('mouseout', onMouseOut, true);

        window.addEventListener('pagehide', stopSpeech);

        active = safeGet(STORAGE_KEY) === '1' && supported();
        paintToggles();
    }

    /*
     * Handler klik didaftarkan sedini mungkin supaya tombol lama
     * (yang membaca seluruh halaman) tidak sempat berjalan.
     */
    document.addEventListener('click', function (event) {
        if (event.target.closest && event.target.closest(TOGGLE_SELECTOR) && !window.__smartpathVoiceReady) {
            event.preventDefault();
            event.stopImmediatePropagation();
        }
    }, true);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            init();
            window.__smartpathVoiceReady = true;
        }, { once: true });
    } else {
        init();
        window.__smartpathVoiceReady = true;
    }

    window.SmartPathVoice = {
        enable: function () { setActive(true); },
        disable: function () { setActive(false); },
        toggle: function () { setActive(!active); },
        isActive: function () { return active; }
    };
})();