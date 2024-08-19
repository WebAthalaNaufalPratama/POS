@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Produk Penjualan</h4>
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
                        @foreach ($drivers as $item)
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
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('dopenjualan.index') }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('dopenjualan.index') }}" class="btn btn-warning">Clear</a>
                </div>
            </div>
                <div class="table-responsive">
                    <table class="table pb-5" id="formDopenjualanTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No DO</th>
                                <th>No Invoice</th>
                                <th>Customer</th>
                                <th>Tanggal Kirim</th>
                                <th>Status</th>
                                <th>Driver</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($dopenjualans as $dopenjualan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $dopenjualan->no_do }}</td>
                                <td>{{ $dopenjualan->no_referensi }}</td>
                                <td>{{ $dopenjualan->customer->nama }}</td>
                                <td>{{ date('d F Y', strtotime($dopenjualan->tanggal_kirim)) }}</td>
                                <td>{{ $dopenjualan->status }}</td>
                                <td>{{ $dopenjualan->data_driver->nama }}</td>
                                <td>
                                    <div class="dropdown">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                        @php
                                            $user = Auth::user();
                                        @endphp
                                        <div class="dropdown-menu">
                                            @if($dopenjualan->status != 'DIBATALKAN')
                                                @if($user->hasRole(['Auditor', 'Finance', 'SuperAdmin']))
                                                    <a class="dropdown-item" href="{{ route('auditdopenjualan.edit', ['dopenjualan' => $dopenjualan->id]) }}"><img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Audit</a>
                                                @elseif($user->hasRole(['AdminGallery', 'KasirGallery', 'KasirOutlet']) && $dopenjualan->status != 'DIKONFIRMASI')
                                                    <a class="dropdown-item" href="{{ route('auditdopenjualan.edit', ['dopenjualan' => $dopenjualan->id]) }}"><img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Edit</a>
                                                @endif
                                                <a class="dropdown-item" href="{{ route('dopenjualan.show', ['dopenjualan' => $dopenjualan->id]) }}"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Show</a>
                                                <a class="dropdown-item" href="{{ route('pdfdopenjualan.generate', ['dopenjualan' => $dopenjualan->id]) }}"><img src="assets/img/icons/printer.svg" class="me-2" alt="img">Cetak DO</a>
                                            @elseif($dopenjualan->status == 'DIBATALKAN')
                                                <a class="dropdown-item" href="{{ route('dopenjualan.show', ['dopenjualan' => $dopenjualan->id]) }}"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Show</a>
                                            @endif

                                            <!-- <a class="dropdown-item" href="javascript:void(0);" onclick="deleteData({{ $dopenjualan->id }})">Delete</a> -->
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
    $(document).ready(function() {
        // Initialize Select2 for filters
        $('#filterCustomer, #filterDriver').select2();

        // Define routes with proper ID replacement
        window.routes = {
            auditDoPenjualanEdit: "{{ route('auditdopenjualan.edit', ['dopenjualan' => '__ID__']) }}",
            auditDoPenjualanShow: "{{ route('dopenjualan.show', ['dopenjualan' => '__ID__']) }}",
            pdfDoPenjualan: "{{ route('pdfdopenjualan.generate', ['dopenjualan' => '__ID__']) }}",
        };

        // Initialize DataTable
        $('#formDopenjualanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("dopenjualan.index") }}',
                type: 'GET',
                data: function(d) {
                    d.customer = $('#filterCustomer').val();
                    d.driver = $('#filterDriver').val();
                    d.dateStart = $('#filterDateStart').val();
                    d.dateEnd = $('#filterDateEnd').val();
                },
                // dataSrc: function(json) {
                //     console.log("Received Data:", json); 
                //     return json.data;
                // }
            },
            columns: [
                { data: null, name: null, searchable: false, orderable: false, render: function (data, type, row, meta) {
                    return meta.row + 1;
                }},
                { data: 'no_do', name: 'no_do' },
                { data: 'no_referensi', name: 'no_referensi' },
                { data: 'customer.nama', name: 'customer.nama' },
                { data: 'tanggal_kirim', name: 'tanggal_kirim' },
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
                { data: 'data_driver.nama', name: 'data_driver.nama' },
                {
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        // Get user roles from server-side rendering
                        const userRoles = @json(Auth::user()->roles->pluck('name')->toArray());
                        
                        let dropdownHtml = `
                            <div class="dropdown">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <div class="dropdown-menu">`;

                        if (row.status !== 'DIBATALKAN') {
                            if (userRoles.includes('Auditor') || userRoles.includes('Finance') || userRoles.includes('SuperAdmin')) {
                                dropdownHtml += `<a class="dropdown-item" href="${window.routes.auditDoPenjualanEdit.replace('__ID__', row.id)}">
                                                    <img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Audit
                                                </a>`;
                            } else if ((userRoles.includes('AdminGallery') || userRoles.includes('KasirGallery') || userRoles.includes('KasirOutlet')) && row.status !== 'DIKONFIRMASI') {
                                dropdownHtml += `<a class="dropdown-item" href="${window.routes.auditDoPenjualanEdit.replace('__ID__', row.id)}">
                                                    <img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Edit
                                                </a>`;
                            }
                            if(row.status !== 'TUNDA') {
                                dropdownHtml += `<a class="dropdown-item" href="${window.routes.auditDoPenjualanShow.replace('__ID__', row.id)}">
                                                <img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Show
                                            </a>`;

                                dropdownHtml += `<a class="dropdown-item" href="${window.routes.pdfDoPenjualan.replace('__ID__', row.id)}">
                                                    <img src="assets/img/icons/printer.svg" class="me-2" alt="img">Cetak DO
                                                </a>`;
                            }
                            
                        } else {
                            dropdownHtml += `<a class="dropdown-item" href="${window.routes.auditDoPenjualanShow.replace('__ID__', row.id)}">
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