@php
    $input_id = substr(str_shuffle(str_repeat($x='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);
@endphp
<div class="from-group mb-3 col-md-{{$width}}">
   <div class="mb-1">
       @if($label)
           <label for="{{ $input_id }}" class="form-label">{{ $label }}</label>
       @endif
       <input class="form-control" type="file" id="{{ $input_id }}" name="{{ $id }}">
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