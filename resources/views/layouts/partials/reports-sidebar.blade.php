@php
    $reportsOpen = request()->routeIs('reports.*');
    $exportQuery = request()->query();
@endphp

<div x-data="{ reportsOpen: {{ $reportsOpen ? 'true' : 'false' }} }" class="space-y-1">
    <button @click="reportsOpen = !reportsOpen"
        class="w-full sidebar-link flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg text-indigo-200 text-sm font-medium {{ $reportsOpen ? 'active' : '' }}">
        <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span class="sidebar-text">Reports</span>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform duration-200 sidebar-text"
            :class="{ 'rotate-180': reportsOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="reportsOpen" x-collapse style="display: none;" class="pl-11 space-y-1 sidebar-text">
        <a href="{{ route('reports.index') }}"
            class="block px-3 py-2 rounded-lg text-indigo-300 hover:text-white hover:bg-white/10 text-sm transition {{ request()->routeIs('reports.index') ? 'text-white bg-white/10' : '' }}">
            Travel Requests
        </a>

        @hasanyrole('admin|commercial-director|project-manager')
        <a href="{{ route('reports.most-traveled-cities') }}"
            class="block px-3 py-2 rounded-lg text-indigo-300 hover:text-white hover:bg-white/10 text-sm transition {{ request()->routeIs('reports.most-traveled-cities') ? 'text-white bg-white/10' : '' }}">
            Most Requested Cities
        </a>
        @endhasanyrole

        @hasanyrole('admin|commercial-director|head-office-director|ceo')
        <a href="{{ route('reports.most-requested-projects') }}"
            class="block px-3 py-2 rounded-lg text-indigo-300 hover:text-white hover:bg-white/10 text-sm transition {{ request()->routeIs('reports.most-requested-projects') ? 'text-white bg-white/10' : '' }}">
            Most Requested Projects
        </a>
        @endhasanyrole
    </div>
</div>
