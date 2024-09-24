@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Mutasi Galery Ke Outlet</h4>
                    </div>
                    <div class="page-btn">
                    @php
                        $user = Auth::user();
                        $lokasi = \App\Models\Karyawan::where('user_id', $user->id)->first();
                    @endphp
                    @if($lokasi->lokasi->tipe_lokasi != 2)
                        <a href="{{ route('mutasigalery.create') }}" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Mutasi</a>
                    @endif
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
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('mutasigalery.index') }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('mutasigalery.index') }}" class="btn btn-warning">Clear</a>
                </div>
            </div>
                <div class="table-responsive">
                    <table class="table pb-5" id="mutasiTable" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Mutasi</th>
                                <th>Pengirim</th>
                                <th>Penerima</th>
                                <th>Tanggal Kirim</th>
                                <th>Tanggal Diterima</th>
                                <th>Tanggal Dibuat</th>
                                <th>Status</th>
                                <th>Status Penerima</th>
                                <th>Status Finance</th>
                                <th>Status Auditor</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($mutasis as $mutasi)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $mutasi->no_mutasi }}</td>
                                <td>{{ $mutasi->lokasi->nama }}</td>
                                <td>{{ $mutasi->lokasi->nama }}</td>
                                <td>{{ date('d F Y', strtotime($mutasi->tanggal_kirim)) }}</td>
                                <td>{{ date('d F Y', strtotime($mutasi->tanggal_diterima)) }}</td>
                                <td>{{ date('d F Y', strtotime($mutasi->tanggal_pembuat)) }}</td>
                                <td>{{ $mutasi->status }}</td>
                                <td>
                                    <div class="dropdown">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                        <div class="dropdown-menu">
                                            @if($mutasi->status != 'DIBATALKAN')
                                            @php
                                                $produkMutasi = $mutasi->produkMutasi->first();
                                            @endphp
                                            @if($user->hasRole(['Auditor', 'Finance', 'SuperAdmin']) && !empty($produkMutasi) && $produkMutasi->jumlah_diterima == null)
                                                <a class="dropdown-item" href="{{ route('auditmutasigalery.edit', ['mutasiGO' => $mutasi->id]) }}"><img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Audit</a>
                                            @elseif($user->hasRole(['Auditor', 'Finance', 'SuperAdmin']) && !empty($produkMutasi) &&  $produkMutasi->jumlah_diterima != null)
                                                <a class="dropdown-item" href="{{ route('mutasigalery.acc', ['mutasiGO' => $mutasi->id]) }}"><img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Audit ACC Terima</a>
                                            @elseif($user->hasRole(['KasirGallery', 'KasirOutlet', 'AdminGallery']) && $mutasi->status != 'DIKONFIRMASI')
                                                <a class="dropdown-item" href="{{ route('auditmutasigalery.edit', ['mutasiGO' => $mutasi->id]) }}"><img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Edit</a>
                                            @endif
                                            @if($lokasi->lokasi->tipe_lokasi != 2)
                                                <a class="dropdown-item" href="{{ route('mutasigalery.payment', ['mutasiGO' => $mutasi->id]) }}"><img src="assets/img/icons/dollar-square.svg" class="me-2" alt="img">pembayaran mutasi</a>
                                                <a class="dropdown-item" href="{{ route('mutasigalery.show', ['mutasiGO' => $mutasi->id]) }}"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Atur Perangkai</a>
                                            @elseif($lokasi->lokasi->tipe_lokasi != 1)
                                                <a class="dropdown-item" href="{{ route('mutasigalery.acc', ['mutasiGO' => $mutasi->id]) }}"><img src="assets/img/icons/transcation.svg" class="me-2" alt="img">Acc Terima</a>
                                            @endif
                                            <a class="dropdown-item" href="{{ route('mutasigalery.view', ['mutasiGO' => $mutasi->id]) }}"><img src="assets/img/icons/transcation.svg" class="me-2" alt="img">View</a>
                                            @else
                                                <a class="dropdown-item" href="{{ route('mutasigalery.view', ['mutasiGO' => $mutasi->id]) }}"><img src="assets/img/icons/transcation.svg" class="me-2" alt="img">View</a>
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
        // Initialize Select2
        $('#filterCustomer, #filterDriver').select2();

        // Define route templates
        window.routes = {
            auditmutasiedit: "{{ route('auditmutasigalery.edit', ['mutasiGO' => '__ID__']) }}",
            mutasiGalleryAcc: "{{ route('mutasigalery.acc', ['mutasiGO' => '__ID__']) }}",
            mutasiGalleryPayment: "{{ route('mutasigalery.payment', ['mutasiGO' => '__ID__']) }}",
            mutasiGalleryShow: "{{ route('mutasigalery.show', ['mutasiGO' => '__ID__']) }}",
            mutasiGalleryView: "{{ route('mutasigalery.view', ['mutasiGO' => '__ID__']) }}",
            pdfMutasiPenjualan: "{{ route('pdfmutasipenjualan.generate', ['mutasiGO' => '__ID__']) }}",
        };

        // Initialize DataTable
        $('#mutasiTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('mutasigalery.index') }}",
                data: function (d) {
                    d.dateStart = $('#filterDateStart').val();
                    d.dateEnd = $('#filterDateEnd').val();
                },
                dataSrc: function (json) {
                    console.log("Received Data:", json); // Log received data
                    return json.data;
                }
            },
            columns: [ 
                { data: null, name: null, searchable: false, orderable: false, render: function (data, type, row, meta) {
                    return meta.row + 1;
                }},
                { data: 'no_mutasi', name: 'no_mutasi' },
                { data: 'pengirim', name: 'pengirim' },
                { data: 'penerima', name: 'penerima' },
                { data: 'tanggal_kirim', name: 'tanggal_kirim' },
                { data: 'tanggal_diterima', name: 'tanggal_diterima' },
                { data: 'tanggal_dibuat', name: 'tanggal_dibuat' },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data) {
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
                    data: 'penerima_id',
                    name: 'penerima_id',
                    render: function (data) {
                        let badgeClass;
                        let displayText;

                        if (data !== null) { 
                            badgeClass = 'bg-lightgreen';
                            displayText = 'DIKONFIRMASI'; 
                        } else { 
                            badgeClass = 'bg-lightred';
                            displayText = 'TUNDA'; 
                        }

                        return `<span class="badges ${badgeClass}">${displayText || '-'}</span>`;
                    }
                },
                {
                    data: 'diperiksa_id',
                    name: 'diperiksa_id',
                    render: function (data) {
                        let badgeClass;
                        let displayText;

                        if (data !== null) { 
                            badgeClass = 'bg-lightgreen';
                            displayText = 'DIKONFIRMASI'; 
                        } else { 
                            badgeClass = 'bg-lightred';
                            displayText = 'TUNDA'; 
                        }

                        return `<span class="badges ${badgeClass}">${displayText || '-'}</span>`;
                    }
                },
                {
                    data: 'dibukukan_id',
                    name: 'dibukukan_id',
                    render: function (data) {
                        let badgeClass;
                        let displayText;

                        if (data !== null) { 
                            badgeClass = 'bg-lightgreen';
                            displayText = 'DIKONFIRMASI'; 
                        } else { 
                            badgeClass = 'bg-lightred';
                            displayText = 'TUNDA'; 
                        }

                        return `<span class="badges ${badgeClass}">${displayText || '-'}</span>`;
                    }
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        // Assuming `row` contains necessary data to check conditions
                        const isJumlahDiterima = row.jumlah_diterima === true || row.jumlah_diterima === 'true';
                        const isNoForm = row.noform === true || row.noform === 'true';
                        const userRoles = @json(Auth::user()->roles->pluck('name')->toArray());
                        const mutasiStatus = row.mutasi_status; // Adjust this according to your data

                        // Construct the dropdown HTML
                        let dropdownHtml = `
                            <div class="dropdown">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <div class="dropdown-menu">`;

                        // Add permission-based actions
                        if (mutasiStatus !== 'DIBATALKAN') {
                            if (userRoles.includes('Auditor') || userRoles.includes('Finance') || userRoles.includes('SuperAdmin')) {
                                if (isJumlahDiterima) {
                                    dropdownHtml += `<a class="dropdown-item" href="${window.routes.mutasiGalleryAcc.replace('__ID__', row.id)}"><img src="assets/img/icons/transcation.svg" class="me-2" alt="img">Audit ACC Terima</a>`;
                                } else {
                                    dropdownHtml += `<a class="dropdown-item" href="${window.routes.auditmutasiedit.replace('__ID__', row.id)}"><img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Audit</a>`;
                                }
                            } 
                            if ((userRoles.includes('KasirGallery') || userRoles.includes('AdminGallery'))) {
                                if(row.status !== 'DIKONFIRMASI') {
                                    dropdownHtml += `<a class="dropdown-item" href="${window.routes.auditmutasiedit.replace('__ID__', row.id)}"><img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Edit</a>`;
                                }else{
                                    if(isNoForm){
                                        dropdownHtml += `<a class="dropdown-item" href="${window.routes.mutasiGalleryPayment.replace('__ID__', row.id)}"><img src="assets/img/icons/dollar-square.svg" class="me-2" alt="img">Bayar</a>`;
                                    }else{
                                        dropdownHtml += `<a class="dropdown-item" href="${window.routes.mutasiGalleryShow.replace('__ID__', row.id)}"><img src="assets/img/icons/transcation.svg" class="me-2" alt="img">Atur Perangkai</a>`;
                                    }
                                    dropdownHtml += `<a class="dropdown-item" href="${window.routes.pdfMutasiPenjualan.replace('__ID__', row.id)}">
                                        <img src="assets/img/icons/printer.svg" class="me-2" alt="img">Cetak MUTASI
                                    </a>`;
                                }
                                
                            }
                            if (userRoles.includes('KasirOutlet')) {
                                if(isNoForm){
                                    dropdownHtml += `<a class="dropdown-item" href="${window.routes.mutasiGalleryAcc.replace('__ID__', row.id)}"><img src="assets/img/icons/transcation.svg" class="me-2" alt="img">Acc Terima</a>`;
                                }
                            }
                        }
                        if(row.status !== 'TUNDA') {
                            dropdownHtml += `
                                <a class="dropdown-item" href="${window.routes.mutasiGalleryView.replace('__ID__', row.id)}"><img src="assets/img/icons/transcation.svg" class="me-2" alt="img">View</a>
                                </div>
                            </div>`;
                        }
                        

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