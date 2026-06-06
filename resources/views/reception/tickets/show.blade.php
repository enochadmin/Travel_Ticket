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
                'time' => $ticket->hod ? 'Final approval recorded' : 'Approval time not recorded',
                'tone' => $ticket->hod ? 'emerald' : 'slate',
            ],
        ];

        $routeParams = request()->query();
        $backRoute = request()->query('from') === 'archived'
            ? route('reception.tickets.archived', $routeParams)
            : route('reception.tickets.index', $routeParams);
    @endphp

    <div class="mx-auto max-w-7xl space-y-6">
        <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
            <div class="relative px-6 py-8 sm:px-8 lg:px-10" style="background:linear-gradient(135deg,#082f49 0%,#0f766e 45%,#ecfeff 100%);">
                <div class="absolute inset-0 opacity-20" style="background-image:radial-gradient(circle at top right, white 0, transparent 32%), radial-gradient(circle at bottom left, #67e8f9 0, transparent 28%);"></div>
                <div class="relative flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    <div class="max-w-3xl">
                        <div class="mb-4 flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center rounded-full bg-white/18 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-white ring-1 ring-white/25">
                                Reception View
                            </span>
                            <span class="inline-flex items-center rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-semibold text-emerald-50 ring-1 ring-emerald-200/30">
                                Fully Approved
                            </span>
                        </div>

                        <h2 class="text-3xl font-black tracking-tight text-white sm:text-4xl">
                            {{ $ticket->origin ?: 'Origin not set' }} to {{ $ticket->destination ?: 'Destination not set' }}
                        </h2>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-cyan-50/95 sm:text-base">
                            Clear ticket summary for reception processing, including travel schedule, approval trail, passenger details, and project context.
                        </p>

                        <div class="mt-6 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-white/15 bg-white/12 px-4 py-4 backdrop-blur-sm">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-100">Travel Date</p>
                                <p class="mt-2 text-lg font-bold text-white">{{ $travelDate?->format('M d, Y') ?? 'Not set' }}</p>
                                <p class="mt-1 text-xs text-cyan-100/80">{{ $travelDate?->format('l') ?? 'Schedule unavailable' }}</p>
                            </div>
                            <div class="rounded-2xl border border-white/15 bg-white/12 px-4 py-4 backdrop-blur-sm">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-100">Return Date</p>
                                <p class="mt-2 text-lg font-bold text-white">{{ $returnDate?->format('M d, Y') ?? 'One-way / open' }}</p>
                                <p class="mt-1 text-xs text-cyan-100/80">{{ $returnDate?->format('l') ?? 'No return recorded' }}</p>
                            </div>
                            <div class="rounded-2xl border border-white/15 bg-white/12 px-4 py-4 backdrop-blur-sm">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-100">Passengers</p>
                                <p class="mt-2 text-lg font-bold text-white">{{ $passengerCount }}</p>
                                <p class="mt-1 text-xs text-cyan-100/80">{{ $tripLength ? $tripLength . ' day trip window' : 'Trip length unavailable' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="w-full max-w-sm rounded-[1.75rem] border border-white/20 bg-slate-950/30 p-5 text-white shadow-2xl shadow-slate-950/20 backdrop-blur-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-100">Ticket Snapshot</p>
                                <p class="mt-2 text-2xl font-black">#{{ $ticket->id }}</p>
                            </div>
                            <span class="rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-semibold text-emerald-100 ring-1 ring-emerald-200/30">
                                Ready for Action
                            </span>
                        </div>

                        <dl class="mt-5 space-y-3 text-sm">
                            <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-3">
                                <dt class="text-cyan-100/80">Project</dt>
                                <dd class="text-right font-semibold">{{ $ticket->project?->name ?? 'Not assigned' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-3">
                                <dt class="text-cyan-100/80">Flight Type</dt>
                                <dd class="text-right font-semibold">{{ ucfirst($ticket->flight_type ?? 'national') }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-3">
                                <dt class="text-cyan-100/80">Submitted</dt>
                                <dd class="text-right font-semibold">{{ $submittedAt?->format('M d, Y') ?? 'Unknown' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-cyan-100/80">Requester</dt>
                                <dd class="text-right font-semibold">{{ $ticket->user?->name ?? 'Unknown' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[1.45fr_0.95fr]">
            <div class="space-y-6">
                <section class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-teal-600">Trip Overview</p>
                            <h3 class="mt-2 text-xl font-bold text-slate-900">Everything reception needs at a glance</h3>
                        </div>
                        <div class="hidden rounded-2xl bg-slate-100 px-4 py-3 text-right sm:block">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Trip Window</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ $tripLength ? $tripLength . ' day' . ($tripLength > 1 ? 's' : '') : 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ([
                            ['label' => 'Starting Point', 'value' => $ticket->origin ?? 'Not provided', 'accent' => 'from-amber-50 to-white', 'icon' => 'M17.657 16.657L13.414 12l4.243-4.243m0 8.486H9m8.657 0A8 8 0 1112 4a8 8 0 015.657 12.657z'],
                            ['label' => 'Destination', 'value' => $ticket->destination ?? 'Not provided', 'accent' => 'from-cyan-50 to-white', 'icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 01.553-.894L9 2m0 18l6-3m-6 3V2m6 15l6 3m-6-3V6m6 14V8m0 12l-6-3'],
                            ['label' => 'Travel Date', 'value' => $travelDate?->format('M d, Y') ?? 'Not provided', 'accent' => 'from-emerald-50 to-white', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                            ['label' => 'Return Date', 'value' => $returnDate?->format('M d, Y') ?? 'Not scheduled', 'accent' => 'from-rose-50 to-white', 'icon' => 'M8 7V3m8 4V3m-4 8v4m-2-2h4m7 2a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v12z'],
                            ['label' => 'Passengers', 'value' => $passengerCount, 'accent' => 'from-violet-50 to-white', 'icon' => 'M17 20h5V18a4 4 0 00-5-3.874M9 20H4V18a4 4 0 015-3.874M9 20h6m-6 0v-2a3 3 0 013-3h0a3 3 0 013 3v2m-6 0a3 3 0 11-6 0 3 3 0 016 0zm12 0a3 3 0 11-6 0 3 3 0 016 0zM9 7a3 3 0 116 0 3 3 0 01-6 0z'],
                            ['label' => 'Flight Type', 'value' => ucfirst($ticket->flight_type ?? 'national'), 'accent' => 'from-slate-100 to-white', 'icon' => 'M10.18 9l-2.03 4.06-4.06 2.03a1 1 0 000 1.82l4.06 2.03 2.03 4.06a1 1 0 001.82 0l2.03-4.06 4.06-2.03a1 1 0 000-1.82l-4.06-2.03-2.03-4.06a1 1 0 00-1.82 0z'],
                        ] as $detail)
                            <article class="rounded-[1.5rem] border border-slate-200 bg-gradient-to-br {{ $detail['accent'] }} p-5 shadow-sm">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $detail['icon'] }}" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $detail['label'] }}</p>
                                        <p class="mt-2 text-base font-bold text-slate-900">{{ $detail['value'] }}</p>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-teal-600">Approval Trail</p>
                    <div class="mt-2 flex items-center justify-between gap-4">
                        <h3 class="text-xl font-bold text-slate-900">Approval history and ownership</h3>
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
                            Ticket cleared for reception
                        </span>
                    </div>

                    <div class="mt-6 space-y-4">
                        @foreach ($approvalItems as $index => $item)
                            @php
                                $toneClasses = [
                                    'sky' => 'bg-sky-50 text-sky-700 ring-sky-200',
                                    'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                    'slate' => 'bg-slate-100 text-slate-600 ring-slate-200',
                                ][$item['tone']];
                                $dotClasses = [
                                    'sky' => 'bg-sky-500',
                                    'emerald' => 'bg-emerald-500',
                                    'slate' => 'bg-slate-400',
                                ][$item['tone']];
                            @endphp
                            <div class="relative rounded-[1.5rem] border border-slate-200 bg-slate-50/70 p-5">
                                @if (!$loop->last)
                                    <div class="absolute left-[2.15rem] top-[4.15rem] h-8 w-px bg-slate-200"></div>
                                @endif
                                <div class="flex gap-4">
                                    <div class="flex h-9 w-9 flex-none items-center justify-center rounded-full {{ $dotClasses }} text-white shadow-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                            <div>
                                                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-500">{{ $item['label'] }}</p>
                                                <h4 class="mt-1 text-lg font-bold text-slate-900">{{ $item['person'] }}</h4>
                                                <p class="text-sm text-slate-500">{{ $item['role'] }}</p>
                                            </div>
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $toneClasses }}">
                                                {{ $item['status'] }}
                                            </span>
                                        </div>
                                        <p class="mt-3 text-sm text-slate-600">{{ $item['time'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>

            <div class="space-y-6">
                <section class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-teal-600">Requester And Project</p>
                    <h3 class="mt-2 text-xl font-bold text-slate-900">Contact and assignment details</h3>

                    <div class="mt-6 space-y-5">
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Requester</p>
                            <p class="mt-2 text-lg font-bold text-slate-900">{{ $ticket->user?->name ?? 'Unknown requester' }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ $ticket->user?->email ?? 'Email unavailable' }}</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-[1.5rem] border border-slate-200 p-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Project</p>
                                <p class="mt-2 text-base font-bold text-slate-900">{{ $ticket->project?->name ?? 'Not assigned' }}</p>
                            </div>
                            <div class="rounded-[1.5rem] border border-slate-200 p-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Submitted On</p>
                                <p class="mt-2 text-base font-bold text-slate-900">{{ $submittedAt?->format('M d, Y') ?? 'Unknown' }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $submittedAt?->format('h:i A') ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-teal-600">Travel Purpose</p>
                    <h3 class="mt-2 text-xl font-bold text-slate-900">Reason for this trip</h3>
                    <div class="mt-5 rounded-[1.5rem] border border-dashed border-slate-300 bg-slate-50 p-5">
                        <p class="text-sm leading-7 text-slate-700">{{ $ticket->purpose ?: 'No purpose was provided for this ticket.' }}</p>
                    </div>
                </section>

                <section class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-teal-600">Reception Action</p>
                    <h3 class="mt-2 text-xl font-bold text-slate-900">Next step</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        This request is fully approved and ready for booking, ticket issuance, or archiving after processing.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ $backRoute }}" class="inline-flex items-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">
                            Back to Tickets
                        </a>
                        <a href="{{ route('reception.tickets.index') }}" class="inline-flex items-center rounded-2xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            View Approved Queue
                        </a>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
