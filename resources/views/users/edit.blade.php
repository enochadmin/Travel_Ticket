<x-app-layout>
    <x-slot name="pageTitle">Assign Role & Project</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100" style="background:linear-gradient(90deg,#eef2ff,#fff)">
                <h2 class="text-lg font-bold text-gray-800">Edit User Account: {{ $user->name }}</h2>
                <p class="text-xs text-gray-500 mt-0.5">Update user information, role, and project assignment.</p>
            </div>

            <form action="{{ route('users.update', $user) }}" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Full Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address <span
                            class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('email') border-red-400 @enderror">
                    @error('email')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">New Password <span
                                class="text-gray-400 font-normal">(Leave blank to keep current)</span></label>
                        <input type="password" name="password"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('password') border-red-400 @enderror">
                        @error('password')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm New Password</label>
                        <input type="password" name="password_confirmation"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition">
                    </div>
                </div>

                <hr class="border-gray-100 my-4">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">System Role <span
                            class="text-red-500">*</span></label>
                    <select name="role" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('role') border-red-400 @enderror">
                        <option value="">-- Select a Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ ($user->roles->first()?->name === $role->name) ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Project membership <span
                            class="text-gray-400 font-normal">(Optional)</span></label>
                    <p class="text-xs text-gray-500 mb-2">For travel request access only — not for approval. To assign who approves tickets, set the Project Manager on the project page.</p>
                    @if($user->managedProject)
                        <p class="text-xs text-indigo-700 mb-2 rounded-lg bg-indigo-50 border border-indigo-100 px-3 py-2">
                            This user approves tickets for <strong>{{ $user->managedProject->name }}</strong> (assigned on the project).
                        </p>
                    @endif
                    <select name="project_id"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('project_id') border-red-400 @enderror">
                        <option value="">-- No project --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $user->project_id) == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}@if($project->manager) (PM: {{ $project->manager->name }})@endif
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-3 pt-4">
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition"
                        style="background:linear-gradient(135deg,#4f46e5,#6366f1);">
                        Save Changes
                    </button>
                    <a href="{{ route('users.index') }}"
                        class="text-sm text-gray-500 hover:text-gray-700 font-medium">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>