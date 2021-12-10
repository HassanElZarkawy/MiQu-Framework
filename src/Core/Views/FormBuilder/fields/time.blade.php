<div class="form-group mb-3 col-md-{{$width}}">
    @if ($label)
        <label for="{{$id}}">{{ $label }}</label>
    @endif
    <input
        id="{{$id}}"
        name="{{$id}}"
        type="time"
        class="form-control {{ $classes }}"
        @if ($placeholder)
            placeholder="{{ $placeholder }}"
        @endif
        @if ($value)
            value="{{$value}}"
        @endif
        {{ $required ? 'required' : null }}
    >
    @if($helpText)
        <small class="form-text text-muted">{{ $helpText }}</small>
    @endif
</div>
