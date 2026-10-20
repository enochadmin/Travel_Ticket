<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'EEC Travel') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/eec-logo.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Instrument Sans', sans-serif; }

        :root {
            --primary: #0ea5e9;
            --accent: #facc15;
        }

        .hero-bg {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.85)),
                        url('{{ asset('images/landing-bg.jpg') }}');
            background-size: cover;
            background-position: center;
        }

        .nav-blur {
            backdrop-filter: blur(12px);
            background: rgba(0,0,0,0.6);
        }

        .card-hover:hover {
            transform: translateY(-10px);
            transition: 0.3s ease;
        }

        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.8s ease;
        }

        .fade-in.show {
            opacity: 1;
            transform: translateY(0);
        }
        .theme-toggle {
            width: 36px;
            height: 36px;
            border-radius: 9999px;
            border: 1px solid rgba(255,255,255,0.25);
            background: rgba(255,255,255,0.08);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .theme-toggle svg {
            width: 18px;
            height: 18px;
        }

        .theme-toggle .sun { display: none; }
        .light-mode .theme-toggle .sun { display: block; }
        .light-mode .theme-toggle .moon { display: none; }

        .light-mode {
            background: #f8fafc;
            color: #0f172a;
        }

        .light-mode .hero-bg {
            background: linear-gradient(rgba(255,255,255,0.7), rgba(255,255,255,0.9)),
                        url('{{ asset('images/landing-bg.jpg') }}');
            background-size: cover;
            background-position: center;
        }

        .light-mode .nav-blur {
            background: rgba(255,255,255,0.75);
        }

        .light-mode .bg-black { background: #f8fafc !important; }
        .light-mode .bg-zinc-900 { background: #e2e8f0 !important; }
        .light-mode .bg-zinc-800 { background: #f1f5f9 !important; }
        .light-mode .text-gray-400 { color: #475569 !important; }
        .light-mode .text-gray-300 { color: #334155 !important; }
        .light-mode .text-white { color: #0f172a !important; }
        .light-mode .border { border-color: #94a3b8 !important; }
        .light-mode .theme-toggle {
            background: rgba(15,23,42,0.08);
            border-color: rgba(15,23,42,0.2);
        }

        /* ============ New visitor guide (spotlight on Register) ============ */
        .nv-guide {
            position: fixed;
            inset: 0;
            z-index: 60;
            pointer-events: none;
        }

        .nv-guide[hidden] { display: none; }

        .nv-guide.nv-hide .nv-spotlight,
        .nv-guide.nv-hide .nv-ring,
        .nv-guide.nv-hide .nv-bubble {
            opacity: 0;
            transition: opacity .2s ease;
        }

        /* Dims the whole page but leaves a hole over the target button */
        .nv-spotlight {
            position: fixed;
            border-radius: 14px;
            box-shadow: 0 0 0 9999px rgba(2, 6, 23, 0.66);
            transition: left .35s cubic-bezier(.4, 0, .2, 1),
                        top .35s cubic-bezier(.4, 0, .2, 1),
                        width .35s cubic-bezier(.4, 0, .2, 1),
                        height .35s cubic-bezier(.4, 0, .2, 1);
            pointer-events: none;
        }

        .nv-ring {
            position: fixed;
            overflow: visible;
            pointer-events: none;
            filter: drop-shadow(0 0 3px rgba(250, 204, 21, .5));
        }

        .nv-ring ellipse {
            fill: none;
            stroke: #facc15;
            stroke-width: 2.6;
            stroke-linecap: round;
        }

        .nv-ring.nv-pulse {
            animation: nv-ring-pulse 2.4s ease-in-out infinite;
        }

        @keyframes nv-ring-pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50%      { transform: scale(1.05); opacity: .72; }
        }

        .nv-bubble {
            position: fixed;
            width: 292px;
            max-width: calc(100vw - 24px);
            pointer-events: auto;
            border-radius: 16px;
            padding: 15px 16px 14px;
            background: #0b1220;
            border: 1px solid rgba(250, 204, 21, .38);
            box-shadow: 0 20px 48px rgba(2, 6, 23, .55), inset 0 1px 0 rgba(255, 255, 255, .05);
            animation: nv-bubble-in .32s cubic-bezier(.34, 1.4, .64, 1) both;
        }

        @keyframes nv-bubble-in {
            from { opacity: 0; transform: translateY(-8px) scale(.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .nv-arrow {
            position: absolute;
            top: -7px;
            width: 13px;
            height: 13px;
            background: #0b1220;
            border-left: 1px solid rgba(250, 204, 21, .38);
            border-top: 1px solid rgba(250, 204, 21, .38);
            transform: translateX(-50%) rotate(45deg);
            border-top-left-radius: 3px;
        }

        .nv-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #facc15;
        }

        .nv-eyebrow .nv-dot {
            width: 6px;
            height: 6px;
            border-radius: 9999px;
            background: #facc15;
            animation: nv-dot 1.8s ease-in-out infinite;
        }

        @keyframes nv-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%      { opacity: .35; transform: scale(.75); }
        }

        .nv-title {
            margin-top: 6px;
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -.01em;
        }

        .nv-copy {
            margin-top: 4px;
            font-size: 12.5px;
            line-height: 1.5;
            color: #94a3b8;
        }

        .nv-actions {
            margin-top: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nv-cta {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #0ea5e9;
            color: #ffffff;
            font-size: 12.5px;
            font-weight: 600;
            padding: 8px 12px;
            border-radius: 9px;
            transition: background .18s ease, transform .18s ease;
        }

        .nv-cta:hover { background: #0284c7; transform: translateY(-1px); }

        .nv-dismiss {
            background: none;
            border: 0;
            padding: 4px 2px;
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            cursor: pointer;
        }

        .nv-dismiss:hover { color: #e2e8f0; }

        .nv-close {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #64748b;
            font-size: 16px;
            line-height: 1;
            cursor: pointer;
        }

        .nv-close:hover { background: rgba(148, 163, 184, .15); color: #e2e8f0; }

        /* Gentle shimmer left on the button after the guide is dismissed */
        .nv-shimmer::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(100deg, transparent 32%, rgba(255, 255, 255, .5) 50%, transparent 68%);
            transform: translateX(-130%);
            animation: nv-sweep 3.4s ease-in-out 3;
            pointer-events: none;
        }

        @keyframes nv-sweep {
            0%        { transform: translateX(-130%); }
            55%, 100% { transform: translateX(130%); }
        }

        @media (prefers-reduced-motion: reduce) {
            .nv-ring.nv-pulse,
            .nv-eyebrow .nv-dot,
            .nv-shimmer::after,
            .nv-bubble { animation: none !important; }
        }
    </style>
</head>

<body class="bg-black text-white">

<!-- NAVBAR -->
<nav id="navbar" class="fixed w-full z-50 transition-all">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/eec-logo.png') }}" class="h-8">
            <span class="font-bold text-xl">EEC Travel</span>
        </div>

        <!-- Desktop -->
        <div class="hidden md:flex gap-8 items-center">
            <a href="#features">Features</a>
            <a href="#how">How it works</a>

            @auth
                <a href="/dashboard" class="bg-white text-black px-6 py-2 rounded-xl">Dashboard</a>
            @else
                <a href="/login">Login</a>
                <a href="/register" id="navRegister" class="relative overflow-hidden bg-sky-500 px-6 py-2 rounded-xl">Register</a>
            @endauth
        </div>
        <!-- Theme Toggle -->
        <button id="themeToggle" class="theme-toggle" aria-label="Toggle theme">
            <svg class="moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"></path>
            </svg>
            <svg class="sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="4"></circle>
                <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
            </svg>
        </button>
        <!-- Mobile -->
        <button id="navMenuToggle" type="button" onclick="toggleMenu()" class="md:hidden text-2xl"
            aria-label="Open navigation menu" aria-controls="mobileMenu" aria-expanded="false">☰</button>
    </div>

    <!-- Mobile menu -->
    <div id="mobileMenu" class="hidden flex-col bg-black px-6 pb-4 md:hidden">
        <a href="#features" class="py-2">Features</a>
        <a href="#how" class="py-2">How it works</a>

        @auth
            <a href="/dashboard" class="py-2">Dashboard</a>
        @else
            <a href="/login" class="py-2">Login</a>
            <a href="/register" class="py-2">Register</a>
        @endauth
    </div>
</nav>

<!-- HERO -->
<section class="hero-bg min-h-screen flex items-center text-center px-6">
    <div class="max-w-4xl mx-auto">
        
        <h1 class="text-4xl md:text-6xl font-bold leading-tight">
            Travel Requests <br>
            <span class="text-sky-400">Made Simple</span>
        </h1>

        <p class="mt-6 text-lg text-gray-300">
            Submit, track, and approve travel requests seamlessly across EEC.
        </p>

        <div class="mt-8 flex flex-col md:flex-row gap-4 justify-center">
            <a href="/login" class="bg-white text-black px-8 py-4 rounded-xl font-semibold">
                Get Started
            </a>
            <a href="#features" class="border px-8 py-4 rounded-xl">
                Learn More
            </a>
        </div>
    </div>
</section>

<!-- STATS -->
<section class="py-16 bg-zinc-900 text-center">
    <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-8">
        <div>
            <h2 class="text-4xl font-bold text-sky-400"> <span class="stat-count" data-target="{{ $employeeCount }}" data-suffix="+">0</span> 
        </h2>
            <p class="text-gray-400">Employees</p>
        </div>
        <div>
            <h2 class="text-4xl font-bold text-amber-400">  <span class="stat-count" data-target="{{ $requestCount }}" data-suffix="+">0</span>        
        </h2>
            <p class="text-gray-400">Requests Processed</p>
        </div>
        <div>
            <h2 class="text-4xl font-bold text-green-400"> <span class="stat-count" data-target="99" data-suffix="%">0</span> 
        </h2>
            <p class="text-gray-400">Approval Accuracy</p>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section id="features" class="py-20 px-6">
    <div class="max-w-7xl mx-auto text-center">
        <h2 class="text-4xl font-bold mb-12">Features</h2>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-zinc-800 p-8 rounded-2xl card-hover">
                ✈️
                <h3 class="text-xl font-semibold mt-4">Fast Requests</h3>
                <p class="text-gray-400 mt-2">Submit requests in seconds</p>
            </div>

            <div class="bg-zinc-800 p-8 rounded-2xl card-hover">
                ✅
                <h3 class="text-xl font-semibold mt-4">Approvals</h3>
                <p class="text-gray-400 mt-2">Smart approval workflows</p>
            </div>

            <div class="bg-zinc-800 p-8 rounded-2xl card-hover">
                📊
                <h3 class="text-xl font-semibold mt-4">Reports</h3>
                <p class="text-gray-400 mt-2">Real-time analytics</p>
            </div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section id="how" class="py-20 bg-zinc-900 px-6">
    <div class="max-w-6xl mx-auto text-center">
        <h2 class="text-4xl font-bold mb-12">How It Works</h2>

        <div class="grid md:grid-cols-3 gap-10">
            <div>
                <h3 class="text-xl font-semibold">1. Submit</h3>
                <p class="text-gray-400 mt-2">Create travel request</p>
            </div>

            <div>
                <h3 class="text-xl font-semibold">2. Approve</h3>
                <p class="text-gray-400 mt-2">Managers review instantly</p>
            </div>

            <div>
                <h3 class="text-xl font-semibold">3. Travel</h3>
                <p class="text-gray-400 mt-2">Get tickets & go</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-20 text-center">
    <h2 class="text-3xl font-bold">Start your journey today</h2>

    <div class="mt-6">
        <a href="/register" class="bg-sky-500 px-10 py-4 rounded-xl text-lg">
            Create Account
        </a>
    </div>
</section>

<!-- FOOTER -->
<footer class="py-10 text-center text-gray-500 text-sm">
    © {{ date('Y') }} EEC Travel System
</footer>

@guest
{{-- First-look guide: dims the page and circles the Register button once per visitor --}}
<div id="nvGuide" class="nv-guide" hidden aria-hidden="true">
    <div id="nvSpot" class="nv-spotlight"></div>

    <svg id="nvRing" class="nv-ring" aria-hidden="true">
        <ellipse id="nvRingPath" cx="0" cy="0" rx="0" ry="0"></ellipse>
    </svg>

    <div id="nvBubble" class="nv-bubble" role="status" aria-live="polite">
        <span class="nv-arrow" id="nvArrow"></span>
        <button type="button" class="nv-close" id="nvClose" aria-label="Dismiss registration hint">&times;</button>

        <span class="nv-eyebrow"><span class="nv-dot"></span>New here?</span>
        <p class="nv-title">Create your account</p>
        <p class="nv-copy">Registration takes under a minute — then submit and track your travel requests from anywhere.</p>

        <div class="nv-actions">
            <a href="/register" class="nv-cta">
                Register now
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14M13 6l6 6-6 6"></path>
                </svg>
            </a>
            <button type="button" class="nv-dismiss" id="nvLater">Maybe later</button>
        </div>
    </div>
</div>
@endguest

<!-- JS -->
<script>
    function toggleMenu() {
        const menu = document.getElementById('mobileMenu');
        const toggle = document.getElementById('navMenuToggle');
        const isOpen = menu.classList.toggle('hidden') === false;

        toggle.setAttribute('aria-expanded', String(isOpen));
        toggle.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
    }

    function setTheme(isLight) {
        document.body.classList.toggle('light-mode', isLight);
        localStorage.setItem('theme', isLight ? 'light' : 'dark');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') setTheme(true);

        const themeToggle = document.getElementById('themeToggle');
        themeToggle.addEventListener('click', () => {
            const isLight = document.body.classList.contains('light-mode');
            setTheme(!isLight);
        });
    });
    // navbar blur on scroll
    window.addEventListener('scroll', function () {
        let nav = document.getElementById('navbar');
        if (window.scrollY > 50) {
            nav.classList.add('nav-blur');
        } else {
            nav.classList.remove('nav-blur');
        }
    });

    // scroll animation
    const elements = document.querySelectorAll('.fade-in');
    window.addEventListener('scroll', () => {
        elements.forEach(el => {
            if (el.getBoundingClientRect().top < window.innerHeight - 50) {
                el.classList.add('show');
            }
        });
    });

    function animateCount(el) {
        const target = parseInt(el.dataset.target, 10);
        const suffix = el.dataset.suffix || '';
        const duration = 1600;
        const startTime = performance.now();

        function update(now) {
            const progress = Math.min((now - startTime) / duration, 1);
            const current = Math.floor(progress * target);
            el.textContent = current.toLocaleString() + suffix;
            if (progress < 1) requestAnimationFrame(update);
        }

        requestAnimationFrame(update);
    }

    const statCounters = document.querySelectorAll('.stat-count');
    const statObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCount(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.6 });

    statCounters.forEach(counter => statObserver.observe(counter));

    /* ------------------------------------------------------------------
       New visitor guide
       One-time spotlight that draws a hand-drawn circle around Register.
       Kept in a closure so nothing leaks into the global scope.
    ------------------------------------------------------------------ */
    (function () {
        const STORAGE_KEY = 'eec_register_guide_seen_at';
        const COOLDOWN_DAYS = 7;

        const guide = document.getElementById('nvGuide');
        if (!guide) return;

        const spot = document.getElementById('nvSpot');
        const ring = document.getElementById('nvRing');
        const path = document.getElementById('nvRingPath');
        const bubble = document.getElementById('nvBubble');
        const arrow = document.getElementById('nvArrow');
        const closeBtn = document.getElementById('nvClose');
        const laterBtn = document.getElementById('nvLater');
        const cta = bubble.querySelector('.nv-cta');

        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const desktopQuery = window.matchMedia('(min-width: 768px)');

        let open = false;
        let closed = false;

        function alreadySeen() {
            try {
                const stamp = localStorage.getItem(STORAGE_KEY);
                if (!stamp) return false;

                return Date.now() - Number(stamp) < COOLDOWN_DAYS * 24 * 60 * 60 * 1000;
            } catch (e) {
                return false;
            }
        }

        function remember() {
            try {
                localStorage.setItem(STORAGE_KEY, String(Date.now()));
            } catch (e) {
                /* storage blocked — the guide simply shows again next visit */
            }
        }

        // Desktop points at the Register link; mobile points at the menu toggle
        // because that is where Register lives on small screens.
        function target() {
            const register = document.getElementById('navRegister');
            const toggle = document.getElementById('navMenuToggle');

            if (desktopQuery.matches && register) {
                return register;
            }

            return toggle;
        }

        function place() {
            const el = target();
            if (!el) return false;

            const rect = el.getBoundingClientRect();
            if (rect.width === 0 || rect.height === 0) return false;

            const pad = 10;
            const boxWidth = rect.width + pad * 2;
            const boxHeight = rect.height + pad * 2;

            spot.style.left = (rect.left - pad) + 'px';
            spot.style.top = (rect.top - pad) + 'px';
            spot.style.width = boxWidth + 'px';
            spot.style.height = boxHeight + 'px';

            ring.style.left = (rect.left - pad) + 'px';
            ring.style.top = (rect.top - pad) + 'px';
            ring.setAttribute('width', boxWidth);
            ring.setAttribute('height', boxHeight);
            ring.setAttribute('viewBox', '0 0 ' + boxWidth + ' ' + boxHeight);

            path.setAttribute('cx', boxWidth / 2);
            path.setAttribute('cy', boxHeight / 2);
            path.setAttribute('rx', boxWidth / 2 - 2);
            path.setAttribute('ry', boxHeight / 2 - 2);

            // Bubble drops below the button, right aligned but never off-screen.
            // It also has to clear the fixed navbar so the two do not visually merge.
            const navbar = document.getElementById('navbar');
            const navBottom = navbar ? navbar.getBoundingClientRect().bottom : rect.bottom;
            const bubbleWidth = Math.min(292, window.innerWidth - 24);
            const left = Math.max(12, Math.min(rect.right - bubbleWidth, window.innerWidth - bubbleWidth - 12));
            const top = Math.max(rect.bottom + 12, navBottom + 10);

            bubble.style.width = bubbleWidth + 'px';
            bubble.style.left = left + 'px';
            bubble.style.top = top + 'px';

            // Keep the little arrow pointing at the button's centre.
            const arrowX = Math.max(18, Math.min(rect.left + rect.width / 2 - left, bubbleWidth - 18));
            arrow.style.left = arrowX + 'px';

            return true;
        }

        function drawRing() {
            const length = typeof path.getTotalLength === 'function' ? path.getTotalLength() : 400;

            path.style.strokeDasharray = length;
            path.style.strokeDashoffset = length;

            if (reduceMotion) {
                path.style.strokeDashoffset = 0;
                return;
            }

            path.animate(
                [{ strokeDashoffset: length }, { strokeDashoffset: 0 }],
                { duration: 950, easing: 'ease-out', fill: 'forwards' }
            );

            window.setTimeout(() => ring.classList.add('nv-pulse'), 1000);
        }

        function show() {
            if (open || closed || alreadySeen()) return;

            guide.hidden = false;
            if (!place()) {
                guide.hidden = true;
                return;
            }

            open = true;
            guide.setAttribute('aria-hidden', 'false');
            drawRing();
        }

        function dismiss(rememberIt) {
            if (!open || closed) return;

            closed = true;
            open = false;

            if (rememberIt !== false) remember();

            guide.classList.add('nv-hide');
            window.setTimeout(() => {
                guide.hidden = true;
                guide.setAttribute('aria-hidden', 'true');
            }, 220);

            // Leave a single, quiet shimmer sweep on the button instead of a nag.
            const el = target();
            if (!reduceMotion && el) {
                el.classList.add('nv-shimmer');
                window.setTimeout(() => el.classList.remove('nv-shimmer'), 11000);
            }
        }

        closeBtn.addEventListener('click', () => dismiss(true));
        laterBtn.addEventListener('click', () => dismiss(true));
        cta.addEventListener('click', () => dismiss(true));

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') dismiss(true);
        });

        // Any click outside the bubble means they are exploring on their own.
        document.addEventListener('click', (event) => {
            if (!open) return;
            if (bubble.contains(event.target)) return;
            if (target() && target().contains(event.target)) return;

            dismiss(true);
        });

        // Scrolling is a sign they are reading, not hesitating.
        window.addEventListener('scroll', () => {
            if (open && window.scrollY > 60) dismiss(true);
        }, { passive: true });

        window.addEventListener('resize', () => {
            if (open) place();
        });

        window.setTimeout(show, 1100);
    })();
</script>

</body>
</html>





