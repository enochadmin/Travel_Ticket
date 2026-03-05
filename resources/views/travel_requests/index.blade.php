<x-app-layout>
    <x-slot name="pageTitle">Travel Requests</x-slot>

    <div class="space-y-5">

        @if (session('success'))
            <div
                class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm px-5 py-3 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-500 flex-shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm px-5 py-3 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 flex-shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Travel Requests</h2>
                    <p class="text-xs text-gray-400 mt-0.5">All requests visible to your role</p>
                </div>
                <a href="{{ route('travel-requests.create') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-white px-4 py-2.5 rounded-xl transition"
                    style="background: linear-gradient(135deg,#4f46e5,#6366f1);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Raise New Request
                </a>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Requester</th>
                            <th class="px-6 py-3">Project</th>
                            <th class="px-6 py-3">Destination</th>
                            <th class="px-6 py-3">Travel Date</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($travelRequests as $request)
                            @php
                                $statusMap = [
                                    'pending_pm' => 'bg-yellow-100 text-yellow-800',
                                    'pending_commercial' => 'bg-purple-100 text-purple-800',
                                    'pending_hod' => 'bg-blue-100 text-blue-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <tr class="hover:bg-indigo-50/30 transition">
                                <td class="px-6 py-4 text-gray-400">{{ $request->id }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-800">{{ $request->user->name }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ optional($request->project)->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $request->destination }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $request->travel_date }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusMap[$request->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-3">
                                    <a href="{{ route('travel-requests.show', $request) }}"
                                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">View</a>
                                    @if($request->user_id === Auth::id() && $request->status === 'pending_pm')
                                        <a href="{{ route('travel-requests.edit', $request) }}"
                                            class="text-xs font-semibold text-gray-500 hover:text-gray-700">Edit</a>
                                        <form action="{{ route('travel-requests.destroy', $request) }}" method="POST"
                                            class="inline" onsubmit="return confirm('Delete this request?');">
                                            @csrf @method('DELETE')
                                            <button
                                                class="text-xs font-semibold text-red-500 hover:text-red-700">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                    <p class="text-3xl mb-2">✈️</p>
                                    No travel requests found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($travelRequests->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $travelRequests->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>