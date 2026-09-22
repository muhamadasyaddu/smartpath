<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPath - Kota yang Lebih Aksesibel</title>
    <meta name="description" content="SmartPath adalah platform partisipatif untuk melaporkan dan memetakan hambatan aksesibilitas di ruang publik.">

    <!-- Font Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                    colors: {
                        emerald: {
                            50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',300:'#6ee7b7',
                            400:'#34d399',500:'#10b981',600:'#059669',700:'#047857',
                            800:'#065f46',900:'#064e3b',950:'#022c22'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* ===== RESET & BASE ===== */
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            color: #172033;
            background: #f8fafc;
            transition: background 0.3s ease, color 0.3s ease;
        }
        body.dark {
            background: #0f172a;
            color: #f1f5f9;
        }

        /* ===== SKIP LINK ===== */
        .skip-link {
            position: absolute;
            top: -9999px;
            left: 50%;
            transform: translateX(-50%);
            background: #059669;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 8px;
            z-index: 9999;
            font-weight: 600;
            transition: top 0.3s;
        }
        .skip-link:focus {
            top: 16px;
        }

        /* ===== SCROLL PROGRESS BAR ===== */
        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, #10b981, #059669, #34d399);
            z-index: 99999;
            width: 0%;
            transition: width 0.1s ease;
        }

        /* ===== FIX ANCHOR SCROLL (AGAR TIDAK TERTUTUP HEADER) ===== */
        .section-anchor {
            scroll-margin-top: 100px; /* Memberi jarak agar header tidak menutupi judul */
        }

        /* ===== HERO ===== */
        .hero {
            background: linear-gradient(90deg, rgba(0,45,37,.98) 0%, rgba(0,54,45,.95) 45%, rgba(0,45,37,.78) 100%);
        }
        .dark .hero {
            background: linear-gradient(90deg, rgba(2,8,23,.98) 0%, rgba(4,20,30,.95) 45%, rgba(2,8,23,.78) 100%);
        }

        .hero-photo {
            background-image: url('{{ asset('foto-tunanetra.png') }}');
            background-size: cover;
            background-position: center;
            opacity: .17;
        }

        .hero-glow {
            background: radial-gradient(circle, rgba(16,185,129,.18), transparent 65%);
        }

        .map-shell {
            background: rgba(9,25,36,.76);
            border: 1px solid rgba(16,185,129,.75);
            box-shadow: 0 25px 80px rgba(0,0,0,.35);
        }

        #smartpath-map .leaflet-tile {
            filter: brightness(.42) saturate(.65) contrast(1.12);
        }

        #smartpath-map .leaflet-control-zoom {
            display: none;
        }

        .map-report {
            position: absolute;
            z-index: 1000;
            left: 50%;
            bottom: 28px;
            transform: translateX(-50%);
            width: 82%;
            max-width: 320px;
            background: #fff;
            border-radius: 12px;
            padding: 9px;
            box-shadow: 0 18px 45px rgba(0,0,0,.32);
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .dark .map-report {
            background: #1e293b;
        }

        .map-report img {
            width: 76px;
            height: 66px;
            border-radius: 8px;
            object-fit: cover;
        }

        .status {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 700;
            background: #fef3c7;
            color: #d97706;
        }
        .dark .status {
            background: #f59e0b30;
            color: #f59e0b;
        }

        /* ===== FEATURE CARDS ===== */
        .feature-card {
            background: #fff;
            border: 1px solid #eef2f7;
            border-radius: 13px;
            box-shadow: 0 8px 25px rgba(15,23,42,.05);
            transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .dark .feature-card {
            background: #1e293b;
            border-color: #334155;
        }
        .feature-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 18px 35px rgba(15,23,42,.10);
        }

        .icon-circle {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg,#e6f7ed,#d9f3e7);
            color: #059669;
            font-size: 22px;
        }
        .dark .icon-circle {
            background: linear-gradient(135deg,#064e3b,#065f46);
            color: #34d399;
        }

        /* ===== STATS ===== */
        .stats-box {
            background: linear-gradient(135deg,#243545,#1c2b39);
            border-radius: 12px;
            box-shadow: 0 14px 35px rgba(15,23,42,.10);
        }
        .dark .stats-box {
            background: linear-gradient(135deg,#0f172a,#1e293b);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,.10);
            color: #fff;
            font-size: 21px;
        }

        /* ===== STEPS ===== */
        .step-icon {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            background: linear-gradient(135deg,#e6f8f1,#d8f2ee);
            color: #087f6d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 15px;
            position: relative;
            z-index: 2;
        }
        .dark .step-icon {
            background: linear-gradient(135deg,#064e3b,#065f46);
            color: #34d399;
        }

        .step-number {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translate(-50%,-12px);
            width: 23px;
            height: 23px;
            border-radius: 50%;
            background: #10b981;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 800;
            z-index: 3;
        }

        .steps-line {
            position: absolute;
            top: 31px;
            left: 12%;
            right: 12%;
            height: 1px;
            border-top: 1px dashed #72cdbd;
        }

        /* ===== CTA ===== */
        .cta-mini {
            background: linear-gradient(145deg,#004b40,#003c35);
            border-radius: 12px;
        }
        .dark .cta-mini {
            background: linear-gradient(145deg,#022c22,#064e3b);
        }

        /* ===== FOOTER ===== */
        .footer {
            background: #0d1f2b;
            color: #cbd5df;
        }
        .dark .footer {
            background: #020617;
            color: #94a3b8;
        }

        .footer-divider {
            border-color: rgba(255,255,255,.10);
        }
        .dark .footer-divider {
            border-color: rgba(255,255,255,.05);
        }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { 
            background: linear-gradient(180deg, #10b981, #059669);
            border-radius: 4px;
        }
        .dark ::-webkit-scrollbar-track { background: #1e293b; }
        .dark ::-webkit-scrollbar-thumb { 
            background: linear-gradient(180deg, #34d399, #10b981);
        }

        /* ===== ANIMASI ===== */
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1023px) {
            .steps-line { display:none; }
            .mobile-menu { display: none; }
            .mobile-menu.open { display: block; }
        }
    </style>
</head>

<body>

    <!-- ===== SKIP LINK ===== -->
    <a href="#main-content" class="skip-link">Langsung ke konten utama</a>

    <!-- ===== SCROLL PROGRESS ===== -->
    <div class="scroll-progress" id="scrollProgress"></div>

    <!-- ===== NAVBAR ===== -->
    <header class="sticky top-0 z-50 bg-[#062a25]/95 dark:bg-[#020617]/95 backdrop-blur-md border-b border-white/10 dark:border-slate-800/50">
        <div class="max-w-[1180px] mx-auto px-6 h-16 flex items-center justify-between">
            <a href="#" class="flex items-center gap-3 text-white">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-700 flex items-center justify-center">
                    <i class="fa-solid fa-route"></i>
                </div>
                <span class="text-xl font-extrabold tracking-tight">SmartPath</span>
            </a>

            <!-- Desktop Menu (Semua ke halaman yang sama) -->
            <nav class="hidden lg:flex items-center gap-8 text-[12px]">
                <!-- Tambahkan class nav-link ke semua menu, dan ganti href ke #id_section -->
                <a href="#beranda" data-section="beranda" class="nav-link text-white border-b-2 border-emerald-400 pb-5">Beranda</a>
                <a href="{{ route('tentang') }}" class="nav-link text-slate-300 hover:text-white transition">Tentang</a>
                <a href="#fitur" data-section="fitur" class="nav-link text-slate-300 hover:text-white transition">Fitur</a>
                <a href="{{ route('peta.index') }}" class="nav-link text-slate-300 hover:text-white transition">Peta</a>
                <a href="#cara-kerja" data-section="cara-kerja" class="nav-link text-slate-300 hover:text-white transition">Cara Kerja</a>
                <a href="#kontak" data-section="kontak" class="nav-link text-slate-300 hover:text-white transition">Kontak</a>
            </nav>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                
                <div class="hidden sm:flex items-center gap-2">
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center justify-center
                              rounded-xl
                              border border-white/20
                              bg-white/5
                              px-5 py-2.5
                              text-sm font-semibold text-white
                              backdrop-blur-sm
                              transition-all duration-300
                              hover:bg-white/10
                              hover:border-emerald-400/50
                              hover:text-emerald-300">
                        Masuk
                    </a>
                    
                    <a href="{{ route('register.post') }}" 
                       class="inline-flex items-center justify-center
                              rounded-xl
                              bg-emerald-600 hover:bg-emerald-500
                              px-5 py-2.5
                              text-sm font-bold text-white
                              transition-all duration-300
                              hover:scale-105">
                        Daftar
                    </a>

                    <button id="themeToggle"
                            class="w-11 h-11 rounded-xl
                                   border border-white/20
                                   bg-white/5
                                   text-white
                                   backdrop-blur-sm
                                   transition-all duration-300
                                   hover:bg-white/10
                                   hover:border-emerald-400/50
                                   hover:text-emerald-300">
                        <i id="themeIcon" class="fa-solid fa-moon"></i>
                    </button>
                </div>

                <button id="mobileMenuBtn" class="lg:hidden text-white text-xl" aria-label="Buka menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="lg:hidden mobile-menu bg-[#062a25]/95 dark:bg-[#020617]/95 px-6 py-4 border-t border-white/10">
            <nav class="flex flex-col space-y-3 text-[14px]">
                <a href="#beranda" class="text-white font-semibold">Beranda</a>
                <a href="{{ route('tentang') }}" class="text-slate-300 hover:text-white transition">Tentang</a>
                <a href="#fitur" class="text-slate-300 hover:text-white transition">Fitur</a>
                <a href="{{ route('peta.index') }}" class="text-slate-300 hover:text-white transition">Peta</a>
                <a href="#cara-kerja" class="text-slate-300 hover:text-white transition">Cara Kerja</a>
                <a href="#kontak" class="text-slate-300 hover:text-white transition">Kontak</a>
            </nav>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <main id="main-content">

        <!-- ===== BERANDA / HERO SECTION ===== -->
        <section id="beranda" class="hero relative min-h-[500px] overflow-hidden section-anchor">
            <div class="absolute inset-0 hero-photo"></div>
            <div class="absolute inset-0 hero-glow"></div>

            <div class="relative z-10 max-w-[1180px] mx-auto px-6 py-14 lg:py-16 grid lg:grid-cols-[.9fr_1.1fr] gap-12 items-center">
                <div class="text-white">
                    <div class="inline-flex items-center gap-2 rounded-full bg-emerald-500/15 border border-emerald-400/20 px-3 py-1.5 text-[11px] font-semibold text-emerald-300 mb-6 animate-pulse">
                        <i class="fa-solid fa-heart-pulse"></i>
                        Bersama, Wujudkan Kota yang Aksesibel
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-[48px] font-extrabold leading-[1.05] tracking-tight">
                        Setiap Jalan<br>
                        Berhak untuk<br>
                        <span class="text-emerald-400">Semua Orang</span>
                    </h1>

                    <p class="mt-5 max-w-[510px] text-[13px] sm:text-[14px] leading-6 text-emerald-50/85">
                        SmartPath adalah platform partisipatif untuk melaporkan dan memetakan hambatan aksesibilitas di ruang publik. Bersama kita ciptakan kota yang inklusif dan ramah untuk semua.
                    </p>

                    <!-- HERO BUTTONS -->
                    <div class="mt-7 flex flex-wrap gap-3">
                        
                        <a href="{{ route('laporan.create') }}" 
                           class="group inline-flex items-center justify-center gap-2
                                  rounded-xl
                                  bg-gradient-to-r from-emerald-500 to-emerald-400
                                  px-6 py-3
                                  text-sm font-bold text-emerald-950
                                  shadow-lg shadow-emerald-500/30
                                  transition-all duration-300
                                  hover:shadow-emerald-400/50
                                  hover:-translate-y-1">
                                  <i class="fa-solid fa-paper-plane"></i>
                            <span>Laporkan Sekarang</span>
                            <i class="fa-solid fa-arrow-right opacity-0 -translate-x-2 transition-all duration-300 group-hover:opacity-100 group-hover:translate-x-0"></i>
                        </a>

                        <a href="{{ route('peta.index') }}" 
                           class="group inline-flex items-center justify-center gap-2
                                  rounded-xl
                                  border border-white/30
                                  bg-white/10
                                  backdrop-blur-sm
                                  px-6 py-3
                                  text-sm font-semibold text-white
                                  transition-all duration-300
                                  hover:bg-white/20
                                  hover:border-emerald-400/50">
                            <i class="fa-solid fa-circle-play"></i>
                            <span>Lihat Peta</span>
                            <i class="fa-solid fa-arrow-right opacity-0 -translate-x-2 transition-all duration-300 group-hover:opacity-100 group-hover:translate-x-0"></i>
                        </a>

                    </div>

                    <div class="mt-7 flex flex-wrap gap-5 text-[11px] text-emerald-100/90">
                        <span><i class="fa-regular fa-circle-check text-emerald-400 mr-1.5"></i>Mudah Digunakan</span>
                        <span><i class="fa-regular fa-circle-check text-emerald-400 mr-1.5"></i>Verifikasi Cepat</span>
                        <span><i class="fa-regular fa-circle-check text-emerald-400 mr-1.5"></i>Dampak Nyata</span>
                    </div>
                </div>

                <!-- ===== MAP CARD ===== -->
                <div class="relative animate-on-scroll">
                    <div class="map-shell rounded-2xl p-3">
                        <div class="relative">
                            <div class="absolute z-[1001] top-3 left-3 right-3">
                                <div class="max-w-[300px] bg-[#0b1822]/90 border border-white/15 rounded-lg px-3 py-2 flex items-center gap-2 backdrop-blur">
                                    <input id="mapSearch" type="text" placeholder="Cari lokasi di 
                                    ..." class="w-full bg-transparent text-white text-[11px] outline-none placeholder:text-slate-400">
                                    <i class="fa-solid fa-magnifying-glass text-slate-300 text-xs"></i>
                                </div>
                            </div>

                            <div id="smartpath-map" class="h-[310px] rounded-xl overflow-hidden"></div>

                            <div class="map-report">
                                <img src="{{ asset('trotoar-depok.jpg') }}" alt="Contoh hambatan trotoar">
                                <div class="min-w-0">
                                    <p class="text-[12px] font-bold text-slate-800 dark:text-white">Trotoar Rusak</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 truncate">Jl. Margonda Raya, Depok</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="status">Pending</span>
                                        <span class="text-[9px] text-slate-400">2 hari yang lalu</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- LEGEND -->
                        <div class="flex justify-center flex-wrap gap-x-5 gap-y-2 pt-3 text-[9px] text-slate-300">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-amber-400 text-[11px]"></i> Pending
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-teal-400 text-[11px]"></i> Diverifikasi
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-sky-400 text-[11px]"></i> Dalam Perbaikan
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-emerald-400 text-[11px]"></i> Selesai
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-red-400 text-[11px]"></i> Ditolak
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== TENTANG SECTION ===== -->
        <!-- ===== FITUR UTAMA ===== -->
        <section id="fitur" class="py-12 bg-white dark:bg-slate-950 section-anchor">
            <div class="max-w-[1180px] mx-auto px-6">
                <div class="grid lg:grid-cols-[220px_1fr] gap-7 items-start">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Fitur Utama</span>
                        <h2 class="mt-2 text-2xl font-extrabold leading-tight text-slate-800 dark:text-white">
                            Fitur yang Membuat<br>
                            <span class="text-emerald-600 dark:text-emerald-400">Perubahan Nyata</span>
                        </h2>
                    </div>

                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div class="feature-card p-5 animate-on-scroll">
                            <div class="icon-circle mb-4"><i class="fa-solid fa-file-pen"></i></div>
                            <h3 class="text-[12px] font-bold text-slate-800 dark:text-white">Laporkan dengan Mudah</h3>
                            <p class="mt-2 text-[10px] leading-5 text-slate-500 dark:text-slate-400">Laporkan hambatan aksesibilitas di sekitar Anda hanya dalam beberapa langkah sederhana.</p>
                        </div>

                        <div class="feature-card p-5 animate-on-scroll">
                            <div class="icon-circle mb-4"><i class="fa-solid fa-shield-halved"></i></div>
                            <h3 class="text-[12px] font-bold text-slate-800 dark:text-white">Verifikasi & Deduplikasi</h3>
                            <p class="mt-2 text-[10px] leading-5 text-slate-500 dark:text-slate-400">Laporan diverifikasi dan didukung sistem deduplikasi agar data tetap akurat.</p>
                        </div>

                        <div class="feature-card p-5 animate-on-scroll">
                            <div class="icon-circle mb-4"><i class="fa-solid fa-chart-column"></i></div>
                            <h3 class="text-[12px] font-bold text-slate-800 dark:text-white">Prioritas Berdasarkan Data</h3>
                            <p class="mt-2 text-[10px] leading-5 text-slate-500 dark:text-slate-400">Sistem skor membantu menentukan prioritas perbaikan berdasarkan dampak nyata.</p>
                        </div>

                        <div class="feature-card p-5 animate-on-scroll">
                            <div class="icon-circle mb-4"><i class="fa-solid fa-people-group"></i></div>
                            <h3 class="text-[12px] font-bold text-slate-800 dark:text-white">Bersama Membangun</h3>
                            <p class="mt-2 text-[10px] leading-5 text-slate-500 dark:text-slate-400">Libatkan komunitas dan pemerintah dalam mewujudkan kota yang lebih aksesibel.</p>
                        </div>
                    </div>
                </div>

                <!-- ===== STATISTICS ===== -->
                <div class="stats-box mt-8 px-7 py-6 text-white animate-on-scroll">
                    <div class="text-[9px] font-bold uppercase tracking-widest text-slate-300 mb-5">SmartPath dalam Angka</div>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-7">
                        <div class="flex items-center gap-4">
                            <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                            <div><p class="text-2xl font-extrabold"><span class="animated-counter" data-target="1245">0</span>+</p><p class="text-[9px] text-slate-300 mt-1">Laporan Masuk<br>di wilayah Depok</p></div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="stat-icon"><i class="fa-solid fa-shield-halved text-emerald-300"></i></div>
                            <div><p class="text-2xl font-extrabold"><span class="animated-counter" data-target="876">0</span>+</p><p class="text-[9px] text-slate-300 mt-1">Laporan Diverifikasi<br>oleh tim SmartPath</p></div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="stat-icon"><i class="fa-solid fa-chart-column text-sky-300"></i></div>
                            <div><p class="text-2xl font-extrabold"><span class="animated-counter" data-target="342">0</span>+</p><p class="text-[9px] text-slate-300 mt-1">Lokasi dalam Proses<br>Perbaikan</p></div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="stat-icon"><i class="fa-solid fa-circle-check text-emerald-300"></i></div>
                            <div><p class="text-2xl font-extrabold"><span class="animated-counter" data-target="210">0</span>+</p><p class="text-[9px] text-slate-300 mt-1">Lokasi Selesai<br>Diperbaiki</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== CARA KERJA ===== -->
        <section id="cara-kerja" class="py-12 bg-slate-50 dark:bg-slate-900 section-anchor">
            <div class="max-w-[1180px] mx-auto px-6">
                <div class="grid lg:grid-cols-[1fr_245px] gap-10 items-start">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Cara Kerja</span>
                        <h2 class="mt-2 text-2xl font-extrabold text-slate-800 dark:text-white">Bersama dalam 4 Langkah Mudah</h2>

                        <div class="relative mt-10">
                            <div class="steps-line"></div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                                <div class="text-center relative animate-on-scroll">
                                    <div class="step-number">1</div>
                                    <div class="step-icon"><i class="fa-solid fa-file-circle-plus"></i></div>
                                    <h3 class="text-[12px] font-bold text-slate-800 dark:text-white">Laporkan</h3>
                                    <p class="text-[9px] leading-4 text-slate-500 dark:text-slate-400 mt-2">Isi formulir laporan dan unggah foto lokasi hambatan aksesibilitas.</p>
                                </div>
                                <div class="text-center relative animate-on-scroll">
                                    <div class="step-number">2</div>
                                    <div class="step-icon"><i class="fa-solid fa-shield-halved"></i></div>
                                    <h3 class="text-[12px] font-bold text-slate-800 dark:text-white">Verifikasi</h3>
                                    <p class="text-[9px] leading-4 text-slate-500 dark:text-slate-400 mt-2">Tim memverifikasi dan menduplikasi laporan agar data akurat.</p>
                                </div>
                                <div class="text-center relative animate-on-scroll">
                                    <div class="step-number">3</div>
                                    <div class="step-icon"><i class="fa-solid fa-chart-column"></i></div>
                                    <h3 class="text-[12px] font-bold text-slate-800 dark:text-white">Prioritaskan</h3>
                                    <p class="text-[9px] leading-4 text-slate-500 dark:text-slate-400 mt-2">Sistem memberi skor dan menentukan prioritas perbaikan.</p>
                                </div>
                                <div class="text-center relative animate-on-scroll">
                                    <div class="step-number">4</div>
                                    <div class="step-icon"><i class="fa-solid fa-circle-check"></i></div>
                                    <h3 class="text-[12px] font-bold text-slate-800 dark:text-white">Tindak Lanjut</h3>
                                    <p class="text-[9px] leading-4 text-slate-500 dark:text-slate-400 mt-2">Pemerintah dan pihak terkait menindaklanjuti hingga perbaikan selesai.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CTA Box -->
                    <div id="laporkan" class="cta-mini p-6 text-white mt-1 animate-on-scroll">
                        <h3 class="text-xl font-extrabold leading-tight">
                            Jadi Bagian dari<br>Perubahan!
                        </h3>
                        
                        <p class="text-[10px] leading-5 text-emerald-100/80 mt-3">
                            Setiap laporan Anda membantu mewujudkan kota yang lebih nyaman dan setara untuk semua.
                        </p>
                        
                        <a href="#beranda" class="inline-block mt-2 text-[10px] font-bold text-emerald-300 hover:text-emerald-200 transition hover:underline cursor-pointer">
                            Ayo Segera!
                        </a>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- ===== FOOTER ===== -->
    <footer id="kontak" class="footer pt-10 pb-5 section-anchor">
        <div class="max-w-[1180px] mx-auto px-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 pb-8 border-b footer-divider">
                <!-- Kolom 1: Brand -->
                <div>
                    <div class="flex items-center gap-3 text-white mb-4">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-700 flex items-center justify-center"><i class="fa-solid fa-route"></i></div>
                        <span class="text-xl font-extrabold">SmartPath</span>
                    </div>
                    <p class="text-[10px] leading-5 text-slate-400">SmartPath adalah platform partisipatif untuk melaporkan dan memetakan hambatan aksesibilitas di ruang publik.</p>
                    <div class="flex gap-2 mt-4">
                        <a href="#" class="w-7 h-7 rounded-full bg-slate-700 flex items-center justify-center text-xs hover:bg-emerald-600 transition"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="w-7 h-7 rounded-full bg-slate-700 flex items-center justify-center text-xs hover:bg-emerald-600 transition"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="w-7 h-7 rounded-full bg-slate-700 flex items-center justify-center text-xs hover:bg-emerald-600 transition"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

                <!-- Kolom 2: Navigasi -->
                <div>
                    <h4 class="text-[11px] font-bold text-white mb-4">Navigasi</h4>
                    <ul class="space-y-2 text-[10px]">
                        <li><a href="#beranda" class="hover:text-emerald-400 transition">Beranda</a></li>
                        <li><a href="{{ route('tentang') }}" class="hover:text-emerald-400 transition">Tentang</a></li>
                        <li><a href="#fitur" class="hover:text-emerald-400 transition">Fitur</a></li>
                        <li><a href="{{ route('peta.fasilitas') }}" class="hover:text-emerald-400 transition">Peta</a></li>
                        <li><a href="#cara-kerja" class="hover:text-emerald-400 transition">Cara Kerja</a></li>
                        <li><a href="#kontak" class="hover:text-emerald-400 transition">Kontak</a></li>
                    </ul>
                </div>

                <!-- Kolom 3: Kategori Laporan -->
                <div>
                    <h4 class="text-[11px] font-bold text-white mb-4">Kategori Laporan</h4>
                    <ul class="space-y-3 text-[10px]">
                        <li><i class="fa-solid fa-road w-5 text-slate-500"></i> Trotoar Rusak</li>
                        <li><i class="fa-solid fa-wheelchair w-5 text-slate-500"></i> Ramp Tidak Ada</li>
                        <li><i class="fa-solid fa-grip-lines w-5 text-slate-500"></i> Guiding Block Rusak</li>
                        <li><i class="fa-solid fa-ellipsis w-5 text-slate-500"></i> Lainnya</li>
                    </ul>
                </div>

                <!-- Kolom 4: Alamat -->
                <div>
                    <h4 class="text-[11px] font-bold text-white mb-4">Alamat</h4>
                    <ul class="space-y-3 text-[10px]">
                        <li><i class="fa-solid fa-location-dot text-emerald-400 w-5"></i> Kota Depok, Jawa Barat, Indonesia</li>
                        <li><i class="fa-solid fa-envelope text-emerald-400 w-5"></i> hello@smartpath.id</li>
                        <li><i class="fa-solid fa-phone text-emerald-400 w-5"></i> (021) 1234 5678</li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="pt-5 flex flex-col sm:flex-row justify-between gap-3 text-[9px] text-slate-500">
                <span>© 2026 SmartPath. Semua hak dilindungi.</span>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-emerald-400 transition">Kebijakan Privasi</a>
                    <a href="#" class="hover:text-emerald-400 transition">Syarat & Ketentuan →</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ===== SCRIPTS ===== -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // ================================
        // SCROLL PROGRESS
        // ================================
        window.addEventListener('scroll', () => {
            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const progress = (scrollTop / docHeight) * 100;
            document.getElementById('scrollProgress').style.width = progress + '%';
        });

        // ================================
        // DARK MODE TOGGLE
        // ================================
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');

        function setTheme(theme) {
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                themeIcon.className = 'fa-solid fa-sun';
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                themeIcon.className = 'fa-solid fa-moon';
                localStorage.setItem('theme', 'light');
            }
        }

        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
            setTheme('dark');
        } else {
            setTheme('light');
        }

        themeToggle.addEventListener('click', () => {
            const isDark = document.documentElement.classList.contains('dark');
            setTheme(isDark ? 'light' : 'dark');
        });

        // ================================
        // MOBILE MENU
        // ================================
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('open');
            const icon = mobileMenuBtn.querySelector('i');
            if (mobileMenu.classList.contains('open')) {
                icon.className = 'fa-solid fa-xmark';
            } else {
                icon.className = 'fa-solid fa-bars';
            }
        });

        // ================================
        // NAVBAR ACTIVE LINK (GARIS BAWAH BERPINDAH)
        // ================================
        const navLinks = document.querySelectorAll('.nav-link');

        function removeActiveClass() {
            navLinks.forEach(link => {
                link.classList.remove('text-white', 'border-b-2', 'border-emerald-400', 'pb-5');
                link.classList.add('text-slate-300');
            });
        }

        function setActiveLink(activeLink) {
            removeActiveClass();
            activeLink.classList.remove('text-slate-300');
            activeLink.classList.add('text-white', 'border-b-2', 'border-emerald-400', 'pb-5');
        }

        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                setActiveLink(this);
            });
        });

        // Pindahkan menu aktif mengikuti section yang sedang terlihat.
        const sectionLinks = document.querySelectorAll('.nav-link[data-section]');
        const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;

                const activeLink = document.querySelector(`.nav-link[data-section="${entry.target.id}"]`);
                if (activeLink) setActiveLink(activeLink);
            });
        }, {
            rootMargin: '-25% 0px -60% 0px',
            threshold: 0
        });

        sectionLinks.forEach(link => {
            const section = document.getElementById(link.dataset.section);
            if (section) sectionObserver.observe(section);
        });

        // ================================
        // SMARTPATH MAP
        // ================================
        const map = L.map('smartpath-map', { zoomControl: false }).setView([-6.4025, 106.7942], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        const reports = [
            {lat:-6.4025,lng:106.7942,title:'Trotoar Rusak',location:'Jl. Margonda Raya, Depok',status:'Pending',color:'#f59e0b',time:'2 jam lalu'},
            {lat:-6.4100,lng:106.8000,title:'Guiding Block Rusak',location:'Jl. Ciliwung, Depok',status:'Pending',color:'#f59e0b',time:'4 jam lalu'},
            {lat:-6.3950,lng:106.7880,title:'Ramp Tidak Tersedia',location:'Jl. Kalimantan, Depok',status:'Pending',color:'#f59e0b',time:'6 jam lalu'},
            {lat:-6.3915,lng:106.8218,title:'Akses Kursi Roda',location:'Jl. Juanda, Depok',status:'Diverifikasi',color:'#2dd4bf',time:'5 jam lalu'},
            {lat:-6.3800,lng:106.8100,title:'Trotoar Rusak',location:'Jl. Sumatra, Depok',status:'Diverifikasi',color:'#2dd4bf',time:'1 hari lalu'},
            {lat:-6.4180,lng:106.8250,title:'Guiding Block Rusak',location:'Jl. Papua, Depok',status:'Diverifikasi',color:'#2dd4bf',time:'2 hari lalu'},
            {lat:-6.4280,lng:106.8050,title:'Ramp Tidak Tersedia',location:'Jl. Kartini, Depok',status:'Dalam Perbaikan',color:'#60a5fa',time:'2 hari lalu'},
            {lat:-6.4120,lng:106.7920,title:'Trotoar Rusak',location:'Jl. Pahlawan, Depok',status:'Dalam Perbaikan',color:'#60a5fa',time:'3 hari lalu'},
            {lat:-6.3855,lng:106.7920,title:'Guiding Block Rusak',location:'Jl. Nusantara, Depok',status:'Selesai',color:'#34d399',time:'3 hari lalu'},
            {lat:-6.4050,lng:106.8150,title:'Akses Kursi Roda',location:'Jl. Diponegoro, Depok',status:'Selesai',color:'#34d399',time:'5 hari lalu'},
            {lat:-6.4170,lng:106.8320,title:'Trotoar Rusak',location:'Jl. Raya Sawangan, Depok',status:'Ditolak',color:'#ef4444',time:'1 hari lalu'},
            {lat:-6.3980,lng:106.8200,title:'Ramp Tidak Tersedia',location:'Jl. Merdeka, Depok',status:'Ditolak',color:'#ef4444',time:'2 hari lalu'}
        ];

        reports.forEach(report => {
            const marker = L.marker([report.lat, report.lng], { icon: L.divIcon({
                className: 'custom-pin-icon',
                html: `<i class="fa-solid fa-location-dot text-2xl" style="color: ${report.color};"></i>`,
                iconSize: [24, 24],
                iconAnchor: [12, 24],
                popupAnchor: [0, -24]
            })}).addTo(map);

            marker.bindPopup(`
                <div style="font-family:Inter,sans-serif;padding:4px">
                    <b style="font-size:13px">${report.title}</b>
                    <div style="font-size:10px;color:#64748b;margin-top:5px">${report.location}</div>
                    <div style="margin-top:7px;font-size:9px;font-weight:700;color:${report.color}">${report.status}</div>
                    <div style="font-size:9px;color:#94a3b8;margin-top:3px">${report.time}</div>
                </div>
            `);
        });

        document.getElementById('mapSearch').addEventListener('keydown', function(e) {
            if (e.key !== 'Enter') return;
            const q = this.value.trim().toLowerCase();
            if (!q) return;

            const found = reports.find(r =>
                r.title.toLowerCase().includes(q) ||
                r.location.toLowerCase().includes(q)
            );

            if (found) {
                map.setView([found.lat, found.lng], 16, {animate:true, duration:1});
                map.eachLayer(layer => {
                    if (layer.getLatLng && 
                        Math.abs(layer.getLatLng().lat - found.lat) < 0.001 && 
                        Math.abs(layer.getLatLng().lng - found.lng) < 0.001) {
                        layer.openPopup();
                    }
                });
            } else {
                this.value = '';
                this.placeholder = '❌ Lokasi tidak ditemukan';
                this.style.borderColor = '#ef4444';
                setTimeout(() => {
                    this.placeholder = 'Cari lokasi di peta...';
                    this.style.borderColor = 'inherit';
                }, 2000);
            }
        });

        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.getAttribute('data-target'));
                    const duration = 2000;
                    const increment = target / (duration / 16);
                    let current = 0;

                    const updateCounter = () => {
                        current += increment;
                        if (current < target) {
                            el.textContent = Math.round(current);
                            requestAnimationFrame(updateCounter);
                        } else {
                            el.textContent = target;
                        }
                    };
                    updateCounter();
                    counterObserver.unobserve(el);
                }
            });
        }, { threshold: 0.3 });

        document.querySelectorAll('.animated-counter').forEach(el => {
            counterObserver.observe(el);
        });

        const scrollObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -50px 0px' });

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            scrollObserver.observe(el);
        });
    </script>

</body>
</html>