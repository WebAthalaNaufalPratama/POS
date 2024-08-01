@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Mutasi Inden ke Gallery/GreenHouse</h4>
                    </div>
                    @if(Auth::user()->hasRole('Purchasing'))
                    <div class="page-btn">
                        <a href="{{ route('mutasiindengh.create') }}" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Mutasi</a>
                    </div>
                    @endif
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
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('mutasiindengh.index') }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('mutasiindengh.index') }}" class="btn btn-warning">Clear</a>
                </div>
            </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Mutasi</th>
                                <th>Supplier</th>
                                <th>Penerima</th>
                                <th>Tanggal Kirim</th>
                                <th>Tanggal Diterima</th>
                                @if(Auth::user()->hasRole('Purchasing'))
                                <th>Status Dibuat</th>
                                @else
                                <th>Status Diterima</th>
                                @endif
                                {{-- <th>Status Diterima</th>
                                <th>Status Dibukukan</th>
                                <th>Status Diperiksa</th> --}}
                                <th>Tagihan</th>
                                <th>Sisa Tagihan</th>
                                <th>Status Pembayaran</th>
                                <th>Refund</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mutasis as $mutasi)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $mutasi->no_mutasi }}</td>
                                <td>{{ $mutasi->supplier->nama }}</td>
                                <td>{{ $mutasi->lokasi->nama }}</td>
                                <td>{{ tanggalindo($mutasi->tgl_dikirim) }}</td>
                                <td>{{ $mutasi->tgl_diterima ? tanggalindo($mutasi->tgl_diterima) : ''}}</td>
                                @if(Auth::user()->hasRole('Purchasing'))
                                <td>{{ $mutasi->status_dibuat }}</td>
                                @else
                                <td>{{ $mutasi->status_diterima }}</td>
                                @endif
                                {{-- <td>{{ $mutasi->status_diterima }}</td>
                                <td>{{ $mutasi->status_dibukukan }}</td>
                                <td>{{ $mutasi->status_diperiksa }}</td> --}}
                                <td>{{ formatRupiah($mutasi->total_biaya) }}</td>
                                <td>{{ formatRupiah($mutasi->sisa_bayar) }}</td>
                                <td>
                                    @if ( $mutasi->sisa_bayar == 0 && $mutasi->sisa_bayar !== null  ) 
                                        Lunas
                                    @else
                                        Belum Lunas
                                    @endif
                                </td>
                                <td>
                                    @if ( $mutasi->returinden !== null  ) 
                                        {{ formatRupiah($mutasi->returinden->refund) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        @role('Purchasing')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('mutasiindengh.editpurchase', ['mutasiIG' => $mutasi->id]) }}"><img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                        </li>
                                        @endrole
                                        @role('Auditor')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('mutasiindengh.edit', ['mutasiIG' => $mutasi->id]) }}"><img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Periksa</a>
                                        </li>
                                        @endrole
                                        @if ($mutasi->tgl_diterima == null && Auth::user()->hasRole('AdminGallery'))               
                                        <li>
                                            <a class="dropdown-item" href="{{ route('mutasiindengh.edit', ['mutasiIG' => $mutasi->id]) }}"><img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Acc Terima</a>
                                        </li>
                                        @endif
                                    @if ($mutasi->returinden !== null)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('show.returinden', ['mutasiIG' => $mutasi->id]) }}"><img src="/assets/img/icons/transcation.svg" class="me-2" alt="img">Bayar Mutasi/Input Refund</a>
                                        </li>
                                    @else
                                        <li>
                                            <a class="dropdown-item" href="{{ route('mutasiindengh.show', ['mutasiIG' => $mutasi->id]) }}"><img src="/assets/img/icons/transcation.svg" class="me-2" alt="img">Bayar Mutasi</a>
                                        </li>
                                       @if ( $mutasi->sisa_bayar == $mutasi->total_biaya || $mutasi->sisa_bayar == 0  ) 
                                       <li>
                                           <a class="dropdown-item" href="{{ route('create.retur', ['mutasiIG' => $mutasi->id]) }}"><img src="/assets/img/icons/return1.svg" class="me-2" alt="img">Komplain</a>
                                       </li>
                                           
                                       @endif
                                      
                                    @endif
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