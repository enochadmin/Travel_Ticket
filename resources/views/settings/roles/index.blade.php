<x-app-layout>
    <x-slot name="pageTitle">Roles</x-slot>

    <div class="mx-auto max-w-6xl space-y-5">
        @if (session('success'))
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm px-5 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm px-5 py-3 rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Settings</p>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-slate-100">Available Roles</h2>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Create roles and assign permissions by ticking the permissions list</p>
                </div>
                <a href="{{ route('settings.roles.create') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-white px-4 py-2.5 rounded-xl transition"
                    style="background: linear-gradient(135deg,#4f46e5,#6366f1);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Role
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-slate-800 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Role</th>
                            <th class="px-6 py-3">Users</th>
                            <th class="px-6 py-3">Permissions</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
                        @forelse($roles as $role)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-800 dark:text-slate-100">{{ ucfirst(str_replace('-', ' ', $role->name)) }}</p>
                                    <p class="text-xs text-gray-400">{{ $role->name }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-slate-300">{{ $role->users_count }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex max-w-xl flex-wrap gap-1.5">
                                        @forelse($role->permissions->take(8) as $permission)
                                            <span class="rounded-full bg-indigo-50 text-indigo-700 px-2.5 py-1 text-xs font-semibold">
                                                {{ $permission->name }}
                                            </span>
                                        @empty
                                            <span class="text-gray-400 text-xs">No permissions assigned</span>
                                        @endforelse
                                        @if($role->permissions->count() > 8)
                                            <span class="rounded-full bg-gray-100 text-gray-600 px-2.5 py-1 text-xs font-semibold">
                                                +{{ $role->permissions->count() - 8 }} more
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right space-x-3">
                                    <a href="{{ route('settings.roles.edit', $role) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Edit</a>
                                    @if($role->name !== 'admin' && $role->users_count === 0)
                                        <form action="{{ route('settings.roles.destroy', $role) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Delete this role?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs font-semibold text-red-500 hover:text-red-700">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400">No roles available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($roles->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700">
                    {{ $roles->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
