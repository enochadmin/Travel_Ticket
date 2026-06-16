<x-app-layout>
    <x-slot name="pageTitle">Most Requested Projects</x-slot>

    <div class="mx-auto max-w-6xl space-y-6">

        @include('reports._nav')

        <div class="flex justify-end">
            @include('reports._export', ['route' => 'reports.export.most-requested-projects', 'filters' => $filters])
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-900 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Requests in scope</p>
                <p class="mt-2 text-3xl font-bold text-gray-800 dark:text-slate-100">{{ $totalRequests }}</p>
            </div>
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 dark:bg-indigo-950/30 dark:border-indigo-900 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-400">Projects ranked</p>
                <p class="mt-2 text-3xl font-bold text-indigo-800 dark:text-indigo-300">{{ $projectStats->count() }}</p>
            </div>
            <div class="rounded-2xl border border-purple-200 bg-purple-50 dark:bg-purple-950/30 dark:border-purple-900 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-purple-700 dark:text-purple-400">Top project</p>
                @if($topProject)
                    <p class="mt-2 text-xl font-bold text-purple-800 dark:text-purple-300">{{ $topProject->project_name }}</p>
                    <p class="text-sm text-purple-600 dark:text-purple-400">{{ $topProject->request_count }} request{{ $topProject->request_count === 1 ? '' : 's' }}</p>
                @else
                    <p class="mt-2 text-xl font-bold text-purple-800 dark:text-purple-300">—</p>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
            <h2 class="text-base font-bold text-gray-700 dark:text-slate-100 mb-1">Filter Data</h2>
            <form action="{{ route('reports.most-requested-projects') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mt-4">

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
                    <a href="{{ route('reports.most-requested-projects') }}"
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

        @if($projectStats->isNotEmpty())
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
                <h2 class="text-base font-bold text-gray-700 dark:text-slate-100 mb-4">Top {{ min(10, $projectStats->count()) }} Projects</h2>
                <div style="height: 320px;">
                    <canvas id="projectChart"></canvas>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-700 dark:text-slate-100">Project Rankings</h2>
                <span class="text-xs bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 px-2.5 py-1 rounded-full font-semibold">
                    {{ $projectStats->count() }} projects
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-slate-800 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3 w-16">Rank</th>
                            <th class="px-6 py-3">Project</th>
                            <th class="px-6 py-3">Region</th>
                            <th class="px-6 py-3 text-right">Requests</th>
                            <th class="px-6 py-3 text-right">Approved</th>
                            <th class="px-6 py-3 text-right">Pending</th>
                            <th class="px-6 py-3 text-right">Rejected</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
                        @forelse ($projectStats as $row)
                            <tr class="hover:bg-indigo-50/30 dark:hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4 font-semibold text-gray-400">{{ $row->rank }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-gray-800 dark:text-slate-100">{{ $row->project_name }}</span>
                                    @if($row->is_head_office)
                                        <span class="ml-2 rounded-full bg-purple-100 text-purple-700 px-2 py-0.5 text-[10px] font-bold uppercase">HO</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-slate-400">{{ $row->region ?? '—' }}</td>
                                <td class="px-6 py-4 text-right font-bold text-indigo-600 dark:text-indigo-400">{{ $row->request_count }}</td>
                                <td class="px-6 py-4 text-right text-green-600 dark:text-green-400">{{ $row->approved_count }}</td>
                                <td class="px-6 py-4 text-right text-yellow-600 dark:text-yellow-400">{{ $row->pending_count }}</td>
                                <td class="px-6 py-4 text-right text-red-600 dark:text-red-400">{{ $row->rejected_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                    No travel requests match the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($projectStats->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#cbd5e1' : '#6b7280';
                const gridColor = isDark ? '#1f2937' : '#f1f5f9';
                const ctx = document.getElementById('projectChart');

                if (ctx) {
                    new Chart(ctx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($chartData['labels']) !!},
                            datasets: [{
                                label: 'Requests',
                                data: {!! json_encode($chartData['data']) !!},
                                backgroundColor: '#6366f1',
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
