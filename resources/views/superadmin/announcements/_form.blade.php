<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="form-label">Title</label>
        <input type="text" name="title" value="{{ old('title', $announcement->title) }}" required class="form-input">
    </div>
    <div class="md:col-span-2">
        <label class="form-label">Message</label>
        <textarea name="body" rows="3" required class="form-textarea">{{ old('body', $announcement->body) }}</textarea>
    </div>
    <div>
        <label class="form-label">Severity</label>
        <select name="level" class="form-select">
            @foreach($levels as $level)
                <option value="{{ $level }}" @selected(old('level', $announcement->level) === $level)>{{ ucfirst($level) }}</option>
            @endforeach
        </select>
    </div>
    <label class="flex items-end gap-2 pb-2 text-sm text-on-surface">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $announcement->is_active)) class="rounded border-outline-variant text-primary focus:ring-primary/30">
        Active
    </label>
    <div>
        <label class="form-label">Starts at (optional)</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($announcement->starts_at)->format('Y-m-d\TH:i')) }}" class="form-input">
    </div>
    <div>
        <label class="form-label">Ends at (optional)</label>
        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($announcement->ends_at)->format('Y-m-d\TH:i')) }}" class="form-input">
    </div>
</div>

<div class="mt-6 flex gap-2">
    <button class="btn btn-primary">{{ $announcement->exists ? 'Save changes' : 'Publish' }}</button>
    <a href="{{ route('superadmin.announcements.index') }}" class="btn btn-outline">Cancel</a>
</div>
