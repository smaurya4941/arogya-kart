@extends('layouts.superadmin')

@section('title', 'Pharmacies')

@section('content')
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <form method="GET" class="flex flex-wrap gap-3 mb-5">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, email, owner…"
                   class="flex-1 min-w-[200px] rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
            <select name="status" class="rounded-lg border-gray-300 text-sm">
                <option value="">All statuses</option>
                <option value="active" @selected(request('status')==='active')>Active</option>
                <option value="suspended" @selected(request('status')==='suspended')>Suspended</option>
            </select>
            <button class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-left text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="py-2 pr-4">Pharmacy</th>
                        <th class="py-2 pr-4">Owner</th>
                        <th class="py-2 pr-4">Plan</th>
                        <th class="py-2 pr-4">Users</th>
                        <th class="py-2 pr-4">Status</th>
                        <th class="py-2 pr-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pharmacies as $pharmacy)
                        <tr>
                            <td class="py-3 pr-4">
                                <a href="{{ route('superadmin.pharmacies.show', $pharmacy) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $pharmacy->name }}</a>
                                <div class="text-xs text-gray-400">{{ $pharmacy->email }}</div>
                            </td>
                            <td class="py-3 pr-4 text-gray-600">{{ $pharmacy->owner_name }}</td>
                            <td class="py-3 pr-4">{{ $pharmacy->currentSubscription?->plan?->name ?? '—' }}</td>
                            <td class="py-3 pr-4 text-gray-600">{{ $pharmacy->users_count }}</td>
                            <td class="py-3 pr-4">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs {{ $pharmacy->isActive() ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($pharmacy->status) }}</span>
                            </td>
                            <td class="py-3 pr-4 text-right">
                                <form method="POST" action="{{ route('superadmin.pharmacies.toggle-status', $pharmacy) }}" class="inline"
                                      onsubmit="return confirm('{{ $pharmacy->isActive() ? 'Suspend' : 'Reactivate' }} this pharmacy?')">
                                    @csrf @method('PATCH')
                                    <button class="px-3 py-1.5 rounded-lg text-xs font-medium {{ $pharmacy->isActive() ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                                        {{ $pharmacy->isActive() ? 'Suspend' : 'Reactivate' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-gray-400">No pharmacies found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $pharmacies->links() }}</div>
    </div>
@endsection
