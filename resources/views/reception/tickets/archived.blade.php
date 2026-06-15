<x-app-layout>
    <x-slot name="pageTitle">Archived Tickets</x-slot>

    <div class="space-y-8">
        <!-- Filters -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-900">Archived Tickets</h2>
                    <p class="text-slate-600 text-sm mt-1">Previously processed and archived travel requests</p>
                </div>
                <a href="{{ route('reception.tickets.export', array_merge(request()->query(), ['archived' => 1])) }}"
                   class="inline-flex items-center gap-2 px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-sm font-semibold transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v-4m0 0l4 4m-4-4l4-4m12 4v4m0 0l-4-4m4 4l-4 4" />
                    </svg>
                    Download Archived CSV
                </a>
            </div>

            <form method="GET" action="{{ route('reception.tickets.archived') }}" 
                  class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">Archived From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">Archived To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">Project</label>
                    <select name="project_id" 
                            class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" @selected(request('project_id') == $project->id)>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">Requester</label>
                    <input type="text" name="requester" value="{{ request('requester') }}" placeholder="Name or email"
                           class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">Destination</label>
                    <input type="text" name="destination" value="{{ request('destination') }}" placeholder="Destination city"
                           class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                </div>

                <div class="lg:col-span-5 flex items-center gap-4 pt-2">
                    <button type="submit"
                            class="px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-2xl transition">
                        Apply Filters
                    </button>
                    <a href="{{ route('reception.tickets.archived') }}"
                       class="px-6 py-3.5 text-slate-500 hover:text-slate-700 font-medium transition">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Archived Tickets Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-semibold text-slate-900">Processed &amp; Archived Tickets</h2>
                    <span class="px-3 py-1 bg-slate-200 text-slate-700 text-sm font-medium rounded-2xl">
                        {{ $tickets->total() }} total
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-5 text-left font-semibold text-slate-600">No.</th>
                            <th class="px-8 py-5 text-left font-semibold text-slate-600">Requester</th>
                            <th class="px-6 py-5 text-left font-semibold text-slate-600">Project</th>
                            <th class="px-6 py-5 text-left font-semibold text-slate-600">Route</th>
                            <th class="px-6 py-5 text-left font-semibold text-slate-600">Travel Date</th>
                            <th class="px-6 py-5 text-left font-semibold text-slate-600">Return</th>
                            <th class="px-6 py-5 text-left font-semibold text-slate-600">Approvals</th>
                            <th class="px-6 py-5 text-left font-semibold text-slate-600">Archived</th>
                            <th class="px-8 py-5 text-right font-semibold text-slate-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $start = ($tickets->currentPage() - 1) * $tickets->perPage(); @endphp
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-slate-50 transition group">
                                <td class="px-6 py-5 text-slate-700">{{ $start + $loop->iteration }}</td>
                                <td class="px-8 py-5">
                                    <div class="font-semibold text-slate-900">{{ $ticket->user?->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $ticket->user?->email }}</div>
                                </td>
                                <td class="px-6 py-5 text-slate-700 font-medium">
                                    {{ $ticket->project?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-slate-800">{{ $ticket->origin }}</span>
                                        <span class="text-slate-400">→</span>
                                        <span class="font-medium text-slate-800">{{ $ticket->destination }}</span>
                                    </div>
                                    @if($ticket->purpose)
                                        <p class="text-xs text-slate-500 mt-1 line-clamp-1">
                                            {{ \Illuminate\Support\Str::limit($ticket->purpose, 65) }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-slate-700">
                                    {{ $ticket->travel_date ? \Carbon\Carbon::parse($ticket->travel_date)->format('d M Y') : '—' }}
                                </td>
                                <td class="px-6 py-5 text-slate-700">
                                    {{ $ticket->return_date ? \Carbon\Carbon::parse($ticket->return_date)->format('d M Y') : '—' }}
                                </td>
                                <td class="px-6 py-5">
                                    <div class="text-xs">
                                        <span class="text-emerald-600">PM:</span> 
                                        <span class="font-medium">{{ $ticket->pm?->name ?? '—' }}</span>
                                    </div>
                                    <div class="text-xs mt-1">
                                        <span class="text-emerald-600">Director:</span> 
                                        <span class="font-medium">{{ $ticket->hod?->name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-slate-600 text-sm">
                                    <div>{{ optional($ticket->archived_at)->format('d M Y') ?? '—' }}</div>
                                    <div class="text-xs text-slate-500">
                                        by {{ $ticket->archivedBy?->name ?? 'System' }}
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-right flex items-center justify-end gap-2">
                                    <a href="{{ route('reception.tickets.export', array_merge(request()->query(), ['archived' => 1, 'ticket_id' => $ticket->id])) }}"
                                       class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-emerald-600 hover:bg-emerald-50 rounded-2xl transition">
                                        Download
                                    </a>
                                    <a href="{{ route('reception.tickets.show', $ticket) }}?from=archived"
                                       class="inline-flex items-center justify-center px-5 py-2 text-sm font-semibold text-indigo-600 hover:bg-indigo-50 rounded-2xl transition">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-8 py-20 text-center">
                                    <div class="mx-auto w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center text-4xl mb-6">
                                        📦
                                    </div>
                                    <p class="text-slate-400 text-lg font-medium">No archived tickets yet</p>
                                    <p class="text-slate-500 mt-2">Processed tickets will appear here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-8 py-5 border-t border-slate-100">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
</x-app-layout>