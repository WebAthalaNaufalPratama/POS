<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.87.0">
    <title>Signin Template · Bootstrap v5.1</title>

    <!-- Bootstrap core CSS -->
    <link href="{!! url('assets/bootstrap/css/bootstrap.min.css') !!}" rel="stylesheet">
    <link href="{!! url('assets/css/signin.css') !!}" rel="stylesheet">
    <link rel="stylesheet" href="{!! url('assets/plugins/font-awesome/css/font-awesome.min.css') !!}">
    <link rel="stylesheet" href="/assets/plugins/toastr/toatr.css" />
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    
    <!-- Custom styles for this template -->
    <!-- <link href="signin.css" rel="stylesheet"> -->
</head>
<body>
    <div class="wrapper">
      <div class="inner">
        <img src="/assets/img/image-1.png" class="image-1">
        <!-- <main class="form"> -->

        @yield('content')

        <!-- </main> -->
        <img src="/assets/img/image-2.png" class="image-2">
      </div>
    </div>
    <script src="/assets/js/jquery-3.6.0.min.js"></script>
    <script src="/assets/plugins/toastr/toastr.min.js"></script>
    <script>
      $(document).ready(function() {
        let sessionData = @json(session()->all());
        console.log(sessionData)
        @if(session('fail'))
        toastr.error(sessionData.fail, {
          closeButton: true,
          tapToDismiss: false,
          rtl: false,
          progressBar: true
        });
        @endif
        @if(session('success'))
        toastr.success(sessionData.success, {
          closeButton: true,
          tapToDismiss: false,
          rtl: false,
          progressBar: true
        });
        @endif
        @if(session('warning'))
        toastr.warning(sessionData.warning, {
          closeButton: true,
          tapToDismiss: false,
          rtl: false,
          progressBar: true
        });
        @endif
      });
    </script>
</body>
</html>
