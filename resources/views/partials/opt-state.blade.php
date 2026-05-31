{{-- Negeri Kelahiran / State of Birth --}}
<select name="{{ $name }}">
    <option value="">—</option>
    @foreach (['Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang', 'Perak', 'Perlis', 'Pulau Pinang', 'Sabah', 'Sarawak', 'Selangor', 'Terengganu', 'Kuala Lumpur', 'W.P. Kuala Lumpur', 'W.P. Labuan', 'W.P. Putrajaya', 'Luar Negara'] as $o)
        <option value="{{ $o }}">{{ $o }}</option>
    @endforeach
</select>
