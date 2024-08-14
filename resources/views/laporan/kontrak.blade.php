@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Laporan Kontrak</h4>
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
                                <select id="filterCustomer" name="filterCustomer" class="form-control" title="Customer">
                                    <option value="">Pilih Customer</option>
                                    @foreach ($customer as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == request()->input('customer') ? 'selected' : '' }}>{{ $item->nama }}</option>
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
                                <select id="filterMasaSewa" name="filterMasaSewa" class="form-control" title="MasaSewa">
                                    <option value="">Masa Sewa</option>
                                    @foreach ($masa_sewa as $item)
                                        <option value="{{ $item }}" {{ $item == request()->input('masa_sewa') ? 'selected' : '' }}>{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <select id="filterStatus" name="filterStatus" class="form-control" title="Status">
                                    <option value="">Pilih Status</option>
                                    @foreach ($statuses as $item)
                                        <option value="{{ $item }}" {{ $item == request()->input('status') ? 'selected' : '' }}>{{ $item }}</option>
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
                                <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('laporan.kontrak') }}" class="btn btn-info">Filter</a>
                                <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('laporan.kontrak') }}" class="btn btn-warning">Clear</a>
                            </div>
                        </div>
                    </row>
                </div>
                <div class="table-responsive">
                    <table class="table datanew" id="dataTable">
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle">No</th>
                                <th rowspan="2" class="align-middle">Gallery</th>
                                <th rowspan="2" class="align-middle">Customer</th>
                                <th rowspan="2" class="align-middle">Masa Sewa</th>
                                <th colspan="2" class="text-center">Tanggal Kontrak</th>
                                <th rowspan="2" class="align-middle">Produk Sewa</th>
                                <th rowspan="2" class="align-middle">Jumlah</th>
                                <th rowspan="2" class="align-middle">Harga Satuan</th>
                                <th rowspan="2" class="align-middle">Total Harga</th>
                                <th rowspan="2" class="align-middle">Diskon</th>
                                <th rowspan="2" class="align-middle">Total Harga Akhir</th>
                                <th rowspan="2" class="align-middle">PPN</th>
                                <th rowspan="2" class="align-middle">PPH</th>
                                <th rowspan="2" class="align-middle">Total Yang Diterima</th>
                                <th rowspan="2" class="align-middle">Status</th>
                            </tr>
                            <tr>
                                <th>Awal Sewa</th>
                                <th>Akhir Sewa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->lokasi->nama }}</td>
                                <td>{{ $item->customer->nama }}</td>
                                <td>{{ $item->masa_sewa }} bulan</td>
                                <td>{{ tanggalindo($item->tanggal_mulai) }}</td>
                                <td>{{ tanggalindo($item->tanggal_selesai) }}</td>
                                <td>
                                    <table>
                                        @foreach ($item->produk as $produk)
                                        <tr>
                                            <td>{{ $produk->produk->nama }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($item->produk as $produk)
                                        <tr>
                                            <td>{{ $produk->jumlah }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($item->produk as $produk)
                                        <tr>
                                            <td>{{ formatRupiah($produk->harga) }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>{{ formatRupiah($item->total_sebelum_diskon) }}</td>
                                <td>{{ formatRupiah($item->total_promo) }}</td>
                                <td>{{ formatRupiah($item->subtotal) }}</td>
                                <td>{{ formatRupiah($item->ppn_nominal) }} ({{ $item->ppn_persen }}%)</td>
                                <td>{{ formatRupiah($item->pph_nominal) }} ({{ $item->pph_persen }}%)</td>
                                <td>{{ formatRupiah($item->total_harga) }}</td>
                                <td>{{ $item->status_kontrak }}</td>
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

                var customer = $('#filterCustomer').val();
                if (customer) {
                    var filterCustomer = 'customer=' + customer;
                    if (first == true) {
                        symbol = '?';
                        first = false;
                    } else {
                        symbol = '&';
                    }
                    urlString += symbol;
                    urlString += filterCustomer;
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

                var MasaSewa = $('#filterMasaSewa').val();
                if (MasaSewa) {
                    var filterMasaSewa = 'masa_sewa=' + MasaSewa;
                    if (first == true) {
                        symbol = '?';
                        first = false;
                    } else {
                        symbol = '&';
                    }
                    urlString += symbol;
                    urlString += filterMasaSewa;
                }

                var Status = $('#filterStatus').val();
                if (Status) {
                    var filterStatus = 'status=' + Status;
                    if (first == true) {
                        symbol = '?';
                        first = false;
                    } else {
                        symbol = '&';
                    }
                    urlString += symbol;
                    urlString += filterStatus;
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
            var filterCustomer = $('#filterCustomer').val();
            var filterGallery = $('#filterGallery').val();
            var filterMasaSewa = $('#filterMasaSewa').val();
            var filterStatus = $('#filterStatus').val();
            var filterDateStart = $('#filterDateStart').val();
            var filterDateEnd = $('#filterDateEnd').val();

            var desc = 'Cetak laporan tanpa filter';
            if(filterCustomer || filterGallery || filterMasaSewa || filterStatus || filterDateStart || filterDateEnd){
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
                    var url = "{{ route('laporan.kontrak-pdf') }}" + '?' + $.param({
                        customer: filterCustomer,
                        gallery: filterGallery,
                        masa_sewa: filterMasaSewa,
                        status: filterStatus,
                        dateStart: filterDateStart,
                        dateEnd: filterDateEnd,
                    });
                    
                    window.open(url, '_blank');
                }
            });
        }
        function excel(){
            var filterCustomer = $('#filterCustomer').val();
            var filterGallery = $('#filterGallery').val();
            var filterMasaSewa = $('#filterMasaSewa').val();
            var filterStatus = $('#filterStatus').val();
            var filterDateStart = $('#filterDateStart').val();
            var filterDateEnd = $('#filterDateEnd').val();

            var desc = 'Cetak laporan tanpa filter';
            if(filterCustomer || filterGallery || filterMasaSewa || filterStatus || filterDateStart || filterDateEnd){
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
                    var url = "{{ route('laporan.kontrak-excel') }}" + '?' + $.param({
                        customer: filterCustomer,
                        gallery: filterGallery,
                        masa_sewa: filterMasaSewa,
                        status: filterStatus,
                        dateStart: filterDateStart,
                        dateEnd: filterDateEnd,
                    });
                    
                    window.location.href = url;
                }
            });
        }
    </script>
@endsection