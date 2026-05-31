{{-- LM01 Pendidikan / Education --}}
<select name="{{ $name }}">
    <option value="">—</option>
    @foreach (['Tiada', 'Rendah', 'Menengah', 'Tertiari / Tinggi'] as $o)
        <option value="{{ $o }}">{{ $o }}</option>
    @endforeach
</select>
