<x-app-layout>
    <x-slot name="pageTitle">Open-as Log</x-slot>

    <div class="space-y-5">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-800">"Open as" Activity</h2>
                <p class="text-xs text-gray-400 mt-0.5">Who impersonated whom, when — admin-only audit trail.</p>
            </div>

            @if (session('success'))
                <div class="mx-6 mt-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm px-5 py-3 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">When</th>
                            <th class="px-6 py-3">Administrator</th>
                            <th class="px-6 py-3">Opened user</th>
                            <th class="px-6 py-3">Action</th>
                            <th class="px-6 py-3">IP address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-indigo-50/30 transition">
                                <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-800">
                                    {{ $log->admin?->name ?? '—' }}
                                    @if($log->admin)<span class="block text-xs font-normal text-gray-400">{{ $log->admin->email }}</span>@endif
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $log->target?->name ?? '—' }}
                                    @if($log->target)<span class="block text-xs text-gray-400">{{ $log->target->email }}</span>@endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($log->action === 'start')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-100 text-sky-800">Opened</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">Exited</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $log->ip_address ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    <p class="text-2xl mb-2">🔍</p>
                                    No "Open as" activity recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
