@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Laporan Delivery Order Penjualan</h4>
                    </div>
                    <div class="page-btn">
                        <button class="btn btn-outline-danger" style="height: 2.5rem; padding: 0.5rem 1rem; font-size: 1rem;" onclick="pdf()">
                            <img src="/assets/img/icons/pdf.svg" alt="PDF" style="height: 1rem;" /> PDF
                        </button>
                        <button class="btn btn-outline-success" style="height: 2.5rem; padding: 0.5rem 1rem; font-size: 1rem;" onclick="excel()">
                            <img src="/assets/img/icons/excel.svg" alt="EXCEL" style="height: 1rem;" /> EXCEL
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
                                <input type="date" class="form-control" name="filterTanggalKirim" id="filterTanggalKirim" value="{{ request()->input('tanggalkirim') }}" title="Tanggal Pengiriman">
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <input type="date" class="form-control" name="filterDateStart" id="filterDateStart" value="{{ request()->input('dateStart') }}" title="Awal doPenjualan">
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <input type="date" class="form-control" name="filterDateEnd" id="filterDateEnd" value="{{ request()->input('dateEnd') }}" title="Akhir doPenjualan">
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('laporan.dopenjualan') }}" class="btn btn-info">Filter</a>
                                <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('laporan.dopenjualan') }}" class="btn btn-warning">Clear</a>
                            </div>
                        </div>
                    </row>
                </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Delivery Order</th>
                                <th>Lokasi Galeri</th>
                                <th>Nama Pengirim</th>
                                <th>Nama Penerima</th>
                                <th>Tanggal Pengiriman</th>
                                <th>Tanggal Invoice</th>
                                <th>Nama Produk Jual</th>
                                <th>Jumlah Produk Jual</th>
                                <th>Nama Produk</th>
                                <th>Jumlah</th>
                                <th>Kondisi (Baik)</th>
                                <th>Kondisi (Afkir)</th>
                                <th>Kondisi (Bonggol)</th>
                                <th>Unit Satuan</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($combinedData as $no_do => $data)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $data['no_do'] }}</td>
                                <td>{{ $data['lokasi_pengirim'] }}</td>
                                <td>{{ $data['customer'] }}</td>
                                <td>{{ $data['penerima'] }}</td>
                                <td>{{ $data['tanggal_kirim'] }}</td>
                                <td>{{ $data['tanggal_invoice'] }}</td>
                                <td>
                                    <table>
                                        @foreach ($data['produk_jual'] as $index => $produkJual)
                                        <tr>
                                            <td>{{ $produkJual['nama_produkjual'] }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($data['produk_jual'] as $index => $produkJual)
                                        <tr>
                                            <td>{{ $produkJual['jumlahprodukjual'] }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($data['produk_jual'] as $index => $produkJual)
                                        @foreach ($produkJual['komponen'] as $index => $komponen)
                                        <tr>
                                            <td>{{ $komponen['nama_produk'] }}
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($data['produk_jual'] as $index => $produkJual)
                                        @foreach ($produkJual['komponen'] as $komponen)
                                        <tr>
                                            <td>{{ $komponen['jumlah'] }}
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($data['produk_jual'] as $index => $produkJual)
                                        @foreach ($produkJual['komponen'] as $komponen)
                                        <tr>
                                            <td>{{ $komponen['kondisibaik'] }}
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($data['produk_jual'] as $index => $produkJual)
                                        @foreach ($produkJual['komponen'] as $komponen)
                                        <tr>
                                            <td>{{ $komponen['kondisiafkir'] }}
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($data['produk_jual'] as $index => $produkJual)
                                        @foreach ($produkJual['komponen'] as $komponen)
                                        <tr>
                                            <td>{{ $komponen['kondisibonggol'] }}
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($data['produk_jual'] as $index => $produkJual)
                                        @foreach ($produkJual['komponen'] as $komponen)
                                        <tr>
                                            <td>{{ $produkJual['unitsatuan'] }}
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($data['produk_jual'] as $index => $produkJual)
                                        @foreach ($produkJual['komponen'] as $komponen)
                                        <tr>
                                            <td>{{ $produkJual['keterangan'] }}
                                            </td>
                                        </tr>
                                        @endforeach
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
    $('#filterBtn').click(function() {
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

        var TanggalKirim = $('#filterTanggalKirim').val();
        if (TanggalKirim) {
            var filterTanggalKirim = 'tanggalkirim=' + TanggalKirim;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterTanggalKirim;
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
    $('#clearBtn').click(function() {
        var baseUrl = $(this).data('base-url');
        var url = window.location.href;
        if (url.indexOf('?') !== -1) {
            window.location.href = baseUrl;
        }
        return 0;
    });
    function pdf(){
        var filterGallery = $('#filterGallery').val();
        var filterTanggalKirim = $('#filterTanggalKirim').val();
        var filterCustomer = $('#filterCustomer').val();
        var filterDateStart = $('#filterDateStart').val();
        var filterDateEnd = $('#filterDateEnd').val();

        var desc = 'Cetak laporan tanpa filter';
        if(filterGallery || filterTanggalKirim || filterCustomer || filterDateStart || filterDateEnd){
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
                var url = "{{ route('laporan.dopenjualan-pdf') }}" + '?' + $.param({
                    gallery: filterGallery,
                    tanggal_kirim: filterTanggalKirim,
                    customer: filterCustomer,
                    dateStart: filterDateStart,
                    dateEnd: filterDateEnd,
                });
                
                window.open(url, '_blank');
            }
        });
    }
    function excel(){
        var filterGallery = $('#filterGallery').val();
        var filterTanggalKirim = $('#filterTanggalKirim').val();
        var filterCustomer = $('#filterCustomer').val();
        var filterDateStart = $('#filterDateStart').val();
        var filterDateEnd = $('#filterDateEnd').val();

        var desc = 'Cetak laporan tanpa filter';
        if(filterGallery || filterTanggalKirim || filterCustomer || filterDateStart || filterDateEnd){
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
                var url = "{{ route('laporan.dopenjualan-excel') }}" + '?' + $.param({
                    gallery: filterGallery,
                    tanggal_kirim: filterTanggalKirim,
                    customer: filterCustomer,
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