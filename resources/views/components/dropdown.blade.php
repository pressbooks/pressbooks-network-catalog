<div class="dropdown {{$dropdown_class??''}}">
    <label>{{$options_prefix??''}}</label>
    <select x-data @change="changeOnSelect" name="{{ $name }}" aria-label="{{ $label }}">
        @foreach($options as $value => $text)
            <option
                value="{{ $value }}" {{ $value == $default ? 'selected' : '' }}>{{ $options_prefix ?? '' }} {{ $text }}</option>
        @endforeach
    </select>
</div>
