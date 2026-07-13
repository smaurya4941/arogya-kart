@php
    // $action = route name for the current report; $pdfRoute = optional PDF export route name.
    $action = $action ?? null;
    $pdfRoute = $pdfRoute ?? null;
    $from = request('from', optional($start ?? null)->toDateString());
    $to = request('to', optional($end ?? null)->toDateString());
@endphp

<form method="GET" action="{{ route($action) }}" class="card card-pad">
    <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
        <div>
            <label class="form-label">From</label>
            <input type="date" name="from" value="{{ $from }}" class="form-input">
        </div>
        <div>
            <label class="form-label">To</label>
            <input type="date" name="to" value="{{ $to }}" class="form-input">
        </div>
        <div class="flex items-end gap-2">
            <button class="btn btn-primary btn-sm">Apply</button>
            <a href="{{ route($action) }}" class="btn btn-outline btn-sm">Reset</a>
        </div>
        @if ($pdfRoute)
            <div class="flex items-end justify-end">
                <a href="{{ route($pdfRoute, ['from' => $from, 'to' => $to]) }}" class="btn btn-outline">
                    <span class="material-symbols-outlined text-[18px]">download</span> Download PDF
                </a>
            </div>
        @endif
    </div>
</form>
