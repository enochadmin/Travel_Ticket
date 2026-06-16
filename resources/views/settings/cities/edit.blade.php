<x-app-layout>
    <x-slot name="pageTitle">Edit City</x-slot>

    <div class="max-w-lg">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-700">
                <h2 class="text-lg font-bold text-gray-800 dark:text-slate-100">Edit City</h2>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">{{ $city->name }}</p>
            </div>

            <form action="{{ route('settings.cities.update', $city) }}" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-slate-200 mb-1.5">City Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $city->name) }}" required
                        class="w-full border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none transition @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-slate-200 mb-1.5">Region</label>
                    <input type="text" name="region" value="{{ old('region', $city->region) }}"
                        class="w-full border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none transition @error('region') border-red-400 @enderror">
                    @error('region')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $city->is_active) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm font-medium text-gray-700 dark:text-slate-200">Active (visible in travel forms)</span>
                </label>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition"
                        style="background:linear-gradient(135deg,#4f46e5,#6366f1);">
                        Update City
                    </button>
                    <a href="{{ route('settings.cities.index') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
