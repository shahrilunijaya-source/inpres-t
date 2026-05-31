{{-- Taraf Perkahwinan / Marital Status (marriage applicant) --}}
<select name="{{ $name }}">
    <option value="">—</option>
    @foreach (['Bujang', 'Duda', 'Janda', 'Balu'] as $o)
        <option value="{{ $o }}">{{ $o }}</option>
    @endforeach
</select>
