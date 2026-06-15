<div>
    <label class="block text-sm font-semibold text-gray-700 dark:text-slate-200 mb-1.5">Role Name <span class="text-red-500">*</span></label>
    <input type="text" name="name" value="{{ old('name', $role?->name) }}" required
        class="w-full border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none transition @error('name') border-red-400 @enderror"
        placeholder="e.g. travel-auditor">
    @error('name')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
</div>

<div>
    <div class="mb-3">
        <h3 class="text-sm font-bold text-gray-800 dark:text-slate-100">Permissions</h3>
        <p class="text-xs text-gray-500 dark:text-slate-400">Tick every permission this role should receive.</p>
    </div>

    @error('permissions')<p class="text-red-500 text-xs mb-2">{{ $message }}</p>@enderror

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($permissions as $permission)
            <label class="flex items-start gap-3 rounded-xl border border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/70 px-4 py-3 cursor-pointer hover:border-indigo-200 transition">
                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                    {{ in_array($permission->name, $selectedPermissions, true) ? 'checked' : '' }}
                    class="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span>
                    <span class="block text-sm font-semibold text-gray-700 dark:text-slate-100">{{ $permission->name }}</span>
                    <span class="block text-xs text-gray-400">{{ ucfirst(str_replace(['.', '-'], [' ', ' '], $permission->name)) }}</span>
                </span>
            </label>
        @endforeach
    </div>
</div>
