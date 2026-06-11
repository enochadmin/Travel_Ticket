@php
    $showHistory = $travelRequest->pm_id
        || $travelRequest->hod_id
        || in_array($travelRequest->status, ['pending_commercial', 'pending_hod', 'pending_ceo', 'approved', 'rejected'], true);

    $pmSkipped = in_array($travelRequest->status, ['pending_commercial', 'pending_hod', 'pending_ceo', 'approved', 'rejected'], true)
        && ! $travelRequest->pm_id
        && $travelRequest->user?->hasRole('project-manager');

    $commercialRejected = $travelRequest->status === 'rejected' && $travelRequest->hod_id;
@endphp

@if($showHistory)
    <div class="px-6 py-4 border-t border-indigo-100 bg-indigo-50 dark:bg-indigo-950/20 dark:border-indigo-900">
        <p class="text-xs text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-wider mb-4">Approval History</p>
        <div class="space-y-4 ml-1">

            {{-- Requester --}}
            <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-full bg-indigo-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-slate-100">
                        {{ $travelRequest->user?->name ?? 'Unknown' }}
                        <span class="text-xs font-normal text-gray-400">(Requester)</span>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-slate-400">
                        Submitted {{ $travelRequest->created_at->format('M d, Y \a\t h:i A') }}
                    </p>
                </div>
            </div>

            {{-- PM step --}}
            @if($travelRequest->pm_id)
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-slate-100">
                            {{ $travelRequest->pm?->name ?? 'Project Manager' }}
                            <span class="text-xs font-normal text-gray-400">(Project Manager)</span>
                        </p>
                        <span class="inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-[11px] font-semibold">Approved</span>
                        @if($travelRequest->pm_approved_at)
                            <span class="text-xs text-gray-400 ml-1">{{ $travelRequest->pm_approved_at->format('M d, Y \a\t h:i A') }}</span>
                        @endif
                    </div>
                </div>
            @elseif($pmSkipped)
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-full bg-indigo-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-slate-100">
                            {{ $travelRequest->user?->name }}
                            <span class="text-xs font-normal text-gray-400">(Project Manager)</span>
                        </p>
                        <span class="inline-flex items-center mt-0.5 px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-[11px] font-semibold">Auto-forwarded to Commercial Director</span>
                    </div>
                </div>
            @endif

            {{-- Commercial Director step --}}
            @if($travelRequest->hod_id)
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-full {{ $commercialRejected ? 'bg-red-500' : 'bg-purple-500' }} flex items-center justify-center flex-shrink-0">
                        @if($commercialRejected)
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        @else
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-slate-100">
                            {{ $travelRequest->hod?->name ?? 'Commercial Director' }}
                            <span class="text-xs font-normal text-gray-400">(Commercial Director)</span>
                        </p>
                        @if($commercialRejected)
                            <span class="inline-flex items-center mt-0.5 px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-[11px] font-semibold">Rejected</span>
                        @else
                            <span class="inline-flex items-center mt-0.5 px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 text-[11px] font-semibold">
                                {{ $travelRequest->status === 'pending_ceo' ? 'Approved (forwarded to CEO)' : 'Approved' }}
                            </span>
                            @if($travelRequest->hod_approved_at)
                                <span class="text-xs text-gray-400 ml-1">{{ $travelRequest->hod_approved_at->format('M d, Y \a\t h:i A') }}</span>
                            @endif
                        @endif
                    </div>
                </div>
            @elseif(in_array($travelRequest->status, ['pending_commercial', 'pending_hod'], true))
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-full bg-yellow-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-slate-100">Commercial Director</p>
                        <span class="inline-flex items-center mt-0.5 px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800 text-[11px] font-semibold">Awaiting approval</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif
