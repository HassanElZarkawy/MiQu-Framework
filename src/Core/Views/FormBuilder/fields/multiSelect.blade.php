@php
    $select_id = substr(str_shuffle(str_repeat($x='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);
@endphp
<div class="form-group mb-3 col-md-{{$width}}">
    @if ($label)
        <label for="{{$select_id}}">{{ $label }}</label>
    @endif
    <select id="{{$select_id}}" name="{{$id}}[]" class="form-control {{ $classes }}" {{ $required ? 'required' : null }} multiple>
        @foreach($options as $key => $text)
            <option value="{{$key}}" {{ $key === $value ? 'selected' : null }}>{{$text}}</option>
        @endforeach
    </select>
    @if($helpText)
        <small class="form-text text-muted">{{ $helpText }}</small>
    @endif
</div>
<script>
  $(document).ready(() => {
    $('#{{$select_id}}').multiselect({
      includeSelectAllOption: true,
      buttonClass: 'd-block form-control',
      buttonWidth: '100%',
      widthSynchronizationMode: 'always',
    });
  });
</script>