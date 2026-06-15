<x-app-layout>
    <x-slot name="pageTitle">Travel Request Details</x-slot>

    @if (session('success'))
        <div
            class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm px-5 py-3 rounded-xl mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-500 flex-shrink-0" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @php
        $statusMap = [
            'pending_pm' => ['label' => 'Pending PM', 'color' => 'bg-yellow-100 text-yellow-800'],
            'pending_commercial' => ['label' => 'Pending Commercial Director', 'color' => 'bg-purple-100 text-purple-800'],
            // legacy status kept for backward compatibility
            'pending_hod' => ['label' => 'Pending Commercial Director', 'color' => 'bg-purple-100 text-purple-800'],
            'pending_ceo' => ['label' => 'Pending CEO', 'color' => 'bg-indigo-100 text-indigo-800'],
            'approved' => ['label' => 'Approved', 'color' => 'bg-green-100 text-green-800'],
            'rejected' => ['label' => 'Rejected', 'color' => 'bg-red-100 text-red-800'],
        ];
        $s = $statusMap[$travelRequest->status] ?? ['label' => $travelRequest->status, 'color' => 'bg-gray-100 text-gray-700'];
        $pmProjectId = Auth::user()->hasRole('project-manager')
            ? Auth::user()->approverProjectId()
            : null;
    @endphp

    <div class="max-w-3xl space-y-5">

        {{-- Header card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between"
                style="background:linear-gradient(90deg,#eff6ff,#fff)">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Request #{{ $travelRequest->id }}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Submitted by <span
                            class="font-semibold text-gray-600">{{ $travelRequest->user->name }}</span></p>
                    @if($travelRequest->status === 'pending_pm')
                        @php
                            $pm = $travelRequest->project?->manager;
                        @endphp
                        <div class="mt-2">
                            <span class="text-xs text-yellow-700 font-semibold">Approval State: Pending</span>
                            <br>
                            <span class="text-xs text-gray-600">Expected Approver: 
                                @if($pm)
                                    <span class="font-semibold">{{ $pm->name }}</span> <span class="text-xs text-gray-400">({{ $pm->email }})</span>
                                @else
                                    <span class="text-red-500">No Project Manager assigned</span>
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
                <span class="px-3 py-1.5 rounded-full text-sm font-semibold {{ $s['color'] }}">{{ $s['label'] }}</span>
            </div>

            {{-- Details grid --}}
            <div class="grid grid-cols-2 gap-0 divide-x divide-y divide-gray-50">
                @php
                    $fields = [
                        ['label' => 'Project', 'value' => optional($travelRequest->project)->name ?? 'N/A'],
                        ['label' => 'Origin (Starting Place)', 'value' => $travelRequest->origin],
                        ['label' => 'Destination', 'value' => $travelRequest->destination],
                        ['label' => 'Number of Passengers', 'value' => $travelRequest->passenger_count],
                        ['label' => 'Flight Type', 'value' => ucfirst($travelRequest->flight_type ?? 'national')],
                        ['label' => 'Travel Date', 'value' => $travelRequest->travel_date],
                        ['label' => 'Return Date', 'value' => $travelRequest->return_date ?? 'Not specified'],
                        ['label' => 'Purpose', 'value' => $travelRequest->purpose],
                        ['label' => 'Submitted', 'value' => $travelRequest->created_at->format('M d, Y H:i')],
                    ];
                @endphp
                @foreach($fields as $field)
                    <div class="px-6 py-4">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">{{ $field['label'] }}
                        </p>
                        <p class="text-sm font-semibold text-gray-800">{{ $field['value'] }}</p>
                    </div>
                @endforeach
            </div>

            @if($travelRequest->remarks)
                <div class="px-6 py-4 border-t border-gray-50">
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Remarks</p>
                    <p class="text-sm text-gray-700">{{ $travelRequest->remarks }}</p>
                </div>
            @endif

            @include('travel_requests._approval_history')

            {{-- Rejection Reason (visible to everyone on the ticket) --}}
            @if($travelRequest->status === 'rejected' && $travelRequest->rejection_reason)
                <div class="px-6 py-4 border-t border-red-100 bg-red-50">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        <div>
                            <p class="text-xs text-red-600 font-bold uppercase tracking-wider mb-1">Rejection Reason</p>
                            <p class="text-sm text-red-800 font-medium">{{ $travelRequest->rejection_reason }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Approval Actions --}}
        <div x-data="{ rejectModal: false, rejectAction: '' }" class="flex flex-wrap gap-3 items-center">

            @if(Auth::user()->hasRole('project-manager') && $travelRequest->status === 'pending_pm' && $pmProjectId && (int) $pmProjectId === (int) $travelRequest->project_id)
                <form action="{{ route('travel-requests.approve', $travelRequest) }}" method="POST">
                    @csrf @method('PATCH')
                    <button
                        class="px-5 py-2.5 rounded-xl bg-green-500 hover:bg-green-600 text-white text-sm font-semibold transition">✓
                        Approve as PM</button>
                </form>
                <button type="button"
                    @click="rejectAction='{{ route('travel-requests.reject', $travelRequest) }}'; rejectModal=true"
                    class="px-5 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-semibold transition">✗
                    Reject as PM</button>
            @endif

            @if(Auth::user()->hasRole('commercial-director') && in_array($travelRequest->status, ['pending_commercial', 'pending_hod'], true))
                <form action="{{ route('travel-requests.approve', $travelRequest) }}" method="POST">
                    @csrf @method('PATCH')
                    <button
                        class="px-5 py-2.5 rounded-xl bg-green-500 hover:bg-green-600 text-white text-sm font-semibold transition">✓
                        Approve as Commercial Director</button>
                </form>
                <button type="button"
                    @click="rejectAction='{{ route('travel-requests.reject', $travelRequest) }}'; rejectModal=true"
                    class="px-5 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-semibold transition">✗
                    Reject as Commercial Director</button>
            @endif

            @if(Auth::user()->hasRole('ceo') && $travelRequest->status === 'pending_ceo')
                <form action="{{ route('travel-requests.approve', $travelRequest) }}" method="POST">
                    @csrf @method('PATCH')
                    <button
                        class="px-5 py-2.5 rounded-xl bg-green-500 hover:bg-green-600 text-white text-sm font-semibold transition">âœ“
                        Approve as CEO</button>
                </form>
                <button type="button"
                    @click="rejectAction='{{ route('travel-requests.reject', $travelRequest) }}'; rejectModal=true"
                    class="px-5 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-semibold transition">âœ—
                    Reject as CEO</button>
            @endif

            <a href="{{ route('travel-requests.index') }}"
                class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                ← Back to List
            </a>

            {{-- Rejection Reason Modal --}}
            <div x-show="rejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
                style="background: rgba(0,0,0,0.5);" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8" @click.outside="rejectModal = false"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100">

                    <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-5">
                        <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>

                    <h3 class="text-xl font-bold text-gray-800 text-center mb-2">Reject Request</h3>
                    <p class="text-sm text-gray-500 text-center mb-6">Please provide a reason for rejection. This will
                        be visible to the requester.</p>

                    <form :action="rejectAction" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Rejection Reason <span
                                    class="text-red-500">*</span></label>
                            <textarea name="rejection_reason" rows="4" required
                                placeholder="e.g. Budget constraints, insufficient justification, travel dates conflict..."
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-transparent resize-none"></textarea>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit"
                                class="flex-1 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white font-semibold text-sm transition">
                                Confirm Rejection
                            </button>
                            <button type="button" @click="rejectModal = false"
                                class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
