@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Laporan Mutasi</h4>
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
                <row class="col-lg-12 col-sm-12">
                    <div class="row">
                        <div class="col-lg col-sm-6 col-12">
                            <select id="filterPengirim" name="filterPengirim" class="form-control" title="Pengirim">
                                <option value="">Pilih Pengirim</option>
                                @foreach ($galleries as $item)
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
                            <select id="filterJenisMutasi" name="filterJenisMutasi" class="form-control" title="JenisMutasi">
                                <option value="">Pilih Mutasi</option>
                                <option value="MGO%" {{ 'MGO%' == request()->input('jenismutasi') ? 'selected' : '' }}>Mutasi Gallery Outlet</option>
                                <option value="MOG%" {{ 'MOG%' == request()->input('jenismutasi') ? 'selected' : '' }}>Mutasi Outlet Gallery</option>
                                <option value="MGG%" {{ 'MGG%' == request()->input('jenismutasi') ? 'selected' : '' }}>Mutasi GH/Pusat</option>
                                <option value="MGA%" {{ 'MGA%' == request()->input('jenismutasi') ? 'selected' : '' }}>Mutasi Gallery Gallery</option>
                            </select>
                        </div>
                        <div class="col-lg col-sm-6 col-12">
                            <input type="date" class="form-control" name="filterTanggalKirim" id="filterTanggalKirim" value="{{ request()->input('tanggalkirim') }}" title="Tanggal Pengiriman">
                        </div>
                        <div class="col-lg col-sm-6 col-12">
                            <input type="date" class="form-control" name="filterTanggalDiterima" id="filterTanggalDiterima" value="{{ request()->input('tanggalditerima') }}" title="Tanggal Penerima">
                        </div>
                        <div class="col-lg col-sm-6 col-12">
                            <input type="date" class="form-control" name="filterDateStart" id="filterDateStart" value="{{ request()->input('dateStart') }}" title="Awal Mutasi">
                        </div>
                        <div class="col-lg col-sm-6 col-12">
                            <input type="date" class="form-control" name="filterDateEnd" id="filterDateEnd" value="{{ request()->input('dateEnd') }}" title="Akhir Mutasi">
                        </div>
                        <div class="col-lg col-sm-6 col-12">
                            <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('laporan.mutasi') }}" class="btn btn-info">Filter</a>
                            <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('laporan.mutasi') }}" class="btn btn-warning">Clear</a>
                        </div>
                    </div>
                </row>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Mutasi</th>
                                <th>Pengirim</th>
                                <th>Penerima</th>
                                <th>Tanggal Pengiriman</th>
                                <th>Tanggal Diterima</th>
                                <th>Nama Produk</th>
                                <th>Jumlah Pengiriman</th>
                                <th>Kondisi Pengiriman</th>
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
                            @foreach ($combinedData as $no_do => $data)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $data['no_mutasi'] }}</td>
                                <td>{{ $data['lokasi_pengirim'] }}</td>
                                <td>{{ $data['lokasi_penerima'] }}</td>
                                <td>{{ $data['tanggal_pengiriman'] }}</td>
                                <td>{{ $data['tanggal_diterima'] }}</td>
                                <td>
                                    <table>
                                        @if(substr($data['no_mutasi'], 0, 3) != 'MGO')
                                            @foreach ($data['produk_jual'] as $index => $produkJual)
                                                @foreach ($produkJual['komponen'] as $komponenIndex => $komponen)
                                                <tr>
                                                    <td>{{ $komponen['nama_produk'] }}</td>
                                                </tr>
                                                @endforeach
                                            @endforeach
                                        @else
                                            @foreach ($data['produk_jual'] as $index => $produkJual)
                                            <tr>
                                                <td>{{ $produkJual['nama_produkjual'] }}</td>
                                            </tr>
                                            @endforeach
                                        @endif
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
                                        @if(substr($data['no_mutasi'], 0, 3) != 'MGO')
                                            @foreach ($data['produk_jual'] as $index => $produkJual)
                                                @foreach ($produkJual['komponen'] as $komponenIndex => $komponen)
                                                <tr>
                                                    <td>{{ $komponen['kondisi'] }}</td>
                                                </tr>
                                                @endforeach
                                            @endforeach
                                        @else
                                        <tr>
                                            <td>Tidak Ada Kondisi</td>
                                        </tr>
                                        @endif
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($data['produk_jual'] as $index => $produkJual)
                                        <tr>
                                            <td>{{ $produkJual['jumlah_diterima'] }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @if(substr($data['no_mutasi'], 0, 3) != 'MGO')
                                            @foreach ($data['produk_jual'] as $index => $produkJual)
                                            @foreach ($produkJual['komponen'] as $komponenIndex => $komponen)
                                            <tr>
                                                <td>{{ $komponen['kondisi_diterima']->nama }}</td>
                                            </tr>
                                            @endforeach
                                            @endforeach
                                        @else
                                        <tr>
                                            <td>Tidak Ada Kondisi</td>
                                        </tr>
                                        @endif
                                    </table>
                                </td>

                                <td>{{ $data['biaya_pengiriman'] }}</td>
                                <td>{{ $data['rekening'] }}</td>
                                <td>{{ $data['total_biaya'] }}</td>
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

        var JenisMutasi = $('#filterJenisMutasi').val();
        if (JenisMutasi) {
            var filterJenisMutasi = 'jenismutasi=' + JenisMutasi;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterJenisMutasi;
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
    function pdf() {
        var filterPengirim = $('#filterPengirim').val();
        var filterPenerima = $('#filterPenerima').val();
        var filterJenisMutasi = $('#filterJenisMutasi').val();
        var filterTanggalkirim = $('#filterTanggalkirim').val();
        var filterTanggalDiterima = $('#filterTanggalDiterima').val();
        var filterDateStart = $('#filterDateStart').val();
        var filterDateEnd = $('#filterDateEnd').val();

        var desc = 'Cetak laporan tanpa filter';
        if (filterPengirim || filterPenerima || filterJenisMutasi || filterTanggalkirim || filterTanggalDiterima || filterDateStart || filterDateEnd) {
            desc = 'Cetak laporan dengan filter';
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
                var url = "{{ route('laporan.mutasi-pdf') }}" + '?' + $.param({
                    pengirim: encodeURIComponent(filterPengirim),
                    penerima: encodeURIComponent(filterPenerima),
                    jenis_mutasi: encodeURIComponent(filterJenisMutasi),
                    tanggal_kirim: encodeURIComponent(filterTanggalkirim),
                    tanggal_diterima: encodeURIComponent(filterTanggalDiterima),
                    dateStart: encodeURIComponent(filterDateStart),
                    dateEnd: encodeURIComponent(filterDateEnd),
                });
                
                window.open(url, '_blank');
            }
        });
    }

    function excel(){
        var filterPengirim = $('#filterPengirim').val();
        var filterPenerima = $('#filterPenerima').val();
        var filterJenisMutasi = $('#filterJenisMutasi').val();
        var filterTanggalkirim = $('#filterTanggalkirim').val();
        var filterTanggalDiterima = $('#filterTanggalDiterima').val();
        var filterDateStart = $('#filterDateStart').val();
        var filterDateEnd = $('#filterDateEnd').val();

        var desc = 'Cetak laporan tanpa filter';
        if(filterPengirim || filterPenerima || filterJenisMutasi || filterTanggalkirim || filterTanggalDiterima || filterDateStart || filterDateEnd){
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
                var url = "{{ route('laporan.mutasi-excel') }}" + '?' + $.param({
                    pengirim: encodeURIComponent(filterPengirim),
                    penerima: encodeURIComponent(filterPenerima),
                    jenis_mutasi: encodeURIComponent(filterJenisMutasi),
                    tanggal_kirim: encodeURIComponent(filterTanggalkirim),
                    tanggal_diterima: encodeURIComponent(filterTanggalDiterima),
                    dateStart: encodeURIComponent(filterDateStart),
                    dateEnd: encodeURIComponent(filterDateEnd),
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