<div class="form-group mb-3 col-md-{{$width}}">
    @if ($label)
        <label for="{{$id}}">{{ $label }}</label>
    @endif
    <textarea id="{{$id}}" name="{{$id}}" class="form-control {{ $classes }}" {{ $required ? 'required' : null }}
        @if ($placeholder)
            placeholder="{{ $placeholder }}"
        @endif
        cols="{{$columns}}"
        rows="{{$rows}}"
    >{{$value}}</textarea>
    @if($helpText)
        <small class="form-text text-muted">{{ $helpText }}</small>
    @endif
</div>
