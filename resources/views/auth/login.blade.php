<x-guest-layout>

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 via-white to-sky-50/40 px-4 sm:px-2 lg:px-2">
        <div class="w-full max-w-md">

            <!-- Logo / Brand -->
            <div class="text-center mb-10">
                <div class="flex justify-center items-center gap-3 mb-4">
                    <img src="{{ asset('images/eec-logo.png') }}" alt="EEC Logo" class="h-10 w-auto">
                    <div class="flex flex-col items-start leading-tight">
                        <span class="font-semibold text-2xl tracking-tighter text-gray-900">EEC</span>
                        <span class="text-sky-600 font-medium text-lg">TRAVEL</span>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
                    Sign in to your account
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Access your travel requests and approvals
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-6 text-center text-emerald-600 text-sm font-medium" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6 bg-white/80 backdrop-blur-sm p-8 rounded-2xl shadow-xl border border-gray-200/70">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-gray-700" />
                    <x-text-input
                        id="email"
                        class="block mt-2 w-full bg-white border-gray-300 text-gray-900 placeholder-gray-400 focus:border-sky-500 focus:ring-sky-500/30 rounded-xl shadow-sm transition-all"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-sm" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" class="text-gray-700" />
                    <x-text-input
                        id="password"
                        class="block mt-2 w-full bg-white border-gray-300 text-gray-900 placeholder-gray-400 focus:border-sky-500 focus:ring-sky-500/30 rounded-xl shadow-sm transition-all"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 text-sm" />
                </div>

                <!-- Remember Me + Forgot Password -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input
                            id="remember_me"
                            type="checkbox"
                            class="rounded border-gray-300 text-sky-600 focus:ring-sky-500/30"
                            name="remember"
                        >
                        <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-sky-600 hover:text-sky-800 font-medium transition-colors"
                           href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <div>
                    <x-primary-button class="w-full justify-center bg-gradient-to-r from-sky-600 to-sky-500 hover:from-sky-500 hover:to-sky-400 text-white font-semibold py-3 rounded-xl shadow-lg shadow-sky-500/20 hover:shadow-sky-600/30 transition-all duration-200">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>

            <!-- Optional register link -->
            @if (Route::has('register'))
                <p class="mt-8 text-center text-sm text-gray-600">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-sky-600 hover:text-sky-800 font-medium">
                        Create one now
                    </a>
                </p>
            @endif

        </div>
    </div>

</x-guest-layout>