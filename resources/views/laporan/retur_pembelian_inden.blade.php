@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Laporan Retur Pembelian Inden</h4>
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
                                <input type="date" class="form-control" name="filterDateStart" id="filterDateStart" value="{{ request()->input('dateStart') }}" title="Awal Sewa">
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <input type="date" class="form-control" name="filterDateEnd" id="filterDateEnd" value="{{ request()->input('dateEnd') }}" title="Akhir Sewa">
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('laporan.retur_pembelian_inden') }}" class="btn btn-info">Filter</a>
                                <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('laporan.retur_pembelian_inden') }}" class="btn btn-warning">Clear</a>
                            </div>
                        </div>
                    </row>
                </div>
                <div class="table-responsive">
                    <table class="table datanew" id="dataTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Komplain</th>
                                <th>No Retur</th>
                                <th>No Mutasi</th>
                                <th>Tipe Komplain</th>
                                <th>Alasan</th>
                                <th>Kode Inden</th>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>QTY</th>
                                <th>Total</th>
                                <th>Supplier</th>
                                <th>Tujuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($returs as $retur)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ tanggalindo($retur->tgl_dibuat) }}</td>
                                <td>{{ $retur->no_retur }}</td>
                                <td>{{ $retur->mutasiinden->no_mutasi}}</td>
                                <td>{{ $retur->tipe_komplain}}
                                    {{-- @if ( $retur->tipe_komplain == "Refund" && $retur->sisa_refund == 0)
                                        | Lunas
                                    @elseif( $retur->tipe_komplain == "Refund" && $retur->sisa_refund !== 0)
                                        | Belum Lunas
                                    @endif
                                    @if ( $retur->tipe_komplain == "Diskon" && $retur->mutasiinden->sisa_bayar == 0)
                                        | Lunas
                                    @elseif( $retur->tipe_komplain == "Diskon" && $retur->mutasiinden->sisa_bayar !== 0)
                                        | Belum Lunas
                                    @endif --}}

                                </td>
                                <td>
                                    <ul>
                                        @foreach($retur->produkreturinden as $produkretur)
                                            <li>{{ $produkretur->alasan }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        @foreach($retur->produkreturinden as $produkretur)
                                            <li>{{ $produkretur->produk->produk->kode_produk_inden }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        @foreach($retur->produkreturinden as $produkretur)
                                            <li>{{ $produkretur->produk->produk->produk->nama }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        @foreach($retur->produkreturinden as $produkretur)
                                            <li>{{ formatRupiah($produkretur->harga_satuan) }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        @foreach($retur->produkreturinden as $produkretur)
                                            <li>{{ $produkretur->jml_diretur }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>{{ formatRupiah($retur->refund) }}</td>
                                <td>{{ $retur->mutasiinden->supplier->nama }}</td>
                                <td>{{ $retur->mutasiinden->lokasi->nama }}</td>
                               
                               
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
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function(){
            $('select[id^=filter]').select2();
            $('#filterBtn').click(function(){
                var baseUrl = $(this).data('base-url');
                var urlString = baseUrl;
                var first = true;
                var symbol = '';

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
        });
        function pdf(){
            var filterDateStart = $('#filterDateStart').val();
            var filterDateEnd = $('#filterDateEnd').val();

            var desc = 'Cetak laporan tanpa filter';
            if(filterDateStart || filterDateEnd){
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
                    var url = "{{ route('laporan.retur_pembelian_inden-pdf') }}" + '?' + $.param({
                        dateStart: filterDateStart,
                        dateEnd: filterDateEnd,
                    });
                    
                    window.open(url, '_blank');
                }
            });
        }
        function excel(){
            var filterDateStart = $('#filterDateStart').val();
            var filterDateEnd = $('#filterDateEnd').val();

            var desc = 'Cetak laporan tanpa filter';
            if(filterDateStart || filterDateEnd){
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
                    var url = "{{ route('laporan.retur_pembelian_inden-excel') }}" + '?' + $.param({
                        dateStart: filterDateStart,
                        dateEnd: filterDateEnd,
                    });
                    
                    window.location.href = url;
                }
            });
        }
    </script>
@endsection