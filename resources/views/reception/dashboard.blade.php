<x-app-layout>
    <x-slot name="pageTitle">Reception Dashboard</x-slot>

    @php
        $queueHealth = $approvedTotal > 0 ? round(($pendingProcessing / max($approvedTotal, 1)) * 100) : 0;
    @endphp

    <div class="mx-auto max-w-7xl space-y-6">
        <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
            <div class="relative px-6 py-8 sm:px-8 lg:px-10" style="background:linear-gradient(135deg,#0f172a 0%,#0f766e 42%,#ecfeff 100%);">
                <div class="absolute inset-0 opacity-20" style="background-image:radial-gradient(circle at top right, white 0, transparent 28%), radial-gradient(circle at bottom left, #99f6e4 0, transparent 32%);"></div>
                <div class="relative flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
                    <div class="max-w-3xl">
                        <div class="mb-4 flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-white ring-1 ring-white/25">
                                Reception Control Center
                            </span>
                            <span class="inline-flex items-center rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-semibold text-emerald-50 ring-1 ring-emerald-200/30">
                                {{ $pendingProcessing }} tickets awaiting processing
                            </span>
                        </div>

                        <h2 class="text-3xl font-black tracking-tight text-white sm:text-4xl lg:text-5xl">
                            A cleaner overview for approved ticket handling
                        </h2>
                        <p class="mt-4 max-w-2xl text-sm leading-7 text-teal-50/95 sm:text-base">
                            This dashboard brings the queue, workload trends, destination demand, and top projects into one place so reception can process tickets with less hunting around.
                        </p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-3 xl:w-[33rem]">
                        <div class="rounded-2xl border border-white/15 bg-white/12 p-4 text-white backdrop-blur-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-teal-100">Latest Month</p>
                            <p class="mt-2 text-2xl font-black">{{ $latestMonthTotal }}</p>
                            <p class="mt-1 text-xs text-teal-100/85">{{ $latestMonthLabel }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/15 bg-white/12 p-4 text-white backdrop-blur-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-teal-100">Busiest Month</p>
                            <p class="mt-2 text-2xl font-black">{{ $busiestMonthTotal }}</p>
                            <p class="mt-1 text-xs text-teal-100/85">{{ $busiestMonthLabel }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/15 bg-white/12 p-4 text-white backdrop-blur-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-teal-100">Top Destination</p>
                            <p class="mt-2 text-lg font-black">{{ $topDestinationName ?? 'No data yet' }}</p>
                            <p class="mt-1 text-xs text-teal-100/85">{{ $topDestinationTotal }} approved tickets</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Approved Tickets</p>
                <p class="mt-3 text-4xl font-black text-slate-900">{{ $approvedTotal }}</p>
                <p class="mt-2 text-sm text-slate-600">All tickets that have reached final approval.</p>
            </article>

            <article class="rounded-[1.75rem] border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Archived</p>
                <p class="mt-3 text-4xl font-black text-emerald-700">{{ $archivedTotal }}</p>
                <p class="mt-2 text-sm text-emerald-800">{{ $archivedRatio }}% of approved tickets have already been processed.</p>
                <a href="{{ route('reception.tickets.archived') }}" class="mt-4 inline-flex text-sm font-semibold text-emerald-700 hover:text-emerald-900">
                    View archived tickets
                </a>
            </article>

            <article class="rounded-[1.75rem] border border-indigo-200 bg-gradient-to-br from-indigo-50 to-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-700">Pending Queue</p>
                <p class="mt-3 text-4xl font-black text-indigo-700">{{ $pendingProcessing }}</p>
                <p class="mt-2 text-sm text-indigo-900">Ready for booking, issuance, or final processing by reception.</p>
            </article>

            <article class="rounded-[1.75rem] border border-amber-200 bg-gradient-to-br from-amber-50 to-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">Queue Health</p>
                <p class="mt-3 text-4xl font-black text-amber-700">{{ $queueHealth }}%</p>
                <p class="mt-2 text-sm text-amber-900">Share of approved tickets still waiting in the active queue.</p>
            </article>
        </section>

        <div class="grid gap-6 xl:grid-cols-[1.4fr_0.9fr]">
            <div class="space-y-6">
                <section class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-teal-600">Volume Trend</p>
                            <h3 class="mt-2 text-xl font-bold text-slate-900">Approved tickets per month</h3>
                        </div>
                        <a href="{{ route('reception.tickets.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-700 hover:text-slate-900">
                            Open approved queue
                        </a>
                    </div>

                    <div class="mt-6 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4">
                        <div class="relative" style="height:300px;">
                            <canvas id="receptionMonthlyChart"></canvas>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        @forelse($monthlyCounts as $month => $count)
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->format('M Y') }}</p>
                                <p class="mt-2 text-2xl font-bold text-slate-900">{{ $count }}</p>
                                <p class="mt-1 text-xs text-slate-500">approved tickets</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No approved tickets yet.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-teal-600">Project Load</p>
                            <h3 class="mt-2 text-xl font-bold text-slate-900">Which projects are generating the most approved tickets</h3>
                        </div>
                        <div class="rounded-2xl bg-slate-100 px-4 py-3 text-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Top Project</p>
                            <p class="mt-1 font-bold text-slate-900">{{ $topProject['name'] ?? 'No data yet' }}</p>
                            <p class="text-slate-500">{{ $topProject['total'] ?? 0 }} approved tickets</p>
                        </div>
                    </div>

                    <div class="mt-6 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4">
                        <div class="relative" style="height:300px;">
                            <canvas id="receptionProjectChart"></canvas>
                        </div>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse($projectCounts as $row)
                            <div class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $row['name'] }}</p>
                                    <p class="text-xs text-slate-500">Approved travel requests</p>
                                </div>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-bold text-emerald-700 ring-1 ring-emerald-200">{{ $row['total'] }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No approved tickets yet.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <div class="space-y-6">
                <section class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-teal-600">Quick Actions</p>
                    <h3 class="mt-2 text-xl font-bold text-slate-900">Common receptionist tasks</h3>

                    <div class="mt-6 space-y-3">
                        <a href="{{ route('reception.tickets.index') }}" class="flex items-center justify-between rounded-[1.5rem] border border-slate-200 bg-slate-50 px-5 py-4 transition hover:border-slate-300 hover:bg-white">
                            <div>
                                <p class="text-sm font-bold text-slate-900">Review Approved Tickets</p>
                                <p class="text-xs text-slate-500">Open the live queue and process current approvals.</p>
                            </div>
                            <span class="text-sm font-semibold text-slate-700">{{ $pendingProcessing }}</span>
                        </a>
                        <a href="{{ route('reception.tickets.archived') }}" class="flex items-center justify-between rounded-[1.5rem] border border-slate-200 bg-slate-50 px-5 py-4 transition hover:border-slate-300 hover:bg-white">
                            <div>
                                <p class="text-sm font-bold text-slate-900">Review Archived Tickets</p>
                                <p class="text-xs text-slate-500">Check what has already been processed and closed.</p>
                            </div>
                            <span class="text-sm font-semibold text-slate-700">{{ $archivedTotal }}</span>
                        </a>
                    </div>
                </section>

                <section class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-teal-600">Destination Demand</p>
                    <h3 class="mt-2 text-xl font-bold text-slate-900">Approvals by destination</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        Useful for spotting the busiest routes and jumping straight into destination-specific approved tickets.
                    </p>

                    <div class="mt-6 space-y-3">
                        @forelse($destinationCounts as $destination => $count)
                            <a href="{{ route('reception.destinations.show', urlencode($destination)) }}"
                                class="flex items-center justify-between rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-4 transition hover:border-cyan-200 hover:bg-cyan-50/60">
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $destination }}</p>
                                    <p class="text-xs text-slate-500">View approved tickets for this route</p>
                                </div>
                                <span class="rounded-full bg-cyan-100 px-3 py-1 text-sm font-bold text-cyan-700">{{ $count }}</span>
                            </a>
                        @empty
                            <p class="text-sm text-slate-500">No approved tickets yet.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const theme = () => {
                const isDark = document.documentElement.classList.contains('dark');
                return {
                    text: isDark ? '#e2e8f0' : '#475569',
                    grid: isDark ? 'rgba(148, 163, 184, 0.2)' : 'rgba(148, 163, 184, 0.25)',
                    tooltipBg: isDark ? 'rgba(2, 6, 23, 0.95)' : 'rgba(15, 23, 42, 0.92)'
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
                            backgroundColor: ['#0f766e', '#14b8a6', '#2dd4bf', '#5eead4', '#99f6e4', '#0f766e', '#14b8a6', '#2dd4bf'],
                            borderRadius: 10,
                            borderSkipped: false,
                            maxBarThickness: 42
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { backgroundColor: t.tooltipBg, padding: 12 }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { color: t.text, precision: 0 }, grid: { color: t.grid } },
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
                            backgroundColor: '#0f172a',
                            hoverBackgroundColor: '#1e293b',
                            borderRadius: 10,
                            borderSkipped: false,
                            maxBarThickness: 38
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { backgroundColor: t.tooltipBg, padding: 12 }
                        },
                        scales: {
                            y: { ticks: { color: t.text }, grid: { display: false } },
                            x: { beginAtZero: true, ticks: { color: t.text, precision: 0 }, grid: { color: t.grid } }
                        }
                    }
                });
                window.__charts.push(projectChart);
            }

            window.addEventListener('themechange', () => {
                const nt = applyTheme();
                (window.__charts || []).forEach((chart) => {
                    if (chart?.options?.plugins?.tooltip) {
                        chart.options.plugins.tooltip.backgroundColor = nt.tooltipBg;
                    }
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
