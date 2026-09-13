<aside
    id="admin-sidebar"
    class="fixed inset-y-0 left-0 z-50 flex w-[186px] -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
    role="complementary"
    aria-label="Navigasi administrator"
>

    {{-- LOGO --}}
    <div
        class="flex h-[72px] shrink-0 items-center border-b border-slate-200 px-4"
    >

        <a
            href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-2.5"
            aria-label="SmartPath Dashboard"
        >

            <span
                class="flex h-8 w-8 items-center justify-center rounded-md border border-slate-800 bg-white text-slate-900"
            >

                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 19V5l6 3 4-3 6 3v14l-6-3-4 3-6-3Z"
                    />

                    <path
                        stroke-linecap="round"
                        d="M10 8v11M14 5v14"
                    />

                </svg>

            </span>


            <span class="min-w-0">

                <span
                    class="block text-[16px] font-semibold tracking-[-0.03em] text-slate-950"
                >
                    SmartPath
                </span>

                <span
                    class="block text-[7px] font-medium text-slate-500"
                >
                    Aksesibilitas untuk Semua
                </span>

            </span>

        </a>

    </div>


    <nav
        class="flex-1 overflow-y-auto px-2 py-4"
        aria-label="Menu utama administrator"
    >

        {{-- DASHBOARD --}}
        <a
            href="{{ route('admin.dashboard') }}"
            class="sp-nav {{ request()->routeIs('admin.dashboard') ? 'sp-nav-active' : '' }}"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >

                <rect
                    x="4"
                    y="4"
                    width="6"
                    height="6"
                    rx="1"
                />

                <rect
                    x="14"
                    y="4"
                    width="6"
                    height="6"
                    rx="1"
                />

                <rect
                    x="4"
                    y="14"
                    width="6"
                    height="6"
                    rx="1"
                />

                <rect
                    x="14"
                    y="14"
                    width="6"
                    height="6"
                    rx="1"
                />

            </svg>

            <span>
                Dashboard
            </span>

        </a>


        {{-- PETA --}}
        <a
            href="{{ route('peta.index') }}"
            class="sp-nav {{ request()->routeIs('peta.*') ? 'sp-nav-active' : '' }}"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >

                <path
                    stroke-linejoin="round"
                    d="m4 6 6-3 4 3 6-3v15l-6 3-4-3-6 3V6Z"
                />

                <path
                    stroke-linecap="round"
                    d="M10 3v15M14 6v15"
                />

            </svg>

            <span>
                Peta Publik
            </span>

        </a>


        {{-- LAPORAN --}}
        <a
            href="{{ route('laporan.index') }}"
            class="sp-nav {{ request()->routeIs('laporan.*') ? 'sp-nav-active' : '' }}"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >

                <path
                    stroke-linejoin="round"
                    d="M6 3h9l3 3v15H6z"
                />

                <path
                    stroke-linecap="round"
                    d="M9 11h6M9 15h6M9 7h3"
                />

            </svg>

            <span>
                Laporan
            </span>

        </a>


        @admin

        {{-- VERIFIKASI --}}
        <a
            href="{{ route('admin.verifikasi.index') }}"
            class="sp-nav {{ request()->routeIs('admin.verifikasi.*') ? 'sp-nav-active' : '' }}"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >

                <path
                    stroke-linejoin="round"
                    d="M12 3 20 6v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"
                />

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m8.5 12 2.2 2.2 4.8-5"
                />

            </svg>

            <span>
                Verifikasi
            </span>

        </a>


        {{-- Kategori Hambatan --}}
        <a
            href="{{ route('admin.kategori-hambatan.index') }}"
            class="sp-nav {{ request()->routeIs(
                'admin.kategori-hambatan.*',
                'admin.fasilitas-publik.*',
                'admin.wilayah.*',
                'admin.user.*'
            ) ? 'sp-nav-active' : '' }}"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >

                <path
                    stroke-linecap="round"
                    d="M4 7h16M4 12h16M4 17h16"
                />

                <circle
                    cx="8"
                    cy="7"
                    r="2"
                />

                <circle
                    cx="15"
                    cy="12"
                    r="2"
                />

                <circle
                    cx="10"
                    cy="17"
                    r="2"
                />

            </svg>

            <span>
                Kategori Hambatan
            </span>

        </a>


        {{-- PRIORITAS --}}
        <a
            href="{{ route('admin.pengaturan-prioritas.index') }}"
            class="sp-nav {{ request()->routeIs('admin.pengaturan-prioritas.*') ? 'sp-nav-active' : '' }}"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >

                <path
                    stroke-linecap="round"
                    d="M5 4v16M12 4v16M19 4v16"
                />

                <circle
                    cx="5"
                    cy="9"
                    r="2"
                />

                <circle
                    cx="12"
                    cy="15"
                    r="2"
                />

                <circle
                    cx="19"
                    cy="8"
                    r="2"
                />

            </svg>

            <span>
                Prioritas
            </span>

        </a>


        {{-- PENGATURAN --}}
        <a
            href="{{ route('admin.konfigurasi-sistem.index') }}"
            class="sp-nav {{ request()->routeIs('admin.konfigurasi-sistem.*') ? 'sp-nav-active' : '' }}"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >

                <circle
                    cx="12"
                    cy="12"
                    r="3"
                />

                <path
                    stroke-linecap="round"
                    d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-1.8 1.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V20h-2.5v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1-1.8-1.8.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H4V11.5h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1 1.8-1.8.1.1a1.7 1.7 0 0 0 1.9.3 1.7 1.7 0 0 0 1-1.6V5h2.5v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1 1.8 1.8-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.1V14h-.1a1.7 1.7 0 0 0-1.6 1Z"
                />

            </svg>

            <span>
                Pengaturan
            </span>

        </a>

        @endadmin


        <div class="my-3 border-t border-slate-200"></div>


        <p
            class="px-3 pb-2 text-[9px] font-semibold uppercase tracking-[0.14em] text-slate-400"
        >
            Lainnya
        </p>


        @admin

        {{-- ANALITIK --}}
        <a
            href="{{ route('admin.dashboard') }}#analytics"
            class="sp-nav"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >

                <path
                    stroke-linecap="round"
                    d="M5 19V9M12 19V5M19 19v-7"
                />

                <path
                    stroke-linecap="round"
                    d="M3 19h18"
                />

            </svg>

            <span>
                Analitik
            </span>

        </a>

        @endadmin


        {{-- NOTIFIKASI --}}
        <a
            href="#"
            class="sp-nav"
            aria-disabled="true"
            onclick="return false;"
            title="Modul notifikasi tersedia melalui sistem notifikasi pengguna"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"
                />

            </svg>

            <span>
                Notifikasi
            </span>

            @if(
                auth()->user()
                    ->notifikasi_belum_dibaca_count > 0
            )

                <span
                    class="ml-auto min-w-4 rounded-full bg-slate-900 px-1 text-center text-[8px] font-semibold text-white"
                >
                    {{
                        min(
                            auth()->user()
                                ->notifikasi_belum_dibaca_count,
                            99
                        )
                    }}
                </span>

            @endif

        </a>


        @admin

        {{-- RIWAYAT --}}
        <a
            href="#"
            class="sp-nav"
            aria-disabled="true"
            onclick="return false;"
            title="Gunakan Audit Log untuk melihat aktivitas administrator"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >

                <path
                    stroke-linejoin="round"
                    d="M5 5h14v14H5z"
                />

                <path
                    stroke-linecap="round"
                    d="M8 9h8M8 13h6M8 17h4"
                />

            </svg>

            <span>
                Riwayat Aktivitas
            </span>

        </a>

        @endadmin


        {{-- MODE TUNANETRA --}}
        <button
            type="button"
            id="voice-mode-toggle"
            class="sp-nav w-full text-left"
            aria-pressed="false"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M5 10v4h3l4 4V6l-4 4H5Z"
                />

                <path
                    stroke-linecap="round"
                    d="M16 9.5a4 4 0 0 1 0 5M18.5 7a8 8 0 0 1 0 10"
                />

            </svg>

            <span>
                Mode Tunanetra
            </span>

        </button>


        {{-- BANTUAN --}}
        <button
            type="button"
            class="sp-nav w-full text-left text-slate-400"
            disabled
            aria-disabled="true"
            title="Modul bantuan belum tersedia"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >

                <circle
                    cx="12"
                    cy="12"
                    r="9"
                />

                <path
                    stroke-linecap="round"
                    d="M9.5 9a2.5 2.5 0 1 1 4.3 1.7c-.9.8-1.8 1.2-1.8 2.3M12 16.5h.01"
                />

            </svg>

            <span>
                Bantuan
            </span>

        </button>

    </nav>


    {{-- LOGOUT --}}
    <div
        class="shrink-0 border-t border-slate-200 p-2"
    >

        <form
            method="POST"
            action="{{ route('logout') }}"
        >

            @csrf

            <button
                type="submit"
                class="sp-nav w-full text-left text-slate-600 hover:bg-slate-50 hover:text-slate-950"
            >

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >

                    <path
                        stroke-linecap="round"
                        d="M10 17l5-5-5-5M15 12H3"
                    />

                    <path
                        stroke-linecap="round"
                        d="M21 19V5a2 2 0 0 0-2-2h-6"
                    />

                </svg>

                <span>
                    Keluar
                </span>

            </button>

        </form>

    </div>

</aside>


<div
    id="admin-sidebar-overlay"
    class="fixed inset-0 z-40 hidden bg-slate-950/25 lg:hidden"
    aria-hidden="true"
></div>


@once

<style>

    .sp-nav {

        display: flex;

        align-items: center;

        gap: .65rem;

        min-height: 38px;

        padding: .55rem .65rem;

        margin-bottom: .12rem;

        border-radius: .55rem;

        color: #475569;

        font-size: 11px;

        font-weight: 500;

        line-height: 1.2;

        transition:
            background-color .15s ease,
            color .15s ease;
    }


    .sp-nav svg {

        width: 17px;

        height: 17px;

        flex: 0 0 auto;

    }


    .sp-nav:hover:not(:disabled) {

        background: #f8fafc;

        color: #0f172a;

    }


    .sp-nav-active {

        background: #f1f5f9;

        color: #0f172a;

        font-weight: 600;

        box-shadow:
            inset 0 0 0 1px #d1d5db;

    }


    .sp-nav:focus-visible {

        outline:
            2px solid #10b981;

        outline-offset: 1px;

    }


    .sp-nav:disabled {

        cursor: not-allowed;

        opacity: .7;

    }


    .smartpath-voice-mode {

        font-size: 1.08em;

    }


    .smartpath-voice-mode
    #main-content {

        letter-spacing: .01em;

    }

</style>


@push('scripts')

<script>

(() => {

    const toggle =
        document.getElementById(
            'voice-mode-toggle'
        );


    if (!toggle) {
        return;
    }


    toggle.addEventListener(
        'click',
        () => {

            const enabled =
                document.body.classList.toggle(
                    'smartpath-voice-mode'
                );


            toggle.setAttribute(
                'aria-pressed',
                enabled
                    ? 'true'
                    : 'false'
            );


            if (
                'speechSynthesis'
                in window
            ) {

                window
                    .speechSynthesis
                    .cancel();


                if (enabled) {

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

                        window
                            .speechSynthesis
                            .speak(
                                new SpeechSynthesisUtterance(
                                    text
                                )
                            );

                    }

                }

            }

        }
    );

})();

</script>

@endpush

@endonce