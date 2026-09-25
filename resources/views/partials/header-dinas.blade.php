<header
    class="h-[72px] shrink-0 border-b border-slate-200 bg-white px-5 sm:px-6 lg:px-7 flex items-center justify-between gap-4 sticky top-0 z-30"
    role="banner"
>

    {{-- =========================================================
         BAGIAN KIRI HEADER
    ========================================================== --}}
    <div class="flex min-w-0 items-center gap-3">

        {{-- MENU MOBILE --}}
        <button
            type="button"
            id="dinas-mobile-menu"
            class="lg:hidden inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
            aria-label="Buka menu navigasi"
            aria-controls="dinas-sidebar"
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


        {{-- JUDUL HEADER --}}
        <div class="min-w-0">

            <h1
                class="truncate text-[17px] sm:text-[18px] font-semibold tracking-[-0.02em] text-slate-900"
            >
                Dashboard Dinas PUPR
            </h1>

            <p
                class="truncate text-[10px] sm:text-[11px] leading-4 text-slate-500"
            >
                Monitoring infrastruktur & aksesibilitas kota
            </p>

        </div>

    </div>


    {{-- =========================================================
         BAGIAN KANAN HEADER
    ========================================================== --}}
    <div class="flex shrink-0 items-center gap-2">


        {{-- =====================================================
             TANGGAL
        ====================================================== --}}
        <div
            class="flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2"
        >

            {{-- ICON KALENDER --}}
            <svg
                class="h-4 w-4 text-slate-600"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >
                <rect
                    x="3"
                    y="4"
                    width="18"
                    height="17"
                    rx="2"
                />

                <path
                    stroke-linecap="round"
                    d="M16 2v4M8 2v4M3 10h18"
                />
            </svg>


            {{-- TANGGAL --}}
            <span
                class="text-[10px] sm:text-[11px] font-medium text-slate-700"
            >
                {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d M Y') }}
            </span>

        </div>


        {{-- =====================================================
             TOMBOL UNDUH LAPORAN
        ====================================================== --}}
      <a href="{{ route('dinas.laporan.index') }}"
   class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-2 text-[10px] sm:text-[11px] font-medium text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
   title="Unduh Laporan">
   Unduh Laporan
        

            {{-- ICON DOWNLOAD --}}
            <svg
                class="h-4 w-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 3v12"
                />

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m7 10 5 5 5-5"
                />

                <path
                    stroke-linecap="round"
                    d="M5 21h14"
                />
            </svg>



    

        </a>

    </div>

</header>


{{-- =============================================================
     SCRIPT MENU MOBILE
============================================================= --}}
@once

@push('scripts')

<script>
(() => {

    const tombolMenu =
        document.getElementById(
            'dinas-mobile-menu'
        );

    const sidebar =
        document.getElementById(
            'dinas-sidebar'
        );

    const overlay =
        document.getElementById(
            'dinas-sidebar-overlay'
        );


    /*
     * Pastikan semua element tersedia.
     */
    if (
        !tombolMenu ||
        !sidebar ||
        !overlay
    ) {
        return;
    }


    /*
     * Fungsi membuka / menutup sidebar.
     */
    const aturMenu =
        (terbuka) => {

            sidebar.classList.toggle(
                '-translate-x-full',
                !terbuka
            );

            overlay.classList.toggle(
                'hidden',
                !terbuka
            );

            tombolMenu.setAttribute(
                'aria-expanded',
                terbuka
                    ? 'true'
                    : 'false'
            );

            document.body.classList.toggle(
                'overflow-hidden',
                terbuka
            );

        };


    /*
     * Tombol menu mobile.
     */
    tombolMenu.addEventListener(
        'click',
        () => {

            aturMenu(
                sidebar.classList.contains(
                    '-translate-x-full'
                )
            );

        }
    );


    /*
     * Klik overlay untuk menutup menu.
     */
    overlay.addEventListener(
        'click',
        () => {

            aturMenu(false);

        }
    );


    /*
     * Klik menu sidebar otomatis menutup
     * sidebar pada tampilan mobile.
     */
    sidebar
        .querySelectorAll('a')
        .forEach((tautan) => {

            tautan.addEventListener(
                'click',
                () => {

                    if (
                        window.innerWidth < 1024
                    ) {

                        aturMenu(false);

                    }

                }
            );

        });


    /*
     * Jika layar kembali ke desktop,
     * tutup menu mobile.
     */
    window.addEventListener(
        'resize',
        () => {

            if (
                window.innerWidth >= 1024
            ) {

                aturMenu(false);

            }

        }
    );

})();
</script>

@endpush

@endonce
