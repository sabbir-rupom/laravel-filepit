@foreach ($attachments as $attachment)
<div class="col pe-auto file-preview-item">
  <div class="card">
    <img src="{{ asset('storage/'.$attachment->file_path) }}" class="card-img-top" height="52" width="150" alt="...">
    <div class="card-body text-start p-0">
      <h6 class="d-flex mb-0">
        <span class="text-truncate title text-dark">{{ $attachment->file_name }}</span>
        <span class="text flex-shrink-0 text-dark">{{ $attachment->extention }}</span>
      </h6>
      <p class="mb-0 text-muted fs-6">{{ $attachment->formatted_file_size }}</p>
    </div>
  </div>
</div>
<button type="button" class="btn-close btn-close-white remove-attachment" data-value="{{ $attachment->id }}" aria-label="Close"></button>
@endforeach