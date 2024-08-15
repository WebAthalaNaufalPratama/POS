@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Laporan Bunga Keluar</h4>
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
                                        <option value="{{ $key }}" {{ $key == request()->input('Bulan') ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <select id="filterTahun" name="filterTahun" class="form-control" title="Tahun">
                                    <option value="">Pilih Tahun</option>
                                    @foreach ($tahun as $item)
                                        <option value="{{ $item }}" {{ $item == request()->input('Tahun') ? 'selected' : '' }}>{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <select id="filterGallery" name="filterGallery" class="form-control" title="Gallery">
                                    <option value="">Pilih Gallery</option>
                                    @foreach ($galleries as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == request()->input('gallery') ? 'selected' : '' }}>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('laporan.bunga_keluar') }}" class="btn btn-info">Filter</a>
                                <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('laporan.bunga_keluar') }}" class="btn btn-warning">Clear</a>
                            </div>
                        </div>
                    </row>
                </div>
                <div class="table-responsive">
                    <table class="table datanew" id="dataTable">
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle">No</th>
                                <th rowspan="2" class="align-middle">Bulan</th>
                                <th rowspan="2" class="align-middle">Produk</th>
                                <th colspan="3" class="text-center align-middle">Kondisi</th>
                                <th rowspan="2" class="text-center align-middle">Total Keluar</th>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">Bagus</th>
                                <th class="text-center align-middle">Afkir</th>
                                <th class="text-center align-middle">Bonggol</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($list as $index => $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                @if($loop->first)
                                <td>{{ $periode }}</td>
                                @else
                                <td></td>
                                @endif
                                <td>{{ $item['nama'] }}</td>
                                <td class="text-center">{{ $item['baik'] }}</td>
                                <td class="text-center">{{ $item['afkir'] }}</td>
                                <td class="text-center">{{ $item['bonggol'] }}</td>
                                <td class="text-center">{{ $item['total'] }}</td>
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

                var Tahun = $('#filterTahun').val();
                if (Tahun) {
                    var filterTahun = 'Ttahun=' + Tahun;
                    if (first == true) {
                        symbol = '?';
                        first = false;
                    } else {
                        symbol = '&';
                    }
                    urlString += symbol;
                    urlString += filterTahun;
                }

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
            var filterBulan = $('#filterBulan').val();
            var filterGallery = $('#filterGallery').val();
            var filterTahun = $('#filterTahun').val();

            var desc = 'Cetak laporan tanpa filter';
            if(filterBulan || filterGallery || filterTahun){
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
                    var url = "{{ route('laporan.bunga_keluar-pdf') }}" + '?' + $.param({
                        bulan: filterBulan,
                        gallery: filterGallery,
                        tahun: filterTahun,
                    });
                    
                    window.open(url, '_blank');
                }
            });
        }
        function excel(){
            var filterBulan = $('#filterBulan').val();
            var filterGallery = $('#filterGallery').val();
            var filterTahun = $('#filterTahun').val();
            var filterStatus = $('#filterStatus').val();
            var filterDateStart = $('#filterDateStart').val();
            var filterDateEnd = $('#filterDateEnd').val();

            var desc = 'Cetak laporan tanpa filter';
            if(filterBulan || filterGallery || filterTahun || filterStatus || filterDateStart || filterDateEnd){
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
                    var url = "{{ route('laporan.bunga_keluar-excel') }}" + '?' + $.param({
                        Bulan: filterBulan,
                        gallery: filterGallery,
                        tahun: filterTahun,
                    });
                    
                    window.location.href = url;
                }
            });
        }
    </script>
@endsection