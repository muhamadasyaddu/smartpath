@php
    $penggunaHeader = auth()->user();

    $inisialHeader = collect(
        preg_split('/\s+/', trim($penggunaHeader->nama_lengkap ?: 'Admin'))
    )
        ->filter()
        ->map(fn ($bagian) => mb_strtoupper(mb_substr($bagian, 0, 1)))
        ->take(2)
        ->implode('');

    $fotoHeader = null;

    if (
        $penggunaHeader->foto_profil
        && \Illuminate\Support\Facades\Storage::disk('public')->exists($penggunaHeader->foto_profil)
    ) {
        $fotoHeader = asset('storage/' . ltrim($penggunaHeader->foto_profil, '/'));
    }

    $jumlahNotifikasi = (int) $penggunaHeader->notifikasi_belum_dibaca_count;
@endphp

<header
    class="shrink-0 bg-emerald-100 px-4 sm:px-5 lg:px-6"
    role="banner"
>

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
                    <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div class="min-w-0">

                <div class="flex items-center gap-2">

                    <h1 class="sp-header-title truncate">
                        {{
                            match (true) {
                                now()->hour >= 5 && now()->hour < 12 => 'Good Morning',
                                now()->hour >= 12 && now()->hour < 18 => 'Good Afternoon',
                                now()->hour >= 18 && now()->hour < 24 => 'Good Evening',
                                default => 'Good Night',
                            }
                        }},
                        Admin
                    </h1>

                    <span aria-hidden="true" class="text-sm">👋</span>

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
                    data-search-url="{{ route('admin.dashboard.search') }}"
                    class="relative hidden md:block"
                >

                <label for="dashboard-search" class="sr-only">
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
                    <circle cx="11" cy="11" r="7" />
                    <path stroke-linecap="round" d="m20 20-4-4" />
                </svg>

                
                <input
                    id="dashboard-search"
                    name="search"
                    value="{{ request('search') }}"
                    type="search"
                    autocomplete="off"
                    placeholder="Cari laporan, lokasi, kategori..."
                    aria-controls="dashboard-search-results"
                    aria-expanded="false"
                    aria-autocomplete="list"
                    class="sp-header-search pl-9 pr-12"
                />

                
                <div
                    id="dashboard-search-results"
                    role="listbox"
                    aria-label="Hasil pencarian laporan"
                    class="absolute left-0 right-0 top-full z-[100] mt-2 hidden max-h-80 overflow-y-auto rounded-xl border border-slate-200 bg-white p-2 shadow-xl"
                >
                    <p
                        id="dashboard-search-message"
                        class="px-3 py-3 text-sm text-slate-500"
                        role="status"
                        aria-live="polite"
                    >
                        Ketik minimal 2 karakter untuk mencari.
                    </p>

                    <div id="dashboard-search-items"></div>
                </div>


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
                aria-label="Buka notifikasi{{ $jumlahNotifikasi > 0 ? ', ' . $jumlahNotifikasi . ' belum dibaca' : '' }}"
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

                @if ($jumlahNotifikasi > 0)
                    <span class="sp-notification-badge" aria-hidden="true">
                        {{ min($jumlahNotifikasi, 99) }}
                    </span>
                @endif
            </a>


            {{-- AKUN: AVATAR + DROPDOWN --}}
            <div class="relative border-l border-slate-200 pl-3">

                <button
                    type="button"
                    id="admin-account-toggle"
                    class="flex items-center gap-2 rounded-lg p-1 text-left transition hover:bg-white/60 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600"
                    aria-haspopup="menu"
                    aria-expanded="false"
                    aria-controls="admin-account-menu"
                    aria-label="Menu akun {{ $penggunaHeader->nama_lengkap }}"
                >

                    <span class="sp-profile-avatar overflow-hidden" aria-hidden="true">
                        @if ($fotoHeader)
                            <img
                                src="{{ $fotoHeader }}"
                                alt=""
                                class="h-full w-full object-cover"
                            >
                        @else
                            {{ $inisialHeader }}
                        @endif
                    </span>

                    <span class="hidden leading-tight xl:block">
                        <span class="sp-profile-name block truncate">Admin PUPR</span>
                        <span class="sp-profile-location block">Kota Depok</span>
                    </span>

                    <svg
                        id="admin-account-chevron"
                        class="hidden h-3.5 w-3.5 text-slate-400 transition-transform duration-150 xl:block"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" d="m6 9 6 6 6-6" />
                    </svg>

                </button>


                <div
                    id="admin-account-menu"
                    class="absolute right-0 top-full z-[1100] mt-3 hidden w-64 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg"
                    role="menu"
                    aria-labelledby="admin-account-toggle"
                >

                    <div class="border-b border-slate-100 px-4 py-3">
                        <p class="truncate text-sm font-semibold text-slate-900">
                            {{ $penggunaHeader->nama_lengkap }}
                        </p>
                        <p class="truncate text-xs text-slate-500">
                            {{ $penggunaHeader->email }}
                        </p>
                    </div>

                    <a
                        href="{{ route('profile.edit') }}"
                        role="menuitem"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-800 focus:bg-emerald-50 focus:outline-none"
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            aria-hidden="true"
                        >
                            <circle cx="12" cy="8" r="4" />
                            <path stroke-linecap="round" d="M4 21c0-4 3.6-7 8-7s8 3 8 7" />
                        </svg>
                        Profil Saya
                    </a>

                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                        class="border-t border-slate-100"
                    >
                        @csrf
                        <button
                            type="submit"
                            role="menuitem"
                            class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm font-medium text-red-600 transition hover:bg-red-50 focus:bg-red-50 focus:outline-none"
                        >
                            <svg
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                aria-hidden="true"
                            >
                                <path stroke-linecap="round" d="M10 17l5-5-5-5M15 12H3" />
                                <path stroke-linecap="round" d="M21 19V5a2 2 0 0 0-2-2h-6" />
                            </svg>
                            Keluar
                        </button>
                    </form>

                </div>

            </div>

        </div>

    </div>

</header>



@once
@push('scripts')
<script>
(() => {
    'use strict';

    /*
    |--------------------------------------------------------------------------
    | Menu akun administrator
    |--------------------------------------------------------------------------
    */
    const accountButton = document.getElementById('admin-account-toggle');
    const accountMenu = document.getElementById('admin-account-menu');
    const accountChevron = document.getElementById('admin-account-chevron');

    if (accountButton && accountMenu) {
        const menuItems = () =>
            Array.from(
                accountMenu.querySelectorAll('[role="menuitem"]')
            );

        const isOpen = () =>
            accountButton.getAttribute('aria-expanded') === 'true';

        const setMenuOpen = (open, focusFirst = false) => {
            accountMenu.classList.toggle('hidden', !open);
            accountButton.setAttribute('aria-expanded', String(open));

            if (accountChevron) {
                accountChevron.classList.toggle('rotate-180', open);
            }

            if (open && focusFirst) {
                menuItems()[0]?.focus();
            }
        };

        accountButton.addEventListener('click', (event) => {
            event.stopPropagation();
            setMenuOpen(!isOpen());
        });

        accountButton.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                setMenuOpen(true, true);
            }
        });

        accountMenu.addEventListener('keydown', (event) => {
            const items = menuItems();

            if (!items.length) return;

            const index = items.indexOf(document.activeElement);

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                items[(index + 1 + items.length) % items.length].focus();
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                items[(index - 1 + items.length) % items.length].focus();
            } else if (event.key === 'Escape') {
                event.preventDefault();
                setMenuOpen(false);
                accountButton.focus();
            }
        });

        document.addEventListener('click', (event) => {
            if (
                isOpen() &&
                !accountButton.contains(event.target) &&
                !accountMenu.contains(event.target)
            ) {
                setMenuOpen(false);
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && isOpen()) {
                setMenuOpen(false);
                accountButton.focus();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Pencarian global laporan administrator
    |--------------------------------------------------------------------------
    */
    const searchInput = document.getElementById('dashboard-search');
    const resultsBox = document.getElementById('dashboard-search-results');
    const message = document.getElementById('dashboard-search-message');
    const itemsBox = document.getElementById('dashboard-search-items');

    if (!searchInput || !resultsBox || !message || !itemsBox) {
        return;
    }

    const searchForm = searchInput.closest('form');
    const endpoint = searchForm?.dataset.searchUrl;

    if (!endpoint) {
        console.error('SmartPath: URL pencarian administrator tidak ditemukan.');
        return;
    }

    let debounceTimer = null;
    let activeController = null;
    let requestNumber = 0;

    const openResults = () => {
        resultsBox.classList.remove('hidden');
        searchInput.setAttribute('aria-expanded', 'true');
    };

    const closeResults = () => {
        resultsBox.classList.add('hidden');
        searchInput.setAttribute('aria-expanded', 'false');
    };

    const showMessage = (text) => {
        message.textContent = text;
        message.classList.remove('hidden');
    };

    const clearResults = () => {
        itemsBox.replaceChildren();
    };

    const renderResults = (results) => {
        clearResults();

        if (!results.length) {
            showMessage('Tidak ada laporan yang cocok.');
            return;
        }

        message.classList.add('hidden');

        results.forEach((item) => {
            const link = document.createElement('a');
            link.href = item.url;
            link.setAttribute('role', 'option');
            link.className =
                'block rounded-lg px-3 py-3 text-sm transition ' +
                'hover:bg-emerald-50 focus:bg-emerald-50 ' +
                'focus:outline-none focus-visible:ring-2 ' +
                'focus-visible:ring-emerald-600';

            const title = document.createElement('span');
            title.className = 'block font-semibold text-slate-800';
            title.textContent = item.judul || 'Laporan tanpa judul';

            const code = document.createElement('span');
            code.className = 'mt-1 block text-xs text-emerald-700';
            code.textContent = item.kode || '';

            const details = document.createElement('span');
            details.className = 'mt-1 block text-xs text-slate-500';
            details.textContent =
                `${item.kategori || 'Kategori tidak tersedia'} · ` +
                `${item.lokasi || 'Lokasi tidak tersedia'}`;

            const status = document.createElement('span');
            status.className = 'mt-1 block text-xs text-slate-600';
            status.textContent =
                `Status: ${item.status || 'Tidak diketahui'}`;

            link.append(title, code, details, status);
            itemsBox.appendChild(link);
        });
    };

    const searchReports = async (keyword) => {
        if (activeController) {
            activeController.abort();
        }

        const controller = new AbortController();
        activeController = controller;

        const currentRequest = ++requestNumber;

        clearResults();
        showMessage('Mencari laporan...');
        openResults();

        try {
            const url = new URL(endpoint, window.location.origin);
            url.searchParams.set('q', keyword);

            const response = await fetch(url, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: controller.signal
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const payload = await response.json();

            // Abaikan hasil lama jika permintaan baru sudah dimulai.
            if (currentRequest !== requestNumber) return;

            renderResults(
                Array.isArray(payload.results) ? payload.results : []
            );
        } catch (error) {
            if (error.name === 'AbortError') return;
            if (currentRequest !== requestNumber) return;

            clearResults();
            showMessage('Pencarian gagal. Silakan coba kembali.');
            console.error('SmartPath: pencarian laporan gagal.', error);
        }
    };

    searchInput.addEventListener('input', () => {
        window.clearTimeout(debounceTimer);

        if (activeController) {
            activeController.abort();
            activeController = null;
        }

        const keyword = searchInput.value.trim();

        if (keyword.length < 2) {
            requestNumber++;
            clearResults();
            showMessage('Ketik minimal 2 karakter untuk mencari.');
            openResults();
            return;
        }

        debounceTimer = window.setTimeout(() => {
            searchReports(keyword);
        }, 300);
    });

    searchInput.addEventListener('focus', () => {
        if (searchInput.value.trim().length >= 2) {
            openResults();
        }
    });

    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeResults();
            return;
        }

        if (event.key === 'ArrowDown') {
            const firstResult = itemsBox.querySelector('a');

            if (firstResult && !resultsBox.classList.contains('hidden')) {
                event.preventDefault();
                firstResult.focus();
            }
        }
    });

    document.addEventListener('click', (event) => {
        if (
            !searchInput.contains(event.target) &&
            !resultsBox.contains(event.target)
        ) {
            closeResults();
        }
    });
})();
</script>
@endpush
@endonce