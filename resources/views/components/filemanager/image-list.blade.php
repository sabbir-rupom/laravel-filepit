@if ($attachments->isEmpty())
  <div class="row row-cols-md-6 row-cols-sm-3 g-4 w-100 h-100">
    <div class="d-flex justify-content-center align-items-center w-100">
      <div class="text-center text-dark">
        <h3>No files found</h3>
      </div>
    </div>
  </div>
@else
  <div class="row row-cols-md-6 row-cols-sm-3 g-4 w-100 ">
    @foreach ($attachments as $attachment)
      <div class="col pe-auto attachment-div">
        <div class="card attachment-card attachement-card-{{ $attachment->id }}" title="{{ $attachment->name }}"
          data-value="{{ $attachment->id }}" data-fileurl="{{ $attachment->url }}" data-type="{{ $attachment->type }}">
          <img src="{{ $attachment->image }}" class="card-img-top p-2" height="130" width="190"
            alt="...">
          <div class="card-body text-start ps-2 pe-2 pt-0 pb-0">
            <h6 class="d-flex mb-0">
              <span class="text-truncate title text-dark">{{ $attachment->name }}</span>
              {{-- <span class="text flex-shrink-0 text-dark">.{{ $attachment->extention }}</span> --}}
            </h6>
            <p class="mb-0 text-muted fs-6">{{ $attachment->formatted_file_size }}</p>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endif
