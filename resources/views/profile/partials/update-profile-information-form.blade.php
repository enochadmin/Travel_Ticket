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

    {{-- Crop modal --}}
    <div id="avatar-crop-modal"
        class="hidden fixed inset-0 z-[95] items-center justify-center p-4 bg-black/70">
        <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-800">Crop profile picture</h3>
                <p class="text-xs text-gray-400">Drag to position · zoom to fit</p>
            </div>

            <div class="p-5">
                <div id="crop-stage"
                    class="relative mx-auto rounded-2xl overflow-hidden bg-slate-200 select-none touch-none cursor-move"
                    style="width:min(100%, 340px); aspect-ratio:1/1;">
                    <img id="crop-image" alt="Crop preview" draggable="false"
                        class="absolute top-0 left-0 max-w-none will-change-transform"
                        style="transform-origin: 0 0;">
                </div>

                <div class="mt-4 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 8h-6m6 4H9" />
                    </svg>
                    <input id="crop-zoom" type="range" min="1" max="3" step="0.01" value="1"
                        class="flex-1 accent-indigo-600">
                    <button type="button" id="crop-reset"
                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition flex-shrink-0">
                        Reset
                    </button>
                </div>
            </div>

            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-2">
                <button type="button" id="crop-cancel"
                    class="px-4 py-2 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-100 transition">
                    Cancel
                </button>
                <button type="button" id="crop-apply"
                    class="px-5 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition">
                    Apply &amp; upload
                </button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const input = document.getElementById('avatar-input');
            const preview = document.getElementById('avatar-preview');
            if (!input || !preview) return;

            const modal = document.getElementById('avatar-crop-modal');
            const stage = document.getElementById('crop-stage');
            const image = document.getElementById('crop-image');
            const zoomEl = document.getElementById('crop-zoom');
            const resetBtn = document.getElementById('crop-reset');
            const cancelBtn = document.getElementById('crop-cancel');
            const applyBtn = document.getElementById('crop-apply');

            if (!modal || !stage || !image) return;

            let file = null;        // the chosen file
            let url = null;         // object URL for the preview
            let naturalW = 0, naturalH = 0;
            let baseScale = 1;      // cover-fit scale of the natural image
            let zoom = 1;           // user zoom multiplier
            let tx = 0, ty = 0;     // translation (CSS px)
            let dragging = false, dragStartX = 0, dragStartY = 0, dragFromX = 0, dragFromY = 0;
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            function showModal() {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function hideModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                if (url) { URL.revokeObjectURL(url); url = null; }
            }

            function applyTransform() {
                const s = baseScale * zoom;
                image.style.transformOrigin = '0 0';
                image.style.transform = 'translate(' + tx + 'px, ' + ty + 'px) scale(' + s + ')';
            }

            // Re-position so the image always covers the square stage.
            function clampTranslation() {
                const size = stage.clientWidth;
                const s = baseScale * zoom;
                const overX = (naturalW * s - size) / 2;
                const overY = (naturalH * s - size) / 2;
                tx = Math.min(overX, Math.max(-overX, tx));
                ty = Math.min(overY, Math.max(-overY, ty));
            }

            function initView() {
                const size = stage.clientWidth;
                baseScale = Math.max(size / naturalW, size / naturalH);
                zoom = 1;
                zoomEl.value = 1;
                tx = (size - naturalW * baseScale) / 2;
                ty = (size - naturalH * baseScale) / 2;
                image.style.width = naturalW + 'px';
                image.style.height = naturalH + 'px';
                applyTransform();
            }

            function cropAndApply() {
                const size = stage.clientWidth;
                const s = baseScale * zoom;

                // Visible region of the image, in natural pixels.
                const srcX = Math.max(0, -tx / s);
                const srcY = Math.max(0, -ty / s);
                const srcSize = Math.min(size / s, naturalW - srcX, naturalH - srcY);

                canvas.width = 512;
                canvas.height = 512;
                ctx.clearRect(0, 0, 512, 512);
                ctx.drawImage(image, srcX, srcY, srcSize, srcSize, 0, 0, 512, 512);

                canvas.toBlob(function (blob) {
                    if (!blob) return;
                    const cropped = new File([blob], 'avatar-cropped.jpg', { type: 'image/jpeg' });
                    const dt = new DataTransfer();
                    dt.items.add(cropped);
                    input.files = dt.files;

                    const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
                    const img = document.createElement('img');
                    img.src = dataUrl;
                    img.alt = 'New profile picture';
                    img.className = 'w-full h-full object-cover';
                    preview.innerHTML = '';
                    preview.appendChild(img);

                    hideModal();
                }, 'image/jpeg', 0.92);
            }

            input.addEventListener('change', function () {
                file = input.files && input.files[0];
                if (!file) return;

                if (url) URL.revokeObjectURL(url);
                url = URL.createObjectURL(file);
                image.onload = function () {
                    naturalW = image.naturalWidth;
                    naturalH = image.naturalHeight;
                    initView();
                    showModal();
                };
                image.src = url;
            });

            zoomEl.addEventListener('input', function () {
                // Keep the centre of the image steady while zooming.
                const size = stage.clientWidth;
                const oldS = baseScale * zoom;
                const centreX = tx + (naturalW * oldS) / 2;
                const centreY = ty + (naturalH * oldS) / 2;
                zoom = parseFloat(zoomEl.value) || 1;
                const newS = baseScale * zoom;
                tx = centreX - (naturalW * newS) / 2;
                ty = centreY - (naturalH * newS) / 2;
                clampTranslation();
                applyTransform();
            });

            resetBtn.addEventListener('click', initView);

            cancelBtn.addEventListener('click', function () {
                input.value = ''; // never upload a file the user cancelled
                hideModal();
            });

            applyBtn.addEventListener('click', function () {
                if (naturalW > 0) { cropAndApply(); }
            });

            // Drag to position
            stage.addEventListener('pointerdown', function (e) {
                if (!modal.classList.contains('flex')) return;
                dragging = true;
                dragStartX = e.clientX;
                dragStartY = e.clientY;
                dragFromX = tx;
                dragFromY = ty;
                stage.setPointerCapture(e.pointerId);
            });

            stage.addEventListener('pointermove', function (e) {
                if (!dragging) return;
                tx = dragFromX + (e.clientX - dragStartX);
                ty = dragFromY + (e.clientY - dragStartY);
                clampTranslation();
                applyTransform();
            });

            ['pointerup', 'pointercancel'].forEach(function (evt) {
                stage.addEventListener(evt, function () { dragging = false; });
            });
        })();
    </script>
</section>
