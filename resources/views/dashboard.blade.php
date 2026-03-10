<x-app-layout>
    <x-slot name="pageTitle">Dashboard</x-slot>

    {{-- ===================== ADMIN DASHBOARD ===================== --}}
    @hasrole('admin')
    <div class="space-y-8">

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @php
                $adminCards = [
                    ['label' => 'Total Users', 'value' => $totalUsers, 'from' => '#3b82f6', 'to' => '#60a5fa', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                    ['label' => 'Total Projects', 'value' => $totalProjects, 'from' => '#8b5cf6', 'to' => '#a78bfa', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                    ['label' => 'Active Projects', 'value' => $activeProjects, 'from' => '#10b981', 'to' => '#34d399', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label' => 'Unassigned Users', 'value' => $unassignedUsers, 'from' => '#ef4444', 'to' => '#f87171', 'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
                ];
            @endphp
            @foreach($adminCards as $card)
                <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center justify-between border border-gray-100">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">{{ $card['label'] }}</p>
                        <p class="text-3xl font-extrabold text-gray-800">{{ $card['value'] }}</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-sm"
                        style="background: linear-gradient(135deg,{{ $card['from'] }},{{ $card['to'] }})">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}" />
                        </svg>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Charts Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- 1. Users by Role (Doughnut) --}}
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex flex-col">
                <h2 class="text-base font-bold text-gray-800 mb-4">Users Distribution by Role</h2>
                <div class="flex-1 relative" style="min-height: 250px;">
                    <canvas id="roleChart"></canvas>
                </div>
            </div>

            {{-- 2. Projects by Discipline (Bar) --}}
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex flex-col">
                <h2 class="text-base font-bold text-gray-800 mb-4">Projects by Discipline</h2>
                <div class="flex-1 relative" style="min-height: 250px;">
                    <canvas id="disciplineChart"></canvas>
                </div>
            </div>

            {{-- 3. Projects by Status (Pie) --}}
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex flex-col">
                <h2 class="text-base font-bold text-gray-800 mb-4">Projects by Status</h2>
                <div class="flex-1 relative" style="min-height: 250px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            {{-- 4. Users per Project (Horizontal Bar) --}}
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex flex-col">
                <h2 class="text-base font-bold text-gray-800 mb-4">Users per Project</h2>
                <div class="flex-1 relative" style="min-height: 250px;">
                    <canvas id="projectUsersChart"></canvas>
                </div>
            </div>

        </div>

        {{-- Latest Users Feed --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-6">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800">Recently Registered Users</h2>
                <a href="{{ route('users.index') }}"
                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Manage all users →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Role</th>
                            <th class="px-6 py-3 text-left">Assigned Project</th>
                            <th class="px-6 py-3 text-right">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($latestUsers as $u)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-800">{{ $u->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $u->email }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="bg-indigo-50 border border-indigo-100 text-indigo-700 px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider">
                                        {{ $u->roles->first()?->name ?? 'None' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($u->project)
                                        <span class="text-gray-700 font-medium">{{ $u->project->name }}</span>
                                    @else
                                        <span class="text-red-500 italic text-xs">Unassigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-gray-500 text-xs">
                                    {{ $u->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Admin Chart Scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartTheme = () => {
                const isDark = document.documentElement.classList.contains('dark');
                return {
                    isDark,
                    text: isDark ? '#e2e8f0' : '#475569',
                    grid: isDark ? 'rgba(148, 163, 184, 0.2)' : 'rgba(148, 163, 184, 0.3)',
                    border: isDark ? '#0b1220' : '#ffffff',
                    tooltipBg: isDark ? 'rgba(2, 6, 23, 0.95)' : 'rgba(17, 24, 39, 0.9)'
                };
            };

            const applyChartTheme = () => {
                const t = chartTheme();
                Chart.defaults.color = t.text;
                Chart.defaults.borderColor = t.grid;
                return t;
            };

            const t = applyChartTheme();
            window.__charts = window.__charts || [];

            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { family: "'Inter', sans-serif" } } },
                    tooltip: { backgroundColor: t.tooltipBg, padding: 12, cornerRadius: 8, titleFont: { size: 14 } }
                }
            };

            // 1. Roles Doughnut
            const roleCtx = document.getElementById('roleChart');
            if (roleCtx) {
                const roleChart = new Chart(roleCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($roleChartLabels) !!},
                        datasets: [{
                            data: {!! json_encode($roleChartData) !!},
                            backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#64748b'],
                            borderWidth: 2, borderColor: t.border
                        }]
                    },
                    options: { ...commonOptions, cutout: '65%' }
                });
                window.__charts.push(roleChart);
            }

            // 2. Discipline Bar
            const discCtx = document.getElementById('disciplineChart');
            if(discCtx) {
                const disciplineChart = new Chart(discCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($disciplineChartLabels) !!},
                        datasets: [{
                            label: 'Total Projects',
                            data: {!! json_encode($disciplineChartData) !!},
                            backgroundColor: '#3b82f6',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        ...commonOptions,
                        plugins: { legend: { display: false }, tooltip: commonOptions.plugins.tooltip },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1, color: t.text }, grid: { color: t.grid } },
                            x: { grid: { display: false }, ticks: { color: t.text } }
                        }
                    }
                });
                window.__charts.push(disciplineChart);
            }

            // 3. Status Pie
            const statusCtx = document.getElementById('statusChart');
            if(statusCtx) {
                const statusChart = new Chart(statusCtx.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: {!! json_encode($statusChartLabels) !!},
                        datasets: [{
                            data: {!! json_encode($statusChartData) !!},
                            backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444'],
                            borderWidth: 2, borderColor: t.border
                        }]
                    },
                    options: commonOptions
                });
                window.__charts.push(statusChart);
            }

            // 4. Users per Project Vertical Bar
            const upCtx = document.getElementById('projectUsersChart');
            if(upCtx) {
                const projectUsersChart = new Chart(upCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($projectUsersChartLabels) !!},
                        datasets: [{
                            label: 'Assigned Users',
                            data: {!! json_encode($projectUsersChartData) !!},
                            backgroundColor: '#8b5cf6',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        ...commonOptions,
                        plugins: { legend: { display: false }, tooltip: commonOptions.plugins.tooltip },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1, color: t.text }, grid: { color: t.grid } },
                            x: { grid: { display: false }, ticks: { color: t.text } }
                        }
                    }
                });
                window.__charts.push(projectUsersChart);
            }

            window.addEventListener('themechange', () => {
                const nt = applyChartTheme();
                (window.__charts || []).forEach((chart) => {
                    if (chart?.options?.scales) {
                        if (chart.options.scales.x?.ticks) chart.options.scales.x.ticks.color = nt.text;
                        if (chart.options.scales.y?.ticks) chart.options.scales.y.ticks.color = nt.text;
                        if (chart.options.scales.x?.grid) chart.options.scales.x.grid.color = nt.grid;
                        if (chart.options.scales.y?.grid) chart.options.scales.y.grid.color = nt.grid;
                    }
                    if (chart?.data?.datasets) {
                        chart.data.datasets.forEach((ds) => {
                            if (Array.isArray(ds.borderColor) || typeof ds.borderColor === 'string') {
                                ds.borderColor = nt.border;
                            }
                        });
                    }
                    chart.update();
                });
            });
        });
    </script>
    @endhasrole

    {{-- ===================== CEO DASHBOARD ===================== --}}
    @hasrole('ceo')
    <div class="space-y-8">

        {{-- CEO Stat Cards --}}
        <h2 class="text-2xl font-bold text-gray-800">Executive Overview</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
            @php
                $ceoCards = [
                    ['label' => 'Total Projects', 'value' => $totalProjects, 'from' => '#3b82f6', 'to' => '#60a5fa', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                    ['label' => 'Total Requests', 'value' => $totalRequests, 'from' => '#8b5cf6', 'to' => '#a78bfa', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['label' => 'Approved Requests', 'value' => $approved, 'from' => '#10b981', 'to' => '#34d399', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label' => 'Rejected Requests', 'value' => $rejected, 'from' => '#ef4444', 'to' => '#f87171', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
            @endphp
            @foreach($ceoCards as $card)
                <div class="bg-white rounded-2xl shadow-sm p-6 flex items-center justify-between border border-gray-100">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">{{ $card['label'] }}</p>
                        <p class="text-3xl font-extrabold text-gray-800">{{ $card['value'] }}</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-sm"
                        style="background: linear-gradient(135deg,{{ $card['from'] }},{{ $card['to'] }})">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}" />
                        </svg>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- CEO Chart: Compare Requests Across Projects --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Pie chart card (separate DIV) --}}
            <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700 mb-3 text-center">Requests by Status</h3>
                <div class="relative" style="height:220px;">
                    <canvas id="ceoStatusChart"></canvas>
                </div>
            </div>

            {{-- Bar chart card (separate DIV) --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Project Request Volume Comparison</h2>
                <div style="height:320px;">
                    <canvas id="ceoChart"></canvas>
                </div>
            </div>
        </div>

        {{-- CEO: Active Projects Cards (clickable, shows approved requests per project) --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-base font-bold text-gray-800 mb-4">Active Projects — Approved Requests</h3>
            @if($projects->isEmpty())
                <p class="text-sm text-gray-500">No projects available.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($projects as $project)
                        @php
                            $approvedCount = $project->travelRequests->where('status', 'approved')->count();
                        @endphp
                        <a href="{{ route('travel-requests.index', ['project_id' => $project->id, 'status' => 'approved']) }}"
                           class="group block bg-gray-50 hover:bg-white border border-gray-100 rounded-xl p-4 transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $project->name }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ optional($project->manager)->name ?? 'No manager' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-indigo-600">{{ $approvedCount }}</p>
                                    <p class="text-xs text-gray-500">Approved</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Dynamic Projects Accordion Using Alpine.js --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">Projects Breakdown</h2>
                <span
                    class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full font-bold uppercase tracking-wider">Click
                    to Expand</span>
            </div>

            @php
                $projectChunks = $projects->chunk(5);
                $chunksCount = $projectChunks->count();
            @endphp

            <div x-data="{ page: 0, pages: {{ $chunksCount }}, expandedProject: null }" class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-800">Projects Breakdown</h2>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="page = Math.max(0, page - 1)"
                            :disabled="page === 0"
                            class="px-3 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 disabled:opacity-50">
                            ‹
                        </button>
                        <button type="button" @click="page = Math.min(pages - 1, page + 1)"
                            :disabled="page >= pages - 1"
                            class="px-3 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 disabled:opacity-50">
                            ›
                        </button>
                    </div>
                </div>

                <div class="overflow-hidden mt-4">
                    <div class="flex transition-transform duration-300" :style="`transform: translateX(-${page * 100}%);`">
                        @foreach($projectChunks as $chunk)
                            <div class="w-full flex-shrink-0 px-2">
                                <div class="divide-y divide-gray-100">
                                    @foreach($chunk as $project)
                                        @php
                                            $projectApproved = $project->travelRequests->where('status', 'approved');
                                            $projectRejected = $project->travelRequests->where('status', 'rejected');
                                        @endphp
                                        <div class="project-accordion">
                                            {{-- Accordion Header --}}
                                            <button
                                                @click="expandedProject === {{ $project->id }} ? expandedProject = null : expandedProject = {{ $project->id }}"
                                                class="w-full px-6 py-5 flex items-center justify-between hover:bg-gray-50 transition duration-150 focus:outline-none">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                                                        {{ substr($project->name, 0, 1) }}
                                                    </div>
                                                    <div class="text-left">
                                                        <p class="font-bold text-gray-800 text-base">{{ $project->name }}</p>
                                                        <p class="text-xs text-gray-500 mt-0.5">Manager: <span
                                                                class="font-medium text-gray-700">{{ optional($project->manager)->name ?? 'Unassigned' }}</span>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-6">
                                                    <div class="flex gap-3 text-sm">
                                                        <span
                                                            class="bg-green-100 text-green-800 px-2 py-1 rounded-md font-semibold font-mono">{{ $projectApproved->count() }}
                                                            ✓</span>
                                                        <span
                                                            class="bg-red-100 text-red-800 px-2 py-1 rounded-md font-semibold font-mono">{{ $projectRejected->count() }}
                                                            ✗</span>
                                                    </div>
                                                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                                                        :class="expandedProject === {{ $project->id }} ? 'transform rotate-180' : ''"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </button>

                                            {{-- Accordion Content --}}
                                            <div x-show="expandedProject === {{ $project->id }}" x-collapse
                                                class="bg-gray-50 border-t border-gray-100 px-6 py-4">

                                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                                    {{-- Approved List --}}
                                                    <div class="bg-white rounded-xl shadow-sm border border-green-100 overflow-hidden">
                                                        <h3 class="bg-green-500 text-white text-xs font-bold uppercase py-2 px-4">Approved
                                                            Requests</h3>
                                                        @if($projectApproved->isEmpty())
                                                            <p class="p-4 text-sm text-gray-500 italic">No approved requests.</p>
                                                        @else
                                                            <ul class="divide-y divide-gray-100">
                                                                @foreach($projectApproved as $req)
                                                                    <li class="p-4 text-sm">
                                                                        <div class="flex justify-between items-start mb-2">
                                                                            <span
                                                                                class="font-semibold text-gray-800">{{ optional($req->user)->name }}</span>
                                                                            <span
                                                                                class="text-xs text-gray-500 font-medium">{{ $req->destination }}</span>
                                                                        </div>
                                                                        <div
                                                                            class="text-xs text-gray-600 bg-gray-50 p-2 rounded border border-gray-100">
                                                                            <p class="mb-1"><span class="font-medium">Approved On:</span>
                                                                                {{ $req->updated_at->format('M d, Y H:i') }}</p>
                                                                            <p class="mb-1"><span class="font-medium text-purple-700">PM:</span>
                                                                                {{ optional($req->pm)->name ?? 'N/A' }}</p>
                                                                            <p><span class="font-medium text-green-700">Director:</span>
                                                                                {{ optional($req->hod)->name ?? 'N/A' }}</p>
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>

                                                    {{-- Rejected List --}}
                                                    <div class="bg-white rounded-xl shadow-sm border border-red-100 overflow-hidden">
                                                        <h3 class="bg-red-500 text-white text-xs font-bold uppercase py-2 px-4">Rejected
                                                            Requests</h3>
                                                        @if($projectRejected->isEmpty())
                                                            <p class="p-4 text-sm text-gray-500 italic">No rejected requests.</p>
                                                        @else
                                                            <ul class="divide-y divide-gray-100">
                                                                @foreach($projectRejected as $req)
                                                                    <li class="p-4 text-sm">
                                                                        <div class="flex justify-between items-start mb-2">
                                                                            <span
                                                                                class="font-semibold text-gray-800">{{ optional($req->user)->name }}</span>
                                                                            <span
                                                                                class="text-xs text-gray-500 font-medium">{{ $req->destination }}</span>
                                                                        </div>
                                                                        <div
                                                                            class="text-xs text-gray-600 bg-gray-50 p-2 rounded border border-gray-100">
                                                                            <p class="mb-1"><span class="font-medium">Rejected On:</span>
                                                                                {{ $req->updated_at->format('M d, Y H:i') }}</p>
                                                                            @if($req->hod_id)
                                                                                <p><span class="font-medium text-red-700">Rejected By Director:</span>
                                                                                    {{ optional($req->hod)->name }}</p>
                                                                            @elseif($req->pm_id)
                                                                                <p><span class="font-medium text-red-700">Rejected By PM:</span>
                                                                                    {{ optional($req->pm)->name }}</p>
                                                                            @else
                                                                                <p><span class="font-medium text-red-700">Rejected By:</span> Unknown</p>
                                                                            @endif
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctxCeo = document.getElementById('ceoChart');
            if (ctxCeo) {
                // Ensure ceoChartLabels contains only project names and ceoChartData matches its length
                // If you generate these in the controller, use:
                // $ceoChartLabels = $projects->pluck('name')->toArray();
                // $ceoChartData = $projects->map(fn($p) => $p->travelRequests->count())->toArray();
                const labels = {!! json_encode($ceoChartLabels) !!};
                const data = {!! json_encode($ceoChartData) !!};
                if (labels.length !== data.length) {
                    console.warn('ceoChartLabels and ceoChartData length mismatch!');
                }
                // Build colors to highlight highest and lowest bars
                const numericData = data.map(v => Number(v) || 0);
                const maxVal = Math.max(...numericData);
                const minVal = Math.min(...numericData);
                const bgColors = numericData.map(v => {
                    if (v === maxVal && v === minVal) return '#3b82f6'; // all equal
                    if (v === maxVal) return '#ef4444'; // highest in red
                    if (v === minVal) return '#10b981'; // lowest in green
                    return '#3b82f6'; // default blue
                });

                // choose stepSize = 1 for small ranges to make counts clear
                const maxCount = maxVal;
                const stepSize = maxCount <= 10 ? 1 : undefined;

                new Chart(ctxCeo.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Ticket Count',
                            data: numericData,
                            backgroundColor: bgColors,
                            borderRadius: 6,
                            borderSkipped: false,
                        }]
                    },
                    options: {
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
                                displayColors: false,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: stepSize ? { stepSize: stepSize, color: '#6b7280' } : { color: '#6b7280' },
                                grid: { color: '#f3f4f6', drawBorder: false }
                            },
                            x: {
                                type: 'category',
                                ticks: { color: '#4b5563', font: { weight: '600' } },
                                grid: { display: false, drawBorder: false }
                            }
                        },
                        interaction: { intersect: false, mode: 'index' }
                    }
                });
                }

                // Status pie chart (Approved / Rejected / Pending PM / Pending Commercial)
                const statusCtx = document.getElementById('ceoStatusChart');
                if (statusCtx) {
                    const statusLabels = {!! json_encode($ceoStatusChartLabels) !!};
                    const statusData = {!! json_encode($ceoStatusChartData) !!};
                    new Chart(statusCtx.getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: statusLabels,
                            datasets: [{
                                data: statusData,
                                backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#8b5cf6'],
                                borderColor: '#ffffff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom', labels: { font: { family: "'Inter', sans-serif" } } },
                                tooltip: { backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 10, cornerRadius: 6 }
                            }
                        }
                    });
            }
        });
    </script>
    @endhasrole

    {{-- ============= COMMERCIAL DIRECTOR DASHBOARD ============= --}}
    @hasrole('commercial-director')
    <div class="space-y-8">

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            @php
                $dirCards = [
                    ['label' => 'Awaiting My Approval', 'value' => $pendingCommercial, 'from' => '#a855f7', 'to' => '#c084fc', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['label' => 'Approved', 'value' => $approved, 'from' => '#10b981', 'to' => '#34d399', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label' => 'Rejected', 'value' => $rejected, 'from' => '#ef4444', 'to' => '#f87171', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
            @endphp
            @foreach($dirCards as $card)
                <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4 border border-gray-100">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                        style="background: linear-gradient(135deg,{{ $card['from'] }},{{ $card['to'] }})">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $card['value'] }}</p>
                        <p class="text-xs text-gray-500 font-medium">{{ $card['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Approval Queue --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between"
                style="background: linear-gradient(90deg,#faf5ff,#fff)">
                <h2 class="text-base font-semibold text-gray-700">🗂 Requests Awaiting Director Approval</h2>
                <span
                    class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full font-semibold">{{ $pendingCommercial }}
                    pending</span>
            </div>
            @if($commercialQueue->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-4xl mb-3">✅</p>
                    <p class="text-gray-500 font-medium">All clear! No pending approvals.</p>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($commercialQueue as $req)
                        <div x-data="{ rejectModal: false }" class="flex items-start justify-between px-6 py-4 hover:bg-gray-50 transition">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 text-sm">{{ $req->user->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    <span class="font-medium text-gray-600">{{ $req->destination }}</span>
                                    · {{ $req->travel_date }}
                                    · Project: {{ optional($req->project)->name ?? 'N/A' }}
                                </p>
                                {{-- PM Approval Badge --}}
                                @if($req->pm_id)
                                <div class="flex items-center gap-1.5 mt-1.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-[11px] font-semibold">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        PM Approved
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        by <span class="font-semibold text-gray-700">{{ optional($req->pm)->name ?? '—' }}</span>
                                        @if($req->pm_approved_at)
                                            · {{ $req->pm_approved_at->format('M d, Y h:i A') }}
                                        @endif
                                    </span>
                                </div>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 ml-4">
                                <a href="{{ route('travel-requests.show', $req) }}"
                                    class="text-xs text-indigo-600 hover:underline font-medium">View</a>
                                <form action="{{ route('travel-requests.approve', $req) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="text-xs bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg font-semibold transition">Approve</button>
                                </form>
                                <button type="button" @click="rejectModal = true"
                                    class="text-xs bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg font-semibold transition">Reject</button>
                            </div>

                            {{-- Reject Modal --}}
                            <div x-show="rejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.5);">
                                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8" @click.outside="rejectModal = false">
                                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-800 text-center mb-1">Reject: {{ $req->destination }}</h3>
                                    <p class="text-sm text-gray-500 text-center mb-5">Provide a reason. This will be visible to the requester.</p>
                                    <form action="{{ route('travel-requests.reject', $req) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <textarea name="rejection_reason" rows="3" required
                                            placeholder="Reason for rejection..."
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-red-300 resize-none"></textarea>
                                        <div class="flex gap-3">
                                            <button type="submit" class="flex-1 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white font-semibold text-sm transition">Confirm</button>
                                            <button type="button" @click="rejectModal = false" class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @endhasrole

    {{-- ============= HEAD-OFFICE-DIRECTOR (LEGACY) DASHBOARD ============= --}}
    @hasrole('head-office-director')
    <div class="space-y-8">
        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            @php
                $hodCards = [
                    ['label' => 'Awaiting My Approval', 'value' => $pendingHod, 'from' => '#3b82f6', 'to' => '#60a5fa', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['label' => 'Approved', 'value' => $approved, 'from' => '#10b981', 'to' => '#34d399', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label' => 'Rejected', 'value' => $rejected, 'from' => '#ef4444', 'to' => '#f87171', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
            @endphp
            @foreach($hodCards as $card)
                <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4 border border-gray-100">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                        style="background: linear-gradient(135deg,{{ $card['from'] }},{{ $card['to'] }})">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $card['value'] }}</p>
                        <p class="text-xs text-gray-500 font-medium">{{ $card['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Approval Queue --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between"
                style="background: linear-gradient(90deg,#eff6ff,#fff)">
                <h2 class="text-base font-semibold text-gray-700">🗂 Requests Awaiting HOD Approval</h2>
                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-semibold">{{ $pendingHod }}
                    pending</span>
            </div>
            @if($hodQueue->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-4xl mb-3">✅</p>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($hodQueue as $req)
                        <div class="flex items-center justify-between px-6 py-4">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 text-sm">{{ $req->user->name }}</p>
                            </div>
                            <div class="flex items-center gap-2 ml-4">
                                <form action="{{ route('travel-requests.approve', $req) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button
                                        class="text-xs bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg">Approve</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @endhasrole

    {{-- ============= PROJECT MANAGER DASHBOARD ============= --}}
    @hasrole('project-manager')
    <div class="space-y-8">

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            @php
                $pmCards = [
                    ['label' => 'Team Total', 'value' => $teamTotal, 'from' => '#6366f1', 'to' => '#818cf8', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['label' => 'Awaiting My Review', 'value' => $pendingPm, 'from' => '#f59e0b', 'to' => '#fbbf24', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label' => 'Approved', 'value' => $approved, 'from' => '#10b981', 'to' => '#34d399', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
            @endphp
            @foreach($pmCards as $card)
                <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4 border border-gray-100">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                        style="background: linear-gradient(135deg,{{ $card['from'] }},{{ $card['to'] }})">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $card['value'] }}</p>
                        <p class="text-xs text-gray-500 font-medium">{{ $card['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- PM Approval Queue --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between"
                style="background:linear-gradient(90deg,#fffbeb,#fff)">
                <h2 class="text-base font-semibold text-gray-700">📋 Team Requests Awaiting Your Review</h2>
                <span
                    class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full font-semibold">{{ $pendingPm }}
                    pending</span>
            </div>
            @if($pmQueue->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-4xl mb-3">✅</p>
                    <p class="text-gray-500 font-medium">No pending reviews from your team.</p>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($pmQueue as $req)
                        <div x-data="{ rejectModal: false }" class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 text-sm">{{ $req->user->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    <span class="font-medium text-gray-600">{{ $req->destination }}</span>
                                    · {{ $req->travel_date }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2 ml-4">
                                <a href="{{ route('travel-requests.show', $req) }}"
                                    class="text-xs text-indigo-600 hover:underline font-medium">View</a>
                                <form action="{{ route('travel-requests.approve', $req) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="text-xs bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg font-semibold transition">Approve</button>
                                </form>
                                <button type="button" @click="rejectModal = true"
                                    class="text-xs bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg font-semibold transition">Reject</button>
                            </div>

                            {{-- Reject Modal --}}
                            <div x-show="rejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.5);">
                                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8" @click.outside="rejectModal = false">
                                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-800 text-center mb-1">Reject: {{ $req->destination }}</h3>
                                    <p class="text-sm text-gray-500 text-center mb-5">Provide a reason. This will be visible to the requester.</p>
                                    <form action="{{ route('travel-requests.reject', $req) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <textarea name="rejection_reason" rows="3" required
                                            placeholder="Reason for rejection..."
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-red-300 resize-none"></textarea>
                                        <div class="flex gap-3">
                                            <button type="submit" class="flex-1 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white font-semibold text-sm transition">Confirm</button>
                                            <button type="button" @click="rejectModal = false" class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @endhasrole

    {{-- ============= REQUESTER (regular user) DASHBOARD ============= --}}
    @hasrole('user')
    @php
        $finalNotification = Auth::user()->unreadNotifications()
            ->whereIn('data->type', ['success', 'error'])
            ->latest()
            ->first();
    @endphp

    {{-- Final Status Popup --}}
    @if($finalNotification)
        <div x-data="{ showPopup: true }" x-show="showPopup"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            style="background: rgba(0,0,0,0.5);"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">
            <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 relative text-center"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100">

                {{-- Icon --}}
                @if($finalNotification->data['type'] === 'success')
                    <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-green-700 mb-2">Ticket Approved! 🎉</h2>
                @else
                    <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-red-700 mb-2">Ticket Rejected</h2>
                @endif

                <p class="text-gray-600 text-sm mb-6">{{ $finalNotification->data['message'] }}</p>

                <div class="flex gap-3 justify-center">
                    <a href="{{ route('notifications.read', $finalNotification->id) }}"
                        class="px-5 py-2.5 rounded-xl font-semibold text-sm {{ $finalNotification->data['type'] === 'success' ? 'bg-green-500 hover:bg-green-600 text-white' : 'bg-red-500 hover:bg-red-600 text-white' }} transition">
                        View Ticket
                    </a>
                    <form method="POST" action="{{ route('notifications.markAllRead') }}">
                        @csrf
                        <button type="submit" @click="showPopup = false"
                            class="px-5 py-2.5 rounded-xl font-semibold text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 transition">
                            Dismiss
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <div class="space-y-8">

        {{-- Hero Banner --}}
        <div class="rounded-2xl p-8 flex items-center justify-between overflow-hidden relative"
            style="background: linear-gradient(135deg,#1e1b4b,#4f46e5);">
            <div class="relative z-10">
                <p class="text-indigo-200 text-sm font-medium mb-1">Welcome back,</p>
                <h2 class="text-2xl font-bold text-white mb-4">{{ Auth::user()->name }}</h2>
                @if(Auth::user()->project_id)
                    <a href="{{ route('travel-requests.create') }}"
                        class="inline-flex items-center gap-2 bg-white text-indigo-700 font-semibold text-sm px-5 py-2.5 rounded-xl shadow hover:shadow-md transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Raise Ticket Here
                    </a>
                @else
                    <p class="text-indigo-300 text-sm">You are not assigned to a project yet. Contact your admin.</p>
                @endif
            </div>
            <div class="absolute right-0 top-0 h-full w-48 opacity-10">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"
                    class="w-full h-full text-white fill-current">
                    <path
                        d="M45.2,-58.7C57.8,-49.5,66.4,-34.7,70.1,-18.6C73.8,-2.6,72.7,14.8,65.1,28.8C57.5,42.9,43.5,53.7,28.1,60.5C12.7,67.3,-4,70.2,-19.3,66.1C-34.6,62,-48.4,50.9,-58.3,36.6C-68.2,22.3,-74.2,4.7,-71.6,-11.5C-69,-27.7,-57.8,-42.5,-44.2,-51.7C-30.7,-60.9,-15.3,-64.5,0.9,-65.6C17.1,-66.7,32.6,-68,45.2,-58.7Z"
                        transform="translate(100 100)" />
                </svg>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @php
                $myCards = [
                    ['label' => 'Total Tickets', 'value' => $myTotal, 'from' => '#6366f1', 'to' => '#818cf8'],
                    ['label' => 'Pending', 'value' => $myPending, 'from' => '#f59e0b', 'to' => '#fbbf24'],
                    ['label' => 'Approved', 'value' => $myApproved, 'from' => '#10b981', 'to' => '#34d399'],
                    ['label' => 'Rejected', 'value' => $myRejected, 'from' => '#ef4444', 'to' => '#f87171'],
                ];
            @endphp
            @foreach($myCards as $card)
                <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100 text-center">
                    <p class="text-3xl font-bold"
                        style="background: linear-gradient(135deg,{{ $card['from'] }},{{ $card['to'] }});-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
                        {{ $card['value'] }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1 font-medium">{{ $card['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- My Recent Requests --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-700">🧳 My Recent Tickets</h2>
                <a href="{{ route('travel-requests.index') }}"
                    class="text-xs text-indigo-600 hover:underline font-medium">View all →</a>
            </div>
            @if($myRequests->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-4xl mb-3">✈️</p>
                    <p class="text-gray-500">You haven't made any travel requests yet.</p>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($myRequests as $req)
                        @php
                            $statusColors = [
                                'approved' => 'bg-green-100 text-green-700',
                                'rejected' => 'bg-red-100 text-red-700',
                                'pending_pm' => 'bg-yellow-100 text-yellow-700',
                                'pending_commercial' => 'bg-purple-100 text-purple-700',
                                'pending_hod' => 'bg-blue-100 text-blue-700',
                            ];
                        @endphp
                        <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition">
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">{{ $req->destination }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $req->travel_date }} ·
                                    {{ optional($req->project)->name ?? 'No project' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span
                                    class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$req->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                </span>
                                <a href="{{ route('travel-requests.show', $req) }}"
                                    class="text-xs text-indigo-600 hover:underline font-medium">View</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @endhasrole

</x-app-layout>
