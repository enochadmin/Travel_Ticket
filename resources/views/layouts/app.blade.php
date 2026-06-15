<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TravelPass') }}</title>

    <script>
        (function () {
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-link {
            transition: background 0.2s, color 0.2s;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }

        .sidebar-link.active {
            border-left: 3px solid #818cf8;
        }

        .top-bar-shadow {
            box-shadow: 0 1px 3px rgba(0, 0, 0, .07);
        }

        /* Animate sidebar icons */
        .sidebar-link svg {
            transition: transform 0.2s;
        }

        .sidebar-link:hover svg {
            transform: scale(1.12);
        }

        [x-cloak] {
            display: none !important;
        }

        .sidebar {
            width: 16rem;
            transition: width 0.2s ease;
        }

        #app-shell[data-sidebar-collapsed="true"] .sidebar {
            width: 5rem;
        }

        #app-shell[data-sidebar-collapsed="true"] .sidebar .sidebar-text,
        #app-shell[data-sidebar-collapsed="true"] .sidebar .sidebar-user-text {
            display: none;
        }

        #app-shell[data-sidebar-collapsed="true"] .sidebar .sidebar-link {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        #app-shell[data-sidebar-collapsed="true"] .sidebar .sidebar-link svg {
            margin-right: 0;
        }

        #app-shell[data-sidebar-collapsed="true"] .sidebar .sidebar-logo {
            justify-content: center;
        }

    </style>
</head>

<body class="bg-gray-50 dark:bg-slate-900 text-gray-800 dark:text-slate-200 antialiased">

    <div id="app-shell" class="flex min-h-screen" data-sidebar-collapsed="false">

        {{-- ===========================
        SIDEBAR
        =========================== --}}
        <aside class="sidebar flex-shrink-0 flex flex-col"
            style="background: linear-gradient(180deg,#1e1b4b 0%,#312e81 100%); min-height:100vh;">

            {{-- Logo --}}
            <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10 relative sidebar-logo">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#818cf8;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 004 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                    </svg>
                </div>
                <span class="text-white font-bold text-lg tracking-tight sidebar-text">TravelPass</span>
                <button id="sidebar-toggle" type="button"
                    class="absolute right-4 top-1/2 -translate-y-1/2 p-2 rounded-lg text-indigo-200 hover:text-white hover:bg-white/10 transition"
                    aria-label="Toggle sidebar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-4 py-5 space-y-1 overflow-y-auto">

                @php
                    $myProjectId = null;
                    if (auth()->check() && auth()->user()->hasRole('project-manager')) {
                        $myProjectId = auth()->user()->approverProjectId();
                    }
                @endphp

                {{-- Reception navigation (Dashboard first) --}}
                @hasrole('reception')
                <a href="{{ route('reception.dashboard') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-indigo-200 text-sm font-medium {{ request()->routeIs('reception.dashboard') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="sidebar-text">Dashboard</span>
                </a>
                <a href="{{ route('reception.tickets.index') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-indigo-200 text-sm font-medium {{ request()->routeIs('reception.tickets.index', 'reception.tickets.show') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="sidebar-text">Approved Tickets</span>
                </a>
                <a href="{{ route('reception.tickets.archived') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-indigo-200 text-sm font-medium {{ request()->routeIs('reception.tickets.archived') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4m16 0H4m4 4h8" />
                    </svg>
                    <span class="sidebar-text">Archived Tickets</span>
                </a>
                @endhasrole

                {{-- Dashboard (all except reception) --}}
                @unlessrole('reception')
                <a href="{{ route('dashboard') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-indigo-200 text-sm font-medium {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="sidebar-text">Dashboard</span>
                </a>
                @endunlessrole

                {{-- Travel Requests (admin / director / ceo) --}}
                @hasanyrole('admin|head-office-director|ceo')
                <a href="{{ route('travel-requests.index') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-indigo-200 text-sm font-medium {{ request()->routeIs('travel-requests.*') && !request()->query('view') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="sidebar-text">Travel Requests</span>
                </a>
                @endhasanyrole

                {{-- Commercial Director: Travel + History --}}
                @hasrole('commercial-director')
                <a href="{{ route('travel-requests.index') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-indigo-200 text-sm font-medium {{ request()->routeIs('travel-requests.*') && !request()->query('view') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="sidebar-text">Travel Requests</span>
                </a>
                <div x-data="{ historyOpen: {{ request()->routeIs('travel-requests.*') && request()->query('view') ? 'true' : 'false' }} }"
                    class="space-y-1">
                    <button @click="historyOpen = !historyOpen"
                        class="w-full sidebar-link flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg text-indigo-200 text-sm font-medium hover:bg-white/10 transition">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="sidebar-text">History</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform duration-200 sidebar-text"
                            :class="{ 'rotate-180': historyOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="historyOpen" x-collapse style="display: none;" class="pl-11 space-y-1 sidebar-text">
                        <a href="{{ route('travel-requests.index', ['view' => 'personal']) }}"
                            class="block px-3 py-2 rounded-lg text-indigo-300 hover:text-white hover:bg-white/10 text-sm transition {{ request()->query('view') === 'personal' ? 'text-white bg-white/10' : '' }}">
                            My Travel History
                        </a>
                        <a href="{{ route('travel-requests.index', ['view' => 'approved']) }}"
                            class="block px-3 py-2 rounded-lg text-indigo-300 hover:text-white hover:bg-white/10 text-sm transition {{ request()->query('view') === 'approved' ? 'text-white bg-white/10' : '' }}">
                            Approved History
                        </a>
                    </div>
                </div>
                @endhasrole

                {{-- Travel History (regular users) --}}
                @unlessrole('admin|head-office-director|commercial-director|ceo|project-manager')
                <a href="{{ route('travel-requests.index', ['view' => 'personal']) }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-indigo-200 text-sm font-medium {{ request()->routeIs('travel-requests.*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="sidebar-text">Travel History</span>
                </a>
                @endunlessrole

                {{-- History Dropdown (Project Manager) --}}
                @hasrole('project-manager')
                <div x-data="{ historyOpen: {{ request()->routeIs('travel-requests.*') ? 'true' : 'false' }} }"
                    class="space-y-1">
                    <button @click="historyOpen = !historyOpen"
                        class="w-full sidebar-link flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg text-indigo-200 text-sm font-medium hover:bg-white/10 transition">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="sidebar-text">History</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform duration-200"
                            :class="{ 'rotate-180': historyOpen }" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="historyOpen" x-collapse style="display: none;" class="pl-11 space-y-1">
                        <a href="{{ route('travel-requests.index', ['view' => 'personal']) }}"
                            class="block px-3 py-2 rounded-lg text-indigo-300 hover:text-white hover:bg-white/10 text-sm transition {{ request()->query('view') === 'personal' ? 'text-white' : '' }}">
                            My Travel History
                        </a>
                        <a href="{{ route('travel-requests.index', ['view' => 'project']) }}"
                            class="block px-3 py-2 rounded-lg text-indigo-300 hover:text-white hover:bg-white/10 text-sm transition {{ request()->query('view') === 'project' ? 'text-white' : '' }}">
                            My Projects Travel History
                        </a>
                    </div>
                </div>
                @endhasrole

                {{-- Projects (admin / director / hod / ceo) --}}
                @hasanyrole('admin|head-office-director|commercial-director|ceo')
                <a href="{{ route('projects.index') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-indigo-200 text-sm font-medium {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span class="sidebar-text">Projects</span>
                </a>
                @endhasanyrole

                {{-- My Project (Project Manager only) --}}
                @hasrole('project-manager')
                @if($myProjectId)
                    <a href="{{ route('projects.show', $myProjectId) }}"
                        class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-indigo-200 text-sm font-medium {{ request()->is('projects/' . $myProjectId) ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span class="sidebar-text">My Project</span>
                    </a>
                @endif
                @endhasrole

                {{-- User Management (admin) --}}
                @hasrole('admin')
                <a href="{{ route('users.index') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-indigo-200 text-sm font-medium {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="sidebar-text">Users & Roles</span>
                </a>
                @endhasrole

                {{-- Reports dropdown --}}
                @hasanyrole('admin|head-office-director|commercial-director|ceo|project-manager')
                @include('layouts.partials.reports-sidebar')
                @endhasanyrole

                {{-- Settings (admin) --}}
                @hasrole('admin')
                <div class="pt-4 pb-1 px-3">
                    <span class="text-indigo-400 text-xs font-semibold uppercase tracking-widest sidebar-text">Settings</span>
                </div>
                <a href="{{ route('settings.cities.index') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-indigo-200 text-sm font-medium {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="sidebar-text">Cities</span>
                </a>
                @endhasrole

                {{-- Divider + label --}}
                <div class="pt-4 pb-1 px-3">
                    <span class="text-indigo-400 text-xs font-semibold uppercase tracking-widest sidebar-text">Account</span>
                </div>

                {{-- Profile --}}
                <a href="{{ route('profile.edit') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-indigo-200 text-sm font-medium {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="sidebar-text">My Profile</span>
                </a>
            </nav>

            {{-- User card --}}
            <div class="px-4 py-4 border-t border-white/10">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-sm"
                        style="background:#6366f1;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 sidebar-user-text">
                        <p class="text-white text-sm font-semibold truncate">{{ Auth::user()->name }}</p>
                        <p class="text-indigo-300 text-xs truncate">
                            {{ Auth::user()->getRoleNames()->first() ?? 'user' }}
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ===========================
        MAIN CONTENT AREA
        =========================== --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Top bar --}}
            <header class="bg-white dark:bg-slate-950 top-bar-shadow px-8 py-4 flex items-center justify-between sticky top-0 z-10 border-b border-transparent dark:border-slate-800">
                <div>
                    @isset($pageTitle)
                        <h1 class="text-xl font-bold text-gray-800 dark:text-slate-100">{{ $pageTitle }}</h1>
                    @else
                        <h1 class="text-xl font-bold text-gray-800 dark:text-slate-100">{{ config('app.name') }}</h1>
                    @endisset
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500 dark:text-slate-400">{{ now()->format('l, M d Y') }}</span>

                    {{-- Notification Bell --}}
                    @auth
                        @php $unreadCount = Auth::user()->unreadNotifications->count(); @endphp
                        <div x-data="{ bellOpen: false }" class="relative">
                            <button @click="bellOpen = !bellOpen"
                                class="relative p-2 rounded-full text-gray-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-950 focus:outline-none transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @if($unreadCount > 0)
                                    <span
                                        class="absolute top-0.5 right-0.5 w-4 h-4 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center leading-none">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                                @endif
                            </button>

                            {{-- Bell Dropdown --}}
                            <div x-show="bellOpen" @click.outside="bellOpen = false" x-transition
                                class="absolute right-0 mt-2 w-80 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-700 z-50 overflow-hidden"
                                style="display: none;">

                                <div
                                    class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-800">
                                    <p class="text-sm font-bold text-gray-800 dark:text-slate-100">Notifications <span
                                            class="text-indigo-600">({{ $unreadCount }} new)</span></p>
                                    @if($unreadCount > 0)
                                        <form method="POST" action="{{ route('notifications.markAllRead') }}">
                                            @csrf
                                            <button type="submit"
                                                class="text-xs text-indigo-600 hover:underline font-medium">Mark all
                                                read</button>
                                        </form>
                                    @endif
                                </div>

                                <div class="max-h-72 overflow-y-auto divide-y divide-gray-50">
                                    @forelse(Auth::user()->notifications()->latest()->take(10)->get() as $notif)
                                        @php
                                            $nType = $notif->data['type'] ?? 'info';
                                            $nBorder = ['success' => 'border-l-4 border-green-400', 'error' => 'border-l-4 border-red-400', 'warning' => 'border-l-4 border-yellow-400', 'info' => 'border-l-4 border-blue-400'][$nType] ?? '';
                                            $nIcon = ['success' => 'text-green-500', 'error' => 'text-red-500', 'warning' => 'text-yellow-500', 'info' => 'text-blue-500'][$nType] ?? 'text-gray-400';
                                        @endphp
                                        <a href="{{ route('notifications.read', $notif->id) }}"
                                            class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-slate-800 transition {{ $notif->read_at ? 'opacity-60' : '' }} {{ $nBorder }}">
                                            <span class="{{ $nIcon }} mt-0.5 flex-shrink-0">
                                                @if($nType === 'success')<svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                @elseif($nType === 'error')<svg class="w-4 h-4" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                @elseif($nType === 'warning')<svg class="w-4 h-4" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                                    </svg>
                                                @else<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                @endif
                                            </span>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-semibold text-gray-800 dark:text-slate-100 leading-snug">
                                                    {{ $notif->data['message'] ?? 'Notification' }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-0.5">
                                                    {{ $notif->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            @if(!$notif->read_at)
                                                <span class="w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0 mt-1.5"></span>
                                            @endif
                                        </a>
                                    @empty
                                        <div class="py-8 text-center text-gray-400 text-sm">
                                            <p class="text-2xl mb-1">🔔</p>
                                            No notifications yet.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endauth

                    <button type="button" data-theme-toggle
                        class="p-2 rounded-full text-gray-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-950 focus:outline-none transition"
                        aria-label="Toggle dark mode">
                        <svg data-theme-icon="moon" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg data-theme-icon="sun" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v2m0 14v2m9-9h-2M5 12H3m14.95 6.95-1.414-1.414M7.464 7.464 6.05 6.05m11.9 0-1.414 1.414M7.464 16.536 6.05 17.95M12 7a5 5 0 100 10 5 5 0 000-10z" />
                        </svg>
                    </button>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="p-2 rounded-full text-gray-500 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950 focus:outline-none transition"
                            aria-label="Sign out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>

                    
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 p-8 dark:bg-slate-900">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        (function () {
            const shell = document.getElementById('app-shell');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            if (shell && sidebarToggle) {
                sidebarToggle.addEventListener('click', () => {
                    const next = shell.dataset.sidebarCollapsed !== 'true';
                    shell.dataset.sidebarCollapsed = next ? 'true' : 'false';
                });
            }
        })();
    </script>

</body>

</html>
