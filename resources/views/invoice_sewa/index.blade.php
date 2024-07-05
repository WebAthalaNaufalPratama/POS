@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Invoice Sewa</h4>
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
                    <input type="date" class="form-control" name="filterDateStart" id="filterDateStart" value="{{ request()->input('dateStart') }}" title="Tanggal Awal">
                </div>
                <div class="col-sm-2 ps-0 pe-0">
                    <input type="date" class="form-control" name="filterDateEnd" id="filterDateEnd" value="{{ request()->input('dateEnd') }}" title="Tanggal Akhir">
                </div>
                <div class="col-sm-2">
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('invoice_sewa.index') }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('invoice_sewa.index') }}" class="btn btn-warning">Clear</a>
                </div>
            </div>
            <div class="table-responsive">
            <table class="table datanew">
                <thead>
                <tr>
                    <th>No</th>
                    <th>No Kontrak</th>
                    <th>No Invoice</th>
                    <th>Customer</th>
                    <th>Jatuh Tempo</th>
                    <th>Tagihan</th>
                    <th>DP</th>
                    <th>Sisa Bayar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->no_sewa }}</td>
                            <td>{{ $item->no_invoice }}</td>
                            <td>{{ $item->kontrak->customer->nama }}</td>
                            <td>{{ formatTanggal($item->jatuh_tempo) }}</td>
                            <td>{{ formatRupiah($item->total_tagihan) }}</td>
                            <td>{{ formatRupiah($item->dp) }}</td>
                            <td>{{ formatRupiah($item->sisa_bayar) }}</td>
                            <td>{{ $item->status }}</td>
                            <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('invoice_sewa.cetak', ['invoice_sewa' => $item->id]) }}" class="dropdown-item" target="blank"><img src="assets/img/icons/download.svg" class="me-2" alt="img">Cetak</a>
                                    </li>
                                    @if($item->status == 'DIKONFIRMASI' || $item->status == 'BATAL')
                                    <li>
                                        <a href="{{ route('invoice_sewa.show', ['invoice_sewa' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                    </li>
                                    @else
                                    <li>
                                        <a href="{{ route('invoice_sewa.show', ['invoice_sewa' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Konfirmasi</a>
                                    </li>
                                    @endif
                                    @if( ($item->status == 'DIKONFIRMASI' && (Auth::user()->hasRole('Finance') || Auth::user()->hasRole('Auditor'))) || ($item->status == 'TUNDA' && (Auth::user()->hasRole('AdminGallery') || Auth::user()->hasRole('Finance') || Auth::user()->hasRole('Auditor'))) )
                                    <li>
                                        <a href="{{ route('invoice_sewa.edit', ['invoice_sewa' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item" onclick="deleteData({{ $item->id }})"><img src="assets/img/icons/closes.svg" class="me-2" alt="img">Batal</a>
                                    </li>
                                    @endif
                                    @if ($item->sisa_bayar != 0 && $item->status == 'DIKONFIRMASI')
                                    <li>
                                        <a href="javascript:void(0);" onclick="bayar({{ $item }})" class="dropdown-item"><img src="assets/img/icons/cash.svg" class="me-2" alt="img">Bayar</a>
                                    </li>
                                    @endif
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
            <form action="{{ route('pembayaran_sewa.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="no_invoice">Nomor Kontrak</label>
                            <input type="text" class="form-control" id="no_kontrak" name="no_kontrak" placeholder="Nomor Kontrak" required readonly>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="no_invoice">Nomor Invoice</label>
                            <input type="text" class="form-control" id="no_invoice_bayar" name="no_invoice_bayar" placeholder="Nomor Invoice" value="" required readonly>
                            <input type="hidden" id="invoice_sewa_id" name="invoice_sewa_id" value="">
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
                            <input type="file" class="form-control" id="bukti" name="bukti" required>
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
        $(document).ready(function(){
            $('#filterCustomer').select2();
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
            $('#no_kontrak').val(invoice.no_sewa);
            $('#invoice_sewa_id').val(invoice.id);
            $('#total_tagihan').val(invoice.total_tagihan);
            $('#sisa_tagihan').val(invoice.sisa_bayar);
            $('#nominal').val(invoice.sisa_bayar);
            $('#rekening_id').select2({
                dropdownParent: $("#modalBayar")
            });
            $('#bayar').select2({
                dropdownParent: $("#modalBayar")
            });
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
                    url: "/invoice_sewa/"+id+"/delete",
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