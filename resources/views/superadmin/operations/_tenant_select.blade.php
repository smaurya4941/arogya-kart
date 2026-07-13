<select name="pharmacy_id" class="form-select w-auto">
    <option value="">All pharmacies</option>
    @foreach($pharmacies as $pharmacy)
        <option value="{{ $pharmacy->id }}" @selected($selectedTenant === $pharmacy->id)>{{ $pharmacy->name }}</option>
    @endforeach
</select>
