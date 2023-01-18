<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Laravel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

</head>

<body>
  <form action="{{ route('upload') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="container mt-4">
      <div class="card">
        <div class="card-body">

          @if (Session::has('error'))
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
          @endif
          @if (Session::has('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
          @endif

          <h4>File Validate</h4>

          <div class="row mb-3">
            <div class="col-auto">
              <input type="text" class="form-control" name="prefix" placeholder="enter prefix here">
            </div>
            <div class="col-auto">
              <input type="text" class="form-control" name="path" placeholder="enter path here">
            </div>
            <div class="col-auto">
              <input type="number" min="1" class=" form-control" name="maxFileSize"
                placeholder="enter file Size in MB">
            </div>
            <div class="col-auto">
              <select class="form-select" name="allowedExtensions">
                <option value="*">Select All</option>
                <option value="jpg">jpg</option>
                <option value="jpeg">jpeg</option>
                <option value="png">png</option>
                <option value="pdf">pdf</option>
              </select>
            </div>
          </div>

          <h4>Upload File(s)</h4>
          <div class="row">
            <div class="col-auto">
              <input type="file" class="form-control" name="files[]" id="fileToUpload" multiple required>
            </div>
            <div class="col-auto">
              <button class="btn btn-primary" type="submit">Upload Now</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>

  @if ($files->count() > 0)
    <div class="container mt-4">
      <div class="row">
        @foreach ($files as $file)
          <div class="col-2">

            <div class="card p-3text-center">
              <img src="{{ $file->url }}" class="img-fluid" style="max-height: 150px" class="rounded" />
              <div class="d-flex justify-content-between my-3 px-2">
                <a class="btn btn-success" href="{{ route('image.view', $file->id) }}" target="_BLANK">View</a>
                <form action="{{ route('remove') }}" method="post">
                  @csrf
                  @method('delete')
                  <input type="hidden" name="attachment" value="{{ $file->id }}" />
                  <button type="submit" class="btn btn-danger ms-2 float-start">Remove</button>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      </div>
      <div class="row">
        <div class="col-12">
          <form action="{{ route('remove') }}" method="post">
            @csrf
            @method('delete')
            <input type="submit" class="btn btn-danger mt-5 btn-lg me-2" name="removeAll" value="Remove All">
            <a class="btn btn-secondary btn-lg mt-5" href="{{ url('media-manager') }}">Media Form</a>
          </form>
        </div>
      </div>
    </div>
  @endif
</body>

</html>
