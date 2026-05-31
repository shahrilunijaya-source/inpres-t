{{-- LM01 Taraf Pemastautin / Resident Status --}}
<select name="{{ $name }}">
    <option value="">—</option>
    @foreach (['Warganegara Malaysia', 'Pemastautin Tetap', 'Pemastautin Sementara', 'Bukan Warganegara Malaysia', 'Belum Ditentukan'] as $o)
        <option value="{{ $o }}">{{ $o }}</option>
    @endforeach
</select>
