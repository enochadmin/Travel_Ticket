<x-app-layout>
    <x-slot name="pageTitle">Admin Reports</x-slot>

    <div class="mx-auto max-w-6xl space-y-6">
        @include('reports._nav')

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                'Users' => $stats['users'],
                'Roles' => $stats['roles'],
                'Projects' => $stats['projects'],
                'Travel Requests' => $stats['travel_requests'],
            ] as $label => $value)
                <div class="rounded-2xl border border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ $label }}</p>
                    <p class="mt-2 text-2xl font-bold text-gray-800 dark:text-slate-100">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700">
                    <h2 class="text-base font-bold text-gray-700 dark:text-slate-100">Role Coverage</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-slate-800 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Role</th>
                                <th class="px-6 py-3 text-right">Users</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
                            @foreach($roles as $role)
                                <tr>
                                    <td class="px-6 py-4 font-semibold text-gray-800 dark:text-slate-100">{{ ucfirst(str_replace('-', ' ', $role->name)) }}</td>
                                    <td class="px-6 py-4 text-right text-gray-600 dark:text-slate-300">{{ $role->users_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700">
                    <h2 class="text-base font-bold text-gray-700 dark:text-slate-100">Recent Users</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-slate-800 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">User</th>
                                <th class="px-6 py-3">Role</th>
                                <th class="px-6 py-3">Project</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
                            @foreach($recentUsers as $user)
                                <tr>
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-800 dark:text-slate-100">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-slate-300">{{ $user->roles->first()?->name ?? 'None' }}</td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-slate-300">{{ $user->project?->name ?? 'Unassigned' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700">
                <h2 class="text-base font-bold text-gray-700 dark:text-slate-100">Projects Without Assigned Users</h2>
            </div>
            <div class="p-6">
                @forelse($projectsWithoutUsers as $project)
                    <span class="inline-flex mb-2 mr-2 rounded-full bg-gray-100 dark:bg-slate-800 px-3 py-1.5 text-xs font-semibold text-gray-700 dark:text-slate-200">
                        {{ $project->name }}
                    </span>
                @empty
                    <p class="text-sm text-gray-500 dark:text-slate-400">Every project currently has at least one assigned user.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
