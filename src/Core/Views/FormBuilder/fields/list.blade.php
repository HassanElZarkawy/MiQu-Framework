@php
    $list_id = substr(str_shuffle(str_repeat($x='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);
@endphp
<div class="form-group mb-3 col-md-{{$width}}">
    @if ($label)
        <label for="{{$id}}">{{ $label }}</label>
    @endif
    <input
        id="{{$id}}"
        name="{{$id}}"
        type="text"
        class="form-control {{ collect($classes)->join(' ') }}"
        @if ($placeholder)
            placeholder="{{ $placeholder }}"
        @endif
        {{ $required ? 'required' : null }}
        list="{{$list_id}}"
    >
    <datalist id="{{$list_id}}">
        @foreach($options as $key => $text)
            <option value="{{$key}}" {{ $key === $value ? 'selected' : null }}>{{$text}}</option>
        @endforeach
    </datalist>
    @if($helpText)
        <small class="form-text text-muted">{{ $helpText }}</small>
    @endif
</div>
