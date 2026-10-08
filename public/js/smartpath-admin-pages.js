document.addEventListener(
    'DOMContentLoaded',
    function () {

        'use strict';


        /*
         * ==========================================================
         * KONFIRMASI FORM
         * ==========================================================
         */

        document
            .querySelectorAll(
                '[data-confirm]'
            )
            .forEach(
                function (form) {

                    form.addEventListener(
                        'submit',
                        function (event) {

                            const message =
                                form.getAttribute(
                                    'data-confirm'
                                );


                            if (
                                message &&
                                !window.confirm(
                                    message
                                )
                            ) {

                                event.preventDefault();

                            }

                        }
                    );

                }
            );


        /*
         * ==========================================================
         * SIDEBAR MOBILE
         * ==========================================================
         */

        const menuButton =
            document.getElementById(
                'admin-mobile-menu'
            );

        const sidebar =
            document.getElementById(
                'admin-sidebar'
            );

        const overlay =
            document.getElementById(
                'admin-sidebar-overlay'
            );


        if (
            menuButton &&
            sidebar &&
            overlay
        ) {

            const setSidebarOpen =
                (open) => {

                    const isMobile =
                        window.innerWidth < 1024;

                    sidebar.classList.toggle(
                        '-translate-x-full',
                        !open
                    );


                    overlay.classList.toggle(
                        'hidden',
                        !open
                    );


                    menuButton.setAttribute(
                        'aria-expanded',
                        open
                            ? 'true'
                            : 'false'
                    );

                    sidebar.inert = isMobile && !open;
                    sidebar.setAttribute(
                        'aria-hidden',
                        String(isMobile && !open)
                    );

                    document.body.classList.toggle(
                        'overflow-hidden',
                        isMobile && open
                    );

                    if (isMobile && open) {
                        const firstLink = sidebar.querySelector('a, button');
                        if (firstLink) firstLink.focus();
                    }

                };

            setSidebarOpen(false);

            menuButton.addEventListener(
                'click',
                function () {

                    const isOpen =
                        !sidebar.classList.contains(
                            '-translate-x-full'
                        );


                    setSidebarOpen(
                        !isOpen
                    );

                }
            );


            overlay.addEventListener(
                'click',
                function () {

                    setSidebarOpen(
                        false
                    );
                    menuButton.focus();

                }
            );

            document.addEventListener('keydown', function (event) {
                const sidebarIsOpen =
                    !sidebar.classList.contains('-translate-x-full');

                if (event.key === 'Escape' && sidebarIsOpen) {
                    setSidebarOpen(false);
                    menuButton.focus();
                    menuButton.focus();
                }
            });


            sidebar
                .querySelectorAll('a')
                .forEach(
                    function (link) {

                        link.addEventListener(
                            'click',
                            function () {

                                if (
                                    window.innerWidth
                                    < 1024
                                ) {

                                    setSidebarOpen(
                                        false
                                    );

                                }

                            }
                        );

                    }
                );


            window.addEventListener(
                'resize',
                function () {

                    if (
                        window.innerWidth
                        >= 1024
                    ) {

                        setSidebarOpen(
                            false
                        );

                    }

                }
            );

        }


        /*
         * ==========================================================
         * MODE TUNANETRA
         * ==========================================================
         *
         * Ini merupakan bantuan tambahan.
         * Tidak menggantikan screen reader native.
         */

        const voiceToggle =
            document.getElementById(
                'voice-mode-toggle'
            );


        if (voiceToggle) {

            voiceToggle.addEventListener(
                'click',
                function () {

                    const enabled =
                        document.body.classList.toggle(
                            'smartpath-voice-mode'
                        );


                    voiceToggle.setAttribute(
                        'aria-pressed',
                        enabled
                            ? 'true'
                            : 'false'
                    );


                    if (
                        !(
                            'speechSynthesis'
                            in window
                        )
                    ) {
                        return;
                    }


                    window
                        .speechSynthesis
                        .cancel();


                    if (!enabled) {
                        return;
                    }


                    const main =
                        document.getElementById(
                            'main-content'
                        );


                    const text =
                        main

                            ? main.innerText
                                .replace(
                                    /\s+/g,
                                    ' '
                                )
                                .trim()
                                .slice(
                                    0,
                                    1800
                                )

                            : '';


                    if (text) {

                        const utterance =
                            new SpeechSynthesisUtterance(
                                text
                            );


                        utterance.lang =
                            document.documentElement
                                .lang
                            ||
                            'id-ID';


                        utterance.rate =
                            0.95;


                        window
                            .speechSynthesis
                            .speak(
                                utterance
                            );

                    }

                }
            );

        }


        /*
         * ==========================================================
         * CTRL / K
         * ==========================================================
         */

        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    (
                        event.ctrlKey ||
                        event.metaKey
                    )
                    &&
                    event.key.toLowerCase()
                    ===
                    'k'
                ) {

                    const search =
                        document.getElementById(
                            'dashboard-search'
                        );


                    if (search) {

                        event.preventDefault();

                        search.focus();

                        search.select();

                    }

                }

            }
        );

    }
);