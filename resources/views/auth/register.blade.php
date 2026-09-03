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
                {{-- Success confirmation popup shown after the registration is submitted --}}
                <div
                    x-data="{ show: true }"
                    class="fixed inset-0 z-50 overflow-y-auto"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="registration-success-title"
                >
                    <div
                        x-show="show"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                        @click="show = false"
                    ></div>

                    <div class="min-h-full flex items-center justify-center p-4">
                        <div
                            x-show="show"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden"
                            @keydown.escape.window="show = false"
                        >
                            <button
                                type="button"
                                @click="show = false"
                                aria-label="Close confirmation"
                                class="absolute top-4 right-4 p-1.5 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <div class="px-6 sm:px-8 pt-10 sm:pt-12 pb-8 sm:pb-10 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100">
                                    <svg class="h-9 w-9 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </div>
                                <h2 id="registration-success-title" class="mt-5 text-xl sm:text-2xl font-bold text-slate-900">
                                    Registration Submitted Successfully
                                </h2>
                                <p class="mt-3 text-sm sm:text-base text-slate-600 leading-relaxed">
                                    {{ session('status') }}
                                </p>
                            </div>

                            <div class="bg-slate-50 border-t border-slate-200 px-6 sm:px-8 py-4 sm:py-5 flex flex-col-reverse sm:flex-row gap-3 sm:justify-center">
                                <button
                                    type="button"
                                    @click="show = false"
                                    class="inline-flex items-center justify-center px-5 py-3 rounded-2xl border border-slate-300 bg-white text-slate-600 text-sm font-semibold hover:bg-slate-100 transition"
                                >
                                    Close
                                </button>
                                <a
                                    href="{{ route('login') }}"
                                    class="inline-flex items-center justify-center px-5 py-3 rounded-2xl bg-gradient-to-r from-sky-600 to-sky-500 text-white text-sm font-semibold shadow-lg shadow-sky-500/25 hover:from-sky-500 hover:to-sky-400 transition"
                                >
                                    Back to Sign In
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-8 pt-8 pb-10 sm:px-10">
                    <form method="POST" action="{{ route('register') }}" class="space-y-5" data-prevent-double-submit data-submitting-text="Submitting...">
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
                                placeholder="e.g. yourname@company.com"
                            />
                            <p class="mt-1.5 text-xs text-slate-500">Must contain "@" and end with ".com" — addresses are accepted in lowercase.</p>
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

                    <script>
                        (function () {
                            document.querySelectorAll('form[data-prevent-double-submit]').forEach(function (form) {
                                form.addEventListener('submit', function (e) {
                                    if (form.dataset.submitting === 'true') {
                                        e.preventDefault();
                                        return;
                                    }
                                    form.dataset.submitting = 'true';
                                    var btn = form.querySelector('button[type="submit"]');
                                    if (btn) {
                                        btn.disabled = true;
                                        btn.dataset.originalHtml = btn.innerHTML;
                                        btn.innerHTML = form.getAttribute('data-submitting-text') || 'Saving...';
                                    }
                                });
                            });
                        })();
                    </script>

                    <script>
                        (function () {
                            const form = document.querySelector('form[data-prevent-double-submit]');
                            const emailInput = document.getElementById('email');
                            if (!form || !emailInput) {
                                return;
                            }

                            const emailPattern = /^[^\s@]+@[^\s@]+\.com$/;

                            function restoreSubmitButton() {
                                form.dataset.submitting = 'false';
                                const btn = form.querySelector('button[type="submit"]');
                                if (btn) {
                                    btn.disabled = false;
                                    if (btn.dataset.originalHtml) {
                                        btn.innerHTML = btn.dataset.originalHtml;
                                    }
                                }
                            }

                            // Show a clear message when the email field fails any check.
                            emailInput.addEventListener('invalid', function () {
                                if (emailInput.validity.valueMissing) {
                                    emailInput.setCustomValidity('Email address is required.');
                                } else if (!emailPattern.test(emailInput.value.trim().toLowerCase())) {
                                    emailInput.setCustomValidity('Email address must contain "@" and end with ".com".');
                                } else {
                                    emailInput.setCustomValidity('');
                                }
                            });

                            // Accept lowercase email addresses: convert as the user types
                            // so any casing is accepted and stored consistently.
                            emailInput.addEventListener('input', function () {
                                const start = emailInput.selectionStart;
                                const end = emailInput.selectionEnd;
                                emailInput.value = emailInput.value.toLowerCase().trim();
                                if (emailInput.setSelectionRange) {
                                    emailInput.setSelectionRange(start, end);
                                }
                                emailInput.setCustomValidity('');
                            });

                            // Guard for values like "a@b" that pass the browser's native
                            // email check but are missing ".com".
                            form.addEventListener('submit', function (e) {
                                const value = emailInput.value.trim().toLowerCase();
                                emailInput.value = value;
                                if (!emailPattern.test(value)) {
                                    e.preventDefault();
                                    emailInput.setCustomValidity('Email address must contain "@" and end with ".com".');
                                    emailInput.reportValidity();
                                    restoreSubmitButton();
                                }
                            });
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
