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
            <form action="{{ route('auditpenjualan.update', ['penjualan' => $penjualans->id]) }}" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm">
                        @csrf
                        @method('patch')
                        <div class="row ">
                            <div class="col-md-6 border rounded pt-3">
                                <h5>Informasi Pelanggan</h5>
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label for="id_customer">Nama Customer</label>
                                            <select id="id_customer" name="id_customer" class="form-control" required>
                                                <option value="">Pilih Nama Customer</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}" data-point="{{ $customer->poin_loyalty }}" data-hp="{{ $customer->handphone }}" {{ $penjualans->id_customer == $customer->id ? 'selected': ''}}>{{ $customer->nama }}</option>
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
                                            <input type="text" class="form-control" id="nohandphone" name="nohandphone" placeholder="Nomor Handphone" value="{{ $customer->handphone }}" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="lokasi_id">Lokasi Pembelian</label>
                                            <select id="lokasi_id" name="lokasi_id" class="form-control" required>
                                                @foreach ($lokasis as $lokasi)
                                                <option value="{{ $penjualans->lokasi_id }}" data-tipe="{{ $penjualans->lokasi->tipe_lokasi}}">{{ $lokasi->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="point">Jumlah Point</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="inputGroupPrepend2">
                                                    <input type="checkbox" id="cek_point" name="btndipakai" {{ $penjualans->btndipakai == 'on' ? 'checked' : '' }}>
                                                </span>
                                                <input type="number" class="form-control" id="point_dipakai" name="point_dipakai" placeholder="0" value="{{ $penjualans->point_dipakai }}" aria-describedby="inputGroupPrepend2" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="distribusi">Distribusi Produk</label>
                                            <select id="distribusi" name="distribusi" class="form-control" required>
                                                <!-- <option value="">Pilih Distribusi Produk</option> -->
                                                <option value="Dikirim" {{ $penjualans->distribusi == 'Dikirim' ? 'selected' : '' }}>Dikirim</option>
                                                <option value="Diambil" {{ $penjualans->distribusi == 'Diambil' ? 'selected' : '' }}>Langsung Diambil</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6 border rounded pt-3 ">
                                <h5>Informasi Invoice</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="no_invoice">Nomor Invoice</label>
                                            <input type="text" class="form-control" id="no_invoice" name="no_invoice" placeholder="Nomor Invoice" value="{{ $penjualans->no_invoice}}" required readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="nama">Tanggal Invoice</label>
                                            <input type="date" class="form-control" id="tanggal_invoice" name="tanggal_invoice" placeholder="Tanggal_Invoice" value="{{ $penjualans->tanggal_invoice}}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select id="status" name="status" class="form-control" required>
                                                <option value="">Pilih Status</option>
                                                <option value="TUNDA" {{$penjualans->status == 'TUNDA' ? 'selected' : ''}}>TUNDA</option>
                                                <option value="DIKONFIRMASI" {{$penjualans->status == 'DIKONFIRMASI' ? 'selected' : ''}}>DIKONFIRMASI</option>
                                                @php
                                                    $user = Auth::user();
                                                @endphp
                                                @if($user->hasRole(['AdminGallery', 'KasirAdmin', 'KasirOutlet']) && $dopenjualan->status != 'DIKONFIRMASI')
                                                    <option value="DIBATALKAN" {{$penjualans->status == 'DIBATALKAN' ? 'selected' : ''}}>DIBATALAKAN</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div id="alasan" style="display: none;">
                                                <label for="alasan">Alasan</label>
                                                <textarea name="alasan" id="alasan"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama">Jatuh Tempo</label>
                                            <input type="date" class="form-control" id="jatuh_tempo" name="jatuh_tempo" placeholder="Tanggal_Jatuh_Tempo" value="{{ $penjualans->jatuh_tempo}}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="employee_id">Nama Sales</label>
                                            <select id="employee_id" name="employee_id" class="form-control" required >
                                                <!-- <option value="">Pilih Nama Sales</option> -->
                                                @foreach ($karyawans as $karyawan)
                                                <option value="{{ $karyawan->id }}">{{ $karyawan->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if($lokasi->tipe_lokasi == 1)
                                            <div class="form-group">
                                                <label for="lokasi_pengirim">Lokasi Pengiriman</label>
                                                <select id="lokasi_pengirim" name="lokasi_pengirim" class="form-control">
                                                    @foreach ($lokasigalery as $galery)
                                                    <option value="{{ $galery->id }}">{{ $galery->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
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
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="dynamic_field">
                                                <!-- Template row for when there are no products -->
                                                @if(count($produks) < 1) 
                                                <tr>
                                                    <td>
                                                        <select id="nama_produk_0" name="nama_produk[]" class="form-control" onchange="updateHargaSatuan(this)">
                                                            <option value="">Pilih Produk</option>
                                                            @foreach ($produkjuals as $produk)
                                                                <option value="{{ $produk->kode }}" data-harga="{{ $produk->harga_jual }}" data-tipe_produk="{{ $produk->tipe_produk }}">
                                                                    {{ $produk->nama }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="harga_satuan[]" id="harga_satuan_0" class="form-control" onchange="calculateTotal(0)"></td>
                                                    <td><input type="number" name="jumlah[]" id="jumlah_0" oninput="multiply($(this))" class="form-control" onchange="calculateTotal(0)" disabled></td>
                                                    <td>
                                                        <select id="jenis_diskon_0" name="jenis_diskon[]" class="form-control" onchange="showInputType(0)">
                                                            <option value="0">Pilih Diskon</option>
                                                            <option value="Nominal">Nominal</option>
                                                            <option value="persen">Persen</option>
                                                        </select>
                                                        <div>
                                                            <div class="input-group">
                                                                <input type="text" name="diskon[]" id="diskon_0" class="form-control" style="display: none;" aria-label="Recipient's username" aria-describedby="basic-addon3" onchange="calculateTotal(0)">
                                                                <span class="input-group-text" id="nominalInput_0" style="display: none;">.00</span>
                                                                <span class="input-group-text" id="persenInput_0" style="display: none;">%</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><input type="text" name="harga_total[]" id="harga_total_0" class="form-control"></td>
                                                </tr>
                                                @endif
                                                <!-- Loop through existing products -->
                                                @php $i = 0; @endphp
                                                @foreach ($produks as $komponen)
                                                <tr id="row{{ $i }}">
                                                    <td>
                                                        <select id="nama_produk_{{ $i }}" name="nama_produk[]" class="form-control" onchange="updateHargaSatuan(this)">
                                                            <option value="">Pilih Produk</option>
                                                            @foreach ($produkjuals as $produk)
                                                                <option 
                                                                    value="{{ $produk->kode }}" 
                                                                    data-harga="{{ $produk->harga_jual }}" 
                                                                    data-tipe_produk="{{ $produk->tipe_produk }}"
                                                                    @if(isset($komponen) && isset($komponen->produk) && $komponen->produk && $komponen->produk->kode == $produk->kode) 
                                                                        selected 
                                                                    @endif
                                                                >
                                                                    {{ $produk->nama }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name="harga_satuan[]" id="harga_satuan_{{ $i }}" class="form-control" value="{{ 'Rp '. number_format($komponen->harga, 0, ',', '.',) }}" onchange="calculateTotal({{ $i }})"></td>
                                                    <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" oninput="multiply($(this))" class="form-control" value="{{ $komponen->jumlah }}" onchange="calculateTotal({{ $i }})"></td>
                                                    <td>
                                                        <select id="jenis_diskon_{{ $i }}" name="jenis_diskon[]" class="form-control" onchange="showInputType({{ $i }})">
                                                            <option value="0">Pilih Diskon</option>
                                                            <option value="Nominal" {{ $komponen->jenis_diskon == 'Nominal' ? 'selected' : ''}}>Nominal</option>
                                                            <option value="persen" {{ $komponen->jenis_diskon == 'persen' ? 'selected' : ''}}>Persen</option>
                                                        </select>
                                                        <div>
                                                            <div class="input-group">
                                                                <input type="text" name="diskon[]" id="diskon_{{ $i }}" value="{{ $komponen->diskon}}" style="display:none;" class="form-control" aria-label="Recipient's username" aria-describedby="basic-addon3" onchange="calculateTotal({{ $i }})">
                                                                <span class="input-group-text" id="nominalInput_{{ $i }}" style="display: none;">.00</span>
                                                                <span class="input-group-text" id="persenInput_{{ $i }}" style="display: none;">%</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><input type="text" name="harga_total[]" id="harga_total_{{ $i }}" class="form-control" value="{{ 'Rp '. number_format($komponen->harga_jual, 0, ',', '.',)}}"></td>
                                                    @if($i == 0)
                                                        <td><button type="button" name="add" id="add" class="btn btn-success btnubah">+</button></td>
                                                    @else
                                                        <td><button type="button" name="remove" id="{{ $i }}" class="btn btn-danger btn_remove btnubah">x</button></td>
                                                    @endif
                                                    @php $i++; @endphp
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-around">
                            <div class="col-md-12 pt-3 me-1 mt-2">
                                <div class="row">
                                    <!-- Payment and Shipping Section -->
                                    <div class="col-lg-8 col-sm-12 border rounded" >
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
                                                        <input type="text" class="form-control" id="nominal" name="nominal" value="" placeholder="Nominal Bayar" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div id="inputBuktiBayar" style="display: none;">
                                                        <label for="buktibayar">Unggah Bukti</label>
                                                        <input type="file" class="form-control" id="bukti" name="bukti" value="{{ $pembayaran->bukti ?? '-'}}">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Shipping Section -->
                                            <div class="col-lg-6">
                                                <div class="form-group" style="display:none;" id="kirimpilih">
                                                    <label>Pengiriman</label>
                                                    <select id="pilih_pengiriman" name="pilih_pengiriman" class="form-control">
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
                                    </div>
                                    <div class="col-lg-4 col-sm-12 border rounded">
                                        <div class="row mt-4">
                                            <div class="form-group">
                                                <div class="form-group">
                                                    <!-- <input type="file" id="bukti_file" name="bukti_file" placeholder="Bukti File Invoice" aria-describedby="inputGroupPrepend2" required disabled> -->
                                                    <div class="custom-file-container" data-upload-id="myFirstImage">
                                                        <label>Bukti Invoice <a href="javascript:void(0)" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"></a>
                                                        </label>
                                                        <label class="custom-file-container__custom-file">
                                                            <input type="file" id="bukti_file" class="custom-file-container__custom-file__custom-file-input" name="file" accept="image/*" >
                                                            <span class="custom-file-container__custom-file__custom-file-control"></span>
                                                        </label>
                                                        <span class="text-danger">max 2mb</span>
                                                        <div class="image-preview">
                                                            <img id="imagePreview" src="{{ $penjualans->bukti_file ? '/storage/' . $penjualans->bukti_file : '' }}" />
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-sm-12 border rounded mt-2">
                                        <!-- Table Section -->
                                        <div class="row mt-4">
                                            <div class="col-lg-12">
                                                <table class="table table-responsive border rounded">
                                                    <thead>
                                                        <tr>
                                                            <th>Pembuat</th>
                                                            <th>Pemeriksa</th>
                                                            <th>Pembuku</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            
                                                            @php
                                                                $user = Auth::user();
                                                            @endphp
                                                            @if($penjualans->status == 'DIKONFIRMASI' && $user->hasRole(['Finance']))
                                                                <td id="pembuat">{{ $penjualans->dibuat[0]->name }}</td>
                                                                <td id="penyetuju" >{{ Auth::user()->name}}</td>
                                                                <td id="pemeriksa" >{{ $penjualans->dibuku->name ?? '-'}}</td>
                                                            @elseif($penjualans->status == 'DIKONFIRMASI' && $user->hasRole(['Auditor']))
                                                                <td id="pembuat">{{ $penjualans->dibuat[0]->name }}</td>
                                                                <td id="penyetuju" >{{ $penjualans->diperiksa->name ?? '-'}}</td>
                                                                <td id="pemeriksa">{{ Auth::user()->name}}</td>
                                                            @elseif($user->hasRole(['AdminGallery', 'KasirAdmin', 'KasirOutlet']))
                                                                <td id="pembuat">{{ $penjualans->dibuat[0]->name }}</td>
                                                                <td id="penyetuju" >-</td>
                                                                <td id="pemeriksa">-</td>
                                                            @endif
                                                        </tr>
                                                        <tr>
                                                            @if($user->hasRole(['AdminGallery', 'KasirAdmin', 'KasirOutlet']))
                                                                <td><input type="date" class="form-control" name="tanggal_dibuat" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"></td>
                                                                <td id="tgl_penyetuju">-</td>
                                                                <td id="tgl_pemeriksa">-</td>
                                                            @elseif($penjualans->status == 'DIKONFIRMASI' && $user->hasRole(['Finance']))
                                                                <td><input type="date" class="form-control" name="tanggal_dibuat"  value="{{ $penjualans->tanggal_dibuat }}" readonly></td>
                                                                <td><input type="date" class="form-control" name="tanggal_dibukukan" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" ></td>
                                                                <td><input type="date" class="form-control" name="tanggal_audit"  value="{{ $penjualans->tanggal_audit ?? '-' }}" readonly></td></td>
                                                            @elseif($penjualans->status == 'DIKONFIRMASI' && $user->hasRole(['Auditor']))
                                                                <td><input type="date" class="form-control" name="tanggal_dibuat"  value="{{ $penjualans->tanggal_dibuat }}" readonly></td>
                                                                <td><input type="date" class="form-control" name="tanggal_dibukukan" value="{{ $penjualans->tanggal_dibukukan ?? '-'}}" readonly></td>
                                                                <td><input type="date" class="form-control" name="tanggal_audit"  value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"></td>
                                                            @endif
                                                        </tr>
                                                        <tr>
                                                            @if($penjualans->status == 'DIKONFIRMASI' && $user->hasRole(['Finance']))
                                                                <td>-</td>
                                                                <td><select name="ubahapa" id="ubahapa" class="form-control">
                                                                    <option value="ubahsemua">Ubah Produk</option>
                                                                    <option value="tidakubah">Tidak Ubah Produk</option>
                                                                </td>
                                                                <td></td></td>
                                                            @elseif($penjualans->status == 'DIKONFIRMASI' && $user->hasRole(['Auditor']))
                                                                <td>-</td>
                                                                <td>-</td>
                                                                <td><select name="ubahapa" id="ubahapa" class="form-control">
                                                                    <option value="ubahsemua">Ubah Produk</option>
                                                                    <option value="tidakubah">Tidak Ubah Produk</option>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="card mt-2">
                                            <div class="card-header">
                                                <h4 class="card-title">Riwayat</h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                <table class="table datanew">
                                                    <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Tanggal Perubahan</th>
                                                        <th>Pengubah</th>
                                                        <th>Log</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach ($riwayat as $item)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $item->created_at ?? '-' }}</td>
                                                            <td>{{ $item->causer->name ?? '-' }}</td>
                                                            <td>
                                                                @php
                                                                    $properties = json_decode($item->properties, true);
                                                                    $changes = $item->changes();
                                                                    
                                                                    if (isset($changes['old'])) {
                                                                        $diff = array_keys(array_diff_assoc($changes['attributes'], $changes['old']));
                                                                        foreach ($diff as $key => $value) {
                                                                            echo "$value: <span class='text-danger'>{$changes['old'][$value]}</span> => <span class='text-success'>{$changes['attributes'][$value]}</span><br>";
                                                                        }
                                                                    } else {
                                                                        echo 'Data Invoice Penjualan Terbuat';
                                                                    }
                                                                @endphp
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                                </div>
                                            </div>
                                        </div> 
                                    </div>

                                    <!-- Summary Section -->
                                    <div class="col-lg-4 col-sm-12">
                                        <div class="total-order mt-4">
                                            <ul>
                                                <li>
                                                    <h4>Sub Total</h4>
                                                    <h5><input type="text" id="sub_total" name="sub_total" class="form-control" value="{{ 'Rp '. number_format($penjualans->sub_total, 0, ',', '.',)}}" readonly required></h5>
                                                </li>
                                                <li>
                                                    <h4><select id="jenis_ppn" name="jenis_ppn" class="form-control" required>
                                                            <option value=""> Pilih Jenis PPN</option>
                                                            <option value="exclude" {{ $penjualans->jenis_ppn = 'exclude' ? 'selected' : ''}}> PPN EXCLUDE</option>
                                                            <option value="include" {{ $penjualans->jenis_ppn = 'include' ? 'selected' : ''}}> PPN INCLUDE</option>
                                                        </select></h4>
                                                        <h5 class="col-lg-5">
                                                            <div class="input-group">
                                                                <input type="text" id="persen_ppn" name="persen_ppn" value="{{ $penjualans->persen_ppn}}" class="form-control" required>
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                            <input type="text" id="jumlah_ppn" name="jumlah_ppn" value="{{ 'Rp '. number_format($penjualans->jumlah_ppn, 0, ',', '.',)}}" class="form-control" readonly required>
                                                        </h5>
                                                </li>
                                                <li>
                                                    <h4>Promo</h4>
                                                    <h5 class="col-lg-5">
                                                        <div class="row align-items-center">
                                                            <div class="col-9 pe-0">
                                                                <select id="promo_id" name="promo_id" class="form-control" value="{{ $penjualans->promo_id}}">
                                                                    @foreach ($promos as $promo)
                                                                    <option value="{{ $promo->id }}">{{ $promo->nama }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-3 ps-0 mb-0">
                                                                <button id="btnCheckPromo" class="btn btn-primary w-100"><i class="fa fa-search" data-bs-toggle="tooltip"></i></button>
                                                            </div>
                                                        </div>
                                                        <input type="text" class="form-control" required name="total_promo" id="total_promo" value="{{ 'Rp '. number_format($penjualans->total_promo, 0, ',', '.',) }}" readonly>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Biaya Ongkir</h4>
                                                    <h5><input type="text" id="biaya_ongkir" name="biaya_ongkir" class="form-control" value="{{ 'Rp '. number_format($penjualans->biaya_ongkir, 0, ',', '.',)}}" required></h5>
                                                </li>
                                                <li>
                                                    <h4>DP</h4>
                                                    <h5><input type="text" id="dp" name="dp" class="form-control" value="{{'Rp '. number_format($penjualans->dp, 0, ',', '.',)}}" required ></h5>
                                                </li>
                                                <li class="total">
                                                    <h4>Total Tagihan</h4>
                                                    <h5><input type="text" id="total_tagihan" name="total_tagihan" class="form-control" value="{{ 'Rp '. number_format($penjualans->total_tagihan, 0, ',', '.',)}}" required></h5>
                                                </li>
                                                <li>
                                                    <h4>Sisa Bayar</h4>
                                                    <h5><input type="text" id="sisa_bayar" name="sisa_bayar" class="form-control" value="{{ 'Rp '. number_format($penjualans->sisa_bayar, 0, ',', '.',)}}"  required></h5>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>

                        <!-- <div class="row justify-content-between">
                            <div class="col-md-12">
                                <label for=""></label>
                                <div class="add-icon text-end">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalBayar">add +</button>
                                </div>
                            </div>
                        </div> -->

                        <!-- <div class="row justify-content-around">
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
                                                            <td>{{ 'Rp '. number_format($pembayaran->nominal, 0, ',', '.',) }}</td>
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
                                                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                                </a>
                                                                    <div class="dropdown-menu">
                                                                        <a class="dropdown-item" href="{{ route('pembayaran.edit', ['pembayaran' => $pembayaran->id]) }}"><img src="assets/img/icons/edit-6.svg" class="me-2" alt="img">Edit</a>
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
                            </div> -->

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
                        <select class="form-control" id="rekening_id" name="rekening_id">
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

<div class="modal fade" id="modalPerangkai" tabindex="-1" aria-labelledby="modalPerangkaiLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPerangkaiLabel">Atur Perangkai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="form_perangkai" action="{{ route('formpenjualan.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="route" value="{{ request()->route()->getName() }},penjualan,{{ request()->route()->parameter('penjualan') }}">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-sm-8">
                                <label for="prdTerjual" class="col-form-label">Produk</label>
                                <input type="text" class="form-control" name="produk_id" id="prdTerjual" readonly required>
                            </div>
                            <input type="hidden" id="selectedstatus" name="status">
                            <input type="hidden" name="lokasi_id" id="lokasi_id" value="{{ $penjualans->lokasi_id }}">
                            <input type="hidden" name="distribusi" id="distribusi" value="{{ $penjualans->distribusi }}">
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
                        <input type="date" class="form-control" name="tanggal" id="add_tanggal" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="jml_perangkai" class="col-form-label">Jumlah Perangkai</label>
                        <input type="number" class="form-control" name="jml_perangkai" id="jml_perangkai" required>
                    </div>
                    <div class="mb-3">
                        <label for="perangkai_id" class="col-form-label">Perangkai</label>
                        <div id="div_perangkai" class="form-group">
                            <select id="perangkai_id_0" name="perangkai_id[]" class="form-control" required>
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
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalGift" tabindex="-1" aria-labelledby="modalGiftLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalGiftLabel">Atur Komponen Gift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="form_gift" action="{{ route('komponenpenjulan.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="route" value="{{ request()->route()->getName() }},penjualan,{{ request()->route()->parameter('penjualan') }}">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-sm-8">
                                <label for="prdTerjualGift" class="col-form-label">Produk</label>
                                <input type="text" class="form-control" name="produk_id" id="prdTerjualGift" readonly required>
                            </div>
                            <input type="hidden" name="prdTerjual_id" id="prdTerjualGift_id" value="">
                            <div class="col-sm-4">
                                <label for="jmlGift_produk" class="col-form-label">Jumlah</label>
                                <input type="number" class="form-control" name="jml_produk" id="jmlGift_produk" readonly required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="jml_komponen" class="col-form-label">Jumlah Bunga/POT</label>
                        <input type="number" class="form-control" name="jml_komponen" id="jml_komponen" required>
                    </div>
                    <div class="mb-3">
                        <label for="komponen_id" class="col-form-label">Bunga/POT</label>
                        <div id="div_komponen" class="form-group">
                            <div id="div_produk_jumlah_0" class="row">
                                <div class="col-sm-4">
                                    <select id="komponen_id_0" name="komponen_id[]" class="form-control" required>
                                        <option value="">Pilih Bunga/POT</option>
                                        @foreach ($produkKomponens as $itemkomponen)
                                        <option value="{{ $itemkomponen->id }}">{{ $itemkomponen->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <select id="kondisi_id_0" name="kondisi_id[]" class="form-control" required>
                                        <option value="">kondisi</option>
                                        @foreach ($kondisis as $kondisi)
                                        <option value="{{ $kondisi->id }}">{{ $kondisi->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <input type="number" class="form-control" name="jumlahproduk_id[]" id="jumlahproduk_id_0">
                                </div>
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
    var cekInvoiceNumbers = "0";
    // console.log(cekInvoiceNumbers);
    var ceklokasi = "<?php echo $ceklokasi ?>";
    var nextInvoiceNumber = parseInt(cekInvoiceNumbers) + 1;

    function generateInvoice(kode) {
        var currentDate = new Date();
        var year = currentDate.getFullYear();
        var month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
        var day = currentDate.getDate().toString().padStart(2, '0');
        var formattedNextInvoiceNumber = nextInvoiceNumber.toString().padStart(3, '0');

        var generatedInvoice = kode + year + month + day + formattedNextInvoiceNumber;
        $('#no_invoice_byr').val(generatedInvoice);
    }

    var kode;
    // console.log(ceklokasi);
    if (ceklokasi == 1) {
        kode = "BYR";
    } else if (ceklokasi == 2) {
        kode = "BOT";
    } else {
        kode = "";
    }

    generateInvoice(kode);

</script>
<script>
    function updateDate(element) {
        var today = new Date().toISOString().split('T')[0];
        element.value = today;
    }
    updateDate(document.getElementById('tanggal_bayar'));
</script>

<script>
    function formatRupiah(angka, prefix) {
        var numberString = angka.toString().replace(/[^,\d]/g, ''),
            split = numberString.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            var separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix === undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
    }

    function parseRupiahToNumber(rupiah) {
        return parseInt(rupiah.replace(/[^\d]/g, ''));
    }
    
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

    document.addEventListener("DOMContentLoaded", function() {
        var elements = document.querySelectorAll("[id^='jenis_diskon_']");
        elements.forEach(function(element) {
            var index = element.id.split("_")[2]; // Extract the index from the ID
            showInputType(index);
        });
    });

    function calculateTotal(index) {
        var diskonType = $('#jenis_diskon_' + index).val();
        var diskonValue = parseFloat($('#diskon_' + index).val());
        var jumlah = parseFloat($('#jumlah_' + index).val());
        var hargaSatuan = parseFloat(parseRupiahToNumber($('#harga_satuan_' + index).val())); // Mengonversi hargaSatuan ke angka
        var hargaTotal = 0;

        if (!isNaN(jumlah) && !isNaN(hargaSatuan)) {
            hargaTotal = jumlah * hargaSatuan;
        }

        if (!isNaN(hargaTotal)) {
            if (diskonType === "Nominal" && !isNaN(diskonValue)) {
                hargaTotal -= diskonValue;
                if(hargaTotal <= 0){
                    hargaTotal = 0;
                    alert('nominal tidak boleh melebihi harga total');
                    diskonValue = 0;
                }
                $('#diskon_' + index).val(formatRupiah(diskonValue, 'Rp '));
            } else if (diskonType === "persen" && !isNaN(diskonValue)) {
                var diskonPersen = (hargaTotal * diskonValue / 100); 
                hargaTotal -= diskonPersen; 
                if(hargaTotal <= 0){
                    hargaTotal = 0;
                    alert('diskon tidak boleh melebihi harga total');
                    diskonPersen = 0;
                }
            }
        }

        // Set nilai input harga total dengan format Rupiah
        $('#harga_total_' + index).val(formatRupiah(hargaTotal, 'Rp '));

        // Hitung ulang subtotal
        var subtotal = 0;
        $('input[name="harga_total[]"]').each(function() {
            // Mengonversi format Rupiah menjadi nilai numerik
            var harga_total = parseRupiahToNumber($(this).val());
            subtotal += harga_total || 0;
        });

        // Format subtotal kembali ke format Rupiah sebelum menetapkannya ke input
        $('#sub_total').val(formatRupiah(subtotal, 'Rp '));
        $('#jenis_ppn').trigger('change');
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
        var i = {{count($produks)}};
        $('#add').click(function() {
            var newRow = `<tr class="tr_clone" id="row${i}">
                <td>
                    <select id="nama_produk_${i}" name="nama_produk[]" class="form-control select2">
                        <option value="">Pilih Produk</option>
                        @foreach ($produkjuals as $index => $produk)
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
                <td><input type="text" name="harga_satuan[]" id="harga_satuan_${i}" onchange="calculateTotal(0)" class="form-control" readonly></td>
                <td><input type="number" name="jumlah[]" id="jumlah_${i}" class="form-control" oninput="multiply(this)"></td>
                <td>
                    <select id="jenis_diskon_${i}" name="jenis_diskon[]" class="form-control" onchange="showInputType(${i})">
                        <option value="0">Pilih Diskon</option>
                        <option value="Nominal">Nominal</option>
                        <option value="persen">Persen</option>
                    </select>
                    <div class="input-group">
                        <input type="text" name="diskon[]" id="diskon_${i}" value="" class="form-control" style="display: none;" aria-label="Recipients username" aria-describedby="basic-addon3" onchange="calculateTotal(${i})">
                        <span class="input-group-text" id="nominalInput_${i}" style="display: none;">.00</span>
                        <span class="input-group-text" id="persenInput_${i}" style="display: none;">%</span>
                    </div>
                </td>
                <td><input type="text" name="harga_total[]" id="harga_total_${i}" class="form-control" readonly></td>
                <td><button type="button" name="remove" id="${i}" class="btn btn-danger btn_remove">x</button></td>
            </tr>`;

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

        function togglePaymentFields() {
            var pembayaran = $('#cara_bayar').val();

            $('#inputCash').hide();
            $('#inputTransfer').hide();

            if (pembayaran === "cash") {
                $('#inputCash').show();
            } else if (pembayaran === "transfer") {
                $('#inputTransfer').show();
            }
        }

        // Add change event listener
        $('#cara_bayar').change(function() {
            togglePaymentFields();
        });

        // Initial value from backend
        var initialPembayaran = "{{ $penjualans->cara_bayar }}";

        // Set the initial value in the dropdown and trigger the change event
        if (initialPembayaran) {
            $('#cara_bayar').val(initialPembayaran).trigger('change');
        }


        $('[id^=btnPerangkai]').click(function(e) {
            e.preventDefault();
            var produk_id = $(this).data('produk');
            getDataPerangkai(produk_id);
        });

        $('[id^=btnGift]').click(function(e) {
            e.preventDefault();
            var produk_id = $(this).data('produk_gift');
            // console.log(produk_id);
            getDataGift(produk_id);
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
                    // console.log(data);
                    // console.log(response.perangkai, produk_id)
                    $('#prdTerjual').val(response.produk.nama);
                    $('#prdTerjual_id').val(response.id);
                    $('#jml_produk').val(response.jumlah);
                    $('#no_form').val(response.kode_form);
                    $('#jml_perangkai').val(response.perangkai.length);
                    $('[id^="perangkai_id"]').select2()
                    $('[id^="perangkai_id_"]').each(function() {
                        $(this).select2().select2('destroy');
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

        function getDataGift(produk_id) {
            var data = {
                produk_id: produk_id,
            };
            // console.log(data);
            $.ajax({
                url: '/getProdukTerjual',
                type: 'GET',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    // console.log(response.produk.nama)
                    $('#prdTerjualGift').val(response.produk.nama);
                    $('#prdTerjualGift_id').val(response.id);
                    // console.log(response.id);
                    $('#jmlGift_produk').val(response.jumlah);
                    $('[id^="komponen_id"]').select2()
                    $('[id^="div_produk_jumlah_"]').each(function() {
                        $(this).remove();
                    });
                    
                    $('[id^="jumlahproduk_id_"]').remove();
                    // console.log(response);
                    var pot_bunga = 0
                    if(response.komponen.length > 0){
                        for(var i = 0; i < response.komponen.length; i++){
                            if(response.komponen[i].tipe_produk == 1 || response.komponen[i].tipe_produk == 2){
                                pot_bunga++;
                                    var rowPerangkai =
                                    '<div id="div_produk_jumlah_'+i+'" class="row">' +
                                    '<div class="col-sm-4">' +
                                    '<select id="komponen_id_' + i + '" name="komponen_id[]" class="form-control">' +
                                    '<option value="">Pilih Bunga/POT</option>' +
                                    '@foreach ($produkKomponens as $itemkomponen)' +
                                    '<option value="{{ $itemkomponen->id }}">{{ $itemkomponen->nama }}</option>' +
                                    '@endforeach' +
                                    '</select>' +
                                    '</div>' +
                                    '<div class="col-sm-4">' +
                                    '<select id="kondisi_id_' + i + '" name="kondisi_id[]" class="form-control" required>' +
                                    '<option value="">kondi i</option>' +
                                    '@foreach ($kondisis as $kondisi)' +
                                    '<option value="{{ $kondisi->id }}">{{ $kondisi->nama }}</option>' +
                                    '@endforeach' +
                                    '</select>' +
                                    '</div>' +
                                    '<div class="col-sm-4">' +
                                    '<input type="number" class="form-control" id="jumlahproduk_id_'+ i +'" name="jumlahproduk[]">' +
                                    '</div>' +
                                    '</div>';
                                    $('#div_komponen').append(rowPerangkai);
                                    $('#kondisi_id_' + i).val(response.komponen[i].kondisi);
                                    $('#jumlahproduk_id_' + i).val(response.komponen[i].jumlah);
                                    $('#komponen_id_' + i).val(response.komponen[i].produk.id);
                                    $('#komponen_id_' + i).select2();
                                    $('#kondisi_id_' + i).select2();
                                }
                            }
                    }
                    $('#jml_komponen').val(pot_bunga);
                    $('#modalGift').modal('show');
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
        });

        $('#jml_komponen').on('input', function(e) {
            e.preventDefault();
            var jumlah = $(this).val();
            jumlah = parseInt(jumlah) > 10 ? 10 : parseInt(jumlah);
            console.log(jumlah)
            $('[id^="komponen_id_"]').each(function() {
                $(this).select2('destroy');
                $(this).remove();
            });
            $('[id^="kondisi_id_"]').each(function() {
                $(this).select2('destroy');
                $(this).remove();
            });
            $('[id^="jumlahproduk_id_"]').remove();
            if (jumlah < 1) return 0;
            for (var i = 0; i < jumlah; i++) {
                var rowPerangkai =
                    '<div class="row">' +
                    '<div class="col-sm-4">' +
                    '<select id="komponen_id_' + i + '" name="komponen_id[]" class="form-control">' +
                    '<option value="">Pilih Bunga/POT</option>' +
                    '@foreach ($produkKomponens as $itemkomponen)' +
                    '<option value="{{ $itemkomponen->id }}">{{ $itemkomponen->nama }}</option>' +
                    '@endforeach' +
                    '</select>' +
                    '</div>' +
                    '<div class="col-sm-4">' +
                    '<select id="kondisi_id_' + i + '" name="kondisi_id[]" class="form-control" required>' +
                    '<option value="">kondisi</option>' +
                    '@foreach ($kondisis as $kondisi)' +
                    '<option value="{{ $kondisi->id }}">{{ $kondisi->nama }}</option>' +
                    '@endforeach' +
                    '</select>' +
                    '</div>' +
                    '<div class="col-sm-4">' +
                    '<input type="number" class="form-control" id="jumlahproduk_id_'+ i +'" name="jumlahproduk[]">' +
                    '</div>' +
                    '</div>';
                $('#div_komponen').append(rowPerangkai);
                $('#komponen_id_' + i).select2();
                $('#kondisi_id_' + i).select2();
            }
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


<script>
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $(document).ready(function() {

        $(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
            $('#row' + button_id + '').remove();
            calculateTotal(0);
            updateIndicesProduk();
        });

        function updateIndicesProduk() {
            var i = 0;
            $('tr[id^="row"]').each(function() {
                $(this).attr('id', 'row' + i);

                $(this).find('[id^="nama_produk_"]').attr('id', 'nama_produk_' + i).attr('name', 'nama_produk[]').attr('data-index', i);
                $(this).find('[id^="harga_satuan_"]').attr('id', 'harga_satuan_' + i).attr('name', 'harga_satuan[]').attr('data-index', i);
                $(this).find('[id^="jumlah_"]').attr('id', 'jumlah_' + i).attr('name', 'jumlah[]').attr('data-index', i);
                $(this).find('[id^="jenis_diskon_"]').attr('id', 'jenis_diskon_' + i).attr('name', 'jenis_diskon[]').attr('data-index', i);
                $(this).find('[id^="diskon_"]').attr('id', 'diskon_' + i).attr('name', 'diskon[]').attr('data-index', i);
                $(this).find('[id^="harga_total_"]').attr('id', 'harga_total_' + i).attr('name', 'harga_total[]').attr('data-index', i);
                $(this).find('[id^="nominalInput_"]').attr('id', 'nominalInput_' + i);
                $(this).find('[id^="persenInput_"]').attr('id', 'persenInput_' + i);
                $(this).find('.btn_remove').attr('id', i);

                i++;
            });
        }

        function addModal() {
            let i = $('.modal').length;
        }
        $('#pic_0').on('click', function() {
            addModal();
        });

        $(document).on('change', '[id^=nama_produk]', function() {
            var id = $(this).attr('id').split('_')[2];
            var selectedOption = $(this).find(':selected');
            var selectedValue = $(this).val();

            // Menggunakan JSON.stringify untuk mengonversi variabel PHP $produks menjadi string JSON
            var selectedProduk = {!! json_encode($produks) !!}.find(produk => produk.kode === selectedValue);

            var kode = selectedValue.substring(0, 3);
            if (selectedProduk && selectedProduk.komponen && kode === 'GFT') {
                // Sembunyikan semua baris komponen
                $('[id^="komponen_row_"]').hide();
                $('[id^=add_produk_' + id + ']').show(); // Menampilkan tombol tambah produk komponen
                $('[id^=jumlah_' + id + ']').val(0);
                multiply($('[id^=jumlah_' + id + ']')); 

            } else {
                // Sembunyikan semua baris komponen jika tidak ada komponen yang sesuai
                $('[id^=add_produk_' + id + ']').hide();
                $('[id^="komponen_row_"]').hide();
                $('[id^=jumlah_' + id + ']').val(0);
                multiply($('[id^=jumlah_' + id + ']')); 
            }

            // Menetapkan nilai data pada elemen HTML
            var kodeProduk = selectedOption.data('kode');
            var tipeProduk = selectedOption.data('tipe_produk');
            var deskripsiProduk = selectedOption.data('deskripsi');
            // console.log(kodeProduk);
            $('#kode_produk_' + id).val(kodeProduk);
            $('#tipe_produk_' + id).val(tipeProduk);
            $('#deskripsi_komponen_' + id).val(deskripsiProduk);

            // Memanggil fungsi updateHargaSatuan
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

        $('#btnCheckPromo').click(function(e) {
            e.preventDefault();
            var total_transaksi = parseRupiahToNumber($('#total_tagihan').val()); 
            var produk = [];
            var tipe_produk = [];
            // console.log(total_transaksi);

            $('select[id^="nama_produk_"]').each(function() {
                produk.push($(this).val());
                tipe_produk.push($(this).select2().find(":selected").data("tipe_produk"));
            });
            
            $(this).html('<span class="spinner-border spinner-border-sm me-2"></span>');
            checkPromo(total_transaksi, tipe_produk, produk);
        });

        $('#ubahapa').change(function () {
            var ubahapa = $(this).val();

            if (ubahapa == 'ubahsemua') {
                $('.btnubah').show();
                $('[id^=nama_produk]').prop('disabled', false);
                $('[id^=harga_satuan]').prop('readonly', false);
                $('[id^=jumlah]').prop('readonly', false);
                $('[id^=jenis_diskon]').prop('disabled', false);
                $('[id^=diskon]').prop('readonly', false);
                $('[id^=harga_total]').prop('readonly', false);
            }else{
                $('[id^=nama_produk]').prop('disabled', true);
                $('[id^=harga_satuan]').prop('readonly', true);
                $('[id^=jumlah]').prop('readonly', true);
                $('[id^=jenis_diskon]').prop('disabled', true);
                $('[id^=diskon]').prop('readonly', true);
                $('[id^=harga_total]').prop('readonly', true);
                $('.btnubah').hide();
            }
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

        function formatRupiah(angka, prefix) {
            var numberString = angka.toString().replace(/[^,\d]/g, ''),
                split = numberString.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                var separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix === undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
        }

        function parseRupiahToNumber(rupiah) {
            return parseInt(rupiah.replace(/[^\d]/g, ''));
        }

        $('#distribusi').change(function() {
            var kirim = $(this).val();

            if (kirim === 'Diambil') {
                $('#kirimpilih').hide();
                $('#biaya_ongkir').val(0);
            }else if(kirim === 'Dikirim'){
                $('#kirimpilih').show();
            }
        });

        $('#distribusi').on('change');

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

        $('#status').change(function(){
            var status = $(this).val();

            if(status == "DIBATALKAN")
            {
                $('#alasan').show();
            }else{
                $('#alasan').hide();
            }
        });

        $('#ongkir_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var ongkirValue = parseFloat(selectedOption.data('biaya_ongkir')) || 0;
            $('#biaya_ongkir').val(formatRupiah(ongkirValue, 'Rp '));
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
                $('#persen_ppn').prop('readonly', false);
            } else if (ppn === "exclude") {
                $('#persen_ppn').prop('readonly', false);
                $('#persen_ppn').val(11);
                $('#jumlah_ppn').val(hitungppn);
            }
            Totaltagihan();
        });

        function dpchange(){
            var inputNominal = $('#dp').val();
            var dpValue = parseRupiahToNumber(inputNominal);

            if (dpValue > 0) {
                $('#inputPembayaran').show();
                $('#inputRekening').show();
                $('#inputTanggalBayar').show();
                $('#inputBuktiBayar').show();
                $('#nominal').val(formatRupiah(dpValue, 'Rp '));
            } else {
                $('#inputPembayaran').hide();
                $('#inputRekening').hide();
                $('#inputTanggalBayar').hide();
                $('#inputBuktiBayar').hide();
            }

            // Update input value to formatted Rupiah
            $(this).val(formatRupiah(dpValue, 'Rp '));
        };

        $('#dp').on('change', dpchange());
        $('#dp').on('input', dpchange);

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
            var hargaSatuanInput = $('#harga_satuan_' + select.id.split('_')[2]);
            var selectedOption = $(select).find('option').eq(index);
            var hargaProduk = selectedOption.data('harga');
            var formattedHarga = formatRupiah(hargaProduk, 'Rp ');
            hargaSatuanInput.val(formattedHarga);
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
            harga_satuan = parseRupiahToNumber(harga_satuan);
            // console.log(harga_satuan);
            if (jumlah && harga_satuan) {
                var harga_total = harga_satuan * jumlah;
                // Mengatur nilai input untuk harga total dengan format Rupiah
                $('#harga_total_' + id).val(formatRupiah(harga_total, 'Rp '));

                var inputs = $('input[name="harga_total[]"]');
                var total = 0;
                inputs.each(function() {
                    // Mengonversi format Rupiah menjadi nilai numerik
                    var harga_total = parseRupiahToNumber($(this).val());
                    total += harga_total || 0;
                });

                // Format total kembali ke format Rupiah sebelum menetapkannya ke input
                var formatted_total = formatRupiah(total, 'Rp ');
                $('#harga').val(formatted_total);
                $('#sub_total').val(formatted_total);
                $('#total_tagihan').val(formatted_total);
            }
        }



        function updateSubTotal() {
            var subTotalInput = $('#sub_total');
            var hargaTotalInputs = $('input[name="harga_total[]"]');
            var subTotal = 0;

            hargaTotalInputs.each(function() {
                subTotal += parseFloat($(this).val()) || 0;
            });

            // subTotalInput.val(subTotal.toFixed(2));
            var formatted_total = formatRupiah(subTotal, 'Rp ');
            subTotalInput.val(formatted_total);
        }

        $('#bukti_file').on('change', function() {
            const file = $(this)[0].files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(event.target.files[0]);
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
                    $('#total_promo').val(formatRupiah(total_promo, 'Rp '));
                    Totaltagihan();
                },
                error: function(xhr, status, error) {
                    console.log(error)
                }
            });
        }

        function Totaltagihan() {
            var subtotal = parseRupiahToNumber($('#sub_total').val()) || 0;
            // var extot = parseFloat($('#jumlah_ppn').val()) || 0;
            var persenPPN = parseRupiahToNumber($('#persen_ppn').val()) || 0;
            var dp = parseRupiahToNumber($('#dp').val()) || 0;
            var biayaOngkir = parseRupiahToNumber($('#biaya_ongkir').val()) || 0;
            var diskon_nominal = parseRupiahToNumber($('#total_promo').val()) || 0;
            // console.log(extot);
            var promo = subtotal - diskon_nominal;
            var ppn = persenPPN * promo / 100;
            var totalTagihan = promo + ppn + biayaOngkir - dp;
            var sisaBayar = totalTagihan - dp;

            $('#total_tagihan').val(formatRupiah(totalTagihan, 'Rp '));
            $('#sisa_bayar').val(formatRupiah(sisaBayar, 'Rp '));
            $('#jumlah_ppn').val(formatRupiah(ppn, 'Rp '));
        }


        $('#sub_total, #jumlah_ppn, #dp, #biaya_ongkir, #total_promo, #persen_ppn').on('input', Totaltagihan);

        $('form').on('submit', function(e) {
            // Parse semua nilai input yang diformat Rupiah ke angka numerik
            $('#sub_total').val(parseRupiahToNumber($('#sub_total').val()));
            $('#persen_ppn').val(parseRupiahToNumber($('#persen_ppn').val()));
            $('#dp').val(parseRupiahToNumber($('#dp').val()));
            $('#biaya_ongkir').val(parseRupiahToNumber($('#biaya_ongkir').val()));
            $('#total_promo').val(parseRupiahToNumber($('#total_promo').val()));
            $('#total_tagihan').val(parseRupiahToNumber($('#total_tagihan').val()));
            $('#sisa_bayar').val(parseRupiahToNumber($('#sisa_bayar').val()));
            $('#jumlah_ppn').val(parseRupiahToNumber($('#jumlah_ppn').val()));
            $('#nominal').val(parseRupiahToNumber($('#dp').val()));

            $('input[id^="harga_satuan_"], input[id^="diskon_"], input[id^="harga_total_"]').each(function() {
                var id = $(this).attr('id').split('_')[2];
                var value = $(this).val();
                var hargaRupiah = $(this).val();
                $(this).val(parseRupiahToNumber(hargaRupiah));
            });

        });
    });
</script>

@endsection