@props([
    'name',
    'label',
    'cities',
    'value' => '',
    'required' => false,
    'placeholder' => 'Search city name...',
])

@php
    $selectedValue = old($name, $value);
    $cityOptions = $cities->map(fn ($city) => [
        'name' => $city->name,
        'region' => $city->region,
        'label' => $city->region ? "{$city->name} ({$city->region})" : $city->name,
    ])->values();
@endphp

<div
    x-data="{
        open: false,
        query: '',
        selected: @js($selectedValue),
        options: @js($cityOptions),
        get filtered() {
            const q = this.query.trim().toLowerCase();
            if (!q) return this.options;
            return this.options.filter(o =>
                o.name.toLowerCase().includes(q) ||
                (o.region && o.region.toLowerCase().includes(q))
            );
        },
        displayLabel() {
            const match = this.options.find(o => o.name === this.selected);
            return match ? match.label : this.selected;
        },
        pick(city) {
            this.selected = city.name;
            this.query = city.label;
            this.open = false;
        },
        init() {
            if (this.selected) {
                const match = this.options.find(o => o.name === this.selected);
                this.query = match ? match.label : this.selected;
            }
        }
    }"
    @click.outside="open = false"
    class="relative"
>
    <label class="block text-sm font-semibold text-gray-700 dark:text-slate-200 mb-1.5">
        {{ $label }}
        @if($required)<span class="text-red-500">*</span>@endif
    </label>

    <input type="hidden" name="{{ $name }}" x-model="selected" @if($required) required @endif>

    <input
        type="text"
        x-model="query"
        @focus="open = true"
        @input="open = true; if (!query) selected = ''"
        @keydown.escape="open = false"
        placeholder="{{ $placeholder }}"
        autocomplete="off"
        class="w-full border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error($name) border-red-400 @enderror"
    >

    <div
        x-show="open && filtered.length > 0"
        x-cloak
        class="absolute z-30 mt-1 w-full max-h-56 overflow-y-auto rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-lg"
    >
        <template x-for="city in filtered" :key="city.name">
            <button
                type="button"
                @click="pick(city)"
                class="w-full text-left px-4 py-2.5 text-sm hover:bg-indigo-50 dark:hover:bg-indigo-950/40 transition border-b border-gray-50 dark:border-slate-800 last:border-0"
            >
                <span class="font-semibold text-gray-800 dark:text-slate-100" x-text="city.name"></span>
                <span class="text-xs text-gray-500 dark:text-slate-400" x-show="city.region" x-text="' · ' + city.region"></span>
            </button>
        </template>
    </div>

    <p x-show="open && filtered.length === 0" x-cloak class="absolute z-30 mt-1 w-full rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-4 py-3 text-sm text-gray-500 dark:text-slate-400 shadow-lg">
        No matching city. Select from the list or contact admin to add a new city.
    </p>

    @error($name)
        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
    @enderror
</div>
