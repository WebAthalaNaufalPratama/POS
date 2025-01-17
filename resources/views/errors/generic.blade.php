<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
<meta name="robots" content="noindex, nofollow">
<title>POS Vonflorist - Error {{ $statusCode }}</title>

<link rel="shortcut icon" type="image/x-icon" href="/assets/img/favicon.jpg">
<link rel="stylesheet" href="/assets/css/bootstrap.min.css">
<link rel="stylesheet" href="/assets/css/animate.css">
<link rel="stylesheet" href="/assets/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="/assets/plugins/fontawesome/css/fontawesome.min.css">
<link rel="stylesheet" href="/assets/plugins/fontawesome/css/all.min.css">
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="error-page">
<div id="global-loader">
    <div class="whirly-loader"></div>
</div>

<div class="main-wrapper">
    <div class="error-box">
        <h1>{{ $statusCode }}</h1>
        <h3 class="h2 mb-3"><i class="fas fa-exclamation-circle"></i> Oops! {{ $message }}</h3>
        <a href="{{ route('dashboard.index') }}" class="btn btn-primary">Back to Home</a>
    </div>
</div>

<script src="/assets/js/jquery-3.6.0.min.js"></script>
<script src="/assets/js/feather.min.js"></script>
<script src="/assets/js/jquery.slimscroll.min.js"></script>
<script src="/assets/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/script.js"></script>
</body>
</html>