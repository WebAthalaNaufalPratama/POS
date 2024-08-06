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
                                <select id="filterPengirim" name="filterPengirim" class="form-control" title="Pengirim">
                                    <option value="">Pilih Pengirim</option>
                                    @foreach ($suppliers as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == request()->input('pengirim') ? 'selected' : '' }}>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <select id="filterPenerima" name="filterPenerima" class="form-control" title="Penerima">
                                    <option value="">Pilih Penerima</option>
                                    @foreach ($galleries as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == request()->input('penerima') ? 'selected' : '' }}>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <input type="date" class="form-control" name="filterTanggalDikirim" id="filterTanggalDikirim" value="{{ request()->input('tanggaldikirim') }}" title="Tanggal Pengiriman">
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <input type="date" class="form-control" name="filterTanggalDiterima" id="filterTanggalDiterima" value="{{ request()->input('tanggalditerima') }}" title="Tanggal Diterima">
                            </div>

                            <div class="col-lg col-sm-6 col-12">
                                <select id="filterBulan" name="filterBulan" class="form-control" title="Bulan">
                                    <option value="">Pilih Bulan</option>
                                    @foreach ($inventorys as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == request()->input('bulan') ? 'selected' : '' }}>{{ $item->bulan_inden }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('laporan.mutasiinden') }}" class="btn btn-info">Filter</a>
                                <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('laporan.mutasiinden') }}" class="btn btn-warning">Clear</a>
                            </div>
                        </div>
                    </row>
                </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Mutasi</th>
                                <th>Nama Pengirim</th>
                                <th>Nama Penerima</th>
                                <th>Tanggal Pengiriman</th>
                                <th>Tanggal Diterima</th>
                                <th>Bulan</th>
                                <th>Kategori</th>
                                <th>Jumlah Pengiriman</th>
                                <th>Biaya Perawatan</th>
                                <th>Sub Total Biaya Perawatan</th>
                                <th>Jumlah Diterima</th>
                                <th>Kondisi Diterima</th>
                                <th>Biaya Pengiriman</th>
                                <th>Rekening Bank</th>
                                <th>Total Biaya</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($mutasiinden as $mutasi)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $mutasi->no_mutasi }}</td>
                                <td>{{ $mutasi->supplier->nama }}</td>
                                <td>{{ $mutasi->lokasi->nama }}</td>
                                <td>{{ $mutasi->tgl_dikirim }}</td>
                                <td>{{ $mutasi->tgl_diterima ?? '-' }}</td>

                                @php
                                    $produkMutasi = $produkterjual->where('mutasiinden_id', $mutasi->id);
                                @endphp

                                <td>
                                    <table>
                                        @foreach ($produkMutasi as $index => $produk)
                                        <tr>
                                            <td>{{ $produk->produk->bulan_inden }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($produkMutasi as $index => $produk)
                                        <tr>
                                            <td>{{ $produk->produk->kode_produk }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($produkMutasi as $index => $produk)
                                        <tr>
                                            <td>{{ $produk->jml_dikirim }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($produkMutasi as $index => $produk)
                                        <tr>
                                            <td>{{ $produk->biaya_rawat }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($produkMutasi as $index => $produk)
                                        <tr>
                                            <td>{{ $produk->totalharga }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($produkMutasi as $index => $produk)
                                        <tr>
                                            <td>{{ $produk->jml_diterima }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($produkMutasi as $index => $produk)
                                        <tr>
                                            <td>{{ $produk->kondisi->nama ?? '' }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>{{ $mutasi->biaya_pengiriman }}</td>
                                <td>{{ $mutasi->rekening_id }}</td>
                                <td>{{ $mutasi->total_biaya }}</td>
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

        var Pengirim = $('#filterPengirim').val();
        if (Pengirim) {
            var filterPengirim = 'pengirim=' + Pengirim;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterPengirim;
        }

        var Penerima = $('#filterPenerima').val();
        if (Penerima) {
            var filterPenerima = 'penerima=' + Penerima;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterPenerima;
        }

        var TanggalDikirim = $('#filterTanggalDikirim').val();
        if (TanggalDikirim) {
            var filterTanggalDikirim = 'tanggaldikirim=' + TanggalDikirim;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterTanggalDikirim;
        }

        var TanggalDiterima = $('#filterTanggalDiterima').val();
        if (TanggalDiterima) {
            var filterTanggalDiterima = 'tanggalditerima=' + TanggalDiterima;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterTanggalDiterima;
        }

        var Bulan = $('#filterBulan').val();
        if (Bulan) {
            var filterBulan = 'bulan=' + Bulan;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterBulan;
        }

        var Kategori = $('#filterKategori').val();
        if (Kategori) {
            var filterKategori = 'kategori=' + Kategori;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterKategori;
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
        var filterPengirim = $('#filterPengirim').val();
        var filterPenerima = $('#filterPenerima').val();
        var filterTanggalDikirim = $('#filterTanggalDikirim').val();
        var filterTanggalDiterima = $('#filterTanggalDiterima').val();
        var filterBulan = $('#filterBulan').val();
        var filterKategori = $('#filterKategori').val();

        var desc = 'Cetak laporan tanpa filter';
        if(filterPengirim || filterPenerima || filterTanggalDikirim || filterTanggalDiterima || filterBulan || filterKategori){
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
                var url = "{{ route('laporan.mutasiinden-pdf') }}" + '?' + $.param({
                    pengirim: filterPengirim,
                    penerima: filterPenerima,
                    tanggaldikirim: filterTanggalDikirim,
                    tanggalditerima: filterTanggalDiterima,
                    bulan: filterBulan,
                    kategori: filterKategori,
                });
                
                window.open(url, '_blank');
            }
        });
    }
    function excel(){
        var filterPengirim = $('#filterPengirim').val();
        var filterPenerima = $('#filterPenerima').val();
        var filterTanggalDikirim = $('#filterTanggalDikirim').val();
        var filterTanggalDiterima = $('#filterTanggalDiterima').val();
        var filterBulan = $('#filterBulan').val();
        var filterKategori = $('#filterKategori').val();

        var desc = 'Cetak laporan tanpa filter';
        if(filterPengirim || filterPenerima || filterTanggalDikirim || filterTanggalDiterima || filterBulan || filterKategori){
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
                var url = "{{ route('laporan.mutasiinden-excel') }}" + '?' + $.param({
                    pengirim: filterPengirim,
                    penerima: filterPenerima,
                    tanggaldikirim: filterTanggalDikirim,
                    tanggalditerima: filterTanggalDiterima,
                    bulan: filterBulan,
                    kategori: filterKategori,
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