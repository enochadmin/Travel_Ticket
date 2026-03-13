<x-app-layout>
    <x-slot name="pageTitle">Approved Ticket Details</x-slot>

    <div class="max-w-4xl space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">{{ $ticket->destination }}</h2>
                    <p class="text-sm text-gray-500">Travel Date: {{ $ticket->travel_date }} · Return: {{ $ticket->return_date ?? '—' }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                    Approved
                </span>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-2">Requester</h3>
                    <p class="text-sm text-gray-800 font-medium">{{ $ticket->user?->name }}</p>
                    <p class="text-sm text-gray-500">{{ $ticket->user?->email }}</p>
                </div>
                <div>
                    <h3 class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-2">Project</h3>
                    <p class="text-sm text-gray-800 font-medium">{{ $ticket->project?->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <h3 class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-2">Approvals</h3>
                    <p class="text-sm text-gray-800">PM: {{ $ticket->pm?->name ?? '—' }}</p>
                    <p class="text-sm text-gray-800">Director: {{ $ticket->hod?->name ?? '—' }}</p>
                </div>
                <div>
                    <h3 class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-2">Submitted By</h3>
                    <p class="text-sm text-gray-800">{{ $ticket->created_at?->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Purpose</h3>
            <p class="text-sm text-gray-700 leading-relaxed">{{ $ticket->purpose }}</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('reception.tickets.index', request()->query()) }}"
                class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200">
                Back to Tickets
            </a>
        </div>
    </div>
</x-app-layout>
