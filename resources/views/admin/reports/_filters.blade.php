@php
    // $action = route name for the current report; $pdfRoute = optional PDF export route name.
    $action = $action ?? null;
    $pdfRoute = $pdfRoute ?? null;
    $from = request('from', optional($start ?? null)->toDateString());
    $to = request('to', optional($end ?? null)->toDateString());
@endphp

<form method="GET" action="{{ route($action) }}" class="bg-white rounded shadow p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">From</label>
            <input type="date" name="from" value="{{ $from }}" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">To</label>
            <input type="date" name="to" value="{{ $to }}" class="w-full border rounded px-3 py-2">
        </div>
        <div class="flex items-end gap-3">
            <button class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">Apply</button>
            <a href="{{ route($action) }}" class="px-4 py-2 rounded border">Reset</a>
        </div>
        @if ($pdfRoute)
            <div class="flex items-end justify-end">
                <a href="{{ route($pdfRoute, ['from' => $from, 'to' => $to]) }}"
                   class="inline-flex items-center gap-2 bg-slate-900 text-white px-4 py-2 rounded hover:bg-slate-800">
                    Download PDF
                </a>
            </div>
        @endif
    </div>
</form>
