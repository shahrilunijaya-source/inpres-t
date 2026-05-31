{{-- Marriage Akta 164 = non-Muslim civil. Agama / Religion (no Islam). --}}
<select name="{{ $name }}">
    <option value="">—</option>
    @foreach (['Kristian', 'Buddha', 'Hindu', 'Tiada Agama', 'Lain-lain'] as $o)
        <option value="{{ $o }}">{{ $o }}</option>
    @endforeach
</select>
