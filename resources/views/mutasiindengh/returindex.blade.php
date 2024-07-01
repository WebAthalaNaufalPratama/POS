@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Retur Mutasi Inden</h4>
                    </div>
                    <div class="page-btn">
                        {{-- <a href="{{ route('mutasiindengh.create') }}" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Mutasi</a> --}}
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
                <div class="col-sm-2">
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('returinden.index') }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('returinden.index') }}" class="btn btn-warning">Clear</a>
                </div>
            </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Retur</th>
                                <th>No Mutasi</th>
                                <th>Kode Inden</th>
                                <th>Nama Produk</th>
                                <th>QTY</th>
                                <th>Tanggal Kirim</th>
                                <th>Tanggal Komplain</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($returs as $retur)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $retur->no_retur }}</td>
                                <td>{{ $retur->mutasiinden->no_mutasi}}</td>
                                <td>
                                    <ul>
                                        @foreach($retur->produkreturinden as $produkretur)
                                            <li>{{ $produkretur->produk->produk->kode_produk_inden }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        @foreach($retur->produkreturinden as $produkretur)
                                            <li>{{ $produkretur->produk->produk->produk->nama }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        @foreach($retur->produkreturinden as $produkretur)
                                            <li>{{ $produkretur->jml_diretur }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>{{ tanggalindo($retur->mutasiinden->tgl_dikirim) }}</td>
                                <td>{{ tanggalindo($retur->tgl_dibuat) }}</td>
                                <td></td>
                               
                               
                                {{-- <td class="text-center">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        @if ($mutasi->tgl_diterima == null)               
                                        <li>
                                            <a class="dropdown-item" href="{{ route('mutasiindengh.edit', ['mutasiIG' => $mutasi->id]) }}"><img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Acc Terima</a>
                                        </li>
                                        @endif
                                    <li>
                                        <a class="dropdown-item" href="{{ route('mutasiindengh.show', ['mutasiIG' => $mutasi->id]) }}"><img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail Mutasi</a>
                                    </li>
                                    <li>

                                        @if ($mutasi->returinden !== null)
                                        <a class="dropdown-item" href="{{ route('create.retur', ['mutasiIG' => $mutasi->id]) }}"><img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Lihat Komplain</a>

                                        @else              
                                        <a class="dropdown-item" href="{{ route('create.retur', ['mutasiIG' => $mutasi->id]) }}"><img src="/assets/img/icons/return1.svg" class="me-2" alt="img">Komplain</a>
                                        @endif
                                    </li>
                                    </ul>
                                </td> --}}
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