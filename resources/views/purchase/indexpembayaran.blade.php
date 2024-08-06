@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Pembayaran Keluar (Pembelian)</h4>
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
                    <select id="filterMetodekeluar" name="metode_keluar" class="form-control" title="metode">
                        <option value="">Pilih Metode</option>
                        <option value="cash" {{ 'cash' == request()->input('metode_keluar') ? 'selected' : '' }}>Cash</option>
                        <option value="transfer" {{ 'transfer' == request()->input('metode_keluar') ? 'selected' : '' }}>Transfer</option>
                    </select>
                </div>
                {{-- <div class="col-sm-2 ps-0 pe-0">
                    <select id="filterJenis" name="jenis" class="form-control" title="jenis">
                        <option value="">Pilih Jenis</option>
                        <option value="Tradisional" {{ 'Tradisional' == request()->input('jenis') ? 'selected' : '' }}>Tradisional</option>
                        <option value="Inden" {{ 'Inden' == request()->input('jenis') ? 'selected' : '' }}>Inden</option>
                    </select>
                </div> --}}
                <div class="col-sm-2">
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('pembayaranbeli.index') }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('pembayaranbeli.index') }}" class="btn btn-warning">Clear</a>
                </div>
            </div>
            <div class="table-responsive">
            <table class="table datanew">
                <thead>
                <tr>
                    <th>No</th>
                    <th>No PO</th>
                    <th>No Invoice Purchase</th>
                    <th>No Invoice Pembayaran</th>
                    <th>Nominal</th>
                    <th>Tanggal Bayar</th>
                    <th>Metode</th>
                    <th>Rekening</th>
                    <th class="text-center">Status</th>
                    {{-- <th>Aksi</th> --}}
                </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if ($item->po)
                                    {{ $item->po->pembelian ? $item->po->pembelian->no_po : ($item->po->poinden ? $item->po->poinden->no_po : '') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $item->po ? $item->po->no_inv : '-' }}</td>
                            <td>{{ $item->no_invoice_bayar }}</td>
                            <td>{{ formatRupiah($item->nominal) }}</td>
                            <td>{{ tanggalindo($item->tanggal_bayar) }}</td>
                            <td>{{ $item->cara_bayar }}</td>
                            <td>{{ $item->cara_bayar == 'transfer' ? $item->rekening->nama_akun.' ('.$item->rekening->nomor_rekening.')' : '-' }}</td>
                            <td class="text-center">
                                @if ($item->status_bayar == 'LUNAS')
                                    <span class="badges bg-lightgreen">{{ $item->status_bayar }}</span>
                                @else
                                    <span class="badges bg-lightgrey">{{ $item->status_bayar }}</span>
                                @endif
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
                    <h4>Pembayaran Masuk (Refund)</h4>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row ps-2 pe-2">
                <div class="col-sm-2 ps-0 pe-0">
                    <input type="date" class="form-control" name="filterDateStart2" id="filterDateStart2" value="{{ request()->input('dateStart2') }}" title="Tanggal Awal">
                </div>
                <div class="col-sm-2 ps-0 pe-0">
                    <input type="date" class="form-control" name="filterDateEnd2" id="filterDateEnd2" value="{{ request()->input('dateEnd2') }}" title="Tanggal Akhir">
                </div>
                <div class="col-sm-2 ps-0 pe-0">
                    <select id="filterMetodemasuk" name="metode_masuk" class="form-control" title="metode">
                        <option value="">Pilih Metode</option>
                        <option value="cash" {{ 'cash' == request()->input('metode_masuk') ? 'selected' : '' }}>Cash</option>
                        <option value="transfer" {{ 'transfer' == request()->input('metode_masuk') ? 'selected' : '' }}>Transfer</option>
                    </select>
                </div>
                {{-- <div class="col-sm-2 ps-0 pe-0">
                    <select id="filterJenis" name="jenis" class="form-control" title="jenis">
                        <option value="">Pilih Jenis</option>
                        <option value="Tradisional" {{ 'Tradisional' == request()->input('jenis') ? 'selected' : '' }}>Tradisional</option>
                        <option value="Inden" {{ 'Inden' == request()->input('jenis') ? 'selected' : '' }}>Inden</option>
                    </select>
                </div> --}}
                <div class="col-sm-2">
                    <a href="javascript:void(0);" id="filterBtn2" data-base-url="{{ route('pembayaranbeli.index') }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn2" data-base-url="{{ route('pembayaranbeli.index') }}" class="btn btn-warning">Clear</a>
                </div>
            </div>
            <div class="table-responsive">
            <table class="table datanew">
                <thead>
                <tr>
                    <th>No</th>
                    <th>No PO</th>
                    <th>No Invoice Purchase</th>
                    <th>No Invoice Pembayaran</th>
                    <th>Nominal</th>
                    <th>Tanggal Bayar</th>
                    <th>Metode</th>
                    <th>Rekening</th>
                    <th class="text-center">Status</th>
                    {{-- <th>Aksi</th> --}}
                </tr>
                </thead>
                <tbody>
                    @foreach ($data2 as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if ($item->retur && $item->retur->invoice)
                                    {{ $item->retur->invoice->pembelian ? $item->retur->invoice->pembelian->no_po : ($item->retur->invoice->poinden ? $item->retur->invoice->poinden->no_po : '') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $item->retur && $item->retur->invoice ? $item->retur->invoice->no_inv : '-' }}</td>
                            <td>{{ $item->no_invoice_bayar }}</td>
                            <td>{{ formatRupiah($item->nominal) }}</td>
                            <td>{{ tanggalindo($item->tanggal_bayar) }}</td>
                            <td>{{ $item->cara_bayar }}</td>
                            <td>{{ $item->cara_bayar == 'transfer' ? $item->rekening->nama_akun.' ('.$item->rekening->nomor_rekening.')' : '-' }}</td>
                            <td class="text-center">
                                @if ($item->status_bayar == 'LUNAS')
                                    <span class="badges bg-lightgreen">{{ $item->status_bayar }}</span>
                                @else
                                    <span class="badges bg-lightgrey">{{ $item->status_bayar }}</span>
                                @endif
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
        $(document).ready(function(){
            $('#rekening_id, #bayar, #filterMetodemasuk, #filterMetodekeluar, #filterJenis').select2();
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
        $('#filterBtn, #filterBtn2').click(function(){
            var baseUrl = $(this).data('base-url');
            var urlString = baseUrl;
            var first = true;
            var symbol = '';

            var metode = $('#filterMetodekeluar').val();
            if (metode) {
                var filterMetode = 'metode_keluar=' + metode;
                if (first == true) {
                    symbol = '?';
                    first = false;
                } else {
                    symbol = '&';
                }
                urlString += symbol;
                urlString += filterMetode;
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

            var metode2 = $('#filterMetodemasuk').val();
            if (metode2) {
                var filterMetode2 = 'metode_masuk=' + metode2;
                if (first == true) {
                    symbol = '?';
                    first = false;
                } else {
                    symbol = '&';
                }
                urlString += symbol;
                urlString += filterMetode2;
            }

            var dateStart2 = $('#filterDateStart2').val();
            if (dateStart2) {
                var filterDateStart2 = 'dateStart2=' + dateStart2;
                if (first == true) {
                    symbol = '?';
                    first = false;
                } else {
                    symbol = '&';
                }
                urlString += symbol;
                urlString += filterDateStart2;
            }

            var dateEnd2 = $('#filterDateEnd2').val();
            if (dateEnd2) {
                var filterDateEnd2 = 'dateEnd2=' + dateEnd2;
                if (first == true) {
                    symbol = '?';
                    first = false;
                } else {
                    symbol = '&';
                }
                urlString += symbol;
                urlString += filterDateEnd2;
            }
            window.location.href = urlString;
        });
        $('#clearBtn, #clearBtn2').click(function(){
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
@endsection