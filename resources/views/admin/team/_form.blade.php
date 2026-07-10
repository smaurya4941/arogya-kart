@php
    $currentPosition = old('position', isset($member) ? $member->roles->pluck('name')->first() : null);
    $isEdit = isset($member);
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Full name</label>
        <input type="text" name="name" value="{{ old('name', $member->name ?? '') }}" required
               class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $member->email ?? '') }}" required
               class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-gray-400">(optional)</span></label>
        <input type="text" name="phone" value="{{ old('phone', $member->phone ?? '') }}"
               class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
        <select name="position" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
            <option value="">Select a position…</option>
            @foreach($positions as $position)
                <option value="{{ $position }}" @selected($currentPosition === $position)>{{ $position }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Password @if($isEdit)<span class="text-gray-400">(leave blank to keep)</span>@endif
        </label>
        <input type="password" name="password" {{ $isEdit ? '' : 'required' }} autocomplete="new-password"
               class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm password</label>
        <input type="password" name="password_confirmation" autocomplete="new-password"
               class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button class="px-5 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700">
        {{ $isEdit ? 'Save changes' : 'Add member' }}
    </button>
    <a href="{{ route('admin.team.index') }}" class="px-5 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium hover:bg-gray-200">Cancel</a>
</div>
