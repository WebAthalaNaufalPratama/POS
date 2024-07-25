@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Kembali Sewa</h4>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row ps-2 pe-2">
                <div class="col-sm-2 ps-0 pe-0">
                    <select id="filterCustomer" name="filterCustomer" class="form-control" title="Customer">
                        <option value="">Pilih Customer</option>
                        @foreach ($customer as $item)
                            <option value="{{ $item->customer->id }}" {{ $item->customer->id == request()->input('customer') ? 'selected' : '' }}>{{ $item->customer->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 ps-0 pe-0">
                    <select id="filterDriver" name="filterDriver" class="form-control" title="driver">
                        <option value="">Pilih driver</option>
                        @foreach ($driver as $item)
                            <option value="{{ $item->data_driver->id }}" {{ $item->data_driver->id == request()->input('driver') ? 'selected' : '' }}>{{ $item->data_driver->nama }}</option>
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
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('kembali_sewa.index') }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('kembali_sewa.index') }}" class="btn btn-warning">Clear</a>
                </div>
            </div>
            <div class="table-responsive">
            <table class="table datanew">
                <thead>
                <tr>
                    <th>No</th>
                    <th>No Kembali</th>
                    <th>No Kontrak</th>
                    <th>Customer</th>
                    <th>Driver</th>
                    <th>Tanggal Kembali</th>
                    <th>Status</th>
                    <th>Tanggal Dibuat</th>
                    <th>Tanggal Pemeriksa</th>
                    <th>Tanggal Pembuku</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->no_kembali }}</td>
                            <td>{{ $item->no_sewa }}</td>
                            <td>{{ $item->sewa->customer->nama }}</td>
                            <td>{{ $item->data_driver->nama }}</td>
                            <td>{{ formatTanggal($item->tanggal_kembali) }}</td>
                            <td>
                                <span class="badges
                                {{ $item->status == 'DIKONFIRMASI' ? 'bg-lightgreen' : ($item->status == 'TUNDA' ? 'bg-lightred' : 'bg-lightgrey') }}">
                                {{ $item->status ?? '-' }}
                                </span>
                            <td>{{ $item->tanggal_pembuat ? formatTanggal($item->tanggal_pembuat) : '' }}</td>
                            <td>{{ $item->tanggal_penyetuju ? formatTanggal($item->tanggal_penyetuju) : '' }}</td>
                            <td>{{ $item->tanggal_pemeriksa ? formatTanggal($item->tanggal_pemeriksa) : '' }}</td>
                            </td>
                            <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    @if(in_array('kembali_sewa.show', $thisUserPermissions))
                                        @if((in_array($item->status, ['DIKONFIRMASI', 'BATAL']) && Auth::user()->hasRole('AdminGallery')) || ($item->status == 'DIKONFIRMASI' && (Auth::user()->hasRole('Auditor') && $item->tanggal_penyetuju || Auth::user()->hasRole('Finance') && $item->tanggal_pemeriksa)))
                                        <li>
                                            <a href="{{ route('kembali_sewa.show', ['kembali_sewa' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                        </li>
                                        @else
                                        <li>
                                            <a href="{{ route('kembali_sewa.show', ['kembali_sewa' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/check.svg" class="me-2" alt="img">Konfirmasi</a>
                                        </li>
                                        @endif
                                    @endif
                                    @if( ($item->status == 'DIKONFIRMASI' && (Auth::user()->hasRole('Auditor') || Auth::user()->hasRole('Finance'))) && in_array('kembali_sewa.show', $thisUserPermissions) || ($item->status == 'TUNDA' && Auth::user()->hasRole('AdminGallery') && in_array('kembali_sewa.edit', $thisUserPermissions)) )
                                        @if(!$item->hasKembali)
                                            <li>
                                                <a href="{{ route('kembali_sewa.edit', ['kembali_sewa' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                            </li>
                                        @endif
                                    @endif
                                    @if($item->status == 'TUNDA' && Auth::user()->hasRole('AdminGallery'))
                                        <li>
                                            <a href="#" class="dropdown-item" onclick="deleteData({{ $item->id }})"><img src="assets/img/icons/closes.svg" class="me-2" alt="img">Batal</a>
                                        </li>
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
        Swal.fire({
            title: 'Batalkan kembali sewa?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, batalkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "GET",
                    url: "/kembali_sewa/"+id+"/delete",
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
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
        });
    }
    </script>
@endsection