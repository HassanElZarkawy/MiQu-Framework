@php
    $input_id = substr(str_shuffle(str_repeat($x='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);
@endphp
<div class="from-group mb-3 col-md-{{$width}}">
    @if($label)
        <label for="{{$input_id}}">{{$label}}</label>
    @endif
    <div class="custom-file">
        <input name="{{$id}}" id="{{$input_id}}" type="file" class="custom-file-input">
        <label class="custom-file-label" for="{{$input_id}}">Click to upload</label>
    </div>
</div>

<script>
  $('#{{$input_id}}').on('change',function(e) {
    if (e.target.files.length < 1) {
      return;
    }
    const fileName = e.target.files[0].name;
    $(this).next('.custom-file-label').html(fileName);
  })
</script>