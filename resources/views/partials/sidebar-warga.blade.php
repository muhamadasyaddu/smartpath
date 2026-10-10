<button
    type="button"
    class="warga-sidebar__backdrop"
    data-warga-sidebar-close
    aria-label="Tutup menu navigasi"
    tabindex="-1"
></button>

<aside
    id="warga-sidebar"
    class="warga-sidebar flex flex-col justify-between"
    aria-label="Navigasi warga"
    aria-hidden="false"
>
    <div>
        <div class="warga-sidebar__brand">
            <a href="{{ route('warga.dashboard') }}" class="warga-sidebar__logo">
                <img
                    src="{{ asset('logo-smartpath-cropped.png') }}"
                    alt="SmartPath"
                    class="warga-sidebar__logo-image"
                >
            </a>
            <button
                type="button"
                class="warga-sidebar__close"
                data-warga-sidebar-close
                aria-label="Tutup menu navigasi"
            >
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>

        <nav class="warga-sidebar__nav" aria-label="Menu utama">
            <p class="warga-sidebar__label">Menu</p>

            <a
                href="{{ route('warga.dashboard') }}"
                @if(request()->routeIs('warga.dashboard')) aria-current="page" @endif
                class="warga-sidebar__link {{ request()->routeIs('warga.dashboard') ? 'is-active' : '' }}"
            >
                <i class="fa-solid fa-house" aria-hidden="true"></i>
                <span>Dashboard</span>
            </a>
            <a
                href="{{ route('laporan.create') }}"
                @if(request()->routeIs('laporan.create')) aria-current="page" @endif
                class="warga-sidebar__link {{ request()->routeIs('laporan.create') ? 'is-active' : '' }}"
            >
                <i class="fa-solid fa-file-circle-plus" aria-hidden="true"></i>
                <span>Buat Laporan</span>
            </a>
            <a
                href="{{ route('laporan.index') }}"
                @if(request()->routeIs('laporan.index')) aria-current="page" @endif
                class="warga-sidebar__link {{ request()->routeIs('laporan.index') ? 'is-active' : '' }}"
            >
                <i class="fa-solid fa-file-lines" aria-hidden="true"></i>
                <span>Laporan Saya</span>
            </a>
            <a
                href="{{ route('peta.index') }}"
                @if(request()->routeIs('peta.index')) aria-current="page" @endif
                class="warga-sidebar__link {{ request()->routeIs('peta.index') ? 'is-active' : '' }}"
            >
                <i class="fa-solid fa-map" aria-hidden="true"></i>
                <span>Peta Aksesibilitas</span>
            </a>

            <p class="warga-sidebar__label warga-sidebar__label--spaced">Eksplorasi</p>

            <a
                href="{{ route('peta.nearby') }}"
                @if(request()->routeIs('peta.nearby')) aria-current="page" @endif
                class="warga-sidebar__link {{ request()->routeIs('peta.nearby') ? 'is-active' : '' }}"
            >
                <i class="fa-solid fa-location-crosshairs" aria-hidden="true"></i>
                <span>Nearby</span>
            </a>
            <a
                href="{{ route('navigasi.index') }}"
                @if(request()->routeIs('navigasi.*')) aria-current="page" @endif
                class="warga-sidebar__link {{ request()->routeIs('navigasi.*') ? 'is-active' : '' }}"
            >
                <i class="fa-solid fa-route" aria-hidden="true"></i>
                <span>Navigasi Aktif</span>
            </a>

            {{-- MODE TUNANETRA --}}
            <a
                href="{{ route('peta.nearby') }}"
                @if(request()->routeIs('peta.nearby')) aria-current="page" @endif
                class="warga-sidebar__link {{ request()->routeIs('peta.nearby') ? 'is-active' : '' }}"
                aria-label="Mode Tunanetra - Buka fitur Nearby untuk deteksi hambatan terdekat"
            >
                <i class="fa-solid fa-eye-low-vision text-emerald-600" aria-hidden="true"></i>
                <span class="font-bold text-emerald-700">Mode Tunanetra</span>
            </a>
        </nav>
    </div>

    {{-- KARTU ILUSTRASI SMARTPATH DI BAGIAN BAWAH SIDEBAR --}}
    <div class="warga-sidebar__footer p-4 border-t border-slate-100 mt-auto">
        <div class="warga-sidebar-hero mb-3 text-center">
            <div class="overflow-hidden rounded-2xl border border-emerald-100/70 bg-gradient-to-b from-emerald-50/90 to-teal-50/50">
                <img
                    src="{{ asset('smartpath-banner.png') }}"
                    alt="Ilustrasi aksesibilitas SmartPath dengan kota, kursi roda, dan penanda lokasi"
                    class="block h-32 w-full object-cover object-center"
                    loading="lazy"
                >
                <p class="px-3 pb-3 pt-2 text-[11px] leading-relaxed text-slate-600 font-medium">
                    Aksesibilitas untuk semua, perjalanan lebih mudah bersama SmartPath.
                </p>
            </div>
        </div>

        <button
            type="button"
            id="btn-read-sidebar"
            class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-xs font-bold text-emerald-700 shadow-xs transition hover:border-emerald-300 hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
            aria-label="Dengar panduan navigasi menu"
            aria-pressed="false"
        >
            <i class="fa-solid fa-volume-high" aria-hidden="true"></i>
            <span>Dengar Panduan</span>
        </button>
    </div>
</aside>