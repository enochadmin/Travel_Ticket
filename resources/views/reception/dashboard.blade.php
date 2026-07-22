<x-app-layout>
    <x-slot name="pageTitle">Reception Dashboard</x-slot>

    <div class="mx-auto max-w-5xl space-y-6">

        {{-- Welcome --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-slate-100">Welcome back, {{ Auth::user()->name }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                    Process approved travel tickets and keep the queue moving.
                </p>
            </div>
            <a href="{{ route('reception.tickets.index') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition"
                style="background:linear-gradient(135deg,#0c4a6e,#0ea5e9);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Open Approved Queue
            </a>
        </div>

        {{-- Key stats --}}
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-900 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Awaiting Processing</p>
                <p class="mt-2 text-3xl font-bold text-sky-600 dark:text-sky-400">{{ $pendingProcessing }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Tickets ready for booking</p>
            </div>
            <div class="rounded-2xl border border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-900 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Processed</p>
                <p class="mt-2 text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $archivedTotal }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Already completed tickets</p>
            </div>
            <div class="rounded-2xl border border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-900 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Approved</p>
                <p class="mt-2 text-3xl font-bold text-gray-800 dark:text-slate-100">{{ $approvedTotal }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">All approved tickets</p>
            </div>
        </div>

        {{-- Recent queue --}}
        <div class="rounded-2xl border border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-slate-700 px-6 py-4">
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-slate-100">Upcoming Tickets</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Nearest travel dates in the active queue</p>
                </div>
                @if($pendingProcessing > 6)
                    <a href="{{ route('reception.tickets.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                        View all {{ $pendingProcessing }} →
                    </a>
                @endif
            </div>

            @if($recentPending->count() > 0)
                <div class="divide-y divide-gray-50 dark:divide-slate-800">
                    @foreach($recentPending as $ticket)
                        <div class="flex flex-col gap-3 px-6 py-4 sm:flex-row sm:items-center sm:justify-between hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-slate-100 truncate">
                                    {{ $ticket->user?->name ?? 'Unknown' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5 truncate">
                                    {{ $ticket->origin }} → {{ $ticket->destination }}
                                    · {{ optional($ticket->project)->name ?? 'No project' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <span class="text-xs text-gray-500 dark:text-slate-400">
                                    {{ \Carbon\Carbon::parse($ticket->travel_date)->format('M d, Y') }}
                                </span>
                <a href="{{ route('reception.tickets.show', $ticket) }}"
                    class="text-xs font-semibold text-sky-600 hover:text-sky-800 dark:text-sky-400 bg-sky-50 dark:bg-sky-950 px-3 py-1.5 rounded-lg transition">
                                    Open
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <p class="text-3xl mb-2">✅</p>
                    <p class="text-sm font-semibold text-gray-700 dark:text-slate-200">Queue is clear</p>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">No approved tickets waiting to be processed.</p>
                </div>
            @endif
        </div>

        {{-- Quick links --}}
        <div class="grid gap-4 sm:grid-cols-2">
            <a href="{{ route('reception.tickets.index') }}"
                class="group rounded-2xl border border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-900 p-5 shadow-sm transition hover:border-sky-200 dark:hover:border-sky-800">
                <p class="text-sm font-bold text-gray-800 dark:text-slate-100 group-hover:text-sky-600 dark:group-hover:text-sky-400">
                    Approved Tickets
                </p>
                <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Review and process the live approval queue.</p>
            </a>
            <a href="{{ route('reception.tickets.archived') }}"
                class="group rounded-2xl border border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-900 p-5 shadow-sm transition hover:border-emerald-200 dark:hover:border-emerald-800">
                <p class="text-sm font-bold text-gray-800 dark:text-slate-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">
                    Archived Tickets
                </p>
                <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Browse tickets that have already been processed.</p>
            </a>
        </div>
    </div>
</x-app-layout>
