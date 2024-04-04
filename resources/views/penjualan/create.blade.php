@extends('layouts.app-penj
')

@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Invoice Penjualan</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.html">Penjualan</a>
                </li>
                <li class="breadcrumb-item active">
                    Invoice Penjualan
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">
                Transaksi Penjualan
            </h4>
        </div>
        <div class="card-body">
            <div id="progrss-wizard" class="twitter-bs-wizard">
                <ul class="twitter-bs-wizard-nav nav nav-pills nav-justified">
                    <li class="nav-item">
                        <a href="#progress-seller-details" class="nav-link" data-toggle="tab">
                            <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="User Details">
                                <i class="far fa-user"></i>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#progress-company-document" class="nav-link" data-toggle="tab">
                            <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Address Detail">
                                <i class="fas fa-map-pin"></i>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#progress-bank-detail" class="nav-link" data-toggle="tab">
                            <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Payment Details">
                                <i class="fas fa-credit-card"></i>
                            </div>
                        </a>
                    </li>
                </ul>

                <div id="bar" class="progress mt-4">
                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"></div>
                </div>
                <div class="tab-content twitter-bs-wizard-tab-content">
                    <div class="tab-pane" id="progress-seller-details">
                        <form action="{{ route('penjualan.store') }}" method="POST">
                            @csrf

                            <div class="form-row row">
                                <div class="mb-4">
                                    <h5>Informasi Pelanggan</h5>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="nama_customer">Nama Customer</label>
                                    <select id="nama_customer" name="nama_customer" class="form-control">
                                        <option value="">Pilih Nama Customer</option>
                                        @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" data-point="{{ $customer->poin_loyalty }}">{{ $customer->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1 mb-3">
                                    <label for=""></label>
                                    <div class="add-icon">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                            <img src="/assets/img/icons/plus1.svg" alt="img" />
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="nama">No Hp/Wa</label>
                                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Nomor Handphone" value="" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="lokasi_beli">Lokasi Pembelian</label>
                                    <select id="lokasi_beli" name="lokasi_beli" class="form-control">
                                        <option value="">Pilih Lokasi Pembelian</option>
                                        @foreach ($lokasis as $lokasi)
                                        <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="distribusi">Distribusi Produk</label>
                                    <select id="distribusi" name="distribusi" class="form-control">
                                        <option value="">Pilih Distribusi Produk</option>
                                        <option value="Dikirim">Dikirim</option>
                                        <option value="Diambil">Langsung Diambil</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="col-md-3 mb-2">
                                    <label for="point">Jumlah Point</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="inputGroupPrepend2">
                                            <input type="checkbox">
                                        </span>
                                        <input type="number" class="form-control" id="point" name="point" placeholder="0" value="" aria-describedby="inputGroupPrepend2" readonly required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="harga_jual">Input File</label>
                                    <div class="input-group">
                                        <input type="file" class="form-control" id="harga_jual" name="harga_jual" placeholder="Harga Jual Produk" value="" aria-describedby="inputGroupPrepend2" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h5>Informasi Invoice</h5>
                            </div>

                            <div class="form-row row">

                                <div class="col-md-2 mb-3">
                                    <label for="no_invoice">Nomor Invoice</label>
                                    <input type="text" class="form-control" id="no_invoice" name="no_invoice" placeholder="Nomor Invoice" value="" required>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="nama">Tanggal Invoice</label>
                                    <input type="date" class="form-control" id="tanggal_invoice" name="tanggal_invoice" placeholder="Tanggal_Invoice" value="" required>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="nama">Jatuh Tempo</label>
                                    <input type="date" class="form-control" id="jatuh_tempo" name="jatuh_tempo" placeholder="Tanggal_Jatuh_Tempo" value="" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="sales">Nama Sales</label>
                                    <select id="sales" name="sales" class="form-control">
                                        <option value="">Pilih Nama Sales</option>
                                        @foreach ($karyawans as $karyawan)
                                        <option value="{{ $karyawan->id }}">{{ $karyawan->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="status">Status</label>
                                    <select id="status" name="status" class="form-control">
                                        <option value="">Pilih Status</option>
                                        <option value="DRAFT">DRAFT</option>
                                        <option value="PUBLISH">PUBLISH</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="mb-4">
                                    <h5>List Produk</h5>
                                </div>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Harga Satuan</th>
                                                <th>Jumlah</th>
                                                <th>Diskon</th>
                                                <th>Harga Total</th>
                                                <th>PIC Perangkai</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="dynamic_field">
                                            <tr>
                                                <td>
                                                    <select id="nama_produk" name="nama_produk[]" class="form-control" onchange="updateHargaSatuan(this)">
                                                        <option value="">Pilih Produk</option>
                                                        @foreach ($produks as $produk)
                                                        <option value="{{ $produk->id }}" data-harga="{{ $produk->harga_jual }}">{{ $produk->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="kode_produk[]" style="display: none;">
                                                    <input type="hidden" name="tipe_produk[]" style="display: none;">
                                                    <input type="hidden" name="deskripsi_komponen[]" style="display: none;">
                                                </td>
                                                <td><input type="number" name="harga_satuan[]" id="harga_satuan_0" class="form-control" readonly></td>
                                                <td><input type="number" name="jumlah[]" id="jumlah_0" oninput="multiply(this)" class="form-control"></td>
                                                <td><select id="jenisdiskon" name="jenisdiskon[]" class="form-control">
                                                        <option value="">Pilih Diskon</option>
                                                        <option value="Nominal">Nominal</option>
                                                        <option value="persen">Persen</option>
                                                    </select>
                                                    <input type="number" name="diskon[]" id="diskon_0" class="form-control">
                                                </td>
                                                <td><input type="number" name="harga_total[]" id="harga_total_0" class="form-control"></td>
                                                <td><button type="button" name="pic" id="pic" class="btn btn-warning">PIC Perangkai</td>
                                                <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 col-sm-6 col-12 mt-4">
                                    <div class="form-group">
                                        <label>Pembayaran</label>
                                        <select id="pembayaran" name="pembayaran" class="form-control">
                                            <option value="">Pilih Pembayaran</option>
                                            <option value="cash">CASH</option>
                                            <option value="transfer">TRANSFER</option>
                                        </select>
                                    </div>
                                    <div id="inputCash" style="display: none;">
                                        <label for="jumlahCash">Jumlah Pembayaran (CASH): </label>
                                        <input type="text" id="jumlahCash" name="jumlahCash" class="form-control">
                                    </div>
                                    <div id="inputTransfer" style="display: none;">
                                        <label>Bank</label>
                                        <select id="bank" name="bank" class="form-control">
                                            <option value="">Pilih Bank</option>
                                            @foreach($rekenings as $rekening)
                                            <option value="{{ $rekening->id }}">{{ $rekening->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label>Notes</label>
                                        <textarea class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-6 mt-4 ">
                                    <div class="form-group">
                                        <label>Pengiriman</label>
                                        <select id="pengiriman" name="pengiriman" class="form-control">
                                            <option value="">Pilih Jenis Pengiriman</option>
                                            <option value="exspedisi">Ekspedisi</option>
                                            <option value="sameday">SameDay</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div id="inputOngkir" style="display: none;">
                                            <label for="jumlahOngkir">Jumlah Biaya Ongkir : </label>
                                            <input type="text" id="jumlahOngkir" name="jumlahOngkir" class="form-control">
                                        </div>
                                        <div id="inputExspedisi" style="display: none;">
                                            <label>Alamat Pengiriman</label>
                                            <select id="exspedisi" name="exspedisi" class="form-control">
                                                <option value="">Pilih Alamat Tujuan</option>
                                                @foreach($ongkirs as $ongkir)
                                                <option value="{{ $ongkir->id }}">{{ $ongkir->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 float-md-right">
                                    <div class="total-order">
                                        <ul>
                                            <li>
                                                <h4>Sub Total</h4>
                                                <h5>$ 0.00 (0.00%)</h5>
                                            </li>
                                            <li>
                                                <h4><select id="jenis_ppn" name="jenis_ppn" class="form-control" onchange="updateHargaSatuan(this)">
                                                        <option value="exclude">EXCLUDE</option>
                                                        <option value="include">INCLUDE</option>
                                                    </select></h4>
                                                <h5>$ 0.00</h5>
                                            </li>
                                            <li>
                                                <h4><select id="promo" name="promo" class="form-control" onchange="updateHargaSatuan(this)">
                                                        <option value="">Pilih Promo</option>
                                                        @foreach ($promos as $promo)
                                                        <option value="{{ $promo->id }}" data-harga="{{ $promo->harga_jual }}">{{ $promo->nama }}</option>
                                                        @endforeach
                                                    </select></h4>
                                                <h5>$ 0.00 (0.00%)</h5>
                                            </li>
                                            <li>
                                                <h4>DP</h4>
                                                <h5>$ 0.00 (0.00%)</h5>
                                            </li>
                                            <li class="total">
                                                <h4>Total Tagihan</h4>
                                                <h5>$ 0.00</h5>
                                            </li>
                                            <li>
                                                <h4>Sisa Bayar</h4>
                                                <h5>$ 0.00 (0.00%)</h5>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                            </div>
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <a href="{{ route('penjualan.index') }}" class="btn btn-secondary" type="button">Back</a>
                        </form>
                        <ul class="pager wizard twitter-bs-wizard-pager-link">
                            <li class="next">
                                <a href="javascript: void(0);" class="btn btn-primary" onclick="nextTab()">Next
                                    <i class="bx bx-chevron-right ms-1"></i></a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-pane" id="progress-company-document">
                        <div>
                            <div class="mb-4">
                                <h5>
                                    Location Details
                                </h5>
                            </div>
                            <form>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="progresspill-pancard-input" class="form-label">Address
                                                Line
                                                1</label>
                                            <input type="text" class="form-control" id="progresspill-pancard-input" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="progresspill-vatno-input" class="form-label">Address
                                                Line
                                                2</label>
                                            <input type="text" class="form-control" id="progresspill-vatno-input" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="progresspill-cstno-input" class="form-label">Landmark</label>
                                            <input type="text" class="form-control" id="progresspill-cstno-input" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="progresspill-servicetax-input" class="form-label">City</label>
                                            <input type="text" class="form-control" id="progresspill-servicetax-input" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="progresspill-companyuin-input" class="form-label">State</label>
                                            <input type="text" class="form-control" id="progresspill-companyuin-input" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="progresspill-declaration-input" class="form-label">Country</label>
                                            <input type="text" class="form-control" id="progresspill-declaration-input" />
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <ul class="pager wizard twitter-bs-wizard-pager-link">
                                <li class="previous">
                                    <a href="javascript: void(0);" class="btn btn-primary" onclick="nextTab()"><i class="bx bx-chevron-left me-1"></i>
                                        Previous</a>
                                </li>
                                <li class="next">
                                    <a href="javascript: void(0);" class="btn btn-primary" onclick="nextTab()">Next
                                        <i class="bx bx-chevron-right ms-1"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-pane" id="progress-bank-detail">
                        <div>
                            <div class="mb-4">
                                <h5>Payment Details</h5>
                            </div>
                            <form>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="progresspill-namecard-input" class="form-label">Name on
                                                Card</label>
                                            <input type="text" class="form-control" id="progresspill-namecard-input" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Credit
                                                Card
                                                Type</label>
                                            <select class="form-select">
                                                <option selected>
                                                    Select
                                                    Card
                                                    Type
                                                </option>
                                                <option value="AE">
                                                    American
                                                    Express
                                                </option>
                                                <option value="VI">
                                                    Visa
                                                </option>
                                                <option value="MC">
                                                    MasterCard
                                                </option>
                                                <option value="DI">
                                                    Discover
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="progresspill-cardno-input" class="form-label">Credit
                                                Card
                                                Number</label>
                                            <input type="text" class="form-control" id="progresspill-cardno-input" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="progresspill-card-verification-input" class="form-label">Card
                                                Verification
                                                Number</label>
                                            <input type="text" class="form-control" id="progresspill-card-verification-input" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="progresspill-expiration-input" class="form-label">Expiration
                                                Date</label>
                                            <input type="text" class="form-control" id="progresspill-expiration-input" />
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <ul class="pager wizard twitter-bs-wizard-pager-link">
                                <li class="previous">
                                    <a href="javascript: void(0);" class="btn btn-primary" onclick="nextTab()"><i class="bx bx-chevron-left me-1"></i>
                                        Previous</a>
                                </li>
                                <li class="float-end">
                                    <a href="javascript: void(0);" class="btn btn-primary" data-bs-toggle="modal" data-bs-target=".confirmModal">Save Changes</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form action="{{ route('customer.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="add_nama" required>
            </div>
            <div class="mb-3">
              <label for="tipe" class="col-form-label">Tipe Customer</label>
              <div class="form-group">
                <select class="select2" name="tipe" id="add_tipe" required>
                  <option value="">Pilih Tipe</option>
                  <option value="tradisional">tradisional</option>
                  <option value="sewa">sewa</option>
                  <option value="premium">premium</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="handphone" class="col-form-label"> No Handphone</label>
              <input type="text" class="form-control" name="handphone" id="add_handphone" required>
            </div>
            <div class="mb-3">
              <label for="alamat" class="col-form-label">Alamat</label>
              <textarea class="form-control" name="alamat" id="add_alamat" required></textarea>
            </div>
            <div class="mb-3">
              <label for="tanggal_lahir" class="col-form-label">Tanggal Lahir</label>
              <input type="date" class="form-control" name="tanggal_lahir" id="add_tanggal_lahir" required>
            </div>
            <div class="mb-3">
              <label for="tanggal_bergabung" class="col-form-label">Tanggal Gabung</label>
              <input type="date" class="form-control" name="tanggal_bergabung" id="add_tanggal_bergabung" required>
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
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('nama_customer').addEventListener('change', function() {
            var pointInput = document.getElementById('point');
            var selectedOption = this.options[this.selectedIndex];
            var pointValue = selectedOption.getAttribute('data-point');
            pointInput.value = pointValue;
            console.log("Selected customer's point value: " + pointValue);
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var select = document.getElementById('nama_produk');
        select.addEventListener('change', function() {
            updateHargaSatuan(this);
        });

        function updateHargaSatuan(select) {
            var index = select.selectedIndex;
            var hargaSatuanInput = document.getElementById('harga_satuan_0');
            var selectedOption = select.options[index];
            var hargaProduk = selectedOption.getAttribute('data-harga');
            hargaSatuanInput.value = hargaProduk;
        }
    });
</script>

<script>
    document.getElementById("pembayaran").addEventListener("change", function() {
        var pembayaran = this.value;

        document.getElementById("inputCash").style.display = "none";
        document.getElementById("inputTransfer").style.display = "none";

        if (pembayaran === "cash") {
            document.getElementById("inputCash").style.display = "block";
        } else if (pembayaran === "transfer") {
            document.getElementById("inputTransfer").style.display = "block";
        }
    });
</script>
<script>
    document.getElementById("pengiriman").addEventListener("change", function() {
        var pengiriman = this.value;

        document.getElementById("inputOngkir").style.display = "none";
        document.getElementById("inputExspedisi").style.display = "none";

        if (pengiriman === "sameday") {
            document.getElementById("inputOngkir").style.display = "block";
        } else if (pengiriman == "exspedisi") {
            document.getElementById("inputExspedisi").style.display = "block";
        }
    });
</script>

<script>
    $(document).ready(function() {
        $('[id^=nama_produk]').select2();
        var i = 1;
        $('#add').click(function() {
            var newRow = '<tr class="tr_clone" id="row' + i + '">' +
                '<td>' +
                '<select id="nama_produk_' + i + '" name="nama_produk[]" class="form-control select2">' +
                '<option value="">Pilih Produk</option>' +
                '@foreach ($produks as $produk)' +
                '<option value="{{ $produk->id }}">{{ $produk->nama }}</option>' +
                '@endforeach' +
                '</select>' +
                '<input type="hidden" name="kode_produk[]" id="kode_produk_' + i + '" style="display: none;">' +
                '<input type="hidden" name="tipe_produk[]" id="tipe_produk_' + i + '" style="display: none;">' +
                '<input type="hidden" name="deskripsi_komponen[]" id="deskripsi_komponen_' + i + '" style="display: none;">' +
                '</td>' +
                '<td><input type="number" name="harga_satuan[]" id="harga_satuan_' + i + '" oninput="multiply(this)" class="form-control"></td>' +
                '<td><input type="number" name="jumlah[]" id="jumlah_' + i + '" oninput="multiply(this)" class="form-control"></td>' +
                '<td><input type="number" name="harga_total[]" id="harga_total_' + i + '" class="form-control"></td>' +
                '<td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">x</button></td>' +
                '</tr>';
            $('#dynamic_field').append(newRow);
            $('#nama_produk_' + i + ', #kondisi_' + i).select2();
            i++
        });
        $(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
            $('#row' + button_id + '').remove();
            multiply($('#harga_satuan_0'))
            multiply($('#jumlah_0'))
        });
        $(document).on('change', '[id^=nama_produk]', function() {
            var id = $(this).attr('id').split('_')[2]; // Ambil bagian angka ID
            var selectedValue = $(this).val();
            $('#kode_produk_' + id).val($(this).data('kode'));
            $('#tipe_produk_' + id).val($(this).data('tipe'));
            $('#deskripsi_komponen_' + id).val($(this).data('deskripsi'));
        });
    });

    function multiply(element) {
        var id = 0
        var jumlah = 0
        var harga_satuan = 0
        var jenis = $(element).attr('id')
        if (jenis.split('_').length == 2) {
            id = $(element).attr('id').split('_')[1];
            jumlah = $(element).val();
            harga_satuan = $('#harga_satuan_' + id).val();
            if (harga_satuan) {
                $('#harga_total_' + id).val(harga_satuan * jumlah)
            }
        } else if (jenis.split('_').length == 3) {
            id = $(element).attr('id').split('_')[2];
            harga_satuan = $(element).val();
            jumlah = $('#jumlah_' + id).val();
            if (jumlah) {
                $('#harga_total_' + id).val(harga_satuan * jumlah)
            }
        }

        var inputs = $('input[name="harga_total[]"]');
        var total = 0;
        inputs.each(function() {
            total += parseInt($(this).val()) || 0;
        });
        $('#harga').val(total)
    }
</script>
@endsection