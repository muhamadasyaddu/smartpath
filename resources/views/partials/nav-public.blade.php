<nav class="smartpath-public-navbar sticky top-0 z-40 border-b border-slate-200/80 bg-white shadow-xs" role="navigation" aria-label="Navigasi utama">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            
            {{-- Brand & Menu Kiri --}}
            <div class="flex items-center gap-8">
                <a href="{{ route('beranda') }}" class="flex items-center {{ request()->routeIs('warga.dashboard', 'navigasi.*', 'peta.nearby') ? 'warga-navbar-brand--hidden' : '' }}" aria-label="SmartPath Beranda">
                    <img src="{{ asset('logo-smartpath-cropped.png') }}" alt="SmartPath" class="h-9 w-[155px] object-contain object-left">
                </a>

                <div class="hidden items-center gap-6 md:flex">
                    <a href="{{ route('beranda') }}" class="text-sm font-medium text-slate-600 transition-colors hover:text-emerald-700">Beranda</a>
                    <a href="{{ route('peta.index') }}" class="text-sm font-medium text-slate-600 transition-colors hover:text-emerald-700">Peta</a>
                    @auth
                        <a href="{{ route('laporan.create') }}" class="text-sm font-medium text-slate-600 transition-colors hover:text-emerald-700">Lapor</a>
                        <a href="{{ route('laporan.index') }}" class="text-sm font-medium text-slate-600 transition-colors hover:text-emerald-700">Laporan Saya</a>
                    @endauth
                </div>
            </div>

            {{-- Navigasi Kanan (Notifikasi & Profil) --}}
            <div class="flex items-center gap-3">
                @auth
                    @if(auth()->user()->isAdmin() || auth()->user()->isDinas())
                        <a href="{{ route('admin.dashboard') }}" class="hidden items-center gap-1.5 text-sm font-medium text-emerald-700 transition-colors hover:text-emerald-800 sm:inline-flex">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            Dashboard
                        </a>
                    @endif

                    {{-- Tombol Notifikasi --}}
                    <a href="{{ route('notifikasi.index') }}" class="relative flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition-colors hover:bg-slate-200 hover:text-emerald-700" aria-label="Notifikasi">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"/>
                        </svg>
                        @if((auth()->user()->notifikasi_belum_dibaca_count ?? 0) > 0)
                            <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                                {{ auth()->user()->notifikasi_belum_dibaca_count }}
                            </span>
                        @endif
                    </a>

                    {{-- Dropdown Profil --}}
                    <div class="relative group">
                        <button type="button" class="flex items-center gap-2 rounded-xl px-2 py-1 text-sm font-medium text-slate-700 transition-colors hover:text-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2" aria-haspopup="true" aria-expanded="false" aria-controls="public-account-menu" aria-label="Menu akun {{ auth()->user()->nama_lengkap }}">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-xs font-semibold text-emerald-700">
                                {{ strtoupper(substr(auth()->user()->nama_lengkap ?? 'U', 0, 2)) }}
                            </div>
                            <span class="hidden sm:inline">{{ auth()->user()->nama_lengkap }}</span>
                        </button>

                        <div id="public-account-menu" class="absolute right-0 mt-2 w-48 hidden rounded-xl border border-slate-200 bg-white py-1 shadow-lg z-50" role="menu" aria-label="Menu akun">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50" role="menuitem">Profil</a>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50" role="menuitem">Keluar</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Masuk
                    </a>
                @endauth
            </div>

        </div>
    </div>
</nav>