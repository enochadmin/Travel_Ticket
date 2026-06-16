<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 via-white to-indigo-50/40 px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Set your new password</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Your account uses a temporary password. Choose a personal password to continue.
                </p>
            </div>

            <form method="POST" action="{{ route('password.change.store') }}"
                class="space-y-5 bg-white p-8 rounded-2xl shadow-xl border border-gray-200/70">
                @csrf

                <div>
                    <x-input-label for="password" value="New password" />
                    <x-text-input id="password" class="block mt-2 w-full" type="password" name="password" required autofocus />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Confirm new password" />
                    <x-text-input id="password_confirmation" class="block mt-2 w-full" type="password"
                        name="password_confirmation" required />
                </div>

                <button type="submit"
                    class="w-full py-3 rounded-xl text-white font-semibold transition"
                    style="background:linear-gradient(135deg,#4f46e5,#6366f1);">
                    Save and continue
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
