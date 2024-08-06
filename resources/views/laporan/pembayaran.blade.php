@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Laporan Pembayaran</h4>
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
                                <select id="filterMetode" name="filterMetode" class="form-control" title="Metode">
                                    <option value="">Pilih Metode Bayar</option>
                                    <option value="transfer" {{ 'transfer' == request()->input('metode') ? 'selected' : '' }}>Transfer</option>
                                    <option value="cash" {{ 'cash' == request()->input('metode') ? 'selected' : '' }}>Cash</option>
                                </select>
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <select id="filterRekening" name="filterRekening" class="form-control" title="Rekening">
                                    <option value="">Pilih Nama Akun</option>
                                    @foreach ($rekenings as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == request()->input('rekening') ? 'selected' : '' }}>{{ $item->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <input type="date" class="form-control" name="filterDateStart" id="filterDateStart" value="{{ request()->input('dateStart') }}" title="Awal Sewa">
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <input type="date" class="form-control" name="filterDateEnd" id="filterDateEnd" value="{{ request()->input('dateEnd') }}" title="Akhir Sewa">
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('laporan.pembayaran') }}" class="btn btn-info">Filter</a>
                                <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('laporan.pembayaran') }}" class="btn btn-warning">Clear</a>
                            </div>
                        </div>
                    </row>
                </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Cara Pembayaran</th>
                                <th>Nama Akun</th>
                                <th>Jumlah Transaksi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($duit as $rekening_id => $total_nominal)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>
                                        {{ $pembayaran->firstWhere('rekening_id', $rekening_id)->cara_bayar ?? 'Cash' }} @if(!empty($rekening_id))({{($pembayaran->firstWhere('rekening_id', $rekening_id)->rekening->bank)}})@endif
                                </td>
                                <td>
                                    @if(!empty($rekening_id))
                                        {{ $pembayaran->firstWhere('rekening_id', $rekening_id)->rekening->nama_akun ?? 'Unknown' }}
                                    @else
                                        Pembayaran Cash
                                    @endif
                                </td>
                                <td>{{ number_format($total_nominal, 2) }}</td>
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
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $('#filterBtn').click(function(){
        var baseUrl = $(this).data('base-url');
        var urlString = baseUrl;
        var first = true;
        var symbol = '';

        var metode = $('#filterMetode').val();
        if (metode) {
            var filterMetode = 'metode=' + metode;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterMetode;
        }

        var rekening = $('#filterRekening').val();
        if (rekening) {
            var filterRekening = 'rekening=' + rekening;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterRekening;
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
        var filterMetode = $('#filterMetode').val();
        var filterRekening = $('#filterRekening').val();
        var filterDateStart = $('#filterDateStart').val();
        var filterDateEnd = $('#filterDateEnd').val();

        var desc = 'Cetak laporan tanpa filter';
        if(filterMetode || filterRekening || filterDateStart || filterDateEnd){
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
                var url = "{{ route('laporan.pembayaran-pdf') }}" + '?' + $.param({
                    metode: filterMetode,
                    rekening: filterRekening,
                    dateStart: filterDateStart,
                    dateEnd: filterDateEnd,
                });
                
                window.open(url, '_blank');
            }
        });
    }
    function excel(){
        var filterMetode = $('#filterMetode').val();
        var filterRekening = $('#filterRekening').val();
        var filterDateStart = $('#filterDateStart').val();
        var filterDateEnd = $('#filterDateEnd').val();

        var desc = 'Cetak laporan tanpa filter';
        if(filterMetode || filterRekening || filterDateStart || filterDateEnd){
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
                var url = "{{ route('laporan.pembayaran-excel') }}" + '?' + $.param({
                    metode: filterMetode,
                    rekening: filterRekening,
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