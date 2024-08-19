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
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('pembayaran_sewa.index') }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('pembayaran_sewa.index') }}" class="btn btn-warning">Clear</a>
                </div>
            </div>
            <div class="table-responsive">
            <table class="table" id="dataTable">
                <thead>
                <tr>
                    <th>No</th>
                    <th>No Invoice Pembayaran</th>
                    <th>No Invoice Tagihan</th>
                    <th>No Kontrak</th>
                    <th>Nominal</th>
                    <th>Tanggal Bayar</th>
                    <th class="text-center">Metode</th>
                    <th>Rekening</th>
                    <th class="text-center">Aksi</th>
                </tr>
                </thead>
                <tbody>
                    {{-- @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->sewa->no_sewa }}</td>
                            <td>{{ $item->sewa->no_invoice }}</td>
                            <td>{{ $item->no_invoice_bayar }}</td>
                            <td>{{ formatRupiah($item->nominal) }}</td>
                            <td>{{ formatTanggal($item->tanggal_bayar) }}</td>
                            <td>{{ $item->cara_bayar }}</td>
                            <td>{{ $item->cara_bayar == 'transfer' ? $item->rekening->nama_akun : '-' }}</td>
                            <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('invoice_sewa.show', ['invoice_sewa' => $item->sewa->id]) }}" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Invoice</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" onclick="editbayar({{ $item->id }})" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Form Pembayaran</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST" id="editBayarForm" enctype="multipart/form-data">
                @csrf
                @method('patch')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="no_invoice">Nomor Kontrak</label>
                                    <input type="text" class="form-control" id="no_kontrak" name="no_kontrak" placeholder="Nomor Kontrak" required readonly>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label for="no_invoice">Nomor Invoice</label>
                                    <input type="text" class="form-control" id="no_invoice_bayar" name="no_invoice_bayar" placeholder="Nomor Invoice" value="" required readonly>
                                    <input type="hidden" id="invoice_sewa_id" name="invoice_sewa_id" value="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="no_invoice">Total Tagihan</label>
                                    <input type="text" class="form-control" id="total_tagihan" name="total_tagihan" placeholder="Total Taqgihan" required readonly>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label for="no_invoice">Sisa Tagihan</label>
                                    <input type="text" class="form-control" id="sisa_tagihan" name="sisa_tagihan" placeholder="Sisa Taqgihan" required readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="bayar">Cara Bayar</label>
                                    <select class="form-control" id="bayar" name="cara_bayar" required>
                                        <option value="">Pilih Cara Bayar</option>
                                        <option value="cash">Cash</option>
                                        <option value="transfer">Transfer</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-12" id="rekening" style="display: none">
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
                                <div class="form-group col-sm-12">
                                    <label for="nominal">Nominal</label>
                                    <input type="text" class="form-control" id="nominal" name="nominal" value="" placeholder="Nominal Bayar" required>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label for="tanggalbayar">Tanggal</label>
                                    <input type="date" class="form-control" id="tanggal_bayar" name="tanggal_bayar" value="" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="buktibayar">Unggah Bukti</label>
                                    <input type="file" class="form-control" id="bukti" name="bukti" onchange="previewImage(this, 'preview')" accept="image/*">
                                </div>
                                <img id="preview" src="" alt="your image" style="max-width: 100%"/>
                            </div>
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
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function(){
            $('#rekening_id, #bayar, #filterMetode').select2();

            // Start Datatable
            const columns = [
                { data: 'no', name: 'no', orderable: false },
                { data: 'no_invoice_bayar', name: 'no_invoice_bayar' },
                { data: 'no_invoice_tagihan', name: 'no_invoice_tagihan', orderable: false },
                { data: 'no_kontrak', name: 'no_kontrak', orderable: false },
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
                { 
                    data: 'cara_bayar',
                    name: 'cara_bayar',
                    render: function(data, type, row) {
                        let badgeClass;
                        switch (data) {
                            case 'Cash':
                                badgeClass = 'bg-lightgreen';
                                break;
                            case 'Transfer':
                                badgeClass = 'bg-lightblue';
                                break;
                            default:
                                badgeClass = 'bg-lightgrey';
                                break;
                        }
                        
                        return `<div class="text-center">
                            <span class="badges ${badgeClass}">
                                ${data ?? '-'}
                            </span></div>
                        `;
                    }
                },
                { data: 'nama_rekening', name: 'nama_rekening', orderable: false },
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

                        actionsHtml += `
                            <li>
                                <a href="invoice_sewa/${row.sewa.id}/show" class="dropdown-item">
                                    <img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Invoice
                                </a>
                            </li>
                        `;

                        actionsHtml += `
                            <li>
                                <a href="javascript:void(0);" onclick="editbayar(${row.id})" class="dropdown-item">
                                    <img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit
                                </a>
                            </li>
                        `;

                        actionsHtml += `</ul></div>`;

                        return actionsHtml;
                    }
                }
            ];

            let table = initDataTable('#dataTable', {
                ajaxUrl: "{{ route('pembayaran_sewa.index') }}",
                columns: columns,
                order: [[1, 'asc']],
                searching: true,
                lengthChange: true,
                pageLength: 10
            }, {
                metode: '#filterMetode',
                dateStart: '#filterDateStart',
                dateEnd: '#filterDateEnd'
            });

            $('#filterBtn').on('click', function() {
                table.ajax.reload();
            });

            $('#clearBtn').on('click', function() {
                $('#filterMetode').val('').trigger('change');
                $('#filterDateStart').val('');
                $('#filterDateEnd').val('');
                table.ajax.reload();
            });
            // End Datatable
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
        $(document).on('input', '#nominal', function() {
            let input = $(this);
            let value = input.val();
            
            if (!isNumeric(cleanNumber(value))) {
            value = value.replace(/[^\d]/g, "");
            }

            value = cleanNumber(value);
            let formattedValue = formatNumber(value);
            
            input.val(formattedValue);
        });
        $('#editBayarForm').on('submit', function(e) {
            let inputs = $('#editBayarForm').find('#nominal');
            inputs.each(function() {
                let input = $(this);
                let value = input.val();
                let cleanedValue = cleanNumber(value);

                input.val(cleanedValue);
            });

            return true;
        });
        $('#bukti').on('change', function() {
            const file = $(this)[0].files[0];
            if (file.size > 2 * 1024 * 1024) { 
                toastr.warning('Ukuran file tidak boleh lebih dari 2mb', {
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: false,
                    progressBar: true
                });
                $(this).val(''); 
                return;
            }
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
        
        function editbayar(id){
            $.ajax({
                    type: "GET",
                    url: "/pembayaran_sewa/"+id+"/show",
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        $('#editBayarForm').attr('action', `{{ route("pembayaran_sewa.update", ":id") }}`.replace(':id', id));
                        $('#no_kontrak').val(response.sewa.no_sewa);
                        $('#no_invoice_bayar').val(response.no_invoice_bayar);
                        $('#invoice_sewa_id').val(response.sewa.id);
                        $('#total_tagihan').val(formatNumber(response.sewa.total_tagihan));
                        $('#sisa_tagihan').val(formatNumber(parseInt(response.sewa.sisa_bayar) + parseInt(response.nominal)));
                        $('#nominal').val(formatNumber(response.nominal));
                        $('#tanggal_bayar').val(response.tanggal_bayar);
                        $('#rekening_id').val(response.rekening_id).change();
                        $('#bayar').val(response.cara_bayar).change();
                        if(response.bukti){
                            $('#preview').attr('src', '/storage/'+response.bukti);
                        } else {
                            $('#preview').attr('src', defaultImg);
                        }
                        $('#rekening_id').select2({
                            dropdownParent: $("#modalBayar")
                        });
                        $('#bayar').select2({
                            dropdownParent: $("#modalBayar")
                        });
                        $('#modalBayar').modal('show');
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