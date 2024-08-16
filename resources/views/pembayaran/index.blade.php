@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Form Perangkai Penjualan</h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row ps-2 pe-2">
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterMetode" name="metode" class="form-control" title="metode">
                            <option value="">Pilih Metode</option>
                            <option value="cash" {{ 'cash' == request()->input('metode') ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ 'transfer' == request()->input('metode') ? 'selected' : '' }}>Transfer</option>
                        </select>
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <input type="date" class="form-control" name="filterDateStart" id="filterDateStart" value="{{ request()->input('dateStart') }}" title="Tanggal Awal">
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <input type="date" class="form-control" name="filterDateEnd" id="filterDateEnd" value="{{ request()->input('dateEnd') }}" title="Tanggal Akhir">
                    </div>
                    <div class="col-sm-2">
                        <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('pembayaran.index') }}" class="btn btn-info">Filter</a>
                        <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('pembayaran.index') }}" class="btn btn-warning">Clear</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="pembayaran-table" class="table pb-5">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Invoice Bayar</th>
                                <th> Cara Bayar</th>
                                <th>Nominal</th>
                                <th>Rekening</th>
                                <th>Tanggal_Bayar</th>
                                <th>Status Bayar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($data as $pembayaran)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $pembayaran->no_invoice_bayar }}</td>
                                <td>{{ $pembayaran->cara_bayar }}</td>
                                <td>{{ $pembayaran->nominal }}</td>
                                <td>@if($pembayaran->rekening == null)
                                    Pembayaran Cash
                                    @else
                                    {{ $pembayaran->rekening->bank }}
                                    @endif
                                </td>
                                <td>{{ date('d F Y', strtotime($pembayaran->tanggal_bayar)) }}</td>
                                <td>{{ $pembayaran->status_bayar }}</td>
                                <td>
                                    <div class="dropdown">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('pembayaran.edit', ['pembayaran' => $pembayaran->id]) }}"><img src="assets/img/icons/edit-6.svg" class="me-2" alt="img">Edit</a>
                                        </div>
                                    </div>
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
    function deleteData(id) {
        $.ajax({
            type: "GET",
            url: "/do_sewa/" + id + "/delete",
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
        $('#rekening_id, #bayar, #filterMetode').select2();

        $('#pembayaran-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('pembayaran.index') }}',
                type: 'GET',
                data: function(d) {
                    d.metode = $('select[name="metode"]').val(); // Assuming you have a select for filtering by metode
                    d.dateStart = $('input[name="dateStart"]').val(); // Assuming you have inputs for dateStart
                    d.dateEnd = $('input[name="dateEnd"]').val(); // Assuming you have inputs for dateEnd
                }
            },
            columns: [
                { data: 'id', name: 'id', orderable: false, searchable: false },
                { data: 'no_invoice_bayar', name: 'no_invoice_bayar' },
                { data: 'cara_bayar', name: 'cara_bayar' },
                { data: 'nominal', name: 'nominal' },
                { data: 'rekening', name: 'rekening' },
                { data: 'tanggal_bayar', name: 'tanggal_bayar' },
                { data: 'status_bayar', name: 'status_bayar' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    targets: 3,
                    render: function(data, type, row) {
                        return Number(data).toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
                    }
                }
            ]
        });
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
@endsection