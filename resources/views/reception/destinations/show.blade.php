<x-app-layout>
    <x-slot name="pageTitle">Destination: {{ $destination }}</x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-700">Approved Tickets for {{ $destination }}</h2>
            <a href="{{ route('reception.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">Back to Dashboard</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($tickets as $ticket)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs text-gray-500">{{ $ticket->travel_date }} → {{ $ticket->return_date ?? '—' }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-700">Approved</span>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-800">{{ $ticket->user?->name }}</h3>
                    <p class="text-xs text-gray-500">{{ $ticket->user?->email }}</p>
                    <p class="text-xs text-gray-500 mt-2">{{ $ticket->project?->name ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-600 mt-2">{{ \Illuminate\Support\Str::limit($ticket->purpose, 90) }}</p>
                    <div class="mt-4">
                        <a href="{{ route('reception.tickets.show', $ticket) }}"
                            class="text-xs text-indigo-600 hover:underline font-medium">View details</a>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No approved tickets for this destination.</p>
            @endforelse
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-4 py-3">
            {{ $tickets->links() }}
        </div>
    </div>
</x-app-layout>
