@extends('layouts.app-von')

@section('css')
    <style>
        .table-wrapper {
            display: flex;
            align-items: flex-start; /* Sesuaikan align jika diperlukan */
        }

        .table-container {
            display: flex;
            overflow-x: auto; /* Mengizinkan scroll horizontal */
            width: 100%; /* Mengambil lebar penuh kontainer */
        }

        .table-responsive {
            flex: 0 0 auto; /* Memastikan tabel pertama tidak menyusut */
        }

        .scrollable-table {
            overflow-x: auto; /* Scroll horizontal hanya untuk tabel ini */
            flex: 1 1 auto; /* Tabel kedua mengambil sisa ruang */
            min-width: 600px; /* Atur lebar minimum sesuai kebutuhan */
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

        thead th, tbody td {
            border: 1px solid #dee2e6; /* Border pada sel */
            padding: 0.75rem;
            text-align: center;
        }

        .sticky-col {
            position: sticky;
            background-color: rgb(0, 0, 0);
            color: white;
            z-index: 2; /* Agar tetap di atas */
        }

        .first-col {
            left: 0;
        }

        .second-col {
            left: 50px; /* Sesuaikan dengan lebar kolom sebelumnya */
        }

        .third-col {
            left: 100px; /* Sesuaikan dengan lebar kolom sebelumnya */
        }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Laporan Bunga Datang</h4>
                    </div>
                    <div class="page-btn">
                        {{-- <button class="btn btn-outline-danger" style="height: 2.5rem; padding: 0.5rem 1rem; font-size: 1rem;" onclick="pdf()">
                            <img src="/assets/img/icons/pdf.svg" alt="PDF" style="height: 1rem;"/> PDF
                        </button> --}}
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
                            <!-- <div class="col-lg col-sm-6 col-12">
                                <select id="filterGallery" name="filterGallery" class="form-control" title="Gallery">
                                    <option value="">Pilih Lokasi</option>
                                    @foreach ($greenhouse as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == request()->input('gallery') ? 'selected' : '' }}>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div> -->
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
                                <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('laporan.bunga_datang') }}" class="btn btn-info">Filter</a>
                                <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('laporan.bunga_datang') }}" class="btn btn-warning">Clear</a>
                            </div>
                        </div>
                    </row>
                </div>
                <!-- <div class="table-wrapper"> -->
                    <!-- <div class="table-container"> -->
                        <div class="table-responsive">
                            <table class="table" id="datanew">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle sticky-col first-col" style="width: 20%">Bulan</th>
                                        <th class="text-center align-middle sticky-col second-col" style="width: 20%">Lokasi</th>
                                        <th class="text-center align-middle sticky-col third-col" style="width: 20%">Supplier</th>
                                        <th class="text-center align-middle sticky-col fourth-col" style="width: 20%">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @php
                                    $previousNoDo = '';
                                @endphp
                                    @foreach ($groupedData->groupBy('lokasi_id') as $lokasiId => $items)
                                        @foreach ($items as $item)
                                        @if ($loop->first)
                                            <tr>
                                                @if( \Carbon\Carbon::parse($listDate[0])->locale('id')->translatedFormat('F') != $previousNoDo)
                                                    <td class="text-center sticky-col" rowspan="{{ count($item) }}">{{ \Carbon\Carbon::parse($listDate[0])->locale('id')->translatedFormat('F') }}</td>
                                                @endif
                                                <td class="text-center sticky-col" rowspan="{{  $items->count() }}">{{ $item['lokasi_name'] }}</td>
                                                <td>{{ $item['supplier_name'] }}</td>
                                                <td>{{ $item['total_masuk'] }}</td>
                                            </tr>
                                        @else
                                             <tr>
                                                <td>{{ $item['supplier_name'] }}</td>
                                                <td>{{ $item['total_masuk'] }}</td>
                                             </tr>   
                                        @endif
                                        @php
                                            $previousNoDo = \Carbon\Carbon::parse($listDate[0])->locale('id')->translatedFormat('F');
                                        @endphp
                                        @endforeach
                                    @endforeach
                                </tbody>
                                <tbody style="border-top: 2px solid #000;">
                                    @foreach ($groupedData->groupBy('lokasi_id') as $lokasiId => $items)
                                    @foreach ($items as $item)
                                    @if ($loop->first)
                                        <tr>
                                        @if( 1 != $previousNoDo)
                                                <td rowspan="{{ count($item) }}" colspan="2">Total Kedatangan</td>
                                        @endif
                                            <td>{{ $item['supplier_name'] }}</td>
                                            <td>{{ $items->sum('total_masuk') }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>{{ $item['supplier_name'] }}</td>
                                            <td>{{ $items->sum('total_masuk') }}</td>
                                        </tr>
                                    @endif
                                    @php
                                        $previousNoDo = 1;
                                    @endphp
                                    @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    <!-- </div> -->
                <!-- </div>                            -->
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
        // function pdf(){
        //     var filterGallery = $('#filterGallery').val();
        //     var filterBulan = $('#filterBulan').val();
        //     var filterTahun = $('#filterTahun').val();

        //     var desc = 'Cetak laporan tanpa filter';
        //     if(filterGallery || filterBulan || filterTahun){
        //         desc = 'cetak laporan dengan filter';
        //     }
            
        //     Swal.fire({
        //         title: 'Cetak PDF?',
        //         text: desc,
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#3085d6',
        //         cancelButtonColor: '#d33',
        //         confirmButtonText: 'Cetak',
        //         cancelButtonText: 'Batal'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             var url = "{{ route('laporan.stok_pusat-pdf') }}" + '?' + $.param({
        //                 gallery: filterGallery,
        //                 bulan: filterBulan,
        //                 tahun: filterTahun,
        //             });
                    
        //             window.open(url, '_blank');
        //         }
        //     });
        // }
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
                    var url = "{{ route('laporan.bunga_datang-excel') }}" + '?' + $.param({
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