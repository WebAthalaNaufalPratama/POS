@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Purchase Order</h4>
                    </div>
                    <div class="page-btn">
                        <a href="{{ route('pembelian.create') }}" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Pembelian</a>
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
                        <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('pembelian.index') }}" class="btn btn-info">Filter</a>
                        <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('pembelian.index') }}" class="btn btn-warning">Clear</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Purchase Order</th>
                                <th>Supplier</th>
                                <th>Tanggal Kirim</th>
                                <th>Tanggal Terima</th>
                                <th>No DO Supplier</th>
                                <th>Lokasi</th>
                                <th>Status Purchase</th>
                                {{-- <th>Status Admin</th>
                                <th>Status Finance</th> --}}
                                <th>Status Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datapos as $datapo)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $datapo->no_po }}</td>
                                <td>{{ $datapo->supplier->nama }}</td>
                                <td>{{ tanggalindo($datapo->tgl_kirim) }}</td>
                                <td>{{ tanggalindo($datapo->tgl_diterima)}}</td>
                                <td>{{ $datapo->no_do_suplier}}</td>
                                <td>{{ $datapo->lokasi->nama}}</td>
                                <td>{{ $datapo->status_dibuat}}</td>
                                {{-- <td>{{ $datapo->status_diterima}}</td>
                                <td>{{ $datapo->status_diperiksa}}</td> --}}

                                <td>
                                @if ($datapo->invoice !== null && $datapo->invoice->sisa == 0 )
                                    LUNAS
                                @elseif($datapo->invoice == null || $datapo->invoice->sisa !== 0  )
                                    BELUM LUNAS
                                @endif
                                </td>
                                
                            
                                <td class="text-center">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            @php
                                                $invoiceExists = $datainv->contains('pembelian_id', $datapo->id);
                                            @endphp
                                
                                            @if ($invoiceExists)
                                                <a href="{{ route('invoice.edit',['datapo' => $datapo->id, 'type' => 'pembelian']) }}" class="dropdown-item">
                                                    <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Pembayaran Invoice
                                                </a>
                                            @else
                                            <a href="{{ route('invoicebiasa.create', ['type' => 'pembelian', 'datapo' => $datapo->id]) }}" class="dropdown-item"><img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Create Invoice
                                                </a>
                                            @endif
                                        </li>
                                        <li>
                                            <a href="{{ route('pembelian.show', ['type' => 'pembelian','datapo' => $datapo->id]) }}" class="dropdown-item"><img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('pembelian.edit', ['type' => 'pembelian','datapo' => $datapo->id]) }}" class="dropdown-item"><img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                        </li>
                                        <li>
                                            {{-- <a href="#" class="dropdown-item" onclick="deleteData({{ $datapo->id }})"><img src="/assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a> --}}
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
                        <h4>Purchase Order Inden</h4>
                    </div>
                    <div class="page-btn">
                        <a href="{{ route('pembelianinden.create') }}" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Pembelian</a>
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
                        <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('pembelian.index') }}" class="btn btn-info">Filter</a>
                        <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('pembelian.index') }}" class="btn btn-warning">Clear</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Purchase Order</th>
                                <th>Supplier</th>
                                <th>Bulan Stok Inden</th>
                                <th>Status Purchase</th>
                                {{-- <th>Status Finance</th> --}}
                                <th>Status Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datainden as $inden)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $inden->no_po }}</td>
                                <td>{{ $inden->supplier->nama }}</td>
                                <td>{{ $inden->bulan_inden}}</td>
                                <td>{{ $inden->status_dibuat}}</td>
                                {{-- <td>{{ $inden->status_diperiksa}}</td> --}}
                                <td>
                                @if ($inden->invoice !== null && $inden->invoice->sisa == 0 )
                                    LUNAS
                                @elseif($inden->invoice == null || $inden->invoice->sisa !== 0  )
                                    BELUM LUNAS
                                @endif
                                </td>
                                
                            
                                <td class="text-center">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            @php
                                                $invoiceExists = $datainv->contains('poinden_id', $inden->id);
                                            @endphp
                                
                                            @if ($invoiceExists)
                                                <a href="{{ route('invoice.edit',['datapo' => $inden->id, 'type' => 'poinden']) }}" class="dropdown-item">
                                                    <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Pembayaran Invoice
                                                </a>
                                            @else
                                            <a href="{{ route('invoicebiasa.create', ['type' => 'poinden', 'datapo' => $inden->id]) }}" class="dropdown-item"><img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Create Invoice
                                            </a>
                                            @endif
                                        </li>
                                        <li>
                                            <a href="{{ route('pembelian.show', ['type' => 'poinden','datapo' => $inden->id]) }}" class="dropdown-item"><img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('pembelian.edit', ['type' => 'poinden','datapo' => $inden->id]) }}" class="dropdown-item"><img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                        </li>
                                        <li>
                                            {{-- <a href="#" class="dropdown-item" onclick="deleteData({{ $inden->id }})"><img src="/assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a> --}}
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