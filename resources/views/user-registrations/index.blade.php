<x-app-layout>
    <x-slot name="pageTitle">User Registrations</x-slot>

    <div class="space-y-5">
        @if (session('success'))
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm px-5 py-3 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-500 flex-shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm px-5 py-3 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 flex-shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Registered Users</h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Review access requests and approve with the default password
                        @if ($pendingCount > 0)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                {{ $pendingCount }} pending
                            </span>
                        @endif
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <div class="flex items-center gap-1 bg-gray-50 p-1 rounded-xl border border-gray-100">
                        @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'] as $key => $label)
                            <a href="{{ route('user-registrations.index', array_filter(['status' => $key === 'all' ? 'all' : $key, 'search' => request('search')])) }}"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ ($status === $key || ($key === 'all' && ! in_array($status, ['pending', 'approved', 'rejected'], true))) ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    <form method="GET" action="{{ route('user-registrations.index') }}" class="flex items-center gap-2">
                        <input type="hidden" name="status" value="{{ $status }}">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                            class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="submit"
                            class="px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                            Search
                        </button>
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Full Name</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Project Name</th>
                            <th class="px-6 py-3">Role</th>
                            <th class="px-6 py-3">Submitted</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($registrations as $registration)
                            <tr class="hover:bg-indigo-50/30 transition">
                                <td class="px-6 py-4 font-semibold text-gray-800">{{ $registration->name }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $registration->email }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $registration->project_name }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                                        {{ $registration->roleLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                    {{ $registration->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClasses = match ($registration->status) {
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            default => 'bg-amber-100 text-amber-800',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClasses }}">
                                        {{ ucfirst($registration->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    @if ($registration->isPending())
                                        <form action="{{ route('user-registrations.approve', $registration) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Approve this registration? The user will receive the default password \"password\" and must change it on first login.');">
                                            @csrf
                                            <button type="submit"
                                                class="text-xs font-semibold text-green-700 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg transition">
                                                Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('user-registrations.reject', $registration) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Reject this registration request?');">
                                            @csrf
                                            <button type="submit"
                                                class="text-xs font-semibold text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition">
                                                Reject
                                            </button>
                                        </form>
                                    @elseif ($registration->user_id)
                                        <a href="{{ route('users.edit', $registration->user_id) }}"
                                            class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition">
                                            Edit user
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">
                                    No registrations found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($registrations->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $registrations->links() }}
                </div>
            @endif
        </div>

        <p class="text-xs text-gray-400 px-1">
            After approval, assign the correct system project under
            <a href="{{ route('users.index') }}" class="text-indigo-600 hover:underline">User Management → Edit</a>
            using the requested project name as a guide.
        </p>
    </div>
</x-app-layout>
