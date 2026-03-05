<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @hasanyrole('admin|head-office-director')
                    <x-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.*')">
                        {{ __('Projects') }}
                    </x-nav-link>
                    @endhasanyrole

                    <x-nav-link :href="route('travel-requests.index')"
                        :active="request()->routeIs('travel-requests.*')">
                        {{ __('Travel Requests') }}
                    </x-nav-link>

                    @hasanyrole('admin|head-office-director')
                    <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                        {{ __('Reports') }}
                    </x-nav-link>
                    @endhasanyrole
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">

                {{-- Notification Bell --}}
                @auth
                    @php $unreadCount = Auth::user()->unreadNotifications->count(); @endphp
                    <div x-data="{ bellOpen: false }" class="relative">
                        <button @click="bellOpen = !bellOpen"
                            class="relative p-2 rounded-full text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 focus:outline-none transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            @if($unreadCount > 0)
                                <span
                                    class="absolute top-1 right-1 w-4 h-4 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                            @endif
                        </button>

                        {{-- Bell Dropdown --}}
                        <div x-show="bellOpen" @click.outside="bellOpen = false" x-transition
                            class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden"
                            style="display: none;">

                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50">
                                <p class="text-sm font-bold text-gray-800">Notifications <span
                                        class="text-indigo-600">({{ $unreadCount }} new)</span></p>
                                @if($unreadCount > 0)
                                    <form method="POST" action="{{ route('notifications.markAllRead') }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-indigo-600 hover:underline font-medium">Mark
                                            all read</button>
                                    </form>
                                @endif
                            </div>

                            <div class="max-h-72 overflow-y-auto divide-y divide-gray-50">
                                @forelse(Auth::user()->notifications()->latest()->take(10)->get() as $notification)
                                    @php
                                        $type = $notification->data['type'] ?? 'info';
                                        $typeClasses = [
                                            'success' => 'bg-green-50 border-l-4 border-green-400',
                                            'error' => 'bg-red-50 border-l-4 border-red-400',
                                            'warning' => 'bg-yellow-50 border-l-4 border-yellow-400',
                                            'info' => 'bg-blue-50 border-l-4 border-blue-400',
                                        ][$type] ?? 'bg-gray-50';
                                        $iconColor = [
                                            'success' => 'text-green-500',
                                            'error' => 'text-red-500',
                                            'warning' => 'text-yellow-500',
                                            'info' => 'text-blue-500',
                                        ][$type] ?? 'text-gray-400';
                                    @endphp
                                    <a href="{{ route('notifications.read', $notification->id) }}"
                                        class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition {{ $notification->read_at ? 'opacity-60' : '' }} {{ $typeClasses }}">
                                        <span class="{{ $iconColor }} mt-0.5 flex-shrink-0">
                                            @if($type === 'success')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @elseif($type === 'error')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @elseif($type === 'warning')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @endif
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-xs font-semibold text-gray-800 leading-snug">
                                                {{ $notification->data['message'] ?? 'Notification' }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">
                                                {{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if(!$notification->read_at)
                                            <span class="w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0 mt-1.5 ml-auto"></span>
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

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @hasanyrole('admin|head-office-director')
            <x-responsive-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.*')">
                {{ __('Projects') }}
            </x-responsive-nav-link>
            @endhasanyrole

            <x-responsive-nav-link :href="route('travel-requests.index')"
                :active="request()->routeIs('travel-requests.*')">
                {{ __('Travel Requests') }}
            </x-responsive-nav-link>

            @hasanyrole('admin|head-office-director')
            <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                {{ __('Reports') }}
            </x-responsive-nav-link>
            @endhasanyrole
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>