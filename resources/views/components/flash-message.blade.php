@php
    $messages = collect([
        [
            'key' => 'success',
            'classes' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
            'title' => 'Success',
        ],
        [
            'key' => 'error',
            'classes' => 'border-rose-200 bg-rose-50 text-rose-800',
            'title' => 'Error',
        ],
        [
            'key' => 'status',
            'classes' => 'border-sky-200 bg-sky-50 text-sky-800',
            'title' => 'Notice',
        ],
    ])->filter(fn ($message) => session()->has($message['key']));
@endphp

@foreach ($messages as $message)
    <div
        x-data="{ visible: true }"
        x-show="visible"
        x-transition.opacity.duration.300ms
        class="flex items-start justify-between gap-4 rounded-2xl border px-4 py-4 shadow-sm {{ $message['classes'] }}"
        role="alert"
    >
        <div>
            <p class="text-sm font-semibold">{{ $message['title'] }}</p>
            <p class="mt-1 text-sm">{{ session($message['key']) }}</p>
        </div>

        <button
            type="button"
            class="rounded-xl p-1.5 transition hover:bg-black/5"
            @click="visible = false"
            aria-label="Dismiss notification"
        >
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
@endforeach

@if ($errors->any())
    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-amber-900 shadow-sm" role="alert">
        <p class="text-sm font-semibold">Please review the highlighted fields.</p>
        <ul class="mt-2 space-y-1 text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
