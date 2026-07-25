@php
    $reportsOpen = request()->routeIs('reports.*');
    $exportQuery = request()->query();
@endphp

<div x-data="{ reportsOpen: {{ $reportsOpen ? 'true' : 'false' }} }" class="space-y-1">
    <button @click="reportsOpen = !reportsOpen"
        class="w-full sidebar-link flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ $reportsOpen ? 'active' : '' }}">
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

    <div x-show="reportsOpen" x-collapse class="pl-11 space-y-1 sidebar-text sidebar-submenu">
        <a href="{{ route('reports.index') }}"
            class="sidebar-sublink block px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('reports.index') ? 'active' : '' }}">
            Travel Requests
        </a>

        @hasanyrole('admin|commercial-director|project-manager')
        <a href="{{ route('reports.most-traveled-cities') }}"
            class="sidebar-sublink block px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('reports.most-traveled-cities') ? 'active' : '' }}">
            Most Requested Cities
        </a>
        @endhasanyrole

        @hasanyrole('admin|commercial-director|head-office-director|ceo')
        <a href="{{ route('reports.most-requested-projects') }}"
            class="sidebar-sublink block px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('reports.most-requested-projects') ? 'active' : '' }}">
            Most Requested Projects
        </a>
        @endhasanyrole

        @hasanyrole('admin|commercial-director')
        <a href="{{ route('reports.travel-trend-analysis') }}"
            class="sidebar-sublink block px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('reports.travel-trend-analysis') ? 'active' : '' }}">
            Travel Trends
        </a>

        <a href="{{ route('reports.frequent-travelers') }}"
            class="sidebar-sublink block px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('reports.frequent-travelers') ? 'active' : '' }}">
            Frequent Travelers
        </a>
        @endhasanyrole
    </div>
</div>
