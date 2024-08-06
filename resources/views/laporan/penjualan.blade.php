@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Laporan Penjualan</h4>
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
                                <select id="filterSales" name="filterSales" class="form-control" title="Sales">
                                    <option value="">Pilih Sales</option>
                                    @foreach ($sales as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == request()->input('sales') ? 'selected' : '' }}>{{ $item->nama }}</option>
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
                                <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('laporan.penjualan') }}" class="btn btn-info">Filter</a>
                                <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('laporan.penjualan') }}" class="btn btn-warning">Clear</a>
                            </div>
                        </div>
                    </row>
                </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Invoice</th>
                                <th>Galery</th>
                                <th>Nama Customer</th>
                                <th>Tanggal Invoice</th>
                                <th>Jatuh Tempo</th>
                                <th>Nama Sales</th>
                                <th>Nama Produk Jual</th>
                                <th>Jumlah Produk Jual</th>
                                <th>Nama Produk</th>
                                <th>Jumlah</th>
                                <th>Kondisi (Baik)</th>
                                <th>Kondisi (Afkir)</th>
                                <th>Kondisi (Bonggol)</th>
                                <th>Harga</th>
                                <th>Diskon</th>
                                <th>Jumlah Harga</th>
                                <th>Sub Total</th>
                                <th>PPN</th>
                                <th>Biaya Pengiriman</th>
                                <th>Total Tagihan</th>
                                <th>DP</th>
                                <th>Sisa Bayar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($combinedData as $no_do => $data)       
                            <tr>
                                <td >{{ $no++ }}</td>
                                <td >{{ $data['no_invoice'] }}</td>
                                <td>{{ $data['lokasi_pengirim'] }}</td>
                                <td>{{ $data['customer'] }}</td>
                                <td>{{ $data['tanggal_invoice'] }}</td>
                                <td>{{ $data['jatuh_tempo'] }}</td>
                                <td>{{ $data['sales'] }}</td>
                                <td>
                                    <table>
                                        @foreach ($data['produk_jual'] as $index => $produkJual)
                                        <tr>
                                            <td>{{ $produkJual['nama_produkjual'] }}</td>
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
                                            @foreach ($produkJual['komponen'] as $komponen)
                                            <tr>
                                                <td>{{ $komponen['nama_produk'] }}</td>
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
                                                <td>{{ $komponen['jumlah'] }}</td>
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
                                                <td>{{ $komponen['kondisibaik'] }}</td>
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
                                                <td>{{ $komponen['kondisiafkir'] }}</td>
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
                                                <td>{{ $komponen['kondisibonggol'] }}</td>
                                            </tr>
                                            @endforeach
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($data['produk_jual'] as $index => $produkJual)
                                        <tr>
                                            <td>{{ $produkJual['harga'] }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($data['produk_jual'] as $index => $produkJual)
                                        <tr>
                                            <td>{{ $produkJual['diskon'] }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($data['produk_jual'] as $index => $produkJual)
                                        <tr>
                                            <td>{{ $produkJual['jumlah_harga'] }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>{{ $data['sub_total'] }}</td>
                                <td>{{ $data['jumlah_ppn'] }}</td>
                                <td>{{ $data['biaya_pengiriman'] }}</td>
                                <td>{{ $data['total_tagihan'] }}</td>
                                <td>{{ $data['dp'] }}</td>
                                <td>{{ $data['sisa_bayar'] }}</td>
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

        var sales = $('#filterSales').val();
        if (sales) {
            var filterSales = 'sales=' + sales;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterSales;
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
        var filterSales = $('#filterSales').val();
        var filterCustomer = $('#filterCustomer').val();
        var filterJatuhTempo = $('#filterJatuhTempo').val();
        var filterDateStart = $('#filterDateStart').val();
        var filterDateEnd = $('#filterDateEnd').val();

        var desc = 'Cetak laporan tanpa filter';
        if(filterGallery || filterSales || filterCustomer || filterJatuhTempo || filterDateStart || filterDateEnd){
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
                var url = "{{ route('laporan.penjualan-pdf') }}" + '?' + $.param({
                    gallery: filterGallery,
                    sales: filterSales,
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
        var filterSales = $('#filterSales').val();
        var filterCustomer = $('#filterCustomer').val();
        var filterJatuhTempo = $('#filterJatuhTempo').val();
        var filterDateStart = $('#filterDateStart').val();
        var filterDateEnd = $('#filterDateEnd').val();

        var desc = 'Cetak laporan tanpa filter';
        if(filterGallery || filterSales || filterCustomer || filterJatuhTempo || filterDateStart || filterDateEnd){
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
                var url = "{{ route('laporan.penjualan-excel') }}" + '?' + $.param({
                    gallery: filterGallery,
                    sales: filterSales,
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