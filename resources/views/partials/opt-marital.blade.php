{{-- LM01 Taraf Perkahwinan / Status of Marriage --}}
<select name="{{ $name }}">
    <option value="">—</option>
    @foreach (['Tidak Berkahwin', 'Berkahwin'] as $o)
        <option value="{{ $o }}">{{ $o }}</option>
    @endforeach
</select>
