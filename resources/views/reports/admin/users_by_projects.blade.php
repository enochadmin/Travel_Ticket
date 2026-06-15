<x-app-layout>
    <x-slot name="pageTitle">Users by Projects</x-slot>

    <div class="mx-auto max-w-6xl space-y-6">
        @include('reports._nav')

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-900 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Assigned Users</p>
                <p class="mt-2 text-3xl font-bold text-gray-800 dark:text-slate-100">{{ $totalAssigned }}</p>
                <p class="mt-1 text-xs text-gray-400">{{ $unassignedUsers->count() }} unassigned users</p>
            </div>
            <div class="lg:col-span-2 rounded-2xl border border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-900 p-5 shadow-sm">
                <h2 class="text-base font-bold text-gray-700 dark:text-slate-100 mb-4">Users per Project</h2>
                <div style="height: 240px;">
                    <canvas id="projectsChart"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700">
                <h2 class="text-base font-bold text-gray-700 dark:text-slate-100">Project Assignments</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-slate-800 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Project</th>
                            <th class="px-6 py-3">Users</th>
                            <th class="px-6 py-3">Assigned Users</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
                        @foreach($projects as $project)
                            <tr class="align-top hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4 font-semibold text-gray-800 dark:text-slate-100">{{ $project->name }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-slate-300">{{ $project->users_count }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        @forelse($project->users as $user)
                                            <span class="rounded-full bg-indigo-50 text-indigo-700 px-2.5 py-1 text-xs font-semibold">
                                                {{ $user->name }} - {{ $user->roles->first()?->name ?? 'No role' }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-400">No users assigned</span>
                                        @endforelse
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($unassignedUsers->isNotEmpty())
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
                <h2 class="text-base font-bold text-gray-700 dark:text-slate-100 mb-3">Unassigned Users</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($unassignedUsers as $user)
                        <span class="rounded-full bg-yellow-50 text-yellow-800 px-2.5 py-1 text-xs font-semibold">
                            {{ $user->name }} - {{ $user->roles->first()?->name ?? 'No role' }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('projectsChart');
            if (!ctx) return;

            new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartData['labels']) !!},
                    datasets: [{
                        label: 'Users',
                        data: {!! json_encode($chartData['data']) !!},
                        backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#14b8a6', '#64748b'],
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        });
    </script>
</x-app-layout>
