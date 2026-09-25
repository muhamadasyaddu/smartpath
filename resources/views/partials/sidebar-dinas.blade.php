{{-- ==========================================================
     SIDEBAR DINAS
========================================================== --}}

@if(auth()->check() && auth()->user()->isDinas())

<aside
    id="dinas-sidebar"
    class="hidden lg:flex lg:w-64 lg:flex-col bg-white border-r border-slate-200 min-h-screen justify-between"
    role="complementary"
    aria-label="Menu dinas"
>

    {{-- ======================================================
         BAGIAN MENU
    ======================================================= --}}
    <div class="flex-1 overflow-y-auto">

        {{-- LOGO --}}
        <div
            class="flex items-center gap-2 px-6 h-16 border-b border-slate-200"
        >
            <svg
                class="w-7 h-7 text-emerald-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"
                />
            </svg>

            <span class="font-semibold text-emerald-700">
                SmartPath
            </span>
        </div>


        {{-- ==================================================
             NAVIGASI
        =================================================== --}}
        <nav
            class="px-4 py-6 space-y-1"
            aria-label="Menu dinas"
        >

            {{-- DASHBOARD --}}
            <a
                href="{{ route('dinas.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-colors
                    {{ request()->routeIs('dinas.dashboard')
                        ? 'bg-emerald-50 text-emerald-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                    }}"
            >
                <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                    />
                </svg>

                Dashboard
            </a>


            {{-- JUDUL LAPORAN --}}
            <div class="pt-5 pb-2">
                <span
                    class="text-xs font-semibold text-slate-400 uppercase tracking-wider"
                >
                    Laporan
                </span>
            </div>


            {{-- VERIFIKASI LAPORAN --}}
            
              {{-- VERIFIKASI LAPORAN --}}
<a href="{{ route('admin.verifikasi.index') }}"
   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-colors
       {{ request()->routeIs('admin.verifikasi.*')
           ? 'bg-emerald-50 text-emerald-700'
           : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
       }}"
            >
                <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 14l2 2 4-4"
                    />
                </svg>

                Verifikasi Laporan
            </a>


            {{-- PETA --}}
            <a
                href="{{ route('peta.fasilitas') }}"
                class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors"
            >
                <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 20l-5-2.5V5l5 2.5L15 5l5 2.5v12.5L15 17l-6 3z"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 7.5V20M15 5v12"
                    />
                </svg>

                Peta Infrastruktur
            </a>


            {{-- RENCANA PERBAIKAN --}}
            <a
                href="{{ route('rencana.perbaikan.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors"
            >
                <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 4l2.5-2.5 3 3L7 7 4 4zm4 4l8.5 8.5M16.5 16.5l3 3-1.5 1.5-3-3"
                    />
                </svg>

                Rencana Perbaikan
            </a>
            {{-- KINERJA & ANGGARAN --}}
<a
    href="#"
    class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors"
>
    {{-- ICON KINERJA & ANGGARAN --}}
    <svg
        class="w-5 h-5"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        aria-hidden="true"
    >
        {{-- Sumbu grafik --}}
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="1.8"
            d="M4 19V5"
        />

        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="1.8"
            d="M4 19h16"
        />

        {{-- Grafik naik --}}
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="1.8"
            d="M7 15l3-4 3 2 5-6"
        />

        {{-- Titik grafik --}}
        <circle
            cx="7"
            cy="15"
            r="1"
            fill="currentColor"
            stroke="none"
        />

        <circle
            cx="10"
            cy="11"
            r="1"
            fill="currentColor"
            stroke="none"
        />

        <circle
            cx="13"
            cy="13"
            r="1"
            fill="currentColor"
            stroke="none"
        />

        <circle
            cx="18"
            cy="7"
            r="1"
            fill="currentColor"
            stroke="none"
        />
    </svg>

    Kinerja & Anggaran
</a>


        </nav>

    </div>


    {{-- ======================================================
         PROFIL + DROPDOWN
    ======================================================= --}}
    <div
        class="relative p-4 border-t border-slate-200 bg-white flex-shrink-0"
    >

        {{-- TOMBOL PROFIL --}}
        <button
            type="button"
            id="dinas-profile-button"
            class="w-full flex items-center gap-3 p-2 rounded-2xl border border-slate-200 bg-slate-50 hover:bg-slate-100 transition-colors text-left"
            aria-expanded="false"
            aria-controls="dinas-profile-dropdown"
        >

            {{-- INISIAL --}}
            <div
                class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs shrink-0 border border-emerald-200"
            >
                {{
                    strtoupper(
                        substr(
                            auth()->user()->name
                            ?? 'DP',
                            0,
                            2
                        )
                    )
                }}
            </div>


            {{-- NAMA --}}
            <div class="min-w-0 flex-1">

                <p
                    class="text-xs font-bold text-slate-800 truncate"
                >
                    {{ auth()->user()->name ?? 'Dinas PUPR' }}
                </p>

                <p
                    class="text-[11px] text-slate-500 truncate"
                >
                    Petugas Dinas
                </p>

            </div>


            {{-- ICON PANAH --}}
            <svg
                id="dinas-profile-arrow"
                class="w-4 h-4 text-slate-400 transition-transform"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                />
            </svg>

        </button>


        {{-- ==================================================
             DROPDOWN PROFIL
        =================================================== --}}
        <div
            id="dinas-profile-dropdown"
            class="hidden absolute bottom-[88px] left-4 right-4 rounded-xl border border-slate-200 bg-white shadow-lg overflow-hidden z-50"
        >

            {{-- INFORMASI AKUN --}}
            <div class="px-4 py-3 border-b border-slate-100">

                <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">
                    Akun
                </p>

                <p class="mt-1 text-xs font-semibold text-slate-800 truncate">
                    {{ auth()->user()->name ?? 'Dinas PUPR' }}
                </p>

                <p class="text-[11px] text-slate-500 truncate">
                    {{ auth()->user()->email ?? 'pupr@depok.go.id' }}
                </p>

            </div>


            {{-- PROFIL --}}
                        {{-- LOGOUT --}}
            <form
                action="{{ route('logout') }}"
                method="POST"
                class="border-t border-slate-100"
            >
                @csrf

                <button
                    type="submit"
                    class="flex w-full items-center gap-3 px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors"
                >

                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                        />
                    </svg>

                    Keluar

                </button>

            </form>

        </div>

    </div>

</aside>


{{-- ==========================================================
     SCRIPT DROPDOWN PROFIL
========================================================== --}}

@once

@push('scripts')

<script>
(() => {

    const tombolProfil =
        document.getElementById(
            'dinas-profile-button'
        );

    const dropdown =
        document.getElementById(
            'dinas-profile-dropdown'
        );

    const panah =
        document.getElementById(
            'dinas-profile-arrow'
        );


    if (
        !tombolProfil ||
        !dropdown ||
        !panah
    ) {
        return;
    }


    tombolProfil.addEventListener(
        'click',
        (event) => {

            event.stopPropagation();

            const sedangTerbuka =
                !dropdown.classList.contains(
                    'hidden'
                );


            dropdown.classList.toggle(
                'hidden',
                sedangTerbuka
            );


            tombolProfil.setAttribute(
                'aria-expanded',
                sedangTerbuka
                    ? 'false'
                    : 'true'
            );


            panah.classList.toggle(
                'rotate-180',
                !sedangTerbuka
            );

        }
    );


    document.addEventListener(
        'click',
        (event) => {

            if (
                !tombolProfil.contains(event.target) &&
                !dropdown.contains(event.target)
            ) {

                dropdown.classList.add(
                    'hidden'
                );

                tombolProfil.setAttribute(
                    'aria-expanded',
                    'false'
                );

                panah.classList.remove(
                    'rotate-180'
                );

            }

        }
    );

})();
</script>

@endpush

@endonce

@endif
