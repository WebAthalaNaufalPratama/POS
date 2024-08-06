@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Laporan Retur Penjualan</h4>
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
                                <select id="filterSupplier" name="filterSupplier" class="form-control" title="Supplier">
                                    <option value="">Pilih Supplier</option>
                                    @foreach ($supplier as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == request()->input('supplier') ? 'selected' : '' }}>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <select id="filterKomplain" name="filterKomplain" class="form-control" title="Komplain">
                                    <option value="">Pilih Komplain</option>
                                    <option value="retur" {{ 'retur' == request()->input('komplain') ? 'selected' : '' }}>Retur</option>
                                    <option value="diskon" {{ 'diskon' == request()->input('komplain') ? 'selected' : '' }}>Diskon</option>
                                    <option value="refund" {{ 'refund' == request()->input('komplain') ? 'selected' : '' }}>Refund</option>
                                </select>
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <input type="date" class="form-control" name="filterDateStart" id="filterDateStart" value="{{ request()->input('dateStart') }}" title="Awal Penjualan">
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <input type="date" class="form-control" name="filterDateEnd" id="filterDateEnd" value="{{ request()->input('dateEnd') }}" title="Akhir Penjualan">
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('laporan.returpenjualan') }}" class="btn btn-info">Filter</a>
                                <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('laporan.returpenjualan') }}" class="btn btn-warning">Clear</a>
                            </div>
                        </div>
                    </row>
                </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Retur</th>
                                <th>No Invoice</th>
                                <th>Tanggal Invoice</th>
                                <th>Tanggal Komplain</th>
                                <th>Nama Customer</th>
                                <th>Nama Produk</th>
                                <th>Komplain Kerusakan</th>
                                <th>QTY</th>
                                <th>Harga Jual</th>
                                <th>Jumlah Harga</th>
                                <th>Jumlah Diskon</th>
                                <th>Penanganan Komplain</th>
                                <th>Supplier</th>
                                <th>Galery</th>
                                <th>Nama Sales</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($returpenjualan as $retur)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $retur->no_retur }}</td>
                                <td>{{ $retur->no_invoice }}</td>
                                <td>{{ $retur->tanggal_invoice }}</td>
                                <td>{{ $retur->tanggal_retur }}</td>
                                <td>{{ $retur->customer->nama }}</td>

                                @php
                                    $produkRetur = $produkterjual->where('no_retur', $retur->no_retur);
                                    $produkPenjualan = $penjualan->where('no_invoice', $retur->no_invoice);
                                @endphp

                                <td>
                                    <table>
                                        @foreach ($produkRetur as $index => $produk)
                                        <tr>
                                            <td>{{ $produk->produk->nama }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($produkRetur as $index => $produk)
                                        <tr>
                                            <td>{{ $produk->alasan }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($produkRetur as $index => $produk)
                                        <tr>
                                            <td>{{ $produk->jumlah }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($produkRetur as $index => $produk)
                                        <tr>
                                            <td>{{ $produk->harga_jual }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>{{ $retur->total }}</td>
                                <td>
                                    <table>
                                        @foreach ($produkRetur as $index => $produk)
                                        <tr>
                                            <td>{{ $produk->diskon }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>

                                <td>{{ $retur->komplain }}</td>
                                <td>{{ $retur->supplier->nama }}</td>
                                <td>{{ $retur->lokasi->nama }}</td>
                                <td>
                                    <table>
                                        @foreach ($produkPenjualan as $index => $produk)
                                        <tr>
                                            <td>{{ $produk->karyawan->nama }}</td>
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

        var Supplier = $('#filterSupplier').val();
        if (Supplier) {
            var filterSupplier = 'supplier=' + Supplier;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterSupplier;
        }

        var customer = $('#filterCustomer').val();
        if (customer) {
            var filterCustomer = 'customer=' + customer;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterCustomer;
        }

        var Komplain = $('#filterKomplain').val();
        if (Komplain) {
            var filterKomplain = 'komplain=' + Komplain;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterKomplain;
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
        var filterSupplier = $('#filterSupplier').val();
        var filterCustomer = $('#filterCustomer').val();
        var filterKomplain = $('#filterKomplain').val();
        var filterDateStart = $('#filterDateStart').val();
        var filterDateEnd = $('#filterDateEnd').val();

        var desc = 'Cetak laporan tanpa filter';
        if(filterGallery || filterSupplier || filterCustomer || filterKomplain || filterDateStart || filterDateEnd){
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
                var url = "{{ route('laporan.returpenjualan-pdf') }}" + '?' + $.param({
                    gallery: filterGallery,
                    supplier: filterSupplier,
                    customer: filterCustomer,
                    komplain: filterKomplain,
                    dateStart: filterDateStart,
                    dateEnd: filterDateEnd,
                });
                
                window.open(url, '_blank');
            }
        });
    }
    function excel(){
        var filterGallery = $('#filterGallery').val();
        var filterSupplier = $('#filterSupplier').val();
        var filterCustomer = $('#filterCustomer').val();
        var filterKomplain = $('#filterKomplain').val();
        var filterDateStart = $('#filterDateStart').val();
        var filterDateEnd = $('#filterDateEnd').val();

        var desc = 'Cetak laporan tanpa filter';
        if(filterGallery || filterSupplier || filterCustomer || filterKomplain || filterDateStart || filterDateEnd){
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
                var url = "{{ route('laporan.returpenjualan-excel') }}" + '?' + $.param({
                    gallery: filterGallery,
                    supplier: filterSupplier,
                    customer: filterCustomer,
                    komplain: filterKomplain,
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