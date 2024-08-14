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
                                <option value="{{ $item->id }}" {{ $item->id == request()->input('customer') ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterDriver" name="filterDriver" class="form-control" title="driver">
                            <option value="">Pilih driver</option>
                            @foreach ($suppliers as $item)
                                <option value="{{ $item->id }}" {{ $item->id == request()->input('driver') ? 'selected' : '' }}>{{ $item->nama }}</option>
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
                    <table class="table pb-5" id="formReturPenjualanTable">
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
                            {{-- @foreach ($returs as $retur)
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
                                            @if($user->hasRole(['Auditor', 'Finance', 'SuperAdmin']) && $retur->status != 'TUNDA')
                                                <a class="dropdown-item" href="{{ route('auditretur.edit', ['returpenjualan' => $retur->id]) }}"><img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Auditor</a>
                                            @elseif($user->hasRole(['AdminGallery', 'KasirGallery', 'KasirOutlet']) && $retur->status != 'DIKONFIRMASI')
                                                <a class="dropdown-item" href="{{ route('auditretur.edit', ['returpenjualan' => $retur->id]) }}"><img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Edit</a>
                                            @endif
                                            @if($lokasi->lokasi->tipe_lokasi != 2 && $retur->komplain == 'retur')
                                                <a class="dropdown-item" href="{{ route('returpenjualan.show', ['returpenjualan' => $retur->id]) }}"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Set Ganti</a>
                                            @endif
                                            <!-- @if($retur->komplain == 'retur')
                                                <a class="dropdown-item" href="{{ route('returpenjualan.payment', ['returpenjualan' => $retur->id]) }}"><img src="assets/img/icons/dollar-square.svg" class="me-2" alt="img">Bayar</a>
                                            @endif -->
                                            @if($lokasi->lokasi->tipe_lokasi != 2)
                                            <a class="dropdown-item" href="{{ route('returpenjualan.view', ['returpenjualan' => $retur->id]) }}"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">View</a>
                                            @endif
                                            @if($lokasi->lokasi->tipe_lokasi == 2 & $retur->status == 'DIKONFIRMASI' && !$user->hasRole(['Auditor', 'Finance']))
                                                <a class="dropdown-item" href="{{ route('mutasioutlet.create', ['returpenjualan' => $retur->id]) }}"><img src="assets/img/icons/truck.svg" class="me-2" alt="img">Mutasi Outlet Ke Galery</a>
                                            @endif
                                            @else
                                                <a class="dropdown-item" href="{{ route('returpenjualan.show', ['returpenjualan' => $retur->id]) }}"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Show</a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach --}}
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

        $(document).ready(function() {
            $('#filterCustomer, #filterDriver').select2();

            window.routes = {
                auditReturEdit: "{{ route('auditretur.edit', ['returpenjualan' => '__ID__']) }}",
                returPenjualanShow: "{{ route('returpenjualan.show', ['returpenjualan' => '__ID__']) }}",
                returPenjualanView: "{{ route('returpenjualan.view', ['returpenjualan' => '__ID__']) }}",
                mutasiOutletCreate: "{{ route('mutasioutlet.create', ['returpenjualan' => '__ID__']) }}",
            };

            $('#formReturPenjualanTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("returpenjualan.index") }}',
                    type: 'GET',
                    data: function(d) {
                        d.customer = $('#filterCustomer').val();
                        d.driver = $('#filterDriver').val();
                        d.dateStart = $('#filterDateStart').val();
                        d.dateEnd = $('#filterDateEnd').val();
                    },
                    dataSrc: function(json) {
                        console.log("Received Data:", json); 
                        return json.data;
                    }
                },
                columns: [
                    { data: null, name: null, searchable: false, orderable: false, render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }},
                    { data: 'no_retur', name: 'no_retur' },
                    { data: 'no_invoice', name: 'no_invoice' },
                    { data: 'no_do', name: 'no_do' },
                    { data: 'customer.nama', name: 'customer.nama' },
                    { data: 'lokasi', name: 'lokasi' },
                    { data: 'supplier.nama', name: 'supplier.nama' },
                    { data: 'tanggal_retur', name: 'tanggal_retur' },
                    { data: 'komplain', name: 'komplain' },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            let badgeClass;
                            switch (data) {
                                case 'DIKONFIRMASI':
                                    badgeClass = 'bg-lightgreen';
                                    break;
                                case 'TUNDA':
                                    badgeClass = 'bg-lightred';
                                    break;
                                default:
                                    badgeClass = 'bg-lightgrey';
                                    break;
                            }
                            
                            return `<span class="badges ${badgeClass}">${data || '-'}</span>`;
                        }
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const userRoles = @json(Auth::user()->roles->pluck('name')->toArray());
                            const lokasiTipe = row.lokasi_tipe; // Adjust this based on your actual data structure

                            let dropdownHtml = `
                                <div class="dropdown">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <div class="dropdown-menu">`;

                            if (row.status !== 'DIBATALKAN') {
                                if (userRoles.includes('Auditor') || userRoles.includes('Finance') || userRoles.includes('SuperAdmin')) {
                                    dropdownHtml += `<a class="dropdown-item" href="${window.routes.auditReturEdit.replace('__ID__', row.id)}">
                                                        <img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Auditor
                                                    </a>`;
                                } else if ((userRoles.includes('AdminGallery') || userRoles.includes('KasirGallery') || userRoles.includes('KasirOutlet')) && row.status !== 'DIKONFIRMASI') {
                                    dropdownHtml += `<a class="dropdown-item" href="${window.routes.auditReturEdit.replace('__ID__', row.id)}">
                                                        <img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Edit
                                                    </a>`;
                                }

                                if (lokasiTipe != 2 && row.komplain === 'retur') {
                                    dropdownHtml += `<a class="dropdown-item" href="${window.routes.returPenjualanShow.replace('__ID__', row.id)}">
                                                        <img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Set Ganti
                                                    </a>`;
                                }

                                if (lokasiTipe != 2) {
                                    dropdownHtml += `<a class="dropdown-item" href="${window.routes.returPenjualanView.replace('__ID__', row.id)}">
                                                        <img src="assets/img/icons/eye1.svg" class="me-2" alt="img">View
                                                    </a>`;
                                }

                                if (lokasiTipe == 2 && row.status === 'DIKONFIRMASI' && !userRoles.includes('Auditor') && !userRoles.includes('Finance')) {
                                    dropdownHtml += `<a class="dropdown-item" href="${window.routes.mutasiOutletCreate.replace('__ID__', row.id)}">
                                                        <img src="assets/img/icons/truck.svg" class="me-2" alt="img">Mutasi Outlet Ke Galery
                                                    </a>`;
                                }
                            } else {
                                dropdownHtml += `<a class="dropdown-item" href="${window.routes.returPenjualanShow.replace('__ID__', row.id)}">
                                                    <img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Show
                                                </a>`;
                            }

                            dropdownHtml += `</div></div>`;
                            return dropdownHtml;
                        }
                    }
                ]
            });
        });

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