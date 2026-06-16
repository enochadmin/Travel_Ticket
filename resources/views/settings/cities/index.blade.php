<x-app-layout>
    <x-slot name="pageTitle">Cities</x-slot>

    <div class="mx-auto max-w-4xl space-y-5">
        @if (session('success'))
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm px-5 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Settings</p>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-slate-100">Ethiopian Cities</h2>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage cities available for travel origin and destination</p>
                </div>
                <a href="{{ route('settings.cities.create') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-white px-4 py-2.5 rounded-xl transition"
                    style="background: linear-gradient(135deg,#4f46e5,#6366f1);">
                    Add City
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-slate-800 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">City</th>
                            <th class="px-6 py-3">Region</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
                        @forelse($cities as $city)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4 font-semibold text-gray-800 dark:text-slate-100">{{ $city->name }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-slate-400">{{ $city->region ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    @if($city->is_active)
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Active</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-3">
                                    <a href="{{ route('settings.cities.edit', $city) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Edit</a>
                                    <form action="{{ route('settings.cities.destroy', $city) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Remove this city?');">
                                        @csrf @method('DELETE')
                                        <button class="text-xs font-semibold text-red-500 hover:text-red-700">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                    No cities yet. Run the seeder or add cities manually.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($cities->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700">
                    {{ $cities->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
