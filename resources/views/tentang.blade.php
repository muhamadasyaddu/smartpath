<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang SmartPath - Teknologi untuk Ruang Publik Inklusif</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ============================================================
           SMARTPATH - ABOUT PAGE STYLES
           ============================================================ */

        /* ---------- ROOT VARIABLES ---------- */
        :root {
            --dark-teal: #062a25;
            --dark-green: #004b40;
            --primary-mint: #10b981;
            --primary-mint-dark: #059669;
            --primary-mint-light: #34d399;
            --light-mint: #d1fae5;
            --white: #FFFFFF;
            --light-bg: #f8fafc;
            --dark-text: #172033;
            --secondary-text: #64748b;
            --footer-bg: #0d1f2b;
            --card-radius: 16px;
            --btn-radius: 12px;
            --shadow-sm: 0 2px 8px rgba(6, 42, 37, 0.06);
            --shadow-md: 0 8px 24px rgba(6, 42, 37, 0.08);
            --shadow-lg: 0 16px 48px rgba(6, 42, 37, 0.12);
            --transition: all 0.3s ease;
        }

        /* ---------- BASE ---------- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--dark-text);
            background-color: var(--white);
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            color: var(--dark-text);
        }

        p {
            color: var(--secondary-text);
            line-height: 1.7;
        }

        a {
            text-decoration: none;
            transition: var(--transition);
        }

        a:focus-visible,
        button:focus-visible {
            outline: 3px solid currentColor;
            outline-offset: 3px;
        }

        .skip-link {
            position: absolute;
            top: -9999px;
            left: 1rem;
            z-index: 1100;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            background: #047857;
            color: #fff;
            font-weight: 700;
        }

        .skip-link:focus {
            top: 1rem;
        }

        .skip-link:focus-visible {
            outline-color: #047857;
        }

        /* ---------- SECTION LABEL ---------- */
        .section-label {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .label-line {
            display: inline-block;
            width: 32px;
            height: 2px;
            background-color: var(--primary-mint);
            border-radius: 2px;
        }

        .label-text {
            font-size: 1.0rem;
            font-weight: 700;
            letter-spacing: 2px;
            color: #047857;
            text-transform: uppercase;
        }

        .hero-about .label-text {
            color: #6ee7b7;
        }

        /* ---------- SECTION TITLE & DESC ---------- */
        .section-title {
            font-size: 2.25rem;
            font-weight: 800;
            line-height: 1.2;
            color: var(--dark-text);
        }

        .section-desc {
            font-size: 1rem;
            color: var(--secondary-text);
            max-width: 600px;
        }

        .text-mint {
            color: #6ee7b7 !important;
        }

        .text-light-muted {
            color: rgba(255, 255, 255, 0.75) !important;
        }

        .text-justify {
            text-align: justify;
        }

        /* ============================================================
           NAVBAR (Bootstrap 5)
           ============================================================ */
        #mainNavbar {
            background-color: rgba(6, 42, 37, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 0.75rem 0;
            transition: var(--transition);
            border-bottom: 1px solid rgba(16, 185, 129, 0.1);
        }

        #mainNavbar.scrolled {
            background-color: rgba(6, 42, 37, 0.98);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #34d399, #059669);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark-teal);
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .logo-text {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--white);
            letter-spacing: -0.5px;
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            font-size: 1.0rem;
            padding: 0.5rem 1rem;
            position: relative;
            transition: var(--transition);
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-mint-light);
        }

        .navbar-nav .nav-link.active {
            color: var(--primary-mint-light);
            font-weight: 600;
        }

        .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 1rem;
            right: 1rem;
            height: 2px;
            background-color: var(--primary-mint-light);
            border-radius: 2px;
        }

        @media (min-width: 992px) {
            .navbar-expand-lg .navbar-nav {
                gap: 2rem;
            }

            .navbar-expand-lg .navbar-nav .nav-link {
                padding-right: 0;
                padding-left: 0;
            }

            .navbar-expand-lg .navbar-nav .nav-link.active::after {
                left: 0;
                right: 0;
            }
        }

        .navbar-toggler {
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: var(--white);
            padding: 0.4rem 0.6rem;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,0.8)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* ---------- BUTTONS ---------- */
        .btn-primary-custom {
            background-color: #047857;
            color: var(--white);
            font-weight: 700;
            font-size: 1.0rem;
            padding: 0.6rem 1.5rem;
            border-radius: var(--btn-radius);
            border: none;
            transition: var(--transition);
        }

        .btn-primary-custom:hover {
            background-color: #065f46;
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
        }

        .btn-outline-light-custom {
            background-color: transparent;
            color: var(--white);
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.6rem 1.5rem;
            border-radius: var(--btn-radius);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: var(--transition);
        }

        .btn-outline-light-custom:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--white);
            border-color: rgba(52, 211, 153, 0.5);
        }

        .btn-dark-mode {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--white);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--btn-radius);
            padding: 0.6rem 0.8rem;
            transition: var(--transition);
        }

        .btn-dark-mode:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: var(--primary-mint-light);
        }

        /* ============================================================
           SECTION 1: HERO ABOUT
           ============================================================ */
        .hero-about {
            background: linear-gradient(135deg, var(--dark-teal) 0%, var(--dark-green) 100%);
            position: relative;
            overflow: hidden;
            padding-top: 80px;
            padding-bottom: 50px;
        }

        .hero-about::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.10) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-about::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.06) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-title {
            font-size: 2.75rem;
            font-weight: 800;
            color: var(--white);
            line-height: 1.15;
            letter-spacing: -1px;
        }

        .hero-desc {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.75);
            max-width: 520px;
            text-align: justify;
        }

        .hero-cards .info-card {
            background-color: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(16, 185, 129, 0.18);
            border-radius: var(--card-radius);
            padding: 1.25rem 1rem;
            transition: var(--transition);
            height: 100%;
        }

        .hero-cards .info-card:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(52, 211, 153, 0.4);
            transform: translateY(-4px);
        }

        .hero-cards .card-icon {
            width: 40px;
            height: 40px;
            background-color: rgba(16, 185, 129, 0.18);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-mint-light);
            font-size: 1rem;
            margin-bottom: 0.75rem;
        }

        .hero-cards .card-title {
            color: var(--white);
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
        }

        .hero-cards .card-text {
            color: rgba(255, 255, 255, 0.65);
            font-size: 1.0;
            line-height: 1.5;
            margin: 0;
        }

        .hero-image {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .image-wrapper {
            position: relative;
            max-width: 520px;
            width: 100%;
        }

        .main-image {
            border-radius: 20px;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.3);
            width: 100%;
            height: auto;
            object-fit: cover;
            aspect-ratio: 4/3;
        }

        .floating-card {
            position: absolute;
            bottom: 24px;
            left: -20px;
            background-color: var(--white);
            border-radius: 14px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
            animation: floatCard 3s ease-in-out infinite;
        }

        @keyframes floatCard {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .floating-icon {
            width: 40px;
            height: 40px;
            background-color: var(--light-mint);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark-teal);
            font-size: 1.1rem;
        }

        .floating-card h6 {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--dark-text);
            margin: 0;
        }

        .floating-card p {
            font-size: 1.0rem;
            color: var(--secondary-text);
            margin: 0;
        }

        /* ============================================================
           SECTION 2: APA ITU SMARTPATH
           ============================================================ */
        .section-about-what {
            background-color: var(--white);
        }

        .visual-wrapper img {
            border-radius: 20px;
            box-shadow: var(--shadow-md);
            width: 100%;
            height: auto;
            object-fit: cover;
            aspect-ratio: 4/3;
        }

        /* ============================================================
           SECTION 3: MENGAPA SMARTPATH
           ============================================================ */
        .section-why {
            background-color: var(--light-bg);
        }

        .problem-card {
            background-color: var(--white);
            border-radius: var(--card-radius);
            padding: 2rem 1.5rem;
            height: 100%;
            transition: var(--transition);
            border: 1px solid rgba(6, 42, 37, 0.06);
            box-shadow: var(--shadow-sm);
        }

        .problem-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-md);
            border-color: rgba(16, 185, 129, 0.25);
        }

        .problem-card .card-icon {
            width: 48px;
            height: 48px;
            background-color: var(--light-mint);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-mint-dark);
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .problem-card h5 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--dark-text);
            margin-bottom: 0.5rem;
        }

        .problem-card p {
            font-size: 1.0rem;
            color: var(--secondary-text);
            margin: 0;
        }

        /* ============================================================
           SECTION 4: SOLUSI SMARTPATH
           ============================================================ */
        .section-solution {
            background: linear-gradient(135deg, var(--dark-teal) 0%, var(--dark-green) 100%);
            position: relative;
            overflow: hidden;
        }

        .section-solution::before {
            content: '';
            position: absolute;
            top: -30%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .solution-flow {
            position: relative;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 22px;
            align-items: stretch;
        }

        .solution-card {
            position: relative;
            background: rgba(255, 255, 255, 0.055);
            border: 1px solid rgba(110, 231, 183, 0.22);
            border-radius: 18px;
            padding: 24px 20px;
            min-height: 230px;
            display: flex;
            flex-direction: column;
            transition: transform .3s ease, border-color .3s ease, background .3s ease, box-shadow .3s ease;
        }

        .solution-card:hover {
            transform: translateY(-6px);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(52, 211, 153, 0.55);
            box-shadow: 0 18px 40px rgba(0, 0, 0, .18);
        }

        .solution-icon {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            background: rgba(16, 185, 129, .14);
            border: 1px solid rgba(16, 185, 129, .18);
            color: var(--primary-mint-light);
            font-size: 20px;
            margin-bottom: 18px;
        }

        .solution-number {
            position: absolute;
            top: 16px;
            right: 18px;
            width: 27px;
            height: 27px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--primary-mint-light);
            color: var(--dark-teal);
            font-size: 11px;
            font-weight: 800;
        }

        .solution-card h3 {
            margin: 0;
            color: #ffffff;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -.01em;
        }

        .solution-card p {
            margin-top: 10px;
            color: rgba(236, 253, 245, .72);
            font-size: 11px;
            line-height: 1.75;
            text-align: justify;
        }

        .solution-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-mint-light);
            font-size: 17px;
            z-index: 5;
            pointer-events: none;
        }

        .solution-arrow.arrow-1 { left: calc(25% - 15px); }
        .solution-arrow.arrow-2 { left: calc(50% - 15px); }
        .solution-arrow.arrow-3 { left: calc(75% - 15px); }

        /* ============================================================
           SECTION 5: NILAI UTAMA
           ============================================================ */
        .section-values {
            background-color: var(--white);
        }

        .value-card {
            background-color: var(--white);
            border: 1px solid rgba(6, 42, 37, 0.08);
            border-radius: var(--card-radius);
            padding: 2rem 1.5rem;
            height: 100%;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .value-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-md);
            border-color: rgba(16, 185, 129, 0.3);
        }

        .value-card .card-icon {
            width: 48px;
            height: 48px;
            background-color: var(--light-mint);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-mint-dark);
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .value-card h5 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--dark-text);
            margin-bottom: 0.5rem;
        }

        .value-card p {
            font-size: 1.0rem;
            color: var(--secondary-text);
            margin: 0;
        }

        
        

        .benefit-card {
            background-color: var(--white);
            border-radius: var(--card-radius);
            padding: 2rem 1.5rem;
            height: 100%;
            transition: var(--transition);
            border: 1px solid rgba(6, 42, 37, 0.06);
            box-shadow: var(--shadow-sm);
        }

        .benefit-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-md);
        }

        .benefit-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1.25rem;
        }

        .benefit-header .card-icon {
            width: 44px;
            height: 44px;
            background-color: var(--light-mint);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-mint-dark);
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .benefit-header h5 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark-text);
            margin: 0;
        }

        .benefit-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .benefit-list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 1.0rem;
            color: var(--secondary-text);
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(6, 42, 37, 0.04);
        }

        .benefit-list li:last-child {
            border-bottom: none;
        }

        .benefit-list li i {
            color: var(--primary-mint);
            font-size: 1.0rem;
            margin-top: 4px;
            flex-shrink: 0;
        }

        /* ============================================================
           SECTION 7: VISI
           ============================================================ */
        .section-vision {
            background: linear-gradient(135deg, var(--dark-teal) 0%, var(--dark-green) 100%);
            position: relative;
            overflow: hidden;
        }

        .section-vision::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* ============================================================
           FOOTER
           ============================================================ */
        .footer {
            background-color: var(--footer-bg);
            border-top: 1px solid rgba(16, 185, 129, 0.1);
        }

        .footer-brand .logo-text {
            color: var(--white);
        }

        .footer-desc {
            color: rgba(255, 255, 255, 0.55);
            font-size: 1.0rem;
            max-width: 280px;
        }

        .footer-title {
            color: var(--white);
            font-size: 1.0rem;
            font-weight: 700;
            margin-bottom: 1rem;
            letter-spacing: 0.5px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 0.5rem;
            color: rgba(255, 255, 255, 0.55);
            font-size: 1.0rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.55);
            font-size: 1.0rem;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: var(--primary-mint-light);
            padding-left: 4px;
        }

        .footer-links li i {
            color: var(--primary-mint-light);
            font-size: 0.8rem;
            width: 16px;
        }

        .social-links {
            display: flex;
            gap: 12px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
            transition: var(--transition);
        }

        .social-links a:hover {
            background-color: var(--primary-mint-dark);
            color: var(--white);
            transform: translateY(-3px);
        }

        .footer-divider {
            border-color: rgba(255, 255, 255, 0.08);
        }

        .footer-copy {
            color: rgba(255, 255, 255, 0.5);
            font-size: 1rem;
        }

        .footer-tagline {
            color: var(--primary-mint-light);
            font-size: 1rem;
            font-weight: 600;
        }

        .footer-tagline:hover {
            color: var(--primary-mint-light);
            text-decoration: underline;
        }

        /* ============================================================
           DARK MODE
           ============================================================ */
        [data-bs-theme="dark"] body {
            background-color: #0f172a;
            color: #f1f5f9;
        }

        [data-bs-theme="dark"] h1,
        [data-bs-theme="dark"] h2,
        [data-bs-theme="dark"] h3,
        [data-bs-theme="dark"] h4,
        [data-bs-theme="dark"] h5,
        [data-bs-theme="dark"] h6 {
            color: #f1f5f9;
        }

        [data-bs-theme="dark"] p {
            color: #94a3b8;
        }

        [data-bs-theme="dark"] .section-about-what,
        [data-bs-theme="dark"] .section-values {
            background-color: #0f172a;
        }

        [data-bs-theme="dark"] .section-why,
        [data-bs-theme="dark"] .section-benefits {
            background-color: #020617;
        }

        [data-bs-theme="dark"] .problem-card,
        [data-bs-theme="dark"] .value-card,
        [data-bs-theme="dark"] .benefit-card {
            background-color: #1e293b;
            border-color: #334155;
        }

        [data-bs-theme="dark"] .problem-card h5,
        [data-bs-theme="dark"] .value-card h5,
        [data-bs-theme="dark"] .benefit-header h5 {
            color: #f1f5f9;
        }

        [data-bs-theme="dark"] .problem-card p,
        [data-bs-theme="dark"] .value-card p,
        [data-bs-theme="dark"] .benefit-list li {
            color: #94a3b8;
        }

        [data-bs-theme="dark"] .floating-card {
            background-color: #1e293b;
        }

        [data-bs-theme="dark"] .floating-card h6 {
            color: #f1f5f9;
        }

        [data-bs-theme="dark"] .floating-card p {
            color: #94a3b8;
        }

        [data-bs-theme="dark"] .section-title {
            color: #f1f5f9;
        }

        [data-bs-theme="dark"] .section-desc {
            color: #94a3b8;
        }

        [data-bs-theme="dark"] .footer {
            background-color: #020617;
        }

        /* ============================================================
           RESPONSIVE
           ============================================================ */

        /* Tablet */
        @media (max-width: 991.98px) {
            .hero-title {
                font-size: 2.25rem;
            }

            .section-title {
                font-size: 1.875rem;
            }

            .hero-image {
                margin-top: 3rem;
            }

            .floating-card {
                left: 10px;
                bottom: 16px;
            }

            .solution-flow {
                grid-template-columns: repeat(2, 1fr);
            }

            .solution-arrow {
                display: none;
            }

            .navbar-nav .nav-link.active::after {
                display: none;
            }
        }

        /* Mobile */
        @media (max-width: 767.98px) {
            .hero-title {
                font-size: 1.875rem;
            }

            .section-title {
                font-size: 1.625rem;
            }

            .hero-about .row {
                min-height: auto !important;
                padding-top: 100px;
                padding-bottom: 60px;
            }

            .hero-cards .col-md-4 {
                margin-bottom: 0.5rem;
            }

            .floating-card {
                position: relative;
                left: 0;
                bottom: 0;
                margin-top: -30px;
                margin-left: 16px;
                margin-right: 16px;
                animation: none;
            }

            .footer .row > div {
                text-align: center;
            }

            .footer-desc {
                margin: 0 auto;
            }

            .social-links {
                justify-content: center;
            }

            .footer-links {
                text-align: center;
            }

            .footer-brand {
                justify-content: center;
            }

            .solution-flow {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .solution-card {
                min-height: auto;
                padding: 20px;
            }

            .solution-card p {
                text-align: left;
            }
        }

        /* Small Mobile */
        @media (max-width: 575.98px) {
            .hero-title {
                font-size: 1.625rem;
            }

            .section-title {
                font-size: 1.375rem;
            }

            .hero-desc {
                font-size: 1rem;
            }

            .btn-primary-custom,
            .btn-outline-light-custom {
                padding: 0.5rem 1.25rem;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

    <a href="#main-content" class="skip-link">Langsung ke konten utama</a>

    <!-- ================= NAVBAR (Bootstrap 5) ================= -->
    <nav class="navbar navbar-expand-lg fixed-top" id="mainNavbar" aria-label="Navigasi utama">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('beranda') }}">
                <div class="logo-icon">
                    <i class="fa-solid fa-route"></i>
                </div>
                <span class="logo-text">SmartPath</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Buka atau tutup navigasi">
                <span class="navbar-toggler-icon" aria-hidden="true"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('beranda') }}">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="{{ route('tentang') }}">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('beranda') }}#fitur">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('peta.index') }}">Peta</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('beranda') }}#cara-kerja">Cara Kerja</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('beranda') }}#kontak">Kontak</a></li>
                </ul>

                <div class="d-flex align-items-center gap-2 nav-actions">
                    <a href="{{ route('login') }}" class="btn btn-outline-light-custom">Masuk</a>
                    <a href="{{ route('auth.register') }}" class="btn btn-primary-custom">Daftar</a>
                    <button class="btn btn-dark-mode" id="darkModeToggle" aria-label="Mode gelap" aria-pressed="false">
                        <i class="fa-solid fa-moon" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main id="main-content" tabindex="-1">
    <!-- ================= SECTION 1: HERO TENTANG ================= -->
    <section class="hero-about" id="heroAbout">
        <div class="container">
            <div class="row align-items-center min-vh-100 pt-5">
                <div class="col-lg-6 hero-text" data-aos="fade-up">
                    <div class="section-label mb-3">
                        <span class="label-line"></span>
                        <span class="label-text">TENTANG SMARTPATH</span>
                    </div>
                    <h1 class="hero-title">
                        Teknologi untuk ruang<br>
                        publik yang <span class="text-mint">lebih inklusif.</span>
                    </h1>
                    <p class="hero-desc mt-4">
                        SmartPath adalah platform digital yang menghubungkan masyarakat dan pemerintah untuk menciptakan ruang publik yang lebih aman, mudah diakses, dan inklusif.
                    </p>
                    <p class="hero-desc">
                        Melalui pelaporan, verifikasi, dan pemetaan berbasis data, SmartPath membantu mengidentifikasi berbagai hambatan aksesibilitas di ruang publik agar dapat ditindaklanjuti dengan lebih tepat.
                    </p>

                    <div class="row g-3 mt-4 hero-cards">
                        <div class="col-md-4">
                            <div class="info-card">
                                <div class="card-icon"><i class="fa-solid fa-users"></i></div>
                                <h6 class="card-title">Partisipatif</h6>
                                <p class="card-text">Masyarakat ikut menyampaikan kondisi di lapangan.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-card">
                                <div class="card-icon"><i class="fa-solid fa-map"></i></div>
                                <h6 class="card-title">Berbasis Peta</h6>
                                <p class="card-text">Laporan divisualisasikan agar mudah dipantau.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-card">
                                <div class="card-icon"><i class="fa-solid fa-database"></i></div>
                                <h6 class="card-title">Berbasis Data</h6>
                                <p class="card-text">Data terverifikasi membantu menentukan prioritas perbaikan.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 hero-image" data-aos="fade-left" data-aos-delay="200">
                    <div class="image-wrapper">
                        <img src="foto-tunanetra.png" alt="Aksesibilitas ruang publik" class="img-fluid main-image">
                        <div class="floating-card">
                            <div class="floating-icon"><i class="fa-solid fa-wheelchair"></i></div>
                            <div>
                                <h6>Ruang Publik Inklusif</h6>
                                <p>Akses untuk semua orang.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= SECTION 2: APA ITU SMARTPATH? ================= -->
    <section class="section-about-what py-5" id="whatIs">
        <div class="container py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="section-label mb-3">
                        <span class="label-line"></span>
                        <span class="label-text">TENTANG KAMI</span>
                    </div>
                    <h2 class="section-title">Apa itu SmartPath?</h2>

                    <p class="section-desc mt-3 text-justify">
                        SmartPath merupakan platform digital yang dirancang untuk membantu
                        masyarakat melaporkan berbagai kondisi fasilitas publik yang belum
                        ramah aksesibilitas.
                    </p>

                    <p class="section-desc text-justify">
                        Platform ini memungkinkan laporan mengenai trotoar rusak, ramp yang
                        tidak tersedia, guiding block yang rusak, penerangan yang kurang,
                        maupun hambatan lainnya untuk dikumpulkan dan dipetakan secara digital.
                    </p>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="visual-wrapper">
                        <img src="foto-trotoar.jpeg" alt="Peta digital SmartPath" class="img-fluid rounded-4 shadow-sm">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= SECTION 3: MENGAPA SMARTPATH DIBUTUHKAN? ================= -->
    <section class="section-why py-5" id="whySmartPath">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <div class="section-label justify-content-center mb-3">
                    <span class="label-line"></span>
                    <span class="label-text">KENAPA SMARTPATH?</span>
                </div>
                <h2 class="section-title">Masih banyak ruang publik<br>yang belum ramah aksesibilitas.</h2>
                <p class="section-desc mx-auto" style="max-width: 600px;">
                    Berbagai hambatan sederhana di ruang publik dapat menjadi masalah besar bagi sebagian masyarakat.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="problem-card">
                        <div class="card-icon"><i class="fa-solid fa-road"></i></div>
                        <h5>Trotoar Rusak</h5>
                        <p>Menghambat dan membahayakan perjalanan pejalan kaki.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="problem-card">
                        <div class="card-icon"><i class="fa-solid fa-wheelchair"></i></div>
                        <h5>Ramp Tidak Tersedia</h5>
                        <p>Menyulitkan pengguna kursi roda, lansia, dan ibu hamil.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="problem-card">
                        <div class="card-icon"><i class="fa-solid fa-universal-access"></i></div>
                        <h5>Guiding Block Rusak</h5>
                        <p>Mengganggu mobilitas penyandang tunanetra.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="problem-card">
                        <div class="card-icon"><i class="fa-solid fa-building"></i></div>
                        <h5>Fasilitas Kurang Memadai</h5>
                        <p>Seperti penerangan, akses transportasi, dan fasilitas pendukung lainnya.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= SECTION 4: SOLUSI SMARTPATH ================= -->
    <section class="section-solution py-5" id="solution">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <div class="section-label justify-content-center mb-3">
                    <span class="label-line"></span>
                    <span class="label-text text-mint">SOLUSI SMARTPATH</span>
                </div>
                <h2 class="section-title text-white">Data, partisipasi, dan teknologi<br>untuk perubahan yang nyata.</h2>
                <p class="section-desc text-light-muted mx-auto" style="max-width: 700px;">
                    SmartPath membantu menghubungkan laporan masyarakat dengan data yang dapat digunakan pemerintah sebagai dasar perbaikan infrastruktur.
                </p>
            </div>

            <div class="solution-flow">
                <!-- Panah -->
                <div class="solution-arrow arrow-1"><i class="fa-solid fa-arrow-right"></i></div>
                <div class="solution-arrow arrow-2"><i class="fa-solid fa-arrow-right"></i></div>
                <div class="solution-arrow arrow-3"><i class="fa-solid fa-arrow-right"></i></div>

                <!-- Card 1 -->
                <article class="solution-card">
                    <span class="solution-number">1</span>
                    <div class="solution-icon"><i class="fa-solid fa-file-circle-plus"></i></div>
                    <h3>Laporkan</h3>
                    <p>Masyarakat mengirim laporan berupa foto, lokasi, dan kategori hambatan aksesibilitas yang ditemukan di ruang publik.</p>
                </article>

                <!-- Card 2 -->
                <article class="solution-card">
                    <span class="solution-number">2</span>
                    <div class="solution-icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <h3>Verifikasi</h3>
                    <p>Laporan diperiksa untuk memastikan informasi, kondisi, dan lokasi yang disampaikan dapat dipastikan keakuratannya.</p>
                </article>

                <!-- Card 3 -->
                <article class="solution-card">
                    <span class="solution-number">3</span>
                    <div class="solution-icon"><i class="fa-solid fa-map-location-dot"></i></div>
                    <h3>Petakan</h3>
                    <p>Laporan yang telah terverifikasi ditampilkan pada peta digital sehingga kondisi aksesibilitas dapat dipantau dengan lebih mudah.</p>
                </article>

                <!-- Card 4 -->
                <article class="solution-card">
                    <span class="solution-number">4</span>
                    <div class="solution-icon"><i class="fa-solid fa-chart-column"></i></div>
                    <h3>Tindak Lanjut</h3>
                    <p>Data laporan digunakan sebagai informasi pendukung untuk menentukan prioritas perbaikan aksesibilitas ruang publik.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- ================= SECTION 5: NILAI UTAMA ================= -->
    <section class="section-values py-5" id="values">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <div class="section-label justify-content-center mb-3">
                    <span class="label-line"></span>
                    <span class="label-text">NILAI UTAMA</span>
                </div>
                <h2 class="section-title">Teknologi yang berfokus<br>pada dampak nyata.</h2>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="value-card">
                        <div class="card-icon"><i class="fa-solid fa-users"></i></div>
                        <h5>Partisipatif</h5>
                        <p>Mendorong masyarakat untuk ikut menyampaikan kondisi ruang publik.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="value-card">
                        <div class="card-icon"><i class="fa-solid fa-eye"></i></div>
                        <h5>Transparan</h5>
                        <p>Informasi laporan dapat dikelola dan dipantau dengan lebih terstruktur.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="value-card">
                        <div class="card-icon"><i class="fa-solid fa-chart-simple"></i></div>
                        <h5>Berbasis Data</h5>
                        <p>Data terverifikasi membantu menentukan prioritas perbaikan.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="value-card">
                        <div class="card-icon"><i class="fa-solid fa-hand-holding-heart"></i></div>
                        <h5>Inklusif</h5>
                        <p>Mendorong ruang publik yang dapat digunakan oleh semua orang.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    

    <!-- ================= SECTION 7: VISI SMARTPATH ================= -->
    <section class="section-vision py-5" id="vision">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center" data-aos="fade-up">
                    <h2 class="section-title text-white">Menuju ruang publik<br>yang lebih inklusif.</h2>
                    <p class="section-desc text-light-muted mt-4 mx-auto">
                        Kami percaya bahwa aksesibilitas adalah hak semua orang. Dengan kolaborasi masyarakat, pemerintah, dan teknologi, SmartPath ingin membantu menciptakan lingkungan perkotaan yang lebih aman, nyaman, dan mudah diakses.
                    </p>
                    <div class="d-flex gap-3 justify-content-center mt-5 flex-wrap">
                        <a href="/peta" class="btn btn-primary-custom btn-lg">
                            <i class="fa-solid fa-map me-2"></i>Jelajahi Peta
                        </a>
                        <a href="{{ route('laporan.create') }}" class="btn btn-outline-light-custom btn-lg">
                            <i class="fa-solid fa-bullhorn me-2"></i>Laporkan Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    </main>

    <!-- ================= FOOTER ================= -->
    <footer id="kontak" class="footer pt-5 pb-4">
        <div class="container">
            <div class="row g-4 pb-4 border-bottom footer-divider">
                <div class="col-lg-4 col-md-6">
                    <a class="footer-brand d-flex align-items-center gap-2 mb-3" href="/">
                        <div class="logo-icon"><i class="fa-solid fa-route"></i></div>
                        <span class="logo-text">SmartPath</span>
                    </a>
                    <p class="footer-desc">
                        SmartPath adalah platform partisipatif untuk melaporkan dan memetakan hambatan aksesibilitas di ruang publik.
                    </p>
                    <div class="social-links mt-3">
                        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 col-6">
                    <h6 class="footer-title">Navigasi</h6>
                    <ul class="footer-links">
                        <li><a href="#heroAbout">Beranda</a></li>
                        <li><a href="/about">Tentang</a></li>
                        <li><a href="#whatIs">Fitur</a></li>
                        <li><a href="/peta">Peta</a></li>
                        <li><a href="#solution">Cara Kerja</a></li>
                        <li><a href="#kontak">Kontak</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 col-6">
                    <h6 class="footer-title">Kategori Laporan</h6>
                    <ul class="footer-links">
                        <li><i class="fa-solid fa-road me-2"></i>Trotoar Rusak</li>
                        <li><i class="fa-solid fa-wheelchair me-2"></i>Ramp Tidak Ada</li>
                        <li><i class="fa-solid fa-grip-lines me-2"></i>Guiding Block Rusak</li>
                        <li><i class="fa-solid fa-ellipsis me-2"></i>Lainnya</li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-title">Alamat</h6>
                    <ul class="footer-links">
                        <li><i class="fa-solid fa-location-dot me-2"></i>Kota Depok, Jawa Barat, Indonesia</li>
                        <li><i class="fa-solid fa-envelope me-2"></i>smartpath.official.id@gmail.com</li>
                        <li><i class="fa-solid fa-phone me-2"></i>(021) 1234 5678</li>
                    </ul>
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 pt-4">
                <p class="footer-copy mb-0">&copy; 2026 SmartPath. Semua hak dilindungi.</p>
                <div class="d-flex gap-4">
                    <a href="#" class="footer-tagline">Kebijakan Privasi</a>
                    <a href="#" class="footer-tagline">Syarat &amp; Ketentuan →</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ============================================================
         SMARTPATH - ABOUT PAGE SCRIPTS
         ============================================================ -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /* ============================================================
               NAVBAR SCROLL EFFECT
               ============================================================ */
            const navbar = document.getElementById('mainNavbar');

            function handleNavbarScroll() {
                if (!navbar) return;
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            }

            window.addEventListener('scroll', handleNavbarScroll);
            handleNavbarScroll();

            /* ============================================================
               DARK MODE TOGGLE
               ============================================================ */
            const darkModeToggle = document.getElementById('darkModeToggle');
            const htmlElement = document.documentElement;

            if (darkModeToggle) {
                const icon = darkModeToggle.querySelector('i');

                function updateIcon(theme) {
                    darkModeToggle.setAttribute('aria-pressed', String(theme === 'dark'));
                    if (!icon) return;
                    if (theme === 'dark') {
                        icon.classList.remove('fa-moon');
                        icon.classList.add('fa-sun');
                    } else {
                        icon.classList.remove('fa-sun');
                        icon.classList.add('fa-moon');
                    }
                }

                const savedTheme = localStorage.getItem('smartpath-theme');
                if (savedTheme) {
                    htmlElement.setAttribute('data-bs-theme', savedTheme);
                    updateIcon(savedTheme);
                } else {
                    htmlElement.setAttribute('data-bs-theme', 'light');
                    updateIcon('light');
                }

                darkModeToggle.addEventListener('click', function () {
                    const currentTheme = htmlElement.getAttribute('data-bs-theme');
                    const newTheme = currentTheme === 'light' ? 'dark' : 'light';

                    htmlElement.setAttribute('data-bs-theme', newTheme);
                    localStorage.setItem('smartpath-theme', newTheme);
                    updateIcon(newTheme);
                });
            }

            /* ============================================================
               SMOOTH SCROLLING FOR ANCHOR LINKS
               ============================================================ */
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;

                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        e.preventDefault();
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            /* ============================================================
               FADE-IN ANIMATION ON SCROLL
               ============================================================ */
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('[data-aos]').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });

            /* ============================================================
               CLOSE MOBILE NAVBAR ON LINK CLICK (Bootstrap)
               ============================================================ */
            const navbarCollapse = document.getElementById('navbarNav');

            if (navbarCollapse && typeof bootstrap !== 'undefined') {
                const bsCollapse = new bootstrap.Collapse(navbarCollapse, { toggle: false });
                const navbarToggler = document.querySelector('.navbar-toggler');

                document.addEventListener('keydown', event => {
                    if (event.key === 'Escape' && window.innerWidth < 992 && navbarCollapse.classList.contains('show')) {
                        event.preventDefault();
                        bsCollapse.hide();
                        if (navbarToggler) navbarToggler.focus();
                    }
                });

                document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
                    link.addEventListener('click', () => {
                        if (window.innerWidth < 992) {
                            bsCollapse.hide();
                        }
                    });
                });
            }

            console.log('SmartPath About Page loaded successfully.');
        });
    </script>
</body>
</html>