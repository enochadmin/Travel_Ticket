<x-app-layout>
    <x-slot name="pageTitle">User Management</x-slot>

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
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">System Users</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Manage users, roles and project assignments</p>
                </div>

                {{-- Search Form --}}
                <form method="GET" action="{{ route('users.index') }}" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..."
                        class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('users.index') }}"
                            class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                            Clear
                        </a>
                    @endif
                </form>

                {{-- Actions Container --}}
                <div class="flex items-center gap-3" x-data="{ showImportModal: false }">
                    {{-- Export Button --}}
                    <a href="{{ route('users.export') }}"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Export
                    </a>

                    {{-- Import Button (Triggers Modal) --}}
                    <button @click="showImportModal = true"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Import
                    </button>

                    <a href="{{ route('users.create') }}"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-white px-4 py-2.5 rounded-xl transition"
                        style="background: linear-gradient(135deg,#10b981,#34d399);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add new user
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
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">Import Users via Excel</h3>
                                    <p class="text-sm text-gray-500 mb-4">Upload an Excel (.xlsx) file using the template columns:
                                        <strong>name</strong>, <strong>email</strong>, <strong>project_id</strong> (optional),
                                        and <strong>role_id</strong>. New users receive the default password
                                        <strong>password</strong> and must change it on first login.</p>

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
                                        <a href="{{ route('users.template') }}"
                                            class="text-sm font-bold text-indigo-600 hover:text-indigo-800 underline">Download
                                            Template</a>
                                    </div>

                                    <form action="{{ route('users.import') }}" method="POST"
                                        enctype="multipart/form-data" id="importUsersForm">
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
                                    <button type="button" onclick="document.getElementById('importUsersForm').submit()"
                                        class="px-5 py-2 rounded-xl text-white text-sm font-semibold transition bg-indigo-600 hover:bg-indigo-700">
                                        Upload & Import
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">User</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Role</th>
                            <th class="px-6 py-3">Project Assignment</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($users as $user)
                            <tr class="hover:bg-indigo-50/30 transition">
                                <td class="px-6 py-4 font-semibold text-gray-800">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                                        {{ $user->roles->first()?->name ?? 'None' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ optional($user->project)->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('users.edit', $user) }}"
                                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition">Edit</a>

                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Are you sure you want to permanently delete this user?');">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="text-xs font-semibold text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>