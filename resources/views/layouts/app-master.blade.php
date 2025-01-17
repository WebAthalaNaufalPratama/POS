<!doctype html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.87.0">
    <title>Fixed top navbar example · Bootstrap v5.1</title>

    <!-- Bootstrap core CSS -->
    <link href="{!! url('assets/bootstrap/css/bootstrap.min.css') !!}" rel="stylesheet">

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

      .float-right {
        float: right;
      }
  
/* Mengatur lebar scrollbar horizontal */
    .table-responsive::-webkit-scrollbar {
        height: 15px; /* Ukuran tinggi scrollbar horizontal */
    }

    /* Mengatur warna background track scrollbar */
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1; 
    }

    /* Mengatur warna dan bentuk scrollbar */
    .table-responsive::-webkit-scrollbar-thumb {
        background-color: #888; /* Warna scrollbar */
        border-radius: 10px; /* Membuat ujung scrollbar melengkung */
    }

    /* Mengubah warna scrollbar saat di-hover */
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background-color: #555; /* Warna scrollbar saat dihover */
    }


    </style>

    
    <!-- Custom styles for this template -->
    <link href="{!! url('assets/css/app.css') !!}" rel="stylesheet">
</head>
<body>
    
    @include('layouts.partials.navbar')

    <main class="container mt-5">
        @yield('content')
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{!! url('assets/bootstrap/js/bootstrap.bundle.min.js') !!}"></script>
    
    @section("scripts")

    @show
  </body>
</html>
