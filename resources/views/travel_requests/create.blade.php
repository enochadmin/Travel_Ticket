<x-app-layout>
    <x-slot name="pageTitle">Raise Travel Request</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100" style="background:linear-gradient(90deg,#eff6ff,#fff)">
                <h2 class="text-lg font-bold text-gray-800">New Travel Request</h2>
                <p class="text-xs text-gray-400 mt-0.5">Fill in your trip details. The request will first go to your
                    Project Manager for review.</p>
            </div>

            <form action="{{ route('travel-requests.store') }}" method="POST" class="p-6 space-y-5"
                data-prevent-double-submit data-submitting-text="Submitting...">
                @csrf
                @if(!empty($preselectedProject))
                    <input type="hidden" name="project_id" value="{{ $preselectedProject->id }}">
                    <div
                        class="bg-indigo-50 border border-indigo-100 text-indigo-700 px-4 py-2.5 rounded-xl text-sm font-semibold mb-4">
                        Creating ticket for Project: {{ $preselectedProject->name }}
                    </div>
                @else
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Select Project <span
                                class="text-red-500">*</span></label>
                        <select name="project_id" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('project_id') border-red-400 @enderror">
                            <option value="">-- Choose a Project --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', request('project_id', Auth::user()->project_id)) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>
                @endif

                <x-city-search-select
                    name="origin"
                    label="Origin (Starting Place)"
                    :cities="$cities"
                    :value="old('origin')"
                    :required="true"
                    placeholder="Search origin city..."
                />

                @include('travel_requests._passenger_fields', [
                    'passengerCount' => old('passenger_count', 1),
                    'additionalPassengers' => old('additional_passengers', []),
                ])

                <x-city-search-select
                    name="destination"
                    label="Destination"
                    :cities="$cities"
                    :value="old('destination')"
                    :required="true"
                    placeholder="Search destination city..."
                />

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Flight Type <span
                            class="text-red-500">*</span></label>
                    <select name="flight_type" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('flight_type') border-red-400 @enderror">
                        <option value="">-- Select Type --</option>
                        <option value="national" {{ old('flight_type', 'national') === 'national' ? 'selected' : '' }}>National</option>
                        <option value="international" {{ old('flight_type') === 'international' ? 'selected' : '' }}>International</option>
                    </select>
                    @error('flight_type')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Travel Date <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="travel_date" value="{{ old('travel_date') }}" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('travel_date') border-red-400 @enderror">
                        @error('travel_date')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Return Date <span
                                class="text-gray-400 font-normal">(optional)</span></label>
                        <input type="date" name="return_date" value="{{ old('return_date') }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('return_date') border-red-400 @enderror">
                        @error('return_date')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Purpose / Reason <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="purpose" value="{{ old('purpose') }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('purpose') border-red-400 @enderror"
                        placeholder="e.g. Project site visit, client meeting">
                    @error('purpose')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Remarks / Additional Details</label>
                    <textarea name="remarks" rows="4"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition"
                        placeholder="Any extra notes, accommodation needs, etc.">{{ old('remarks') }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition"
                        style="background:linear-gradient(135deg,#4f46e5,#6366f1);">
                        ✈️ Submit Request
                    </button>
                    <a href="{{ route('travel-requests.index') }}"
                        class="text-sm text-gray-500 hover:text-gray-700 font-medium">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            document.querySelectorAll('form[data-prevent-double-submit]').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    if (form.dataset.submitting === 'true') {
                        e.preventDefault();
                        return;
                    }
                    form.dataset.submitting = 'true';
                    var btn = form.querySelector('button[type="submit"]');
                    if (btn) {
                        btn.disabled = true;
                        btn.dataset.originalHtml = btn.innerHTML;
                        btn.innerHTML = form.getAttribute('data-submitting-text') || 'Saving...';
                    }
                });
            });
        })();
    </script>
</x-app-layout>
