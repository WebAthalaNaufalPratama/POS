@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Retur Penjualan</h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row ps-2 pe-2">
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterCustomer" name="filterCustomer" class="form-control" title="Customer">
                            <option value="">Pilih Customer</option>
                            @foreach ($customers as $item)
                                <option value="{{ $item->customer->id }}" {{ $item->customer->id == request()->input('customer') ? 'selected' : '' }}>{{ $item->customer->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterDriver" name="filterDriver" class="form-control" title="driver">
                            <option value="">Pilih driver</option>
                            @foreach ($suppliers as $item)
                                <option value="{{ $item->supplier->id }}" {{ $item->supplier->id == request()->input('driver') ? 'selected' : '' }}>{{ $item->supplier->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <input type="date" class="form-control" name="filterDateStart" id="filterDateStart" value="{{ request()->input('dateStart') }}" title="Tanggal Awal">
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <input type="date" class="form-control" name="filterDateEnd" id="filterDateEnd" value="{{ request()->input('dateEnd') }}" title="Tanggal Akhir">
                    </div>
                    <div class="col-sm-2">
                        <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('returpenjualan.index') }}" class="btn btn-info">Filter</a>
                        <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('returpenjualan.index') }}" class="btn btn-warning">Clear</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Retur</th>
                                <th>No Invoice</th>
                                <th>No DO</th>
                                <th>Customer</th>
                                <th>Lokasi</th>
                                <th>Supplier</th>
                                <th>Tanggal Retur</th>
                                <th>Komplain</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($returs as $retur)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $retur->no_retur }}</td>
                                <td>{{ $retur->no_invoice }}</td>
                                <td>@if($retur->komplain == 'retur')
                                    {{ $retur->no_do }}
                                    @else
                                    Bukan Retur
                                    @endif
                                </td>
                                <td>{{ $retur->customer->nama }}</td>
                                <td>{{ $retur->lokasi->nama }}</td>
                                <td>{{ $retur->supplier->nama }}</td>
                                <td>{{ date('d F Y', strtotime($retur->tanggal_retur)) }}</td>
                                <td>{{ $retur->komplain }}</td>
                                <td>{{ $retur->status }}</td>
                                <td>
                                    <div class="dropdown">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                        <div class="dropdown-menu">
                                            @php
                                                $user = Auth::user();
                                                $lokasi = \App\Models\Karyawan::where('user_id', $user->id)->first();
                                            @endphp
                                            @if($retur->status != 'DIBATALKAN')
                                            @if($user->hasRole(['Auditor', 'Finance', 'SuperAdmin']))
                                                <a class="dropdown-item" href="{{ route('auditretur.edit', ['returpenjualan' => $retur->id]) }}"><img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Auditor</a>
                                            @elseif($user->hasRole(['AdminGallery', 'KasirGallery', 'KasirOutlet']) && $retur->status != 'DIKONFIRMASI')
                                                <a class="dropdown-item" href="{{ route('auditretur.edit', ['returpenjualan' => $retur->id]) }}"><img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Edit</a>
                                            @endif
                                            @if($lokasi->lokasi->tipe_lokasi != 2 && $retur->komplain == 'retur')
                                                <a class="dropdown-item" href="{{ route('returpenjualan.show', ['returpenjualan' => $retur->id]) }}"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Set Ganti</a>
                                            @endif
                                            @if($retur->komplain == 'retur')
                                                <a class="dropdown-item" href="{{ route('returpenjualan.payment', ['returpenjualan' => $retur->id]) }}"><img src="assets/img/icons/dollar-square.svg" class="me-2" alt="img">Bayar</a>
                                            @endif
                                            @if($lokasi->lokasi->tipe_lokasi != 2)
                                            <a class="dropdown-item" href="{{ route('returpenjualan.view', ['returpenjualan' => $retur->id]) }}"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">View</a>
                                            @endif
                                            @if($lokasi->lokasi->tipe_lokasi == 2 & $retur->status == 'DIKONFIRMASI')
                                                <a class="dropdown-item" href="{{ route('mutasioutlet.create', ['returpenjualan' => $retur->id]) }}"><img src="assets/img/icons/truck.svg" class="me-2" alt="img">Mutasi Outlet Ke Galery</a>
                                            @endif
                                            @else
                                                <a class="dropdown-item" href="{{ route('returpenjualan.show', ['returpenjualan' => $retur->id]) }}"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Show</a>
                                            @endif
                                        </div>
                                    </div>
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
<script>
    $(document).ready(function(){
        $('#filterCustomer, #filterDriver').select2();
    });
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
        
        var driver = $('#filterDriver').val();
        if (driver) {
            var filterDriver = 'driver=' + driver;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterDriver;
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
    function deleteData(id){
        $.ajax({
            type: "GET",
            url: "/do_sewa/"+id+"/delete",
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