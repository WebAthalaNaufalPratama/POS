@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Purchase Order</h4>
                    </div>
                    @php
                        $user = Auth::user();
                    @endphp
                    @if($user->hasRole(['Purchasing']))
                    <div class="page-btn">
                        <a href="{{ route('pembelian.create') }}" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Pembelian</a>
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
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterSupplier" name="filterSupplier" class="form-control" title="Supplier">
                            <option value="">Pilih Supplier</option>
                            @foreach ($supplierTrd as $item)
                                <option value="{{ $item->id }}" {{ $item->id == request()->input('supplier') ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if(Auth::user()->hasRole('Purchasing') || Auth::user()->hasRole('Auditor'))
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterGallery" name="filterGallery" class="form-control" title="Gallery">
                            <option value="">Pilih Gallery</option>
                            @foreach ($galleryTrd as $item)
                                <option value="{{ $item->id }}" {{ $item->id == request()->input('gallery') ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    @if(Auth::user()->hasRole('Purchasing'))
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterStatus" name="filterStatus" class="form-control" title="Status">
                            <option>Pilih Status</option>
                            <option value="Lunas" {{ request()->input('status') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                            <option value="Belum Lunas" {{ request()->input('status') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                            <option value="Belum Ada Tagihan" {{ request()->input('status') == 'Belum Ada Tagihan' ? 'selected' : '' }}>Belum Ada Tagihan</option>
                            {{-- <option value="Invoice Batal" {{ request()->input('status') == 'Invoice Batal' ? 'selected' : '' }}>Invoice Batal</option> --}}
                        </select>
                    </div>
                    @endif
                    <div class="col-sm-2">
                        <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('pembelian.index') }}" class="btn btn-info">Filter</a>
                        <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('pembelian.index') }}" class="btn btn-warning">Clear</a>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table" id="po">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Purchase Order</th>
                                <th>Supplier</th>
                                <th>Lokasi</th>
                                <th>Tanggal Kirim</th>
                                <th>Tanggal Terima</th>
                                <th>No DO Supplier</th>
                                <th>Status Dibuat</th>
                                <th>Status Diterima</th>
                                <th>Status Diperiksa</th>
                                @if($user->hasRole(['Purchasing','Finance']))
                                <th>Status Pembayaran</th>
                                @endif
                                <th>Barang Retur</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($datapos as $datapo)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $datapo->no_po }}</td>
                                <td>{{ $datapo->supplier->nama }}</td>
                                <td>{{ $datapo->lokasi->nama }}</td>
                                <td>{{ tanggalindo($datapo->tgl_kirim) }}</td>
                                <td>{{ $datapo->tgl_diterima ? tanggalindo($datapo->tgl_diterima) : '-' }}</td>
                                <td>{{ $datapo->no_do_suplier }}</td>

                                <td>
                       
                                    @if($datapo->status_dibuat == 'BATAL')
                                    <span class="badges bg-lightgrey">BATAL</span>
                                    @elseif($datapo->status_dibuat == 'TUNDA' || $datapo->status_dibuat == null)
                                    <span class="badges bg-lightred">TUNDA</span>
                                    @elseif($datapo->status_dibuat == 'DIKONFIRMASI')
                                    <span class="badges bg-lightgreen">DIKONFIRMASI</span>
                                    @endif
                                </td>
                                
                                <td>
                                    @if($datapo->lokasi->tipe_lokasi == 1)

                                        @if($datapo->status_diterima == 'BATAL')
                                            <span class="badges bg-lightgrey">BATAL</span>
                                        @elseif($datapo->status_diterima == 'TUNDA' || $datapo->status_diterima == null)
                                            <span class="badges bg-lightred">TUNDA</span>
                                        @elseif($datapo->status_diterima == 'DIKONFIRMASI')
                                            <span class="badges bg-lightgreen">DIKONFIRMASI</span>
                                        @else
                                        {{$datapo->status_diterima  }}
                                        @endif

                                    @else  --}}
                                    {{-- mengikuti auditor --}}
                                        {{-- @if($datapo->status_diperiksa == 'BATAL')
                                            <span class="badges bg-lightgrey">BATAL</span>
                                        @elseif($datapo->status_diperiksa == 'TUNDA' || $datapo->status_diperiksa == null)
                                            <span class="badges bg-lightred">TUNDA</span>
                                        @elseif($datapo->status_diperiksa == 'DIKONFIRMASI')
                                            <span class="badges bg-lightgreen">DIKONFIRMASI</span>
                                        @endif
                                    @endif

                                </td>
                                
                                <td>
                                    @if($datapo->status_diperiksa == 'BATAL')
                                        <span class="badges bg-lightgrey">BATAL</span>
                                    @elseif($datapo->status_diperiksa == 'TUNDA' || $datapo->status_diperiksa == null)
                                        <span class="badges bg-lightred">TUNDA</span>
                                    @elseif($datapo->status_diperiksa == 'DIKONFIRMASI')
                                        <span class="badges bg-lightgreen">DIKONFIRMASI</span>
                                    @endif
                                </td>
                                

                                @if(Auth::user()->hasRole(['Purchasing', 'Finance']))
                                    <td>
                                        @if ($datapo->invoice !== null && $datapo->invoice->sisa == 0)
                                            Lunas
                                        @elseif($datapo->invoice !== null && $datapo->invoice->sisa !== 0 && $datapo->invoice->status_dibuat !== "BATAL")
                                            Belum Lunas
                                        @elseif ($datapo->invoice !== null && $datapo->invoice->status_dibuat == "BATAL")
                                            Invoice Batal
                                        @elseif($datapo->invoice == null && $datapo->status_dibuat == "BATAL")
                                        -
                                        @elseif($datapo->invoice == null)
                                            Belum Ada Tagihan
                                        @endif
                                    </td>
                                @endif
                                <td>
                                    @if($datapo->no_retur !== null) 
                                        {{ $datapo->no_retur }}
                                    @else
                                        -
                                    @endif
                                </td>
                                @php --}}
                                    {{-- // Filter invoice dengan status yang bukan Batal
                                    $invoiceItems = $datainv->where('pembelian_id', $datapo->id)
                                                            ->where('status_dibuat', '!=', 'BATAL');
                                                                    // Filter invoice dengan status Batal

                                @endphp
                        
                                @if(Auth::user()->hasRole(['Purchasing', 'Auditor', 'Finance','AdminGallery']))
                                    <td class="text-center">
                                        <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            
                                            <li>
                                                <a href="{{ route('pembelian.show', ['type' => 'pembelian', 'datapo' => $datapo->id]) }}" class="dropdown-item">
                                                    <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img"> Detail PO
                                                </a>
                                            </li>
                                            @if(Auth::user()->hasRole(['Purchasing']))
                                                @if ($datapo->status_dibuat == "TUNDA")
                                                    <li>
                                                        <a href="{{ route('pembelian.editpurchase', ['type' => 'pembelian', 'datapo' => $datapo->id]) }}" class="dropdown-item">
                                                            <img src="/assets/img/icons/edit.svg" class="me-2" alt="img"> Edit PO
                                                        </a>
                                                    </li>
                                                @endif
                                                @if (($datapo->invoice && $datapo->invoice->status_dibuat == "BATAL") || ( !$datapo->invoice && $datapo->status_diperiksa == "DIKONFIRMASI") )
                                                <li>
                                                    <a href="{{ route('invoicebiasa.create', ['type' => 'pembelian', 'datapo' => $datapo->id]) }}" class="dropdown-item">
                                                        <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Create Invoice
                                                    </a>
                                                </li>
                                                @endif
                                            @endif
                        
                                            @if(Auth::user()->hasRole(['Auditor']))
                                                @if ($datapo->status_diperiksa == "TUNDA" || $datapo->status_diperiksa == null)
                                                    <li>
                                                        <a href="{{ route('pembelian.editaudit', ['type' => 'pembelian', 'datapo' => $datapo->id]) }}" class="dropdown-item">
                                                            <img src="/assets/img/icons/edit.svg" class="me-2" alt="img"> Periksa
                                                        </a>
                                                    </li>
                                                 @endif
                                            @endif

                                            @if(Auth::user()->hasRole(['AdminGallery']))
                                            @if ($datapo->status_dibuat == "DIKONFIRMASI" && $datapo->status_diterima == null)
                                                <li>
                                                    <a href="{{ route('pembelian.edit', ['type' => 'pembelian', 'datapo' => $datapo->id]) }}" class="dropdown-item">
                                                        <img src="/assets/img/icons/edit.svg" class="me-2" alt="img"> Acc Terima
                                                    </a>
                                                </li>
                                             @endif
                                             @endif
                                        </ul>
                                    </td>
                                @endif
                            </tr>
                        @endforeach --}}
                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- @unless(Auth::user()->hasRole('AdminGallery')) --}}
   
@if(Auth::user()->hasRole('Purchasing') || Auth::user()->hasRole('Auditor') || Auth::user()->hasRole('Finance'))
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Purchase Order Inden</h4>
                    </div>
                    @if($user->hasRole(['Purchasing']))
                    <div class="page-btn">
                        <a href="{{ route('pembelianinden.create') }}" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Pembelian</a>
                    </div>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row ps-2 pe-2">
                    <div class="col-sm-2 ps-0 pe-0">
                        <input type="date" class="form-control" name="filterDateStartInd" id="filterDateStartInd" value="{{ request()->input('dateStartInd') }}" title="Tanggal Awal">
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <input type="date" class="form-control" name="filterDateEndInd" id="filterDateEndInd" value="{{ request()->input('dateEndInd') }}" title="Tanggal Akhir">
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterSupplierInd" name="filterSupplierInd" class="form-control" title="Supplier">
                            <option value="">Pilih Supplier</option>
                            @foreach ($supplierInd as $item)
                                <option value="{{ $item->id }}" {{ $item->id == request()->input('supplierInd') ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if(Auth::user()->hasRole(['Purchasing', 'Finance']))
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterStatusInd" name="filterStatusInd" class="form-control" title="Status">
                            <option>Pilih Status</option>
                            <option value="Lunas" {{ request()->input('statusind') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                            <option value="Belum Lunas" {{ request()->input('statusind') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                            <option value="Belum Ada Tagihan" {{ request()->input('statusind') == 'Belum Ada Tagihan' ? 'selected' : '' }}>Belum Ada Tagihan</option>
                            {{-- <option value="Invoice Batal" {{ request()->input('statusind') == 'Invoice Batal' ? 'selected' : '' }}>Invoice Batal</option> --}}
                        </select>
                    </div>
                    @endif
                    <div class="col-sm-2">
                        <a href="javascript:void(0);" id="filterBtnInd" data-base-url="{{ route('pembelian.index') }}" class="btn btn-info">Filter</a>
                        <a href="javascript:void(0);" id="clearBtnInd" data-base-url="{{ route('pembelian.index') }}" class="btn btn-warning">Clear</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table" id="inden">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Purchase Order</th>
                                <th>Tanggal PO</th>
                                <th>Supplier</th>
                                <th>Bulan Stok Inden</th>
                                <th>Status Dibuat</th>
                                <th>Status Diperiksa</th>
                                @if(Auth::user()->hasRole(['Purchasing', 'Finance']))
                                <th>Status Pembayaran</th>
                                @endif
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($datainden as $inden)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $inden->no_po }}</td>
                                <td>{{ $inden->tgl_dibuat }}</td>
                                <td>{{ $inden->supplier->nama }}</td>
                                <td>{{ $inden->bulan_inden}}</td>
                                <td>
                                    @if($inden->status_dibuat == 'BATAL')
                                    <span class="badges bg-lightgrey">BATAL</span>
                                    @elseif($inden->status_dibuat == 'TUNDA' || $inden->status_dibuat == null)
                                    <span class="badges bg-lightred">TUNDA</span>
                                    @elseif($inden->status_dibuat == 'DIKONFIRMASI')
                                    <span class="badges bg-lightgreen">DIKONFIRMASI</span>
                                    @endif
                               
                                </td>
                                <td>
                                    @if($inden->status_diperiksa == 'BATAL')
                                        <span class="badges bg-lightgrey">BATAL</span>
                                    @elseif($inden->status_diperiksa == 'TUNDA' || $inden->status_diperiksa == null)
                                        <span class="badges bg-lightred">TUNDA</span>
                                    @elseif($inden->status_diperiksa == 'DIKONFIRMASI')
                                        <span class="badges bg-lightgreen">DIKONFIRMASI</span>
                                    @endif
                                </td>
                                @if(Auth::user()->hasRole(['Purchasing', 'Finance']))
                                <td>
                                    @if ($inden->invoice !== null && $inden->invoice->sisa == 0)
                                    Lunas
                                    @elseif($inden->invoice !== null && $inden->invoice->sisa !== 0 && $inden->invoice->status_dibuat !== "BATAL")
                                        Belum Lunas
                                    @elseif ($inden->invoice !== null && $inden->invoice->status_dibuat == "BATAL")
                                        Invoice Batal
                                    @elseif($inden->invoice == null && $inden->status_dibuat == "BATAL")
                                    -
                                    @elseif($inden->invoice == null)
                                        Belum Ada Tagihan
                                    @endif
                                </td>
                                @endif
                                    @php
                                        $invoiceExists = $datainv->where('poinden_id', $inden->id)
                                                                ->where('status_dibuat', '!=', 'BATAL');
                                                // Mengambil data retur pertama yang memiliki 'invoicepo_id' sama dengan $inv->id
                                                // $invoiceRetur = $dataretur->firstWhere('invoicepo_id', $inden->id);
                                    @endphp
                                <td class="text-center">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                <ul class="dropdown-menu"> --}}

                                {{-- @if ($inden->status_diperiksa == "DIKONFIRMASI")
                                    @if(Auth::user()->hasRole(['Finance']))
                                        <li>
                                        @foreach ($invoiceExists as $invoice)
                                                @if ($invoice->sisa != 0)
                                                    <a href="{{ route('invoice.edit',['datapo' => $inden->id, 'type' => 'poinden', 'id' => $datainv->id]) }}" class="dropdown-item">
                                                        <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Pembayaran Invoice
                                                    </a>
                                                @elseif($invoice->sisa == 0)
                                                    <a href="{{ route('invoice.show',['datapo' => $inden->id, 'type' => 'poinden', 'id' => $datainv->id]) }}" class="dropdown-item">
                                                        <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Detail Invoice
                                                    </a>
                                                @endif
                                        @endforeach
                                        </li>
                                    @endif
                                @endif --}}

                                {{-- @if ($inden->status_diperiksa == "DIKONFIRMASI")
                                    @if(Auth::user()->hasRole(['Purchasing']))
                                        <li>
                                            <a href="{{ route('invoicebiasa.create', ['type' => 'poinden', 'datapo' => $inden->id]) }}" class="dropdown-item"><img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Create Invoice
                                            </a>
                                        </li>
                                    @endif
                                @endif
                                        <li>
                                            <a href="{{ route('pembelian.show', ['type' => 'poinden','datapo' => $inden->id]) }}" class="dropdown-item"><img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail PO</a>
                                        </li>

                                @if(Auth::user()->hasRole('Purchasing'))
                                    @if ($inden->status_dibuat == "TUNDA" || $inden->status_dibuat == null )
                                        <li>
                                            <a href="{{ route('pembelian.edit', ['type' => 'poinden','datapo' => $inden->id]) }}" class="dropdown-item"><img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit PO</a>
                                        </li>
                                    @endif
                                @endif

                                @if(Auth::user()->hasRole('Auditor'))
                                    @if ($inden->status_diperiksa == null && $inden->status_dibuat == "DIKONFIRMASI")
                                        <li>
                                            <a href="{{ route('pembelian.edit', ['type' => 'poinden','datapo' => $inden->id]) }}" class="dropdown-item"><img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Periksa</a>
                                        </li>
                                    @endif
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
@endif

{{-- @endunless --}}
@endsection

@section('scripts')
<script>
    $(document).ready(function(){
        $('[id^=filterSupplier], [id^=filterGallery], [id^=filterStatus]').select2();

        // Start Datatable PO
            const columns = [
                { data: 'no', name: 'no', orderable: false },
                { data: 'no_po', name: 'no_po' },
                { data: 'supplier_nama', name: 'supplier_nama', orderable: false },
                { data: 'lokasi_nama', name: 'lokasi_nama', orderable: false },
                { 
                    data: 'tgl_kirim',
                    name: 'tgl_kirim',
                    render: function(data, type, row) {
                        return row.tgl_kirim_format;
                    }  
                },
                { 
                    data: 'tgl_diterima', 
                    name: 'tgl_diterima',
                    render: function(data, type, row) {
                        return row.tgl_diterima_format;
                    }  
                },
                { data: 'no_do_suplier', name: 'no_do_suplier' },
                { 
                    data: 'status_dibuat', 
                    name: 'status_dibuat',
                    render: function(data) {
                        switch (data) {
                            case 'BATAL': return '<span class="badges bg-lightgrey">BATAL</span>';
                            case 'TUNDA': return '<span class="badges bg-lightred">TUNDA</span>';
                            case 'DIKONFIRMASI': return '<span class="badges bg-lightgreen">DIKONFIRMASI</span>';
                            default: return '-';
                        }
                    }
                },
                { 
                    data: 'status_diterima', 
                    name: 'status_diterima',
                    render: function(data) {
                        switch (data) {
                            case 'BATAL': return '<span class="badges bg-lightgrey">BATAL</span>';
                            case 'TUNDA': return '<span class="badges bg-lightred">TUNDA</span>';
                            case 'DIKONFIRMASI': return '<span class="badges bg-lightgreen">DIKONFIRMASI</span>';
                            default: return '-';
                        }
                    }
                },
                { 
                    data: 'status_diperiksa', 
                    name: 'status_diperiksa',
                    render: function(data) {
                        switch (data) {
                            case 'BATAL': return '<span class="badges bg-lightgrey">BATAL</span>';
                            case 'TUNDA': return '<span class="badges bg-lightred">TUNDA</span>';
                            case 'DIKONFIRMASI': return '<span class="badges bg-lightgreen">DIKONFIRMASI</span>';
                            default: return '<span class="badges bg-lightred">TUNDA</span>';
                        }
                    }
                },
                @if(Auth::user()->hasRole(['Purchasing', 'Finance']))
                { data: 'status_pembayaran', name: 'status_pembayaran', orderable: false },
                @endif
                { data: 'no_retur', name: 'no_retur' },
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

                        // Detail PO for roles: Purchasing, Auditor, Finance, AdminGallery
                        if (['Purchasing', 'Auditor', 'Finance', 'AdminGallery'].includes(row.userRole)) {
                            actionsHtml += `
                                <li>
                                    <a href="pembelian/${row.id}/show?type=pembelian" class="dropdown-item">
                                        <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img"> Detail PO
                                    </a>
                                </li>
                            `;
                        }

                        // Edit PO for Purchasing if status_dibuat is TUNDA
                        if (row.userRole === 'Purchasing' && row.status_dibuat === 'TUNDA') {
                            actionsHtml += `
                                <li>
                                    <a href="pembelian/${row.id}/editpurchase_po?type=pembelian" class="dropdown-item">
                                        <img src="/assets/img/icons/edit.svg" class="me-2" alt="img"> Edit PO
                                    </a>
                                </li>
                            `;
                        }

                        // Create Invoice for Purchasing when invoice is BATAL or status_diperiksa is DIKONFIRMASI
                        if (row.userRole === 'Purchasing' && ((row.invoice && row.invoice.status_dibuat === 'BATAL') || (!row.invoice && row.status_diperiksa === 'DIKONFIRMASI'))) {
                            actionsHtml += `
                                <li>
                                    <a href="invoice/pembelian/${row.id}/createinv" class="dropdown-item">
                                        <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Create Invoice
                                    </a>
                                </li>
                            `;
                        }

                        // Periksa for Auditor when status_diperiksa is TUNDA or null
                        if (row.userRole === 'Auditor' && (row.status_diperiksa === 'TUNDA' || row.status_diperiksa === null)) {
                            actionsHtml += `
                                <li>
                                    <a href="pembelian/${row.id}/edit_po_audit?type=pembelian" class="dropdown-item">
                                        <img src="/assets/img/icons/edit.svg" class="me-2" alt="img"> Periksa
                                    </a>
                                </li>
                            `;
                        }

                        // Acc Terima for AdminGallery when status_dibuat is DIKONFIRMASI and status_diterima is null
                        if (row.userRole === 'AdminGallery' && row.status_dibuat === 'DIKONFIRMASI' && row.status_diterima === null) {
                            actionsHtml += `
                                <li>
                                    <a href="pembelian/${row.id}/edit_po?type=pembelian" class="dropdown-item">
                                        <img src="/assets/img/icons/edit.svg" class="me-2" alt="img"> Acc Terima
                                    </a>
                                </li>
                            `;
                        }

                        actionsHtml += `
                                </ul>
                            </div>
                        `;

                        return actionsHtml;
                    }
                }
            ];

            let table = initDataTable('#po', {
                ajaxUrl: "{{ route('pembelian.index') }}",
                columns: columns,
                order: [[1, 'asc']],
                searching: true,
                lengthChange: true,
                pageLength: 5
            }, {
                supplier: '#filterSupplier',
                gallery: '#filterGallery',
                status: '#filterStatus',
                dateStart: '#filterDateStart',
                dateEnd: '#filterDateEnd'
            }, 'po');

            const handleSearch = debounce(function() {
                table.ajax.reload();
            }, 5000); // Adjust the debounce delay as needed

            $('#filterSupplier, #filterGallery, #filterStatus, #filterDateStart, #filterDateEnd').on('input', handleSearch);

            $('#filterBtn').on('click', function() {
                table.ajax.reload();
            });

            $('#clearBtn').on('click', function() {
                $('#filterSupplier').val('');
                $('#filterGallery').val('');
                $('#filterStatus').val('');
                $('#filterDateStart').val('');
                $('#filterDateEnd').val('');
                table.ajax.reload();
            });
        // End Datatble PO

        // Start Datatable INDEN
            const columns2 = [
                { data: 'no', name: 'no', orderable: false },
                { data: 'no_po', name: 'no_po' },
                { 
                    data: 'tgl_dibuat', 
                    name: 'tgl_dibuat', 
                    render: function(data, type, row) {
                        return row.tgl_dibuat_format;
                    }  
                },
                { data: 'supplier_nama', name: 'supplier_nama', orderable: false },
                { data: 'bulan_inden', name: 'bulan_inden' },
                { 
                    data: 'status_dibuat', 
                    name: 'status_dibuat',
                    render: function(data) {
                        switch (data) {
                            case 'BATAL': return '<span class="badges bg-lightgrey">BATAL</span>';
                            case 'TUNDA': return '<span class="badges bg-lightred">TUNDA</span>';
                            case 'DIKONFIRMASI': return '<span class="badges bg-lightgreen">DIKONFIRMASI</span>';
                            default: return '-';
                        }
                    }
                },
                { 
                    data: 'status_diperiksa', 
                    name: 'status_diperiksa',
                    render: function(data) {
                        switch (data) {
                            case 'BATAL': return '<span class="badges bg-lightgrey">BATAL</span>';
                            case 'TUNDA': return '<span class="badges bg-lightred">TUNDA</span>';
                            case 'DIKONFIRMASI': return '<span class="badges bg-lightgreen">DIKONFIRMASI</span>';
                            default: return '<span class="badges bg-lightred">TUNDA</span>';;
                        }
                    }
                },
                @if(Auth::user()->hasRole(['Purchasing', 'Finance']))
                { data: 'status_pembayaran', name: 'status_pembayaran', orderable: false },
                @endif
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

                        // Detail PO
                        actionsHtml += `
                            <li>
                                <a href="pembelian/${row.id}/show?type=poinden" class="dropdown-item">
                                    <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img"> Detail PO
                                </a>
                            </li>
                        `;

                        // Edit PO for Purchasing if status_dibuat is TUNDA
                        if (row.userRole === 'Purchasing' && (row.status_dibuat === 'TUNDA' || row.status_dibuat === null)) {
                            actionsHtml += `
                                <li>
                                    <a href="pembelian/${row.id}/edit_po?type=poinden" class="dropdown-item">
                                        <img src="/assets/img/icons/edit.svg" class="me-2" alt="img"> Edit PO
                                    </a>
                                </li>
                            `;
                        }

                        // Create Invoice for Purchasing
                        if (row.userRole === 'Purchasing' && ((row.invoice && row.invoice.status_dibuat === 'BATAL') || (!row.invoice && row.status_diperiksa === 'DIKONFIRMASI'))) {
                            actionsHtml += `
                                <li>
                                    <a href="invoice/poinden/${row.id}/createinv" class="dropdown-item">
                                        <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Create Invoice
                                    </a>
                                </li>
                            `;
                        }

                        // Auditor Periksa
                        if (row.userRole === 'Auditor' && (row.status_diperiksa === 'TUNDA' || row.status_diperiksa === null)) {
                            actionsHtml += `
                                <li>
                                    <a href="pembelian/${row.id}/edit_po?type=poinden" class="dropdown-item">
                                        <img src="/assets/img/icons/edit.svg" class="me-2" alt="img"> Periksa
                                    </a>
                                </li>
                            `;
                        }

                        actionsHtml += `
                                </ul>
                            </div>
                        `;

                        return actionsHtml;
                    }
                }
            ];

            let table2 = initDataTable('#inden', {
                ajaxUrl: "{{ route('pembelian.index') }}",
                columns: columns2,
                order: [[1, 'asc']],
                searching: true,
                lengthChange: true,
                pageLength: 5
            }, {
                supplierInd: '#filterSupplierInd',
                statusInd: '#filterStatusInd',
                dateStartInd: '#filterDateStartInd',
                dateEndInd: '#filterDateEndInd'
            }, 'inden');

            const handleSearch2 = debounce(function() {
                table2.ajax.reload();
            }, 5000); // Adjust the debounce delay as needed

            $('#filterSupplierInd, #filterStatusInd, #filterDateStartInd, #filterDateEndInd').on('input', handleSearch2);

            $('#filterBtnInd').on('click', function() {
                table2.ajax.reload();
            });

            $('#clearBtnInd').on('click', function() {
                $('#filterSupplierInd').val('');
                $('#filterStatusInd').val('');
                $('#filterDateStartInd').val('');
                $('#filterDateEndInd').val('');
                table2.ajax.reload();
            });
        // End Datatble INDEN
    });
    // $('[id^=filterBtn]').click(function(){
    //     var baseUrl = $(this).data('base-url');
    //     var urlString = baseUrl;
    //     var first = true;
    //     var symbol = '';

    //     var supplier = $('#filterSupplier').val();
    //     if (supplier) {
    //         var filtersupplier = 'supplier=' + supplier;
    //         if (first == true) {
    //             symbol = '?';
    //             first = false;
    //         } else {
    //             symbol = '&';
    //         }
    //         urlString += symbol;
    //         urlString += filtersupplier;
    //     }

    //     var gallery = $('#filterGallery').val();
    //     if (gallery) {
    //         var filtergallery = 'gallery=' + gallery;
    //         if (first == true) {
    //             symbol = '?';
    //             first = false;
    //         } else {
    //             symbol = '&';
    //         }
    //         urlString += symbol;
    //         urlString += filtergallery;
    //     }

    //     var status = $('#filterStatus').val();
    //     if (status) {
    //         var filterstatus = 'status=' + status;
    //         if (first == true) {
    //             symbol = '?';
    //             first = false;
    //         } else {
    //             symbol = '&';
    //         }
    //         urlString += symbol;
    //         urlString += filterstatus;
    //     }

    //     var dateStart = $('#filterDateStart').val();
    //     if (dateStart) {
    //         var filterDateStart = 'dateStart=' + dateStart;
    //         if (first == true) {
    //             symbol = '?';
    //             first = false;
    //         } else {
    //             symbol = '&';
    //         }
    //         urlString += symbol;
    //         urlString += filterDateStart;
    //     }

    //     var dateEnd = $('#filterDateEnd').val();
    //     if (dateEnd) {
    //         var filterDateEnd = 'dateEnd=' + dateEnd;
    //         if (first == true) {
    //             symbol = '?';
    //             first = false;
    //         } else {
    //             symbol = '&';
    //         }
    //         urlString += symbol;
    //         urlString += filterDateEnd;
    //     }


    //     var supplier = $('#filterSupplierInd').val();
    //     if (supplier) {
    //         var filtersupplier = 'supplierInd=' + supplier;
    //         if (first == true) {
    //             symbol = '?';
    //             first = false;
    //         } else {
    //             symbol = '&';
    //         }
    //         urlString += symbol;
    //         urlString += filtersupplier;
    //     }

    //     var status = $('#filterStatusInd').val();
    //     if (status) {
    //         var filterstatus = 'statusInd=' + status;
    //         if (first == true) {
    //             symbol = '?';
    //             first = false;
    //         } else {
    //             symbol = '&';
    //         }
    //         urlString += symbol;
    //         urlString += filterstatus;
    //     }

    //     var dateStart = $('#filterDateStartInd').val();
    //     if (dateStart) {
    //         var filterDateStart = 'dateStartInd=' + dateStart;
    //         if (first == true) {
    //             symbol = '?';
    //             first = false;
    //         } else {
    //             symbol = '&';
    //         }
    //         urlString += symbol;
    //         urlString += filterDateStart;
    //     }

    //     var dateEnd = $('#filterDateEndInd').val();
    //     if (dateEnd) {
    //         var filterDateEnd = 'dateEndInd=' + dateEnd;
    //         if (first == true) {
    //             symbol = '?';
    //             first = false;
    //         } else {
    //             symbol = '&';
    //         }
    //         urlString += symbol;
    //         urlString += filterDateEnd;
    //     }
    //     window.location.href = urlString;
    // });
    // $('[id^=clearBtn]').click(function(){
    //     var baseUrl = $(this).data('base-url');
    //     var url = window.location.href;
    //     if(url.indexOf('?') !== -1){
    //         window.location.href = baseUrl;
    //     }
    //     return 0;
    // });
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
@endsection