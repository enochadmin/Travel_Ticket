<x-app-layout>
    <x-slot name="pageTitle">Project Details</x-slot>

    @php
        $statusMap = [
            'active' => ['label' => 'Active', 'color' => 'bg-green-100 text-green-700'],
            'on-hold' => ['label' => 'On Hold', 'color' => 'bg-yellow-100 text-yellow-700'],
            'completed' => ['label' => 'Completed', 'color' => 'bg-blue-100 text-blue-700'],
            'cancelled' => ['label' => 'Cancelled', 'color' => 'bg-red-100 text-red-700'],
        ];
        $s = $statusMap[$project->status] ?? ['label' => ucfirst($project->status), 'color' => 'bg-gray-100 text-gray-700'];
    @endphp

    <div class="max-w-3xl space-y-5">

        @if (session('success'))
            <div
                class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm px-5 py-3 rounded-xl">
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
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @hasrole('project-manager')
        {{-- PM Dashboard Summary --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white rounded-2xl border border-gray-100 p-4">
                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Requests</p>
                <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ $stats['total_requests'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4">
                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Pending PM</p>
                <p class="text-2xl font-extrabold text-yellow-700 mt-1">{{ $stats['pending_pm'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4">
                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Pending Commercial</p>
                <p class="text-2xl font-extrabold text-purple-700 mt-1">{{ $stats['pending_commercial'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4">
                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Requesters</p>
                <p class="text-2xl font-extrabold text-indigo-700 mt-1">{{ $stats['unique_requesters'] ?? 0 }}</p>
            </div>
        </div>

        @if(($pendingRequests ?? collect())->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between"
                    style="background:linear-gradient(90deg,#faf5ff,#fff)">
                    <div>
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-widest">Pending Requests</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">Latest pending tickets in this project</p>
                    </div>
                    <a href="{{ route('travel-requests.index') }}"
                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                        View all →
                    </a>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($pendingRequests as $r)
                        <div class="px-6 py-4 flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $r->user?->name ?? '—' }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $r->destination }} •
                                    {{ \Carbon\Carbon::parse($r->travel_date)->format('M d, Y') }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                @php
                                    $badge = [
                                        'pending_pm' => 'bg-yellow-100 text-yellow-800',
                                        'pending_commercial' => 'bg-purple-100 text-purple-800',
                                        'pending_ceo' => 'bg-indigo-100 text-indigo-800',
                                    ][$r->status] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                                    {{ ucfirst(str_replace('_', ' ', $r->status)) }}
                                </span>
                                <a href="{{ route('travel-requests.show', $r) }}"
                                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition">
                                    Open
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        @endhasrole

        {{-- Header card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between"
                style="background:linear-gradient(90deg,#f5f3ff,#fff)">
                <div>
                    <p class="text-xs text-indigo-500 font-semibold uppercase tracking-widest">
                        {{ $project->discipline ?? 'Project' }}
                    </p>
                    <h2 class="text-xl font-bold text-gray-800 mt-0.5">{{ $project->name }}</h2>
                    @if($project->project_code)
                        <p class="text-xs text-gray-400 mt-0.5">{{ $project->project_code }}</p>
                    @endif
                </div>
                <span class="px-3 py-1.5 rounded-full text-sm font-semibold {{ $s['color'] }}">{{ $s['label'] }}</span>
            </div>

            {{-- Details Grid --}}
            <div class="grid grid-cols-2 divide-x divide-y divide-gray-50">
                @php
                    $fields = [
                        ['Location', $project->location ?? '—'],
                        ['Region', $project->region ?? '—'],
                        ['Discipline', $project->discipline ?? '—'],
                        ['Project Manager', optional($project->manager)->name ?? '—'],
                        ['Start Date', $project->start_date?->format('M d, Y') ?? '—'],
                        ['End Date', $project->end_date?->format('M d, Y') ?? '—'],
                    ];
                @endphp
                @foreach($fields as [$label, $value])
                    <div class="px-6 py-4">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">{{ $label }}</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $value }}</p>
                    </div>
                @endforeach
            </div>

            @if($project->description)
                <div class="px-6 py-4 border-t border-gray-50">
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Description</p>
                    <p class="text-sm text-gray-700">{{ $project->description }}</p>
                </div>
            @endif

            {{-- Project Members --}}
            <div class="px-6 py-5 border-t border-gray-50 bg-white" x-data="{ showAddMember: false }">
                @php
                    $selectedUserIds = collect(old('user_ids', []))->map(fn($id) => (int) $id)->all();
                    $avatarColors = ['#4f46e5', '#0891b2', '#059669', '#d97706', '#be123c', '#7c3aed', '#2563eb', '#0f766e'];
                @endphp
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Project Members ({{ $project->members->count() }})
                    </p>

                    @if(!empty($canManageMembers) && $canManageMembers)
                        <button @click="showAddMember = !showAddMember"
                            class="text-sm font-semibold bg-indigo-600 text-white px-4 py-2 rounded-xl hover:bg-indigo-700 shadow-sm hover:shadow transition flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform"
                                :class="{'rotate-45': showAddMember}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add members
                        </button>
                    @endif
                </div>

                {{-- Collapsible Add Member Form --}}
                @if(!empty($canManageMembers) && $canManageMembers)
                    <div x-show="showAddMember" x-collapse>
                        <form method="POST" action="{{ route('projects.members.store', $project) }}"
                            class="mb-5 bg-gray-50 p-4 rounded-xl border border-gray-100">
                            @csrf
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Select members to assign</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Choose one or more users, then assign them to this project.</p>
                                </div>
                                <button type="submit"
                                    class="px-4 py-2 rounded-xl text-white text-sm font-semibold transition bg-indigo-600 hover:bg-indigo-700 shadow-sm">
                                    Assign selected
                                </button>
                            </div>

                            @error('user_ids')<p class="text-red-500 text-xs mb-2">{{ $message }}</p>@enderror
                            @error('user_ids.*')<p class="text-red-500 text-xs mb-2">{{ $message }}</p>@enderror

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-80 overflow-y-auto pr-1">
                                @forelse(($availableUsers ?? collect()) as $u)
                                    @php
                                        $parts = collect(explode(' ', trim($u->name)))->filter();
                                        $initials = $parts->map(fn($part) => \Illuminate\Support\Str::substr($part, 0, 1))->take(2)->implode('');
                                        $role = ucfirst(str_replace('-', ' ', $u->roles->first()?->name ?? 'user'));
                                        $color = $avatarColors[$loop->index % count($avatarColors)];
                                    @endphp
                                    <label class="relative flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3 cursor-pointer transition hover:border-indigo-200 hover:bg-white shadow-sm">
                                        <input type="checkbox" name="user_ids[]" value="{{ $u->id }}"
                                            class="peer sr-only" {{ in_array((int) $u->id, $selectedUserIds, true) ? 'checked' : '' }}>
                                        <span class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-sm ring-2 ring-white"
                                            style="background:{{ $color }};">
                                            {{ strtoupper($initials ?: \Illuminate\Support\Str::substr($u->name, 0, 1)) }}
                                        </span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block text-sm font-semibold text-gray-800 truncate">{{ $u->name }}</span>
                                            <span class="block text-xs text-gray-400 truncate">{{ $u->email }}</span>
                                            <span class="mt-1 inline-flex max-w-full items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-semibold text-gray-600">
                                                {{ $role }}
                                                <span class="mx-1 text-gray-300">/</span>
                                                {{ $u->project ? 'Currently: ' . $u->project->name : 'Unassigned' }}
                                            </span>
                                        </span>
                                        <span class="w-5 h-5 rounded-full border border-gray-300 bg-white flex items-center justify-center text-white transition peer-checked:bg-indigo-600 peer-checked:border-indigo-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M16.704 5.29a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3.25-3.25a1 1 0 111.414-1.414l2.543 2.543 6.543-6.543a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </label>
                                @empty
                                    <div class="sm:col-span-2 rounded-xl border border-dashed border-gray-200 bg-white px-4 py-5 text-center">
                                        <p class="text-sm font-semibold text-gray-600">No available users to add.</p>
                                        <p class="text-xs text-gray-400 mt-1">Everyone eligible is already assigned to this project.</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="hidden">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Select members to
                                        assign</label>
                                    <select name="legacy_user_ids[]" multiple disabled
                                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition @error('user_ids') border-red-400 @enderror">
                                        @foreach(($availableUsers ?? collect()) as $u)
                                            <option value="{{ $u->id }}">
                                                {{ $u->name }} — {{ ucfirst($u->roles->first()?->name ?? 'user') }}
                                                @if($u->project)
                                                    (currently: {{ $u->project->name }})
                                                @else
                                                    (unassigned)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_ids')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                                    @error('user_ids.*')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                                    <p class="text-xs text-gray-400 mt-1">Tip: Hold Ctrl (Windows) to select multiple.</p>
                                </div>
                                <button type="submit"
                                    class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold transition w-full sm:w-auto"
                                    style="background:linear-gradient(135deg,#4f46e5,#6366f1);">
                                    Assign Members
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- Member Grid --}}
                @if($project->members->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($project->members as $member)
                            @php
                                $parts = collect(explode(' ', trim($member->name)))->filter();
                                $initials = $parts->map(fn($part) => \Illuminate\Support\Str::substr($part, 0, 1))->take(2)->implode('');
                                $color = $avatarColors[$loop->index % count($avatarColors)];
                            @endphp
                            <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50/50">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-sm ring-2 ring-white"
                                    style="background:{{ $color }};">
                                    {{ strtoupper($initials ?: \Illuminate\Support\Str::substr($member->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $member->name }}</p>
                                    <p class="text-xs text-indigo-500 truncate">
                                        {{ ucfirst($member->roles->first()?->name ?? 'User') }}
                                    </p>
                                </div>

                                @if(!empty($canManageMembers) && $canManageMembers && (int) $member->id !== (int) $project->manager_id)
                                    <form method="POST" action="{{ route('projects.members.destroy', [$project, $member]) }}"
                                        onsubmit="return confirm('Remove this member from the project?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-xs font-semibold text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition">
                                            Remove
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 italic mt-2">No members assigned to this project yet.</p>
                @endif
            </div>
        </div>

        {{-- Project History (Travel Records) --}}
        <div class="px-6 py-5 border-t border-gray-50 bg-white" x-data="{ selectedRequest: null }">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Project History & Travel Records ({{ $project->travelRequests->count() }})
            </p>

            @if($project->travelRequests->count() > 0)
                <div class="overflow-hidden rounded-xl border border-gray-100 mb-4">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr
                                class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                <th class="px-5 py-3">Requester</th>
                                <th class="px-5 py-3">Location (Dest.)</th>
                                <th class="px-5 py-3">Travel Date</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($project->travelRequests->sortByDesc('created_at') as $request)
                                <tr class="hover:bg-indigo-50/30 transition">
                                    <td class="px-5 py-3.5 font-medium text-gray-800 flex items-center gap-3">
                                        <div
                                            class="w-7 h-7 rounded-md bg-gray-100 text-gray-600 flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($request->user->name, 0, 1)) }}
                                        </div>
                                        {{ $request->user->name }}
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-600 font-medium">{{ $request->destination }}</td>
                                    <td class="px-5 py-3.5 text-gray-500">
                                        {{ \Carbon\Carbon::parse($request->travel_date)->format('M d, Y') }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @php
                                            $map = [
                                                'pending_pm' => ['label' => 'Pending PM', 'color' => 'bg-yellow-100 text-yellow-800'],
                                                'pending_commercial' => ['label' => 'Pending Commercial', 'color' => 'bg-purple-100 text-purple-800'],
                                                'pending_ceo' => ['label' => 'Pending CEO', 'color' => 'bg-indigo-100 text-indigo-800'],
                                                'approved' => ['label' => 'Approved', 'color' => 'bg-green-100 text-green-800'],
                                                'rejected' => ['label' => 'Rejected', 'color' => 'bg-red-100 text-red-800'],
                                            ];
                                            $rs = $map[$request->status] ?? ['label' => ucfirst($request->status), 'color' => 'bg-gray-100 text-gray-800'];
                                        @endphp
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-semibold shadow-sm {{ $rs['color'] }}">
                                            {{ $rs['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right relative">
                                        {{-- Alpine Payload Setup --}}
                                        @php
                                            $payload = [
                                                'id' => $request->id,
                                                'requester' => $request->user->name,
                                                'destination' => $request->destination,
                                                'travel_date' => \Carbon\Carbon::parse($request->travel_date)->format('M d, Y'),
                                                'return_date' => $request->return_date ? \Carbon\Carbon::parse($request->return_date)->format('M d, Y') : 'N/A',
                                                'purpose' => $request->purpose,
                                                'remarks' => $request->remarks ?? 'None',
                                                'status_label' => $rs['label'],
                                                'status_color' => $rs['color'],
                                                'created' => $request->created_at->format('M d, Y h:i A'),
                                                'pm_name' => optional($request->pm)->name ?? 'Pending/None',
                                                'dir_name' => optional($request->hod)->name ?? 'Pending/None',
                                            ];
                                        @endphp
                                        <button type="button" @click="selectedRequest = {{ json_encode($payload) }}"
                                            class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition">
                                            View Details
                                        </button>
                                        {{-- Direct link as fallback or standard edit --}}
                                        <a href="{{ route('travel-requests.show', $request) }}" title="Open Full Page"
                                            class="ml-1 text-gray-400 hover:text-indigo-600 inline-block align-middle">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6 bg-gray-50 border border-gray-100 rounded-xl text-center">
                    <p class="text-3xl mb-2">📭</p>
                    <p class="text-sm font-medium text-gray-600">No travel history found.</p>
                    <p class="text-xs text-gray-400 mt-1">Travel records related to this project will appear here.</p>
                </div>
            @endif

            {{-- Alpine.js Modal for Travel Record Popup --}}
            <div x-show="selectedRequest" x-cloak style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
                aria-labelledby="modal-title" role="dialog" aria-modal="true">

                {{-- Backdrop --}}
                <div x-show="selectedRequest" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="selectedRequest = null">
                </div>

                {{-- Modal Panel --}}
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="selectedRequest" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100">

                        {{-- Modal Header --}}
                        <div class="bg-indigo-900 px-6 py-4 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2" id="modal-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-300" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 004 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                                </svg>
                                Travel Record Details
                            </h3>
                            <button @click="selectedRequest = null"
                                class="text-white hover:text-indigo-200 transition focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="px-6 py-5">
                            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                                <div>
                                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Requester
                                    </p>
                                    <p class="text-xl font-bold text-gray-800" x-text="selectedRequest?.requester"></p>
                                </div>
                                <div class="text-right">
                                    <span :class="selectedRequest?.status_color"
                                        class="px-3 py-1.5 rounded-full text-xs font-bold shadow-sm inline-block mb-1"
                                        x-text="selectedRequest?.status_label"></span>
                                    <p class="text-xs text-gray-400 mt-1">Requested: <span
                                            x-text="selectedRequest?.created"></span></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6 mb-6">
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">
                                        Destination</p>
                                    <p class="text-sm font-semibold text-gray-800"
                                        x-text="selectedRequest?.destination"></p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 grid grid-cols-2 gap-2">
                                    <div>
                                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">
                                            Departure</p>
                                        <p class="text-sm font-semibold text-gray-800"
                                            x-text="selectedRequest?.travel_date"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">
                                            Return</p>
                                        <p class="text-sm font-semibold text-gray-800"
                                            x-text="selectedRequest?.return_date"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-6">
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Purpose of
                                    Travel</p>
                                <div class="bg-white border border-gray-100 p-4 rounded-xl text-sm text-gray-700 shadow-sm"
                                    x-text="selectedRequest?.purpose"></div>
                            </div>

                            <div class="mb-6">
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Remarks</p>
                                <div class="bg-yellow-50/50 border border-yellow-100 p-4 rounded-xl text-sm text-gray-700 italic"
                                    x-text="selectedRequest?.remarks"></div>
                            </div>

                            {{-- Approvals --}}
                            <div>
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Approval
                                    Chain</p>
                                <div class="flex gap-4">
                                    <div
                                        class="flex-1 bg-white border border-gray-100 p-3 rounded-lg flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400">Project Manager</p>
                                            <p class="text-sm font-semibold text-gray-800"
                                                x-text="selectedRequest?.pm_name"></p>
                                        </div>
                                    </div>
                                    <div
                                        class="flex-1 bg-white border border-gray-100 p-3 rounded-lg flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400">Director</p>
                                            <p class="text-sm font-semibold text-gray-800"
                                                x-text="selectedRequest?.dir_name"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 text-right">
                            <a :href="'/travel-requests/' + selectedRequest?.id"
                                class="text-sm font-semibold text-indigo-600 hover:underline mr-4">View Full Page →</a>
                            <button @click="selectedRequest = null" type="button"
                                class="inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none transition">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3 mt-6">
        <a href="{{ route('travel-requests.create', ['project_id' => $project->id]) }}"
            class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold transition flex items-center gap-2 shadow hover:shadow-md"
            style="background:linear-gradient(135deg,#10b981,#34d399);">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Ticket
        </a>

        @hasanyrole('admin|head-office-director|commercial-director|ceo')
        <a href="{{ route('projects.edit', $project) }}"
            class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold transition"
            style="background:linear-gradient(135deg,#4f46e5,#6366f1);">
            Edit Project
        </a>
        @endhasanyrole

        <a href="{{ route('projects.index') }}"
            class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
            ← Back to Projects
        </a>

        @hasanyrole('admin|head-office-director|commercial-director|ceo')
        <form action="{{ route('projects.destroy', $project) }}" method="POST" class="ml-auto"
            onsubmit="return confirm('Delete this project permanently?');">
            @csrf @method('DELETE')
            <button
                class="px-5 py-2.5 rounded-xl text-red-600 border border-red-200 text-sm font-medium hover:bg-red-50 transition shadow-sm">
                Delete
            </button>
        </form>
        @endhasanyrole
    </div>
    </div>
</x-app-layout>
