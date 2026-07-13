@extends('layouts.superadmin')

@section('title', 'System Health')

@section('content')
    {{-- Health checks --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach($checks as $check)
            <div class="card card-pad flex items-start gap-3">
                <span class="material-symbols-outlined text-[22px] {{ $check['ok'] ? 'text-tertiary' : 'text-error' }}">
                    {{ $check['ok'] ? 'check_circle' : 'error' }}
                </span>
                <div>
                    <p class="font-medium text-on-surface">{{ $check['label'] }}</p>
                    <p class="text-xs text-on-surface-variant">{{ $check['detail'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Queue + environment --}}
    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="card card-pad">
            <h2 class="section-title mb-4">Background queue</h2>
            <div class="flex gap-6">
                <div>
                    <p class="text-3xl font-bold text-primary">{{ $queue['pending'] }}</p>
                    <p class="text-xs text-on-surface-variant">Pending jobs</p>
                </div>
                <div>
                    <p class="text-3xl font-bold {{ $queue['failed'] > 0 ? 'text-error' : 'text-on-surface' }}">{{ $queue['failed'] }}</p>
                    <p class="text-xs text-on-surface-variant">Failed jobs</p>
                </div>
            </div>
            @if($queue['failed'] > 0)
                <div class="mt-4 flex gap-2">
                    <form method="POST" action="{{ route('superadmin.system.failed.retry') }}">
                        @csrf
                        <button class="btn btn-sm btn-outline">Retry all</button>
                    </form>
                    <form method="POST" action="{{ route('superadmin.system.failed.flush') }}" onsubmit="return confirm('Permanently clear the failed-jobs log?')">
                        @csrf
                        <button class="btn btn-sm bg-error-container text-on-error-container hover:opacity-90">Flush log</button>
                    </form>
                </div>
            @endif
        </div>

        <div class="card card-pad lg:col-span-2">
            <h2 class="section-title mb-4">Environment</h2>
            <dl class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm sm:grid-cols-3">
                @foreach($environment as $label => $value)
                    <div>
                        <dt class="text-xs text-on-surface-variant">{{ $label }}</dt>
                        <dd class="font-medium text-on-surface">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </div>

    {{-- Recent failures --}}
    <div class="card mt-4 overflow-hidden">
        <div class="card-header"><h2 class="section-title">Recent failed jobs</h2></div>
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr><th>Job</th><th>Queue</th><th>Failed at</th><th>Error</th></tr>
                </thead>
                <tbody>
                    @forelse($failedJobs as $job)
                        <tr>
                            <td class="font-medium">{{ $job->name }}</td>
                            <td class="text-on-surface-variant">{{ $job->queue }}</td>
                            <td class="text-on-surface-variant">{{ \Illuminate\Support\Carbon::parse($job->failed_at)->format('d M Y, H:i') }}</td>
                            <td class="max-w-md truncate text-xs text-error">{{ $job->error }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="empty-state">No failed jobs. 🎉</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
