@extends('layouts.admin')

@section('title', 'Suppliers')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Suppliers</h1>
            <p class="page-subtitle">Manage the vendors you purchase stock from.</p>
        </div>
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
            <span class="material-symbols-outlined text-[18px]">add</span> Add Supplier
        </a>
    </div>

    <form method="GET" action="{{ route('admin.suppliers.index') }}" class="card card-pad">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
            <div class="md:col-span-2">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Name, company, phone or GST" class="form-input">
            </div>
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button class="btn btn-primary btn-sm">Apply</button>
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline btn-sm">Reset</a>
            </div>
        </div>
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Phone</th>
                        <th>GST</th>
                        <th>Purchases</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td class="font-medium">{{ $supplier->name }}</td>
                            <td class="text-on-surface-variant">{{ $supplier->company_name ?? '-' }}</td>
                            <td class="text-on-surface-variant">{{ $supplier->phone ?? '-' }}</td>
                            <td class="text-on-surface-variant">{{ $supplier->gst_number ?? '-' }}</td>
                            <td>{{ $supplier->purchase_invoices_count }}</td>
                            <td>
                                @if($supplier->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-neutral">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a class="btn-icon" title="View" href="{{ route('admin.suppliers.show', $supplier) }}">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    <a class="btn-icon" title="Edit" href="{{ route('admin.suppliers.edit', $supplier) }}">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <span class="material-symbols-outlined text-[32px] opacity-40">local_shipping</span>
                                    No suppliers yet.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($suppliers->hasPages())
            <div class="card-footer">{{ $suppliers->links() }}</div>
        @endif
    </div>
</div>
@endsection
