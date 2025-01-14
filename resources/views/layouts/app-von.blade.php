<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="description" content="POS - Bootstrap Admin Template">
<meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern,  html5, responsive">
<meta name="author" content="Dreamguys - Bootstrap Admin Template">
<meta name="robots" content="noindex, nofollow">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Vonflorist</title>

  <link rel="shortcut icon" type="image/x-icon" href="/assets/img/favicon.jpg">

  <link rel="stylesheet" href="/assets/css/bootstrap.min.css">

  <link rel="stylesheet" href="/assets/css/animate.css">

  <link rel="stylesheet" href="/assets/plugins/select2/css/select2.min.css" />

  <link rel="stylesheet" href="/assets/plugins/toastr/toatr.css" />

  <link rel="stylesheet" href="/assets/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.7/css/fixedHeader.dataTables.min.css">

  <link rel="stylesheet" href="/assets/plugins/fontawesome/css/fontawesome.min.css">
  <link rel="stylesheet" href="/assets/plugins/fontawesome/css/all.min.css">

  <link rel="stylesheet" href="/assets/css/style.css">
  

  <style>

      .dataTables_wrapper .dataTables_length label {
          display: flex;
          align-items: center;
      }

      .dataTables_wrapper .dataTables_length label::before {
          content: ' '; 
          display: inline-block;
          width: 0;
      }

      .dataTables_wrapper .dataTables_length select {
          display: inline-block; 
          margin: 0; 
      }

      .dataTables_wrapper .dataTables_length select {
          width: auto; 
      }
      .same-size-btn {
          display: inline-flex;
          align-items: center;
          justify-content: center;
          width: 200px; /* Set a fixed width */
          height: 50px; /* Set a fixed height */
          padding: 0; /* Remove any padding */
      }

      .same-size-btn img {
          width: 20px; /* Adjust the width of the icon if needed */
          height: auto; /* Maintain the aspect ratio */
      }

      input[type="number"].hide-arrow::-webkit-inner-spin-button,
      input[type="number"].hide-arrow::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
      }
      
      input[type="number"].hide-arrow {
        -moz-appearance: textfield;
      }
      select.readonly {
          pointer-events: none; 
          background-color: #e9ecef; 
      }

      .image-preview {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 15px;
    }
    
    .custom-file-container__image-preview {
        max-width: 100%;
        max-height: 300px;
        width: auto;
        height: auto;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
        object-fit: contain; /* Ensures the image is contained within the box and maintains aspect ratio */
    }

    .calculation-header {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
            text-transform: uppercase;
            text-align: center;
        }

        .calculation-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
            font-size: 18px;
        }

        .calculation-list li {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: flex;
            align-items: center;
        }

        .calculation-list li::before {
            content: '✔'; /* You can replace this with any icon or emoji */
            color: #4CAF50;
            font-size: 20px;
            margin-right: 10px;
        }

        .calculation-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background-color: #f1f1f1;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .nav-item .nav-link {
            display: flex;
            align-items: center;
            padding-left: 15px;  /* Sesuaikan nilai ini untuk mengontrol jarak dari tepi kiri */
        }

        .nav-item .nav-link i {
            margin-right: 10px;  /* Sesuaikan nilai ini untuk mengontrol jarak antara ikon dan teks */
        }

        .dash-widget {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            width: 100%;
            color: #fff;
        }

        .dash-widget-content {
            flex: 1;
        }

        .dash-widget-content h5 {
            margin: 0;
            padding: 0;
            font-size: 24px;
            font-weight: bold;
        }

        #currentDate {
            font-size: 20px;
            font-weight: normal;
        }

        .dash-count {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            width: 100%;
        }

        .dash-penjualan-sukses {
            background: linear-gradient(135deg, #42f573, #64d147);
        }
        .dash-penjualan-batal {
            background: linear-gradient(135deg, #f54242, #d14747);
        }
        .dash-penjualan-retur {
            background: linear-gradient(135deg, #42a5f5, #478ed1);
        }

        .dash-customerlama {
            background: linear-gradient(135deg, #66bbae, #409d8e);
        }
        .dash-customerbaru {
            background: linear-gradient(135deg, #8766bb, #6a42a8);
        }
        .dash-pemasukan {
            background: linear-gradient(135deg, #66bb6a, #43a047);
        }

        .dash-counts {
            flex: 1;
        }

        .dash-counts h4 {
            font-size: 28px;
            font-weight: bold;
            color: #fff;
        }

        .dash-counts h5 {
            font-size: 18px;
            color: #fff;
        }

        .dash-imgs i {
            font-size: 40px;
            color: #fff;
            margin-left: 15px;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            padding: 15px;
            color: #fff;
        }

        .card-title {
            margin: 0;
            font-size: 18px;
        }

        .chart-set {
            height: 300px;
        }

        .dataview table thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: bold;
        }

        .dataview table tbody tr {
            border-bottom: 1px solid #dee2e6;
        }

        .productimgname .product-img img {
            width: 50px;
            height: 50px;
            border-radius: 5px;
            margin-right: 10px;
        }

        .productimgname a {
            color: #333;
            text-decoration: none;
            font-weight: bold;
        }

        .productimgname a:hover {
            text-decoration: underline;
        }

        .custom-select-wrapper {
            position: relative;
            width: 100%;
        }

        .custom-select {
            display: block;
            width: 100%;
            height: 40px; /* Adjust height */
            padding: 0 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #fff;
            font-size: 16px; /* Adjust font size */
            line-height: 40px; /* Center text vertically */
            appearance: none;
            -webkit-appearance: none; /* Hide default styling in WebKit browsers */
        }

        .custom-select::after {
            content: '▼'; /* Arrow icon */
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #333;
        }

        .custom-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25);
        }

        .custom-select-option {
            background-color: #fff;
            border: none;
            border-radius: 5px;
            margin-bottom: 5px;
            cursor: pointer;
            padding: 10px;
        }

        .custom-select-option:hover {
            background-color: #f1f1f1;
        }

        .custom-select-wrapper::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 5px;
            pointer-events: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transform: translateY(-50%);
        }

        table {
            width: 100%;
        }

        td {
            white-space: nowrap; /* Prevents text from wrapping */
        }

        select {
            width: 100%; /* Makes sure the select box uses available width */
        }

        /* Scrollbar untuk .table-responsive, .dataTables_scrollBody, html, dan body */
        ::-webkit-scrollbar {
            width: 10px; /* Lebar scrollbar vertikal */
            height: 10px; /* Tinggi scrollbar horizontal */
        }

        /* Mengatur warna background track scrollbar */
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        /* Mengatur warna dan bentuk scrollbar */
        ::-webkit-scrollbar-thumb {
            background-color: #ff9f43; /* Warna scrollbar */
            border-radius: 10px; /* Membuat ujung scrollbar melengkung */
        }

        /* Mengubah warna scrollbar saat di-hover */
        ::-webkit-scrollbar-thumb:hover {
            background-color: #ff9430; /* Warna scrollbar saat dihover */
        }
        table {
          min-width: 100%;
        }

  </style>
  
  @yield('css')
</head>

<body>
  <div id="global-loader">
    <div class="whirly-loader"> </div>
  </div>
  <div id="global-loader-transparent">
    <div class="whirly-loader-transparent"> </div>
  </div>

  <div class="main-wrapper">

    <div class="header">

      <div class="header-left active">
        <a href="index.html" class="logo">
          <img src="/assets/img/logo.png" style="width: 3rem;" alt="">
        </a>
        <!-- <a href="index.html" class="logo-small">
          <img src="/assets/img/logo-small.png" alt="">
        </a> -->
        <!-- <a id="toggle_btn" href="javascript:void(0);">
        </a> -->
      </div>

      <a id="mobile_btn" class="mobile_btn" href="#sidebar">
        <span class="bar-icon">
          <span></span>
          <span></span>
          <span></span>
          <span></span>
        </span>
      </a>

      <ul class="nav user-menu">
        
        <li class="nav-item">
          <div class="top-nav-search">
            <a href="javascript:void(0);" class="responsive-search">
              <i class="fa fa-search"></i>
            </a>
            <form action="{{ route('auditor.update') }}" method="POST">
              @csrf
              @php
                    $user = Auth::user();             
                    $karyawan = \App\Models\Karyawan::where('user_id', $user->id)->first();       
                    $lokasis = \App\Models\Lokasi::all();
                  @endphp
                  @if($user->hasRole(['Auditor', 'SuperAdmin', 'Finance']))
                  <div class="form-group">
                      <select id="lokasi_id" name="lokasi_id" class="form-control p-1 audit" readonly required>
                          <option value="">Pilih Lokasi</option>
                          @foreach ($lokasis as $lokasi)
                          <option value="{{ $lokasi->id }}" data-tipe="{{ $lokasi->tipe_lokasi }}" {{ $lokasi->id == ($karyawan->lokasi_id ?? '') ? 'selected' : '' }}>{{ $lokasi->nama }}</option>
                          @endforeach
                      </select>
                  </div>
              @endif
            </form>
          </div>
        </li>


        <!-- <li class="nav-item dropdown has-arrow flag-nav">
          <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);" role="button">
            <img src="/assets/img/flags/us1.png" alt="" height="20">
          </a>
          <div class="dropdown-menu dropdown-menu-right">
            <a href="javascript:void(0);" class="dropdown-item">
              <img src="/assets/img/flags/us.png" alt="" height="16"> English
            </a>
            <a href="javascript:void(0);" class="dropdown-item">
              <img src="/assets/img/flags/fr.png" alt="" height="16"> French
            </a>
            <a href="javascript:void(0);" class="dropdown-item">
              <img src="/assets/img/flags/es.png" alt="" height="16"> Spanish
            </a>
            <a href="javascript:void(0);" class="dropdown-item">
              <img src="/assets/img/flags/de.png" alt="" height="16"> German
            </a>
          </div>
        </li> -->


        {{-- <li class="nav-item dropdown">
          <a href="javascript:void(0);" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
            <img src="/assets/img/icons/notification-bing.svg" alt="img"> <span class="badge rounded-pill">4</span>
          </a>
          <div class="dropdown-menu notifications">
            <div class="topnav-dropdown-header">
              <span class="notification-title">Notifications</span>
              <a href="javascript:void(0)" class="clear-noti"> Clear All </a>
            </div>
            <div class="noti-content">
              <ul class="notification-list">
                <li class="notification-message">
                  <a href="activities.html">
                    <div class="media d-flex">
                      <span class="avatar flex-shrink-0">
                        <img alt="" src="/assets/img/profiles/avatar-02.jpg">
                      </span>
                      <div class="media-body flex-grow-1">
                        <p class="noti-details"><span class="noti-title">Paleonepsis Baik</span> Kurang Dari Stok</p>
                        <p class="noti-time"><span class="notification-time">4 mins ago</span></p>
                      </div>
                    </div>
                  </a>
                </li>
                <li class="notification-message">
                  <a href="activities.html">
                    <div class="media d-flex">
                      <span class="avatar flex-shrink-0">
                        <img alt="" src="/assets/img/profiles/avatar-03.jpg">
                      </span>
                      <div class="media-body flex-grow-1">
                        <p class="noti-details"><span class="noti-title">Tarah Shropshire</span> changed the task name <span class="noti-title">Appointment booking with payment gateway</span></p>
                        <p class="noti-time"><span class="notification-time">6 mins ago</span></p>
                      </div>
                    </div>
                  </a>
                </li>
                <li class="notification-message">
                  <a href="activities.html">
                    <div class="media d-flex">
                      <span class="avatar flex-shrink-0">
                        <img alt="" src="/assets/img/profiles/avatar-06.jpg">
                      </span>
                      <div class="media-body flex-grow-1">
                        <p class="noti-details"><span class="noti-title">Misty Tison</span> added <span class="noti-title">Domenic Houston</span> and <span class="noti-title">Claire Mapes</span> to project <span class="noti-title">Doctor available module</span></p>
                        <p class="noti-time"><span class="notification-time">8 mins ago</span></p>
                      </div>
                    </div>
                  </a>
                </li>
                <li class="notification-message">
                  <a href="activities.html">
                    <div class="media d-flex">
                      <span class="avatar flex-shrink-0">
                        <img alt="" src="/assets/img/profiles/avatar-17.jpg">
                      </span>
                      <div class="media-body flex-grow-1">
                        <p class="noti-details"><span class="noti-title">Rolland Webber</span> completed task <span class="noti-title">Patient and Doctor video conferencing</span></p>
                        <p class="noti-time"><span class="notification-time">12 mins ago</span></p>
                      </div>
                    </div>
                  </a>
                </li>
                <li class="notification-message">
                  <a href="activities.html">
                    <div class="media d-flex">
                      <span class="avatar flex-shrink-0">
                        <img alt="" src="/assets/img/profiles/avatar-13.jpg">
                      </span>
                      <div class="media-body flex-grow-1">
                        <p class="noti-details"><span class="noti-title">Bernardo Galaviz</span> added new task <span class="noti-title">Private chat module</span></p>
                        <p class="noti-time"><span class="notification-time">2 days ago</span></p>
                      </div>
                    </div>
                  </a>
                </li>
              </ul>
            </div>
            <div class="topnav-dropdown-footer">
              <a href="activities.html">View all Notifications</a>
            </div>
          </div>
        </li> --}}

        <li class="nav-item dropdown has-arrow main-drop">
          <a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
            <span class="user-img"><img src="/assets/img/profiles/avator1.jpg" alt="">
              <span class="status online"></span></span>
          </a>
          <div class="dropdown-menu menu-drop-user">
            <div class="profilename">
              <div class="profileset">
                <span class="user-img"><img src="/assets/img/profiles/avator1.jpg" alt="">
                  <span class="status online"></span></span>
                  <div class="profilesets">
                    @auth
                        <h6>{{ auth()->user()->name }}</h6>
                        <h5>{{ auth()->user()->karyawans->jabatan ?? '' }}</h5>
                    @else
                        <h6>Guest</h6>
                        <h5>Not Logged In</h5>
                    @endauth
                </div>
                
              </div>
              <hr class="m-0">
              <a class="dropdown-item" href="{{ route('profile.edit')}}"> <i class="me-2" data-feather="user"></i> My Profile</a>
              <!-- <a class="dropdown-item" href="generalsettings.html"><i class="me-2" data-feather="settings"></i>Settings</a>
              <hr class="m-0"> -->
              @auth
              <a href="{{ route('logout.perform') }}" class="dropdown-item logout pb-0"><img src="/assets/img/icons/log-out.svg" class="me-2" alt="img">Logout</a>
              @endauth
            </div>
          </div>
        </li>
      </ul>


      <div class="dropdown mobile-user-menu">
        <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item">
              <select id="lokasi_id" name="lokasi_id" class="form-control" readonly required>
                <option value="">Pilih Lokasi</option>
                @foreach ($lokasis as $lokasi)
                <option value="{{ $lokasi->id }}" data-tipe="{{ $lokasi->tipe_lokasi }}">{{ $lokasi->nama }}</option>
                @endforeach
            </select>
          </a>
          <a class="dropdown-item" href="generalsettings.html">Settings</a>
          @auth
          <a href="{{ route('logout.perform') }}" class="dropdown-item">Logout</a>
          @endauth
        </div>
      </div>

    </div>

    @include('layouts.partials.sidebar')

    <div class="page-wrapper">
      <div class="content">
        @yield('content')
      </div>
    </div>

    <script src="/assets/js/jquery-3.6.0.min.js"></script>

    <script src="/assets/js/feather.min.js"></script>

    <script src="/assets/js/jquery.slimscroll.min.js"></script>

    <script src="/assets/js/jquery.dataTables.min.js"></script>
    <script src="/assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>

    <script src="/assets/plugins/select2/js/select2.min.js"></script>
    <script src="/assets/plugins/select2/js/custom-select.js"></script>

    <script src="/assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
    <script src="/assets/plugins/sweetalert/sweetalerts.min.js"></script>

    <script src="/assets/plugins/toastr/toastr.js"></script>
    <script src="/assets/plugins/toastr/toastr.min.js"></script>

    <script src="/assets/plugins/apexchart/apexcharts.min.js"></script>
    <script src="/assets/plugins/apexchart/chart-data.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="/assets/plugins/fileupload/fileupload.min.js"></script>

    <script src="/assets/js/script.js"></script>
    <script type="text/javascript">
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var userPermissions = @json($thisUserPermissions);
        
        $('.audit').select2();

        function formatNumber(value) {
          return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function cleanNumber(value) {
          return value.replace(/\./g, '');
        }

        function isNumeric(value) {
          return /^\d*$/.test(value);
        }

        function validatePersen(element) {
            var value = $(element).val().trim();

            if (value !== "" && !value.startsWith("0.") && value.length > 1) {
                value = value.replace(/^0+/, '');
            }

            value = parseFloat(value);

            if (isNaN(value) || value < 0) {
                $(element).val(0);
                return false;
            } else if (value > 100) {
                $(element).val(100);
                return false;
            }

            $(element).val(value);
            return true;
        }

        function validatePhoneNumber(element) {
            if (element.value.length > 13) {
                element.value = element.value.slice(0, 13);
            }
        }

        function validateDigit(element, limit) {
            if (element.value.length > limit) {
                element.value = element.value.slice(0, limit);
            }
        }
        $(document).ready(function() {
          $('#searchdiv').trigger('click');
        });

        function validateName(element) {
            element.value = element.value.replace(/[^a-zA-Z\s'-]/g, '');
        }

        function validateDotStripNumber(element) {
          element.value = element.value.replace(/[^0-9.-]/g, '');
        }

        function validateCantExceed(element, limit) {
            var value = $(element).val();
            if (value.includes('.')) {
              value = cleanNumber(value);
            } else {
              value = parseInt(value) || 0;
            }
            
            if (value > limit) {
                value = limit;
                $(element).val(formatNumber(value));
              } 
              if (isNaN(value)) {
                value = 0;
                $(element).val(formatNumber(value));
            } 
        }

        function validateMinZero(element, limit) {
            var value = $(element).val().trim();

            if (value !== "" && !value.startsWith("0.") && value.length > 1) {
                value = value.replace(/^0+/, '');
            }

            value = parseFloat(value);

            if (isNaN(value) || value < 0) {
                $(element).val(0);
                return false;
            } else if (value > limit) {
                value = limit;
              } 
              if (isNaN(value)) {
                value = 0;
            } 

            $(element).val(value);
            return true;
        }

        function cantMinus(element) {
          // console.log('awal')
          var value = $(element).val().trim();
          
          if (value !== "" && !value.startsWith("0.") && value.length > 1) {
            value = value.replace(/^0+/, '');
          }
          
          value = parseFloat(value);
          
          if (isNaN(value) || value < 0) {
            // console.log('akhir')
            $(element).val(0);
            return false;
          }

          $(element).val(value);
          return true;
        }

      $(document).ready(function() {
        let sessionData = @json(session()->all());
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

      $(document).ready(function() {
        $('#lokasi_id').change(function() {
            var lokasiId = $(this).val();
            var csrfToken = $('input[name="_token"]').val();

            if (lokasiId) {
                $.ajax({
                    url: '{{ route("auditor.update") }}',
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        lokasi_id: lokasiId
                    },
                    success: function(response) {
                        // Handle success response
                        // console.log(response);
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.error(error);
                    }
                });
            }
        });
      });

      function initDataTable(selector, options = {}, filters = {}, tableType = '') {
          let defaultOptions = {
              processing: true,
              serverSide: true,
              ajax: {
                  url: options.ajaxUrl,
                  type: "GET",
                  data: function(d) {
                      // table type
                      if (tableType) {
                          d.table = tableType;
                      }
                      // filter
                      $.each(filters, function(key, value) {
                        if ($(value).find('input[type="checkbox"]').length > 0) { // jika checklist
                            let selectedValues = [];
                            $(value).find('input[type="checkbox"]:checked').each(function() {
                                selectedValues.push($(this).val());
                            });
                            d[key] = selectedValues;
                        } else {
                            d[key] = $(value).val();
                        }
                      });
                  },
                  dataSrc: function(json) {
                      // Debugging data from server
                      console.log('Data received from server:', json); 
                      return json.data;
                  },
                  error: function(xhr, error, code) {
                      console.log(xhr);
                      console.log(code);
                  }
              },
              columns: options.columns,
              order: options.order || [[0, 'asc']],
              searching: options.searching !== undefined ? options.searching : true,
              searchDelay: 1000,
              lengthChange: options.lengthChange !== undefined ? options.lengthChange : true,
              pageLength: options.pageLength || 10,
              lengthMenu: [ [5, 10, 25, 50, 100], [5, 10, 25, 50, 100] ],
              responsive: true,
              autoWidth: true,
              info: false,
              fixedHeader: true,
              scrollX: '100%',
              scrollY: '400px',
              scrollCollapse: true,
              language: {
                  url: '/assets/Indonesian.json',
              },
          };

          let dataTableOptions = $.extend({}, defaultOptions, options);

          return $(selector).DataTable(dataTableOptions);
      }

      function debounce(func, wait) {
          let timeout;
          return function(...args) {
              clearTimeout(timeout);
              timeout = setTimeout(() => func.apply(this, args), wait);
          };
      }

      var defaultImg = 'data:image/png;base64,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alQAAAAAgClIaAAAAABgChIaAAAAAJiChAYAAAAApiChAQAAAIApSGgAAAAAYAoSGgAAAACYgoQGAAAAAKYgoQEAAACAKUhoAAAAAGAKEhoAAAAAmIKEBgAAAACmIKEBAAAAgClIaAAAAABgChIaAAAAAJiChAYAAAAApiChAQAAAIApSGgAAAAAYAoSGgAAAACYgoQGAAAAAKYgoQEAAACAKUhoAAAAAGAKEhoAAAAAmIKEBgAAAACmIKEBAAAAgClIaAAAAABgChIaAAAAAJiChAYAAAAApiChAQAAAIApSGgAAAAAYAoSGgAAAACYgoQGAAAAAKYgoQEAAACAKUhoAAAAAGAKEhoAAAAAmIKEBgAAAACmIKEBAAAAgClIaAAAAABgChIaAAAAAJiChAYAAAAApiChAQAAAIApSGgAAAAAYAoSGgAAAACYgoQGAAAAAKYgoQEAAACAKUhoAAAAAGAKEhoAAAAAmIKEBgAAAACmIKEBAAAAgClIaAAAAABgChIaAAAAAJiChAYAAAAApiChAQAAAIApSGgAAAAAYAoSGgAAAACYgoQGAAAAAKYgoQEAAACAKUhoAAAAAGAKEhoAAAAAmIKEBgAAAACmIKEBAAAAgClIaAAAAABgChIaAAAAAJiChAYAAAAApiChAQAAAIApSGgAAAAAYAoSGgAAAACYgoQGAAAAAKYgoQEAAACAKUhoAAAAAGAKEhoAAAAAmIKEBgAAAACmIKEBAAAAgClIaAAAAABgChIaAAAAAJiChAYAAAAApiChAQAAAIApSGgAAAAAYAoSGgAAAACYgoQGAAAAAKYgoQEAAACAKUhoAAAAAGAKEhoAAAAAmIKEBgAAAACmIKEBAAAAgClIaAAAAABgChIaAAAAAJiChAYAAAAApiChAQAAAIApSGgAAAAAYAoSGgAAAACYgoQGAAAAAKYgoQEAAACAKf4/a+o6ARVh9YkAAAAASUVORK5CYII=';
    </script>
    
    @section("scripts")

    @show
</body>

</html>