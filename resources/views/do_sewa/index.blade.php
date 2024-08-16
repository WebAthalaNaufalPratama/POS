@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Delivery Order</h4>
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
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('do_sewa.index') }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('do_sewa.index') }}" class="btn btn-warning">Clear</a>
                </div>
            </div>
            <div class="table-responsive">
            <table class="table pb-5" id="dataTable">
                <thead>
                <tr>
                    <th>No</th>
                    <th>No Delivery Order</th>
                    <th>No Kontrak</th>
                    <th>Customer</th>
                    <th>PIC</th>
                    <th>Driver</th>
                    <th>Tanggal Kirim</th>
                    <th>Status</th>
                    <th>Tanggal Dibuat</th>
                    <th>Tanggal Pemeriksa</th>
                    <th>Tanggal Pembuku</th>
                    <th class="text-center">Aksi</th>
                </tr>
                </thead>
                <tbody>
                    {{-- @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->no_do }}</td>
                            <td>{{ $item->no_referensi }}</td>
                            <td>{{ $item->customer->nama }}</td>
                            <td>{{ $item->pic }}</td>
                            <td>{{ $item->data_driver->nama }}</td>
                            <td>{{ $item->tanggal_kirim ? formatTanggal($item->tanggal_kirim) : '' }}</td>
                            <td>
                                <span class="badges
                                {{ $item->status == 'DIKONFIRMASI' ? 'bg-lightgreen' : ($item->status == 'TUNDA' ? 'bg-lightred' : 'bg-lightgrey') }}">
                                {{ $item->status ?? '-' }}
                                </span>
                            </td>
                            <td>{{ $item->tanggal_pembuat ? formatTanggal($item->tanggal_pembuat) : '' }}</td>
                            <td>{{ $item->tanggal_penyetuju ? formatTanggal($item->tanggal_penyetuju) : '' }}</td>
                            <td>{{ $item->tanggal_pemeriksa ? formatTanggal($item->tanggal_pemeriksa) : '' }}</td>
                            <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    @if(in_array('do_sewa.show', $thisUserPermissions))
                                        @if((in_array($item->status, ['DIKONFIRMASI', 'BATAL']) && Auth::user()->hasRole('AdminGallery')) || ($item->status == 'DIKONFIRMASI' && (Auth::user()->hasRole('Auditor') && $item->tanggal_penyetuju || Auth::user()->hasRole('Finance') && $item->tanggal_pemeriksa)))
                                        <li>
                                            <a href="{{ route('do_sewa.show', ['do_sewa' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                        </li>
                                        @else
                                        <li>
                                            <a href="{{ route('do_sewa.show', ['do_sewa' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/check.svg" class="me-2" alt="img">Konfirmasi</a>
                                        </li>
                                        @endif
                                    @endif
                                    @if( ($item->status == 'DIKONFIRMASI' && (Auth::user()->hasRole('Auditor') || Auth::user()->hasRole('Finance'))) && in_array('do_sewa.show', $thisUserPermissions) || ($item->status == 'TUNDA' && Auth::user()->hasRole('AdminGallery') && in_array('do_sewa.edit', $thisUserPermissions)) )
                                        @if(!$item->hasKembali)
                                            <li>
                                                <a href="{{ route('do_sewa.edit', ['do_sewa' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
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
    $(document).ready(function(){
        $('#filterCustomer, #filterDriver').select2();

        // Start Datatable
        const columns = [
            { data: 'no', name: 'no', orderable: false },
            { data: 'no_do', name: 'no_do' },
            { data: 'no_referensi', name: 'no_referensi' },
            { data: 'nama_customer', name: 'nama_customer', orderable: false },
            { data: 'pic', name: 'pic', orderable: false },
            { data: 'nama_driver', name: 'nama_driver', orderable: false },
            { data: 'tanggal_kirim', name: 'tanggal_kirim' },
            { 
                data: 'status',
                name: 'status',
                render: function(data, type, row) {
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
                    
                    return `
                        <span class="badges ${badgeClass}">
                            ${data ?? '-'}
                        </span>
                    `;
                }
            },
            { data: 'tanggal_pembuat', name: 'tanggal_pembuat' },
            { data: 'tanggal_pemeriksa', name: 'tanggal_pemeriksa' },
            { data: 'tanggal_penyetuju', name: 'tanggal_penyetuju' },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    let actionsHtml = `
                    <div class="text-center">
                        <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                        </a>
                        <ul class="dropdown-menu">
                    `;

                    if (userPermissions.includes('do_sewa.show')) {
                        if ((['DIKONFIRMASI', 'BATAL'].includes(row.status) && row.userRole === 'AdminGallery') ||
                            (row.status === 'DIKONFIRMASI' && (row.userRole === 'Auditor' && row.tanggal_penyetuju) || (row.userRole === 'Finance' && row.tanggal_pemeriksa))) {
                            actionsHtml += `
                                <li>
                                    <a href="do_sewa/${row.id}/show" class="dropdown-item">
                                        <img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail
                                    </a>
                                </li>
                            `;
                        } else if (row.userRole === 'SuperAdmin') {
                            actionsHtml += `
                                <li>
                                    <a href="do_sewa/${row.id}/show" class="dropdown-item">
                                        <img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail
                                    </a>
                                </li>
                            `;
                        } else {
                            actionsHtml += `
                                <li>
                                    <a href="do_sewa/${row.id}/show" class="dropdown-item">
                                        <img src="assets/img/icons/check.svg" class="me-2" alt="img">Konfirmasi
                                    </a>
                                </li>
                            `;
                        }
                    }

                    if ((row.status === 'DIKONFIRMASI' && ['Auditor', 'Finance'].includes(row.userRole) && userPermissions.includes('do_sewa.edit')) ||
                        (row.status === 'TUNDA' && row.userRole === 'AdminGallery' && userPermissions.includes('do_sewa.edit'))) {
                        if (!row.hasKembali) {
                            actionsHtml += `
                                <li>
                                    <a href="do_sewa/${row.id}/edit" class="dropdown-item">
                                        <img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit
                                    </a>
                                </li>
                            `;
                        }
                    }

                    if (row.status === 'TUNDA' && row.userRole === 'AdminGallery') {
                        actionsHtml += `
                            <li>
                                <a href="#" class="dropdown-item" onclick="deleteData(${row.id})">
                                    <img src="assets/img/icons/closes.svg" class="me-2" alt="img">Batal
                                </a>
                            </li>
                        `;
                    }

                    actionsHtml += `</ul></div>`;

                    return actionsHtml;
                }
            }
        ];

        let table = initDataTable('#dataTable', {
            ajaxUrl: "{{ route('do_sewa.index') }}",
            columns: columns,
            order: [[1, 'asc']],
            searching: true,
            lengthChange: true,
            pageLength: 10
        }, {
            customer: '#filterCustomer',
            driver: '#filterDriver',
            dateStart: '#filterDateStart',
            dateEnd: '#filterDateEnd'
        });

        const handleSearch = debounce(function() {
            table.ajax.reload();
        }, 5000); // Adjust the debounce delay as needed

        // Event listeners for search filters
        $('#filterCustomer, #filterDriver, #filterDateStart, #filterDateEnd').on('input', handleSearch);

        $('#filterBtn').on('click', function() {
            table.ajax.reload();
        });

        $('#clearBtn').on('click', function() {
            $('#filterCustomer').val('').trigger('change');
            $('#filterDriver').val('').trigger('change');
            $('#filterDateStart').val('');
            $('#filterDateEnd').val('');
            table.ajax.reload();
        });
        // End Datatable
    });

    function deleteData(id){
        Swal.fire({
            title: 'Batalkan delivery order?',
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
        });
    }
    </script>
@endsection