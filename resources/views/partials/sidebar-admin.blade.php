<aside
    id="admin-sidebar"
    class="fixed inset-y-0 left-0 z-50 flex -translate-x-full flex-col bg-white transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
    role="complementary"
    aria-label="Navigasi administrator"
>

    <div class="sp-sidebar-brand flex shrink-0 items-center">

        <a
            href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-2.5"
            aria-label="SmartPath Dashboard"
        >

            <span
                class="sp-brand-mark"
                aria-hidden="true"
            >
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
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
            </span>

            <span class="min-w-0">

                <span class="sp-brand-name block">
                    SmartPath
                </span>

                <span class="sp-brand-tagline block">
                    Aksesibilitas untuk Semua
                </span>

            </span>

        </a>

    </div>


    <nav
        class="sp-sidebar-nav flex-1 overflow-y-auto"
        aria-label="Menu utama administrator"
    >

        {{-- DASHBOARD --}}
        <a href="{{ route('admin.dashboard') }}"
        class="sp-nav {{ request()->routeIs('admin.dashboard') ? 'sp-nav-active' : '' }}">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.7"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
                focusable="false"
            >
                <path d="M3 10.5L12 3L21 10.5V20C21 20.5523 20.5523 21 20 21H4C3.44772 21 3 20.5523 3 20V10.5Z"/>
                <path d="M9 21V15H15V21"/>
            </svg>

            <span>Dashboard</span>
        </a>


        {{-- PETA PUBLIK --}}

        <a
            href="{{ route('peta.index') }}"
            class="sp-nav {{ request()->routeIs('peta.*') ? 'sp-nav-active' : '' }}"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke-width="1.7"
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
                stroke-width="1.7"
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
                    stroke-width="1.7"
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


            {{-- MASTER DATA --}}

            <a
                href="{{ route('admin.kategori-hambatan.index') }}"
                class="sp-nav {{
                    request()->routeIs(
                        'admin.kategori-hambatan.*',
                        'admin.fasilitas-publik.*',
                        'Admin.wilayah.*',
                        'admin.user.*'
                    )
                    ? 'sp-nav-active'
                    : ''
                }}"
            >

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke-width="1.7"
                    aria-hidden="true"
                >
                    <ellipse
                        cx="12"
                        cy="5"
                        rx="7"
                        ry="3"
                    />

                    <path
                        stroke-linecap="round"
                        d="M5 5v6c0 1.7 3.1 3 7 3s7-1.3 7-3V5M5 11v6c0 1.7 3.1 3 7 3s7-1.3 7-3v-6"
                    />
                </svg>

                <span>
                    Master Data
                </span>

            </a>


            {{-- PRIORITAS --}}

            <a
                href="{{ route('admin.pengaturan-prioritas.index') }}"
                class="sp-nav {{
                    request()->routeIs(
                        'admin.pengaturan-prioritas.*'
                    )
                    ? 'sp-nav-active'
                    : ''
                }}"
            >

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke-width="1.7"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        d="M12 3.5 14.7 9l6 .9-4.3 4.2 1 6-5.4-2.8-5.4 2.8 1-6L3.3 9.9l6-.9L12 3.5Z"
                    />
                </svg>

                <span>
                    Prioritas
                </span>

            </a>


            {{-- PENGATURAN --}}

            <a
                href="{{ route('admin.konfigurasi-sistem.index') }}"
                class="sp-nav {{
                    request()->routeIs(
                        'admin.konfigurasi-sistem.*'
                    )
                    ? 'sp-nav-active'
                    : ''
                }}"
            >

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke-width="1.7"
                    aria-hidden="true"
                >
                    <circle
                        cx="12"
                        cy="12"
                        r="3"
                    />

                    <path
                        stroke-linecap="round"
                        d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-1.8 1.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V20h-2.5v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1-1.8-1.8.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H4V11.5h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1 1.8-1.8 1.8.1a1.7 1.7 0 0 0 1.9.3 1.7 1.7 0 0 0 1-1.6V5h2.5v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1 1.8 1.8-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.1V14h-.1a1.7 1.7 0 0 0-1.6 1Z"
                    />
                </svg>

                <span>
                    Pengaturan
                </span>

            </a>

        @endadmin


        <div
            class="sp-sidebar-divider"
            aria-hidden="true"
        ></div>


        <p class="sp-sidebar-section-title">
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
                    stroke-width="1.7"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        d="M5 19V9M12 19V5M19 19v-7M3 19h18"
                    />
                </svg>

                <span>
                    Analitik
                </span>

            </a>

        @endadmin


        {{-- NOTIFIKASI --}}

        <a
            href="{{ route('admin.notifikasi.index') }}"
            class="sp-nav {{
                request()->routeIs('admin.notifikasi.*')
                    ? 'sp-nav-active'
                    : ''
            }}"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke-width="1.7"
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
                auth()->user()->notifikasi_belum_dibaca_count > 0
            )

                <span
                    class="sp-sidebar-badge"
                    aria-label="{{
                        auth()->user()->notifikasi_belum_dibaca_count
                    }} notifikasi belum dibaca"
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

            {{-- RIWAYAT AKTIVITAS --}}

            <a
                href="{{ route('admin.audit.index') }}"
                class="sp-nav {{
                    request()->routeIs('admin.audit.*')
                        ? 'sp-nav-active'
                        : ''
                }}"
            >

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke-width="1.7"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        d="M3.5 12a8.5 8.5 0 1 0 2.5-6"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3.5 5v4h4M12 7v5l3 2"
                    />
                </svg>

                <span>
                    Riwayat Aktivitas
                </span>

            </a>

        @endadmin


        {{-- BANTUAN --}}

        <a
            href="{{ route('admin.bantuan.index') }}"
            class="sp-nav {{ request()->routeIs('admin.bantuan.*') ? 'sp-nav-active' : '' }}"
            aria-current="{{ request()->routeIs('admin.bantuan.*') ? 'page' : 'false' }}"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke-width="1.7"
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

        </a>

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke-width="1.7"
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


        {{-- MODE TUNANETRA --}}

        <button
            type="button"
            class="sp-nav w-full text-left"
            id="voice-mode-toggle"
            aria-pressed="false"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke-width="1.7"
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

    </nav>


    {{-- LOGOUT --}}

    <div class="sp-sidebar-bottom shrink-0">

        <form
            method="POST"
            action="{{ route('logout') }}"
        >

            @csrf

            <button
                type="submit"
                class="sp-nav w-full text-left"
            >

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke-width="1.7"
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