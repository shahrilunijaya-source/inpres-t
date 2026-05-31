{{-- LM01 Agama / Religion --}}
<select name="{{ $name }}">
    <option value="">—</option>
    @foreach (['Islam', 'Kristian', 'Buddha', 'Hindu', 'Lain-lain'] as $o)
        <option value="{{ $o }}">{{ $o }}</option>
    @endforeach
</select>
