<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'EEC Travel') }}</title>
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
        }    </style>
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
                <a href="/register" class="bg-sky-500 px-6 py-2 rounded-xl">Register</a>
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
        <button onclick="toggleMenu()" class="md:hidden text-2xl">☰</button>
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
            <h2 class="text-4xl font-bold text-sky-400"> <span class="stat-count" data-target="1000" data-suffix="+">0</span> 
        </h2>
            <p class="text-gray-400">Employees</p>
        </div>
        <div>
            <h2 class="text-4xl font-bold text-amber-400">  <span class="stat-count" data-target="1200" data-suffix="+">0</span>        
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

<!-- JS -->
<script>
    function toggleMenu() {
        document.getElementById('mobileMenu').classList.toggle('hidden');
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
</script>

</body>
</html>





