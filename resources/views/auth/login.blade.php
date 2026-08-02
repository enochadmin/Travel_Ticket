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
                    Ethiopian Engineering Corporation
                </h1>
                <p class="text-slate-600 text-base sm:text-lg font-light">
                    Travel Requests & Approvals
                </p>
            </div>

            <x-auth-session-status class="mb-6 text-center text-emerald-600 text-sm font-medium" :status="session('status')" />

            <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 sm:px-10 pt-8 sm:pt-10 pb-10 sm:pb-12">
                    <form method="POST" action="{{ route('login') }}" class="space-y-6 sm:space-y-7">
                        @csrf

                        <div>
                            <x-input-label for="email" :value="__('Email address')" class="text-slate-700 font-semibold text-base" />
                            <x-text-input
                                id="email"
                                class="block mt-3 w-full bg-white border-slate-300 text-slate-900 placeholder-slate-400 focus:border-sky-500 focus:ring-sky-500 rounded-2xl py-3.5 sm:py-4 px-5 text-base shadow-sm transition-all duration-200"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                autofocus
                                autocomplete="username"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-sm" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Password')" class="text-slate-700 font-semibold text-base" />
                            <x-text-input
                                id="password"
                                class="block mt-3 w-full bg-white border-slate-300 text-slate-900 placeholder-slate-400 focus:border-sky-500 focus:ring-sky-500 rounded-2xl py-3.5 sm:py-4 px-5 text-base shadow-sm transition-all duration-200"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                            />
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 text-sm" />
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <label for="remember_me" class="flex items-center cursor-pointer group">
                                <input
                                    id="remember_me"
                                    type="checkbox"
                                    class="w-5 h-5 rounded border-slate-300 text-sky-600 focus:ring-sky-500 transition-colors"
                                    name="remember"
                                >
                                <span class="ml-3 text-sm sm:text-base text-slate-600 group-hover:text-slate-700 transition-colors">
                                    {{ __('Remember me') }}
                                </span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="text-sm sm:text-base font-medium text-sky-600 hover:text-sky-700 transition-colors">
                                    {{ __('Forgot password?') }}
                                </a>
                            @endif
                        </div>

                        <div>
                            <x-primary-button class="w-full py-4 text-base sm:text-lg font-bold rounded-2xl bg-gradient-to-r from-sky-600 to-sky-500 hover:from-sky-500 hover:to-sky-400 shadow-lg shadow-sky-500/25 hover:shadow-xl hover:shadow-sky-500/30 active:scale-[0.985] transition-all duration-200">
                                {{ __('Sign in') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>

                <div class="bg-slate-50 border-t border-slate-200 px-6 sm:px-10 py-4 sm:py-5 text-center space-y-2">
                    <p class="text-sm text-slate-600 font-medium">
                        Secure access for EEC team members
                    </p>
                    <p class="text-sm text-slate-500">
                        Need an account?
                        <a href="{{ route('register') }}" class="font-semibold text-sky-600 hover:text-sky-700">
                            Request access
                        </a>
                    </p>
                </div>
            </div>

            <div class="mt-8 text-center text-sm text-slate-500">
                © {{ date('Y') }} EEC Travel • All Rights Reserved
            </div>
        </div>
    </div>
</x-auth-layout>
