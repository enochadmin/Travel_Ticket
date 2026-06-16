<x-app-layout>
    <x-slot name="pageTitle">Create Booking</x-slot>

    <div class="mx-auto max-w-3xl">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-lg font-bold">Create Booking (Reception)</h2>
            @if($ticket)
                <p class="text-sm text-gray-600 mt-2">Ticket #{{ $ticket->id }} — {{ $ticket->origin }} → {{ $ticket->destination }}</p>
            @endif

            <form method="POST" action="{{ route('reception.bookings.store') }}" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="ticket_id" value="{{ $ticket?->id }}">

                <div>
                    <label class="block text-xs font-semibold text-gray-600">Carrier</label>
                    <input name="carrier" class="mt-1 w-full rounded-lg border-gray-200 px-3 py-2" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600">PNR</label>
                    <input name="pnr" class="mt-1 w-full rounded-lg border-gray-200 px-3 py-2" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600">Booking Reference</label>
                    <input name="booking_reference" class="mt-1 w-full rounded-lg border-gray-200 px-3 py-2" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600">Notes</label>
                    <textarea name="notes" rows="4" class="mt-1 w-full rounded-lg border-gray-200 px-3 py-2"></textarea>
                </div>

                <div class="flex gap-3">
                    <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white">Save Booking</button>
                    <a href="{{ url()->previous() }}" class="px-4 py-2 rounded-lg border">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
