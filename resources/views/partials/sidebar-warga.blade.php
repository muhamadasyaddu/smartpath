<button
    type="button"
    class="warga-sidebar__backdrop"
    data-warga-sidebar-close
    aria-label="Tutup menu navigasi"
    tabindex="-1"
></button>

<aside
    id="warga-sidebar"
    class="warga-sidebar"
    aria-label="Navigasi warga"
    aria-hidden="false"
>
    <div class="warga-sidebar__brand">
        <a href="{{ route('warga.dashboard') }}" class="warga-sidebar__logo">
            <span class="warga-sidebar__logo-icon" aria-hidden="true">
                <i class="fa-solid fa-map-location-dot"></i>
            </span>
            <span>Smart<span>Path</span></span>
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
    </nav>

    <div class="warga-sidebar__footer">
       
       
           
       
    </div>
</aside>
