<!doctype html>
<html lang="en" class="h-100">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta name="generator" content="Hugo 0.84.0">
  <title>File Upload</title>
  <!-- Bootstrap core CSS -->
  <link href="{{ asset('assets/libs/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('css/style.css') }}" rel="stylesheet">
  <link href="{{ asset('css/filemanager.css') }}" rel="stylesheet">
  <script>
    const baseUrl = '{{ url('/') }}';
  </script>
</head>

<body>
  <div class="d-flex h-100 flex-column justify-content-center p-5">
    @isset($formData)
      <code class="p-4">
        <pre>{!! print_r($formData, true) !!}</pre>
      </code>
    @endisset
    <form action="" method="POST">
      @csrf
      <div class="mb-3">
        <label class="fw-bold mb-2">Multiple Image(s)</label class="fw-bold mb-2">
        <x-filemanager.input-group inputName="inputMultiple" multiple="1" type="image" />
      </div>

      <div class="mb-3">
        <label class="fw-bold mb-2">Single Image</label class="fw-bold mb-2">
        <x-filemanager.input-group inputName="inputSingle" multiple="0" type="image" />
      </div>

      <div class="mb-3">
        <label class="fw-bold mb-2">Document File</label class="fw-bold mb-2">
        <x-filemanager.input-group inputName="inputDoc" multiple="0" type="document" />
      </div>

      <button type="submit" class="btn btn-lg btn-primary">Submit</button>
    </form>
  </div>

  <x-filemanager.modal />

  <script src="{{ asset('assets/libs/bootstrap/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('js/filemanager.js') }}"></script>
</body>

</html>
