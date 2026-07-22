@php
    $navQuery = request()->only(['project_id', 'status', 'destination', 'purpose', 'start_date', 'end_date', 'head_office_only', 'city_field']);
@endphp

<div class="flex flex-col gap-4 border-b border-gray-100 dark:border-slate-700 pb-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('reports.index', $navQuery) }}"
            class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('reports.index') ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-200 hover:bg-gray-200 dark:hover:bg-slate-700' }}">
            Travel Requests
        </a>
        @hasanyrole('admin|commercial-director|project-manager')
        <a href="{{ route('reports.most-traveled-cities', $navQuery) }}"
            class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('reports.most-traveled-cities') ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-200 hover:bg-gray-200 dark:hover:bg-slate-700' }}">
            Most Requested Cities
        </a>
        @endhasanyrole
        @hasanyrole('admin|commercial-director|head-office-director|ceo')
        <a href="{{ route('reports.most-requested-projects', $navQuery) }}"
            class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('reports.most-requested-projects') ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-200 hover:bg-gray-200 dark:hover:bg-slate-700' }}">
            Most Requested Projects
        </a>
        @endhasanyrole
        @hasanyrole('admin|commercial-director')
        <a href="{{ route('reports.travel-trend-analysis', $navQuery) }}"
            class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('reports.travel-trend-analysis') ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-200 hover:bg-gray-200 dark:hover:bg-slate-700' }}">
            Travel Trends
        </a>
        <a href="{{ route('reports.frequent-travelers', $navQuery) }}"
            class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('reports.frequent-travelers') ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-200 hover:bg-gray-200 dark:hover:bg-slate-700' }}">
            Frequent Travelers
        </a>
        @endhasanyrole
    </div>
</div>

@if(!empty($scopeLabel))
    <p class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">{{ $scopeLabel }}</p>
@endif
