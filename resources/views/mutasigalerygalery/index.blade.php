@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Mutasi Galery Ke Galery</h4>
                    </div>
                    @php
                        $user = Auth::user();
                        $lokasi = \App\Models\Karyawan::where('user_id', $user->id)->first();
                    @endphp
                    @if($user->hasRole(['Purchasing', 'SuperAdmin']))
                    <div class="page-btn">
                        <a href="{{ route('mutasigalerygalery.create') }}" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Mutasi</a>
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
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('mutasigalerygalery.index') }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('mutasigalerygalery.index') }}" class="btn btn-warning">Clear</a>
                </div>
            </div>
                <div class="table-responsive">
                    <table class="table pb-5" id="mutasiTable">
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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($mutasis as $mutasi)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $mutasi->no_mutasi }}</td>
                                <td>{{ $mutasi->lokasi->nama }}</td>
                                <td>{{ $mutasi->lokasi_penerima->nama }}</td>
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
                                        @php
                                            $produkMutasi = $mutasi->produkMutasiGAG->first();
                                        @endphp
                                        @if($user->hasRole(['Auditor', 'Finance', 'SuperAdmin']))
                                        @if($user->hasRole(['Auditor', 'Finance', 'SuperAdmin']) && !empty($produkMutasi) && $produkMutasi->jumlah_diterima == null)
                                            <a class="dropdown-item" href="{{ route('auditmutasigalerygalery.edit', ['mutasiGAG' => $mutasi->id]) }}"><img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Audit</a>
                                        @elseif($user->hasRole(['Auditor', 'Finance', 'SuperAdmin']) && !empty($produkMutasi) && $produkMutasi->jumlah_diterima != null)
                                            <a class="dropdown-item" href="{{ route('mutasigalerygalery.show', ['mutasiGAG' => $mutasi->id]) }}"><img src="assets/img/icons/transcation.svg" class="me-2" alt="img">Audit Acc Terima</a>
                                        @endif
                                        @elseif($user->hasRole(['Purchasing']))
                                        @if($mutasi->status != 'DIKONFIRMASI')
                                            <a class="dropdown-item" href="{{ route('auditmutasigalerygalery.edit', ['mutasiGAG' => $mutasi->id]) }}"><img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Edit</a>
                                        @endif
                                            <a class="dropdown-item" href="{{ route('mutasigalerygalery.payment', ['mutasiGAG' => $mutasi->id]) }}"><img src="assets/img/icons/dollar-square.svg" class="me-2" alt="img">pembayaran mutasi</a>
                                        @endif
                                            
                                        @if($lokasi->lokasi_id == $mutasi->penerima && !$user->hasRole(['Auditor', 'Finance', 'SuperAdmin']))
                                            <a class="dropdown-item" href="{{ route('mutasigalerygalery.show', ['mutasiGAG' => $mutasi->id]) }}"><img src="assets/img/icons/transcation.svg" class="me-2" alt="img">Acc Terima</a>
                                        @endif
                                            <a class="dropdown-item" href="{{ route('mutasigalerygalery.view', ['mutasiGAG' => $mutasi->id]) }}"><img src="assets/img/icons/transcation.svg" class="me-2" alt="img">View</a>
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
        $('#filterCustomer, #filterDriver').select2();

        // Define route templates
        window.routes = {
            auditmutasigalerygaleryEdit: "{{ route('auditmutasigalerygalery.edit', ['mutasiGAG' => '__ID__']) }}",
            mutasiGalleryGalleryShow: "{{ route('mutasigalerygalery.show', ['mutasiGAG' => '__ID__']) }}",
            mutasiGalleryGalleryPayment: "{{ route('mutasigalerygalery.payment', ['mutasiGAG' => '__ID__']) }}",
            mutasiGalleryGalleryView: "{{ route('mutasigalerygalery.view', ['mutasiGAG' => '__ID__']) }}"
        };

        // Initialize DataTable
        $('#mutasiTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('mutasigalerygalery.index') }}",
                data: function(d) {
                    d.dateStart = $('#filterDateStart').val();
                    d.dateEnd = $('#filterDateEnd').val();
                },
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
                        const isJumlahDiterima = row.jumlah_diterima === true || row.jumlah_diterima === 'true';
                        const userRoles = @json(Auth::user()->roles->pluck('name')->toArray());
                        const mutasiStatus = row.status; // Adjust this if necessary

                        // Construct the dropdown HTML
                        let dropdownHtml = `
                            <div class="dropdown">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <div class="dropdown-menu">`;

                        // Add permission-based actions
                        if (userRoles.includes('Auditor') || userRoles.includes('Finance') || userRoles.includes('SuperAdmin')) {
                            if (isJumlahDiterima) {
                                dropdownHtml += `<a class="dropdown-item" href="${window.routes.auditmutasigalerygaleryEdit.replace('__ID__', row.id)}"><img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Audit</a>`;
                            } else {
                                dropdownHtml += `<a class="dropdown-item" href="${window.routes.mutasigalerygalery.show.replace('__ID__', row.id)}"><img src="assets/img/icons/transcation.svg" class="me-2" alt="img">Audit Acc Terima</a>`;
                            }
                        }

                        if (userRoles.includes('Purchasing') && mutasiStatus !== 'DIBATALKAN') {
                            if (mutasiStatus !== 'DIKONFIRMASI') {
                                dropdownHtml += `<a class="dropdown-item" href="${window.routes.auditmutasigalerygaleryEdit.replace('__ID__', row.id)}"><img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Edit</a>`;
                            }else{
                                dropdownHtml += `<a class="dropdown-item" href="${window.routes.mutasiGalleryGalleryPayment.replace('__ID__', row.id)}"><img src="assets/img/icons/dollar-square.svg" class="me-2" alt="img">Bayar</a>`;
                            }
                            
                        }

                        if (userRoles.includes('KasirGallery') || userRoles.includes('AdminGallery')) {
                            if (row.lokasi === row.penerima && mutasiStatus === 'DIKONFIRMASI') {
                                dropdownHtml += `<a class="dropdown-item" href="${window.routes.mutasiGalleryGalleryShow.replace('__ID__', row.id)}"><img src="assets/img/icons/transcation.svg" class="me-2" alt="img">Acc Terima</a>`;
                            }
                        }
                        if(mutasiStatus !== 'TUNDA') {
                            dropdownHtml += `
                                <a class="dropdown-item" href="${window.routes.mutasiGalleryGalleryView.replace('__ID__', row.id)}"><img src="assets/img/icons/transcation.svg" class="me-2" alt="img">View</a>
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