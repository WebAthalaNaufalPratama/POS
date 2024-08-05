@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Laporan Pelanggan</h4>
                    </div>
                    <div class="page-btn">
                        <button class="btn btn-outline-danger" style="height: 2.5rem; padding: 0.5rem 1rem; font-size: 1rem;" onclick="pdf()">
                            <img src="/assets/img/icons/pdf.svg" alt="PDF" style="height: 1rem;"/> PDF
                        </button>
                        <button class="btn btn-outline-success" style="height: 2.5rem; padding: 0.5rem 1rem; font-size: 1rem;" onclick="excel()">
                            <img src="/assets/img/icons/excel.svg" alt="EXCEL" style="height: 1rem;"/> EXCEL
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
            <div class="row mb-2">
                    <row class="col-lg-12 col-sm-12">
                        <div class="row">
                            <div class="col-lg col-sm-6 col-12">
                                <select id="filterGallery" name="filterGallery" class="form-control" title="Gallery">
                                    <option value="">Pilih Gallery</option>
                                    @foreach ($galleries as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == request()->input('gallery') ? 'selected' : '' }}>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <select id="filterCustomer" name="filterCustomer" class="form-control" title="Customer">
                                    <option value="">Pilih Customer</option>
                                    @foreach ($customers as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == request()->input('customer') ? 'selected' : '' }}>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <input type="date" class="form-control" name="filterJatuhTempo" id="filterJatuhTempo" value="{{ request()->input('tempo') }}" title="Jatuh Tempo">
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <input type="date" class="form-control" name="filterDateStart" id="filterDateStart" value="{{ request()->input('dateStart') }}" title="Awal Penjualan">
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <input type="date" class="form-control" name="filterDateEnd" id="filterDateEnd" value="{{ request()->input('dateEnd') }}" title="Akhir Penjualan">
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('laporan.pelanggan') }}" class="btn btn-info">Filter</a>
                                <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('laporan.pelanggan') }}" class="btn btn-warning">Clear</a>
                            </div>
                        </div>
                    </row>
                </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. Invoice</th>
                                <th>Lokasi</th>
                                <th>Tanggal Invoice</th>
                                <th>Jatuh Tempo</th>
                                <th>ID Customer</th>
                                <th>Total Tagihan</th>
                                <th>DP</th>
                                <th>Sisa Bayar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach($penjualan as $item)
                                <tr>
                                    <td >{{ $no++ }}</td>
                                    <td>
                                        <table>
                                        @foreach(explode(', ', $item->no_invoice) as $invoice)
                                            <tr>
                                                <td>{{ $invoice }}</td>
                                            </tr>
                                        @endforeach
                                        </table>
                                    </td>
                                    <td>
                                        <table>
                                        @foreach(explode(', ', $item->lokasi->nama) as $lokasi)
                                            <tr>
                                                <td>{{ $lokasi }}</td>
                                            </tr>
                                        @endforeach
                                        </table>
                                    </td>
                                    <td>
                                        <table>
                                        @foreach(explode(', ', $item->tanggal_invoice) as $tanggalInvoice)
                                            <tr>
                                                <td>{{ $tanggalInvoice }}</td>
                                            </tr>
                                        @endforeach
                                        </table>
                                    </td>
                                    <td>
                                        <table>
                                        @foreach(explode(', ', $item->jatuh_tempo) as $jatuhTempo)
                                            <tr>
                                                <td>{{ $jatuhTempo }}</td>
                                            </tr>
                                        @endforeach
                                        </table>
                                    </td>
                                    <td>{{ $item->customer->nama }}</td>
                                    <td>
                                        <table>
                                        @foreach(explode(', ', $item->total_tagihan) as $totalTagihan)
                                            <tr>
                                                <td>{{ $totalTagihan }}</td>
                                            </tr>
                                        @endforeach
                                        </table>
                                    </td>
                                    <td>
                                        <table>
                                        @foreach(explode(', ', $item->dp) as $dp)
                                            <tr>
                                                <td>{{ $dp }}</td>
                                            </tr>
                                        @endforeach
                                        </table>
                                    </td>
                                    <td>
                                        <table>
                                        @foreach(explode(', ', $item->sisa_bayar) as $sisaBayar)
                                            <tr>
                                                <td>{{ $sisaBayar }}</td>
                                            </tr>
                                        @endforeach
                                        </table>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function deleteData(id) {
        $.ajax({
            type: "GET",
            url: "/penjualan/" + id + "/delete",
            success: function(response) {
                toastr.success(response.msg, 'Success', {
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: false,
                    progressBar: true
                });

                setTimeout(() => {
                    location.reload()
                }, 2000);
            },
            error: function(error) {
                toastr.error(JSON.parse(error.responseText).msg, 'Error', {
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: false,
                    progressBar: true
                });
            }
        });
    }
</script>
<script>
    $('#filterBtn').click(function(){
        var baseUrl = $(this).data('base-url');
        var urlString = baseUrl;
        var first = true;
        var symbol = '';

        var Gallery = $('#filterGallery').val();
        if (Gallery) {
            var filterGallery = 'gallery=' + Gallery;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterGallery;
        }

        var Customer = $('#filterCustomer').val();
        if (Customer) {
            var filterCustomer = 'customer=' + Customer;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterCustomer;
        }

        var JatuhTempo = $('#filterJatuhTempo').val();
        if (JatuhTempo) {
            var filterJatuhTempo = 'tempo=' + JatuhTempo;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterJatuhTempo;
        }

        var dateStart = $('#filterDateStart').val();
        if (dateStart) {
            var filterDateStart = 'dateStart=' + dateStart;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterDateStart;
        }

        var dateEnd = $('#filterDateEnd').val();
        if (dateEnd) {
            var filterDateEnd = 'dateEnd=' + dateEnd;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterDateEnd;
        }
        window.location.href = urlString;
    });
    $('#clearBtn').click(function(){
        var baseUrl = $(this).data('base-url');
        var url = window.location.href;
        if(url.indexOf('?') !== -1){
            window.location.href = baseUrl;
        }
        return 0;
    });
    
    function pdf(){
        var filterGallery = $('#filterGallery').val();
        var filterCustomer = $('#filterCustomer').val();
        var filterJatuhTempo = $('#filterJatuhTempo').val();
        var filterDateStart = $('#filterDateStart').val();
        var filterDateEnd = $('#filterDateEnd').val();

        var desc = 'Cetak laporan tanpa filter';
        if(filterGallery || filterCustomer || filterJatuhTempo || filterDateStart || filterDateEnd){
            desc = 'cetak laporan dengan filter';
        }
        
        Swal.fire({
            title: 'Cetak PDF?',
            text: desc,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Cetak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                var url = "{{ route('laporan.pelanggan-pdf') }}" + '?' + $.param({
                    gallery: filterGallery,
                    customer: filterCustomer,
                    jatuh_tempo: filterJatuhTempo,
                    dateStart: filterDateStart,
                    dateEnd: filterDateEnd,
                });
                
                window.open(url, '_blank');
            }
        });
    }
    function excel(){
        var filterGallery = $('#filterGallery').val();
        var filterCustomer = $('#filterCustomer').val();
        var filterJatuhTempo = $('#filterJatuhTempo').val();
        var filterDateStart = $('#filterDateStart').val();
        var filterDateEnd = $('#filterDateEnd').val();

        var desc = 'Cetak laporan tanpa filter';
        if(filterGallery || filterCustomer || filterJatuhTempo || filterDateStart || filterDateEnd){
            desc = 'cetak laporan dengan filter';
        }
        
        Swal.fire({
            title: 'Cetak Excel?',
            text: desc,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Cetak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                var url = "{{ route('laporan.pelanggan-excel') }}" + '?' + $.param({
                    gallery: filterGallery,
                    customer: filterCustomer,
                    jatuh_tempo: filterJatuhTempo,
                    dateStart: filterDateStart,
                    dateEnd: filterDateEnd,
                });
                
                window.location.href = url;
            }
        });
    }
</script>

<!-- mematikan js atau klik kanan js -->
<!-- <script>
    document.addEventListener("contextmenu", function(e){
        e.preventDefault();
    }, false);
</script> -->
@endsection