<x-app-layout>
    <x-slot name="pageTitle">Most Requested Cities</x-slot>

    <div class="mx-auto max-w-6xl space-y-6">

        @include('reports._nav')

        <div class="flex justify-end">
            @include('reports._export', ['route' => 'reports.export.most-traveled-cities', 'filters' => $filters])
        </div>

        {{-- Summary --}}
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-900 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Requests in scope</p>
                <p class="mt-2 text-3xl font-bold text-gray-800 dark:text-slate-100">{{ $totalRequests }}</p>
            </div>
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 dark:bg-indigo-950/30 dark:border-indigo-900 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-400">Cities ranked</p>
                <p class="mt-2 text-3xl font-bold text-indigo-800 dark:text-indigo-300">{{ $cityStats->count() }}</p>
            </div>
            <div class="rounded-2xl border border-purple-200 bg-purple-50 dark:bg-purple-950/30 dark:border-purple-900 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-purple-700 dark:text-purple-400">Top city</p>
                @if($topCity)
                    <p class="mt-2 text-xl font-bold text-purple-800 dark:text-purple-300">{{ $topCity->city }}</p>
                    <p class="text-sm text-purple-600 dark:text-purple-400">{{ $topCity->request_count }} request{{ $topCity->request_count === 1 ? '' : 's' }}</p>
                @else
                    <p class="mt-2 text-xl font-bold text-purple-800 dark:text-purple-300">—</p>
                @endif
            </div>
        </div>

        {{-- City field tabs --}}
        <div class="flex flex-wrap gap-2">
            @foreach([
                'destination' => 'Destinations',
                'origin' => 'Origins',
                'all' => 'All Cities',
            ] as $value => $label)
                <a href="{{ route('reports.most-traveled-cities', array_merge($filters, ['city_field' => $value])) }}"
                    class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $cityField === $value ? 'bg-purple-600 text-white' : 'bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 hover:bg-purple-200 dark:hover:bg-purple-900' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
            <h2 class="text-base font-bold text-gray-700 dark:text-slate-100 mb-4">Filter Data</h2>
            <form action="{{ route('reports.most-traveled-cities') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                <input type="hidden" name="city_field" value="{{ $cityField }}">

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Project</label>
                    <select name="project_id"
                        class="w-full border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none transition">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ ($filters['project_id'] ?? '') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}{{ $project->isHeadOffice() ? ' (Head Office)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Status</label>
                    <select name="status"
                        class="w-full border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none transition">
                        <option value="">All Statuses</option>
                        @foreach([
                            'pending_pm' => 'Pending PM',
                            'pending_commercial' => 'Pending Commercial',
                            'pending_ceo' => 'Pending CEO',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ] as $val => $label)
                            <option value="{{ $val }}" {{ ($filters['status'] ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">From</label>
                    <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}"
                        class="w-full border border-gray-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">To</label>
                    <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}"
                        class="w-full border border-gray-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none transition">
                </div>

                <div class="flex items-end">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="head_office_only" value="1"
                            {{ !empty($filters['head_office_only']) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-slate-200">Head Office projects only</span>
                    </label>
                </div>

                <div class="sm:col-span-2 xl:col-span-4 flex justify-end gap-3 pt-1">
                    <a href="{{ route('reports.most-traveled-cities', ['city_field' => $cityField]) }}"
                        class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-slate-700 text-sm font-medium text-gray-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition">
                        Clear
                    </a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold transition"
                        style="background:linear-gradient(135deg,#4f46e5,#6366f1);">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        {{-- Chart --}}
        @if($cityStats->isNotEmpty())
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
                <h2 class="text-base font-bold text-gray-700 dark:text-slate-100 mb-1">
                    Top {{ min(10, $cityStats->count()) }} Cities
                    @if($cityField === 'destination')
                        (by destination)
                    @elseif($cityField === 'origin')
                        (by origin)
                    @else
                        (origin + destination combined)
                    @endif
                </h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mb-4">Number of travel requests selecting each city</p>
                <div style="height: 320px;">
                    <canvas id="cityChart"></canvas>
                </div>
            </div>
        @endif

        {{-- Rankings table --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-700 dark:text-slate-100">City Rankings</h2>
                <span class="text-xs bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 px-2.5 py-1 rounded-full font-semibold">
                    {{ $cityStats->count() }} cities
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-slate-800 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3 w-16">Rank</th>
                            <th class="px-6 py-3">City</th>
                            <th class="px-6 py-3">Region</th>
                            <th class="px-6 py-3 text-right">Requests</th>
                            <th class="px-6 py-3 text-right">Passengers</th>
                            <th class="px-6 py-3">Share</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
                        @php $maxCount = $cityStats->max('request_count') ?: 1; @endphp
                        @forelse ($cityStats as $row)
                            <tr class="hover:bg-indigo-50/30 dark:hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4">
                                    @if($row->rank <= 3)
                                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold
                                            {{ $row->rank === 1 ? 'bg-yellow-100 text-yellow-800' : ($row->rank === 2 ? 'bg-gray-200 text-gray-700' : 'bg-amber-100 text-amber-800') }}">
                                            {{ $row->rank }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 font-semibold">{{ $row->rank }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-800 dark:text-slate-100">{{ $row->city }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-slate-400">{{ $row->region ?? '—' }}</td>
                                <td class="px-6 py-4 text-right font-bold text-indigo-600 dark:text-indigo-400">{{ $row->request_count }}</td>
                                <td class="px-6 py-4 text-right text-gray-600 dark:text-slate-300">{{ $row->passenger_count }}</td>
                                <td class="px-6 py-4 min-w-[140px]">
                                    @php $pct = round(($row->request_count / $maxCount) * 100); @endphp
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-2 rounded-full bg-gray-100 dark:bg-slate-800 overflow-hidden">
                                            <div class="h-full rounded-full bg-indigo-500" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-400 w-8 text-right">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    No travel requests match the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($cityStats->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#cbd5e1' : '#6b7280';
                const gridColor = isDark ? '#1f2937' : '#f1f5f9';
                const ctx = document.getElementById('cityChart');

                if (ctx) {
                    new Chart(ctx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($chartData['labels']) !!},
                            datasets: [{
                                label: 'Requests',
                                data: {!! json_encode($chartData['data']) !!},
                                backgroundColor: '#8b5cf6',
                                borderRadius: 8,
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: { stepSize: 1, color: textColor },
                                    grid: { color: gridColor },
                                },
                                y: {
                                    ticks: { color: textColor },
                                    grid: { display: false },
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endif
</x-app-layout>
