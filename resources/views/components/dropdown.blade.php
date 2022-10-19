<select x-data @change="changeOnSelect" name="{{ $name }}" aria-label="{{ $label }}">
    @foreach($options as $value => $text)
        <option value="{{ $value }}" {{ $value == $default ? 'selected' : '' }}>{{ $text }}</option>
    @endforeach
</select>
