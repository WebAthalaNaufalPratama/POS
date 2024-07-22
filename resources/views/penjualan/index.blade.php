@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Invoice Penjualan</h4>
                    </div>
                    <div class="page-btn">
                        @php
                            $roles = Auth::user()->roles()->get();
                            $user = Auth::user();
                            $lokasi = \App\Models\Karyawan::where('user_id', $user->id)->first();
                            if($user->hasRole(['SuperAdmin'])) {
                                $rolePermissions = \Spatie\Permission\Models\Permission::pluck('name')->toArray();
                            } else {
                                $rolePermissions = [];
                                if (!$roles->isEmpty()) {
                                    $rolePermissions = $roles->flatMap->permissions->pluck('name')->toArray();
                                }
                            }
                        @endphp
                        @if(in_array('penjualan.create', $rolePermissions))
                            <a href="{{ route('penjualan.create') }}" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Penjualan</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row ps-2 pe-2">
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterSales" name="sales" class="form-control" title="sales">
                            <option value="">Pilih Sales</option>
                            @foreach($sales as $salesd)
                            <option value="{{ $salesd->id}}" {{ $salesd->id == request()->input('sales') ? 'selected' : '' }}>{{ $salesd->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterCustomer" name="customer" class="form-control" title="customer">
                            <option value="">Pilih Customer</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id}}" {{ $customer->id == request()->input('customer') ? 'selected' : '' }}>{{ $customer->nama }}</option>
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
                        <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('penjualan.index') }}" class="btn btn-info">Filter</a>
                        <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('penjualan.index') }}" class="btn btn-warning">Clear</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Invoice</th>
                                <th>Sales</th>
                                <th>Tanggal Invoice</th>
                                <th>Jatuh Tempo</th>
                                <th>Status Bayar</th>
                                <th>Total Tagihan</th>
                                <th>Sisa Bayar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penjualans as $penjualan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $penjualan->no_invoice }}</td>
                                <td>{{ $penjualan->karyawan->nama }}</td>
                                <td>{{ date('d F Y', strtotime($penjualan->tanggal_invoice)) }}</td>
                                <td>{{ date('d F Y', strtotime($penjualan->jatuh_tempo)) }}</td>
                                <td>
                                    @if(isset($latestPayments[$penjualan->id]))
                                    {{ $latestPayments[$penjualan->id]->status_bayar }}
                                    @else
                                    Belum ada pembayaran
                                    @endif
                                </td>
                                <td>{{ 'Rp '. number_format($penjualan->total_tagihan, 0, ',', '.',) }}</td>
                                <td>{{ 'Rp '. number_format($penjualan->sisa_bayar, 0, ',', '.',) }}</td>
                                <td>{{ $penjualan->status }}</td>
                                <td>
                                    <div class="dropdown">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    @if($penjualan->status != 'DIBATALKAN')
                                        <div class="dropdown-menu">
                                            @if($user->hasRole(['Auditor', 'Finance', 'SuperAdmin']))
                                                <a class="dropdown-item" href="{{ route('auditpenjualan.edit', ['penjualan' => $penjualan->id]) }}"><img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Audit</a>
                                            @elseif($user->hasRole(['AdminGallery', 'KasirGallery', 'KasirOutlet']) && $penjualan->status != 'DIKONFIRMASI')
                                                <a class="dropdown-item" href="{{ route('auditpenjualan.edit', ['penjualan' => $penjualan->id]) }}"><img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Edit</a>
                                            @endif
                                            @if($penjualan->status == 'DIKONFIRMASI')
                                            @if($lokasi->lokasi->tipe_lokasi != 2 && in_array('penjualan.show', $rolePermissions))
                                                <a class="dropdown-item" href="{{ route('penjualan.show', ['penjualan' => $penjualan->id]) }}"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Perangkai</a>
                                            @endif
                                            @if(in_array('penjualan.payment', $rolePermissions))
                                                <a class="dropdown-item" href="{{ route('penjualan.payment', ['penjualan' => $penjualan->id]) }}"><img src="assets/img/icons/dollar-square.svg" class="me-2" alt="img">Pembayaran</a>
                                            @endif
                                            @if($penjualan->distribusi == 'Dikirim' && in_array('dopenjualan.create', $rolePermissions))
                                            <a class="dropdown-item" href="{{ route('dopenjualan.create', ['penjualan' => $penjualan->id]) }}"><img src="assets/img/icons/truck.svg" class="me-2" alt="img">Delivery Order</a>
                                            @endif
                                            <!-- && $penjualan->dibukukan_id != null && $penjualan->auditor_id != null && $penjualan->status == 'DIKONFIRMASI' -->
                                            @php
                                                $retur = \App\Models\ReturPenjualan::where('no_invoice', $penjualan->no_invoice)->first();
                                            @endphp
                                            @if(in_array('returpenjualan.create', $rolePermissions) && $penjualan->status == 'DIKONFIRMASI' && empty($retur))
                                                <a class="dropdown-item" href="{{ route('returpenjualan.create', ['penjualan' => $penjualan->id]) }}"><img src="assets/img/icons/return1.svg" class="me-2" alt="img">Retur</a>
                                            @endif
                                            <a class="dropdown-item" href="{{ route('pdfinvoicepenjualan.generate', ['penjualan' => $penjualan->id]) }}"><img src="assets/img/icons/printer.svg" class="me-2" alt="img">Cetak Invoice</a>
                                            @if(!empty($retur))
                                                <a class="dropdown-item" href="{{ route('penjualan.view', ['penjualan' => $penjualan->id]) }}"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">View Retur</a>
                                            @endif
                                            @endif
                                            <!-- <a class="dropdown-item" href="javascript:void(0);" onclick="deleteData({{ $penjualan->id }})">Delete</a> -->
                                        </div>
                                    @else
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('auditpenjualan.show', ['penjualan' => $penjualan->id]) }}"><img src="assets/img/icons/edit-5.svg" class="me-2" alt="img">Show</a>
                                        </div>
                                    @endif
                                    </div>
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
        $('#rekening_id, #bayar, #filterMetode, #filterSales').select2();
    });

    $('#bayar').on('change', function() {
        var caraBayar = $(this).val();
        if (caraBayar == 'transfer') {
            $('#rekening').show();
            $('#rekening_id').attr('required', true);
        } else {
            $('#rekening').hide();
            $('#rekening_id').attr('required', false);
        }
    });
    $('#nominal').on('input', function() {
        var nominal = parseFloat($(this).val());
        var sisaTagihan = parseFloat($('#sisa_tagihan').val());
        if(nominal < 0) {
            $(this).val(0);
        }
        if(nominal > sisaTagihan) {
            $(this).val(sisaTagihan);
        }
    });
    $('#filterBtn').click(function(){
        var baseUrl = $(this).data('base-url');
        var urlString = baseUrl;
        var first = true;
        var symbol = '';

        var metode = $('#filterMetode').val();
        if (metode) {
            var filterMetode = 'metode=' + metode;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterMetode;
        }

        var sales = $('#filterSales').val();
        if (sales) {
            var filterSales = 'sales=' + sales;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterSales;
        }

        var status_bayar = $('#filterStatusBayar').val();
        if (status_bayar) {
            var filterStatusBayar = 'status_bayar=' + status_bayar;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterStatusBayar;
        }

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
    
    function bayar(invoice){
        console.log(invoice)
        $('#no_kontrak').val(invoice.no_sewa);
        $('#invoice_sewa_id').val(invoice.id);
        $('#total_tagihan').val(invoice.total_tagihan);
        $('#sisa_tagihan').val(invoice.sisa_bayar);
        $('#nominal').val(invoice.sisa_bayar);
        $('#modalBayar').modal('show');
        generateInvoice();
    }

    function generateInvoice() {
        var invoicePrefix = "BYR";
        var currentDate = new Date();
        var year = currentDate.getFullYear();
        var month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
        var day = currentDate.getDate().toString().padStart(2, '0');
        var formattedNextInvoiceNumber = nextInvoiceNumber.toString().padStart(3, '0');

        var generatedInvoice = invoicePrefix + year + month + day + formattedNextInvoiceNumber;
        $('#no_invoice_bayar').val(generatedInvoice);
    }
</script>

<!-- mematikan js atau klik kanan js -->
<!-- <script>
    document.addEventListener("contextmenu", function(e){
        e.preventDefault();
    }, false);
</script> -->
@endsection