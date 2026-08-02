@php
    $requesterName = $requesterName ?? Auth::user()->name;
    $passengerCount = (int) old('passenger_count', $passengerCount ?? 1);
    $additionalPassengers = old('additional_passengers', $additionalPassengers ?? []);
@endphp

<div
    x-data="{
        passengerCount: {{ max(1, $passengerCount) }},
        requesterName: @js($requesterName),
        additionalNames: @js(array_values($additionalPassengers)),
        syncFields() {
            const needed = Math.max(0, this.passengerCount - 1);
            while (this.additionalNames.length < needed) {
                this.additionalNames.push('');
            }
            if (this.additionalNames.length > needed) {
                this.additionalNames = this.additionalNames.slice(0, needed);
            }
        }
    }"
    x-init="syncFields()"
    class="space-y-4"
>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
            Number of Passengers <span class="text-red-500">*</span>
        </label>
        <input
            type="number"
            name="passenger_count"
            x-model.number="passengerCount"
            @input="syncFields()"
            min="1"
            required
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('passenger_count') border-red-400 @enderror"
            placeholder="e.g. 1"
        >
        @error('passenger_count')
            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
        @enderror
        <p class="text-xs text-gray-500 mt-1.5">
            You (the requester) count as one passenger. Add full names for each additional traveler.
        </p>
    </div>

    <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 px-4 py-3">
        <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 mb-1">Passenger 1 (Requester)</p>
        <p class="text-sm font-semibold text-gray-800">{{ $requesterName }}</p>
    </div>

    <template x-if="passengerCount > 1">
        <div class="space-y-3">
            <p class="text-sm font-semibold text-gray-700">Additional Passengers</p>
            <template x-for="(name, index) in additionalNames" :key="index">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        <span x-text="'Passenger ' + (index + 2) + ' — Full Name (including grandfather)'"></span>
                        <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        :name="'additional_passengers[' + index + ']'"
                        x-model="additionalNames[index]"
                        required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition"
                        placeholder="e.g. Abebe Kebede Alemu"
                    >
                </div>
            </template>
            @error('additional_passengers')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
            @if ($errors->has('additional_passengers.*'))
                <p class="text-red-500 text-xs mt-1.5">Please enter the full name for each additional passenger.</p>
            @endif
        </div>
    </template>
</div>
