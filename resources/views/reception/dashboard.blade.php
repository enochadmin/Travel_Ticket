<x-app-layout>
    <x-slot name="pageTitle">Reception Dashboard</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-xs uppercase tracking-widest text-gray-400">Approved Tickets</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $approvedTotal }}</p>
            <p class="text-xs text-gray-500 mt-1">All fully approved</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-xs uppercase tracking-widest text-gray-400">Archived (Processed)</p>
            <p class="text-3xl font-bold text-emerald-600 mt-2">{{ $archivedTotal }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $archivedRatio }}% of approved</p>
            <a href="{{ route('reception.tickets.archived') }}"
                class="inline-block mt-3 text-xs font-semibold text-emerald-700 hover:text-emerald-800">View archived tickets</a>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-xs uppercase tracking-widest text-gray-400">Pending Processing</p>
            <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $pendingProcessing }}</p>
            <p class="text-xs text-gray-500 mt-1">Ready to process</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Approved Tickets Per Month</h2>
            <div class="relative" style="height:260px;">
                <canvas id="receptionMonthlyChart"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                @forelse($monthlyCounts as $month => $count)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">{{ $month }}</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $count }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No approved tickets yet.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Top Projects (Approved Tickets)</h2>
            <div class="relative" style="height:260px;">
                <canvas id="receptionProjectChart"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                @forelse($projectCounts as $row)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">{{ $row['name'] }}</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $row['total'] }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No approved tickets yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-4">Approvals by Destination</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($destinationCounts as $destination => $count)
                <a href="{{ route('reception.destinations.show', urlencode($destination)) }}"
                    class="block p-4 rounded-xl border border-gray-100 hover:border-indigo-200 hover:shadow-sm transition bg-gray-50">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-800">{{ $destination }}</span>
                        <span class="text-xs text-indigo-600 font-bold">{{ $count }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">View approved tickets</p>
                </a>
            @empty
                <p class="text-sm text-gray-500">No approved tickets yet.</p>
            @endforelse
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const theme = () => {
                const isDark = document.documentElement.classList.contains('dark');
                return {
                    text: isDark ? '#e2e8f0' : '#475569',
                    grid: isDark ? 'rgba(148, 163, 184, 0.2)' : 'rgba(148, 163, 184, 0.3)',
                    tooltipBg: isDark ? 'rgba(2, 6, 23, 0.95)' : 'rgba(17, 24, 39, 0.9)'
                };
            };

            const applyTheme = () => {
                const t = theme();
                Chart.defaults.color = t.text;
                Chart.defaults.borderColor = t.grid;
                return t;
            };

            const t = applyTheme();
            window.__charts = window.__charts || [];

            const monthlyCtx = document.getElementById('receptionMonthlyChart');
            if (monthlyCtx) {
                const monthlyChart = new Chart(monthlyCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($monthlyLabels) !!},
                        datasets: [{
                            label: 'Approved Tickets',
                            data: {!! json_encode($monthlyData) !!},
                            backgroundColor: '#6366f1',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false }, tooltip: { backgroundColor: t.tooltipBg } },
                        scales: {
                            y: { beginAtZero: true, ticks: { color: t.text }, grid: { color: t.grid } },
                            x: { ticks: { color: t.text }, grid: { display: false } }
                        }
                    }
                });
                window.__charts.push(monthlyChart);
            }

            const projectCtx = document.getElementById('receptionProjectChart');
            if (projectCtx) {
                const projectChart = new Chart(projectCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($projectLabels) !!},
                        datasets: [{
                            label: 'Approved Tickets',
                            data: {!! json_encode($projectData) !!},
                            backgroundColor: '#10b981',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false }, tooltip: { backgroundColor: t.tooltipBg } },
                        scales: {
                            y: { beginAtZero: true, ticks: { color: t.text }, grid: { color: t.grid } },
                            x: { ticks: { color: t.text }, grid: { display: false } }
                        }
                    }
                });
                window.__charts.push(projectChart);
            }

            window.addEventListener('themechange', () => {
                const nt = applyTheme();
                (window.__charts || []).forEach((chart) => {
                    if (chart?.options?.scales) {
                        if (chart.options.scales.x?.ticks) chart.options.scales.x.ticks.color = nt.text;
                        if (chart.options.scales.y?.ticks) chart.options.scales.y.ticks.color = nt.text;
                        if (chart.options.scales.x?.grid) chart.options.scales.x.grid.color = nt.grid;
                        if (chart.options.scales.y?.grid) chart.options.scales.y.grid.color = nt.grid;
                    }
                    chart.update();
                });
            });
        });
    </script>
</x-app-layout>
