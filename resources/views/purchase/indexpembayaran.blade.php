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
            <table class="table" id="keluar">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Id</th>
                    <th>No PO/Mutasi Inden</th>
                    <th>No Invoice Pembayaran</th>
                    <th>Nominal</th>
                    <th>Tanggal Bayar</th>
                    <th>Metode</th>
                    <th>Rekening</th>
                    <th class="text-center">Status</th>
                </tr>
                </thead>
                <tbody>
                    {{-- @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if ($item->po)
                                    {{ $item->po->pembelian ? $item->po->pembelian->no_po : ($item->po->poinden ? $item->po->poinden->no_po : '') }}
                                @elseif($item->mutasiinden)
                                    {{ $item->mutasiinden->no_mutasi ?? '' }}
                                @endif
                            </td>
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
            <table class="table" id="masuk">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Id</th>
                    <th>No Retur/No Retur Inden</th>
                    <th>No Invoice Pembayaran</th>
                    <th>Nominal</th>
                    <th>Tanggal Bayar</th>
                    <th>Metode</th>
                    <th>Rekening</th>
                    <th class="text-center">Status</th>
                </tr>
                </thead>
                <tbody>
                    {{-- @foreach ($data2 as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if ($item->retur)
                                    {{ $item->retur->no_retur }}
                                @elseif($item->returinden)
                                    {{ $item->returinden->no_retur}}
                                @endif
                            </td>
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
            $('#rekening_id, #bayar, #filterMetodemasuk, #filterMetodekeluar, #filterJenis').select2();

            // Start Datatable keluar
                const columns = [
                    { data: 'no', name: 'no', orderable: false },
                    { data: 'id', name: 'id', visible: false },
                    { data: 'no_referensi', name: 'no_referensi', orderable: false },
                    { data: 'no_invoice_bayar', name: 'no_invoice_bayar' },
                    { 
                        data: 'nominal', 
                        name: 'nominal', 
                        render: function(data, type, row) {
                            return row.nominal_format;
                        } 
                    },
                    { 
                        data: 'tanggal_bayar', 
                        name: 'tanggal_bayar', 
                        render: function(data, type, row) {
                            return row.tanggal_bayar_format;
                        } 
                    },
                    { data: 'cara_bayar', name: 'cara_bayar' },
                    { data: 'nomor_rekening', name: 'nomor_rekening', orderable: false  },
                    { 
                        data: 'status_bayar', 
                        name: 'status_bayar',
                        render: function(data, type, row) {
                            if (row.status_bayar == 'LUNAS'){
                                return `<span class="badges bg-lightgreen">${row.status_bayar}</span>`;
                            }
                            else {
                                return `<span class="badges bg-lightgrey">${row.status_bayar}</span>`;
                            }
                        }
                    },
                ];

                let table = initDataTable('#keluar', {
                    ajaxUrl: "{{ route('pembayaranbeli.index') }}",
                    columns: columns,
                    order: [[1, 'asc']],
                    searching: true,
                    lengthChange: true,
                    pageLength: 5
                }, {
                    metode_keluar: '#filterMetodekeluar',
                    dateStart: '#filterDateStart',
                    dateEnd: '#filterDateEnd'
                }, 'keluar');

                const handleSearch = debounce(function() {
                    table.ajax.reload();
                }, 5000); // Adjust the debounce delay as needed

                $('#filterMetodekeluar, #filterDateStart, #filterDateEnd').on('input', handleSearch);

                $('#filterBtn').on('click', function() {
                    table.ajax.reload();
                });

                $('#clearBtn').on('click', function() {
                    $('#filterMetodekeluar').val('').trigger('change');
                    $('#filterDateStart').val('');
                    $('#filterDateEnd').val('');
                    table.ajax.reload();
                });
            // End Datatble keluar

            // Start Datatable masuk
                const columns2 = [
                    { data: 'no', name: 'no', orderable: false },
                    { data: 'id', name: 'id', visible: false },
                    { data: 'no_referensi', name: 'no_referensi', orderable: false },
                    { data: 'no_invoice_bayar', name: 'no_invoice_bayar' },
                    { 
                        data: 'nominal', 
                        name: 'nominal', 
                        render: function(data, type, row) {
                            return row.nominal_format;
                        } 
                    },
                    { 
                        data: 'tanggal_bayar', 
                        name: 'tanggal_bayar', 
                        render: function(data, type, row) {
                            return row.tanggal_bayar_format;
                        } 
                    },
                    { data: 'cara_bayar', name: 'cara_bayar' },
                    { data: 'nomor_rekening', name: 'nomor_rekening', orderable: false  },
                    { 
                        data: 'status_bayar', 
                        name: 'status_bayar',
                        render: function(data, type, row) {
                            if (row.status_bayar == 'LUNAS'){
                                return `<span class="badges bg-lightgreen">${row.status_bayar}</span>`;
                            }
                            else {
                                return `<span class="badges bg-lightgrey">${row.status_bayar}</span>`;
                            }
                        }
                    },
                ];

                let table2 = initDataTable('#masuk', {
                    ajaxUrl: "{{ route('pembayaranbeli.index') }}",
                    columns: columns2,
                    order: [[1, 'asc']],
                    searching: true,
                    lengthChange: true,
                    pageLength: 5
                }, {
                    metode_masuk: '#filterMetodemasuk',
                    dateStart2: '#filterDateStart2',
                    dateEnd2: '#filterDateEnd2'
                }, 'masuk');

                const handleSearch2 = debounce(function() {
                    table2.ajax.reload();
                }, 5000); // Adjust the debounce delay as needed

                $('#filterMetodemasuk, #filterDateStart2, #filterDateEnd2').on('input', handleSearch2);

                $('#filterBtn2').on('click', function() {
                    table2.ajax.reload();
                });

                $('#clearBtn2').on('click', function() {
                    $('#filterMetodemasuk').val('').trigger('change');
                    $('#filterDateStart2').val('');
                    $('#filterDateEnd2').val('');
                    table2.ajax.reload();
                });
            // End Datatble masuk
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