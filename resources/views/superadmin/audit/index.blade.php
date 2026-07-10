@extends('layouts.superadmin')

@section('title', 'Activity Log')

@section('content')
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="flex items-center gap-2 mb-5 text-sm">
            <a href="{{ route('superadmin.audit.index', ['filter' => 'impersonation']) }}"
               class="px-3 py-1.5 rounded-lg font-medium {{ $filter === 'impersonation' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Impersonation
            </a>
            <a href="{{ route('superadmin.audit.index', ['filter' => 'all']) }}"
               class="px-3 py-1.5 rounded-lg font-medium {{ $filter === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                All activity
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-left text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="py-2 pr-4">When</th>
                        <th class="py-2 pr-4">Actor</th>
                        <th class="py-2 pr-4">Action</th>
                        <th class="py-2 pr-4">Details</th>
                        <th class="py-2 pr-4">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="align-top">
                            <td class="py-3 pr-4 text-gray-500 whitespace-nowrap">{{ $log->created_at->format('d M Y, H:i') }}</td>
                            <td class="py-3 pr-4">{{ $log->user?->name ?? 'System' }}</td>
                            <td class="py-3 pr-4">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs
                                    {{ str_starts_with($log->action, 'impersonation') ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </td>
                            <td class="py-3 pr-4 text-gray-600">
                                @if(is_array($log->meta))
                                    @foreach($log->meta as $k => $v)
                                        <span class="mr-3"><span class="text-gray-400">{{ str_replace('_',' ',$k) }}:</span> {{ is_scalar($v) ? $v : json_encode($v) }}</span>
                                    @endforeach
                                @endif
                            </td>
                            <td class="py-3 pr-4 text-gray-400 whitespace-nowrap">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-gray-400">No activity recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
@endsection
