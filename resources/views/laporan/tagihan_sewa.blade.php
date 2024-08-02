@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Laporan Tagihan Sewa</h4>
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
                                <input type="date" class="form-control" name="filterDateStart" id="filterDateStart" value="{{ request()->input('dateStart') }}" title="Awal Sewa">
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <input type="date" class="form-control" name="filterDateEnd" id="filterDateEnd" value="{{ request()->input('dateEnd') }}" title="Akhir Sewa">
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('laporan.tagihan_sewa') }}" class="btn btn-info">Filter</a>
                                <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('laporan.tagihan_sewa') }}" class="btn btn-warning">Clear</a>
                            </div>
                        </div>
                    </row>
                </div>
                <div class="table-responsive">
                    <table class="table datanew" id="dataTable">
                        <thead>
                            <tr>
                                <th class="align-middle">No</th>
                                <th class="align-middle">No Kontrak</th>
                                <th class="align-middle">No Invoice</th>
                                <th class="align-middle">Customer</th>
                                <th class="align-middle">Tanggal Invoice</th>
                                <th class="align-middle">Total Nilai</th>
                                <th class="align-middle">Terbayar</th>
                                <th class="align-middle">Sisa Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->no_sewa }}</td>
                                <td>{{ $item->no_invoice }}</td>
                                <td>{{ $item->kontrak->customer->nama }}</td>
                                <td>{{ tanggalindo($item->tanggal_invoice) }}</td>
                                <td>{{ formatRupiah($item->total_tagihan) }}</td>
                                <td>{{ formatRupiah($item->terbayar) }}</td>
                                <td>{{ formatRupiah($item->sisa_bayar) }}</td>
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
                    var url = "{{ route('laporan.tagihan_sewa-pdf') }}" + '?' + $.param({
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
                    var url = "{{ route('laporan.tagihan_sewa-excel') }}" + '?' + $.param({
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