<x-app-layout>
    <x-slot name="pageTitle">Projects</x-slot>

    <div class="space-y-5">

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

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Header --}}
            <div
                class="px-6 py-5 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">All Projects</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $projects->total() }} projects registered</p>
                </div>

                {{-- Actions Container --}}
                <div class="flex items-center gap-3" x-data="{ showImportModal: false }">
                    {{-- Export Button --}}
                    <a href="{{ route('projects.export') }}"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 hidden sm:flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Export
                    </a>

                    {{-- Import Button (Triggers Modal) --}}
                    <button @click="showImportModal = true"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition flex items-center gap-2 hidden sm:flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Import
                    </button>

                    <a href="{{ route('projects.create') }}"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-white px-4 py-2.5 rounded-xl transition"
                        style="background: linear-gradient(135deg,#4f46e5,#6366f1);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Project
                    </a>

                    {{-- Alpine Import Modal --}}
                    <div x-show="showImportModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
                        aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
                        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                            @click="showImportModal = false"></div>
                        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                            <div
                                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
                                <div class="bg-white px-6 py-5">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">Import Projects via Excel</h3>
                                    <p class="text-sm text-gray-500 mb-4">Upload an Excel (.xlsx) file to bulk create or
                                        update projects.</p>

                                    {{-- Download Template Action --}}
                                    <div
                                        class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 mb-5 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="text-sm font-semibold text-indigo-900">Need the correct
                                                format?</span>
                                        </div>
                                        <a href="{{ route('projects.template') }}"
                                            class="text-sm font-bold text-indigo-600 hover:text-indigo-800 underline">Download
                                            Template</a>
                                    </div>

                                    <form action="{{ route('projects.import') }}" method="POST"
                                        enctype="multipart/form-data" id="importProjectsForm">
                                        @csrf
                                        <label class="block mb-2 text-sm font-semibold text-gray-700">Select
                                            File</label>
                                        <input type="file" name="file" accept=".xlsx,.csv" required
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-200 rounded-xl" />
                                    </form>
                                </div>
                                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 text-right">
                                    <button @click="showImportModal = false" type="button"
                                        class="mr-3 text-sm font-semibold text-gray-600 hover:text-gray-800 transition">Cancel</button>
                                    <button type="button"
                                        onclick="document.getElementById('importProjectsForm').submit()"
                                        class="px-5 py-2 rounded-xl text-white text-sm font-semibold transition bg-indigo-600 hover:bg-indigo-700">
                                        Upload & Import
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Code / Name</th>
                            <th class="px-6 py-3">Discipline</th>
                            <th class="px-6 py-3">Location / Region</th>
                            <th class="px-6 py-3">Manager</th>
                            <th class="px-6 py-3">Period</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($projects as $project)
                            @php
                                $statusMap = [
                                    'active' => 'bg-green-100 text-green-700',
                                    'on-hold' => 'bg-yellow-100 text-yellow-700',
                                    'completed' => 'bg-blue-100 text-blue-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                ];
                                $disciplineColor = [
                                    'Infrastructure' => 'bg-indigo-100 text-indigo-700',
                                    'Water' => 'bg-cyan-100 text-cyan-700',
                                    'Building' => 'bg-orange-100 text-orange-700',
                                ];
                            @endphp
                            <tr class="hover:bg-indigo-50/30 transition">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-800">{{ $project->name }}</p>
                                    @if($project->project_code)
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $project->project_code }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($project->discipline)
                                        <span
                                            class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $disciplineColor[$project->discipline] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ $project->discipline }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-gray-700">{{ $project->location ?? '—' }}</p>
                                    @if($project->region)
                                        <p class="text-xs text-gray-400">{{ $project->region }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ optional($project->manager)->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs">
                                    @if($project->start_date)
                                        {{ $project->start_date->format('M d, Y') }}<br>
                                        <span class="text-gray-400">→
                                            {{ $project->end_date?->format('M d, Y') ?? 'Ongoing' }}</span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusMap[$project->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ ucwords(str_replace('-', ' ', $project->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-3">
                                    <a href="{{ route('projects.show', $project) }}"
                                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">View</a>
                                    <a href="{{ route('projects.edit', $project) }}"
                                        class="text-xs font-semibold text-gray-500 hover:text-gray-700">Edit</a>
                                    <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Delete this project?');">
                                        @csrf @method('DELETE')
                                        <button
                                            class="text-xs font-semibold text-red-500 hover:text-red-700">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                    <p class="text-3xl mb-2">📂</p>
                                    No projects yet. <a href="{{ route('projects.create') }}"
                                        class="text-indigo-600 hover:underline">Create the first one →</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($projects->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>