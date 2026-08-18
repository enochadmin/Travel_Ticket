<x-app-layout>
    <x-slot name="pageTitle">Approved Ticket Details</x-slot>

    @php
        $travelDate = $ticket->travel_date ? \Illuminate\Support\Carbon::parse($ticket->travel_date) : null;
        $returnDate = $ticket->return_date ? \Illuminate\Support\Carbon::parse($ticket->return_date) : null;
        $submittedAt = $ticket->created_at ? $ticket->created_at->copy() : null;
        $tripLength = $travelDate
            ? ($returnDate ? $travelDate->diffInDays($returnDate) + 1 : 1)
            : null;
        $passengerCount = (int) ($ticket->passenger_count ?? 1);
        $approvalItems = [
            [
                'label' => 'Requested',
                'person' => $ticket->user?->name ?? 'Unknown requester',
                'role' => 'Requester',
                'status' => 'Completed',
                'time' => $submittedAt?->format('M d, Y \a\t h:i A') ?? 'Not available',
                'tone' => 'sky',
            ],
            [
                'label' => 'PM Approval',
                'person' => $ticket->pm?->name ?? 'Not recorded',
                'role' => 'Project Manager',
                'status' => $ticket->pm ? 'Approved' : 'Pending record',
                'time' => $ticket->pm_approved_at?->format('M d, Y \a\t h:i A') ?? 'Approval time not recorded',
                'tone' => $ticket->pm ? 'emerald' : 'slate',
            ],
            [
                'label' => 'Director Approval',
                'person' => $ticket->hod?->name ?? 'Not recorded',
                'role' => 'Commercial Director',
                'status' => $ticket->hod ? 'Approved' : 'Pending record',
                'time' => $ticket->hod_approved_at?->format('M d, Y \\a\\t h:i A') ?? 'Approval time not recorded',
                'tone' => $ticket->hod ? 'emerald' : 'slate',
            ],
        ];

        $routeParams = request()->query();
        $backRoute = request()->query('from') === 'archived'
            ? route('reception.tickets.archived', $routeParams)
            : route('reception.tickets.index', $routeParams);
    @endphp

    <div x-data="{ processModal: false, bookModal: false, rejectModal: false }">
    <div class="mx-auto max-w-7xl space-y-8">
        <!-- Hero Header -->
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl">
            <div class="relative px-8 py-10 lg:px-12" 
                 style="background: linear-gradient(135deg, #0f172a 0%, #164e63 50%, #67e8f9 100%);">
                <div class="absolute inset-0 opacity-10" 
                     style="background-image: radial-gradient(circle at 30% 20%, white 0%, transparent 40%), 
                            radial-gradient(circle at 80% 70%, #a5f3fc 0%, transparent 50%);"></div>
                
                <div class="relative flex flex-col lg:flex-row lg:items-start lg:justify-between gap-8">
                    <!-- Left Content -->
                    <div class="max-w-2xl">
                        <div class="flex flex-wrap items-center gap-3 mb-6">
                            <span class="inline-flex items-center rounded-2xl bg-white/20 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-white ring-1 ring-white/30">
                                Reception
                            </span>
                            <span class="inline-flex items-center rounded-2xl bg-emerald-400/30 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-emerald-100 ring-1 ring-emerald-200/40">
                                ✅ Fully Approved
                            </span>
                        </div>

                        <h1 class="text-4xl lg:text-5xl font-black tracking-tighter text-white leading-none">
                            {{ $ticket->origin ?: 'Origin' }} → {{ $ticket->destination ?: 'Destination' }}
                        </h1>
                        
                        <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-5 text-white">
                                <div class="text-xs uppercase tracking-widest text-cyan-200 mb-1">Travel Date</div>
                                <div class="text-2xl font-semibold">{{ $travelDate?->format('d M Y') ?? '—' }}</div>
                                <div class="text-sm text-cyan-100/80 mt-1">{{ $travelDate?->format('l') ?? '' }}</div>
                            </div>
                            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-5 text-white">
                                <div class="text-xs uppercase tracking-widest text-cyan-200 mb-1">Return</div>
                                <div class="text-2xl font-semibold">{{ $returnDate?->format('d M Y') ?? 'Open' }}</div>
                                <div class="text-sm text-cyan-100/80 mt-1">{{ $returnDate?->format('l') ?? 'One-way' }}</div>
                            </div>
                            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-5 text-white">
                                <div class="text-xs uppercase tracking-widest text-cyan-200 mb-1">Passengers</div>
                                <div class="text-2xl font-semibold">{{ $passengerCount }}</div>
                                <div class="text-sm text-cyan-100/80 mt-1">{{ $tripLength ?? '—' }} day trip</div>
                            </div>
                        </div>
                    </div>

                    <!-- Ticket Snapshot -->
                    <div class="w-full lg:w-80 bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-7 text-white shadow-2xl">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <div class="uppercase text-xs tracking-[0.125em] text-cyan-200">Ticket ID</div>
                                <div class="text-4xl font-black text-white">#{{ $ticket->id }}</div>
                            </div>
                            <div class="px-4 py-2 bg-emerald-500/20 text-emerald-100 text-xs font-semibold rounded-2xl ring-1 ring-emerald-400/30">
                                Ready
                            </div>
                        </div>

                        <div class="space-y-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-cyan-100/80">Project</span>
                                <span class="font-medium text-right">{{ $ticket->project?->name ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-cyan-100/80">Flight</span>
                                <span class="font-medium">{{ ucfirst($ticket->flight_type ?? 'national') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-cyan-100/80">Submitted</span>
                                <span class="font-medium">{{ $submittedAt?->format('M d, Y') ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between pt-3 border-t border-white/20">
                                <span class="text-cyan-100/80">Requester</span>
                                <span class="font-medium">{{ $ticket->user?->name ?? 'Unknown' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid gap-8 lg:grid-cols-12">
            <!-- Main Content -->
            <div class="lg:col-span-7 space-y-8">
                <!-- Trip Details -->
                <section class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <span class="text-teal-600 uppercase text-xs font-semibold tracking-widest">Trip Details</span>
                            <h2 class="text-2xl font-semibold text-slate-900 mt-1">Key Information</h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ([
                            ['label' => 'Origin', 'value' => $ticket->origin ?? 'Not provided', 'icon' => 'M17.657 16.657L13.414 12l4.243-4.243m0 8.486H9m8.657 0A8 8 0 1112 4a8 8 0 015.657 12.657z'],
                            ['label' => 'Destination', 'value' => $ticket->destination ?? 'Not provided', 'icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 01.553-.894L9 2m0 18l6-3m-6 3V2m6 15l6 3m-6-3V6m6 14V8m0 12l-6-3'],
                            ['label' => 'Travel Date', 'value' => $travelDate?->format('d M Y') ?? '—', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                            ['label' => 'Return Date', 'value' => $returnDate?->format('d M Y') ?? 'One-way', 'icon' => 'M8 7V3m8 4V3m-4 8v4m-2-2h4m7 2a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v12z'],
                            ['label' => 'Passengers', 'value' => $passengerCount . ' traveler' . ($passengerCount > 1 ? 's' : ''), 'icon' => 'M17 20h5V18a4 4 0 00-5-3.874M9 20H4V18a4 4 0 015-3.874M9 20h6m-6 0v-2a3 3 0 013-3h0a3 3 0 013 3v2m-6 0a3 3 0 11-6 0 3 3 0 016 0zm12 0a3 3 0 11-6 0 3 3 0 016 0zM9 7a3 3 0 116 0 3 3 0 01-6 0z'],
                            ['label' => 'Flight Type', 'value' => ucfirst($ticket->flight_type ?? 'national'), 'icon' => 'M10.18 9l-2.03 4.06-4.06 2.03a1 1 0 000 1.82l4.06 2.03 2.03 4.06a1 1 0 001.82 0l2.03-4.06 4.06-2.03a1 1 0 000-1.82l-4.06-2.03-2.03-4.06a1 1 0 00-1.82 0z'],
                        ] as $detail)
                            <div class="flex gap-4 bg-slate-50 rounded-2xl p-5 border border-slate-100">
                                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-2xl bg-white shadow">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $detail['icon'] }}" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs uppercase font-medium text-slate-500 tracking-wider">{{ $detail['label'] }}</p>
                                    <p class="font-semibold text-slate-900 text-[17px] leading-tight mt-1">{{ $detail['value'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                @include('travel_requests._passengers_display', [
                    'travelRequest' => $ticket,
                    'wrapperClass' => 'bg-white rounded-3xl border border-slate-100 shadow-sm p-8',
                    'sectionTitle' => 'All Travelers',
                ])

                <!-- Approval Trail -->
                <section class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <span class="text-teal-600 uppercase text-xs font-semibold tracking-widest">Process Flow</span>
                            <h2 class="text-2xl font-semibold text-slate-900 mt-1">Approval Trail</h2>
                        </div>
                        <span class="px-5 py-2 text-emerald-700 bg-emerald-100 rounded-3xl text-sm font-medium">Cleared for Booking</span>
                    </div>

                    <div class="space-y-6">
                        @foreach ($approvalItems as $index => $item)
                            @php
                                $toneClasses = [
                                    'sky' => 'bg-sky-100 text-sky-700',
                                    'emerald' => 'bg-emerald-100 text-emerald-700',
                                    'slate' => 'bg-slate-100 text-slate-600',
                                ][$item['tone']];
                                $dotClasses = [
                                    'sky' => 'bg-sky-500',
                                    'emerald' => 'bg-emerald-500',
                                    'slate' => 'bg-slate-400',
                                ][$item['tone']];
                            @endphp
                            <div class="flex gap-6">
                                <!-- Timeline Dot -->
                                <div class="flex flex-col items-center">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-2xl {{ $dotClasses }} text-white font-semibold shadow-inner">
                                        {{ $index + 1 }}
                                    </div>
                                    @if (!$loop->last)
                                        <div class="w-px h-8 bg-slate-200 mt-3"></div>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="flex-1 pt-1">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div>
                                            <div class="uppercase text-xs text-slate-500 font-medium">{{ $item['label'] }}</div>
                                            <div class="font-semibold text-lg text-slate-900">{{ $item['person'] }}</div>
                                            <div class="text-sm text-slate-500">{{ $item['role'] }}</div>
                                        </div>
                                        <span class="inline-flex items-center px-5 py-1.5 text-sm font-medium rounded-3xl {{ $toneClasses }}">
                                            {{ $item['status'] }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-500 mt-2">{{ $item['time'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-5 space-y-8">
                <!-- Requester & Project -->
                <section class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
                    <span class="uppercase text-xs font-semibold tracking-widest text-teal-600">Team</span>
                    <h2 class="text-2xl font-semibold text-slate-900 mt-1">Requester &amp; Project</h2>

                    <div class="mt-7 space-y-6">
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-6">
                            <div class="uppercase text-xs tracking-widest text-slate-500">Requester</div>
                            <div class="font-semibold text-xl mt-2">{{ $ticket->user?->name ?? 'Unknown' }}</div>
                            <div class="text-slate-600 text-sm mt-1">{{ $ticket->user?->email ?? '—' }}</div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-2xl border border-slate-100 p-6">
                                <div class="uppercase text-xs tracking-widest text-slate-500">Project</div>
                                <div class="font-semibold mt-3 text-slate-900">{{ $ticket->project?->name ?? 'Unassigned' }}</div>
                            </div>
                            <div class="rounded-2xl border border-slate-100 p-6">
                                <div class="uppercase text-xs tracking-widest text-slate-500">Submitted</div>
                                <div class="font-semibold mt-3 text-slate-900">{{ $submittedAt?->format('M d, Y') }}</div>
                                <div class="text-xs text-slate-500">{{ $submittedAt?->format('h:i A') }}</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Purpose -->
                <section class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
                    <span class="uppercase text-xs font-semibold tracking-widest text-teal-600">Purpose</span>
                    <h2 class="text-2xl font-semibold text-slate-900 mt-1">Why this trip?</h2>
                    <div class="mt-6 text-slate-600 leading-relaxed bg-slate-50 border border-dashed border-slate-200 rounded-2xl p-7 min-h-[140px]">
                        {{ $ticket->purpose ?: 'No purpose statement provided.' }}
                    </div>
                </section>

                @if($ticket->remarks)
                <!-- Remarks -->
                <section class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
                    <span class="uppercase text-xs font-semibold tracking-widest text-teal-600">Remarks</span>
                    <h2 class="text-2xl font-semibold text-slate-900 mt-1">Additional Notes</h2>
                    <div class="mt-6 text-slate-600 leading-relaxed bg-slate-50 border border-dashed border-slate-200 rounded-2xl p-7 min-h-[140px]">
                        {{ $ticket->remarks }}
                    </div>
                </section>
                @endif

                <!-- Actions -->
                <section class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
                    <span class="uppercase text-xs font-semibold tracking-widest text-teal-600">Actions</span>
                    <h2 class="text-2xl font-semibold text-slate-900 mt-1">Next Steps</h2>
                    <p class="text-slate-600 mt-2 text-[15px]">This ticket is fully approved and ready for processing.</p>

                    <div class="mt-8 flex flex-col gap-3">
                        <button type="button" @click="processModal = true"
                                class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 transition-colors text-white font-semibold rounded-2xl flex items-center justify-center gap-2 text-base">
                            <span>Process &amp; Archive</span>
                        </button>

                        <button type="button" @click="bookModal = true"
                                class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 transition-colors text-white font-semibold rounded-2xl flex items-center justify-center gap-2 text-base">
                            <span>Process &amp; Create Booking</span>
                        </button>

                        <button type="button" @click="rejectModal = true"
                                class="w-full py-4 bg-red-600 hover:bg-red-700 transition-colors text-white font-semibold rounded-2xl flex items-center justify-center gap-2 text-base">
                            <span>Reject Ticket</span>
                        </button>

                        <div class="flex gap-3 pt-2">
                            <a href="{{ $backRoute }}" 
                               class="flex-1 py-4 border border-slate-300 hover:bg-slate-50 transition-colors text-slate-700 font-semibold rounded-2xl text-center">
                                Back
                            </a>
                            <a href="{{ route('reception.tickets.index') }}" 
                               class="flex-1 py-4 border border-slate-300 hover:bg-slate-50 transition-colors text-slate-700 font-semibold rounded-2xl text-center">
                                All Tickets
                            </a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Modals (unchanged) -->
    <div x-show="processModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8" @click.outside="processModal = false">
            <h3 class="text-xl font-semibold">Confirm Processing</h3>
            <p class="mt-3 text-slate-600">Mark ticket #{{ $ticket->id }} as processed and archive it?</p>
            <div class="flex gap-3 mt-8">
                <form method="POST" action="{{ route('reception.tickets.process') }}" class="flex-1" data-prevent-double-submit data-submitting-text="Processing...">
                    @csrf
                    <input type="hidden" name="ticket_ids[]" value="{{ $ticket->id }}">
                    <button type="submit" class="w-full py-3.5 bg-emerald-600 text-white font-semibold rounded-2xl">Yes, Process</button>
                </form>
                <button type="button" @click="processModal = false" 
                        class="flex-1 py-3.5 border border-slate-300 font-medium rounded-2xl">Cancel</button>
            </div>
        </div>
    </div>

    <div x-show="bookModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8" @click.outside="bookModal = false">
            <h3 class="text-xl font-semibold">Create Booking?</h3>
            <p class="mt-3 text-slate-600">Process ticket #{{ $ticket->id }} and proceed to booking creation.</p>
            <div class="flex gap-3 mt-8">
                <form method="POST" action="{{ route('reception.tickets.process_and_book') }}" class="flex-1" data-prevent-double-submit data-submitting-text="Processing...">
                    @csrf
                    <input type="hidden" name="ticket_ids[]" value="{{ $ticket->id }}">
                    <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white font-semibold rounded-2xl">Continue</button>
                </form>
                <button type="button" @click="bookModal = false" 
                        class="flex-1 py-3.5 border border-slate-300 font-medium rounded-2xl">Cancel</button>
            </div>
        </div>
    </div>

    <div x-show="rejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8" @click.outside="rejectModal = false">
            <h3 class="text-xl font-semibold text-red-600">Reject Ticket #{{ $ticket->id }}</h3>
            <p class="mt-3 text-slate-600">This ticket is fully approved. Rejecting it (e.g. duplicate/repeated request) will notify the requester, admins and commercial directors with the reason below.</p>
            <form method="POST" action="{{ route('reception.tickets.reject', $ticket) }}" class="mt-6" data-prevent-double-submit data-submitting-text="Rejecting...">
                @csrf
                <label class="block text-sm font-semibold text-gray-700 mb-2">Rejection Reason <span class="text-red-500">*</span></label>
                <textarea name="rejection_reason" rows="4" required
                    placeholder="e.g. Duplicate request — an identical ticket already exists."
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-transparent resize-none"></textarea>
                <div class="flex gap-3 mt-6">
                    <button type="submit"
                        class="flex-1 py-3.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-2xl transition">
                        Confirm Rejection
                    </button>
                    <button type="button" @click="rejectModal = false"
                        class="flex-1 py-3.5 border border-slate-300 text-slate-700 font-semibold rounded-2xl hover:bg-slate-50 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>

    <script>
        (function () {
            document.querySelectorAll('form[data-prevent-double-submit]').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    if (form.dataset.submitting === 'true') {
                        e.preventDefault();
                        return;
                    }
                    form.dataset.submitting = 'true';
                    var btn = form.querySelector('button[type="submit"]');
                    if (btn) {
                        btn.disabled = true;
                        btn.dataset.originalHtml = btn.innerHTML;
                        btn.innerHTML = form.getAttribute('data-submitting-text') || 'Saving...';
                    }
                });
            });
        })();
    </script>
</x-app-layout>
