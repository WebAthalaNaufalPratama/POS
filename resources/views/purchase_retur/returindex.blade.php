@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Retur Pembelian</h4>
                    </div>
                    <div class="page-btn">
                        {{-- <a href="{{ route('returbeli.create') }}" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Retur Pembelian</a> --}}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row ps-2 pe-2">
                    <div class="col-sm-2 ps-0 pe-0">
                        <input type="text" class="form-control" name="filterDateStart" id="filterDateStart" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Tanggal Awal Retur" value="{{ request()->input('dateStart') }}" title="Tanggal Awal">
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <input type="text" class="form-control" name="filterDateEnd" id="filterDateEnd" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Tanggal Akhir Retur" value="{{ request()->input('dateEnd') }}" title="Tanggal Akhir">
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterGallery" name="filterGallery" class="form-control" title="Gallery">
                            <option value="">Pilih Gallery</option>
                            @foreach ($gallery as $item)
                                <option value="{{ $item->id }}" {{ $item->id == request()->input('gallery') ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('returbeli.index') }}" class="btn btn-info">Filter</a>
                        <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('returbeli.index') }}" class="btn btn-warning">Clear</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table pb-5" id="returTable" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Retur</th>
                                <th>Tanggal</th>
                                <th>Suplier</th>
                                <th>Lokasi</th>
                                <th>Produk</th>
                                <th>Alasan</th>
                                <th>Jumlah</th>
                                <th>Komplain</th>
                                <th>Total Harga</th>
                                <th>Status dibuat</th>
                                <th>Status dibuku</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody> 
                            {{-- @foreach ($dataretur as $data)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $data->no_retur }}</td>
                                <td>{{ tanggalindo($data->tgl_retur)}}</td>
                                <td>{{ $data->invoice->pembelian->supplier->nama }}</td>
                                <td>{{ $data->invoice->pembelian->lokasi->nama }}</td>
                                <td>
                                    <ul>
                                        @foreach($data->produkretur as $produkretur)
                                            <li>{{ $produkretur->produkbeli->produk->nama }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        @foreach($data->produkretur as $produkretur)
                                            <li>{{ $produkretur->alasan }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        @foreach($data->produkretur as $produkretur)
                                            <li>{{ $produkretur->jumlah }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                @php
                                        $pembelianRetur = $pembelian->firstWhere('no_retur', $data->no_retur ?? null);
                                @endphp
                                <td>{{ $data->komplain }} 
                                    @if($data->komplain == "Refund")
                                        @if($data->sisa == 0)
                                           | Lunas
                                        @else
                                           | Belum Lunas
                                        @endif
                                    @endif
                                    @if($data->komplain == "Retur")
                                        @if($pembelianRetur)
                                           | {{ $pembelianRetur->no_po }}
                                        @else
                                           | PO belum dibuat
                                        @endif
                                    @endif
                                </td>
                                <td>{{ formatRupiah($data->subtotal)}}</td>
                                <td>
                                    @if ($data->status_dibuat == 'BATAL')
                                        <span class="badges bg-lightgrey">BATAL</span>
                                    @elseif ($data->status_dibuat == 'TUNDA' || $data->status_dibuat == null)
                                        <span class="badges bg-lightred">TUNDA</span>
                                    @elseif ($data->status_dibuat == 'DIKONFIRMASI')
                                        <span class="badges bg-lightgreen">DIKONFIRMASI</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($data->status_dibuku == 'BATAL')
                                        <span class="badges bg-lightgrey">BATAL</span>
                                    @elseif ($data->status_dibuku == 'TUNDA' || $data->status_dibuku == null)
                                        <span class="badges bg-lightred">TUNDA</span>
                                    @elseif ($data->status_dibuku == 'DIKONFIRMASI')
                                        <span class="badges bg-lightgreen">DIKONFIRMASI</span>
                                    @endif
                                </td>
                                
                                <td class="text-center">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        
                                        @if(Auth::user()->hasRole('Finance'))
                                        <li>
                                            <a href="{{ route('returbeli.show', ['retur_id' => $data->id]) }}" class="dropdown-item">
                                                <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img">
                                                @if($data->komplain == "Refund" && $data->sisa !== 0 )
                                                    Input Refund
                                                @else
                                                    Detail Retur
                                                @endif
                                            </a>
                                        </li>
                                        @endif

                                        @if(Auth::user()->hasRole('Purchasing'))
                                        <li>
                                            <a href="{{ route('returbeli.show', ['retur_id' => $data->id]) }}" class="dropdown-item"><img src="/assets/img/icons/eye1.svg" class="me-2" alt="img"> Detail Retur</a>
                                        </li>
                                        @if($data->status_dibuat == "TUNDA" || $data->status_dibuat == "BATAL")
                                        <li>
                                            <a href="{{ route('returbeli.edit', ['retur_id' => $data->id]) }}" class="dropdown-item"><img src="/assets/img/icons/edit.svg" class="me-2" alt="img"> Edit Retur</a>
                                        </li>
                                        @endif

                                        @endif
                                        @if(Auth::user()->hasRole(['Finance']))
                                            @if($data->status_dibuat == "DIKONFIRMASI" && ( $data->status_dibuku == "TUNDA" || $data->status_dibuku == null || $data->status_dibuku == "BATAL" )  )
                                            <li>
                                            <a href="{{ route('returbeli.edit', ['retur_id' => $data->id]) }}" class="dropdown-item"><img src="/assets/img/icons/edit.svg" class="me-2" alt="img"> Edit Retur</a>
                                            </li>
                                            @endif
                                        @endif
                                        <li>
                                            <a href="{{ route('invoice.show', ['datapo' => $data->invoice->pembelian_id, 'type'=>"pembelian", 'id' => $data->invoice->id]) }}" class="dropdown-item"><img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail Invoice</a>
                                        </li>
                                        
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
        $('#filterSupplier, #filterGallery, #filterStatus').select2();

        window.routes = {
            ReturBeliShow: "{{ route('returbeli.show', ['retur_id' => '__ID__']) }}",
            ReturBeliEdit: "{{ route('returbeli.edit', ['retur_id' => '__ID__']) }}",
            InvoiceShow: "{{ route('invoice.show', ['datapo' => '__IDINVOICEBELI__', 'type' => 'pembelian', 'id' => '__IDINVOICE__']) }}",
        };

        $('#returTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("returbeli.index") }}', // Update this URL to match your route
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
                { data: 'no_retur', name: 'no_retur' },
                { data: 'tgl_retur', name: 'tgl_retur' },
                { data: 'supplier_name', name: 'supplier_name' },
                { data: 'lokasi_name', name: 'lokasi_name' },
                { data: 'produk', name: 'produk' },
                { data: 'alasan', name: 'alasan' },
                { data: 'jumlah', name: 'jumlah' },
                { data: 'komplain', name: 'komplain' },
                { data: 'subtotal', name: 'subtotal' },
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
                    data: 'status_dibuku',
                    name: 'status_dibuku',
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
                                badgeClass = 'bg-lightred';
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
                        // Convert user roles to JavaScript array
                        const userRoles = @json(Auth::user()->roles->pluck('name')->toArray());
                        
                        let dropdownHtml = `
                            <div class="dropdown">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <div class="dropdown-menu">`;

                        // Generate action links based on roles and status
                        if (userRoles.includes('Finance')) {
                            if(row.status_dibuku === 'DIKONFIRMASI') {
                                dropdownHtml += `
                                <a href="${window.routes.ReturBeliShow.replace('__ID__', row.id)}" class="dropdown-item">
                                    <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img">
                                    ${row.komplain === 'Refund' && row.sisa != 0 ? 'Input Refund' : 'Detail Retur'}
                                </a>`;
                            }
                            
                            if (row.status_dibuat === 'DIKONFIRMASI' && (row.status_dibuku === 'TUNDA' || row.status_dibuku === null || row.status_dibuku === 'BATAL')) {
                                dropdownHtml += `
                                    <a href="${window.routes.ReturBeliEdit.replace('__ID__', row.id)}" class="dropdown-item">
                                        <img src="/assets/img/icons/edit.svg" class="me-2" alt="img"> Edit Retur
                                    </a>`;
                            }
                        }

                        if (userRoles.includes('Purchasing')) {
                            dropdownHtml += `
                                <a href="${window.routes.ReturBeliShow.replace('__ID__', row.id)}" class="dropdown-item">
                                    <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img"> Detail Retur
                                </a>`;
                            
                            if (row.status_dibuat === 'TUNDA' || row.status_dibuat === 'BATAL') {
                                dropdownHtml += `
                                    <a href="${window.routes.ReturBeliEdit.replace('__ID__', row.id)}" class="dropdown-item">
                                        <img src="/assets/img/icons/edit.svg" class="me-2" alt="img"> Edit Retur
                                    </a>`;
                            }
                        }

                        if (userRoles.includes('Auditor')) {
                            dropdownHtml += `
                                <a href="${window.routes.ReturBeliShow.replace('__ID__', row.id)}" class="dropdown-item">
                                    <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img"> Detail Retur
                                </a>`;
                     
                        }

                        dropdownHtml += `
                                <a href="${window.routes.InvoiceShow
                                    .replace('__IDINVOICEBELI__', row.invoice.pembelian_id)
                                    .replace('__IDINVOICE__', row.invoice.id)}" 
                                class="dropdown-item">
                                    <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img"> Detail Invoice
                                </a>`;

                        dropdownHtml += `</div></div>`;
                        
                        return dropdownHtml;
                    }
                }
            ]
        });
    });

    $('[id^=filterBtn]').click(function(){
        var baseUrl = $(this).data('base-url');
        var urlString = baseUrl;
        var first = true;
        var symbol = '';

        var gallery = $('#filterGallery').val();
        if (gallery) {
            var filtergallery = 'gallery=' + gallery;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filtergallery;
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
    $('[id^=clearBtn]').click(function(){
        var baseUrl = $(this).data('base-url');
        var url = window.location.href;
        if(url.indexOf('?') !== -1){
            window.location.href = baseUrl;
        }
        return 0;
    });
    </script>
@endsection