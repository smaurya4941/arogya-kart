@php
    use App\Enums\UserRole;
    use App\Support\AdminCapability;
    $currentRole = old('role', $user->role?->value ?? $user->role);

    // Capability editor state — only a full platform owner may manage these.
    $canManageCaps = auth()->user()->isFullSuperAdmin();
    $isFullDefault = $user->exists ? $user->isFullSuperAdmin() : true;
    $currentCaps   = old('admin_capabilities', ($user->exists && ! $user->isFullSuperAdmin()) ? ($user->admin_capabilities ?? []) : []);
@endphp

<div x-data="{ role: '{{ $currentRole ?: UserRole::ADMIN->value }}', full: {{ $isFullDefault ? 'true' : 'false' }} }" class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="form-label">Full name</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-input">
    </div>
    <div>
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-input">
    </div>

    <div>
        <label class="form-label">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input">
    </div>
    <div>
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $user->status) === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label">Role</label>
        <select name="role" x-model="role" class="form-select">
            @foreach($roles as $role)
                <option value="{{ $role->value }}" @selected($currentRole === $role->value)>{{ $role->label() }}</option>
            @endforeach
        </select>
    </div>
    {{-- Pharmacy is only meaningful for tenant-bound roles; Super Admins sit above it. --}}
    <div x-show="role !== '{{ UserRole::SUPER_ADMIN->value }}'" x-cloak>
        <label class="form-label">Pharmacy</label>
        <select name="pharmacy_id" class="form-select">
            <option value="">— Select pharmacy —</option>
            @foreach($pharmacies as $pharmacy)
                <option value="{{ $pharmacy->id }}" @selected((string) old('pharmacy_id', $user->pharmacy_id) === (string) $pharmacy->id)>{{ $pharmacy->name }}</option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-on-surface-variant">Required for Pharmacy Owner, Staff and Customer accounts.</p>
    </div>

    {{-- Granular platform capabilities — shown for Super Admins, editable only by a full platform owner. --}}
    @if($canManageCaps)
        <div x-show="role === '{{ UserRole::SUPER_ADMIN->value }}'" x-cloak class="md:col-span-2">
            <label class="form-label">Platform access</label>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="admin_full" value="1" x-model="full" class="rounded border-outline-variant text-primary focus:ring-primary/30">
                Full access (all capabilities)
            </label>
            <div x-show="!full" x-cloak class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                @foreach(AdminCapability::catalogue() as $key => $meta)
                    <label class="flex items-start gap-2 rounded-lg border border-outline-variant/60 p-2 text-sm">
                        <input type="checkbox" name="admin_capabilities[]" value="{{ $key }}" @checked(in_array($key, $currentCaps)) class="mt-0.5 rounded border-outline-variant text-primary focus:ring-primary/30">
                        <span>
                            <span class="font-medium">{{ $meta['label'] }}</span>
                            <span class="block text-xs text-on-surface-variant">{{ $meta['description'] }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
            <p class="mt-1 text-xs text-on-surface-variant">Uncheck "Full access" to restrict this admin (e.g. a support operator) to specific sections only.</p>
        </div>
    @endif

    <div>
        <label class="form-label">Password {{ $user->exists ? '(leave blank to keep current)' : '' }}</label>
        <input type="password" name="password" autocomplete="new-password" @required(! $user->exists) class="form-input">
    </div>
    <div>
        <label class="form-label">Confirm password</label>
        <input type="password" name="password_confirmation" autocomplete="new-password" class="form-input">
    </div>
</div>

<div class="mt-6 flex gap-2">
    <button class="btn btn-primary">{{ $user->exists ? 'Save changes' : 'Create user' }}</button>
    <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline">Cancel</a>
</div>
