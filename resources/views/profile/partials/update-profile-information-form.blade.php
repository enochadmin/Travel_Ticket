<section>
    <header>
        <h2 class="text-lg font-bold text-gray-800">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            {{ __('Update your account profile — avatar, name, email and phone.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data"
        class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Avatar / Profile picture --}}
        <div class="flex items-center gap-5">
            <div id="avatar-preview" class="w-20 h-20 rounded-full flex-shrink-0 overflow-hidden ring-4 ring-indigo-50">
                @if($user->avatarUrl())
                    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-white text-2xl font-bold"
                        style="background:linear-gradient(135deg,#4f46e5,#6366f1);">
                        {{ $user->initials() }}
                    </div>
                @endif
            </div>

            <div class="flex flex-col gap-2">
                <label
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white cursor-pointer transition hover:opacity-90 self-start"
                    style="background:linear-gradient(135deg,#4f46e5,#6366f1);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ $user->avatarUrl() ? 'Change photo' : 'Upload photo' }}
                    <input id="avatar-input" type="file" name="avatar" accept="image/jpeg,image/png,image/webp"
                        class="sr-only">
                </label>
                <p class="text-xs text-gray-400">JPG, PNG or WebP — up to 2 MB.</p>
                <x-input-error class="mt-1" :messages="$errors->get('avatar')" />
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="phone" :value="__('Phone Number')" />
            <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="tel" placeholder="e.g. +251 91 234 5678" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save changes') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-sm font-medium text-green-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    <script>
        (function () {
            const input = document.getElementById('avatar-input');
            const preview = document.getElementById('avatar-preview');
            if (!input || !preview) return;

            input.addEventListener('change', function () {
                const file = input.files && input.files[0];
                if (!file) return;

                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = 'New profile picture';
                img.className = 'w-full h-full object-cover';
                preview.innerHTML = '';
                preview.appendChild(img);
            });
        })();
    </script>
</section>
