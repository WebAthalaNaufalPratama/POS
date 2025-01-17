@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Laporan Pembelian Inden</h4>
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
                                    @foreach ($supplier as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == request()->input('supplier') ? 'selected' : '' }}>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- <div class="col-lg col-sm-6 col-12">
                                <select id="filterGallery" name="filterGallery" class="form-control" title="Gallery">
                                    <option value="">Pilih Gallery</option>
                                    @foreach ($galleries as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == request()->input('gallery') ? 'selected' : '' }}>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                            <div class="col-lg col-sm-6 col-12">
                                <input type="date" class="form-control" name="filterDateStart" id="filterDateStart" value="{{ request()->input('dateStart') }}" title="Awal Sewa">
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <input type="date" class="form-control" name="filterDateEnd" id="filterDateEnd" value="{{ request()->input('dateEnd') }}" title="Akhir Sewa">
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('laporan.pembelian_inden') }}" class="btn btn-info">Filter</a>
                                <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('laporan.pembelian_inden') }}" class="btn btn-warning">Clear</a>
                            </div>
                        </div>
                    </row>
                </div>
                <div class="table-responsive">
                    <table class="table datanew" id="dataTable">
                        <thead>
                            <tr>
                                <th class="align-middle">No</th>
                                <th class="align-middle">No Invoice</th>
                                <th class="align-middle">Tanggal</th>
                                <th class="align-middle">Bulan Inden</th>
                                <th class="align-middle">List Barang</th>
                                <th class="align-middle">Harga</th>
                                <th class="align-middle">Diskon</th>
                                {{-- <th class="align-middle">Gallery</th> --}}
                                <th class="align-middle">Supplier</th>
                                <th class="align-middle">QTY</th>
                                <th class="align-middle">Harga Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->no_inv }}</td>
                                <td>{{ tanggalindo($item->tgl_inv) }}</td>
                                <td>{{ $item->poinden->bulan_inden }}</td>
                                
                                <td>
                                    <table>
                                        @foreach ($item->poinden->produkbeli as $produk)
                                            <tr>
                                                <td>{{ $produk->produk->nama }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($item->poinden->produkbeli as $produk)
                                            <tr>
                                                <td>{{ formatRupiah($produk->harga) }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($item->poinden->produkbeli as $produk)
                                            <tr>
                                                <td>{{ formatRupiah($produk->diskon) }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                                {{-- <td>{{ $item->poinden->lokasi->nama }}</td> --}}
                                <td>{{ $item->poinden->supplier->nama }}</td>
                                <td>
                                    <table>
                                        @foreach ($item->poinden->produkbeli as $produk)
                                            <tr>
                                                <td>{{ $produk->jumlahInden }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($item->poinden->produkbeli as $produk)
                                            <tr>
                                                <td>{{ formatRupiah($produk->totalharga) }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
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
            var filterSupplier = $('#filterSupplier').val();
            var filterGallery = $('#filterGallery').val();
            var filterDateStart = $('#filterDateStart').val();
            var filterDateEnd = $('#filterDateEnd').val();

            var desc = 'Cetak laporan tanpa filter';
            if(filterSupplier || filterGallery || filterDateStart || filterDateEnd){
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
                    var url = "{{ route('laporan.pembelian_inden-pdf') }}" + '?' + $.param({
                        supplier: filterSupplier,
                        gallery: filterGallery,
                        dateStart: filterDateStart,
                        dateEnd: filterDateEnd,
                    });
                    
                    window.open(url, '_blank');
                }
            });
        }
        function excel(){
            var filterSupplier = $('#filterSupplier').val();
            var filterGallery = $('#filterGallery').val();
            var filterMasaSewa = $('#filterMasaSewa').val();
            var filterStatus = $('#filterStatus').val();
            var filterDateStart = $('#filterDateStart').val();
            var filterDateEnd = $('#filterDateEnd').val();

            var desc = 'Cetak laporan tanpa filter';
            if(filterSupplier || filterGallery || filterDateStart || filterDateEnd){
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
                    var url = "{{ route('laporan.pembelian_inden-excel') }}" + '?' + $.param({
                        supplier: filterSupplier,
                        gallery: filterGallery,
                        dateStart: filterDateStart,
                        dateEnd: filterDateEnd,
                    });
                    
                    window.location.href = url;
                }
            });
        }
    </script>
@endsection