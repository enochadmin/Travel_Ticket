<x-app-layout>
    <x-slot name="pageTitle">Create Role</x-slot>

    <div class="max-w-4xl">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-700">
                <h2 class="text-lg font-bold text-gray-800 dark:text-slate-100">Create New Role</h2>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Use lowercase names like project-supervisor and tick the permissions this role should have.</p>
            </div>

            <form action="{{ route('settings.roles.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                @include('settings.roles.partials.form', [
                    'role' => null,
                    'permissions' => $permissions,
                    'selectedPermissions' => old('permissions', []),
                ])

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition"
                        style="background:linear-gradient(135deg,#4f46e5,#6366f1);">
                        Save Role
                    </button>
                    <a href="{{ route('settings.roles.index') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
