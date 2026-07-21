<x-app-layout>
    <x-slot name="pageTitle">Reception Tickets</x-slot>

    <div class="space-y-8">
        <!-- Filters -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-900">Approved Tickets</h2>
                    <p class="text-slate-600 text-sm mt-1">Filter and manage fully approved travel requests</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('reception.tickets.export', array_merge(request()->query(), ['archived' => '0'])) }}"
                       class="inline-flex items-center gap-2 px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-sm font-semibold transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v-4m0 0l4 4m-4-4l4-4m12 4v4m0 0l-4-4m4 4l-4 4" />
                        </svg>
                        Download Approved CSV
                    </a>
                    <a href="{{ route('reception.tickets.export', array_merge(request()->query(), ['archived' => '1'])) }}"
                       class="inline-flex items-center gap-2 px-5 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-2xl text-sm font-semibold transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v-4m0 0l4 4m-4-4l4-4m12 4v4m0 0l-4-4m4 4l-4 4" />
                        </svg>
                        Download Archived CSV
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('reception.tickets.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">Date To</label>
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
                    <a href="{{ route('reception.tickets.index') }}"
                       class="px-6 py-3.5 text-slate-500 hover:text-slate-700 font-medium transition">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Tickets Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-semibold text-slate-900">Fully Approved Tickets</h2>
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-sm font-medium rounded-2xl">
                        {{ $tickets->total() }} total
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('reception.tickets.process') }}" id="process-form">
                @csrf
                
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-6 py-5 text-left font-semibold text-slate-600">No.</th>
                                        <th class="px-8 py-5 text-left w-10">
                                            <input type="checkbox" id="select-all"
                                                class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        </th>
                                <th class="px-6 py-5 text-left font-semibold text-slate-600">Requester</th>
                                <th class="px-6 py-5 text-left font-semibold text-slate-600">Project</th>
                                <th class="px-6 py-5 text-left font-semibold text-slate-600">Route</th>
                                <th class="px-6 py-5 text-left font-semibold text-slate-600">Travel Date</th>
                                <th class="px-6 py-5 text-left font-semibold text-slate-600">Return</th>
                                <th class="px-6 py-5 text-left font-semibold text-slate-600">Remarks</th>
                                <th class="px-6 py-5 text-left font-semibold text-slate-600">Approvals</th>
                                <th class="px-8 py-5 text-right font-semibold text-slate-600">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                        @php $start = ($tickets->currentPage() - 1) * $tickets->perPage(); @endphp
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-slate-50 transition group">
                                <td class="px-6 py-5 text-slate-700">{{ $start + $loop->iteration }}</td>
                                <td class="px-8 py-5">
                                    <input type="checkbox" name="ticket_ids[]" value="{{ $ticket->id }}"
                                        class="ticket-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                        data-label="{{ $ticket->destination }} ({{ $ticket->user?->name ?? 'Unknown' }})">
                                </td>
                                <td class="px-6 py-5">
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
                                <td class="px-6 py-5 text-slate-700">
                                    @if($ticket->remarks)
                                        <span class="text-xs">{{ \Illuminate\Support\Str::limit($ticket->remarks, 50) }}</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
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
                                <td class="px-8 py-5 text-right">
                                    <a href="{{ route('reception.tickets.show', $ticket) }}"
                                       class="inline-flex items-center justify-center px-5 py-2 text-sm font-semibold text-indigo-600 hover:bg-indigo-50 rounded-2xl transition">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-8 py-16 text-center">
                                    <div class="mx-auto w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
                                        ✈️
                                    </div>
                                    <p class="text-slate-400 font-medium">No approved tickets found</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Bulk Actions -->
                <div class="px-8 py-6 border-t border-slate-100 bg-slate-50 flex items-center gap-4">
                    <button type="submit" id="process-btn"
                        class="px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-2xl transition flex items-center gap-2">
                        <span>Process Selected Tickets</span>
                    </button>
                    <p class="text-sm text-slate-500">Selected tickets will be archived after processing.</p>
                </div>
            </form>

            <!-- Pagination -->
            <div class="px-8 py-5 border-t border-slate-100">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>

    <!-- Process Confirmation Modal -->
    <div id="process-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg p-8" @click.outside="modal.classList.add('hidden')">
            <h3 class="text-2xl font-semibold text-slate-900">Process Selected Tickets?</h3>
            <p class="mt-2 text-slate-600">These tickets will be marked as processed and moved to archive.</p>

            <div class="mt-6 max-h-60 overflow-auto bg-slate-50 border border-slate-100 rounded-2xl p-5 text-sm">
                <ul id="process-list" class="space-y-2 text-slate-700"></ul>
            </div>

            <div class="flex gap-3 mt-8">
                <button type="button" id="confirm-process"
                    class="flex-1 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-2xl transition">
                    Yes, Process Tickets
                </button>
                <button type="button" id="cancel-process"
                    class="flex-1 py-4 border border-slate-300 text-slate-700 font-semibold rounded-2xl hover:bg-slate-50 transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <script>
        const selectAll = document.getElementById('select-all');
        const boxes = document.querySelectorAll('.ticket-checkbox');
        const form = document.getElementById('process-form');
        const processBtn = document.getElementById('process-btn');
        const modal = document.getElementById('process-modal');
        const list = document.getElementById('process-list');
        const confirmBtn = document.getElementById('confirm-process');
        const cancelBtn = document.getElementById('cancel-process');

        // Select All
        if (selectAll) {
            selectAll.addEventListener('change', (e) => {
                boxes.forEach(box => box.checked = e.target.checked);
            });
        }

        // Process Form
        if (processBtn && form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const selected = Array.from(boxes).filter(b => b.checked);

                if (selected.length === 0) {
                    alert('Please select at least one ticket.');
                    return;
                }

                // Populate modal list
                list.innerHTML = '';
                selected.forEach(box => {
                    const li = document.createElement('li');
                    li.textContent = box.dataset.label || `Ticket #${box.value}`;
                    list.appendChild(li);
                });

                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });
        }

        if (confirmBtn) {
            confirmBtn.addEventListener('click', () => form.submit());
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });
        }
    </script>
</x-app-layout>