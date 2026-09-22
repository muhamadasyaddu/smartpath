<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartPath - Pemetaan Aksesibilitas Infrastruktur Disabilitas')</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Leaflet CSS (Wajib dipanggil di Head agar peta tidak pecah/berantakan) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Tailwind CSS (CDN Standalone) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        
        /* Fix khusus kontainer peta agar tidak collapse/gepeng */
        #location-map {
            width: 100% !important;
            min-height: 350px !important;
        }
    </style>

    {{-- Stack untuk CSS Tambahan dari view anak --}}
    @stack('styles')
</head>
<body class="bg-white text-slate-800 dark:bg-slate-950 dark:text-slate-100 antialiased min-h-screen flex flex-col justify-between transition-colors duration-200">

    

    <!-- Flash Message Notification -->
    @if(session('success_newsletter'))
        <div id="flash-banner" class="bg-emerald-600 text-white px-4 py-3 text-sm font-semibold text-center sticky top-0 z-50 flex items-center justify-between shadow-md">
            <span class="mx-auto flex items-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                {{ session('success_newsletter') }}
            </span>
            <button onclick="document.getElementById('flash-banner').remove()" class="text-white hover:text-slate-200">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
    @endif

    <!-- Content View -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Leaflet JS (Dipanggil sebelum script view anak) -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Lucide Icons Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>

    {{-- Stack untuk JavaScript Tambahan dari view anak --}}
    @stack('scripts')
</body>
</html>