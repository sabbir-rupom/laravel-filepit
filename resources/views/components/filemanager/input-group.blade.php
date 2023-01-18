@props([
    'inputName' => 'inputFile',
    'multiple' => 0,
    'type' => 'image',
])

@php
  $fileUrl = $type === 'image' ? route('media.image.view') : route('media.image.view', ['id' => $type]);
@endphp

<div class="file-input-wrapper">
  <div class="input-group nxt-upload-button" data-modal="#nxtUploaderModal" data-type={!! $type !!}
    data-multiple={!! intval($multiple) > 0 ? 1 : 0 !!} data-fileurl={!! $fileUrl !!}>
    <span class="input-group-text">Browse</span>
    <div class="form-control file-amount">Choose File</div>
    <input type="hidden" name={{ $inputName }} class="selected-files">
  </div>

  <div class="previews"></div>
</div>
