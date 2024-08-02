@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Laporan Mutasi</h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Mutasi</th>
                                <th>Pengirim</th>
                                <th>Penerima</th>
                                <th>Tanggal Pengiriman</th>
                                <th>Tanggal Diterima</th>
                                <th>Nama Produk</th>
                                <th>Jumlah Pengiriman</th>
                                <th>Kondisi Pengiriman</th>
                                <th>Jumlah Diterima</th>
                                <th>Kondisi Diterima</th>
                                <th>Biaya Pengiriman</th>
                                <th>Rekening Bank</th>
                                <th>Total Biaya</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($combinedData as $no_do => $data)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $data['no_mutasi'] }}</td>
                                <td>{{ $data['lokasi_pengirim'] }}</td>
                                <td>{{ $data['lokasi_penerima'] }}</td>
                                <td>{{ $data['tanggal_pengiriman'] }}</td>
                                <td>{{ $data['tanggal_diterima'] }}</td>
                                <td>
                                    @if(substr($data['no_mutasi'], 0, 3) != 'MGO')
                                        @foreach ($data['produk_jual'] as $index => $produkJual)
                                            @foreach ($produkJual['komponen'] as $komponenIndex => $komponen)
                                                {{ $komponen['nama_produk'] }}
                                                @if ($komponenIndex < count($produkJual['komponen']) - 1)
                                                    <br>
                                                @endif
                                            @endforeach
                                            @if ($index < count($data['produk_jual']) - 1)
                                                <br>
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach ($data['produk_jual'] as $index => $produkJual)
                                            {{ $produkJual['nama_produkjual'] }}
                                            @if ($index < count($data['produk_jual']) - 1)
                                                <br>
                                            @endif
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    @foreach ($data['produk_jual'] as $index => $produkJual)
                                        {{ $produkJual['jumlahprodukjual'] }}
                                        @if ($index < count($data['produk_jual']) - 1)
                                            <br>
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    @if(substr($data['no_mutasi'], 0, 3) != 'MGO')
                                        @foreach ($data['produk_jual'] as $index => $produkJual)
                                            @foreach ($produkJual['komponen'] as $komponenIndex => $komponen)
                                                {{ $komponen['kondisi'] }}
                                                @if ($komponenIndex < count($produkJual['komponen']) - 1)
                                                    <br>
                                                @endif
                                            @endforeach
                                            @if ($index < count($data['produk_jual']) - 1)
                                                <br>
                                            @endif
                                        @endforeach
                                    @else
                                        Tidak Ada Kondisi
                                    @endif
                                </td>
                                <td>
                                    @foreach ($data['produk_jual'] as $index => $produkJual)
                                        {{ $produkJual['jumlah_diterima'] }}
                                        @if ($index < count($data['produk_jual']) - 1)
                                            <br>
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    @if(!empty($produkJual['kondisi_diterima']))
                                        @foreach ($data['produk_jual'] as $index => $produkJual)
                                            {{ $produkJual['kondisi_diterima'] }}
                                            @if ($index < count($data['produk_jual']) - 1)
                                                <br>
                                            @endif
                                        @endforeach
                                    @else
                                        null
                                    @endif
                                </td>
                                <td>{{ $data['biaya_pengiriman'] }}</td>
                                <td>{{ $data['rekening'] }}</td>
                                <td>{{ $data['total_biaya'] }}</td>
                                <td>
                                    <div class="dropdown">
                                        <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                        </a>
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