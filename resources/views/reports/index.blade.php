<x-app-layout>
    <x-slot name="pageTitle">Reports</x-slot>

    <div class="mx-auto max-w-6xl space-y-6">

        @include('reports._nav')

        <div class="flex justify-end">
            @include('reports._export', ['route' => 'reports.export.travel-requests', 'filters' => $filters])
        </div>

        {{-- Summary stats --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
            <div class="rounded-2xl border border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total</p>
                <p class="mt-2 text-2xl font-bold text-gray-800 dark:text-slate-100">{{ $stats['total'] }}</p>
            </div>
            <div class="rounded-2xl border border-green-200 bg-green-50 dark:bg-green-950/30 dark:border-green-900 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-400">Approved</p>
                <p class="mt-2 text-2xl font-bold text-green-800 dark:text-green-300">{{ $stats['approved'] }}</p>
            </div>
            <div class="rounded-2xl border border-red-200 bg-red-50 dark:bg-red-950/30 dark:border-red-900 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-red-700 dark:text-red-400">Rejected</p>
                <p class="mt-2 text-2xl font-bold text-red-800 dark:text-red-300">{{ $stats['rejected'] }}</p>
            </div>
            <div class="rounded-2xl border border-yellow-200 bg-yellow-50 dark:bg-yellow-950/30 dark:border-yellow-900 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-yellow-700 dark:text-yellow-400">Pending PM</p>
                <p class="mt-2 text-2xl font-bold text-yellow-800 dark:text-yellow-300">{{ $stats['pending_pm'] }}</p>
            </div>
            <div class="rounded-2xl border border-purple-200 bg-purple-50 dark:bg-purple-950/30 dark:border-purple-900 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-purple-700 dark:text-purple-400">Pending Commercial</p>
                <p class="mt-2 text-2xl font-bold text-purple-800 dark:text-purple-300">{{ $stats['pending_commercial'] }}</p>
            </div>
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 dark:bg-indigo-950/30 dark:border-indigo-900 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-400">Pending CEO</p>
                <p class="mt-2 text-2xl font-bold text-indigo-800 dark:text-indigo-300">{{ $stats['pending_ceo'] }}</p>
            </div>
        </div>

        {{-- Quick filters --}}
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('reports.index') }}"
                class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ empty($filters['head_office_only']) && empty($filters['status']) ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-200 hover:bg-gray-200' }}">
                All Projects
            </a>
            <a href="{{ route('reports.index', ['head_office_only' => 1]) }}"
                class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ !empty($filters['head_office_only']) ? 'bg-purple-600 text-white' : 'bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 hover:bg-purple-200' }}">
                Head Office Only
            </a>
            <a href="{{ route('reports.index', ['status' => 'approved']) }}"
                class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ ($filters['status'] ?? '') === 'approved' ? 'bg-green-600 text-white' : 'bg-green-100 dark:bg-green-950 text-green-700 dark:text-green-300 hover:bg-green-200' }}">
                Approved
            </a>
            <a href="{{ route('reports.index', ['status' => 'rejected']) }}"
                class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ ($filters['status'] ?? '') === 'rejected' ? 'bg-red-600 text-white' : 'bg-red-100 dark:bg-red-950 text-red-700 dark:text-red-300 hover:bg-red-200' }}">
                Rejected
            </a>
            <a href="{{ route('reports.index', ['status' => 'pending_commercial']) }}"
                class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ ($filters['status'] ?? '') === 'pending_commercial' ? 'bg-purple-600 text-white' : 'bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 hover:bg-purple-200' }}">
                Pending My Approval
            </a>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
            <h2 class="text-base font-bold text-gray-700 dark:text-slate-100 mb-4">Filter Requests</h2>
            <form action="{{ route('reports.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

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
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Destination</label>
                    <input type="text" name="destination" value="{{ $filters['destination'] ?? '' }}"
                        class="w-full border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none transition"
                        placeholder="Country / City">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Purpose</label>
                    <input type="text" name="purpose" value="{{ $filters['purpose'] ?? '' }}"
                        class="w-full border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none transition"
                        placeholder="Reason for travel">
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
                    <a href="{{ route('reports.index') }}"
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

        {{-- Charts --}}
        <div class="grid gap-6 lg:grid-cols-2">
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
                <h2 class="text-base font-bold text-gray-700 dark:text-slate-100 mb-4">Requests per Project</h2>
                <div style="height: 260px;">
                    <canvas id="reportChart"></canvas>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
                <h2 class="text-base font-bold text-gray-700 dark:text-slate-100 mb-4">Status Breakdown</h2>
                <div style="height: 260px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Results Table --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-700 dark:text-slate-100">Detailed Results</h2>
                <span class="text-xs bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 px-2.5 py-1 rounded-full font-semibold">
                    {{ $travelRequests->total() }} records
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-slate-800 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">User</th>
                            <th class="px-6 py-3">Project</th>
                            <th class="px-6 py-3">Route / Purpose</th>
                            <th class="px-6 py-3">Travel Date</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
                        @forelse ($travelRequests as $record)
                            @php
                                $statusMap = [
                                    'pending_pm' => 'bg-yellow-100 text-yellow-800',
                                    'pending_hod' => 'bg-purple-100 text-purple-800',
                                    'pending_commercial' => 'bg-purple-100 text-purple-800',
                                    'pending_ceo' => 'bg-indigo-100 text-indigo-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <tr class="hover:bg-indigo-50/30 dark:hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4 text-gray-400">{{ $record->id }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-800 dark:text-slate-100">{{ $record->user->name }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-slate-400">
                                    {{ optional($record->project)->name ?? 'N/A' }}
                                    @if(optional($record->project)->isHeadOffice())
                                        <span class="ml-1 rounded-full bg-purple-100 text-purple-700 px-1.5 py-0.5 text-[10px] font-bold">HO</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-medium text-gray-700 dark:text-slate-200">{{ $record->origin }} → {{ $record->destination }}</span>
                                    <br><span class="text-xs text-gray-400">{{ $record->purpose }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-slate-400">{{ $record->travel_date }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusMap[$record->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('travel-requests.show', $record) }}"
                                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                    No records found matching the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($travelRequests->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700">
                    {{ $travelRequests->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#cbd5e1' : '#6b7280';
            const gridColor = isDark ? '#1f2937' : '#f1f5f9';

            const reportCtx = document.getElementById('reportChart');
            if (reportCtx) {
                new Chart(reportCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($chartData['labels']) !!},
                        datasets: [{
                            label: 'Requests',
                            data: {!! json_encode($chartData['data']) !!},
                            backgroundColor: ['#6366f1', '#818cf8', '#a855f7', '#10b981', '#34d399', '#f59e0b', '#ef4444'],
                            borderRadius: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1, color: textColor }, grid: { color: gridColor } },
                            x: { ticks: { color: textColor }, grid: { display: false } }
                        }
                    }
                });
            }

            const statusCtx = document.getElementById('statusChart');
            if (statusCtx) {
                new Chart(statusCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($statusChartData['labels']) !!},
                        datasets: [{
                            data: {!! json_encode($statusChartData['data']) !!},
                            backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#8b5cf6', '#6366f1'],
                            borderColor: isDark ? '#0b1220' : '#ffffff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { color: textColor, font: { size: 11 } } }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
