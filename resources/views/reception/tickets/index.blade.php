<x-app-layout>
    <x-slot name="pageTitle">Reception Tickets</x-slot>

    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <form method="GET" action="{{ route('reception.tickets.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="text-xs text-gray-500 font-semibold">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="mt-1 w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="text-xs text-gray-500 font-semibold">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="mt-1 w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="text-xs text-gray-500 font-semibold">Project</label>
                    <select name="project_id" class="mt-1 w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" @selected(request('project_id') == $project->id)>{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 font-semibold">Requester</label>
                    <input type="text" name="requester" value="{{ request('requester') }}" placeholder="Name or email"
                        class="mt-1 w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="text-xs text-gray-500 font-semibold">Destination</label>
                    <input type="text" name="destination" value="{{ request('destination') }}" placeholder="Destination"
                        class="mt-1 w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="md:col-span-5 flex items-center gap-3 pt-2">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">
                        Apply Filters
                    </button>
                    <a href="{{ route('reception.tickets.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                        Clear
                    </a>
                    <a href="{{ route('reception.tickets.export', request()->query()) }}"
                        class="ml-auto px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
                        Download CSV
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-700">Fully Approved Tickets</h2>
                <span class="text-xs text-gray-500">Total: {{ $tickets->total() }}</span>
            </div>
            <form method="POST" action="{{ route('reception.tickets.process') }}" id="process-form">
                @csrf
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    <input type="checkbox" id="select-all"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </th>
                                <th class="px-6 py-3 text-left">Requester</th>
                                <th class="px-6 py-3 text-left">Project</th>
                                <th class="px-6 py-3 text-left">Starting Place</th>
                                <th class="px-6 py-3 text-left">Destination</th>
                                <th class="px-6 py-3 text-left">Travel Date</th>
                                <th class="px-6 py-3 text-left">Return Date</th>
                                <th class="px-6 py-3 text-left">Approved By</th>
                                <th class="px-6 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <input type="checkbox" name="ticket_ids[]" value="{{ $ticket->id }}"
                                        class="ticket-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        data-label="{{ $ticket->destination }} ({{ $ticket->user?->name ?? 'Unknown' }})">
                                </td>
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-800">{{ $ticket->user?->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $ticket->user?->email }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">{{ $ticket->project?->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-gray-700">{{ $ticket->origin }}</td>
                                    <td class="px-6 py-4 text-gray-700">
                                        <p>{{ $ticket->destination }}</p>
                                        <p class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($ticket->purpose, 60) }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">{{ $ticket->travel_date }}</td>
                                    <td class="px-6 py-4 text-gray-700">{{ $ticket->return_date ?? '—' }}</td>
                                    <td class="px-6 py-4 text-gray-700">
                                        <span class="text-xs text-gray-500">PM:</span> {{ $ticket->pm?->name ?? '—' }}<br>
                                        <span class="text-xs text-gray-500">Director:</span> {{ $ticket->hod?->name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('reception.tickets.show', $ticket) }}"
                                            class="text-xs text-indigo-600 hover:underline font-medium">View details</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-10 text-center text-gray-400">No approved tickets found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-3">
                    <button type="submit" id="process-btn"
                        class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">
                        Process Selected
                    </button>
                    <span class="text-xs text-gray-500">Processed tickets will be archived.</span>
                </div>
            </form>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>

    <div id="process-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4"
        style="background: rgba(0,0,0,0.5);">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Process Selected Tickets?</h3>
            <p class="text-sm text-gray-500 mb-4">These tickets will be archived and removed from the approved list.</p>
            <div class="max-h-56 overflow-auto border border-gray-100 rounded-lg p-3 bg-gray-50 text-sm text-gray-700">
                <ul id="process-list" class="list-disc pl-5 space-y-1"></ul>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="button" id="confirm-process"
                    class="flex-1 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition">
                    Confirm
                </button>
                <button type="button" id="cancel-process"
                    class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition">
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

        if (selectAll) {
            selectAll.addEventListener('change', (e) => {
                boxes.forEach((box) => {
                    box.checked = e.target.checked;
                });
            });
        }

        if (processBtn && form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const selected = Array.from(boxes).filter((b) => b.checked);
                if (selected.length === 0) {
                    alert('Please select at least one ticket to process.');
                    return;
                }

                list.innerHTML = '';
                selected.forEach((b) => {
                    const li = document.createElement('li');
                    li.textContent = b.dataset.label || ('Ticket #' + b.value);
                    list.appendChild(li);
                });

                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });
        }

        if (confirmBtn) {
            confirmBtn.addEventListener('click', () => {
                form.submit();
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });
        }
    </script>
</x-app-layout>
