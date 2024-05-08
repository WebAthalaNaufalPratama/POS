@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Pembayaran Sewa</h4>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table datanew">
                <thead>
                <tr>
                    <th>No</th>
                    <th>No Kontrak</th>
                    <th>No Invoice Tagihan</th>
                    <th>No Invoice Pembayaran</th>
                    <th>Nominal</th>
                    <th>Tanggal Bayar</th>
                    <th>Metode</th>
                    <th class="text-center">Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->sewa->no_sewa }}</td>
                            <td>{{ $item->sewa->no_invoice }}</td>
                            <td>{{ $item->no_invoice_bayar }}</td>
                            <td>{{ $item->nominal }}</td>
                            <td>{{ $item->tanggal_bayar }}</td>
                            <td>{{ $item->cara_bayar }}</td>
                            <td class="text-center">
                                @if ($item->status_bayar == 'LUNAS')
                                    <span class="badge bg-success">{{ $item->status_bayar }}</span>
                                @elseif ($item->status_bayar == 'BELUM LUNAS')
                                    <span class="badge bg-secondary">{{ $item->status_bayar }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('invoice_sewa.show', ['invoice_sewa' => $item->sewa->id]) }}" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                    </li>
                                    {{-- @if ($item->sisa_bayar != 0)
                                    <li>
                                        <a href="javascript:void(0);" onclick="bayar({{ $item }})" class="dropdown-item"><img src="assets/img/icons/cash.svg" class="me-2" alt="img">Bayar</a>
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

{{-- <div class="modal fade" id="modalBayar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div> --}}
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            $('#rekening_id, #bayar').select2();
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
@endsection