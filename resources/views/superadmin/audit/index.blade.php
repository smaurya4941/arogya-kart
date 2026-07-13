@extends('layouts.superadmin')

@section('title', 'Activity Log')

@section('content')
    <div class="card overflow-hidden">
        <div class="card-header flex-col items-stretch gap-3">
            <div class="flex items-center gap-2 text-sm">
                <a href="{{ route('superadmin.audit.index', ['filter' => 'impersonation']) }}"
                   class="btn btn-sm {{ $filter === 'impersonation' ? 'btn-primary' : 'btn-outline' }}">Impersonation</a>
                <a href="{{ route('superadmin.audit.index', ['filter' => 'all']) }}"
                   class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-outline' }}">All activity</a>
            </div>
            <form method="GET" class="flex w-full flex-wrap items-center gap-2">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <select name="user_id" class="form-select w-auto">
                    <option value="">All actors</option>
                    @foreach($actors as $actor)
                        <option value="{{ $actor->id }}" @selected((string) request('user_id') === (string) $actor->id)>{{ $actor->name }}</option>
                    @endforeach
                </select>
                <select name="action" class="form-select w-auto">
                    <option value="">All actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>{{ str_replace('_', ' ', $action) }}</option>
                    @endforeach
                </select>
                <input type="date" name="from" value="{{ request('from') }}" class="form-input w-auto" title="From">
                <input type="date" name="to" value="{{ request('to') }}" class="form-input w-auto" title="To">
                <button class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('superadmin.audit.export', request()->query()) }}" class="btn btn-outline btn-sm ml-auto">
                    <span class="material-symbols-outlined text-[18px]">download</span> Export CSV
                </a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>When</th>
                        <th>Actor</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="align-top">
                            <td class="whitespace-nowrap text-on-surface-variant">{{ $log->created_at->format('d M Y, H:i') }}</td>
                            <td>{{ $log->user?->name ?? 'System' }}</td>
                            <td>
                                <span class="badge {{ str_starts_with($log->action, 'impersonation') ? 'badge-info' : 'badge-neutral' }}">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </td>
                            <td class="text-on-surface-variant">
                                @if(is_array($log->meta))
                                    @foreach($log->meta as $k => $v)
                                        <span class="mr-3"><span class="text-outline">{{ str_replace('_',' ',$k) }}:</span> {{ is_scalar($v) ? $v : json_encode($v) }}</span>
                                    @endforeach
                                @endif
                            </td>
                            <td class="whitespace-nowrap text-outline">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state">No activity recorded.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="card-footer">{{ $logs->links() }}</div>
        @endif
    </div>
@endsection
