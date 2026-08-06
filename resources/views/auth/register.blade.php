<x-auth-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-white to-sky-50 px-4 py-8 sm:py-12">
        <div class="w-full max-w-md sm:max-w-lg">

            <div class="text-center mb-8 sm:mb-10">
                <div class="flex justify-center items-center mb-5 sm:mb-6">
                    <img
                        src="{{ asset('images/eec-logo.png') }}"
                        alt="EEC Travel Logo"
                        class="h-16 sm:h-20 w-auto max-w-[180px] sm:max-w-[220px] object-contain drop-shadow-md"
                    >
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 mb-2">
                    Request Access
                </h1>
                <p class="text-slate-600 text-base">
                    Submit your details for IT Admin approval — you'll sign in with the password you choose below
                </p>
            </div>

            @if (session('status'))
                <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-center">
                    <p class="text-sm font-semibold text-amber-900 leading-relaxed">
                        {{ session('status') }}
                    </p>
                    <a href="{{ route('login') }}" class="inline-block mt-3 text-sm font-medium text-sky-600 hover:text-sky-700">
                        Back to sign in
                    </a>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-8 pt-8 pb-10 sm:px-10">
                    <form method="POST" action="{{ route('register') }}" class="space-y-5">
                        @csrf

                        <div>
                            <x-input-label for="name" value="Full Name (including grandfather)" class="text-slate-700 font-semibold text-base" />
                            <x-text-input
                                id="name"
                                class="block mt-2 w-full bg-white border-slate-300 text-slate-900 placeholder-slate-400 focus:border-sky-500 focus:ring-sky-500 rounded-2xl py-3.5 px-5 text-base shadow-sm"
                                type="text"
                                name="name"
                                :value="old('name')"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="e.g. Abebe Kebede Alemu"
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-600 text-sm" />
                        </div>

                        <div>
                            <x-input-label for="email" value="Email address" class="text-slate-700 font-semibold text-base" />
                            <x-text-input
                                id="email"
                                class="block mt-2 w-full bg-white border-slate-300 text-slate-900 placeholder-slate-400 focus:border-sky-500 focus:ring-sky-500 rounded-2xl py-3.5 px-5 text-base shadow-sm"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                autocomplete="username"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-sm" />
                        </div>

                        <div>
                            <x-input-label for="project_id" value="Project" class="text-slate-700 font-semibold text-base" />
                            <select
                                id="project_id"
                                name="project_id"
                                required
                                class="block mt-2 w-full bg-white border-slate-300 text-slate-900 focus:border-sky-500 focus:ring-sky-500 rounded-2xl py-3.5 px-5 text-base shadow-sm"
                            >
                                <option value="" disabled {{ old('project_id') ? '' : 'selected' }}>Select a project</option>
                                @foreach ($projects as $id => $name)
                                    <option value="{{ $id }}" data-name="{{ $name }}" {{ (string) old('project_id') === (string) $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                                <option value="other" {{ old('project_id') === 'other' ? 'selected' : '' }}>
                                    Other (my project isn't listed)
                                </option>
                            </select>
                            <p class="mt-1.5 text-xs text-slate-500">Choose your project, or pick "Other" if it isn't listed.</p>
                            <x-input-error :messages="$errors->get('project_id')" class="mt-2 text-red-600 text-sm" />
                        </div>

                        <div id="custom_project_field" class="hidden">
                            <x-input-label for="project_name" value="Project Name" class="text-slate-700 font-semibold text-base" />
                            <x-text-input
                                id="project_name"
                                class="block mt-2 w-full bg-white border-slate-300 text-slate-900 placeholder-slate-400 focus:border-sky-500 focus:ring-sky-500 rounded-2xl py-3.5 px-5 text-base shadow-sm"
                                type="text"
                                name="project_name"
                                :value="old('project_name')"
                                placeholder="Enter your project name"
                            />
                            <p class="mt-1.5 text-xs text-slate-500">Your IT Admin will review this and add it to the system if approved.</p>
                            <x-input-error :messages="$errors->get('project_name')" class="mt-2 text-red-600 text-sm" />
                        </div>

                        <div>
                            <x-input-label for="role" value="Role" class="text-slate-700 font-semibold text-base" />
                            <select
                                id="role"
                                name="role"
                                required
                                class="block mt-2 w-full bg-white border-slate-300 text-slate-900 focus:border-sky-500 focus:ring-sky-500 rounded-2xl py-3.5 px-5 text-base shadow-sm"
                            >
                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select a role</option>
                                <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                                <option value="project-manager" {{ old('role') === 'project-manager' ? 'selected' : '' }}>Project Manager</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2 text-red-600 text-sm" />
                        </div>

                        <div>
                            <x-input-label for="password" value="Password" class="text-slate-700 font-semibold text-base" />
                            <x-text-input
                                id="password"
                                class="block mt-2 w-full bg-white border-slate-300 text-slate-900 placeholder-slate-400 focus:border-sky-500 focus:ring-sky-500 rounded-2xl py-3.5 px-5 text-base shadow-sm"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="At least 8 characters"
                            />
                            <p class="mt-1.5 text-xs text-slate-500">You'll use this password to sign in once your registration is approved.</p>
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 text-sm" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" value="Confirm Password" class="text-slate-700 font-semibold text-base" />
                            <x-text-input
                                id="password_confirmation"
                                class="block mt-2 w-full bg-white border-slate-300 text-slate-900 placeholder-slate-400 focus:border-sky-500 focus:ring-sky-500 rounded-2xl py-3.5 px-5 text-base shadow-sm"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Re-enter your password"
                            />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-600 text-sm" />
                        </div>

                        <div>
                            <x-primary-button class="w-full py-4 text-lg font-bold rounded-2xl bg-gradient-to-r from-sky-600 to-sky-500 hover:from-sky-500 hover:to-sky-400 shadow-lg shadow-sky-500/25 hover:shadow-xl hover:shadow-sky-500/30 active:scale-[0.985] transition-all duration-200">
                                Submit Registration
                            </x-primary-button>
                        </div>
                    </form>

                    <script>
                        (function () {
                            const select = document.getElementById('project_id');
                            const customField = document.getElementById('custom_project_field');
                            const customInput = document.getElementById('project_name');

                            function toggleCustomField() {
                                const isCustom = select.value === 'other';
                                customField.classList.toggle('hidden', !isCustom);
                                customInput.required = isCustom;
                                if (!isCustom) {
                                    customInput.value = '';
                                }
                            }

                            select.addEventListener('change', toggleCustomField);
                            toggleCustomField();
                        })();
                    </script>
                </div>

                <div class="bg-slate-50 border-t border-slate-200 px-10 py-5 text-center">
                    <p class="text-sm text-slate-600">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-semibold text-sky-600 hover:text-sky-700">Sign in</a>
                    </p>
                </div>
            </div>

            <div class="mt-8 text-center text-sm text-slate-500">
                © {{ date('Y') }} EEC Travel • All Rights Reserved
            </div>
        </div>
    </div>
</x-auth-layout>
