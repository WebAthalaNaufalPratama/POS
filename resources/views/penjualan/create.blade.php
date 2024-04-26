@extends('layouts.app-von')

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
            <form action="{{ route('penjualan.store') }}" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm">
                        @csrf

                        <div class="row justify-content-around">
                            <div class="col-md-6 border rounded pt-3 me-1">
                                <h5>Informasi Pelanggan</h5>
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label for="id_customer">Nama Customer</label>
                                            <select id="id_customer" name="id_customer" class="form-control" required>
                                                <option value="">Pilih Nama Customer</option>
                                                @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}" data-point="{{ $customer->poin_loyalty }}" data-hp="{{ $customer->handphone }}">{{ $customer->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mt-3">
                                        <div class="form-group">
                                            <label for=""></label>
                                            <div class="add-icon">
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                                    <img src="/assets/img/icons/plus1.svg" alt="img" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama">No Hp/Wa</label>
                                            <input type="text" class="form-control" id="nohandphone" name="nohandphone" placeholder="Nomor Handphone" value="" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="lokasi_id">Lokasi Pembelian</label>
                                            <select id="lokasi_id" name="lokasi_id" class="form-control" readonly required>
                                                @foreach ($lokasis as $lokasi)
                                                <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="distribusi">Distribusi Produk</label>
                                            <select id="distribusi" name="distribusi" class="form-control" required>
                                                <option value="">Pilih Distribusi Produk</option>
                                                <option value="Dikirim">Dikirim</option>
                                                <option value="Diambil">Langsung Diambil</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="point">Jumlah Point</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="inputGroupPrepend2">
                                                    <input type="checkbox" id="cek_point" name="cek_point">
                                                </span>
                                                <input type="number" class="form-control" id="point_dipakai" name="point_dipakai" placeholder="0" value="" aria-describedby="inputGroupPrepend2" readonly required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="harga_jual">Input File</label>
                                            <div class="input-group">
                                                <input type="file" class="form-control" id="bukti_file" name="bukti_file" placeholder="Bukti File Invoice" value="" aria-describedby="inputGroupPrepend2" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-5 border rounded pt-3 ms-1">
                                <h5>Informasi Invoice</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="no_invoice">Nomor Invoice</label>
                                            <input type="text" class="form-control" id="no_invoice" name="no_invoice" placeholder="Nomor Invoice" onchange="generateInvoice(this)" required readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="nama">Tanggal Invoice</label>
                                            <input type="date" class="form-control" id="tanggal_invoice" name="tanggal_invoice" placeholder="Tanggal_Invoice" onchange="updateDate(this)" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select id="status" name="status" class="form-control" required>
                                                <option value="">Pilih Status</option>
                                                <option value="DRAFT">DRAFT</option>
                                                <option value="PUBLISH">PUBLISH</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama">Jatuh Tempo</label>
                                            <input type="date" class="form-control" id="jatuh_tempo" name="jatuh_tempo" placeholder="Tanggal_Jatuh_Tempo" onchange="updateDate(this)" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="employee_id">Nama Sales</label>
                                            <select id="employee_id" name="employee_id" class="form-control" required>
                                                <option value="">Pilih Nama Sales</option>
                                                @foreach ($karyawans as $karyawan)
                                                <option value="{{ $karyawan->id }}">{{ $karyawan->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-around">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2">
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
                                                    <!-- <th>PIC Perangkai</th> -->
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="dynamic_field">
                                                <tr>
                                                    <td>
                                                        <select id="nama_produk_0" name="nama_produk[]" class="form-control">
                                                            <option value="">Pilih Produk</option>
                                                            @foreach ($produks as $produk)
                                                            <option value="{{ $produk->kode }}" data-harga="{{ $produk->harga_jual }}" data-tipe_produk="{{ $produk->tipe_produk }}">
                                                                @if (substr($produk->kode, 0, 3) === 'TRD')
                                                                {{ $produk->nama }}
                                                                @foreach ($produk->komponen as $komponen)
                                                                @if ($komponen->kondisi)
                                                                @foreach($kondisis as $kondisi)
                                                                @if($kondisi->id == $komponen->kondisi)
                                                                - {{ $kondisi->nama }}
                                                                @php
                                                                $found = true;
                                                                break;
                                                                @endphp
                                                                @endif
                                                                @endforeach
                                                                @endif
                                                                @if ($found) @break @endif
                                                                @endforeach
                                                                @elseif (substr($produk->kode, 0, 3) === 'GFT')
                                                                {{ $produk->nama }}
                                                                @endif
                                                            </option>
                                                            @endforeach
                                                        </select>


                                                    </td>
                                                    <td><input type="number" name="harga_satuan[]" id="harga_satuan_0" class="form-control" onchange="calculateTotal(0)" readonly></td>
                                                    <td><input type="number" name="jumlah[]" id="jumlah_0" oninput="multiply($(this))" class="form-control" onchange="calculateTotal(0)"></td>
                                                    <td><select id="jenis_diskon_0" name="jenis_diskon[]" class="form-control" onchange="showInputType(0)">
                                                            <option value="0">Pilih Diskon</option>
                                                            <option value="Nominal">Nominal</option>
                                                            <option value="persen">Persen</option>
                                                        </select>

                                                        <div>
                                                            <div class="input-group">
                                                                <input type="number" name="diskon[]" id="diskon_0" value="" class="form-control" style="display: none;" aria-label="Recipient's username" aria-describedby="basic-addon3" onchange="calculateTotal(0)">
                                                                <span class="input-group-text" id="nominalInput_0" style="display: none;">.00</span>
                                                                <span class="input-group-text" id="persenInput_0" style="display: none;">%</span>
                                                            </div>
                                                        </div>

                                                        <!-- <div >
                                                            <div class="input-group">
                                                                <input type="number" name="diskon[]" id="diskon_0" value="" class="form-control" aria-label="Recipient's username" aria-describedby="basic-addon3" onchange="calculateTotal(0)">
                                                                <span class="input-group-text" id="basic-addon3">%</span>
                                                            </div>
                                                        </div> -->
                                                    </td>
                                                    <td><input type="number" name="harga_total[]" id="harga_total_0" class="form-control" readonly></td>
                                                    <!-- <td><button type="button" name="pic[]" id="pic_0" class="btn btn-warning" data-toggle="modal" data-target="#picModal_0" onclick="copyDataToModal(0)">PIC Perangkai</button></td> -->
                                                    <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-around">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2">
                                <div class="row">
                                    <div class="col-lg-3 col-sm-6 col-12 mt-4">
                                        <div class="form-group">
                                            <label>Pembayaran</label>
                                            <select id="cara_bayar" name="cara_bayar" class="form-control" required>
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
                                            <select id="rekening_id" name="rekening_id" class="form-control">
                                                <option value="">Pilih Bank</option>
                                                @foreach($rekenings as $rekening)
                                                <option value="{{ $rekening->id }}">{{ $rekening->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label>Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" required></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6 col-6 mt-4 ">
                                        <div class="form-group">
                                            <label>Pengiriman</label>
                                            <select id="pilih_pengiriman" name="pilih_pengiriman" class="form-control" required>
                                                <option value="">Pilih Jenis Pengiriman</option>
                                                <option value="exspedisi">Ekspedisi</option>
                                                <option value="sameday">SameDay</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div id="inputOngkir" style="display: none;">
                                                <label for="alamat_tujuan">Alamat Tujuan </label>
                                                <textarea type="text" id="alamat_tujuan" name="alamat_tujuan" class="form-control"></textarea>
                                            </div>
                                            <div id="inputExspedisi" style="display: none;">
                                                <label>Alamat Pengiriman</label>
                                                <select id="ongkir_id" name="ongkir_id" class="form-control">
                                                    <option value="">Pilih Alamat Tujuan</option>
                                                    @foreach($ongkirs as $ongkir)
                                                    <option value="{{ $ongkir->id }}" data-biaya_ongkir="{{ $ongkir->biaya}}">{{ $ongkir->nama }}</option>
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
                                                    <h5><input type="text" id="sub_total" name="sub_total" class="form-control" onchange="calculateTotal(0)" readonly required></h5>
                                                </li>
                                                <li>
                                                    <h4>PPN
                                                        <select id="jenis_ppn" name="jenis_ppn" class="form-control" required>
                                                            <option value=""> Pilih Jenis PPN</option>
                                                            <option value="exclude">EXCLUDE</option>
                                                            <option value="include">INCLUDE</option>
                                                        </select>
                                                    </h4>
                                                    <h5 class="col-lg-5">
                                                        <div class="input-group">
                                                            <input type="text" id="persen_ppn" name="persen_ppn" class="form-control" readonly required>
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                        <input type="text" id="jumlah_ppn" name="jumlah_ppn" class="form-control" readonly required>

                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Promo</h4>
                                                    <h5 class="col-lg-5">
                                                        <div class="row align-items-center">
                                                            <div class="col-9 pe-0">
                                                                <select id="promo_id" name="promo_id" class="form-control" required disabled>
                                                                </select>
                                                            </div>
                                                            <div class="col-3 ps-0 mb-0">
                                                                <button id="btnCheckPromo" class="btn btn-primary w-100"><i class="fa fa-search" data-bs-toggle="tooltip"></i></button>
                                                            </div>
                                                        </div>
                                                        <input type="text" class="form-control" required name="total_promo" id="total_promo" value="{{ old('total_promo') }}" readonly>
                                                    </h5>
                                                    <!-- <h5><select id="promo_id" name="promo_id" class="form-control" onchange="updateHargaSatuan(this)">
                                                            <option value="">Pilih Promo</option>
                                                            @foreach ($promos as $promo)
                                                            <option value="{{ $promo->id }}" data-harga="{{ $promo->harga_jual }}">{{ $promo->nama }}</option>
                                                            @endforeach
                                                        </select></h5> -->
                                                    <!-- <h5>$ 0.00 (0.00%)</h5> -->
                                                </li>
                                                <li>
                                                    <h4>Biaya Ongkir</h4>
                                                    <h5><input type="text" id="biaya_ongkir" name="biaya_ongkir" class="form-control" readonly required></h5>
                                                </li>
                                                <li>
                                                    <h4>DP</h4>
                                                    <h5><input type="text" id="dp" name="dp" class="form-control" required></h5>
                                                </li>
                                                <li class="total">
                                                    <h4>Total Tagihan</h4>
                                                    <h5><input type="text" id="total_tagihan" name="total_tagihan" class="form-control" readonly required></h5>
                                                </li>
                                                <li>
                                                    <h4>Sisa Bayar</h4>
                                                    <h5><input type="text" id="sisa_bayar" name="sisa_bayar" class="form-control" readonly required></h5>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="text-end mt-3">
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <a href="{{ route('penjualan.index') }}" class="btn btn-secondary" type="button">Back</a>
                        </div>
            </form>
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

@foreach ($produks as $index => $produk)
<div class="modal fade" id="picModal_{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form PIC Perangkai {{ $index }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('customer.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tglrangkai">Tanggal Rangkaian</label>
                        <input type="date" class="form-control" id="tglrangkai_{{ $index }}" name="tglrangkai" onchange="updateDate(this)">
                    </div>
                    <div class="form-group">
                        <label for="jnsrangkai">Jenis Rangkaian</label>
                        <input type="text" class="form-control" id="jnsrangkai_{{ $index }}" name="jnsrangkai" value="penjualan" readonly>
                    </div>
                    <div class="form-group">
                        <label for="no_invoice_rangkai_{{ $index }}">Nomor Invoice</label>
                        <input type="text" class="form-control" id="no_invoice_rangkai_{{ $index }}" name="no_invoice_rangkai_{{ $index }}" placeholder="Nomor Invoice" onchange="generateInvoice(this)" required>
                    </div>
                    <div class="form-group">
                        <label for="jumlahStaff_{{ $index }}">Jumlah Staff Perangkai</label>
                        <input type="text" class="form-control" id="jumlahStaff_{{ $index }}" name="jumlahStaff_{{ $index }}" placeholder="Jumlah Staff Perangkai" onchange="generateStaffInput(this)" required>
                    </div>
                    <div class="form-group">
                        <label for="staffPerangkaiContainer">Pilih PIC Perangkai</label>
                        <div id="staffPerangkaiContainer_{{ $index }}"></div>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jumlah</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input type="text" id="nama_produk_modal_{{ $index }}" name="nama_produk_modal" class="form-control" readonly>
                                        <input type="hidden" id="nama_produk_{{ $index }}" name="nama_produk[]" style="display: none;">
                                    </td>
                                    <td>
                                        <input type="number" id="jumlah_produk_modal_{{ $index }}" name="jumlah_produk_modal" class="form-control" readonly>
                                        <input type="hidden" id="jumlah_{{ $index }}" name="jumlah[]" style="display: none;">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="simpanButton_{{ $index }}">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach


<div class="modal fade" id="modalBayar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('customer.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="no_invoice">Nomor Invoice</label>
                        <input type="text" class="form-control" id="no_invoice_bayar" name="no_invoice_bayar" placeholder="Nomor Invoice" onchange="generateInvoice(this)" required>
                    </div>
                    <div class="form-group">
                        <label for="nominalbayar">Nominal</label>
                        <input type="number" class="form-control" id="nominalbayar" name="nominalbayar" placeholder="Nominal Bayar">
                    </div>
                    <div class="form-group">
                        <label for="bankpenerima">Bank Penerima</label>
                        <select>
                            <option value="">Pilih Bank Penerima</option>
                            @foreach ($bankpens as $bankpen)
                            <option value="{{ $bankpen->id }}">{{ $bankpen->bank }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tanggalbayar">Tanggal</label>
                        <input type="date" class="form-control" id="tanggalbayar" name="tanggalbayar" onchange="updateDate(this)">
                    </div>
                    <div class="form-group">
                        <label for="buktibayar">Unggah Bukti</label>
                        <input type="file" class="form-control" id="buktibayar" name="buktibayar" readonly>
                    </div>
                </div>

                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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
    var cekInvoiceNumbers = "<?php echo $cekInvoice ?>";
    // console.log(cekInvoiceNumbers);
    var nextInvoiceNumber = parseInt(cekInvoiceNumbers) + 1;

    function generateInvoice() {
        var invoicePrefix = "INV";
        var currentDate = new Date();
        var year = currentDate.getFullYear();
        var month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
        var day = currentDate.getDate().toString().padStart(2, '0');
        var formattedNextInvoiceNumber = nextInvoiceNumber.toString().padStart(3, '0');

        var generatedInvoice = invoicePrefix + year + month + day + formattedNextInvoiceNumber;
        $('#no_invoice').val(generatedInvoice);
    }

    generateInvoice();
</script>
<script>
    // Function to update date to today's date
    function updateDate(element) {
        var today = new Date().toISOString().split('T')[0];
        element.value = today;
    }

    // Call the function to set the date to today's date initially
    updateDate(document.getElementById('tanggal_invoice'));
    updateDate(document.getElementById('jatuh_tempo'));
    @foreach($produks as $index => $produk)
    updateDate(document.getElementById('tglrangkai_{{ $index }}'), '{{ $index }}');
    @endforeach
    updateDate(document.getElementById('tanggalbayar'));
</script>
<script>
    function showInputType(index) {
        var selectElement = document.getElementById("jenis_diskon_" + index);
        var selectedValue = selectElement.value;
        // console.log(selectedValue);
        var diskonInput = document.getElementById("diskon_" + index);
        var nominalInput = document.getElementById("nominalInput_" + index);
        var persenInput = document.getElementById("persenInput_" + index);

        if (selectedValue === "Nominal") {
            diskonInput.style.display = "block";
            nominalInput.style.display = "block";
            persenInput.style.display = "none";
        } else if (selectedValue === "persen") {
            diskonInput.style.display = "block";
            nominalInput.style.display = "none";
            persenInput.style.display = "block";
        } else {
            diskonInput.style.display = "none";
            nominalInput.style.display = "none";
            persenInput.style.display = "none";
            diskonInput.value = 0;
        }

        calculateTotal(index);
    }

    function calculateTotal(index) {
        var diskonType = $('#jenis_diskon_' + index).val();
        // console.log(diskonType);

        var diskonValue = parseFloat($('#diskon_' + index).val());
        var jumlah = parseFloat($('#jumlah_' + index).val());
        var hargaSatuan = parseFloat($('#harga_satuan_' + index).val());
        var hargaTotal = 0;
        // console.log(diskonValue);

        if (!isNaN(jumlah) && !isNaN(hargaSatuan)) {
            hargaTotal = jumlah * hargaSatuan;
        }

        if (!isNaN(hargaTotal)) {
            if (diskonType === "Nominal" && !isNaN(diskonValue)) {
                hargaTotal -= diskonValue;
            } else if (diskonType === "persen" && !isNaN(diskonValue)) {
                hargaTotal -= (hargaTotal * diskonValue / 100);
            }
        }

        // Set nilai input harga total
        $('#harga_total_' + index).val(hargaTotal.toFixed(2));

        // Hitung ulang subtotal
        var subtotal = 0;
        $('input[name="harga_total[]"]').each(function() {
            subtotal += parseFloat($(this).val()) || 0;
        });

        // Set nilai input subtotal
        $('#sub_total').val(subtotal.toFixed(2));
    }

    function copyDataToModal(index) {
        var namaProdukValue = $('#nama_produk_' + index).val();
        var jumlahValue = $('#jumlah_' + index).val();
        // console.log(namaProdukValue);

        $('#nama_produk_modal_' + index).val(namaProdukValue);
        $('#jumlah_produk_modal_' + index).val(jumlahValue);
    }
</script>

<script>
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $(document).ready(function() {
        var i = 1;
        $('#add').click(function() {
            var newRow = `<tr class="tr_clone" id="row${i}">
                            <td>
                                <select id="nama_produk_${i}" name="nama_produk[]" class="form-control select2">
                                    <option value="">Pilih Produk</option>
                                    @foreach ($produks as $index => $produk)
                                        <option value="{{ $produk->kode }}" data-harga="{{ $produk->harga_jual }}" data-kode="{{ $produk->kode }}" data-tipe="{{ $produk->tipe }}" data-deskripsi="{{ $produk->deskripsi }}" data-tipe_produk="{{ $produk->tipe_produk }}">
                                            @if (substr($produk->kode, 0, 3) === 'TRD') 
                                                {{ $produk->nama }}
                                                @foreach ($produk->komponen as $komponen)
                                                    @if ($komponen->kondisi)
                                                        @foreach($kondisis as $kondisi)
                                                            @if($kondisi->id == $komponen->kondisi)
                                                                - {{ $kondisi->nama }}
                                                                @php
                                                                    $found = true;
                                                                    break;
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                    @if ($found) @break @endif
                                                @endforeach
                                            @elseif (substr($produk->kode, 0, 3) === 'GFT')
                                                {{ $produk->nama }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="harga_satuan[]" id="harga_satuan_${i}" class="form-control" readonly></td>
                            <td><input type="number" name="jumlah[]" id="jumlah_${i}" class="form-control" oninput="multiply(this)"></td>
                            <td>
                                <select id="jenis_diskon_${i}" name="jenis_diskon[]" class="form-control" onchange="showInputType(${i})">
                                    <option value="0">Pilih Diskon</option>
                                    <option value="Nominal">Nominal</option>
                                    <option value="persen">Persen</option>
                                </select>
                                <div class="input-group">
                                    <input type="number" name="diskon[]" id="diskon_${i}" value="" class="form-control" style="display: none;" aria-label="Recipients username" aria-describedby="basic-addon3" onchange="calculateTotal(${i})">
                                    <span class="input-group-text" id="nominalInput_${i}" style="display: none;">.00</span>
                                    <span class="input-group-text" id="persenInput_${i}" style="display: none;">%</span>
                                </div>
                            </td>
                            <td><input type="number" name="harga_total[]" id="harga_total_${i}" class="form-control" readonly></td>
                            <td><button type="button" name="remove" id="${i}" class="btn btn-danger btn_remove">x</button></td>
                        </tr>`;

            $('#dynamic_field').append(newRow);

            // var picModal = `<div class="modal fade" id="picModal_${i}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            //                     <div class="modal-dialog" role="document">
            //                         <div class="modal-content">
            //                             <div class="modal-header">
            //                                 <h5 class="modal-title" id="exampleModalLabel">Form PIC Perangkai ${i}</h5>
            //                                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            //                                     <span aria-hidden="true">&times;</span>
            //                                 </button>
            //                             </div>
            //                             <div class="modal-body">
            //                                 <div class="form-group">
            //                                     <label for="tglrangkai_${i}">Tanggal Rangkaian</label>
            //                                     <input type="date" class="form-control" id="tglrangkai_${i}" name="tglrangkai_${i}">
            //                                 </div>
            //                                 <div class="form-group">
            //                                     <label for="jnsrangkai_${i}">Jenis Rangkaian</label>
            //                                     <input type="text" class="form-control" id="jnsrangkai_${i}" name="jnsrangkai_${i}" value="penjualan" readonly>
            //                                 </div>
            //                                 <div class="form-group">
            //                                     <label for="no_invoice_rangkai_${i}">Nomor Invoice</label>
            //                                     <input type="text" class="form-control" id="no_invoice_rangkai_${i}" name="no_invoice_rangkai_${i}" placeholder="Nomor Invoice" onchange="generateInvoice(this)" required>
            //                                 </div>
            //                                 <div class="form-group">
            //                                     <label for="jumlahStaff_${i}">Jumlah Staff Perangkai</label>
            //                                     <input type="text" class="form-control" id="jumlahStaff_${i}" name="jumlahStaff_${i}" placeholder="Jumlah Staff Perangkai" onchange="generateStaffInput(this)" required>
            //                                 </div>
            //                                 <div class="form-group">
            //                                     <label for="staffPerangkaiContainer_${i}">Pilih PIC Perangkai</label>
            //                                     <div id="staffPerangkaiContainer_${i}"></div>
            //                                 </div>
            //                                 <div class="table-responsive">
            //                                     <table class="table">
            //                                         <thead>
            //                                             <tr>
            //                                                 <th>Nama</th>
            //                                                 <th>Jumlah</th>
            //                                                 <th></th>
            //                                             </tr>
            //                                         </thead>
            //                                         <tbody id="dynamic_field">
            //                                             <tr>
            //                                                 <td>
            //                                                     <select id="nama_produk" name="nama_produk[]" class="form-control">
            //                                                         <option value="">Pilih Produk</option>`;

            // @foreach($produks as $produk)
            // picModal += `<option value="{{ $produk->id }}" data-harga="{{ $produk->harga_jual }}">{{ $produk->nama }}</option>`;
            // @endforeach

            // picModal += `                    </select>
            //                                                     <input type="hidden" name="kode_produk[]" style="display: none;">
            //                                                     <input type="hidden" name="tipe_produk[]" style="display: none;">
            //                                                     <input type="hidden" name="deskripsi_komponen[]" style="display: none;">
            //                                                 </td>
            //                                                 <td><input type="number" name="jumlah[]" id="jumlah_0" oninput="multiply($(this))" class="form-control"></td>
            //                                             </tr>
            //                                         </tbody>
            //                                     </table>
            //                                 </div>
            //                             </div>
            //                             <div class="modal-footer justify-content-center">
            //                                 <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            //                             </div>
            //                         </div>
            //                     </div>
            //                 </div>`;


            // $('body').append(picModal);


            $('#nama_produk_' + i + ', #jenis_diskon_' + i).select2();
            i++
        });

        $(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
            $('#row' + button_id + '').remove();
            calculateTotal(0);
        });

        function addModal() {
            let i = $('.modal').length;
        }
        $('#pic_0').on('click', function() {
            addModal();
        });

        $(document).on('change', '[id^=nama_produk]', function() {
            var id = $(this).attr('id').split('_')[2];
            var selectedOption = $(this).find(':selected');

            var kodeProduk = selectedOption.data('kode');
            var tipeProduk = selectedOption.data('tipe');
            var deskripsiProduk = selectedOption.data('deskripsi');
            // console.log(kodeProduk);
            $('#kode_produk_' + id).val(kodeProduk);
            $('#tipe_produk_' + id).val(tipeProduk);
            $('#deskripsi_komponen_' + id).val(deskripsiProduk);

            // Panggil fungsi updateHargaSatuan
            updateHargaSatuan(this);
        });

        @foreach($produks as $index => $produk)
        $('#jumlahStaff_{{ $index }}').on('input', function() {
            var jumlahStaff = parseInt($(this).val());
            var container = $('#staffPerangkaiContainer_{{ $index }}'); // Ganti ${i} dengan $index

            if (jumlahStaff > 10) {
                jumlahStaff = 10;
                $(this).val(jumlahStaff);
            }

            container.empty();

            for (var i = 0; i < jumlahStaff; i++) {
                var select = $('<select>', {
                    'class': 'form-control',
                    'name': 'staffrangkai_' + i
                });
                select.append($('<option>', {
                    'disabled': true,
                    'selected': true,
                    'hidden': true,
                    'text': 'Pilih Staff Perangkai'
                }));
                select.append($('<option>', {
                    'value': '',
                    'text': 'Pilih Staff Perangkai'
                }));

                @foreach($karyawans as $karyawan)
                select.append($('<option>', {
                    'value': '{{ $karyawan->id }}',
                    'text': '{{ $karyawan->nama }}'
                }));
                @endforeach

                container.append(select);
            }
        });
        @endforeach



        // $('#delivery_order_section').show();

        // $('#distribusi').change(function() {
        //     if ($(this).val() === 'Diambil') {
        //         $('#delivery_order_section').hide();
        //     } else {
        //         $('#delivery_order_section').show();
        //     }
        // });

        $('#btnCheckPromo').click(function(e) {
            e.preventDefault();
            var total_transaksi = $('#total_tagihan').val();
            // console.log(total_transaksi);
            var produk = [];
            var tipe_produk = [];
            $('select[id^="nama_produk_"]').each(function() {
                produk.push($(this).val());
                tipe_produk.push($(this).select2().find(":selected").data("tipe_produk"));

            });
            $(this).html('<span class="spinner-border spinner-border-sm me-2">')
            checkPromo(total_transaksi, tipe_produk, produk);
        });

        $('#cara_bayar').change(function() {
            var pembayaran = $(this).val();

            $('#inputCash').hide();
            $('#inputTransfer').hide();

            if (pembayaran === "cash") {
                $('#inputCash').show();
            } else if (pembayaran === "transfer") {
                $('#inputTransfer').show();
            }
        });

        $('#pilih_pengiriman').change(function() {
            var pengiriman = $(this).val();
            var biayaOngkir = parseFloat($('#biaya_ongkir').val()) || 0;

            $('#inputOngkir').hide();
            $('#inputExspedisi').hide();

            if (pengiriman === "sameday") {
                $('#inputOngkir').show();
                $('#biaya_ongkir').prop('readonly', false);
            } else if (pengiriman === "exspedisi") {
                $('#inputExspedisi').show();
                $('#biaya_ongkir').prop('readonly', true);
                ongkirId();
            }
        });

        $('#ongkir_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var ongkirValue = parseFloat(selectedOption.data('biaya_ongkir')) || 0;
            $('#biaya_ongkir').val(ongkirValue);
            Totaltagihan();
        });

        $('#jenis_ppn').change(function() {
            var ppn = $(this).val();
            $('#persen_ppn').prop('readonly', true);
            var subtotal = parseFloat($('#sub_total').val()) || 0;
            var hitungppn = (11 * subtotal) / 100;
            console.log(hitungppn);

            if (ppn === "include") {
                $('#persen_ppn').val(0);
                $('#jumlah_ppn').val(0);
                $('#persen_ppn').prop('readonly', true);
            } else if (ppn === "exclude") {
                $('#persen_ppn').prop('readonly', false);
                $('#persen_ppn').val(11);
                $('#jumlah_ppn').val(hitungppn);
            }
            Totaltagihan();
        });

        $('#promo_id').change(function() {
            var promo_id = $(this).select2().find(":selected").val()
            if (!promo_id) {
                $('#total_promo').val(0);
                total_harga();
                return 0;
            }
            calculatePromo(promo_id);
        });

        $('#id_customer').change(function() {
            var pointInput = $('#point_dipakai');
            var selectedOption = $(this).find('option:selected');
            var pointValue = selectedOption.data('point');
            if ($('#cek_point').prop('checked')) {
                pointInput.val(pointValue);
            } else {
                pointInput.val(0);
            }
            var hpInput = $('#nohandphone');
            var hpValue = selectedOption.data('hp');
            hpInput.val(hpValue);
        });

        $('#cek_point').change(function() {
            var pointInput = $('#point_dipakai');
            var selectedOption = $('#id_customer').find('option:selected');
            var pointValue = selectedOption.data('point');
            if ($(this).prop('checked')) {
                pointInput.val(pointValue);
            } else {
                pointInput.val(0);
            }
        });


        function checkPromo(total_transaksi, tipe_produk, produk) {
            $('#total_promo').val(0);
            var data = {
                total_transaksi: total_transaksi,
                tipe_produk: tipe_produk,
                produk: produk
            };
            $.ajax({
                url: '/checkPromo',
                type: 'GET',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    $('#promo_id').empty()
                    $('#promo_id').append('<option value="">Pilih Diskon</option>')

                    var min_transaksi = response.min_transaksi;
                    for (var j = 0; j < min_transaksi.length; j++) {
                        var promo = min_transaksi[j];
                        $('#promo_id').append('<option value="' + promo.id + '">' + promo.nama + '</option>');
                    }
                    var tipe_produk = response.tipe_produk;
                    for (var j = 0; j < tipe_produk.length; j++) {
                        var promo = tipe_produk[j];
                        $('#promo_id').append('<option value="' + promo.id + '">' + promo.nama + '</option>');
                    }
                    var produk = response.produk;
                    for (var j = 0; j < produk.length; j++) {
                        var promo = produk[j];
                        $('#promo_id').append('<option value="' + promo.id + '">' + promo.nama + '</option>');
                    }
                    $('#promo_id').attr('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.log(error)
                },
                complete: function() {
                    $('#btnCheckPromo').html('<i class="fa fa-search" data-bs-toggle="tooltip"></i>')
                }
            });
        }

        function updateHargaSatuan(select) {
            var index = select.selectedIndex;
            var hargaSatuanInput = $('#harga_satuan_0');
            var selectedOption = $(select).find('option').eq(index);
            var hargaProduk = selectedOption.data('harga');
            hargaSatuanInput.val(hargaProduk);
        }
        $('#nama_produk').on('change', function() {
            updateHargaSatuan(this);
        });

        function updateHargaSatuan(select) {
            var index = select.selectedIndex;
            var hargaSatuanInput = $('#harga_satuan_' + select.id.split('_')[2]);
            var selectedOption = $(select).find('option').eq(index);
            var hargaProduk = selectedOption.data('harga');
            hargaSatuanInput.val(hargaProduk);
            multiply(hargaSatuanInput);
        }

        window.multiply = function(element) {
            var id;
            // Memeriksa apakah element adalah objek jQuery atau bukan
            if (element instanceof jQuery) {
                id = element.attr('id').split('_')[1];
            } else {
                // Jika element bukan objek jQuery, asumsikan itu adalah elemen HTML dan akses langsung properti id
                id = element.id.split('_')[1];
            }
            var jumlah = $('#jumlah_' + id).val();
            var harga_satuan = $('#harga_satuan_' + id).val();
            if (jumlah && harga_satuan) {
                $('#harga_total_' + id).val(harga_satuan * jumlah);
            }
            var inputs = $('input[name="harga_total[]"]');
            var total = 0;
            inputs.each(function() {
                total += parseInt($(this).val()) || 0;
            });
            $('#harga').val(total);

            $('#sub_total').val(total);
            $('#total_tagihan').val(total);
        }

        function updateSubTotal() {
            var subTotalInput = $('#sub_total');
            var hargaTotalInputs = $('input[name="harga_total[]"]');
            var subTotal = 0;

            hargaTotalInputs.each(function() {
                subTotal += parseFloat($(this).val()) || 0;
            });

            subTotalInput.val(subTotal.toFixed(2));
        }

        function calculatePromo(promo_id) {
            var data = {
                promo_id: promo_id,
            };
            $.ajax({
                url: '/getPromo',
                type: 'GET',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    var total_transaksi = parseInt($('#total_tagihan').val());
                    var total_promo;
                    switch (response.diskon) {
                        case 'persen':
                            total_promo = total_transaksi * parseInt(response.diskon_persen) / 100;
                            // console.log(total_promo);
                            break;
                        case 'nominal':
                            total_promo = parseInt(response.diskon_nominal);
                            break;
                        case 'poin':
                            total_promo = 'poin ' + response.diskon_poin;
                            break;
                        case 'produk':
                            total_promo = response.free_produk.kode + '-' + response.free_produk.nama;
                            break;
                        default:
                            break;
                    }
                    $('#total_promo').val(total_promo);
                    Totaltagihan();
                },
                error: function(xhr, status, error) {
                    console.log(error)
                }
            });
        }

        function Totaltagihan() {
            var subtotal = parseFloat($('#sub_total').val()) || 0;
            // var extot = parseFloat($('#jumlah_ppn').val()) || 0;
            var persenPPN = parseFloat($('#persen_ppn').val()) || 0;
            var dp = parseFloat($('#dp').val()) || 0;
            var biayaOngkir = parseFloat($('#biaya_ongkir').val()) || 0;
            var diskon_nominal = parseFloat($('#total_promo').val()) || 0;
            // console.log(extot);
            var ppn = persenPPN * subtotal / 100;
            var totalTagihan = subtotal + ppn + biayaOngkir + diskon_nominal - dp;
            var sisaBayar = totalTagihan - dp;

            $('#total_tagihan').val(totalTagihan.toFixed(2));
            $('#sisa_bayar').val(sisaBayar.toFixed(2));
            $('#jumlah_ppn').val(ppn.toFixed(2));
        }

        $('#sub_total, #jumlah_ppn, #dp, #biaya_ongkir, #total_promo, #persen_ppn').on('input', Totaltagihan);
    });
</script>

@endsection