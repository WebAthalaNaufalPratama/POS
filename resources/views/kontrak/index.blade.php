@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Kontrak</h4>
                </div>
                @if(in_array('kontrak.create', $thisUserPermissions))
                <div class="page-btn">
                    <a href="{{ route('kontrak.create') }}" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Kontrak</a>
                </div>
                @endif
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
                    <select id="filterSales" name="filterSales" class="form-control" title="Sales">
                        <option value="">Pilih Sales</option>
                        @foreach ($sales as $item)
                            <option value="{{ $item->data_sales->id }}" {{ $item->data_sales->id == request()->input('sales') ? 'selected' : '' }}>{{ $item->data_sales->name }}</option>
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
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('kontrak.index') }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('kontrak.index') }}" class="btn btn-warning">Clear</a>
                </div>
            </div>
            <div class="table-responsive">
            <table class="table pb-5" id="dataTable">
                <thead>
                <tr>
                    <th>No</th>
                    <th>No Kontrak</th>
                    <th>Customer</th>
                    <th>PIC</th>
                    <th>Sales</th>
                    <th>Handphone</th>
                    <th>Masa Kontrak</th>
                    <th>Rentang Tanggal</th>
                    <th>Total Biaya</th>
                    <th>Status</th>
                    <th>Tanggal Dibuat</th>
                    <th>Tanggal Pemeriksa</th>
                    <th>Tanggal Pembuku</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    {{-- @foreach ($kontraks as $kontrak)
                        <tr>
                            <td>{{ $loop->iteration ?? '-' }}</td>
                            <td>{{ $kontrak->no_kontrak ?? '-' }}</td>
                            <td>{{ $kontrak->customer->nama ?? '-' }}</td>
                            <td>{{ $kontrak->pic ?? '-'  }}</td>
                            <td>{{ $kontrak->data_sales->nama ?? '-'  }}</td>
                            <td>{{ $kontrak->handphone ?? '-' }}</td>
                            <td>{{ $kontrak->masa_sewa ?? '-' }} bulan</td>
                            <td>{{ formatTanggal($kontrak->tanggal_mulai)}} - {{ formatTanggal($kontrak->tanggal_selesai) ?? '-' }}</td>
                            <td>{{ formatRupiah($kontrak->total_harga) ?? '-' }}</td>
                            <td>
                                <span class="badges
                                {{ $kontrak->status == 'DIKONFIRMASI' ? 'bg-lightgreen' : ($kontrak->status == 'TUNDA' ? 'bg-lightred' : 'bg-lightgrey') }}">
                                {{ $kontrak->status ?? '-' }}
                                </span>
                            </td>
                            <td>{{ formatTanggal($kontrak->tanggal_pembuat) ?? '-'  }}</td>
                            <td>{{ $kontrak->tanggal_penyetuju ? formatTanggal($kontrak->tanggal_penyetuju) : '-'  }}</td>
                            <td>{{ $kontrak->tanggal_pemeriksa ? formatTanggal($kontrak->tanggal_pemeriksa) : '-'  }}</td>
                            <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    @if(in_array('kontrak.pdfKontrak', $thisUserPermissions) && $kontrak->tanggal_pemeriksa && $kontrak->tanggal_penyetuju)
                                        <li>
                                            <a href="{{ route('kontrak.pdfKontrak', ['kontrak' => $kontrak->id]) }}" target="_blank" class="dropdown-item"><img src="assets/img/icons/pdf.svg" class="me-2" alt="img">Kontrak</a>
                                        </li>
                                    @endif
                                    @if($kontrak->status == 'DIKONFIRMASI' && in_array('kontrak.excelPergantian', $thisUserPermissions) && $kontrak->tanggal_pemeriksa && $kontrak->tanggal_penyetuju && !empty($kontrak->kembali_sewa))
                                        <li>
                                            <a href="{{ route('kontrak.excelPergantian', ['kontrak' => $kontrak->id]) }}" class="dropdown-item"><img src="assets/img/icons/reverse-alt.svg" class="me-2" alt="img">Pergantian</a>
                                        </li>
                                    @endif
                                    @if(in_array('do_sewa.create', $thisUserPermissions) && $kontrak->status == 'DIKONFIRMASI')
                                        <li>
                                            <a href="{{ route('do_sewa.create', ['kontrak' => $kontrak->id]) }}" class="dropdown-item"><img src="assets/img/icons/truck.svg" class="me-2" alt="img">Delivery Order</a>
                                        </li>
                                    @endif
                                    @if(in_array('kembali_sewa.create', $thisUserPermissions) && $kontrak->status == 'DIKONFIRMASI')
                                        <li>
                                            <a href="{{ route('kembali_sewa.create', ['kontrak' => $kontrak->id]) }}" class="dropdown-item"><img src="assets/img/icons/return1.svg" class="me-2" alt="img">Kembali Sewa</a>
                                        </li>
                                    @endif
                                    @if(in_array('invoice_sewa.create', $thisUserPermissions) && $kontrak->status == 'DIKONFIRMASI')
                                        <li>
                                            <a href="{{ route('invoice_sewa.create', ['kontrak' => $kontrak->id]) }}" class="dropdown-item"><img src="assets/img/icons/dollar-square.svg" class="me-2" alt="img">Invoice Sewa</a>
                                        </li>
                                    @endif
                                    @if(in_array('kontrak.show', $thisUserPermissions))
                                        @if((in_array($kontrak->status, ['DIKONFIRMASI', 'BATAL']) && Auth::user()->hasRole('AdminGallery')) || ($kontrak->status == 'DIKONFIRMASI' && ($kontrak->tanggal_penyetuju || $kontrak->tanggal_pemeriksa) && (Auth::user()->hasRole('Auditor') && $kontrak->tanggal_penyetuju || Auth::user()->hasRole('Finance') && $kontrak->tanggal_pemeriksa)))
                                            <li>
                                                <a href="{{ route('kontrak.show', ['kontrak' => $kontrak->id]) }}" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                            </li>
                                        @else
                                            <li>
                                                <a href="{{ route('kontrak.show', ['kontrak' => $kontrak->id]) }}" class="dropdown-item"><img src="assets/img/icons/check.svg" class="me-2" alt="img">Konfirmasi</a>
                                            </li>
                                        @endif
                                    @endif
                                    @if( ($kontrak->status == 'DIKONFIRMASI' && (Auth::user()->hasRole('Auditor') || Auth::user()->hasRole('Finance'))) && in_array('kontrak.show', $thisUserPermissions) || ($kontrak->status == 'TUNDA' && Auth::user()->hasRole('AdminGallery') && in_array('kontrak.edit', $thisUserPermissions)) )
                                        @if(!$kontrak->hasKembali)
                                            <li>
                                                <a href="{{ route('kontrak.edit', ['kontrak' => $kontrak->id]) }}" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                            </li>
                                        @endif
                                    @endif
                                    @if($kontrak->status == 'TUNDA' && Auth::user()->hasRole('AdminGallery'))
                                        <li>
                                            <a href="#" class="dropdown-item" onclick="deleteData({{ $kontrak->id }})"><img src="assets/img/icons/closes.svg" class="me-2" alt="img">Batal</a>
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
        $('#filterCustomer, #filterSales').select2();


        // Start Datatable
        const columns = [
            { data: 'no', name: 'no', orderable: false },
            { data: 'no_kontrak', name: 'no_kontrak' },
            { data: 'nama_customer', name: 'nama_customer', orderable: false },
            { data: 'pic', name: 'pic', orderable: false },
            { data: 'nama_sales', name: 'nama_sales', orderable: false },
            { data: 'handphone', name: 'handphone' },
            { data: 'masa_sewa', name: 'masa_sewa' },
            { data: 'rentang_tanggal', name: 'rentang_tanggal', orderable: false },
            { data: 'total_harga', name: 'total_harga' },
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
                        <td class="text-center">
                            <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu">
                    `;

                    if (userPermissions.includes('kontrak.pdfKontrak') && row.tanggal_pemeriksa && row.tanggal_penyetuju) {
                        actionsHtml += `
                            <li>
                                <a href="kontrak/${row.id}/pdfKontrak" target="_blank" class="dropdown-item"><img src="assets/img/icons/pdf.svg" class="me-2" alt="img">Kontrak</a>
                            </li>
                        `;
                    }

                    if (row.status === 'DIKONFIRMASI' && userPermissions.includes('kontrak.excelPergantian') && row.tanggal_pemeriksa && row.tanggal_penyetuju && row.hasKembaliSewa) {
                        actionsHtml += `
                            <li>
                                <a href="kontrak/${row.id}/excelPergantian" class="dropdown-item"><img src="assets/img/icons/reverse-alt.svg" class="me-2" alt="img">Pergantian</a>
                            </li>
                        `;
                    }

                    if (row.status === 'DIKONFIRMASI' && userPermissions.includes('do_sewa.create')) {
                        actionsHtml += `
                            <li>
                                <a href="do_sewa/create?kontrak=${row.id}" class="dropdown-item">
                                    <img src="assets/img/icons/truck.svg" class="me-2" alt="img">Delivery Order
                                </a>
                            </li>
                        `;
                    }

                    if (row.status === 'DIKONFIRMASI' && userPermissions.includes('kembali_sewa.create')) {
                        actionsHtml += `
                            <li>
                                <a href="kembali_sewa/create?kontrak=${row.id}" class="dropdown-item"><img src="assets/img/icons/return1.svg" class="me-2" alt="img">Kembali Sewa</a>
                            </li>
                        `;
                    }

                    if (row.status === 'DIKONFIRMASI' && userPermissions.includes('invoice_sewa.create')) {
                        actionsHtml += `
                            <li>
                                <a href="invoice_sewa/create?kontrak=${row.id}" class="dropdown-item"><img src="assets/img/icons/dollar-square.svg" class="me-2" alt="img">Invoice Sewa</a>
                            </li>
                        `;
                    }

                    if (userPermissions.includes('kontrak.show')) {
                        if ((['DIKONFIRMASI', 'BATAL'].includes(row.status) && row.userRole === 'AdminGallery') ||
                            (row.status === 'DIKONFIRMASI' && (row.tanggal_penyetuju || row.tanggal_pemeriksa) &&
                            (['Auditor', 'Finance'].includes(row.userRole) && (row.tanggal_penyetuju || row.tanggal_pemeriksa)))) {
                            actionsHtml += `
                                <li>
                                    <a href="kontrak/${row.id}/show" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                </li>
                            `;
                        } else {
                            if (row.status === 'TUNDA') {
                                actionsHtml += `
                                    <li>
                                        <a href="kontrak/${row.id}/show" class="dropdown-item"><img src="assets/img/icons/check.svg" class="me-2" alt="img">Konfirmasi</a>
                                    </li>
                                `;
                            }
                        }
                    }

                    if ((row.status === 'DIKONFIRMASI' && ['Auditor', 'Finance'].includes(row.userRole) && userPermissions.includes('kontrak.edit')) ||
                        (row.status === 'TUNDA' && row.userRole === 'AdminGallery' && userPermissions.includes('kontrak.edit'))) {
                        if (!row.hasKembali) {
                            actionsHtml += `
                                <li>
                                    <a href="kontrak/${row.id}/edit" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                </li>
                            `;
                        }
                    }

                    if (row.status === 'TUNDA' && row.userRole === 'AdminGallery') {
                        actionsHtml += `
                            <li>
                                <a href="#" class="dropdown-item" onclick="deleteData(${row.id})"><img src="assets/img/icons/closes.svg" class="me-2" alt="img">Batal</a>
                            </li>
                        `;
                    }

                    actionsHtml += `
                            </ul>
                        </td>
                    `;

                    return actionsHtml;
                }
            }
        ];

        let table = initDataTable('#dataTable', {
            ajaxUrl: "{{ route('kontrak.index') }}",
            columns: columns,
            order: [[1, 'asc']],
            searching: true,
            lengthChange: true,
            pageLength: 5
        }, {
            customer: '#filterCustomer',
            sales: '#filterSales',
            dateStart: '#filterDateStart',
            dateEnd: '#filterDateEnd'
        });

        const handleSearch = debounce(function() {
            table.ajax.reload();
        }, 5000); // Adjust the debounce delay as needed

        // Event listeners for search filters
        $('#filterCustomer, #filterSales, #filterDateStart, #filterDateEnd').on('input', handleSearch);

        $('#filterBtn').on('click', function() {
            table.ajax.reload();
        });

        $('#clearBtn').on('click', function() {
            $('#filterCustomer').val('');
            $('#filterSales').val('');
            $('#filterDateStart').val('');
            $('#filterDateEnd').val('');
            table.ajax.reload();
        });
        // End Datatable
    });

    function deleteData(id){
        Swal.fire({
            title: 'Batalkan kontrak?',
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
                    url: "/kontrak/"+id+"/delete",
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