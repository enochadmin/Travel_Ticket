<x-app-layout>
    <x-slot name="pageTitle">Create Project</x-slot>

    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3"
                style="background:linear-gradient(90deg,#f5f3ff,#fff)">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#6366f1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">New Project</h2>
                    <p class="text-xs text-gray-400">Fill in all the project details below</p>
                </div>
            </div>

            <form action="{{ route('projects.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                {{-- ── Basic Info ── --}}
                <div>
                    <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-3">Basic Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Project Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('name') border-red-400 @enderror"
                                placeholder="e.g. Addis Ring Road Phase II">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Project ID / Code</label>
                            <input type="text" name="project_code" value="{{ old('project_code') }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('project_code') border-red-400 @enderror"
                                placeholder="e.g. PRJ-2024-001">
                            @error('project_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Discipline <span
                                    class="text-red-500">*</span></label>
                            <select name="discipline"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('discipline') border-red-400 @enderror">
                                <option value="">— Select Discipline —</option>
                                @foreach(['Infrastructure', 'Water', 'Building', 'Head-Office'] as $d)
                                    <option value="{{ $d }}" {{ old('discipline') === $d ? 'selected' : '' }}>{{ $d }}
                                    </option>
                                @endforeach
                            </select>
                            @error('discipline')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label>
                            <textarea name="description" rows="3"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition"
                                placeholder="Brief description of the project scope...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- ── Location ── --}}
                <div>
                    <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-3">Location</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Project Location</label>
                            <input type="text" name="location" value="{{ old('location') }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('location') border-red-400 @enderror"
                                placeholder="e.g. Addis Ababa">
                            @error('location')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Region</label>
                            <input type="text" name="region" value="{{ old('region') }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('region') border-red-400 @enderror"
                                placeholder="e.g. Oromia, Amhara...">
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
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('manager_id') border-red-400 @enderror">
                                <option value="">— Assign Manager —</option>
                                @foreach($managers as $mgr)
                                    <option value="{{ $mgr->id }}" {{ old('manager_id') == $mgr->id ? 'selected' : '' }}>
                                        {{ $mgr->name }}@if($mgr->hasRole('commercial-director')) (Commercial Director)@elseif($mgr->hasRole('head-office-manager')) (Head Office Manager)@endif</option>
                                @endforeach
                            </select>
                            @error('manager_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Project Status <span
                                    class="text-red-500">*</span></label>
                            <select name="status"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition">
                                @foreach(['active' => 'Active', 'on-hold' => 'On Hold', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('status', 'active') === $val ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Start Date</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('start_date') border-red-400 @enderror">
                            @error('start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">End Date</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('end_date') border-red-400 @enderror">
                            @error('end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('alpine:init', () => {
                        Alpine.data('memberPicker', (users, initialSelected) => ({
                            users: users || [],
                            per: 12,
                            q: '',
                            page: 1,
                            selected: Object.fromEntries((initialSelected || []).map((id) => [String(id), true])),
                            get filtered() {
                                const q = this.q.trim().toLowerCase();
                                if (!q) return this.users;
                                return this.users.filter(u =>
                                    u.name.toLowerCase().includes(q) ||
                                    u.email.toLowerCase().includes(q)
                                );
                            },
                            get pages() { return Math.max(1, Math.ceil(this.filtered.length / this.per)); },
                            get cur() { return Math.min(this.page, this.pages); },
                            get paged() {
                                const start = (this.cur - 1) * this.per;
                                return this.filtered.slice(start, start + this.per);
                            },
                            get selectedIds() { return Object.keys(this.selected); },
                            get selectedCount() { return this.selectedIds.length; },
                            toggle(id) {
                                const key = String(id);
                                if (this.selected[key]) {
                                    delete this.selected[key];
                                } else {
                                    this.selected[key] = true;
                                }
                            },
                            pageUp() { if (this.cur < this.pages) this.page = this.cur + 1; },
                            pageDown() { if (this.cur > 1) this.page = this.cur - 1; }
                        }));
                    });
                </script>

                {{-- Team Members --}}
                <div>
                    @php
                        $selectedUserIds = collect(old('user_ids', []))->map(fn($id) => (int) $id)->all();
                        $avatarColors = ['#4f46e5', '#0891b2', '#059669', '#d97706', '#be123c', '#7c3aed', '#2563eb', '#0f766e'];
                    @endphp

                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div>
                            <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Team Members</h3>
                            <p class="text-xs text-gray-400 mt-1">Select the users who should belong to this project.</p>
                        </div>
                        <span class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-100 px-3 py-1.5 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            Add members
                        </span>
                    </div>

                    @error('user_ids')<p class="text-red-500 text-xs mb-2">{{ $message }}</p>@enderror
                    @error('user_ids.*')<p class="text-red-500 text-xs mb-2">{{ $message }}</p>@enderror

                    @php
                        $pickerUsers = ($availableUsers ?? collect())->map(function ($user, $index) use ($avatarColors) {
                            $parts = collect(explode(' ', trim($user->name)))->filter();
                            $initials = strtoupper($parts->map(fn($part) => \Illuminate\Support\Str::substr($part, 0, 1))->take(2)->implode(''))
                                ?: strtoupper(\Illuminate\Support\Str::substr($user->name, 0, 1));

                            return [
                                'id' => (int) $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                                'role' => ucfirst(str_replace('-', ' ', $user->roles->first()?->name ?? 'user')),
                                'placement' => $user->project ? 'Currently: ' . $user->project->name : 'Unassigned',
                                'initials' => $initials,
                                'color' => $avatarColors[$index % count($avatarColors)],
                            ];
                        })->values()->all();
                    @endphp

                    <div x-data="memberPicker(@js($pickerUsers), @js($selectedUserIds))" class="space-y-3">
                        {{-- Realtime search --}}
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" x-model="q"
                                placeholder="Search employee by name or email…"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition">
                        </div>

                        <p class="text-xs text-gray-400">
                            <span x-text="`${selectedCount} selected`"></span>
                            <template x-if="q.trim() !== ''">
                                <span> · <span x-text="`${filtered.length} match${filtered.length === 1 ? '' : 'es'}`"></span></span>
                            </template>
                        </p>

                        {{-- Paginated grid --}}
                        <template x-if="filtered.length === 0">
                            <div
                                class="rounded-xl border border-dashed border-gray-200 bg-gray-50 px-4 py-5 text-center text-sm text-gray-500">
                                No employees match your search.
                            </div>
                        </template>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <template x-for="user in paged" :key="user.id">
                                <label
                                    class="relative flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3 cursor-pointer transition hover:border-indigo-200 hover:bg-indigo-50/40 shadow-sm"
                                    :class="selected[user.id] ? 'border-indigo-300 bg-indigo-50/50' : ''">
                                    <input type="checkbox"
                                        class="peer sr-only" :checked="!!selected[user.id]"
                                        @change="toggle(user.id)">
                                    <span class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-sm ring-2 ring-white"
                                        :style="`background:${user.color};`">
                                        <span x-text="user.initials"></span>
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block text-sm font-semibold text-gray-800 truncate"
                                            x-text="user.name"></span>
                                        <span class="block text-xs text-gray-400 truncate" x-text="user.email"></span>
                                        <span
                                            class="mt-1 inline-flex max-w-full items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-semibold text-gray-600">
                                            <span x-text="user.role"></span>
                                            <span class="mx-1 text-gray-300">/</span>
                                            <span class="truncate" x-text="user.placement"></span>
                                        </span>
                                    </span>
                                    <span
                                        class="w-5 h-5 rounded-full border border-gray-300 bg-white flex items-center justify-center text-white transition peer-checked:bg-indigo-600 peer-checked:border-indigo-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M16.704 5.29a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3.25-3.25a1 1 0 111.414-1.414l2.543 2.543 6.543-6.543a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </label>
                            </template>
                        </div>

                        {{-- Pagination --}}
                        <div class="flex items-center justify-between gap-3 pt-1"
                            x-show="filtered.length > 0" x-cloak>
                            <p class="text-xs text-gray-400"
                                x-text="`Showing ${paged.length ? ((cur - 1) * per + 1) : 0}–${Math.min(cur * per, filtered.length)} of ${filtered.length}`"></p>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="pageDown()" :disabled="cur <= 1"
                                    class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                    ← Prev
                                </button>
                                <span class="text-xs font-semibold text-gray-500" x-text="`Page ${cur} / ${pages}`"></span>
                                <button type="button" @click="pageUp()" :disabled="cur >= pages"
                                    class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                    Next →
                                </button>
                            </div>
                        </div>

                        {{-- Keep every selection in the submitted payload --}}
                        <template x-for="id in selectedIds" :key="id">
                            <input type="hidden" name="user_ids[]" :value="id">
                        </template>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition"
                        style="background:linear-gradient(135deg,#4f46e5,#6366f1);">
                        Create Project
                    </button>
                    <a href="{{ route('projects.index') }}"
                        class="text-sm text-gray-500 hover:text-gray-700 font-medium">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
