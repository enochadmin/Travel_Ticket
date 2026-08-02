<x-app-layout>
    <x-slot name="pageTitle">User Sessions</x-slot>

    <div class="mx-auto max-w-6xl space-y-5">
        @if (session('success'))
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm px-5 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm px-5 py-3 rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm px-5 py-3 rounded-xl">
                Select at least one user session to terminate.
            </div>
        @endif

        @unless ($usesDatabase)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">
                <p class="font-semibold">Session monitoring requires the database session driver.</p>
                <p class="mt-1 text-amber-800">
                    Production is currently using <strong>{{ $sessionDriver }}</strong>.
                    Set <code class="rounded bg-amber-100 px-1.5 py-0.5">SESSION_DRIVER=database</code> in your
                    <code class="rounded bg-amber-100 px-1.5 py-0.5">.env</code> file, ensure the
                    <code class="rounded bg-amber-100 px-1.5 py-0.5">sessions</code> table exists, then have users sign in again.
                </p>
            </div>
        @endunless

        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-700 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Settings</p>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-slate-100">Logged-in User Sessions</h2>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                        See who is signed in and force them to log in again by terminating their session.
                        @if ($usesDatabase)
                            <span class="font-semibold text-gray-700 dark:text-slate-200">{{ $sessions->count() }} active session(s)</span>
                        @endif
                    </p>
                </div>

                @if ($usesDatabase && $sessions->isNotEmpty())
                    <form id="bulkTerminateForm" method="POST" action="{{ route('settings.session.bulk-destroy') }}"
                        onsubmit="return confirm('Terminate the selected user sessions? Those users will need to sign in again.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Terminate Selected
                        </button>
                    </form>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-slate-800 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3 w-12">
                                <input id="selectAllSessions" type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    aria-label="Select all terminable sessions">
                            </th>
                            <th class="px-6 py-3">Signed-in User</th>
                            <th class="px-6 py-3">Role</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">IP Address</th>
                            <th class="px-6 py-3">Last Activity</th>
                            <th class="px-6 py-3">Device / Browser</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
                        @forelse($sessions as $session)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4">
                                    @if($session->is_current)
                                        <input type="checkbox" disabled
                                            class="rounded border-gray-200 text-gray-300"
                                            aria-label="Current admin session cannot be selected">
                                    @else
                                        <input type="checkbox" name="session_ids[]" value="{{ $session->id }}"
                                            form="bulkTerminateForm"
                                            class="session-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            aria-label="Select {{ $session->user->name }} session">
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($session->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-slate-100">
                                                {{ $session->user->name }}
                                                @if($session->is_current)
                                                    <span class="ml-2 rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 text-[10px] font-bold">You</span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-400">{{ $session->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-slate-300">
                                    {{ $session->user->getRoleNames()->map(fn ($role) => ucfirst(str_replace('-', ' ', $role)))->join(', ') ?: 'No role assigned' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($session->status === 'Active')
                                        <span class="rounded-full bg-green-100 text-green-700 px-2.5 py-1 text-xs font-semibold">Active</span>
                                    @else
                                        <span class="rounded-full bg-yellow-100 text-yellow-800 px-2.5 py-1 text-xs font-semibold">Idle</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-slate-300">{{ $session->ip_address ?? 'Unknown' }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-slate-300">
                                    <p>{{ $session->last_activity_at->diffForHumans() }}</p>
                                    <p class="text-xs text-gray-400">{{ $session->last_activity_at->format('M d, Y H:i') }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-slate-400 max-w-xs">
                                    <p class="truncate" title="{{ $session->user_agent ?? 'Unknown' }}">{{ $session->browser ?? 'Unknown browser' }}</p>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    @if($session->is_current)
                                        <span class="text-xs font-semibold text-gray-400">Your session</span>
                                    @else
                                        <form method="POST" action="{{ route('settings.session.destroy', $session->id) }}"
                                            onsubmit="return confirm('Terminate {{ $session->user->name }}? They will be signed out immediately.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition">
                                                Terminate Session
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                    @if ($usesDatabase)
                                        No active signed-in users were found. Sessions appear here after users log in.
                                    @else
                                        Switch to the database session driver to monitor and terminate user sessions.
                                    @endif
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
            const selectAll = document.getElementById('selectAllSessions');
            const checkboxes = Array.from(document.querySelectorAll('.session-checkbox'));

            if (!selectAll || checkboxes.length === 0) {
                if (selectAll) {
                    selectAll.disabled = true;
                }
                return;
            }

            selectAll.addEventListener('change', function () {
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = selectAll.checked;
                });
            });

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', function () {
                    selectAll.checked = checkboxes.every((item) => item.checked);
                    selectAll.indeterminate = checkboxes.some((item) => item.checked) && !selectAll.checked;
                });
            });
        });
    </script>
</x-app-layout>
