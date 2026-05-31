{{-- LM01 Keturunan / Race --}}
<select name="{{ $name }}">
    <option value="">—</option>
    @foreach (['Melayu', 'Cina', 'India', 'Bumiputera Sabah', 'Bumiputera Sarawak', 'Lain-lain'] as $o)
        <option value="{{ $o }}">{{ $o }}</option>
    @endforeach
</select>
