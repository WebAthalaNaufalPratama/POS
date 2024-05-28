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
            <form action="{{ route('penjualan.payment', ['penjualan' => $penjualans->id]) }}" method="POST" enctype="multipart/form-data">
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
                                            <select id="id_customer" name="id_customer" class="form-control" required disabled>
                                                <!-- <option value="">Pilih Nama Customer</option> -->
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
                                            <input type="text" class="form-control" id="nohandphone" name="nohandphone" placeholder="Nomor Handphone" value="{{ $customer->handphone }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="lokasi_id">Lokasi Pembelian</label>
                                            <select id="lokasi_id" name="lokasi_id" class="form-control" readonly required>
                                                @foreach ($lokasis as $lokasi)
                                                <option value="{{ $penjualans->lokasi_id }}">{{ $lokasi->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="distribusi">Distribusi Produk</label>
                                            <select id="distribusi" name="distribusi" class="form-control" required disabled>
                                                <!-- <option value="">Pilih Distribusi Produk</option> -->
                                                <option value="Dikirim" {{ $penjualans->distribusi == 'Dikirim' ? 'selected' : '' }}>Dikirim</option>
                                                <option value="Diambil" {{ $penjualans->distribusi == 'Diambil' ? 'selected' : '' }}>Langsung Diambil</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="point">Jumlah Point</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="inputGroupPrepend2">
                                                    <input type="checkbox" id="cek_point" name="cek_point" checked disabled>
                                                </span>
                                                <input type="number" class="form-control" id="point_dipakai" name="point_dipakai" placeholder="0" value="{{ $penjualans->point_dipakai }}" aria-describedby="inputGroupPrepend2" readonly required disabled>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="harga_jual">Input File</label>
                                            <div class="input-group mt-3">
                                                <!-- <input type="file" id="bukti_file" name="bukti_file" placeholder="Bukti File Invoice" aria-describedby="inputGroupPrepend2" required disabled> -->
                                                <div class="custom-file-container" data-upload-id="myFirstImage">
                                                    <label>Bukti Invoice <a href="javascript:void(0)" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"></a>
                                                    </label>
                                                    <label class="custom-file-container__custom-file">
                                                        <input type="file" id="bukti_file" class="custom-file-container__custom-file__custom-file-input" name="file" accept="image/*" required disabled>
                                                        <span class="custom-file-container__custom-file__custom-file-control"></span>
                                                    </label>
                                                    <span class="text-danger">max 2mb</span>
                                                    <img id="preview" src="{{ $penjualans->bukti_file ? '/storage/' . $penjualans->bukti_file : '' }}" alt="your image" />
                                                </div>
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
                                            <input type="text" class="form-control" id="no_invoice" name="no_invoice" placeholder="Nomor Invoice" value="{{ $penjualans->no_invoice}}" required disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="nama">Tanggal Invoice</label>
                                            <input type="date" class="form-control" id="tanggal_invoice" name="tanggal_invoice" placeholder="Tanggal_Invoice" value="{{ $penjualans->tanggal_invoice}}" required disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select id="status" name="status" class="form-control" required disabled>
                                                <option value="">Pilih Status</option>
                                                <option value="DRAFT" {{$penjualans->status == 'DRAFT' ? 'selected' : ''}}>DRAFT</option>
                                                <option value="PUBLISH" {{$penjualans->status == 'PUBLISH' ? 'selected' : ''}}>PUBLISH</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama">Jatuh Tempo</label>
                                            <input type="date" class="form-control" id="jatuh_tempo" name="jatuh_tempo" placeholder="Tanggal_Jatuh_Tempo" value="{{ $penjualans->jatuh_tempo}}" required disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="employee_id">Nama Sales</label>
                                            <select id="employee_id" name="employee_id" class="form-control" required disabled>
                                                <!-- <option value="">Pilih Nama Sales</option> -->
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
                                                @if(count($produks) < 1) <tr>
                                                    <td>
                                                        <select id="nama_produk_0" name="nama_produk[]" class="form-control" onchange="updateHargaSatuan(this)" disabled>
                                                            <option value="">Pilih Produk</option>
                                                            @foreach ($produkjuals as $produk)
                                                            <option value="{{ $produk->kode }}" data-harga="{{ $produk->harga_jual }}">{{ $produk->nama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="harga_satuan[]" id="harga_satuan_0" class="form-control" onchange="calculateTotal(0)" readonly disabled></td>
                                                    <td><input type="number" name="jumlah[]" id="jumlah_0" oninput="multiply($(this))" class="form-control" onchange="calculateTotal(0)" disabled></td>
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
                                                    <!-- <td><button id="btnPerangkai_0" data-produk="" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPerangkai w-100">Perangkai</button></td> -->
                                                    <!-- <td><button type="button" name="pic[]" id="pic_0" class="btn btn-warning" data-toggle="modal" data-target="#picModal_0" onclick="copyDataToModal(0)">PIC Perangkai</button></td> -->
                                                    <!-- <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td> -->
                                                    </tr>
                                                    @endif
                                                    @php
                                                    $i = 0;
                                                    @endphp
                                                    @foreach ($produks as $komponen)
                                                    <tr>
                                                        <td>
                                                            <select id="nama_produk_{{ $i }}" name="nama_produk[]" class="form-control" onchange="updateHargaSatuan(this)" readonly>
                                                                <option value="">Pilih Produk</option>
                                                                @foreach ($produkjuals as $produk)
                                                                <option value="{{ $produk->kode }}" data-harga="{{ $produk->harga_jual }}" {{ $komponen->produk->kode == $produk->kode ? 'selected' : '' }}>{{ $produk->nama }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="number" name="harga_satuan[]" id="harga_satuan_{{ $i }}" class="form-control" value="{{ $komponen->harga }}" onchange="calculateTotal(0)" readonly disabled></td>
                                                        <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" oninput="multiply($(this))" class="form-control" value="{{ $komponen->jumlah }}" onchange="calculateTotal(0)" disabled></td>
                                                        <td><select id="jenis_diskon_{{ $i }}" name="jenis_diskon[]" class="form-control" onchange="showInputType(0)" disabled>
                                                                <option value="0">Pilih Diskon</option>
                                                                <option value="Nominal" {{ $komponen->jenis_diskon == 'Nominal' ? 'selected' : ''}}>Nominal</option>
                                                                <option value="persen" {{ $komponen->jenis_diskon == 'persen' ? 'selected' : ''}}>Persen</option>
                                                            </select>

                                                            <div>
                                                                <div class="input-group">
                                                                    <input type="number" name="diskon[]" id="diskon_{{ $i }}" value="{{ $komponen->diskon}}" class="form-control" style="display: none;" aria-label="Recipient's username" aria-describedby="basic-addon3" onchange="calculateTotal(0)" disabled>
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
                                                        <td><input type="number" name="harga_total[]" id="harga_total_{{ $i }}" class="form-control" value="{{ $komponen->harga_jual}}" readonly></td>
                                                        <!-- <td><button id="btnPerangkai_{{ $i }}" data-produk="{{ $komponen->id }}" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPerangkai w-100">Perangkai</button></td> -->
                                                        <!-- <td><button type="button" id="btnPerangkai_{{ $i }}" data-produk="{{ $komponen->id }}" class="btn btn-warning" data-toggle="modal" data-target="#picModal_0" onclick="copyDataToModal(0)">PIC Perangkai</button></td> -->
                                                        <!-- <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td> -->
                                                        @php
                                                        $i++;
                                                        @endphp
                                                    </tr>
                                                    @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-around">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2">
                                <div class="row">
                                    <!-- Payment and Shipping Section -->
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="row mt-4">
                                            <!-- Payment Section -->
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Pembayaran</label>
                                                    <select id="cara_bayar" name="cara_bayar" class="form-control" required>
                                                        <option value="">Pilih Pembayaran</option>
                                                        <option value="cash" {{ $penjualans->cara_bayar == 'cash' ? 'selected' : ''}}>CASH</option>
                                                        <option value="transfer" {{ $penjualans->cara_bayar == 'transfer' ? 'selected' : ''}}>TRANSFER</option>
                                                    </select>
                                                </div>
                                                <div id="inputTransfer" style="display: none;">
                                                    <label>Rekening Von</label>
                                                    <select id="rekening_id" name="rekening_id" class="form-control">
                                                        <option value="">Pilih Bank</option>
                                                        @foreach($bankpens as $bankpen)
                                                        <option value="{{ $bankpen->id }}" {{ $bankpen->id == $penjualans->rekening_id ? 'selected' : ''}}>{{ $bankpen->bank }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group" style="display:none;">
                                                    <label for="no_invoice">Nomor Invoice</label>
                                                    <input type="text" class="form-control" id="no_invoice_bayar" name="no_invoice_bayar" placeholder="Nomor Invoice" onchange="generateInvoiceBayar(this)" readonly>
                                                </div>
                                                <div class="form-group mt-3">
                                                    <div id="inputPembayaran" style="display: none;">
                                                        <label for="nominal">Nominal</label>
                                                        <input type="number" class="form-control" id="nominal" name="nominal" value="" placeholder="Nominal Bayar" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div id="inputBuktiBayar" style="display: none;">
                                                        <label for="buktibayar">Unggah Bukti</label>
                                                        <input type="file" class="form-control" id="bukti" name="bukti">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Shipping Section -->
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Pengiriman</label>
                                                    <select id="pilih_pengiriman" name="pilih_pengiriman" class="form-control" required>
                                                        <option value="">Pilih Jenis Pengiriman</option>
                                                        <option value="exspedisi" {{ $penjualans->pilih_pengiriman == 'exspedisi' ? 'selected' : ''}}>Ekspedisi</option>
                                                        <option value="sameday" {{ $penjualans->pilih_pengiriman == 'sameday' ? 'selected' : ''}}>SameDay</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <div id="inputOngkir" style="display: none;">
                                                        <label for="alamat_tujuan">Alamat Tujuan</label>
                                                        <textarea type="text" id="alamat_tujuan" name="alamat_tujuan" class="form-control">{{ $penjualans->alamat_tujuan}}</textarea>
                                                    </div>
                                                    <div id="inputExspedisi" style="display: none;">
                                                        <label>Alamat Pengiriman</label>
                                                        <select id="ongkir_id" name="ongkir_id" class="form-control">
                                                            <option value="">Pilih Alamat Tujuan</option>
                                                            @foreach($ongkirs as $ongkir)
                                                            <option value="{{ $ongkir->id }}" data-biaya_ongkir="{{ $ongkir->biaya }}" {{$ongkir->id == $penjualans->ongkir_id ? 'selected' : ''}}>{{ $ongkir->nama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label>Notes</label>
                                                    <textarea class="form-control" id="notes" name="notes" required>{{$penjualans->notes}}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Table Section -->
                                        <div class="row mt-4">
                                            <div class="col-lg-12">
                                                <table class="table table-responsive border rounded">
                                                    <thead>
                                                        <tr>
                                                            <th>Pembuat</th>
                                                            <th>Penyetuju</th>
                                                            <th>Pemeriksa</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td id="pembuat">{{ Auth::user()->name }}</td>
                                                            <td id="penyetuju">-</td>
                                                            <td id="pemeriksa">-</td>
                                                        </tr>
                                                        <tr>
                                                            <td id="tgl_pembuat" style="width: 25%;">{{ date('d-m-Y') }}</td>
                                                            <td id="tgl_penyetuju" style="width: 25%;">-</td>
                                                            <td id="tgl_pemeriksa" style="width: 25%;">-</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Summary Section -->
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="total-order mt-4">
                                            <ul>
                                                <li>
                                                    <h4>Sub Total</h4>
                                                    <h5><input type="text" id="sub_total" name="sub_total" class="form-control" value="{{ $penjualans->sub_total}}" readonly required disabled></h5>
                                                </li>
                                                <li>
                                                    <h4><select id="jenis_ppn" name="jenis_ppn" class="form-control" required disabled>
                                                            <option value=""> Pilih Jenis PPN</option>
                                                            <option value="exclude" {{ $penjualans->jenis_ppn = 'exclude' ? 'selected' : ''}}> PPN EXCLUDE</option>
                                                            <option value="include" {{ $penjualans->jenis_ppn = 'include' ? 'selected' : ''}}> PPN INCLUDE</option>
                                                        </select></h4>
                                                    <h5><input type="text" id="jumlah_ppn" name="jumlah_ppn" class="form-control" value="{{ $penjualans->jumlah_ppn}}" required disabled></h5>
                                                </li>
                                                <li>
                                                    <h4>Promo</h4>
                                                    <h5 class="col-lg-5">
                                                        <div class="row align-items-center">
                                                            <div class="col-9 pe-0">
                                                                <select id="promo_id" name="promo_id" class="form-control" value="{{ $penjualans->promo_id}}" required disabled>
                                                                    @foreach ($promos as $promo)
                                                                    <option value="{{ $promo->id }}">{{ $promo->nama }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-3 ps-0 mb-0">
                                                                <button id="btnCheckPromo" class="btn btn-primary w-100"><i class="fa fa-search" data-bs-toggle="tooltip"></i></button>
                                                            </div>
                                                        </div>
                                                        <input type="text" class="form-control" required name="total_promo" id="total_promo" value="{{ $penjualans->total_promo }}" readonly readonly>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Biaya Ongkir</h4>
                                                    <h5><input type="text" id="biaya_ongkir" name="biaya_ongkir" class="form-control" value="{{ $penjualans->biaya_ongkir}}" required disabled></h5>
                                                </li>
                                                <li>
                                                    <h4>DP</h4>
                                                    <h5><input type="text" id="dp" name="dp" class="form-control" value="{{$penjualans->dp}}" required disabled></h5>
                                                </li>
                                                <li class="total">
                                                    <h4>Total Tagihan</h4>
                                                    <h5><input type="text" id="total_tagihan" name="total_tagihan" class="form-control" value="{{ $penjualans->total_tagihan}}" readonly required disabled></h5>
                                                </li>
                                                <li>
                                                    <h4>Sisa Bayar</h4>
                                                    <h5><input type="text" id="sisa_bayar" name="sisa_bayar" class="form-control" value="{{ $penjualans->sisa_bayar}}" readonly required disabled></h5>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>    
                            </div>
                        </div>

                        <div class="row justify-content-between">
                            <div class="col-md-12">
                                <label for=""></label>
                                <div class="add-icon text-end">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalBayar">add +</button>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-around">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-1">
                                <div class="form-row row">
                                    <div class="mb-4">
                                        <h5>Riwayat Pembayaran</h5>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table datanew">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>No Invoice Bayar</th>
                                                            <th>Nominal</th>
                                                            <th>Rekening</th>
                                                            <th>Tanggal_Bayar</th>
                                                            <th>Status Bayar</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($pembayarans as $pembayaran)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $pembayaran->no_invoice_bayar }}</td>
                                                            <td>{{ $pembayaran->nominal }}</td>
                                                            <td>@if($pembayaran->rekening == null)
                                                                Pembayaran Cash
                                                                @else
                                                                {{ $pembayaran->rekening->bank }}
                                                                @endif
                                                            </td>
                                                            <td>{{ $pembayaran->tanggal_bayar }}</td>
                                                            <td>{{ $pembayaran->status_bayar }}</td>
                                                            <td>
                                                                <div class="dropdown">
                                                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>
                                                                    <div class="dropdown-menu">
                                                                        <a class="dropdown-item" href="{{ route('pembayaran.edit', ['pembayaran' => $pembayaran->id]) }}">Edit</a>
                                                                    </div>
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

                            <div class="text-end mt-3">
                                <!-- <button class="btn btn-primary" type="submit">Submit</button> -->
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

<div class="modal fade" id="modalPerangkai" tabindex="-1" aria-labelledby="modalPerangkaiLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPerangkaiLabel">Atur Perangkai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="form_perangkai" action="{{ route('form.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="route" value="{{ request()->route()->getName() }},penjualan,{{ request()->route()->parameter('penjualan') }}">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-sm-8">
                                <label for="prdTerjual" class="col-form-label">Produk</label>
                                <input type="text" class="form-control" name="produk_id" id="prdTerjual" readonly required>
                            </div>
                            <input type="hidden" name="prdTerjual_id" id="prdTerjual_id" value="">
                            <div class="col-sm-4">
                                <label for="jml_produk" class="col-form-label">Jumlah</label>
                                <input type="number" class="form-control" name="jml_produk" id="jml_produk" readonly required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="no_form" class="col-form-label">No Form Perangkai</label>
                        <input type="text" class="form-control" name="no_form" id="no_form" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="jenis_rangkaian" class="col-form-label">Jenis Rangkaian</label>
                        <input type="text" class="form-control" name="jenis_rangkaian" id="jenis_rangkaian" value="Penjualan" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="col-form-label">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" id="add_tanggal" value="{{ date('Y-m-d') }}" required readonly>
                    </div>
                    <div class="mb-3">
                        <label for="jml_perangkai" class="col-form-label">Jumlah Perangkai</label>
                        <input type="number" class="form-control" name="jml_perangkai" id="jml_perangkai" required readonly>
                    </div>
                    <div class="mb-3">
                        <label for="perangkai_id" class="col-form-label">Perangkai</label>
                        <div id="div_perangkai" class="form-group">
                            <select id="perangkai_id_0" name="perangkai_id[]" class="form-control" required readonly>
                                <option value="">Pilih Perangkai</option>
                                @foreach ($perangkai as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <!-- <button type="submit" class="btn btn-primary">Simpan</button> -->
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBayar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('pembayaran.store', ['invoice_penjualan_id' => $penjualans->id])}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="no_invoice">Nomor Invoice</label>
                        <input type="text" class="form-control" id="no_invoice_byr" name="no_invoice_bayar" placeholder="Nomor Invoice" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="bayar">Cara Bayar</label>
                        <select class="form-control" id="bayar" name="cara_bayar" required>
                            <option value="">Pilih Cara Bayar</option>
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nominal">Nominal</label>
                        <input type="number" class="form-control" id="nominal" name="nominal" value="{{ $penjualans->sisa_bayar }}" placeholder="Nominal Bayar" required>
                    </div>

                    <div class="form-group" id="rekening">
                        <label for="bankpenerima">Rekening Vonflorist</label>
                        <select class="form-control" id="rekening_id" name="rekening_id" required>
                            <option value="">Pilih Rekening Von</option>
                            @foreach ($bankpens as $bankpen)
                            <option value="{{ $bankpen->id }}">{{ $bankpen->bank }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tanggalbayar">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal_bayar" name="tanggal_bayar" onchange="updateDate(this)" required>
                    </div>
                    <div class="form-group">
                        <label for="buktibayar">Unggah Bukti</label>
                        <input type="file" class="form-control" id="bukti" name="bukti" required>
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

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
    var cekInvoiceNumbers = "<?php echo $cekInvoice; ?>";
    var nextInvoiceNumber = parseInt(cekInvoiceNumbers) + 1;

    function generateInvoice() {
        var invoicePrefix = "BYJ";
        var currentDate = new Date();
        var year = currentDate.getFullYear();
        var month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
        var day = currentDate.getDate().toString().padStart(2, '0');
        var formattedNextInvoiceNumber = nextInvoiceNumber.toString().padStart(3, '0');

        var generatedInvoice = invoicePrefix + year + month + day + formattedNextInvoiceNumber;
        $('#no_invoice_byr').val(generatedInvoice);
    }

    generateInvoice();
});

</script>
<script>
    function updateDate(element) {
        var today = new Date().toISOString().split('T')[0];
        element.value = today;
    }
    updateDate(document.getElementById('tanggal_bayar'));
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
            var newRow = '<tr class="tr_clone" id="row' + i + '">' +
                '<td>' +
                '<select id="nama_produk_' + i + '" name="nama_produk[]" class="form-control select2">' +
                '<option value="">Pilih Produk</option>' +
                '@foreach ($produks as $index => $produk)' +
                '<option value="{{ $produk->kode }}" data-harga="{{ $produk->harga_jual }}" data-kode="{{ $produk->kode }}" data-tipe="{{ $produk->tipe }}" data-deskripsi="{{ $produk->deskripsi }}">{{ $produk->nama }}</option>' +
                '@endforeach' +
                '</select>' +
                '</td>' +
                '<td><input type="number" name="harga_satuan[]" id="harga_satuan_' + i + '" class="form-control" readonly></td>' +
                '<td><input type="number" name="jumlah[]" id="jumlah_' + i + '" class="form-control" oninput="multiply(this)"></td>' +
                '<td>' +
                '<select id="jenis_diskon_' + i + '" name="jenis_diskon[]" class="form-control" onchange="showInputType(' + i + ')">' +
                '<option value="0">Pilih Diskon</option>' +
                '<option value="Nominal">Nominal</option>' +
                '<option value="persen">Persen</option>' +
                '</select>' +
                '<div class="input-group">' +
                '<input type="number" name="diskon[]" id="diskon_' + i + '" value="" class="form-control" style="display: none;" aria-label="Recipients username" aria-describedby="basic-addon3" onchange="calculateTotal(' + i + ')">' +
                '<span class="input-group-text" id="nominalInput_' + i + '" style="display: none;">.00</span>' +
                '<span class="input-group-text" id="persenInput_' + i + '" style="display: none;">%</span>' +
                '</div>' +
                '</div>' +
                '</td>' +
                '<td><input type="number" name="harga_total[]" id="harga_total_' + i + '" class="form-control" readonly></td>' +
                '<td><button type="button" name="pic[]" id="pic_' + i + '" class="btn btn-warning" data-toggle="modal" data-target="#picModal_' + i + '" onclick="copyDataToModal(' + i + ')">PIC Perangkai</button></td>' +
                '<td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">x</button></td>' +
                '</tr>';


            $('#dynamic_field').append(newRow);

            // Menambahkan modal untuk setiap pic
            var picModal = '<div class="modal fade" id="picModal_' + i + '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">' +
                '<div class="modal-dialog" role="document">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '<h5 class="modal-title" id="exampleModalLabel">Form PIC Perangkai ' + i + '</h5>' +
                '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>' +
                '<div class="modal-body">' +
                '<div class="form-group">' +
                '<label for="tglrangkai">Tanggal Rangkaian</label>' +
                '<input type="date" class="form-control" id="tglrangkai" name="tglrangkai">' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="jnsrangkai">Jenis Rangkaian</label>' +
                '<input type="text" class="form-control" id="jnsrangkai" name="jnsrangkai" value="penjualan" readonly>' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="no_invoice">Nomor Invoice</label>' +
                '<input type="text" class="form-control" id="no_invoice" name="no_invoice" placeholder="Nomor Invoice" onchange="generateInvoice(this)" required>' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="staffrangkai">Nama Staff Perangkai</label>' +
                '<select id="staffrangkai" name="staffrangkai" class="form-control">' +
                '<option value="">Pilih Nama Staff Perangkai</option>' +
                '@foreach ($karyawans as $karyawan)' +
                '<option value="{{ $karyawan->id }}">{{ $karyawan->nama }}</option>' +
                '@endforeach' +
                '</select>' +
                '</div>' +
                '<div class="table-responsive">' +
                '<table class="table">' +
                '<thead>' +
                '<tr>' +
                '<th>Nama</th>' +
                '<th>Jumlah</th>' +
                '<th></th>' +
                '</tr>' +
                '</thead>' +
                '<tbody id="dynamic_field">' +
                '<tr>' +
                '<td>' +
                '<select id="nama_produk" name="nama_produk[]" class="form-control">' +
                '<option value="">Pilih Produk</option>' +
                '@foreach ($produks as $produk)' +
                '<option value="{{ $produk->id }}" data-harga="{{ $produk->harga_jual }}">{{ $produk->nama }}</option>' +
                '@endforeach' +
                '</select>' +
                '<input type="hidden" name="kode_produk[]" style="display: none;">' +
                '<input type="hidden" name="tipe_produk[]" style="display: none;">' +
                '<input type="hidden" name="deskripsi_komponen[]" style="display: none;">' +
                '</td>' +
                '<td><input type="number" name="jumlah[]" id="jumlah_0" oninput="multiply($(this))" class="form-control"></td>' +
                '</tr>' +
                '</tbody>' +
                '</table>' +
                '</div>' +
                '</div>' +
                '<div class="modal-footer justify-content-center">' +
                '<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';

            $('body').append(picModal);

            $('#nama_produk_' + i + ', #jenis_diskon_' + i).select2();
            i++
        });

        $(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
            $('#row' + button_id + '').remove();
            calculateTotal(0);
        });

        $('[id^=btnPerangkai]').click(function(e) {
            e.preventDefault();
            var produk_id = $(this).data('produk');
            getDataPerangkai(produk_id);
        });

        function getDataPerangkai(produk_id) {
            var data = {
                produk_id: produk_id,
            };
            $.ajax({
                url: '/getProdukTerjual',
                type: 'GET',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    console.log(response.perangkai, produk_id)
                    $('#prdTerjual').val(response.produk.nama);
                    $('#prdTerjual_id').val(response.id);
                    $('#jml_produk').val(response.jumlah);
                    $('#no_form').val(response.kode_form);
                    $('#jml_perangkai').val(response.perangkai.length);
                    $('[id^="perangkai_id"]').select2()
                    $('[id^="perangkai_id_"]').each(function() {
                        $(this).select2('destroy');
                        $(this).remove();
                    });
                    if (response.perangkai.length > 0) {
                        for (var i = 0; i < response.perangkai.length; i++) {
                            var rowPerangkai =
                                '<select id="perangkai_id_' + i + '" name="perangkai_id[]" class="form-control">' +
                                '<option value="">Pilih Perangkai</option>' +
                                '@foreach ($perangkai as $item)' +
                                '<option value="{{ $item->id }}">{{ $item->nama }}</option>' +
                                '@endforeach' +
                                '</select>';
                            $('#div_perangkai').append(rowPerangkai);
                            $('#div_perangkai select').each(function(index) {
                                $(this).val(response.perangkai[index].perangkai_id);
                            });
                            $('#perangkai_id_' + i).select2();
                        }
                    }
                    $('#modalPerangkai').modal('show');
                },
                error: function(xhr, status, error) {
                    console.log(error)
                }
            });
        }

        $('#jml_perangkai').on('input', function(e) {
            e.preventDefault();
            var jumlah = $(this).val();
            jumlah = parseInt(jumlah) > 10 ? 10 : parseInt(jumlah);
            console.log(jumlah)
            $('[id^="perangkai_id_"]').each(function() {
                $(this).select2('destroy');
                $(this).remove();
            });
            if (jumlah < 1) return 0;
            for (var i = 0; i < jumlah; i++) {
                var rowPerangkai =
                    '<select id="perangkai_id_' + i + '" name="perangkai_id[]" class="form-control">' +
                    '<option value="">Pilih Perangkai</option>' +
                    '@foreach ($perangkai as $item)' +
                    '<option value="{{ $item->id }}">{{ $item->nama }}</option>' +
                    '@endforeach' +
                    '</select>';
                $('#div_perangkai').append(rowPerangkai);
                $('#perangkai_id_' + i).select2();
            }
        })

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


        $('#delivery_order_section').show();

        $('#distribusi').change(function() {
            if ($(this).val() === 'Diambil') {
                $('#delivery_order_section').hide();
            } else {
                $('#delivery_order_section').show();
            }
        });

        $('#pilih_pengiriman').change(function() {
            var pengiriman = $(this).val();

            $('#inputOngkir').hide();
            $('#inputExspedisi').hide();

            if (pengiriman === "sameday") {
                $('#inputOngkir').show();
            } else if (pengiriman === "exspedisi") {
                $('#inputExspedisi').show();
            }
        });

        $('#pilih_pengiriman').trigger('change');

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

        var sisaBayar = {{ $penjualans->sisa_bayar}};
        // console.log(sisaBayar);

        $('#nominal').on('input', function() {
            var inputNominal = $(this).val();

            if (parseInt(inputNominal) > sisaBayar) {
                alert('Nominal pembayaran tidak boleh lebih dari sisa bayar!');
                $(this).val(sisaBayar);
            }
        });

        $('#bukti_file').on('change', function() {
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

        function clearFile() {
            $('#bukti_file').val('');
            $('#preview').attr('src', defaultImg);
        };

        $('#bayar').on('change', function() {
            var caraBayar = $(this).val();
            console.log(caraBayar);
            if (caraBayar == 'cash') {
                $('#rekening').hide();
            } else if (caraBayar == 'transfer') {
                $('#rekening').show();
            }
        });
    });
</script>

@endsection