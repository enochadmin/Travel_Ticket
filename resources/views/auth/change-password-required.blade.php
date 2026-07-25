<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-gradient-to-br from-slate-50 via-sky-50/40 to-slate-100 px-4 py-8 sm:py-12">

        <!-- Subtle decorative background elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-sky-200/20 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 w-80 h-80 rounded-full bg-indigo-200/15 blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full bg-sky-100/30 blur-3xl opacity-60"></div>
        </div>

        <div class="relative w-full max-w-md sm:max-w-lg z-10">

            <!-- Header -->
            <div class="text-center mb-8 sm:mb-10">
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 mb-2 sm:mb-3">
                    Set your new password
                </h2>
                <p class="text-slate-600 text-sm sm:text-base max-w-sm mx-auto leading-relaxed">
                    Your account uses a temporary password. Choose a personal password to continue.
                </p>
            </div>

            <!-- Form Card -->
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl sm:rounded-3xl shadow-xl sm:shadow-2xl border border-slate-200/80 overflow-hidden">
                <div class="px-6 pt-8 pb-8 sm:px-10 sm:pt-10 sm:pb-12">
                    <form method="POST" action="{{ route('password.change.store') }}" class="space-y-5 sm:space-y-6">
                        @csrf

                        <!-- New Password -->
                        <div>
                            <x-input-label for="password" value="New password" class="text-slate-700 font-semibold text-sm sm:text-base" />
                            <div class="relative mt-2 sm:mt-3">
                                <x-text-input
                                    id="password"
                                    class="block w-full bg-white border-slate-300 text-slate-900 placeholder-slate-400 focus:border-sky-500 focus:ring-sky-500 rounded-xl sm:rounded-2xl py-3.5 sm:py-4 px-4 sm:px-5 pr-12 text-sm sm:text-base shadow-sm transition-all duration-200"
                                    type="password"
                                    name="password"
                                    required
                                    autofocus
                                    autocomplete="new-password"
                                />
                                <button
                                    type="button"
                                    onclick="togglePassword('password', this)"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600 transition-colors"
                                    aria-label="Show password"
                                >
                                    <!-- Eye icon (show) -->
                                    <svg class="eye-open h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <!-- Eye-off icon (hide) - hidden by default -->
                                    <svg class="eye-closed h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 text-sm" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <x-input-label for="password_confirmation" value="Confirm new password" class="text-slate-700 font-semibold text-sm sm:text-base" />
                            <div class="relative mt-2 sm:mt-3">
                                <x-text-input
                                    id="password_confirmation"
                                    class="block w-full bg-white border-slate-300 text-slate-900 placeholder-slate-400 focus:border-sky-500 focus:ring-sky-500 rounded-xl sm:rounded-2xl py-3.5 sm:py-4 px-4 sm:px-5 pr-12 text-sm sm:text-base shadow-sm transition-all duration-200"
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    autocomplete="new-password"
                                />
                                <button
                                    type="button"
                                    onclick="togglePassword('password_confirmation', this)"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600 transition-colors"
                                    aria-label="Show password"
                                >
                                    <!-- Eye icon (show) -->
                                    <svg class="eye-open h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <!-- Eye-off icon (hide) -->
                                    <svg class="eye-closed h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-600 text-sm" />
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit"
                                class="w-full py-3.5 sm:py-4 text-base sm:text-lg font-bold rounded-xl sm:rounded-2xl text-white
                                       bg-gradient-to-r from-sky-600 to-sky-500 hover:from-sky-500 hover:to-sky-400
                                       shadow-lg shadow-sky-500/25 hover:shadow-xl hover:shadow-sky-500/30
                                       active:scale-[0.98] transition-all duration-200">
                                Save and continue
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 sm:mt-10 text-center text-xs sm:text-sm text-slate-500">
                © {{ date('Y') }} EEC Travel • All Rights Reserved
            </div>
        </div>
    </div>

    <!-- Password toggle script -->
    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const eyeOpen = button.querySelector('.eye-open');
            const eyeClosed = button.querySelector('.eye-closed');

            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
                button.setAttribute('aria-label', 'Hide password');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
                button.setAttribute('aria-label', 'Show password');
            }
        }
    </script>
</x-guest-layout>