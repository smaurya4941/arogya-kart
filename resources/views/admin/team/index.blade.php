@extends('layouts.admin')

@section('title', 'Team')

@section('content')
<div class="page mx-auto max-w-6xl">
    <div class="page-header">
        <div>
            <h1 class="page-title">Team</h1>
            <p class="page-subtitle">
                Manage the people who work in your pharmacy.
                @if(!is_null($seatLimit))
                    <span class="ml-1 font-medium text-on-surface">{{ $seatsUsed }} / {{ $seatLimit }} seats used.</span>
                @endif
            </p>
        </div>
        @if($canAdd)
            <a href="{{ route('admin.team.create') }}" class="btn btn-primary">
                <span class="material-symbols-outlined text-[18px]">person_add</span> Add Member
            </a>
        @else
            <a href="{{ route('admin.subscription.index') }}" class="btn btn-primary bg-amber-500 hover:bg-amber-600" title="Plan seat limit reached">
                <span class="material-symbols-outlined text-[18px]">upgrade</span> Upgrade to add more
            </a>
        @endif
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $member)
                        @php $isOwner = $member->isAdmin(); $isSelf = $member->id === auth()->id(); @endphp
                        <tr>
                            <td>
                                <div class="font-medium text-on-surface">{{ $member->name }} @if($isSelf)<span class="text-xs text-on-surface-variant">(you)</span>@endif</div>
                                <div class="text-xs text-on-surface-variant">{{ $member->email }}</div>
                            </td>
                            <td>
                                @if($isOwner)
                                    <span class="badge badge-info">Owner</span>
                                @else
                                    {{ $member->roles->pluck('name')->join(', ') ?: '—' }}
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $member->status === 'active' ? 'badge-success' : 'badge-neutral' }}">{{ ucfirst($member->status) }}</span>
                            </td>
                            <td>
                                @if($isOwner || $isSelf)
                                    <span class="float-right text-xs text-outline-variant">—</span>
                                @else
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.team.edit', $member) }}" class="btn btn-outline btn-xs">Edit</a>
                                        <form method="POST" action="{{ route('admin.team.toggle-status', $member) }}">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-xs {{ $member->status === 'active' ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25' }}">
                                                {{ $member->status === 'active' ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.team.destroy', $member) }}" onsubmit="return confirm('Remove {{ $member->name }} from your team?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs bg-error-container text-on-error-container hover:opacity-90">Remove</button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
