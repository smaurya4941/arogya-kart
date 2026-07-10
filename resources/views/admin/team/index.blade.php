@extends('layouts.admin')

@section('title', 'Team')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Team</h1>
            <p class="text-sm text-gray-600">
                Manage the people who work in your pharmacy.
                @if(!is_null($seatLimit))
                    <span class="ml-1 font-medium text-gray-800">{{ $seatsUsed }} / {{ $seatLimit }} seats used.</span>
                @endif
            </p>
        </div>
        @if($canAdd)
            <a href="{{ route('admin.team.create') }}" class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">Add Member</a>
        @else
            <a href="{{ route('admin.subscription.index') }}" class="bg-amber-500 text-white px-4 py-2 rounded hover:bg-amber-600" title="Plan seat limit reached">Upgrade to add more</a>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-3 font-semibold">Name</th>
                    <th class="px-5 py-3 font-semibold">Position</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($members as $member)
                    @php $isOwner = $member->isAdmin(); $isSelf = $member->id === auth()->id(); @endphp
                    <tr>
                        <td class="px-5 py-3">
                            <div class="font-medium text-gray-900">{{ $member->name }} @if($isSelf)<span class="text-xs text-gray-400">(you)</span>@endif</div>
                            <div class="text-xs text-gray-400">{{ $member->email }}</div>
                        </td>
                        <td class="px-5 py-3">
                            @if($isOwner)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-indigo-100 text-indigo-700">Owner</span>
                            @else
                                {{ $member->roles->pluck('name')->join(', ') ?: '—' }}
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs {{ $member->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                {{ ucfirst($member->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            @if($isOwner || $isSelf)
                                <span class="text-xs text-gray-300 float-right">—</span>
                            @else
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.team.edit', $member) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium hover:bg-gray-200">Edit</a>
                                    <form method="POST" action="{{ route('admin.team.toggle-status', $member) }}">
                                        @csrf @method('PATCH')
                                        <button class="px-3 py-1.5 rounded-lg text-xs font-medium {{ $member->status === 'active' ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                                            {{ $member->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.team.destroy', $member) }}" onsubmit="return confirm('Remove {{ $member->name }} from your team?')">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100">Remove</button>
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
@endsection
