<select x-data @change="changeOnSelect" name="{{ $name }}">
    @foreach($options as $value => $label)
        <option value="{{ $value }}" {{ $value == $default ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
</select>
