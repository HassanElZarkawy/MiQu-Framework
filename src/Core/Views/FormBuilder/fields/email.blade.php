<div class="form-group mb-3 col-md-{{$width}}">
    @if ($label)
        <label for="{{$id}}">{{ $label }}</label>
    @endif
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">@</span>
        </div>
        <input
            id="{{$id}}"
            name="{{$id}}"
            type="email"
            class="form-control {{ collect($classes)->join(' ') }}"
            @if ($placeholder)
            placeholder="{{ $placeholder }}"
            @endif
            @if ($value)
            value="{{$value}}"
            @endif
            {{ $required ? 'required' : null }}
        >
    </div>
</div>