<x-app-layout>
    <x-slot name="pageTitle">Reports</x-slot>

    <div class="space-y-6">

        {{-- Filters --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-bold text-gray-700 mb-4">🔍 Filter Requests</h2>
            <form action="{{ route('reports.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Project</label>
                    <select name="project_id"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none transition">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Destination</label>
                    <input type="text" name="destination" value="{{ request('destination') }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none transition"
                        placeholder="Country / City">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Purpose</label>
                    <input type="text" name="purpose" value="{{ request('purpose') }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none transition"
                        placeholder="Reason for travel">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">From</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">To</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none transition">
                    </div>
                </div>

                <div class="sm:col-span-2 xl:col-span-4 flex justify-end gap-3 pt-1">
                    <a href="{{ route('reports.index') }}"
                        class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                        Clear
                    </a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold transition"
                        style="background:linear-gradient(135deg,#4f46e5,#6366f1);">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        {{-- Chart --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-bold text-gray-700 mb-4">📊 Requests per Project</h2>
            <div style="height: 300px;">
                <canvas id="reportChart"></canvas>
            </div>
        </div>

        {{-- Results Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-700">Detailed Results</h2>
                <span
                    class="text-xs bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-full font-semibold">{{ $travelRequests->count() }}
                    records</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">User</th>
                            <th class="px-6 py-3">Project</th>
                            <th class="px-6 py-3">Destination / Purpose</th>
                            <th class="px-6 py-3">Travel Date</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($travelRequests as $record)
                            @php
                                $statusMap = [
                                    'pending_pm' => 'bg-yellow-100 text-yellow-800',
                                    'pending_hod' => 'bg-purple-100 text-purple-800',
                                    'pending_commercial' => 'bg-purple-100 text-purple-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <tr class="hover:bg-indigo-50/30 transition">
                                <td class="px-6 py-4 text-gray-400">{{ $record->id }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-800">{{ $record->user->name }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ optional($record->project)->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-medium text-gray-700">{{ $record->destination }}</span>
                                    <br><span class="text-xs text-gray-400">{{ $record->purpose }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $record->travel_date }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusMap[$record->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    No records found matching the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Chart Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('reportChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartData['labels']) !!},
                    datasets: [{
                        label: 'Requests',
                        data: {!! json_encode($chartData['data']) !!},
                        backgroundColor: ['#6366f1', '#818cf8', '#a5b4fc', '#10b981', '#34d399', '#f59e0b', '#fbbf24', '#ef4444'],
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
</x-app-layout>