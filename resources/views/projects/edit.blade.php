<x-app-layout>
    <x-slot name="pageTitle">Edit Project</x-slot>

    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3"
                style="background:linear-gradient(90deg,#fef3c7,#fff)">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#f59e0b">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Edit Project</h2>
                    <p class="text-xs text-gray-400">{{ $project->name }} — {{ $project->project_code }}</p>
                </div>
            </div>

            <form action="{{ route('projects.update', $project) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                {{-- ── Basic Info ── --}}
                <div>
                    <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-3">Basic Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Project Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $project->name) }}" required
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('name') border-red-400 @enderror">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Project ID / Code</label>
                            <input type="text" name="project_code"
                                value="{{ old('project_code', $project->project_code) }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('project_code') border-red-400 @enderror">
                            @error('project_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Discipline</label>
                            <select name="discipline"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition">
                                <option value="">— Select Discipline —</option>
                                @foreach(['Infrastructure', 'Water', 'Building'] as $d)
                                    <option value="{{ $d }}" {{ old('discipline', $project->discipline) === $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label>
                            <textarea name="description" rows="3"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition">{{ old('description', $project->description) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- ── Location ── --}}
                <div>
                    <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-3">Location</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Project Location</label>
                            <input type="text" name="location" value="{{ old('location', $project->location) }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('location') border-red-400 @enderror">
                            @error('location')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Region</label>
                            <input type="text" name="region" value="{{ old('region', $project->region) }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('region') border-red-400 @enderror">
                            @error('region')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- ── Schedule & Ownership ── --}}
                <div>
                    <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-3">Schedule & Ownership
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Project Manager</label>
                            <select name="manager_id"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition">
                                <option value="">— Assign Manager —</option>
                                @foreach($managers as $mgr)
                                    <option value="{{ $mgr->id }}" {{ old('manager_id', $project->manager_id) == $mgr->id ? 'selected' : '' }}>{{ $mgr->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Project Status <span
                                    class="text-red-500">*</span></label>
                            <select name="status"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition">
                                @foreach(['active' => 'Active', 'on-hold' => 'On Hold', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('status', $project->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Start Date</label>
                            <input type="date" name="start_date"
                                value="{{ old('start_date', optional($project->start_date)->format('Y-m-d')) }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('start_date') border-red-400 @enderror">
                            @error('start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">End Date</label>
                            <input type="date" name="end_date"
                                value="{{ old('end_date', optional($project->end_date)->format('Y-m-d')) }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('end_date') border-red-400 @enderror">
                            @error('end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition"
                        style="background:linear-gradient(135deg,#4f46e5,#6366f1);">
                        Update Project
                    </button>
                    <a href="{{ route('projects.index') }}"
                        class="text-sm text-gray-500 hover:text-gray-700 font-medium">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>