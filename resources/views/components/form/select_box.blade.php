<select name='{{$name}}' {{$multiple ? 'multiple' : ''}} class="{{$label}}-select">
    <option value="" disabled selected>Choose {{$label}}(s)</option>
    @foreach($list as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>