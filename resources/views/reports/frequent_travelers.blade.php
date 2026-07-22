<x-app-layout>
    <x-slot name="pageTitle">Frequent Travelers</x-slot>

    <div class="mx-auto max-w-6xl space-y-6">

        @include('reports._nav')

        <div class="flex justify-end">
            @include('reports._export', ['route' => 'reports.export.frequent-travelers', 'filters' => $filters])
        </div>

        {{-- Summary Cards --}}
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-900 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Requests</p>
                <p class="mt-2 text-3xl font-bold text-gray-800 dark:text-slate-100">{{ $totalRequests }}</p>
            </div>
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 dark:bg-indigo-950/30 dark:border-indigo-900 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-400">Unique Travelers</p>
                <p class="mt-2 text-3xl font-bold text-indigo-800 dark:text-indigo-300">{{ $totalTravelers }}</p>
            </div>
            <div class="rounded-2xl border border-purple-200 bg-purple-50 dark:bg-purple-950/30 dark:border-purple-900 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-purple-700 dark:text-purple-400">Average Trips/Person</p>
                <p class="mt-2 text-3xl font-bold text-purple-800 dark:text-purple-300">{{ $totalTravelers > 0 ? round($totalRequests / $totalTravelers, 1) : 0 }}</p>
            </div>
        </div>

        {{-- Top Destinations Chart --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
            <h2 class="text-base font-bold text-gray-800 dark:text-slate-100 mb-4">Top Destinations by Frequency</h2>
            <div style="height: 300px;">
                <canvas id="destinationsChart"></canvas>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
            <h2 class="text-base font-bold text-gray-700 dark:text-slate-100 mb-4">Filter Data</h2>
            <form action="{{ route('reports.frequent-travelers') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Project</label>
                    <select name="project_id"
                        class="w-full border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none transition">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ ($filters['project_id'] ?? '') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Status</label>
                    <select name="status"
                        class="w-full border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none transition">
                        <option value="">All Statuses</option>
                        @foreach(['approved' => 'Approved', 'rejected' => 'Rejected', 'pending_commercial' => 'Pending Commercial'] as $val => $label)
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

                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition">
                        Apply Filters
                    </button>
                    <a href="{{ route('reports.frequent-travelers') }}"
                        class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-200 text-sm font-semibold rounded-xl transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Frequent Travelers Table --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800 dark:text-slate-100">Top Travelers</h2>
                <span class="text-sm text-gray-500 dark:text-slate-400">Showing {{ $travelers->count() }} travelers</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-800 text-gray-500 dark:text-slate-300 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-6 py-4 text-left">Rank</th>
                            <th class="px-6 py-4 text-left">Traveler Name</th>
                            <th class="px-6 py-4 text-left">Email</th>
                            <th class="px-6 py-4 text-center">Total Trips</th>
                            <th class="px-6 py-4 text-left">Top Destinations</th>
                            <th class="px-6 py-4 text-left">Projects</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @forelse($travelers as $index => $traveler)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition">
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 font-bold text-sm">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-800 dark:text-slate-100">{{ $traveler->user_name }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-gray-600 dark:text-slate-400 text-xs">{{ $traveler->user_email }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 font-bold">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5.951-1.429 5.951 1.429a1 1 0 001.169-1.409l-7-14z" />
                                        </svg>
                                        {{ $traveler->trip_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_slice(explode(', ', $traveler->destinations), 0, 3) as $dest)
                                            <span class="inline-block px-2.5 py-1 rounded-lg bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-300 text-xs font-medium">
                                                {{ $dest }}
                                            </span>
                                        @endforeach
                                        @if(count(explode(', ', $traveler->destinations)) > 3)
                                            <span class="inline-block px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 text-xs font-medium">
                                                +{{ count(explode(', ', $traveler->destinations)) - 3 }} more
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_slice(explode(', ', $traveler->projects), 0, 2) as $project)
                                            <span class="inline-block px-2.5 py-1 rounded-lg bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 text-xs font-medium">
                                                {{ $project }}
                                            </span>
                                        @endforeach
                                        @if(count(explode(', ', $traveler->projects)) > 2)
                                            <span class="inline-block px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 text-xs font-medium">
                                                +{{ count(explode(', ', $traveler->projects)) - 2 }} more
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400 dark:text-slate-500">
                                    No travelers found matching the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#e2e8f0' : '#475569';
            const gridColor = isDark ? 'rgba(148, 163, 184, 0.2)' : 'rgba(148, 163, 184, 0.3)';
            const borderColor = isDark ? '#0b1220' : '#ffffff';

            // Destinations Chart
            const destCtx = document.getElementById('destinationsChart');
            if (destCtx) {
                new Chart(destCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($chartData['labels']) !!},
                        datasets: [{
                            label: 'Number of Requests',
                            data: {!! json_encode($chartData['data']) !!},
                            backgroundColor: '#8b5cf6',
                            borderRadius: 6,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                titleFont: { size: 14, family: "'Inter', sans-serif" },
                                bodyFont: { size: 13, family: "'Inter', sans-serif" },
                                padding: 12,
                                cornerRadius: 8,
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: { stepSize: 1, color: textColor },
                                grid: { color: gridColor, drawBorder: false }
                            },
                            y: {
                                ticks: { color: textColor },
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
