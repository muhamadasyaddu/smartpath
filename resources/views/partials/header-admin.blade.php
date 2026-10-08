<header
    class="shrink-0 bg-emerald-100 px-4 sm:px-5 lg:px-6"
role="banner">

    <div class="flex h-full min-w-0 items-center justify-between gap-4">

        <div class="flex min-w-0 items-center gap-3">

            <button
                type="button"
                id="admin-mobile-menu"
                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-md border border-slate-200 text-slate-600 lg:hidden"
                aria-label="Buka menu navigasi"
                aria-controls="admin-sidebar"
                aria-expanded="false"
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
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>

            </button>


            <div class="min-w-0">

                <div class="flex items-center gap-2">

                    <h1 class="sp-header-title truncate">

                        {{
                            now()->hour < 12
                                ? 'Good Morning'
                                : (
                                    now()->hour < 18
                                        ? 'Good Afternoon'
                                        : 'Good Evening'
                                )
                        }},

                        Admin

                    </h1>

                    <span
                        aria-hidden="true"
                        class="text-sm"
                    >
                        👋
                    </span>

                </div>


                <p class="sp-header-subtitle hidden truncate sm:block">

                    Pantau, verifikasi, dan analisis laporan
                    aksesibilitas kota Depok secara real-time

                </p>

            </div>

        </div>


        <div class="flex shrink-0 items-center gap-2 sm:gap-3">

            {{-- SEARCH --}}

            <form
                method="GET"
                action="{{ route('admin.dashboard') }}"
                class="relative hidden md:block"
            >

                <label
                    for="dashboard-search"
                    class="sr-only"
                >
                    Cari laporan, lokasi, atau kategori
                </label>


                <svg
                    class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <circle
                        cx="11"
                        cy="11"
                        r="7"
                    />

                    <path
                        stroke-linecap="round"
                        d="m20 20-4-4"
                    />
                </svg>


                <input
                    id="dashboard-search"
                    name="search"
                    value="{{ request('search') }}"
                    type="search"
                    autocomplete="off"
                    placeholder="Cari laporan, lokasi, kategori..."
                    class="sp-header-search pl-9 pr-12"
                >


                <span
                    class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5 text-[7px] font-medium text-slate-400"
                >
                    Ctrl / K
                </span>

            </form>


            {{-- NOTIFIKASI --}}

            <a
                href="{{ route('admin.notifikasi.index') }}"
                class="sp-header-action relative"
                aria-label="Buka notifikasi"
                title="Notifikasi"
            >

                <svg
                    class="h-[18px] w-[18px]"
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


                @if(
                    auth()->user()->notifikasi_belum_dibaca_count > 0
                )

                    <span
                        class="sp-notification-badge"
                        aria-label="{{
                            auth()->user()
                                ->notifikasi_belum_dibaca_count
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


            {{-- PROFILE --}}

            <div
                class="flex items-center gap-2 border-l border-slate-200 pl-3"
            >

                <span
                    class="sp-profile-avatar"
                    aria-hidden="true"
                >

                    {{
                        collect(
                            preg_split(
                                '/\s+/',
                                trim(
                                    auth()->user()
                                        ->nama_lengkap
                                        ?? 'Admin'
                                )
                            )
                        )
                        ->filter()
                        ->map(
                            fn ($part) =>
                                mb_substr(
                                    $part,
                                    0,
                                    1
                                )
                        )
                        ->take(2)
                        ->implode('')
                    }}

                </span>


                <div class="hidden leading-tight xl:block">

                    <span class="sp-profile-name block truncate">
                        Admin PUPR
                    </span>

                    <span class="sp-profile-location block">
                        Kota Depok
                    </span>

                </div>


                <svg
                    class="hidden h-3.5 w-3.5 text-slate-400 xl:block"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        d="m6 9 6 6 6-6"
                    />
                </svg>

            </div>

        </div>

    </div>

</header>