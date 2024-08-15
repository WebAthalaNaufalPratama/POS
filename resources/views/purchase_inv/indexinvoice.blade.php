@extends('layouts.app-von')

@section('content')
@php
$user = Auth::user();
@endphp
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Invoice Pembelian</h4>
                    </div>
                    <div class="page-btn">
                        {{-- <a href="{{ route('pembelian.create') }}" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Invoice</a> --}}
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
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterSupplier" name="filterSupplier" class="form-control" title="Supplier">
                            <option value="">Pilih Supplier</option>
                            @foreach ($supplierTrd as $item)
                                <option value="{{ $item->id }}" {{ $item->id == request()->input('supplier') ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterGallery" name="filterGallery" class="form-control" title="Gallery">
                            <option value="">Pilih Gallery</option>
                            @foreach ($galleryTrd as $item)
                                <option value="{{ $item->id }}" {{ $item->id == request()->input('gallery') ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterStatus" name="filterStatus" class="form-control" title="Status">
                            <option>Pilih Status</option>
                            <option value="Lunas" {{ request()->input('status') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                            <option value="Belum Lunas" {{ request()->input('status') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('invoicebeli.index') }}" class="btn btn-info">Filter</a>
                        <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('invoicebeli.index') }}" class="btn btn-warning">Clear</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table" id="po">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Invoice</th>
                                <th>Tanggal Invoice</th>
                                <th>No PO</th>
                                <th>Supplier</th>
                                <th>lokasi</th>
                                <th>Status</th>
                                <th>Nominal</th>
                                <th>Sisa Tagihan</th>
                                <th>Status dibuat</th>
                                <th>Status dibuku</th>
                                <th>Komplain</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                         <tbody>
                            {{-- @foreach ($invoices as $inv)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $inv->no_inv }}</td>
                                <td>{{ tanggalindo($inv->tgl_inv) }}</td>
                                <td>{{ $inv->pembelian->no_po }}</td>
                                <td>{{ $inv->pembelian->supplier->nama }}</td>
                                <td>{{ $inv->pembelian->lokasi->nama}}</td>
                                <td>
                                    @if ($inv->sisa == 0)
                                        <span class="badges bg-lightgreen">Lunas</span>
                                    @elseif($inv->sisa !== 0 && $inv->status_dibuat !== 'BATAL')
                                        <span class="badges bg-lightred">Belum Lunas</span>
                                    @elseif($inv->status_dibuat == 'BATAL')
                                    <span class="badges bg-lightgrey">BATAL</span>
                                    @endif
                                </td>
                                <td>{{ formatRupiah($inv->total_tagihan) }}</td>
                                
                                <td>
                                {{ formatRupiah($inv->sisa) }}
                                </td>
                                <td>
                                    @if ($inv->status_dibuat == 'TUNDA' || $inv->status_dibuat == null)
                                        <span class="badges bg-lightred">TUNDA</span>
                                    @elseif ($inv->status_dibuat == 'DIKONFIRMASI')
                                        <span class="badges bg-lightgreen">DIKONFIRMASI</span>
                                    @elseif ($inv->status_dibuat == 'BATAL')
                                        <span class="badges bg-lightgrey">BATAL</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($inv->status_dibuku == 'TUNDA' || $inv->status_dibuku == null)
                                        <span class="badges bg-lightred">TUNDA</span>
                                    @elseif ($inv->status_dibuku == 'DIKONFIRMASI')
                                        <span class="badges bg-lightgreen">DIKONFIRMASI</span>
                                    @elseif ($inv->status_dibuku == 'MENUNGGU PEMBAYARAN' && $inv->sisa !== 0)
                                        <span class="badges bg-lightyellow">MENUNGGU PEMBAYARAN</span>
                                    @elseif ($inv->status_dibuku == 'MENUNGGU PEMBAYARAN' && $inv->sisa == 0)
                                        <span class="badges bg-lightyellow">MENUNGGU KONFIRMASI</span>
                                    @endif
                                </td>
                                
                                <td>
                                    @php
                                        $invoiceRetur = $dataretur->firstWhere('invoicepo_id', $inv->id);
                                        $pembelianRetur = $pembelian->firstWhere('no_retur', $inv->retur->no_retur ?? null);
                                    @endphp
                                
                                    @if ($invoiceRetur && isset($inv->retur) && $inv->retur->status_dibuat !== "BATAL" && $inv->retur->status_dibuku !== "BATAL" )
                                        {{ $inv->retur->komplain }}  
                                        @if ($inv->retur->komplain == "Refund")
                                            @if ($inv->retur->sisa == 0 )
                                                | Lunas
                                            @else
                                                | Belum Lunas
                                            @endif
                                        @endif
                                        @if ($inv->retur->komplain == "Retur")
                                            @if (!$pembelianRetur && $inv->retur->status_dibuku == "DIKONFIRMASI")
                                                | PO retur belum dibuat
                                            @elseif($pembelianRetur)
                                                | {{$pembelianRetur->no_po }}
                                            @endif
                                        @endif
                                        @if($inv->retur->status_dibuku == null || $inv->retur->status_dibuku == "TUNDA")
                                        | Belum Dikonfirmasi
                                        @endif
                                    @elseif($invoiceRetur && isset($inv->retur) && ($inv->retur->status_dibuat == "BATAL" || $inv->retur->status_dibuku == "BATAL"))
                                        Komplain Batal
                                    @endif
                                </td>
                                
                               
                                <td class="text-center">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            @php
                                                // Mengambil data retur pertama yang memiliki 'invoicepo_id' sama dengan $inv->id
                                                $invoiceRetur = $dataretur->firstWhere('invoicepo_id', $inv->id);
                                             @endphp
                                        
                                       
                                        @if($invoiceRetur && $invoiceRetur->status_dibuat == "DIKONFIRMASI" && $inv->sisa !== 0 )
                                 
                                        @elseif(($inv->sisa == 0 || $inv->sisa == $inv->total_tagihan) && !$invoiceRetur && ($inv->status_dibuku !== "TUNDA" || $inv->status_dibuku !== null) && $inv->status_dibuat == "DIKONFIRMASI" && $inv->status_dibuku == "MENUNGGU PEMBAYARAN" )
                                            @if(Auth::user()->hasRole('Purchasing'))
                                                <a href="{{ route('returbeli.create', ['invoice' => $inv->id]) }}" class="dropdown-item">
                                                    <img src="/assets/img/icons/return1.svg" class="me-2" alt="img">Komplain
                                                </a>
                                            @endif
                                         @endif
                                         
                                        </li>
                                        @if(Auth::user()->hasRole('Purchasing'))
                                            @if ($inv->status_dibuat == "TUNDA")
                                                <li>
                                                    <a href="{{ route('invoicepurchase.edit', ['datapo' => $inv->pembelian->id, 'type' => 'pembelian', 'id' => $inv->id]) }}" class="dropdown-item">
                                                        <img src="/assets/img/icons/edit.svg" class="me-2" alt="img"> Edit Invoice
                                                    </a>
                                                </li>
                                            @endif
                                        @endif
                                       
                                    @if(Auth::user()->hasRole('Finance'))

                                        @if($inv->sisa == 0)
                                            @if((!$invoiceRetur && $inv->status_dibuku == "MENUNGGU PEMBAYARAN") || ($invoiceRetur && ($invoiceRetur->status_dibuat == "BATAL" || $invoiceRetur->status_dibuku == "BATAL")  && $inv->status_dibuku == "MENUNGGU PEMBAYARAN") || ($invoiceRetur && $invoiceRetur->sisa == 0 && $inv->status_dibuku == "MENUNGGU PEMBAYARAN" && $invoiceRetur->status_dibuat !== "BATAL" ))
                                            <li>
                                                <a href="{{ route('invoice.edit', ['datapo' => $inv->pembelian->id, 'type' => 'pembelian', 'id' => $inv->id]) }}" class="dropdown-item">
                                                    <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Konfirmasi
                                                </a>
                                            </li>
                                            @endif
                                        @else

                                       
                                        @if(!$invoiceRetur || ($invoiceRetur && $invoiceRetur->status_dibuku =="DIKONFIRMASI"))
                                            @if ($inv->status_dibuku == "MENUNGGU PEMBAYARAN")
                                            <li>
                                                <a href="{{ route('invoice.edit', ['datapo' => $inv->pembelian->id, 'type' => 'pembelian', 'id' => $inv->id]) }}" class="dropdown-item">
                                                    <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Pembayaran Invoice
                                                </a>
                                            </li>
                                            @elseif ($inv->status_dibuku == "TUNDA" || $inv->status_dibuku == null )
                                            <li>
                                                <a href="{{ route('invoicepurchase.edit', ['datapo' => $inv->pembelian->id, 'type' => 'pembelian', 'id' => $inv->id]) }}" class="dropdown-item">
                                                    <img src="/assets/img/icons/edit.svg" class="me-2" alt="img"> Edit Invoice
                                                </a>
                                            </li>
                                            @endif
                                        @endif

                                        @endif
                                    @endif                                 
                                       
                                        
                                        <li>
                                            <a href="{{ route('invoice.show',['datapo' => $inv->pembelian->id, 'type' => 'pembelian', 'id' => $inv->id]) }}" class="dropdown-item">
                                                <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail
                                            </a>
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
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Invoice Pembelian Inden</h4>
                    </div>
                    <div class="page-btn">
                        {{-- <a href="{{ route('pembelianinden.create') }}" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Invoice</a> --}}
                    </div>
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
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterStatusInd" name="filterStatusInd" class="form-control" title="Status">
                            <option value="">Pilih Status</option>
                            <option value="Lunas" {{ request()->input('statusInd') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                            <option value="Belum Lunas" {{ request()->input('statusInd') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <a href="javascript:void(0);" id="filterBtnInd" data-base-url="{{ route('invoicebeli.index') }}" class="btn btn-info">Filter</a>
                        <a href="javascript:void(0);" id="clearBtnInd" data-base-url="{{ route('invoicebeli.index') }}" class="btn btn-warning">Clear</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table" id="inden">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Invoice</th>
                                <th>No PO</th>
                                <th>Supplier</th>
                                <th>Bulan Inden</th>
                                <th>Tanggal Invoice</th>
                                <th>Status</th>
                                <th>Nominal</th>
                                <th>Sisa Tagihan</th>
                                <th>Status Dibuat</th>
                                <th>Status Dibuku</th>
                                {{-- <th>Komplain</th> --}}
                                <th>Aksi</th>
                            </tr>
                        </thead>
                         <tbody>
                            {{-- @foreach ($invoiceinden as $inv)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $inv->no_inv }}</td>
                                <td>{{ $inv->poinden->no_po }}</td>
                                <td>{{ $inv->poinden->supplier->nama }}</td>
                                <td>{{ $inv->poinden->bulan_inden}}</td>
                                <td>{{ tanggalindo($inv->tgl_inv)}}</td>
                                <td>
                                    @if ($inv->sisa == 0)
                                        <span class="badges bg-lightgreen">Lunas</span>
                                    @elseif($inv->sisa !== 0 && $inv->status_dibuat !== 'BATAL')
                                        <span class="badges bg-lightred">Belum Lunas</span>
                                    @elseif($inv->status_dibuat == 'BATAL')
                                    <span class="badges bg-lightgrey">BATAL</span>
                                    @endif
                                </td>
                                <td>{{ formatRupiah($inv->total_tagihan) }}</td>
                                
                                <td>
                                {{ formatRupiah($inv->sisa) }}
                                </td>
                                <td>
                                    @if ($inv->status_dibuat == 'TUNDA' || $inv->status_dibuat == null)
                                        <span class="badges bg-lightred">TUNDA</span>
                                    @elseif ($inv->status_dibuat == 'DIKONFIRMASI')
                                        <span class="badges bg-lightgreen">DIKONFIRMASI</span>
                                    @elseif ($inv->status_dibuat == 'BATAL')
                                        <span class="badges bg-lightgrey">BATAL</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($inv->status_dibuku == 'TUNDA' || $inv->status_dibuku == null)
                                        <span class="badges bg-lightred">TUNDA</span>
                                    @elseif ($inv->status_dibuku == 'DIKONFIRMASI')
                                        <span class="badges bg-lightgreen">DIKONFIRMASI</span>
                                    @elseif ($inv->status_dibuku == 'MENUNGGU PEMBAYARAN' && $inv->sisa !== 0)
                                        <span class="badges bg-lightyellow">MENUNGGU PEMBAYARAN</span>
                                    @elseif ($inv->status_dibuku == 'MENUNGGU PEMBAYARAN' && $inv->sisa == 0)
                                        <span class="badges bg-lightyellow">MENUNGGU KONFIRMASI</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        @php
                                            $user = Auth::user();
                                        @endphp
                                      
                                        @if($user->hasRole(['Purchasing']))
                                            @if($inv->status_dibuat == "TUNDA")
                                            <li>
                                                <a href="{{ route('editinvoice.edit', ['datapo' => $inv->poinden->id, 'type' => 'poinden']) }}" class="dropdown-item"><img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit Invoice</a>
                                            </li>
                                            @endif
                                        @endif

                                        @if($user->hasRole(['Finance']))
                                            @if($inv->status_dibuku == "TUNDA" || $inv->status_dibuku == null )
                                            <li>
                                                <a href="{{ route('editinvoice.edit', ['datapo' => $inv->poinden->id, 'type' => 'poinden']) }}" class="dropdown-item"><img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit Invoice</a>
                                            </li>
                                            @endif

                                            @if($inv->sisa !== 0 && $inv->status_dibuku == "MENUNGGU PEMBAYARAN")
                                            <li>
                                                <a href="{{ route('invoice.edit',['datapo' => $inv->poinden->id, 'type' => 'poinden', 'id' => $inv->id]) }}" class="dropdown-item">
                                                    <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Pembayaran Invoice
                                                </a>
                                            </li>
                                            @endif

                                            @if($inv->sisa == 0)
                                            @if((!$invoiceRetur && $inv->status_dibuku == "MENUNGGU PEMBAYARAN") || ($invoiceRetur && ($invoiceRetur->status_dibuat == "BATAL" || $invoiceRetur->status_dibuku == "BATAL")  && $inv->status_dibuku == "MENUNGGU PEMBAYARAN") || ($invoiceRetur && $invoiceRetur->sisa == 0 && $inv->status_dibuku == "MENUNGGU PEMBAYARAN" && $invoiceRetur->status_dibuat !== "BATAL" ))
                                            <li>
                                                <a href="{{ route('invoice.edit',['datapo' => $inv->poinden->id, 'type' => 'poinden', 'id' => $inv->id]) }}" class="dropdown-item">
                                                    <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Konfirmasi
                                                </a>
                                            </li>
                                            @endif
                                            @endif
                                        @endif
                                        <li>
                                            <a href="{{ route('invoice.show',['datapo' => $inv->poinden->id, 'type' => 'poinden', 'id' => $inv->id]) }}" class="dropdown-item">
                                                <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail
                                            </a>
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

<div class="modal fade" id="modalBayar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form Pembayaran</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('pembayaranbeli.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="no_invoice">Nomor PO</label>
                            <input type="text" class="form-control" id="no_po" name="no_po" placeholder="Nomor Po" required readonly>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="no_invoice">Nomor Invoice</label>
                            <input type="text" class="form-control" id="no_invoice_bayar" name="no_invoice_bayar" placeholder="Nomor Invoice" value="{{ $no_invpo }}" required readonly>
                            <input type="hidden" id="invoice_purchase_id" name="invoice_purchase_id" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="no_invoice">Total Tagihan</label>
                            <input type="text" class="form-control" id="total_tagihan" name="total_tagihan" placeholder="Total Taqgihan" required readonly>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="no_invoice">Sisa Tagihan</label>
                            <input type="text" class="form-control" id="sisa_tagihan" name="sisa_tagihan" placeholder="Sisa Taqgihan" required readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="bayar">Cara Bayar</label>
                            <select class="form-control" id="bayar" name="cara_bayar" required>
                                <option value="">Pilih Cara Bayar</option>
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-6" id="rekening" style="display: none">
                            <label for="bankpenerima">Rekening Vonflorist</label>
                            <select class="form-control" id="rekening_id" name="rekening_id" required>
                                <option value="">Pilih Rekening Von</option>
                                @foreach ($bankpens as $bankpen)
                                <option value="{{ $bankpen->id }}">{{ $bankpen->nama_akun }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="nominal">Nominal</label>
                            <input type="number" class="form-control" id="nominal" name="nominal" value="" placeholder="Nominal Bayar" required>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="tanggalbayar">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal_bayar" name="tanggal_bayar" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label for="buktibayar">Unggah Bukti</label>
                            <input type="file" class="form-control" id="bukti" name="bukti" accept="image/*" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var cekInvoiceNumbers = "{{ $invoice_bayar }}";
    var nextInvoiceNumber = parseInt(cekInvoiceNumbers) + 1;
    $(document).ready(function(){
        $('[id^=filterSupplier], [id^=filterGallery], [id^=filterStatus]').select2();
    });
    // Start Datatable PO
        const columns = [
            { data: 'no', name: 'no', orderable: false },
            { data: 'no_inv', name: 'no_inv' },
            { 
                data: 'tgl_inv', 
                name: 'tgl_inv', 
                render: function(data, type, row) {
                    return row.tgl_inv_format;
                } 
            },
            { data: 'no_po', name: 'no_po', orderable: false  },
            { data: 'supplier_nama', name: 'supplier_nama', orderable: false  },
            { data: 'lokasi_nama', name: 'lokasi_nama', orderable: false  },
            { 
                data: 'status', 
                name: 'status',
                render: function(data, type, row) {
                    return data;
                }, 
                orderable: false 
            },
            { 
                data: 'total_tagihan', 
                name: 'total_tagihan', 
                render: function(data, type, row) {
                    return row.total_tagihan_format;
                } 
            },
            { 
                data: 'sisa', 
                name: 'sisa',
                render: function(data, type, row) {
                    return row.sisa_format;
                }  
            },
            { 
                data: 'status_dibuat', 
                name: 'status_dibuat',
                render: function(data, type, row) {
                    return row.status_dibuat_format;
                } 
            },
            { 
                data: 'status_dibuku', 
                name: 'status_dibuku',
                render: function(data, type, row) {
                    return row.status_dibuku_format;
                } 
            },
            {data: 'komplain_format', name: 'komplain_format', orderable: false},
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
                                <a href="invoice/${row.pembelian_id}/show?type=pembelian&id=${row.id}" class="dropdown-item">
                                    <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img"> Detail
                                </a>
                            </li>
                        `;
                    }

                    // Komplain for Purchasing role if conditions met
                    if (row.userRole === 'Purchasing' &&
                        ((row.invoiceRetur && row.invoiceRetur.status_dibuat === 'DIKONFIRMASI' && row.sisa !== 0) ||
                        (row.sisa === 0 || row.sisa === row.total_tagihan) && !row.invoiceRetur &&
                        (row.status_dibuku !== "TUNDA" && row.status_dibuku !== null) &&
                        row.status_dibuat === "DIKONFIRMASI" &&
                        row.status_dibuku === "MENUNGGU PEMBAYARAN")) {
                        actionsHtml += `
                            <li>
                                <a href="retur/create?invoice=${row.id}" class="dropdown-item">
                                    <img src="/assets/img/icons/return1.svg" class="me-2" alt="img"> Komplain
                                </a>
                            </li>
                        `;
                    }

                    // Edit Invoice for Purchasing if status_dibuat is TUNDA
                    if (row.userRole === 'Purchasing' && row.status_dibuat == 'TUNDA') {
                        actionsHtml += `
                            <li>
                                <a href="invoice/${row.pembelian_id}/edit_inv_nominal?type=pembelian&id=${row.id}" class="dropdown-item">
                                    <img src="/assets/img/icons/edit.svg" class="me-2" alt="img"> Edit Invoice
                                </a>
                            </li>
                        `;
                    }

                    // Konfirmasi for Finance if conditions met
                    if (row.userRole === 'Finance' && row.sisa === 0) {
                        if ((!row.invoiceRetur && row.status_dibuku === "MENUNGGU PEMBAYARAN") ||
                            (row.invoiceRetur && (row.invoiceRetur.status_dibuat === "BATAL" || row.invoiceRetur.status_dibuku === "BATAL") &&
                            row.status_dibuku === "MENUNGGU PEMBAYARAN") ||
                            (row.invoiceRetur && row.invoiceRetur.sisa === 0 && row.status_dibuku === "MENUNGGU PEMBAYARAN" && row.invoiceRetur.status_dibuat !== "BATAL")) {
                            actionsHtml += `
                                <li>
                                    <a href="invoice/${row.pembelian_id}/edit?type=pembelian&id=${row.id}" class="dropdown-item">
                                        <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Konfirmasi
                                    </a>
                                </li>
                            `;
                        }
                    } else if (row.userRole === 'Finance') {
                        if (!row.invoiceRetur || (row.invoiceRetur && row.invoiceRetur.status_dibuku === "DIKONFIRMASI")) {
                            if (row.status_dibuku === "MENUNGGU PEMBAYARAN") {
                                actionsHtml += `
                                    <li>
                                        <a href="invoice/${row.pembelian_id}/edit?type=pembelian&id=${row.id}" class="dropdown-item">
                                            <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Pembayaran Invoice
                                        </a>
                                    </li>
                                `;
                            } else if (row.status_dibuku === "TUNDA" || row.status_dibuku === null) {
                                actionsHtml += `
                                    <li>
                                        <a href="invoice/${row.pembelian_id}/edit_inv_nominal?type=pembelian&id=${row.id}" class="dropdown-item">
                                            <img src="/assets/img/icons/edit.svg" class="me-2" alt="img"> Edit Invoice
                                        </a>
                                    </li>
                                `;
                            }
                        }
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
            ajaxUrl: "{{ route('invoicebeli.index') }}",
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
            $('#filterSupplier').val('').trigger('change');
            $('#filterGallery').val('').trigger('change');
            $('#filterStatus').val('').trigger('change');
            $('#filterDateStart').val('');
            $('#filterDateEnd').val('');
            table.ajax.reload();
        });
    // End Datatble PO

    // Start Datatable INDEN
        const columns2 = [
            { data: 'no', name: 'no', orderable: false },
            { data: 'no_inv', name: 'no_inv' },
            { data: 'no_po', name: 'no_po', orderable: false },
            { data: 'supplier_nama', name: 'supplier_nama', orderable: false },
            { data: 'bulan_inden', name: 'bulan_inden', orderable: false },
            { 
                data: 'tgl_inv',
                name: 'tgl_inv',
                render: function (data, type, row) {
                    return row.tgl_inv_format;
                }
            },
            { 
                data: 'status', 
                name: 'status',
                render: function(data, type, row) {
                    return data;
                }, 
                orderable: false
            },
            { 
                data: 'total_tagihan', 
                name: 'total_tagihan', 
                render: function (data, type, row) {
                    return row.total_tagihan_format;
                } 
            },
            { 
                data: 'sisa', 
                name: 'sisa',
                render: function(data, type, row) {
                    return row.sisa_format;
                }  
            },
            { 
                data: 'status_dibuat', 
                name: 'status_dibuat',
                render: function(data, type, row) {
                    return row.status_dibuat_format;
                }
            },
            { 
                data: 'status_dibuku', 
                name: 'status_dibuku',
                render: function(data, type, row) {
                    return row.status_dibuku_format;
                }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    var html = `<div class="text-center">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">`;

                    var userRole = row.userRole; // Asumsi bahwa userRole sudah ada di dalam data row

                    // Role 'Purchasing'
                    if (userRole === 'Purchasing') {
                        if (row.status_dibuat === 'TUNDA') {
                            html += `<li>
                                        <a href="invoice/${row.poinden_id}/editinvoice?type=poinden&id=${row.id}" class="dropdown-item">
                                            <img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit Invoice
                                        </a>
                                    </li>`;
                        }
                    }

                    // Role 'Finance'
                    if (userRole === 'Finance') {
                        if (row.status_dibuku === 'TUNDA' || row.status_dibuku === null) {
                            html += `<li>
                                        <a href="invoice/${row.poinden_id}/editinvoice?type=poinden&id=${row.id}" class="dropdown-item">
                                            <img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit Invoice
                                        </a>
                                    </li>`;
                        }

                        if (row.sisa !== 0 && row.status_dibuku === 'MENUNGGU PEMBAYARAN') {
                            html += `<li>
                                        <a href="invoice/${row.poinden_id}/edit?type=poinden&id=${row.id}" class="dropdown-item">
                                            <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img">Pembayaran Invoice
                                        </a>
                                    </li>`;
                        }

                        if (row.sisa === 0) {
                            if ((!row.invoiceRetur && row.status_dibuku === 'MENUNGGU PEMBAYARAN') ||
                                (row.invoiceRetur && (row.invoiceRetur.status_dibuat === 'BATAL' || row.invoiceRetur.status_dibuku === 'BATAL') && row.status_dibuku === 'MENUNGGU PEMBAYARAN') ||
                                (row.invoiceRetur && row.invoiceRetur.sisa === 0 && row.status_dibuku === 'MENUNGGU PEMBAYARAN' && row.invoiceRetur.status_dibuat !== 'BATAL')) {
                                html += `<li>
                                            <a href="invoice/${row.poinden_id}/edit?type=poinden&id=${row.id}" class="dropdown-item">
                                                <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img">Konfirmasi
                                            </a>
                                        </li>`;
                            }
                        }
                    }

                    // Detail
                    html += `<li>
                                <a href="invoice/${row.poinden_id}/show?type=poinden&id=${row.id}" class="dropdown-item">
                                    <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail
                                </a>
                            </li>
                            </ul></div>`;

                    return html;
                }
            }
        ];

        let table2 = initDataTable('#inden', {
            ajaxUrl: "{{ route('invoicebeli.index') }}",
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
            $('#filterSupplierInd').val('').trigger('change');
            $('#filterStatusInd').val('').trigger('change');
            $('#filterDateStartInd').val('');
            $('#filterDateEndInd').val('');
            table2.ajax.reload();
        });
    // End Datatble PO
    $('#bayar').on('change', function() {
        var caraBayar = $(this).val();
        if (caraBayar == 'transfer') {
            $('#rekening').show();
            $('#rekening_id').attr('required', true);
            $('#bukti').attr('required', true);
        } else {
            $('#rekening').hide();
            $('#rekening_id').attr('required', false);
            $('#bukti').attr('required', false);
        }
    });

    function formatRupiah(value) {
    // Ensure the value is a number
    var number = parseFloat(value);

    // Format the number with thousand separators and add the Rp prefix
    return 'Rp ' + number.toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    }

    function bayar(invoice){
        $('#no_po').val(invoice.pembelian.no_po);
        $('#invoice_purchase_id').val(invoice.id);
        $('#total_tagihan').val(formatRupiah(invoice.total_tagihan));
        $('#sisa_tagihan').val(formatRupiah(invoice.sisa));
        $('#nominal').val(invoice.sisa);
        $('#rekening_id').select2({
            dropdownParent: $("#modalBayar")
        });
        $('#bayar').select2({
            dropdownParent: $("#modalBayar")
        });
        $('#modalBayar').modal('show');
        generateInvoice();
    }
    function bayar2(invoice){
        $('#no_po').val(invoice.poinden.no_po);
        $('#invoice_purchase_id').val(invoice.id);
        $('#total_tagihan').val(invoice.total_tagihan);
        $('#sisa_tagihan').val(invoice.sisa);
        $('#nominal').val(invoice.sisa);
        $('#rekening_id').select2({
            dropdownParent: $("#modalBayar")
        });
        $('#bayar').select2({
            dropdownParent: $("#modalBayar")
        });
        $('#modalBayar').modal('show');
        generateInvoice();
    }

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