@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Laporan Delivery Order Penjualan</h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Delivery Order</th>
                                <th>Lokasi Galeri</th>
                                <th>Nama Pengirim</th>
                                <th>Nama Penerima</th>
                                <th>Tanggal Pengiriman</th>
                                <th>Tanggal Invoice</th>
                                <th>Nama Produk Jual</th>
                                <th>Jumlah Produk Jual</th>
                                <th>Nama Produk</th>
                                <th>Jumlah</th>
                                <th>Kondisi (Baik)</th>
                                <th>Kondisi (Afkir)</th>
                                <th>Kondisi (Bonggol)</th>
                                <th>Unit Satuan</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($combinedData as $no_do => $data)       
                            <tr>
                                <td >{{ $no++ }}</td>
                                <td >{{ $data['no_do'] }}</td>
                                <td>{{ $data['lokasi_pengirim'] }}</td>
                                <td>{{ $data['customer'] }}</td>
                                <td>{{ $data['penerima'] }}</td>
                                <td>{{ $data['tanggal_kirim'] }}</td>
                                <td>{{ $data['tanggal_invoice'] }}</td>
                                <td>@foreach ($data['produk_jual'] as $index => $produkJual)
                                        {{ $produkJual['nama_produkjual'] }}
                                        @if ($index < count($data['produk_jual']) - 1)
                                            <br>
                                        @endif
                                    @endforeach
                                </td>
                                <td>@foreach ($data['produk_jual'] as $index => $produkJual)
                                        {{ $produkJual['jumlahprodukjual'] }}
                                        @if ($index < count($data['produk_jual']) - 1)
                                            <br>
                                        @endif
                                    @endforeach
                                </td>
                                <td>@foreach ($data['produk_jual'] as $index => $produkJual)
                                    @foreach ($produkJual['komponen'] as $index => $komponen)
                                        {{ $komponen['nama_produk'] }}
                                        @if ($index < count($komponen) - 1)
                                            <br>
                                        @endif
                                    @endforeach
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($data['produk_jual'] as $index => $produkJual)
                                        @foreach ($produkJual['komponen'] as $komponen)
                                            {{ $komponen['jumlah'] }}
                                    @if ($index < count($komponen) - 1)
                                        <br>
                                    @endif
                                    @endforeach
                                    @endforeach
                                </td>
                                <td> 
                                    @foreach ($data['produk_jual'] as $index => $produkJual)
                                        @foreach ($produkJual['komponen'] as $komponen)
                                    {{ $komponen['kondisibaik'] }}
                                    @if ($index < count($komponen) - 1)
                                                <br>
                                            @endif
                                    @endforeach
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($data['produk_jual'] as $index => $produkJual)
                                        @foreach ($produkJual['komponen'] as $komponen)
                                    {{ $komponen['kondisiafkir'] }}
                                    @if ($index < count($komponen) - 1)
                                                <br>
                                            @endif
                                    @endforeach
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($data['produk_jual'] as $index => $produkJual)
                                        @foreach ($produkJual['komponen'] as $komponen)
                                    {{ $komponen['kondisibonggol'] }}
                                    @if ($index < count($komponen) - 1)
                                                <br>
                                            @endif
                                    @endforeach
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($data['produk_jual'] as $index => $produkJual)
                                        @foreach ($produkJual['komponen'] as $komponen)
                                    {{ $produkJual['unitsatuan'] }}
                                    @if ($index < count($komponen) - 1)
                                                <br>
                                            @endif
                                    @endforeach
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($data['produk_jual'] as $index => $produkJual)
                                    @foreach ($produkJual['komponen'] as $komponen)
                                    {{ $produkJual['keterangan'] }}
                                    @if ($index < count($komponen) - 1)
                                                <br>
                                            @endif
                                    @endforeach
                                    @endforeach
                                </td>
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