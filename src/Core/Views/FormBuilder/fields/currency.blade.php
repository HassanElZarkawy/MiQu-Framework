<div class="form-group mb-3 col-md-{{$width}}">
    @if ($label)
        <label for="{{$id}}">{{ $label }}</label>
    @endif
    <div class="input-group">
        <input
            id="{{$id}}"
            name="{{$id}}"
            type="number"
            class="form-control {{ $classes }}"
            @if ($placeholder)
                placeholder="{{ $placeholder }}"
            @endif
            @if ($value)
                value="{{$value}}"
            @endif
            {{ $required ? 'required' : null }}
        >
        <div class="input-group-append">
            <span class="input-group-text">{{$currency}}</span>
        </div>
    </div>
</div>