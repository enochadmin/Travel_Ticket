{{-- Reusable user avatar button that opens an info popup on click --}}
@props(['user', 'size' => 'md'])
@php
    $sizes = [
        'sm' => 'w-7 h-7 text-[10px]',
        'md' => 'w-9 h-9 text-xs',
        'lg' => 'w-11 h-11 text-sm',
    ];
    $btnSize = $sizes[$size] ?? $sizes['md'];
    $palette = ['#6366f1', '#0ea5e9', '#f59e0b', '#ef4444', '#10b981', '#8b5cf6', '#ec4899', '#14b8a6'];
    $avatarColor = $palette[((int) $user->id) % count($palette)];
    $roleName = ucwords(str_replace('-', ' ', $user->roles->first()?->name ?? 'user'));
    $isOnline = $user->isOnline();
@endphp

<div class="relative inline-flex items-center gap-2 align-middle"
    x-data="{ open: false }" @click.outside="open = false" @scroll.window="open = false">

    <button type="button"
        @click.stop="open = !open; if (open) { $nextTick(() => { const btn = $refs.trigger.getBoundingClientRect(); const panel = $refs.panel; const pw = panel.offsetWidth; const gap = 8; let left = Math.min(Math.max(gap, btn.left + btn.width / 2 - pw / 2), window.innerWidth - pw - gap); let top = btn.bottom + gap; if (top + panel.offsetHeight > window.innerHeight - gap) { top = Math.max(gap, btn.top - panel.offsetHeight - gap); } panel.style.left = left + 'px'; panel.style.top = top + 'px'; }); }"
        x-ref="trigger"
        class="{{ $btnSize }} rounded-full overflow-hidden ring-2 ring-white dark:ring-slate-700 hover:ring-indigo-400 dark:hover:ring-sky-400 focus:outline-none transition flex-shrink-0 cursor-pointer"
        :aria-label="'View profile of {{ $user->name }}'"
        :title="'View profile of {{ $user->name }}'">
        @if($user->avatarUrl())
            <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
        @else
            <span class="w-full h-full flex items-center justify-center text-white font-bold"
                style="background:{{ $avatarColor }};">
                {{ $user->initials() }}
            </span>
        @endif
    </button>

    {{-- Info popup (fixed-positioned so tables/scroll containers never clip it) --}}
    <div x-show="open" x-cloak
        x-ref="panel"
        class="fixed z-[95] w-72 rounded-2xl bg-white shadow-2xl border border-gray-100 overflow-hidden"
        style="display:none;">
        <div class="px-5 py-5 text-center" style="background:linear-gradient(135deg,#0c2d44,#0d547a);">
            @if($user->avatarUrl())
                <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}"
                    class="w-16 h-16 rounded-full object-cover mx-auto ring-4 ring-white/20">
            @else
                <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-lg font-bold mx-auto ring-4 ring-white/20"
                    style="background:{{ $avatarColor }};">
                    {{ $user->initials() }}
                </div>
            @endif
            <p class="text-white font-bold mt-3 truncate">{{ $user->name }}</p>
            <p class="text-indigo-300 text-xs truncate">{{ $roleName }}</p>
        </div>

        <div class="px-5 py-4 space-y-3">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Full Name</p>
                <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Email</p>
                <p class="text-sm text-gray-700 mt-0.5 break-all">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Phone Number</p>
                <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $user->phone ?: 'Not set' }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Status</p>
                @if($isOnline)
                    <span class="inline-flex items-center gap-1.5 mt-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                        Online
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 mt-1 text-sm text-gray-500">
                        <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                        {{ $user->presenceLabel() }}
                    </span>
                @endif
            </div>
            @if($user->job_title || $user->project)
                <div class="pt-2 border-t border-gray-100 text-[11px] text-gray-400 space-y-0.5">
                    @if($user->job_title)<p>Job: {{ $user->job_title }}</p>@endif
                    @if($user->project)<p class="truncate">Project: {{ $user->project->name }}</p>@endif
                </div>
            @endif
        </div>
    </div>
</div>
