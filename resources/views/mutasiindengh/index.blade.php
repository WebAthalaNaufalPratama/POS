@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Mutasi Inden ke Gallery/Pusat</h4>
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
                    <table id="mutasiInden" class="table pb-5">
                        <thead>
                            <tr> 
                                <th>No</th>
                                <th>No Mutasi</th>
                                <th>Supplier</th>
                                <th>Penerima</th>
                                <th>Tanggal Kirim</th>
                                <th>Tanggal Diterima</th>
                                {{-- @if(Auth::user()->hasRole('Purchasing')) --}}
                                <th>Status dibuat</th>
                                <th>Status diterima</th>
                                <th>Status diperiksa</th>
                                <th>Status dibuku</th>
                                {{-- @if (Auth::user()->hasRole('Finance') || Auth::user()->hasRole('Purchasing')) --}}
                                <th>Tagihan</th>
                                <th>Sisa Tagihan</th>
                                <th>Status Pembayaran</th>
                                <th>Komplain</th>
                                <th>Status Komplain</th>
                                {{-- @endif --}}
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($mutasis as $mutasi)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $mutasi->no_mutasi }}</td>
                                <td>{{ $mutasi->supplier->nama }}</td>
                                <td>{{ $mutasi->lokasi->nama }}</td>
                                <td>{{ tanggalindo($mutasi->tgl_dikirim) }}</td>
                                <td>{{ $mutasi->tgl_diterima ? tanggalindo($mutasi->tgl_diterima) : '-'}}</td>
                                 <!-- @if(Auth::user()->hasRole('Purchasing'))  -->
                                <td>
                                    @if ($mutasi->status_dibuat == 'TUNDA' || $mutasi->status_dibuat == null)
                                        <span class="badges bg-lightred">TUNDA</span>
                                    @elseif ($mutasi->status_dibuat == 'DIKONFIRMASI')
                                        <span class="badges bg-lightgreen">DIKONFIRMASI</span>
                                    @elseif ($mutasi->status_dibuat == 'BATAL')
                                        <span class="badges bg-lightgrey">BATAL</span>
                                    @endif
                                </td>
                                <td>
                                    @if($mutasi->lokasi->tipe_lokasi == 1)

                                        @if($mutasi->status_diterima == 'BATAL')
                                            <span class="badges bg-lightgrey">BATAL</span>
                                        @elseif($mutasi->status_diterima == 'TUNDA' || $mutasi->status_diterima == null)
                                            <span class="badges bg-lightred">TUNDA</span>
                                        @elseif($mutasi->status_diterima == 'DIKONFIRMASI')
                                            <span class="badges bg-lightgreen">DIKONFIRMASI</span>
                                        @else
                                        {{$mutasi->status_diterima  }}
                                        @endif

                                    @else 
                                    <!--  mengikuti auditor  -->
                                        @if($mutasi->status_diperiksa == 'BATAL')
                                            <span class="badges bg-lightgrey">BATAL</span>
                                        @elseif($mutasi->status_diperiksa == 'TUNDA' || $mutasi->status_diperiksa == null)
                                            <span class="badges bg-lightred">TUNDA</span>
                                        @elseif($mutasi->status_diperiksa == 'DIKONFIRMASI')
                                            <span class="badges bg-lightgreen">DIKONFIRMASI</span>
                                        @endif
                                    @endif

                                </td>
                                <td>
                                    @if($mutasi->status_diperiksa == 'BATAL')
                                    <span class="badges bg-lightgrey">BATAL</span>
                                    @elseif($mutasi->status_diperiksa == 'TUNDA' || $mutasi->status_diperiksa == null)
                                    <span class="badges bg-lightred">TUNDA</span>
                                    @elseif($mutasi->status_diperiksa == 'DIKONFIRMASI')
                                    <span class="badges bg-lightgreen">DIKONFIRMASI</span>
                                    @endif
                                </td>
                                <td>
                                    @if($mutasi->status_dibukukan == 'BATAL')
                                        <span class="badges bg-lightgrey">BATAL</span>
                                    @elseif($mutasi->status_dibukukan == 'TUNDA' || $mutasi->status_dibukukan == null)
                                        <span class="badges bg-lightred">TUNDA</span>
                                    @elseif ($mutasi->status_dibukukan == 'DIKONFIRMASI')
                                        <span class="badges bg-lightgreen">DIKONFIRMASI</span>
                                    @elseif ($mutasi->status_dibukukan == 'MENUNGGU PEMBAYARAN' && $mutasi->sisa_bayar !== 0)
                                        <span class="badges bg-lightyellow">MENUNGGU PEMBAYARAN</span>
                                    @elseif ($mutasi->status_dibukukan == 'MENUNGGU PEMBAYARAN' && $mutasi->sisa_bayar == 0)
                                        <span class="badges bg-lightyellow">MENUNGGU KONFIRMASI</span>
                                    @endif
                                </td>

                                @if (Auth::user()->hasRole('Finance') || Auth::user()->hasRole('Purchasing'))
                                <td>{{ formatRupiah($mutasi->total_biaya) }}</td>
                                <td>{{ formatRupiah($mutasi->sisa_bayar) }}</td>
                                <td>@if($mutasi->status_dibukukan !== 'BATAL')
                                        @if ( $mutasi->sisa_bayar == 0 && $mutasi->sisa_bayar !== null ) 
                                        <span class="badges bg-lightgreen">Lunas</span>
                                        @else
                                        <span class="badges bg-lightred">Belum Lunas</span>
                                        @endif
                                    @else
                                    <span class="badges bg-lightgrey">BATAL</span>
                                    @endif

                                </td>
                                <td>
                                    @if ( $mutasi->returinden !== null && $mutasi->returinden->status_dibuat !== "BATAL" ) 

                                      {{ $mutasi->returinden->tipe_komplain }} : {{ formatRupiah($mutasi->returinden->refund) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ( $mutasi->returinden !== null &&  ($mutasi->returinden->status_dibuat == null || $mutasi->returinden->status_dibuat == "TUNDA")) 
                                    Menunggu Konfirmasi Purchase
                                    @elseif ( $mutasi->returinden !== null &&  ($mutasi->returinden->status_dibukukan == null || $mutasi->returinden->status_dibukukan == "TUNDA")) 
                                    Menunggu Konfirmasi Finance
                                    @elseif ( $mutasi->returinden !== null &&  ($mutasi->returinden->status_dibukukan == "DIKONFIRMASI" || $mutasi->returinden->status_dibukukan == "MENUNGGU PEMBAYARAN"))
                                    {{ $mutasi->returinden->status_dibukukan }}

                                    @else
                                        -
                                    @endif
                                </td>
                                @endif
                                
                                <td class="text-center">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                
                                         <!-- Actions for Purchasing  -->
                                        @if (Auth::user()->hasRole('Purchasing'))
                                            @if ($mutasi->status_dibuat == "TUNDA")
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('mutasiindengh.editpurchase', ['mutasiIG' => $mutasi->id]) }}">
                                                        <img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit
                                                    </a>
                                                </li>
                                            @else
                                            <li>
                                                <a class="dropdown-item" href="{{ route('mutasiindengh.show', ['mutasiIG' => $mutasi->id]) }}">
                                                    <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail Mutasi
                                                </a>
                                            </li>
                                            @if ($mutasi->returinden && $mutasi->returinden->status_dibuat !== "BATAL")
                                            <li>
                                                <a class="dropdown-item" href="{{ route('show.returinden', ['mutasiIG' => $mutasi->id]) }}">
                                                    <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail Retur
                                                </a>
                                            </li>
                                                
                                            @endif
                                            @endif
                                
                                            @if (($mutasi->sisa_bayar == $mutasi->total_biaya || $mutasi->sisa_bayar == 0) && $mutasi->status_dibukukan == "MENUNGGU PEMBAYARAN" && !$mutasi->returinden)
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('create.retur', ['mutasiIG' => $mutasi->id]) }}">
                                                        <img src="/assets/img/icons/return1.svg" class="me-2" alt="img">Komplain
                                                    </a>
                                                </li>
                                            @endif
                                
                                            @if ($mutasi->returinden && ($mutasi->returinden->status_dibuat == "TUNDA" || is_null($mutasi->returinden->status_dibuat)))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('edit.retur', ['idretur' => $mutasi->returinden->id]) }}">
                                                        <img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit Komplain
                                                    </a>
                                                </li>
                                            @endif
                                
                                           
                                        @endif
                                
                                         <!-- Actions for Finance  -->
                                        @if (Auth::user()->hasRole('Finance'))
                                            @if ($mutasi->status_dibukukan == "TUNDA" || is_null($mutasi->status_dibukukan))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('mutasiindengh.editfinance', ['mutasiIG' => $mutasi->id]) }}">
                                                        <img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Acc Harga
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($mutasi->status_dibukukan == "MENUNGGU PEMBAYARAN" && $mutasi->sisa_bayar !== 0 && (!$mutasi->returinden || ($mutasi->returinden && $mutasi->returinden->status_dibukukan == "BATAL")))
                                            <li>
                                                <a class="dropdown-item" href="{{ route('mutasiindengh.show', ['mutasiIG' => $mutasi->id]) }}">
                                                    <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img">Bayar Mutasi
                                                </a>
                                            </li>
                                            @elseif ($mutasi->status_dibukukan == "MENUNGGU PEMBAYARAN" && $mutasi->sisa_bayar == 0 && (!$mutasi->returinden || ($mutasi->returinden && $mutasi->returinden->status_dibukukan == "BATAL")))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('mutasiindengh.show', ['mutasiIG' => $mutasi->id]) }}">
                                                        <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img">Konfirmasi
                                                    </a>
                                                </li>
                                            @elseif (($mutasi->status_dibukukan == "DIKONFIRMASI" && $mutasi->sisa_bayar == 0 && !$mutasi->returinden) || ($mutasi->returinden && $mutasi->returinden->status_dibukukan == "BATAL"))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('mutasiindengh.show', ['mutasiIG' => $mutasi->id]) }}">
                                                        <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail Mutasi
                                                    </a>
                                                </li>
                                            @endif
                                    
                                
                                            @if ($mutasi->returinden)
                                            
                                                @if (($mutasi->returinden->status_dibukukan == "TUNDA" || is_null($mutasi->returinden->status_dibukukan)) && $mutasi->returinden->status_dibuat == "DIKONFIRMASI")
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('edit.retur', ['idretur' => $mutasi->returinden->id]) }}">
                                                            <img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit Komplain
                                                        </a>
                                                    </li>
                                              
                                                @endif
                                                @if ($mutasi->returinden->status_dibukukan == "MENUNGGU PEMBAYARAN" && ($mutasi->returinden->sisa_refund !== 0 || $mutasi->sisa_bayar !== 0))
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('show.returinden', ['mutasiIG' => $mutasi->id]) }}">
                                                            <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img">Bayar Mutasi/Input Refund
                                                        </a>
                                                    </li>
                                                @elseif ($mutasi->returinden->status_dibukukan == "MENUNGGU PEMBAYARAN" && $mutasi->returinden->sisa_refund == 0 && $mutasi->sisa_bayar == 0)
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('show.returinden', ['mutasiIG' => $mutasi->id]) }}">
                                                            <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img">Konfirmasi Retur
                                                        </a>
                                                    </li>
                                                 @elseif ($mutasi->status_dibukukan == "MENUNGGU PEMBAYARAN" && $mutasi->returinden->status_dibukukan == "DIKONFIRMASI")
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('mutasiindengh.show', ['mutasiIG' => $mutasi->id]) }}">
                                                            <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img">Konfirmasi Mutasi
                                                        </a>
                                                    </li>
                                                @elseif ($mutasi->returinden->status_dibukukan == "DIKONFIRMASI" && $mutasi->returinden->sisa_refund == 0 && $mutasi->sisa_bayar == 0)
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('mutasiindengh.show', ['mutasiIG' => $mutasi->id]) }}">
                                                            <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail Mutasi
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('show.returinden', ['mutasiIG' => $mutasi->id]) }}">
                                                            <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail Retur
                                                        </a>
                                                    </li>
                                                @endif
                                            @endif
                                            
                                        @endif
                                
                                         <!-- Actions for AdminGallery  -->
                                        @if (Auth::user()->hasRole('AdminGallery') && is_null($mutasi->status_diterima))
                                            <li>
                                                <a class="dropdown-item" href="{{ route('mutasiindengh.edit', ['mutasiIG' => $mutasi->id]) }}">
                                                    <img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Acc Terima
                                                </a>
                                            </li>
                                        @elseif(Auth::user()->hasRole('AdminGallery') && $mutasi->status_diterima == "DIKONFIRMASI")
                                            <li>
                                                <a class="dropdown-item" href="{{ route('mutasiindengh.show', ['mutasiIG' => $mutasi->id]) }}">
                                                    <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail Mutasi
                                                </a>
                                            </li>
                                        @endif
                                
                                         <!-- Actions for Auditor  -->
                                        @if (Auth::user()->hasRole('Auditor') && is_null($mutasi->status_diperiksa))
                                            <li>
                                                <a class="dropdown-item" href="{{ route('mutasiindengh.edit', ['mutasiIG' => $mutasi->id]) }}">
                                                    <img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Periksa
                                                </a>
                                            </li>
                                        @elseif (Auth::user()->hasRole('Auditor') && $mutasi->status_diperiksa == "DIKONFIRMASI")
                                            <li>
                                                <a class="dropdown-item" href="{{ route('mutasiindengh.show', ['mutasiIG' => $mutasi->id]) }}">
                                                    <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail Mutasi
                                                </a>
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
    $(document).ready(function() {

        window.routes = {
            editPurchase: "{{ route('mutasiindengh.editpurchase', ['mutasiIG' => '__ID__']) }}",
            showMutasi: "{{ route('mutasiindengh.show', ['mutasiIG' => '__ID__']) }}",
            createRetur: "{{ route('create.retur', ['mutasiIG' => '__ID__']) }}",
            editRetur: "{{ route('edit.retur', ['idretur' => '__ID__']) }}",
            showRetur: "{{ route('show.returinden', ['mutasiIG' => '__ID__']) }}",
            editFinance: "{{ route('mutasiindengh.editfinance', ['mutasiIG' => '__ID__']) }}",
            confirmPayment: "{{ route('mutasiindengh.show', ['mutasiIG' => '__ID__']) }}",
            confirmRetur: "{{ route('show.returinden', ['mutasiIG' => '__ID__']) }}",
            editMutasi: "{{ route('mutasiindengh.edit', ['mutasiIG' => '__ID__']) }}"
        };
        
        $('#mutasiInden').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("mutasiindengh.index") }}',
                type: 'GET',
                data: function (d) {
                    d.gallery = $('#filterGallery').val();
                    d.dateStart = $('#filterDateStart').val();
                    d.dateEnd = $('#filterDateEnd').val();
                },
                dataSrc: function(json) {
                        console.log("Received Data:", json); 
                        return json.data;
                    }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'no_mutasi', name: 'no_mutasi' },
                { data: 'supplier', name: 'supplier' },
                { data: 'penerima', name: 'penerima' },
                { data: 'tgl_kirim', name: 'tgl_kirim' },
                { data: 'tgl_diterima', name: 'tgl_diterima' },
                {
                    data: 'status_dibuat',
                    name: 'status_dibuat',
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
                    data: 'status_diterima',
                    name: 'status_diterima',
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
                    data: 'status_diperiksa',
                    name: 'status_diperiksa',
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
                { data: 'status_dibuku', name: 'status_dibuku' },
                { data: 'tagihan', name: 'tagihan' },
                { data: 'sisa_tagihan', name: 'sisa_tagihan' },
                {
                    data: 'status_pembayaran',
                    name: 'status_pembayaran',
                    render: function(data, type, row) {
                        // Assume `status_dibukukan` is also available in the row data
                        let statusDibukukan = row.status_dibuku;
                        let sisaBayar = row.sisa_tagihan;

                        let statusHtml = '';

                        if (statusDibukukan !== 'BATAL') {
                            if (sisaBayar == 0 && sisaBayar !== null) {
                                statusHtml = '<span class="badges bg-lightgreen">Lunas</span>';
                            } else {
                                statusHtml = '<span class="badges bg-lightred">Belum Lunas</span>';
                            }
                        } else {
                            statusHtml = '<span class="badges bg-lightgrey">BATAL</span>';
                        }

                        return statusHtml;
                    }
                },
                { data: 'komplain', name: 'komplain' },
                { data: 'status_komplain', name: 'status_komplain' },
                {
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        const userRoles = @json(Auth::user()->roles->pluck('name')->toArray());

                        let dropdownHtml = `
                            <div class="dropdown">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <div class="dropdown-menu">`;

                        if (userRoles.includes('AdminGallery')) {
                            if (row.status_diterima === null) {
                                dropdownHtml += `
                                    <li>
                                        <a class="dropdown-item" href="${window.routes.editMutasi.replace('__ID__', row.id)}">
                                            <img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Acc Terima
                                        </a>
                                    </li>`;
                            } else if (row.status_diterima === "DIKONFIRMASI") {
                                dropdownHtml += `
                                    <li>
                                        <a class="dropdown-item" href="${window.routes.showMutasi.replace('__ID__', row.id)}">
                                            <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail Mutasi
                                        </a>
                                    </li>`;
                            }
                        }

                        // Actions for Auditor role
                        if (userRoles.includes('Auditor')) {
                            if (row.status_diperiksa === null) {
                                dropdownHtml += `
                                    <li>
                                        <a class="dropdown-item" href="${window.routes.editMutasi.replace('__ID__', row.id)}">
                                            <img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Periksa
                                        </a>
                                    </li>`;
                            } else if (row.status_diperiksa === "DIKONFIRMASI") {
                                dropdownHtml += `
                                    <li>
                                        <a class="dropdown-item" href="${window.routes.showMutasi.replace('__ID__', row.id)}">
                                            <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail Mutasi
                                        </a>
                                    </li>`;
                            }
                        }
                        
                        // Actions for Purchasing role
                        if (userRoles.includes('Purchasing')) {
                            if (row.status_dibuat === "TUNDA") {
                                dropdownHtml += `
                                    <li>
                                        <a class="dropdown-item" href="${window.routes.editPurchase.replace('__ID__', row.id)}">
                                            <img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit
                                        </a>
                                    </li>`;
                            } else {
                                dropdownHtml += `
                                    <li>
                                        <a class="dropdown-item" href="${window.routes.showMutasi.replace('__ID__', row.id)}">
                                            <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail Mutasi
                                        </a>
                                    </li>`;
                                if (row.returinden && row.returinden.status_dibuat !== "BATAL") {
                                    dropdownHtml += `
                                        <li>
                                            <a class="dropdown-item" href="${window.routes.showRetur.replace('__ID__', row.id)}">
                                                <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail Retur
                                            </a>
                                        </li>`;
                                }
                            }
                            if ((row.sisa_bayar === row.total_biaya || row.sisa_bayar === 0) && row.status_dibuku === "MENUNGGU PEMBAYARAN" && !row.returinden) {
                                dropdownHtml += `
                                    <li>
                                        <a class="dropdown-item" href="${window.routes.createRetur.replace('__ID__', row.id)}">
                                            <img src="/assets/img/icons/return1.svg" class="me-2" alt="img">Komplain
                                        </a>
                                    </li>`;
                            }
                            if (row.returinden && (row.returinden.status_dibuat === "DIKONFIRMASI" && (row.returinden.status_dibuku === "TUNDA" || row.returinden.status_dibuku === null))) {
                                dropdownHtml += `
                                    <li>
                                        <a class="dropdown-item" href="${window.routes.editRetur.replace('__ID__', row.returinden.id)}">
                                            <img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit Komplain
                                        </a>
                                    </li>`;
                            }
                        }

                        // Actions for Finance role
                        if (userRoles.includes('Finance')) {
                            if (row.status_dibuku === "TUNDA" || row.status_dibuku === null) {
                                dropdownHtml += `
                                    <li>
                                        <a class="dropdown-item" href="${window.routes.editFinance.replace('__ID__', row.id)}">
                                            <img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Acc Harga
                                        </a>
                                    </li>`;
                            }
                            if (row.status_dibuku === "MENUNGGU PEMBAYARAN" && row.sisa_bayar !== 0 && (!row.returinden || (row.returinden && row.returinden.status_dibuku === "BATAL"))) {
                                dropdownHtml += `
                                    <li>
                                        <a class="dropdown-item" href="${window.routes.confirmPayment.replace('__ID__', row.id)}">
                                            <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img">Bayar Mutasi
                                        </a>
                                    </li>`;
                            } else if (row.status_dibuku === "MENUNGGU PEMBAYARAN" && row.sisa_bayar === 0 && (!row.returinden || (row.returinden && row.returinden.status_dibuku === "BATAL"))) {
                                dropdownHtml += `
                                    <li>
                                        <a class="dropdown-item" href="${window.routes.confirmPayment.replace('__ID__', row.id)}">
                                            <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img">Konfirmasi
                                        </a>
                                    </li>`;
                            } else if ((row.status_dibuku === "DIKONFIRMASI" && row.sisa_bayar === 0 && !row.returinden) || (row.returinden && row.returinden.status_dibuku === "BATAL")) {
                                dropdownHtml += `
                                    <li>
                                        <a class="dropdown-item" href="${window.routes.showMutasi.replace('__ID__', row.id)}">
                                            <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail Mutasi
                                        </a>
                                    </li>`;
                            }
                            if (row.returinden) {
                                if ((row.returinden.status_dibuku === "TUNDA" || row.returinden.status_dibuku === null) && row.returinden.status_dibuat === "DIKONFIRMASI") {
                                    dropdownHtml += `
                                        <li>
                                            <a class="dropdown-item" href="${window.routes.editRetur.replace('__ID__', row.returinden.id)}">
                                                <img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit Komplain
                                            </a>
                                        </li>`;
                                }
                                if (row.returinden.status_dibuku === "MENUNGGU PEMBAYARAN" && (row.returinden.sisa_refund !== 0 || row.sisa_bayar !== 0)) {
                                    dropdownHtml += `
                                        <li>
                                            <a class="dropdown-item" href="${window.routes.showRetur.replace('__ID__', row.id)}">
                                                <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img">Bayar Mutasi/Input Refund
                                            </a>
                                        </li>`;
                                } else if (row.returinden.status_dibuku === "MENUNGGU PEMBAYARAN" && row.returinden.sisa_refund === 0 && row.sisa_bayar === 0) {
                                    dropdownHtml += `
                                        <li>
                                            <a class="dropdown-item" href="${window.routes.showRetur.replace('__ID__', row.id)}">
                                                <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img">Konfirmasi Retur
                                            </a>
                                        </li>`;
                                } else if (row.status_dibuku === "MENUNGGU PEMBAYARAN" && row.returinden.status_dibuku === "DIKONFIRMASI") {
                                    dropdownHtml += `
                                        <li>
                                            <a class="dropdown-item" href="${window.routes.showRetur.replace('__ID__', row.id)}">
                                                <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img">Konfirmasi Retur
                                            </a>
                                        </li>`;
                                }
                                
                            }
                        }
                        if (row.returinden) {

                            dropdownHtml += `
                                            <li>
                                                <a class="dropdown-item" href="${window.routes.editRetur.replace('__ID__', row.returinden.id)}">
                                                    <img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit Komplain
                                                </a>
                                            </li>`;
                        }

                        dropdownHtml += `
                                </div>
                            </div>`;

                        return dropdownHtml;
                    }
                }
            ]
        });
    });
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