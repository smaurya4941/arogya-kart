@php
    $currentPosition = old('position', isset($member) ? $member->roles->pluck('name')->first() : null);
    $isEdit = isset($member);
@endphp

@if ($errors->any())
    <div class="mb-4 rounded-lg border border-error/30 bg-error-container/40 p-3 text-sm text-on-error-container">
        <ul class="list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <label class="form-label">Full name</label>
        <input type="text" name="name" value="{{ old('name', $member->name ?? '') }}" required class="form-input">
    </div>
    <div>
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email', $member->email ?? '') }}" required class="form-input">
    </div>
    <div>
        <label class="form-label">Phone <span class="text-outline">(optional)</span></label>
        <input type="text" name="phone" value="{{ old('phone', $member->phone ?? '') }}" class="form-input">
    </div>
    <div>
        <label class="form-label">Position</label>
        <select name="position" required class="form-select">
            <option value="">Select a position…</option>
            @foreach($positions as $position)
                <option value="{{ $position }}" @selected($currentPosition === $position)>{{ $position }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label">
            Password @if($isEdit)<span class="text-outline">(leave blank to keep)</span>@endif
        </label>
        <input type="password" name="password" {{ $isEdit ? '' : 'required' }} autocomplete="new-password" class="form-input">
    </div>
    <div>
        <label class="form-label">Confirm password</label>
        <input type="password" name="password_confirmation" autocomplete="new-password" class="form-input">
    </div>
</div>

<div class="mt-6 flex gap-2">
    <button class="btn btn-primary">{{ $isEdit ? 'Save changes' : 'Add member' }}</button>
    <a href="{{ route('admin.team.index') }}" class="btn btn-outline">Cancel</a>
</div>
