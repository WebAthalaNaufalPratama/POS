@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Laporan Kas Pusat</h4>
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
                                <select id="filterBulan" name="filterBulan" class="form-control" title="Bulan">
                                    <option value="">Pilih Bulan</option>
                                    @foreach ($bulan as $key => $value)
                                        <option value="{{ $key }}" {{ $key == request()->input('bulan') ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <select id="filterTahun" name="filterTahun" class="form-control" title="Tahun">
                                    <option value="">Pilih Tahun</option>
                                    @foreach ($tahun as $item)
                                        <option value="{{ $item }}" {{ $item == request()->input('tahun') ? 'selected' : '' }}>{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('laporan.kas_pusat') }}" class="btn btn-info">Filter</a>
                                <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('laporan.kas_pusat') }}" class="btn btn-warning">Clear</a>
                            </div>
                        </div>
                    </row>
                </div>
                <div class="table-responsive">
                    <table class="table datanew" id="dataTable">
                        <thead>
                            <tr>
                                <th class="d-none"></th>
                                <th class="align-middle">Periode</th>
                                <th class="align-middle text-center">Tanggal</th>
                                <th class="align-middle">Keterangan</th>
                                <th class="align-middle">Operasional</th>
                                <th class="align-middle">Debit</th>
                                <th class="align-middle">Kredit</th>
                                <th class="align-middle">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="d-none">0</td>
                                <td>{{ $thisMonth }} {{ $thisYear }}</td>
                                <td class="text-center">01</td>
                                <td>Saldo</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{ formatRupiah($saldo) }}</td>
                            </tr>
                            @foreach ($data as $index => $item)
                            <tr>
                                <td class="d-none">{{ $index + 1 }}</td>
                                <td></td>
                                <td class="text-center">{{ $item->dateNumber }}</td>
                                <td>{{ $item->keterangan }}</td>
                                @if($item->lokasi_penerima != null)
                                    <td>{{$item->lok_penerima->operasional->nama }}</td>
                                    <td>{{ formatRupiah($item->nominal) }}</td>
                                    <td></td>
                                @elseif($item->lokasi_pengirim != null)
                                    <td>{{$item->lok_pengirim->operasional->nama }}</td>
                                    <td></td>
                                    <td>{{ formatRupiah($item->nominal) }}</td>
                                @else
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                @endif
                                <td>{{ formatRupiah($item->saldo) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" class="text-end">Saldo Rekening</td>
                                <td>{{ formatRupiah($saldoRekening) }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end">Saldo Cash</td>
                                <td>{{ formatRupiah($saldoCash) }}</td>
                            </tr>
                        </tfoot>
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

                var bulan = $('#filterBulan').val();
                if (bulan) {
                    var filterBulan = 'bulan=' + bulan;
                    if (first == true) {
                        symbol = '?';
                        first = false;
                    } else {
                        symbol = '&';
                    }
                    urlString += symbol;
                    urlString += filterBulan;
                }

                var tahun = $('#filterTahun').val();
                if (tahun) {
                    var filterTahun = 'tahun=' + tahun;
                    if (first == true) {
                        symbol = '?';
                        first = false;
                    } else {
                        symbol = '&';
                    }
                    urlString += symbol;
                    urlString += filterTahun;
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
            var filterGallery = $('#filterGallery').val();
            var filterBulan = $('#filterBulan').val();
            var filterTahun = $('#filterTahun').val();

            var desc = 'Cetak laporan tanpa filter';
            if(filterGallery || filterBulan || filterTahun){
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
                    var url = "{{ route('laporan.kas_pusat-pdf') }}" + '?' + $.param({
                        gallery: filterGallery,
                        bulan: filterBulan,
                        tahun: filterTahun,
                    });
                    
                    window.open(url, '_blank');
                }
            });
        }
        function excel(){
            var filterGallery = $('#filterGallery').val();
            var filterBulan = $('#filterBulan').val();
            var filterTahun = $('#filterTahun').val();

            var desc = 'Cetak laporan tanpa filter';
            if(filterGallery || filterBulan || filterTahun){
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
                    var url = "{{ route('laporan.kas_pusat-excel') }}" + '?' + $.param({
                        gallery: filterGallery,
                        bulan: filterBulan,
                        tahun: filterTahun,
                    });
                    
                    window.location.href = url;
                }
            });
        }
    </script>
@endsection