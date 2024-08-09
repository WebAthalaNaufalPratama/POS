@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Laporan Stok Inden</h4>
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
                                <select id="filterSupplier" name="filterSupplier" class="form-control" title="Supplier">
                                    <option value="">Pilih Supplier</option>
                                    @foreach ($supplier as $key => $value)
                                        <option value="{{ $key }}" {{ $key == request()->input('supplier') ? 'selected' : '' }}>{{ $value }}</option>
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
                                <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('laporan.stok_inden') }}" class="btn btn-info">Filter</a>
                                <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('laporan.stok_inden') }}" class="btn btn-warning">Clear</a>
                            </div>
                        </div>
                    </row>
                </div>
                <div class="table-responsive">
                    <table class="table datanew" id="dataTable">
                        <thead>
                            <tr>
                                <th class="d-none"></th>
                                <th>BULAN</th>
                                @foreach ($produk as $key1 => $value1)
                                    <th class="text-center">{{ $value1 }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $value)
                            <tr>
                                <td class="d-none">{{ $loop->iteration }}</td>
                                <td>{{ $key }}</td>
                                @foreach ($value as $key1 => $item1)
                                    <td class="text-center">{{ $item1 }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>TOTAL</th>
                                @foreach ($produk as $key1 => $value1)
                                    <th class="text-center">{{ $total[$value1] ?? 0 }}</th>
                                @endforeach
                            </tr>
                            <tr>
                                <th>TOTAL SISA BUNGA</th>
                                <th class="text-center" colspan="{{ count($produk) }}">{{ $totalSisaBunga }}</th>
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
            var filterSupplier = $('#filterSupplier').val();
            var filterTahun = $('#filterTahun').val();

            var desc = 'Cetak laporan tanpa filter';
            if(filterSupplier || filterTahun){
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
                    var url = "{{ route('laporan.stok_inden-pdf') }}" + '?' + $.param({
                        supplier: filterSupplier,
                        tahun: filterTahun,
                    });
                    
                    window.open(url, '_blank');
                }
            });
        }
        function excel(){
            var filterSupplier = $('#filterSupplier').val();
            var filterTahun = $('#filterTahun').val();

            var desc = 'Cetak laporan tanpa filter';
            if(filterSupplier || filterTahun){
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
                    var url = "{{ route('laporan.stok_inden-excel') }}" + '?' + $.param({
                        supplier: filterSupplier,
                        tahun: filterTahun,
                    });
                    
                    window.location.href = url;
                }
            });
        }
    </script>
@endsection