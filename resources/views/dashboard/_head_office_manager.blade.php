<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header Card --}}
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="px-6 py-6 sm:px-8" style="background:linear-gradient(135deg,#059669 0%,#10b981 55%,#34d399 100%);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-green-100">{{ $department }}</p>
                    <h2 class="mt-2 text-2xl font-bold text-white sm:text-3xl">{{ $dashboardTitle }}</h2>
                    <p class="mt-2 text-sm text-green-100 max-w-2xl">{{ $dashboardSubtitle }}</p>
                </div>
                <div class="hidden sm:flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.581m0 0H9m0 0h-.581m0 0H3m4 0V9m0 0h4m0 0h4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @php
            $hodCards = [
                ['label' => 'Total Tickets', 'value' => $myTotal, 'from' => '#6366f1', 'to' => '#818cf8', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Approved', 'value' => $myApproved, 'from' => '#10b981', 'to' => '#34d399', 'icon' => 'M5 13l4 4L19 7'],
                ['label' => 'Rejected', 'value' => $myRejected, 'from' => '#ef4444', 'to' => '#f87171', 'icon' => 'M6 18L18 6M6 6l12 12'],
                ['label' => 'In Process', 'value' => $myPending, 'from' => '#f59e0b', 'to' => '#fbbf24', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
        @endphp
        @foreach($hodCards as $card)
            <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center justify-between border border-gray-100 hover:shadow-md transition">
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">{{ $card['label'] }}</p>
                    <p class="text-3xl font-extrabold text-gray-800">{{ $card['value'] }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-sm"
                    style="background: linear-gradient(135deg,{{ $card['from'] }},{{ $card['to'] }})">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}" />
                    </svg>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Charts Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Status Chart --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex flex-col">
            <h2 class="text-base font-bold text-gray-800 mb-4">Request Status Distribution</h2>
            <div class="flex-1 relative" style="min-height: 250px;">
                <canvas id="hodStatusChart"></canvas>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex flex-col">
            <h2 class="text-base font-bold text-gray-800 mb-4">Quick Actions</h2>
            <div class="space-y-3 flex-1">
                <a href="{{ route('travel-requests.create') }}" 
                   class="flex items-center justify-between p-4 bg-gradient-to-r from-indigo-50 to-indigo-100 border border-indigo-200 rounded-xl hover:shadow-md transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-indigo-500 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <span class="font-semibold text-indigo-700">Create New Request</span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="{{ route('travel-requests.index') }}" 
                   class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl hover:shadow-md transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-green-500 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <span class="font-semibold text-green-700">View All Requests</span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

    </div>

    {{-- Recent Requests --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-700">📋 My Recent Tickets</h2>
            <a href="{{ route('travel-requests.index') }}"
                class="text-xs text-indigo-600 hover:underline font-medium">View all →</a>
        </div>
        @if($myRequests->isEmpty())
            <div class="p-8 text-center">
                <p class="text-4xl mb-3">✈️</p>
                <p class="text-gray-500">You haven't made any travel requests yet.</p>
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach($myRequests as $req)
                    @php
                        $statusColors = [
                            'approved' => 'bg-green-100 text-green-700',
                            'rejected' => 'bg-red-100 text-red-700',
                            'pending_pm' => 'bg-yellow-100 text-yellow-700',
                            'pending_commercial' => 'bg-purple-100 text-purple-700',
                            'pending_hod' => 'bg-blue-100 text-blue-700',
                            'pending_ceo' => 'bg-indigo-100 text-indigo-700',
                        ];
                    @endphp
                    <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-1">
                                <p class="font-semibold text-gray-800 text-sm">{{ $req->destination }}</p>
                                <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                                    {{ $req->flight_type === 'international' ? '🌍 International' : '🇳🇴 National' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400">{{ $req->travel_date }} · 
                                {{ optional($req->project)->name ?? 'No project' }} · 
                                {{ $req->passenger_count }} passenger{{ $req->passenger_count > 1 ? 's' : '' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span
                                class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$req->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                            </span>
                            <a href="{{ route('travel-requests.show', $req) }}"
                                class="text-xs text-indigo-600 hover:underline font-medium">View</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusCtx = document.getElementById('hodStatusChart');
        if (statusCtx) {
            const statusLabels = {!! json_encode($statusLabels) !!};
            const statusData = {!! json_encode($statusData) !!};
            new Chart(statusCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                        backgroundColor: ['#10b981', '#ef4444'],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { 
                                color: '#4b5563', 
                                font: { size: 12 },
                                padding: 15
                            }
                        }
                    }
                }
            });
        }
    });
</script>
