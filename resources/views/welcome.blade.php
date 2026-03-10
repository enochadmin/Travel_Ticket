<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'EEC Travel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap');
        
        :root {
            --primary: #0ea5e9;   /* Sky blue - modern & trustworthy */
            --accent: #eab308;    /* Gold accent (Ethiopian flag inspiration) */
        }
        
        .hero-bg {
            background-image: linear-gradient(rgba(15, 23, 42, 0.72), rgba(15, 23, 42, 0.78)), 
                             url('{{ asset('images/landing-bg.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .nav-link {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .nav-link:hover {
            color: #eab308;
            transform: translateY(-1px);
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            line-height: 1.05;
            letter-spacing: -0.04em;
        }

        .feature-card {
            transition: all 0.4s cubic-bezier(0.4, 0.0, 0.2, 1);
        }
        
        .feature-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25);
        }

        .cta-button {
            position: relative;
            overflow: hidden;
        }
        
        .cta-button::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 40%;
            height: 400%;
            background: linear-gradient(
                120deg,
                transparent,
                rgba(255,255,255,0.4),
                transparent
            );
            transform: skewX(-25deg);
            animation: shine 4s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-150%) skewX(-25deg); }
            20% { transform: translateX(300%) skewX(-25deg); }
            100% { transform: translateX(300%) skewX(-25deg); }
        }
    </style>
</head>
<body class="bg-zinc-950 text-white font-sans">

    <!-- Top Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-zinc-950/80 backdrop-blur-lg border-b border-white/10">
        <div class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/eec-logo.png') }}" alt="EEC Logo" class="h-9 w-auto">
                <div>
                    <span class="font-semibold text-2xl tracking-tighter">EEC</span>
                    <span class="text-sky-400 font-medium text-xl">TRAVEL</span>
                </div>
            </div>

            <div class="flex items-center gap-8 text-sm font-medium">
                <a href="#features" class="nav-link text-zinc-300 hover:text-white">Features</a>
                <a href="#how" class="nav-link text-zinc-300 hover:text-white">How it Works</a>
                <a href="#contact" class="nav-link text-zinc-300 hover:text-white">Contact</a>
                
                @auth
                    <a href="{{ url('/dashboard') }}" 
                       class="px-8 py-3 bg-white text-zinc-900 rounded-2xl font-semibold hover:bg-amber-400 transition-all">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ url('/login') }}" 
                       class="px-8 py-3 border border-white/70 hover:border-white rounded-2xl font-semibold transition-all">
                        Log in
                    </a>
                    
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" 
                           class="px-8 py-3 bg-sky-500 hover:bg-sky-600 rounded-2xl font-semibold transition-all">
                            Register
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero-bg min-h-screen flex items-center pt-20">
        <div class="max-w-5xl mx-auto px-6 text-center">
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-6 py-2 rounded-3xl text-sm font-medium mb-8 border border-white/20">
                <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                Ethiopian Engineering Corporation
            </div>

            <h1 class="hero-title font-bold tracking-tighter leading-none mb-6">
                Travel Requests.<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 via-amber-300 to-white">Approved Faster.</span>
            </h1>

            <p class="max-w-2xl mx-auto text-xl text-zinc-300 leading-relaxed mb-12">
                The official ticket management system for EEC staff.<br>
                Submit, track, and approve travel requests with complete transparency.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-5">
                @auth
                    <a href="{{ url('/dashboard') }}" 
                       class="cta-button px-12 py-5 bg-white text-zinc-950 rounded-3xl font-semibold text-lg shadow-2xl hover:shadow-sky-500/30 transition-all flex items-center gap-3 group">
                        Open Dashboard
                        <span class="text-2xl group-active:rotate-45 transition">→</span>
                    </a>
                @else
                    <a href="{{ url('/login') }}" 
                       class="cta-button px-12 py-5 bg-white text-zinc-950 rounded-3xl font-semibold text-lg shadow-2xl hover:shadow-sky-500/30 transition-all flex items-center gap-3">
                        Log in to Start
                    </a>
                    
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" 
                           class="px-12 py-5 border-2 border-white/70 hover:border-white text-lg font-semibold rounded-3xl transition-all">
                            Create Account
                        </a>
                    @endif
                @endauth
            </div>

            <div class="mt-16 flex items-center justify-center gap-8 text-xs text-zinc-400">
                <div class="flex items-center gap-2">
                    <i class="bi bi-shield-check text-emerald-400"></i>
                    Secure & Encrypted
                </div>
                <div class="flex items-center gap-2">
                    <i class="bi bi-clock-history text-amber-400"></i>
                    Real-time Tracking
                </div>
                <div class="flex items-center gap-2">
                    <i class="bi bi-globe text-sky-400"></i>
                    For All EEC Staff
                </div>
            </div>
        </div>

        <!-- Scroll indicator -->
        <div class="absolute bottom-12 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-zinc-400 text-xs tracking-widest">
            <span>SCROLL TO EXPLORE</span>
            <div class="w-px h-12 bg-gradient-to-b from-transparent via-zinc-400 to-transparent"></div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section id="features" class="py-28 bg-zinc-900">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <span class="uppercase text-sky-400 text-sm font-semibold tracking-[3px]">POWERFUL FEATURES</span>
                <h2 class="text-5xl font-bold mt-3 tracking-tight">Everything you need in one place</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-zinc-800/70 border border-white/10 rounded-3xl p-10">
                    <div class="w-14 h-14 bg-sky-500/10 rounded-2xl flex items-center justify-center mb-8">
                        <i class="bi bi-airplane-fill text-3xl text-sky-400"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4">Instant Travel Requests</h3>
                    <p class="text-zinc-400 leading-relaxed">
                        Submit domestic or international travel requests in under 60 seconds with smart forms and auto-fill.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-zinc-800/70 border border-white/10 rounded-3xl p-10">
                    <div class="w-14 h-14 bg-amber-500/10 rounded-2xl flex items-center justify-center mb-8">
                        <i class="bi bi-check2-circle text-3xl text-amber-400"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4">Smart Approval Workflow</h3>
                    <p class="text-zinc-400 leading-relaxed">
                        Multi-level approvals with automatic notifications, reminders, and escalation rules.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-zinc-800/70 border border-white/10 rounded-3xl p-10">
                    <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center mb-8">
                        <i class="bi bi-graph-up text-3xl text-emerald-400"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4">Live Dashboard & Reports</h3>
                    <p class="text-zinc-400 leading-relaxed">
                        Real-time visibility into all pending, approved, and completed trips with powerful analytics.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- TRUST BAR -->
    <div class="bg-zinc-950 py-8 border-y border-white/10">
        <div class="max-w-6xl mx-auto px-6 flex flex-wrap items-center justify-center gap-x-16 gap-y-6 opacity-70">
            <img src="{{ asset('images/eec-logo.png') }}" class="h-7 grayscale" alt="EEC">
            <div class="text-zinc-500 text-sm font-medium tracking-wider">ETHIOPIAN AIRLINES PARTNER</div>
            <div class="text-zinc-500 text-sm font-medium tracking-wider">MINISTRY OF TRANSPORT</div>
            <div class="text-zinc-500 text-sm font-medium tracking-wider">SECURE • RELIABLE • OFFICIAL</div>
        </div>
    </div>

    <!-- FINAL CTA -->
    <section class="py-28 bg-gradient-to-b from-zinc-900 to-zinc-950">
        <div class="max-w-2xl mx-auto text-center px-6">
            <h2 class="text-4xl font-bold mb-6">Ready to simplify EEC travel?</h2>
            <p class="text-zinc-400 text-lg mb-10">
                Join hundreds of EEC employees already using the most advanced ticket management platform in Ethiopia.
            </p>
            
            @auth
                <a href="{{ url('/dashboard') }}" class="inline-block px-14 py-6 bg-gradient-to-r from-sky-500 to-amber-400 text-xl font-semibold rounded-3xl text-zinc-950 hover:scale-105 transition-transform">
                    Go to My Dashboard
                </a>
            @else
                <div class="flex flex-col sm:flex-row gap-5 justify-center">
                    <a href="{{ url('/login') }}" 
                       class="inline-block px-14 py-6 bg-white text-zinc-950 text-xl font-semibold rounded-3xl hover:bg-amber-300 transition-all">
                        Log in Now
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" 
                           class="inline-block px-14 py-6 border-2 border-white text-xl font-semibold rounded-3xl hover:bg-white/10 transition-all">
                            Create Free Account
                        </a>
                    @endif
                </div>
            @endauth
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-black py-16 border-t border-white/10">
        <div class="max-w-7xl mx-auto px-6 text-center text-xs text-zinc-500">
            <p>&copy; {{ date('Y') }} Ethiopian Engineering Corporation (EEC). All rights reserved.</p>
            <p class="mt-2">Official Internal Ticket &amp; Travel Management System</p>
        </div>
    </footer>

</body>
</html>
