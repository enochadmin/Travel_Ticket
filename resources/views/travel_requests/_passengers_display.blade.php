@php
    $passengerNames = $travelRequest->allPassengerNames();
    $passengerCount = (int) ($travelRequest->passenger_count ?? 1);
@endphp

@if ($passengerCount > 1 || count($passengerNames) > 1)
    <div class="{{ $wrapperClass ?? 'px-6 py-4 border-t border-gray-50' }}">
        @if (! empty($sectionTitle))
            <span class="uppercase text-xs font-semibold tracking-widest text-teal-600">Passengers</span>
            <h2 class="text-2xl font-semibold text-slate-900 mt-1 mb-5">{{ $sectionTitle }}</h2>
        @else
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-3">
                Passengers ({{ $passengerCount }})
            </p>
        @endif

        <ol class="space-y-3">
            @foreach ($passengerNames as $index => $name)
                <li class="flex items-start gap-3 rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3 text-sm">
                    <span class="inline-flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">
                        {{ $index + 1 }}
                    </span>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $name }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $index === 0 ? 'Requester (primary passenger)' : 'Additional passenger' }}
                        </p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
@endif
