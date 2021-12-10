<div class="form-group mb-3 col-md-{{$width}}">
    @if ($label)
        <label for="{{$id}}">{{ $label }}</label>
    @endif
    <select id="{{$id}}" name="{{$id}}" class="form-control {{ $classes }}" {{ $required ? 'required' : null }}>
        @if (!$required)
            <option value="">{{$assistText}}</option>
        @endif
        @foreach($options as $key => $text)
            <option value="{{$key}}" {{ $key === $value ? 'selected' : null }}>{{$text}}</option>
        @endforeach
    </select>
    @if($helpText)
        <small class="form-text text-muted">{{ $helpText }}</small>
    @endif
</div>
