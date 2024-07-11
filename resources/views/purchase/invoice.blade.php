@extends('layouts.app-von')

@section('content')
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
                            <option value="">Pilih Status</option>
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
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Invoice</th>
                                <th>Tanggal Invoice</th>
                                <th>No PO</th>
                                <th>Supplier</th>
                                <th>lokasi</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Sisa Tagihan</th>
                                <th>Status Purchase</th>
                                <th>Status Finance</th>
                                <th>Komplain</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                         <tbody>
                            @foreach ($invoices as $inv)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $inv->no_inv }}</td>
                                <td>{{ tanggalindo($inv->tgl_inv) }}</td>
                                <td>{{ $inv->pembelian->no_po }}</td>
                                <td>{{ $inv->pembelian->supplier->nama }}</td>
                                <td>{{ $inv->pembelian->lokasi->nama}}</td>
                                <td>{{ formatRupiah($inv->total_tagihan) }}</td>
                                <td>
                                    @if ( $inv->sisa == 0)
                                        Lunas
                                    @else
                                        Belum Lunas
                                    @endif

                                </td>
                                <td>
                                {{ formatRupiah($inv->sisa) }}
                                </td>
                                <td> {{ $inv->status_dibuat }}</td>
                                <td> {{ $inv->status_dibuku }}</td>
                                <td>
                                @php
                                    // Mengambil data retur pertama yang memiliki 'invoicepo_id' sama dengan $inv->id
                                    $invoiceRetur = $dataretur->firstWhere('invoicepo_id', $inv->id);
                                @endphp
                                @if ($invoiceRetur)
                                {{ $inv->retur->komplain }}   
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
                                        
                                            @if ($invoiceRetur)
                                                {{-- <a href="{{ route('returinvoice.show', ['retur_id' => $invoiceRetur->id]) }}" class="dropdown-item">
                                                    <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail Invoice retur
                                                </a> --}}
                                            @elseif($inv->sisa == 0 || $inv->sisa == $inv->total_tagihan)
                                                <a href="{{ route('returbeli.create', ['invoice' => $inv->id]) }}" class="dropdown-item">
                                                    <img src="/assets/img/icons/return1.svg" class="me-2" alt="img">Komplain
                                                </a>
                                            @endif
                                        </li>
                                        @if(Auth::user()->hasRole('Purchasing'))
                                            @if($inv->status_dibuat == "TUNDA")
                                            <li>
                                                <a href="{{ route('invoice.edit',['datapo' => $inv->pembelian->id, 'type' => 'pembelian']) }}" class="dropdown-item">
                                                    <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Ubah Invoice
                                                </a>
                                            </li>
                                        @endif
                                        @endif
                                        @if(Auth::user()->hasRole('Finance'))
                                            @if($inv->status_dibuku == "TUNDA")
                                            <li>
                                                <a href="{{ route('invoice.edit',['datapo' => $inv->pembelian->id, 'type' => 'pembelian']) }}" class="dropdown-item">
                                                    <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Ubah Invoice
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" onclick="bayar({{ $inv }})" class="dropdown-item"><img src="/assets/img/icons/dollar-square.svg" class="me-2" alt="img">Bayar</a>
                                            </li>
                                            @endif
                                        @endif
                                        
                                        <li>
                                            <a href="{{ route('invoice.show',['datapo' => $inv->pembelian->id, 'type' => 'pembelian']) }}" class="dropdown-item">
                                                <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail
                                            </a>
                                        </li>

                                        {{-- @if ($inv->sisa == 0 || $inv->sisa == $inv->total_tagihan)
                                        <li>
                                            <a href="{{ route('returbeli.create', ['invoice' => $inv->id]) }}" class="dropdown-item"><img src="/assets/img/icons/return1.svg" class="me-2" alt="img">Komplain</a>
                                        </li>
                                        @endif --}}
                                        
                                        
                                    </ul>
                                </td>
                            </tr>
                            @endforeach
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
                        <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('invoicebeli.index') }}" class="btn btn-info">Filter</a>
                        <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('invoicebeli.index') }}" class="btn btn-warning">Clear</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Invoice</th>
                                <th>No PO</th>
                                <th>Supplier</th>
                                <th>Bulan Inden</th>
                                <th>Tanggal Invoice</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Sisa Tagihan</th>
                                <th>Komplain</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                         <tbody>
                            @foreach ($invoiceinden as $inv)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $inv->no_inv }}</td>
                                <td>{{ $inv->poinden->no_po }}</td>
                                <td>{{ $inv->poinden->supplier->nama }}</td>
                                <td>{{ $inv->poinden->bulan_inden}}</td>
                                <td>{{ tanggalindo($inv->tgl_inv)}}</td>
                                <td>{{ formatRupiah($inv->total_tagihan) }}</td>
                                <td>
                                    @if ( $inv->sisa == 0)
                                    Lunas
                                    @else
                                    Belum Lunas
                                    @endif
                                </td>
                                <td>{{ formatRupiah($inv->sisa) }}</td>
                                <td></td>
                                <td class="text-center">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        {{-- @if($inv->sisa !== 0)
                                        <li>
                                            <a href="{{ route('invoice.edit',['datapo' => $inv->poinden->id, 'type' => 'poinden']) }}" class="dropdown-item">
                                                <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Pembayaran Invoice
                                            </a>
                                        </li>
                                        @endif --}}
                                        @if($inv->status_dibuat == "TUNDA")
                                        <li>
                                            <a href="{{ route('invoice.edit',['datapo' => $inv->poinden->id, 'type' => 'poinden']) }}" class="dropdown-item">
                                                <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Ubah Invoice
                                            </a>
                                        </li>
                                        @endif
                                        <li>
                                            <a href="{{ route('invoice.show',['datapo' => $inv->poinden->id, 'type' => 'poinden']) }}" class="dropdown-item">
                                                <img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail
                                            </a>
                                        </li>
                                    
                                        <li>
                                            <a href="javascript:void(0);" onclick="bayar2({{ $inv }})" class="dropdown-item"><img src="/assets/img/icons/dollar-square.svg" class="me-2" alt="img">Bayar</a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            @endforeach
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
    $('[id^=filterBtn]').click(function(){
        var baseUrl = $(this).data('base-url');
        var urlString = baseUrl;
        var first = true;
        var symbol = '';

        var supplier = $('#filterSupplier').val();
        if (supplier) {
            var filtersupplier = 'supplier=' + supplier;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filtersupplier;
        }

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

        var status = $('#filterStatus').val();
        if (status) {
            var filterstatus = 'status=' + status;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterstatus;
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


        var supplier = $('#filterSupplierInd').val();
        if (supplier) {
            var filtersupplier = 'supplierInd=' + supplier;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filtersupplier;
        }

        var status = $('#filterStatusInd').val();
        if (status) {
            var filterstatus = 'statusInd=' + status;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterstatus;
        }

        var dateStart = $('#filterDateStartInd').val();
        if (dateStart) {
            var filterDateStart = 'dateStartInd=' + dateStart;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterDateStart;
        }

        var dateEnd = $('#filterDateEndInd').val();
        if (dateEnd) {
            var filterDateEnd = 'dateEndInd=' + dateEnd;
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