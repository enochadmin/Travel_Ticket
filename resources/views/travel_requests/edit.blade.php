<x-app-layout>
    <x-slot name="pageTitle">Edit Travel Request</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100" style="background:linear-gradient(90deg,#fef3c7,#fff)">
                <h2 class="text-lg font-bold text-gray-800">Edit Travel Request</h2>
                <p class="text-xs text-gray-400 mt-0.5">You can only edit requests still awaiting PM review</p>
            </div>

            <form action="{{ route('travel-requests.update', $travelRequest) }}" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                <x-city-search-select
                    name="origin"
                    label="Origin (Starting Place)"
                    :cities="$cities"
                    :value="old('origin', $travelRequest->origin)"
                    :required="true"
                    placeholder="Search origin city..."
                />

                <x-city-search-select
                    name="destination"
                    label="Destination"
                    :cities="$cities"
                    :value="old('destination', $travelRequest->destination)"
                    :required="true"
                    placeholder="Search destination city..."
                />

                @include('travel_requests._passenger_fields', [
                    'requesterName' => $travelRequest->user->name,
                    'passengerCount' => old('passenger_count', $travelRequest->passenger_count ?? 1),
                    'additionalPassengers' => old('additional_passengers', $travelRequest->additionalPassengerNames()),
                ])

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Flight Type <span
                            class="text-red-500">*</span></label>
                    <select name="flight_type" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('flight_type') border-red-400 @enderror">
                        <option value="national" {{ old('flight_type', $travelRequest->flight_type ?? 'national') === 'national' ? 'selected' : '' }}>National</option>
                        <option value="international" {{ old('flight_type', $travelRequest->flight_type ?? 'national') === 'international' ? 'selected' : '' }}>International</option>
                    </select>
                    @error('flight_type')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Travel Date <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="travel_date"
                            value="{{ old('travel_date', $travelRequest->travel_date) }}" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('travel_date') border-red-400 @enderror">
                        @error('travel_date')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Return Date <span
                                class="text-gray-400 font-normal">(optional)</span></label>
                        <input type="date" name="return_date"
                            value="{{ old('return_date', $travelRequest->return_date) }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('return_date') border-red-400 @enderror">
                        @error('return_date')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Purpose / Reason <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="purpose" value="{{ old('purpose', $travelRequest->purpose) }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('purpose') border-red-400 @enderror">
                    @error('purpose')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Remarks / Additional Details</label>
                    <textarea name="remarks" rows="4"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition">{{ old('remarks', $travelRequest->remarks) }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition"
                        style="background:linear-gradient(135deg,#4f46e5,#6366f1);">
                        Update Request
                    </button>
                    <a href="{{ route('travel-requests.index') }}"
                        class="text-sm text-gray-500 hover:text-gray-700 font-medium">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
