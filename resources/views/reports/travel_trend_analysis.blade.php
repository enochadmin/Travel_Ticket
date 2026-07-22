<x-app-layout>
    <x-slot name="pageTitle">Travel Trend Analysis</x-slot>

    <div class="mx-auto max-w-6xl space-y-6">

        @include('reports._nav')

        {{-- Period Filter --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
            <h2 class="text-base font-bold text-gray-700 dark:text-slate-100 mb-4">Select Comparison Period</h2>
            <form action="{{ route('reports.travel-trend-analysis') }}" method="GET" class="flex flex-wrap gap-3">
                @foreach(['month' => 'This Month vs Last Month', 'quarter' => 'This Quarter vs Last Quarter', 'year' => 'This Year vs Last Year'] as $value => $label)
                    <button type="submit" name="period" value="{{ $value }}"
                        class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $period === $value ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-200 hover:bg-gray-200 dark:hover:bg-slate-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </form>
        </div>

        {{-- Summary Cards --}}
        <div class="grid gap-4 sm:grid-cols-3">
            {{-- Current Period Total --}}
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 dark:bg-indigo-950/30 dark:border-indigo-900 p-6">
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-400">Current Period - Total Requests</p>
                <p class="mt-3 text-4xl font-bold text-indigo-800 dark:text-indigo-300">{{ $currentData['total'] }}</p>
                <div class="mt-3 flex items-center gap-2">
                    @if($growthData['total']['trend'] === 'up')
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 5a1 1 0 011 1v5.586l1.293-1.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L11 12.586V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-semibold text-green-700 dark:text-green-400">{{ abs($growthData['total']['percent']) }}% increase</span>
                    @else
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 15a1 1 0 01-1 1H9a1 1 0 01-1-1v-5.586l-1.293 1.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 9.414V15z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-semibold text-red-700 dark:text-red-400">{{ abs($growthData['total']['percent']) }}% decrease</span>
                    @endif
                </div>
            </div>

            {{-- Approved Comparison --}}
            <div class="rounded-2xl border border-green-200 bg-green-50 dark:bg-green-950/30 dark:border-green-900 p-6">
                <p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-400">Approved</p>
                <div class="mt-3 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-2xl font-bold text-green-800 dark:text-green-300">{{ $currentData['approved'] }}</p>
                        <p class="text-xs text-green-600 dark:text-green-500">Current</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $previousData['approved'] }}</p>
                        <p class="text-xs text-green-600 dark:text-green-500">Previous</p>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    @if($growthData['approved']['trend'] === 'up')
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 5a1 1 0 011 1v5.586l1.293-1.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L11 12.586V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                    @else
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 15a1 1 0 01-1 1H9a1 1 0 01-1-1v-5.586l-1.293 1.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 9.414V15z" clip-rule="evenodd" />
                        </svg>
                    @endif
                    <span class="text-xs font-semibold">{{ $growthData['approved']['percent'] > 0 ? '+' : '' }}{{ $growthData['approved']['percent'] }}%</span>
                </div>
            </div>

            {{-- Rejected Comparison --}}
            <div class="rounded-2xl border border-red-200 bg-red-50 dark:bg-red-950/30 dark:border-red-900 p-6">
                <p class="text-xs font-semibold uppercase tracking-wider text-red-700 dark:text-red-400">Rejected</p>
                <div class="mt-3 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-2xl font-bold text-red-800 dark:text-red-300">{{ $currentData['rejected'] }}</p>
                        <p class="text-xs text-red-600 dark:text-red-500">Current</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $previousData['rejected'] }}</p>
                        <p class="text-xs text-red-600 dark:text-red-500">Previous</p>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    @if($growthData['rejected']['trend'] === 'down')
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 15a1 1 0 01-1 1H9a1 1 0 01-1-1v-5.586l-1.293 1.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 9.414V15z" clip-rule="evenodd" />
                        </svg>
                    @else
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 5a1 1 0 011 1v5.586l1.293-1.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L11 12.586V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                    @endif
                    <span class="text-xs font-semibold">{{ $growthData['rejected']['percent'] > 0 ? '+' : '' }}{{ $growthData['rejected']['percent'] }}%</span>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Comparison Chart --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
                <h2 class="text-base font-bold text-gray-800 dark:text-slate-100 mb-4">Period Comparison</h2>
                <div style="height: 280px;">
                    <canvas id="trendComparisonChart"></canvas>
                </div>
            </div>

            {{-- Status Breakdown --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
                <h2 class="text-base font-bold text-gray-800 dark:text-slate-100 mb-4">Current Period Status Breakdown</h2>
                <div style="height: 280px;">
                    <canvas id="statusBreakdownChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
            <h2 class="text-base font-bold text-gray-700 dark:text-slate-100 mb-4">Filter Data</h2>
            <form action="{{ route('reports.travel-trend-analysis') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <input type="hidden" name="period" value="{{ $period }}">

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

                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition">
                        Apply Filters
                    </button>
                    <a href="{{ route('reports.travel-trend-analysis') }}"
                        class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-200 text-sm font-semibold rounded-xl transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#e2e8f0' : '#475569';
            const gridColor = isDark ? 'rgba(148, 163, 184, 0.2)' : 'rgba(148, 163, 184, 0.3)';
            const borderColor = isDark ? '#0b1220' : '#ffffff';

            // Comparison Chart
            const comparisonCtx = document.getElementById('trendComparisonChart');
            if (comparisonCtx) {
                new Chart(comparisonCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($chartLabels) !!},
                        datasets: [
                            {
                                label: 'Current Period',
                                data: {!! json_encode($chartData['current']) !!},
                                backgroundColor: '#3b82f6',
                                borderRadius: 6,
                            },
                            {
                                label: 'Previous Period',
                                data: {!! json_encode($chartData['previous']) !!},
                                backgroundColor: '#cbd5e1',
                                borderRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { font: { family: "'Inter', sans-serif" } } },
                            tooltip: { backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 12, cornerRadius: 8 }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1, color: textColor }, grid: { color: gridColor } },
                            x: { grid: { display: false }, ticks: { color: textColor } }
                        }
                    }
                });
            }

            // Status Breakdown Chart
            const statusCtx = document.getElementById('statusBreakdownChart');
            if (statusCtx) {
                new Chart(statusCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Approved', 'Pending', 'Rejected'],
                        datasets: [{
                            data: [{!! $currentData['approved'] !!}, {!! $currentData['pending'] !!}, {!! $currentData['rejected'] !!}],
                            backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                            borderWidth: 2, borderColor: borderColor
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { font: { family: "'Inter', sans-serif" } } },
                            tooltip: { backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 12, cornerRadius: 8 }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
