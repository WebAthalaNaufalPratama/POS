@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Laporan Omset</h4>
                    </div>
                    <div class="page-btn">
                        <button class="btn btn-outline-danger" style="height: 2.5rem; padding: 0.5rem 1rem; font-size: 1rem;" onclick="pdf()">
                            <img src="/assets/img/icons/pdf.svg" alt="PDF" style="height: 1rem;"/> PDF
                        </button>
                        <button class="btn btn-outline-success" style="height: 2.5rem; padding: 0.5rem 1rem; font-size: 1rem;" onclick="excel()">
                            <img src="/assets/img/icons/excel.svg" alt="EXCEL" style="height: 1rem;"/> EXCEL
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <row class="col-lg-12 col-sm-12">
                        <div class="row">
                            <div class="col-lg col-sm-6 col-12">
                                <input type="text" class="form-control" name="filterTanggalInvoice" id="filterTanggalInvoice" value="{{ request()->input('tanggal_invoice') }}" title="Tanggal Invoice" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Tanggal Invoice">
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <input type="text" class="form-control" name="filterTanggalPembayaran" id="filterTanggalPembayaran" value="{{ request()->input('tanggal_pembayaran') }}" title="Tanggal Pembayaran" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Tanggal Pembayaran">
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <select id="filterStatus" name="filterStatus" class="form-control" title="Status">
                                    <option value="">Pilih Status Pembayaran</option>
                                    <option value="Sudah Dibayar" {{ 'Sudah Dibayar' == request()->input('status') ? 'selected' : '' }}>Sudah Dibayar</option>
                                    <option value="Belum Dibayar" {{ 'Belum Dibayar' == request()->input('status') ? 'selected' : '' }}>Belum Dibayar</option>
                                </select>
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <select id="filterSales" name="filterSales" class="form-control" title="Sales">
                                    <option value="">Pilih Sales</option>
                                    @foreach ($sales as $key => $value)
                                        <option value="{{ $key }}" {{ $key == request()->input('sales') ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <select id="filterProduk" name="filterProduk" class="form-control" title="Produk">
                                    <option value="">Pilih Produk</option>
                                    @foreach ($produk as $key => $value)
                                        <option value="{{ $key }}" {{ $key == request()->input('produk') ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <select id="filterTipePenjualan" name="filterTipePenjualan" class="form-control" title="Tipe Penjualan">
                                    <option value="">Pilih Tipe Penjualan</option>
                                    <option value="Tradisional" {{ 'Tradisional' == request()->input('tipe_penjualan') ? 'selected' : '' }}>Tradisional</option>
                                    <option value="Sewa" {{ 'Sewa' == request()->input('tipe_penjualan') ? 'selected' : '' }}>Sewa</option>
                                </select>
                            </div>
                            <div class="col-lg col-sm-6 col-12">
                                <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('laporan.omset') }}" class="btn btn-info">Filter</a>
                                <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('laporan.omset') }}" class="btn btn-warning">Clear</a>
                            </div>
                        </div>
                    </row>
                </div>
                <div class="table-responsive">
                    <table class="table datanew" id="dataTable">
                        <thead>
                            <tr>
                                <th class="align-middle">No</th>
                                <th class="align-middle">No Invoice</th>
                                <th class="align-middle">Marketing</th>
                                <th class="align-middle">Tipe</th>
                                <th class="align-middle">Metode</th>
                                <th class="align-middle">Tanggal Invoice</th>
                                <th class="align-middle">Customer</th>
                                <th class="align-middle">Tanggal Pembayaran</th>
                                <th class="align-middle">Barang</th>
                                <th class="align-middle">Harga</th>
                                <th class="align-middle">QTY</th>
                                <th class="align-middle">Jumlah</th>
                                <th class="align-middle">PPN</th>
                                <th class="align-middle">PPH</th>
                                <th class="align-middle">Ongkir</th>
                                <th class="align-middle">Diskon</th>
                                <th class="align-middle">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $index => $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->no_invoice }}</td>
                                <td>{{ $item->nama_sales }}</td>
                                <td>{{ 'Tradisional / Gift' }}</td>
                                <td>{{ $item->metode }}</td>
                                <td>{{ formatTanggal($item->tanggal_invoice) }}</td>
                                <td>{{ $item->nama_customer }}</td>
                                @if(!$item->pembayaran->isEmpty())
                                <td>
                                    <table>
                                        @foreach ($item->pembayaran as $pembayaran)
                                            <tr>
                                                <td>{{ formatTanggal($pembayaran->tanggal_bayar) }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                                @else
                                <td>Belum ada Pembayaran</td>
                                @endif
                                <td>
                                    <table>
                                        @foreach ($item->produk as $produk)
                                            <tr>
                                                <td>{{ $produk->produk->nama }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($item->produk as $produk)
                                            <tr>
                                                <td>{{ formatRupiah($produk->harga_jual) }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        @foreach ($item->produk as $produk)
                                            <tr>
                                                <td>{{ $produk->jumlah }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>{{ $item->jumlah }}</td>
                                <td>{{ $item->ppn_nominal }}</td>
                                <td>{{ $item->pph_nominal }}</td>
                                <td>{{ $item->ongkir_nominal }}</td>
                                <td>{{ $item->total_promo }}</td>
                                <td>{{ $item->total_tagihan }}</td>
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
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function(){
            $('select[id^=filter]').select2();
            $('#filterBtn').click(function(){
                var baseUrl = $(this).data('base-url');
                var urlString = baseUrl;
                var first = true;
                var symbol = '';

                var TanggalInvoice = $('#filterTanggalInvoice').val();
                if (TanggalInvoice) {
                    var filterTanggalInvoice = 'tanggal_invoice=' + TanggalInvoice;
                    if (first == true) {
                        symbol = '?';
                        first = false;
                    } else {
                        symbol = '&';
                    }
                    urlString += symbol;
                    urlString += filterTanggalInvoice;
                }

                var TanggalPembayaran = $('#filterTanggalPembayaran').val();
                if (TanggalPembayaran) {
                    var filterTanggalPembayaran = 'tanggal_pembayaran=' + TanggalPembayaran;
                    if (first == true) {
                        symbol = '?';
                        first = false;
                    } else {
                        symbol = '&';
                    }
                    urlString += symbol;
                    urlString += filterTanggalPembayaran;
                }

                var Status = $('#filterStatus').val();
                if (Status) {
                    var filterStatus = 'status=' + Status;
                    if (first == true) {
                        symbol = '?';
                        first = false;
                    } else {
                        symbol = '&';
                    }
                    urlString += symbol;
                    urlString += filterStatus;
                }

                var Sales = $('#filterSales').val();
                if (Sales) {
                    var filterSales = 'sales=' + Sales;
                    if (first == true) {
                        symbol = '?';
                        first = false;
                    } else {
                        symbol = '&';
                    }
                    urlString += symbol;
                    urlString += filterSales;
                }

                var Produk = $('#filterProduk').val();
                if (Produk) {
                    var filterProduk = 'produk=' + Produk;
                    if (first == true) {
                        symbol = '?';
                        first = false;
                    } else {
                        symbol = '&';
                    }
                    urlString += symbol;
                    urlString += filterProduk;
                }

                var TipePenjualan = $('#filterTipePenjualan').val();
                if (TipePenjualan) {
                    var filterTipePenjualan = 'tipe_penjualan=' + TipePenjualan;
                    if (first == true) {
                        symbol = '?';
                        first = false;
                    } else {
                        symbol = '&';
                    }
                    urlString += symbol;
                    urlString += filterTipePenjualan;
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
        });
        function pdf(){
            var filterTanggalInvoice = $('#filterTanggalInvoice').val();
            var filterTanggalPembayaran = $('#filterTanggalPembayaran').val();
            var filterStatus = $('#filterStatus').val();
            var filterSales = $('#filterSales').val();
            var filterProduk = $('#filterProduk').val();
            var filterTipePenjualan = $('#filterTipePenjualan').val();

            var desc = 'Cetak laporan tanpa filter';
            if(filterTanggalInvoice || filterTanggalPembayaran || filterStatus || filterSales || filterProduk || filterTipePenjualan){
                desc = 'cetak laporan dengan filter';
            }
            
            Swal.fire({
                title: 'Cetak PDF?',
                text: desc,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Cetak',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = "{{ route('laporan.omset-pdf') }}" + '?' + $.param({
                        tanggal_invoice: filterTanggalInvoice,
                        tanggal_pembayaran: filterTanggalPembayaran,
                        status: filterStatus,
                        sales: filterSales,
                        produk: filterProduk,
                        tipe_penjualan: filterTipePenjualan,
                    });
                    
                    window.open(url, '_blank');
                }
            });
        }
        function excel(){
            var filterTanggalInvoice = $('#filterTanggalInvoice').val();
            var filterTanggalPembayaran = $('#filterTanggalPembayaran').val();
            var filterStatus = $('#filterStatus').val();
            var filterSales = $('#filterSales').val();
            var filterProduk = $('#filterProduk').val();
            var filterTipePenjualan = $('#filterTipePenjualan').val();

            var desc = 'Cetak laporan tanpa filter';
            if(filterTanggalInvoice || filterTanggalPembayaran || filterStatus || filterSales || filterProduk || filterTipePenjualan){
                desc = 'cetak laporan dengan filter';
            }
            
            Swal.fire({
                title: 'Cetak Excel?',
                text: desc,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Cetak',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = "{{ route('laporan.omset-excel') }}" + '?' + $.param({
                        tanggal_invoice: filterTanggalInvoice,
                        tanggal_pembayaran: filterTanggalPembayaran,
                        status: filterStatus,
                        sales: filterSales,
                        produk: filterProduk,
                        tipe_penjualan: filterTipePenjualan,
                    });
                    
                    window.location.href = url;
                }
            });
        }
    </script>
@endsection