<header class="warga-mobile-bar">
    <button
        type="button"
        class="warga-mobile-bar__menu"
        data-warga-sidebar-open
        aria-controls="warga-sidebar"
        aria-expanded="false"
        aria-label="Buka menu navigasi"
    >
        <i class="fa-solid fa-bars" aria-hidden="true"></i>
    </button>
    <a href="{{ route('warga.dashboard') }}" class="warga-mobile-bar__brand">
        Smart<span>Path</span>
    </a>
    <span class="warga-mobile-bar__user" aria-label="Pengguna">
        {{ strtoupper(substr(auth()->user()->nama_lengkap ?? 'W', 0, 1)) }}
    </span>
</header>
