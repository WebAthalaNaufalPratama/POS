@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Invoice Pembelian</h4>
                    </div>
                    <div class="page-btn">
                        {{-- <a href="{{ route('pembelian.create') }}" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Invoice</a> --}}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row ps-2 pe-2">
                    <div class="col-sm-2 ps-0 pe-0">
                        <input type="date" class="form-control" name="filterDateStart" id="filterDateStart" value="{{ request()->input('dateStart') }}" title="Tanggal Awal">
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <input type="date" class="form-control" name="filterDateEnd" id="filterDateEnd" value="{{ request()->input('dateEnd') }}" title="Tanggal Akhir">
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterSupplier" name="filterSupplier" class="form-control" title="Supplier">
                            <option value="">Pilih Supplier</option>
                            @foreach ($supplierTrd as $item)
                                <option value="{{ $item->id }}" {{ $item->id == request()->input('supplier') ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterGallery" name="filterGallery" class="form-control" title="Gallery">
                            <option value="">Pilih Gallery</option>
                            @foreach ($galleryTrd as $item)
                                <option value="{{ $item->id }}" {{ $item->id == request()->input('gallery') ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterStatus" name="filterStatus" class="form-control" title="Status">
                            <option value="">Pilih Status</option>
                            <option value="Lunas" {{ request()->input('status') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                            <option value="Belum Lunas" {{ request()->input('status') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('invoicebeli.index') }}" class="btn btn-info">Filter</a>
                        <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('invoicebeli.index') }}" class="btn btn-warning">Clear</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Invoice</th>
                                <th>No PO</th>
                                <th>Supplier</th>
                                <th>lokasi</th>
                                <th>Tanggal Invoice</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Sisa Tagihan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                         <tbody>
                            @foreach ($invoices as $inv)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $inv->no_inv }}</td>
                                <td>{{ $inv->pembelian->no_po }}</td>
                                <td>{{ $inv->pembelian->supplier->nama }}</td>
                                <td>{{ $inv->pembelian->lokasi->nama}}</td>
                                <td>{{ $inv->tgl_inv }}</td>
                                <td>{{ formatRupiah($inv->total_tagihan) }}</td>
                                <td>
                                    @if ( $inv->sisa == 0)
                                        Lunas
                                    @else
                                        Belum Lunas
                                    @endif

                                </td>
                                <td>
                                {{ formatRupiah($inv->sisa) }}
                                </td>
                                <td class="text-center">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('returbeli.create', ['invoice' => $inv->id]) }}" class="dropdown-item"><img src="/assets/img/icons/return1.svg" class="me-2" alt="img">Retur</a>
                                        </li>
                                    </ul>
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
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Invoice Pembelian Inden</h4>
                    </div>
                    <div class="page-btn">
                        {{-- <a href="{{ route('pembelianinden.create') }}" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Invoice</a> --}}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row ps-2 pe-2">
                    <div class="col-sm-2 ps-0 pe-0">
                        <input type="date" class="form-control" name="filterDateStartInd" id="filterDateStartInd" value="{{ request()->input('dateStartInd') }}" title="Tanggal Awal">
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <input type="date" class="form-control" name="filterDateEndInd" id="filterDateEndInd" value="{{ request()->input('dateEndInd') }}" title="Tanggal Akhir">
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterSupplierInd" name="filterSupplierInd" class="form-control" title="Supplier">
                            <option value="">Pilih Supplier</option>
                            @foreach ($supplierInd as $item)
                                <option value="{{ $item->id }}" {{ $item->id == request()->input('supplierInd') ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterStatusInd" name="filterStatusInd" class="form-control" title="Status">
                            <option value="">Pilih Status</option>
                            <option value="Lunas" {{ request()->input('statusInd') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                            <option value="Belum Lunas" {{ request()->input('statusInd') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('invoicebeli.index') }}" class="btn btn-info">Filter</a>
                        <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('invoicebeli.index') }}" class="btn btn-warning">Clear</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Invoice</th>
                                <th>No PO</th>
                                <th>Supplier</th>
                                <th>Bulan Inden</th>
                                <th>Tanggal Invoice</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Sisa Tagihan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                         <tbody>
                            @foreach ($invoiceinden as $inv)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $inv->no_inv }}</td>
                                <td>{{ $inv->poinden->no_po }}</td>
                                <td>{{ $inv->poinden->supplier->nama }}</td>
                                <td>{{ $inv->poinden->bulan_inden}}</td>
                                <td>{{ $inv->tgl_inv}}</td>
                                <td>{{ formatRupiah($inv->total_tagihan) }}</td>
                                <td>
                                    @if ( $inv->sisa == 0)
                                    Lunas
                                    @else
                                    Belum Lunas
                                    @endif
                                </td>
                                <td>{{ formatRupiah($inv->sisa) }}</td>
                                <td></td>
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
    $(document).ready(function(){
        $('#filterSupplier, #filterGallery, #filterStatus').select2();
    });
    $('[id^=filterBtn]').click(function(){
        var baseUrl = $(this).data('base-url');
        var urlString = baseUrl;
        var first = true;
        var symbol = '';

        var supplier = $('#filterSupplier').val();
        if (supplier) {
            var filtersupplier = 'supplier=' + supplier;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filtersupplier;
        }

        var gallery = $('#filterGallery').val();
        if (gallery) {
            var filtergallery = 'gallery=' + gallery;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filtergallery;
        }

        var status = $('#filterStatus').val();
        if (status) {
            var filterstatus = 'status=' + status;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterstatus;
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


        var supplier = $('#filterSupplierInd').val();
        if (supplier) {
            var filtersupplier = 'supplierInd=' + supplier;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filtersupplier;
        }

        var status = $('#filterStatusInd').val();
        if (status) {
            var filterstatus = 'statusInd=' + status;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterstatus;
        }

        var dateStart = $('#filterDateStartInd').val();
        if (dateStart) {
            var filterDateStart = 'dateStartInd=' + dateStart;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterDateStart;
        }

        var dateEnd = $('#filterDateEndInd').val();
        if (dateEnd) {
            var filterDateEnd = 'dateEndInd=' + dateEnd;
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
    $('[id^=clearBtn]').click(function(){
        var baseUrl = $(this).data('base-url');
        var url = window.location.href;
        if(url.indexOf('?') !== -1){
            window.location.href = baseUrl;
        }
        return 0;
    });
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
@endsection