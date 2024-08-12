@extends('layouts.app-von')

@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Retur Penjualan</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.html">Penjualan</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="index.html">Invoice Penjualan</a>
                </li>
                <li class="breadcrumb-item active">
                    DO Penjualan
                </li>
                <li class="breadcrumb-item active">
                    Retur Penjualan
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">
                Retur Penjualan
            </h4>
        </div>
        <div class="card-body">
            <!-- <form action="{{ route('returpenjualan.store', ['penjualan' => $penjualans->id]) }}" method="POST" enctype="multipart/form-data"> -->
                <div class="row">
                    <div class="col-sm">
                        <!-- @csrf -->

                        <div class="row">
                            <div class="col-md-6 border rounded pt-3">
                                <h5>Informasi Pelanggan</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="no_retur">No Retur Penjualan</label>
                                            <input type="text" class="form-control" id="no_retur" name="no_retur" placeholder="Nomor Delivery Order" value="{{ $penjualans->no_retur}}" readonly required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                        <label for="customer_id">Nama Customer</label>
                                            <select id="customer_id" name="customer_id" class="form-control" disabled>
                                                <option value=""> Pilih Nama Customer </option>
                                                @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}" {{ $customer->id == $penjualans->customer_id ? 'selected' : ''}}>{{ $customer->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="lokasi_id">Lokasi</label>
                                            <select id="lokasi_id" name="lokasi_id" class="form-control" required disabled>
                                                <option value=""> Pilih Lokasi </option>
                                                @foreach ($lokasis as $lokasi)
                                                <option value="{{ $lokasi->id }}" {{ $lokasi->id == $penjualans->lokasi_id ? 'selected' : ''}}>{{ $lokasi->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group" style="display:none;" id="penerima" disabled>
                                            <label for="penerima">Nama Penerima</label>
                                            <input type="text" class="form-control" placeholder="Nama Penerima" name="penerima" id="penerima" value="{{ $penjualans->deliveryOrder->first()->penerima ?? '-'}}" readonly>
                                        </div>
                                        <div class="form-group" style="display:none;" id="tanggalkirim" disabled>
                                            <label for="tanggal_kirim">Tanggal Kirim</label>
                                            <input type="date" class="form-control" placeholder="Tanggal Kirim" id="tanggal_kirim" name="tanggal_kirim" value="{{$penjualans->deliveryOrder->first()->tanggal_kirim ?? '-'}}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                        <label for="supplier_id">Supplier</label>
                                            <select id="supplier_id" name="supplier_id" class="form-control" disabled>
                                                <option value=""> Pilih Nama Supplier </option>
                                                @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ $supplier->id == $penjualans->supplier_id ? 'selected' : ''}}>{{ $supplier->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-file-container" data-upload-id="myFirstImage">
                                                <label>Bukti Retur <a href="javascript:void(0)" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"></a>
                                                </label>
                                                <label class="custom-file-container__custom-file">
                                                    <input type="file" id="bukti" class="custom-file-container__custom-file__custom-file-input" name="bukti" accept="image/*">
                                                    <span class="custom-file-container__custom-file__custom-file-control"></span>
                                                </label>
                                                <span class="text-danger">max 2mb</span>
                                                <img id="preview" src="{{ $penjualans->bukti ? '/storage/' . $penjualans->bukti : '' }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6 border rounded pt-3">
                                <h5>Informasi Komplain</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal_invoice">Tanggal Invoice</label>
                                            <input type="date" class="form-control" id="tanggal_invoice" name="tanggal_invoice" placeholder="Nomor Invoice" value="{{ $penjualans->tanggal_invoice}}" required disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="tanggal_retur">Tanggal Retur</label>
                                            <input type="date" class="form-control" id="tanggal_retur" name="tanggal_retur" placeholder="Tanggal_retur" value="{{ $penjualans->tanggal_retur}}" required disabled>
                                        </div>
                                        <div class="form-group">
                                        <label for="komplain">Komplain</label>
                                            <select id="komplain" name="komplain" class="form-control" disabled>
                                                <option value=""> Pilih Komplain </option>
                                                <option value="refund" {{ $penjualans->komplain == 'refund' ? 'selected' : ''}}>Refund</option>
                                                <option value="diskon" {{ $penjualans->komplain == 'diskon' ? 'selected' : ''}}>Diskon</option>
                                                <option value="retur" {{ $penjualans->komplain == 'retur' ? 'selected' : ''}}>Retur</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select id="status" name="status" class="form-control" required disabled>
                                                <option value="">Pilih Status</option>
                                                <option value="TUNDA" {{ $penjualans->status == 'TUNDA' ? 'seledcted' : ''}}>TUNDA</option>
                                                <option value="DIKONFIRMASI" {{ $penjualans->status == 'DIKONFIRMASI' ? 'selected':''}}>DIKONFIRMASI</option>
                                                <option value="DIBATALKAN" {{$penjualans->status == 'DIBATALKAN' ? 'selected' : ''}}>DIBATALKAN</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div id="alasan" style="display: none;">
                                                <label for="alasan">Alasan</label>
                                                <textarea name="alasan_batal" id="alasan" disabled>{{ $penjualans->alasan_batal}}</textarea>
                                            </div>
                                        </div>
                                        @if(!empty($dopenjualans))
                                        <div class="form-group" style="display:none;" id="driver">
                                            <label for="driver">Driver</label>
                                            <select id="driver" name="driver" class="form-control" disabled>
                                                <option value=""> Pilih Driver </option>
                                                @foreach ($drivers as $driver)
                                                <option value="{{ $driver->id }}" {{$driver->id == $dopenjualans->driver  ? 'selected' : ''}}>{{ $driver->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @endif
                                        <div class="form-group" style="display:none;" id="alamat">
                                            <label for="alamat">Alamat Pengiriman</label>
                                            <textarea id="alamat" name="alamat" value="" disabled>{{ $dopenjualans->alamat ?? '-'}}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="no_invoice">No Invoice</label>
                                            <input type="text" class="form-control" id="no_invoice" name="no_invoice" placeholder="Nomor Invoice" value="{{ $penjualans->no_invoice}}" required readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="no_do">No Delivery Order</label>
                                            <input type="text" class="form-control" id="no_do" name="no_do" placeholder="Nomor Invoice" value="{{ $penjualans->no_do}}" required readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="catatan_komplain">Catatan</label>
                                            <textarea id="catatan_komplain" name="catatan_komplain" value="{{ $penjualans->catatan_komplain}}" disabled>{{ $penjualans->catatan_komplain}}</textarea>
                                        </div>
                                        @if(!empty($dopenjualans))
                                        <div class="form-group" style="display:none;" id="bukti_kirim">
                                            <div class="custom-file-container" data-upload-id="mySecondImage">
                                                <label>Bukti Kirim <a href="javascript:void(0)" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"></a>
                                                </label>
                                                <label class="custom-file-container__custom-file">
                                                    <input type="file" id="bukti_kirim" class="custom-file-container__custom-file__custom-file-input_2" name="file" accept="image/*">
                                                    <span class="custom-file-container__custom-file__custom-file-control_2"></span>
                                                </label>
                                                <span class="text-danger">max 2mb</span>
                                                <img id="preview_kirim" src="{{ $dopenjualans->file ? '/storage/' . $dopenjualans->file : '' }}" />
                                            </div>
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
                                        <h5>Produk Komplain</h5>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Nama Produk</th>
                                                    <th>Nama Komponen</th>
                                                    <th>Kondisi Komponen</th>
                                                    <th>Jumlah Komponen</th>
                                                    <th>Jumlah produk Jual</th>
                                                    <th>Alasan</th>
                                                    <th>Diskon</th>
                                                    <th>Harga</th>
                                                    <th>Total Harga</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="dynamic_field">
                                            @if(count($penjualans->produk_retur) > 0)
                                                    @php
                                                    $i = 0;
                                                    @endphp
                                                    @foreach ($penjualans->produk_retur as $produk)
                                                    @if($produk->jenis == 'RETUR')
                                                    <tr id="row{{ $i }}">
                                                    <td>
                                                    @php
                                                        $isTRDSelected = false;
                                                    @endphp
                                                        <select id="nama_produk_{{ $i }}" name="nama_produk[]" class="form-control pilih-produk" data-index="{{ $i }}" required readonly>
                                                        <option value="">Pilih Produk</option>
                                                        @php
                                                            $isTRDSelected = false; // Reset the variable each time the loop starts
                                                            $selectedTRDKode = ''; // Initialize the selected TRD product code
                                                            $selectedGFTKode = ''; // Initialize the selected GFT product code
                                                        @endphp
                                                        @foreach ($produkjuals as $index => $pj)
                                                            @php
                                                            if($pj->produk && $produk->produk->kode){
                                                                $isSelectedTRD = ($pj->produk->kode == $produk->produk->kode && substr($pj->produk->kode, 0, 3) === 'TRD' && $pj->no_retur ==  $produk->no_retur && $pj->jenis != 'GANTI');
                                                                $isSelectedGFT = ($pj->produk->kode == $produk->produk->kode && substr($pj->produk->kode, 0, 3) === 'GFT' && $pj->no_retur ==  $produk->no_retur && $pj->jenis != 'GANTI');
                                                                if($isSelectedTRD) {
                                                                    $isTRDSelected = true;
                                                                    // Reset selected TRD code
                                                                    $selectedTRDKode = '';
                                                                    foreach ($pj->komponen as $komponen) {
                                                                        if ($komponen->kondisi) {
                                                                            foreach($kondisis as $kondisi) {
                                                                                if($kondisi->id == $komponen->kondisi) {
                                                                                    // Set selected TRD code based on condition
                                                                                    $selectedTRDKode = $kondisi->nama;
                                                                                    $selectedTRDJumlah = $komponen->jumlah;
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            @endphp
                                                            <!-- @if($pj->produk) -->
                                                            <option value="{{ $produk->id }}" {{ $isSelectedTRD || $isSelectedGFT ? 'selected' : '' }}>
                                                                @if (isset($pj->produk->kode) && substr($pj->produk->kode, 0, 3) === 'TRD' && $isSelectedTRD)
                                                                    {{ $pj->produk->nama }}
                                                                @elseif (isset($pj->produk->kode) && substr($pj->produk->kode, 0, 3) === 'GFT' && $isSelectedGFT)
                                                                    {{ $pj->produk->nama }}
                                                                @endif
                                                            </option>
                                                            <!-- @endif -->
                                                        @endforeach
                                                    </select>
                                                    </td>
                                                    @if($isTRDSelected)
                                                    <td>Tidak Ada Komponen</td>
                                                    <td>
                                                        <select name="kondisitradproduk_{{ $i }}[]" id="kondisitradproduk_{{ $i }}" data-produk="{{ $selectedTRDKode }}" class="form-control kondisitrad-{{ $i }}" readonly>
                                                            <option value=""> Pilih Kondisi </option>
                                                            @foreach ($kondisis as $kondisi)
                                                            <option value="{{ $kondisi->nama }}" {{ $kondisi->nama == $selectedTRDKode ? 'selected' : ''}}>{{ $kondisi->nama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="jumlahtradproduk_{{ $i }}[]" id="jumlahtradproduk_{{ $i }}" class="form-control jumlahtrad-{{ $i }}" placeholder="Kondisi Produk" data-produk="{{ $selectedTRDKode }}" value="{{ $selectedTRDJumlah }}" readonly>
                                                    </td>
                                                    @elseif($perPendapatan)
                                                        @foreach ($perPendapatan as $noRETUR => $items)
                                                            @if($noRETUR == $produk->no_retur)
                                                            <td>
                                                            @foreach ($items as $komponen)
                                                                    <div class="row mt-2">
                                                                        <div class="col">
                                                                            <input type="hidden" name="kodegiftproduk_{{ $i }}[]" id="kodegiftproduk_{{ $i }}" class="form-control" value="{{ $komponen['kode'] }}" readonly>
                                                                            <input type="text" name="komponengiftproduk_{{ $i }}[]" id="komponengiftproduk_{{ $i }}" class="form-control komponengift-{{ $i }}" value="{{ $komponen['nama'] }}" readonly>
                                                                        </div>
                                                                    </div>
                                                            @endforeach
                                                            </td>
                                                            <td>
                                                            @foreach ($items as $komponen)
                                                                    <div class="row mt-2">
                                                                        <div class="col">
                                                                            <select name="kondisigiftproduk_{{ $i }}[]" id="kondisigiftproduk_{{ $i }}" class="form-control kondisigift-{{ $i }}" readonly>
                                                                                <option value=""> Pilih Kondisi </option>
                                                                                @foreach ($kondisis as $kondisi)
                                                                                <option value="{{ $kondisi->nama }}" {{ $kondisi->nama == $komponen['kondisi'] ? 'selected' : ''}}>{{ $kondisi->nama }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                            @endforeach
                                                            </td>
                                                            <td>
                                                            @foreach ($items as $komponen)
                                                                    <div class="row mt-2">
                                                                        <div class="col">
                                                                            <input type="number" name="jumlahgiftproduk_{{ $i }}[]" id="jumlahgiftproduk_{{ $i }}" class="form-control jumlahgift-{{ $i }}" data-index="{{ $i }}" value="{{ $komponen['jumlah'] }}" required readonly>
                                                                        </div>
                                                                    </div>
                                                            @endforeach
                                                            </td>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                    <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" class="form-control" data-index="{{ $i }}" value="{{ old('jumlah.' . $i) ?? $produk->jumlah }}" required readonly></td>
                                                    <td><input type="text" name="alasan[]" id="alasan_{{ $i }}" class="form-control" value="{{ $produk->alasan }}" required readonly></td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="text" name="diskon[]" id="diskon_{{ $i }}" value="{{ $produk->diskon}}" class="form-control" aria-label="Recipient's username" aria-describedby="basic-addon3" readonly style="flex: 2;">
                                                            <select id="jenis_diskon_{{ $i }}" name="jenis_diskon[]" class="form-control" style="flex: 1;" readonly>
                                                                <option value="persen" {{ $produk->jenis_diskon == 'persen' ? 'selected' : ''}}>%</option>
                                                                <option value="Nominal" {{ $produk->jenis_diskon == 'Nominal' ? 'selected' : ''}}>.00</option>
                                                            </select>
                                                        </div>
                                                    </td>

                                                    <td><input type="text" name="harga[]" id="harga_{{ $i }}" class="form-control" value="{{$penjualans->harga ?? 0}}" required readonly></td>
                                                    <td><input type="text" name="totalharga[]" id="totalharga_{{ $i }}" class="form-control" value="{{$penjualans->totalharga ?? 0}}" required readonly></td>
                                                    <!-- <td>
                                                        @if ($i == 0)
                                                        <button type="button" name="remove" id="{{ $i }}" class="btn btn-danger btn_remove">x</button>
                                                        @else
                                                        <button type="button" name="remove" id="{{ $i }}" class="btn btn-danger btn_remove">x</button>
                                                        @endif
                                                    </td> -->


                                                    </tr>
                                                    @php
                                                    $i++;
                                                    @endphp
                                                    @endif
                                                    @endforeach
                                                    @endif
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-around">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2">
                                <div class="form-row row">
                                    <div class="mb-4">
                                        <h5>Produk Ganti</h5>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>Jumlah</th>
                                                    <th>Unit Satuan</th>
                                                    <th>Keterangan</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="dynamic_field_tambah">
                                                
                                            @if(count($produks) > 0)
                                                    @php
                                                    $i = 0;
                                                    @endphp
                                                    @foreach ($penjualans->produk_retur as $produk)
                                                    @if($produk->jenis == 'GANTI')
                                                        <tr>
                                                        <td>
                                                            <select id="nama_produk2_{{$i}}" name="nama_produk2[]" class="form-control" readonly>
                                                                <option value="">Pilih Produk</option>
                                                                @foreach ($juals as $pj)
                                                                <option value="{{ $pj->kode }}" {{ $pj->kode == $produk->produk->kode ? 'selected' : '' }}>
                                                                @if (substr($pj->kode, 0, 3) === 'TRD')
                                                                    {{ $pj->nama }}
                                                                    @php
                                                                    $found = false;
                                                                    @endphp
                                                                    @foreach ($produk->komponen as $komponen)
                                                                        @if ($komponen->kondisi)
                                                                            @php
                                                                            $found = false;
                                                                            @endphp
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
                                                                @elseif (substr($pj->kode, 0, 3) === 'GFT')
                                                                    {{ $pj->nama }}
                                                                @endif
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="number" name="jumlah2[]" id="jumlah2_{{$i}}" class="form-control" value="{{$produk->jumlah}}" readonly></td>
                                                        <td><input type="text" name="satuan2[]" id="satuan2_{{$i}}" class="form-control" value="{{$produk->satuan}}" readonly></td>
                                                        <td><input type="text" name="keterangan2[]" id="keterangan2_{{$i}}" class="form-control" value="{{$produk->keterangan}}" readonly></td>
                                                        <td>
                                                            @if($produk->no_form == null)
                                                            <button id="btnGift_{{$i}}" data-produk_gift="{{ $produk->id}}" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalGiftCoba">
                                                                Set Gift
                                                            </button>
                                                            <td><button id="btnPerangkai_{{ $i }}" data-produk="{{ $produk->id}}" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalPerangkai">Perangkai</button></td>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endif
                                                    @php
                                                    $i++;
                                                    @endphp
                                                    @endforeach
                                                    @endif
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="row justify-content-around">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2"> -->
                                <div class="row">
                                    <div class="col-lg-8 col-sm-6 col-12 mt-4">
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
                                                            @php
                                                                $user = Auth::user();
                                                            @endphp
                                                            @if($penjualans->status == 'DIKONFIRMASI' && $user->hasRole(['Finance']))
                                                                <td id="pembuat">{{ $penjualans->dibuat->name }}</td>
                                                                <td id="penyetuju" >{{ $penjualans->diperiksa->name ?? '-' }}</td>
                                                                <td id="pemeriksa" >{{ $penjualans->dibuku->name ?? '-'}}</td>
                                                            @elseif($penjualans->status == 'DIKONFIRMASI' && $user->hasRole(['Auditor']))
                                                                <td id="pembuat">{{ $penjualans->dibuat->name }}</td>
                                                                <td id="penyetuju" >{{ $penjualans->diperiksa->name ?? '-'}}</td>
                                                                <td id="pemeriksa">{{ $penjualans->dibuku->name ?? '-'}}</td>
                                                            @elseif($user->hasRole(['AdminGallery', 'KasirGallery', 'KasirOutlet']))
                                                                <td id="pembuat">{{ $penjualans->dibuat->name }}</td>
                                                                <td id="penyetuju" >{{ $penjualans->diperiksa->name ?? '-' }}</td>
                                                                <td id="pemeriksa">{{ $penjualans->dibuku->name ?? '-' }}</td>
                                                            @endif
                                                        </tr>
                                                        <tr>
                                                            @if($user->hasRole(['AdminGallery', 'KasirGallery', 'KasirOutlet']))
                                                                <td><input type="date" class="form-control" name="tanggal_pembuat" value="{{ $penjualans->tanggal_pembuat }}" readonly></td>
                                                                <td><input type="date" class="form-control" name="tanggal_pembuat"  value="{{ $penjualans->tanggal_diperiksa ?? '-' }}" readonly></td>
                                                                <td><input type="date" class="form-control" name="tanggal_pembuat"  value="{{ $penjualans->tanggal_dibukukan ?? '-' }}" readonly></td>
                                                            @elseif($penjualans->status == 'DIKONFIRMASI' && $user->hasRole(['Finance']))
                                                                <td><input type="date" class="form-control" name="tanggal_pembuat"  value="{{ $penjualans->tanggal_pembuat }}" readonly></td>
                                                                <td><input type="date" class="form-control" name="tanggal_diperiksa" value="{{ $penjualans->tanggal_diperiksa ?? '-' }}" readonly></td>
                                                                <td><input type="date" class="form-control" name="tanggal_dibukukan"  value="{{ $penjualans->tanggal_dibukukan ?? '-' }}" readonly></td></td>
                                                            @elseif($penjualans->status == 'DIKONFIRMASI' && $user->hasRole(['Auditor']))
                                                                <td><input type="date" class="form-control" name="tanggal_pembuat"  value="{{ $penjualans->tanggal_pembuat }}" readonly></td>
                                                                <td><input type="date" class="form-control" name="tanggal_diperiksa" value="{{ $penjualans->tanggal_diperiksa ?? '-'}}" readonly></td>
                                                                <td><input type="date" class="form-control" name="tanggal_dibukukan" value="{{ $penjualans->tanggal_dibukukan ?? '-' }}" readonly></td>
                                                            @endif
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="col-lg-4 float-md-right border radius mt-2">
                                        <div class="total-order">
                                            <ul>
                                                <li>
                                                    <h4>Sub Total</h4>
                                                    <h5><input type="text" id="sub_total" name="sub_total" class="form-control" onchange="calculateTotal(0)" value="{{ $penjualans->sub_total}}" readonly required></h5>
                                                </li>
                                                <li>
                                                    <h4>Pengiriman
                                                    <select id="pilih_pengiriman" name="pilih_pengiriman" class="form-control" required readonly>
                                                        <option value="">Pilih Jenis Pengiriman</option>
                                                        <option value="exspedisi" {{ $penjualans->pilih_pengiriman == 'exspedisi' ? 'selected' : ''}}>Ekspedisi</option>
                                                        <option value="sameday" {{ $penjualans->pilih_pengiriman == 'sameday' ? 'selected' : ''}}>SameDay</option>
                                                    </select>
                                                    </h4>
                                                    <h5>
                                                    <div id="inputOngkir" style="display: none;">
                                                        <!-- <label for="alamat_tujuan">Alamat Tujuan </label> -->
                                                        <input type="text" id="alamat_tujuan" name="alamat_tujuan" value="{{$penjualans->alamat_tujuan}}" class="form-control" readonly>
                                                    </div>
                                                    <div id="inputExspedisi" style="display: none;">
                                                        <!-- <label>Alamat Pengiriman</label> -->
                                                        <select id="ongkir_id" name="ongkir_id" class="form-control" readonly>
                                                            <option value="">Pilih Alamat Tujuan</option>
                                                            @foreach($ongkirs as $ongkir)
                                                            <option value="{{ $ongkir->id }}" data-biaya_pengiriman="{{ $ongkir->biaya}}" {{ $ongkir->id == $penjualans->ongkir_id ? 'selected' : ''}}>{{ $ongkir->nama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div> 
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Biaya Ongkir</h4>
                                                    <h5><input type="text" id="biaya_pengiriman" name="biaya_pengiriman" class="form-control" value="{{'Rp ' . number_format($penjualans->biaya_pengiriman, 0, '.',',')}}" required readonly> </h5>
                                                </li>
                                                <li>
                                                    <h4>Total</h4>
                                                    <h5><input type="text" id="total" name="total" class="form-control" value="{{ 'Rp ' . number_format($penjualans->total, 0, '.', ',')}}" readonly required></h5>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <!-- </div>
                        </div> -->

                        <div class="text-end mt-3">
                            <!-- <button class="btn btn-pr;imary" type="submit">Submit</button> -->
                            <a href="{{ route('returpenjualan.index') }}" class="btn btn-secondary" type="button">Back</a>
                        </div>
            </form>
        </div>

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
                            <input type="hidden" name="status" id="status" value="{{$penjualans->status}}">
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
                        <input type="text" class="form-control" name="jenis_rangkaian" id="jenis_rangkaian" value="Retur Penjualan" readonly required>
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

<div class="modal fade" id="modalGiftCoba" tabindex="-1" aria-labelledby="modalGiftLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalGiftLabel">Atur Komponen Gift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="form_gift" action="{{ route('komponenretur.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="route" value="{{ request()->route()->getName() }},penjualan,{{ request()->route()->parameter('penjualan') }}">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-sm-8">
                                <label for="prdTerjualGift" class="col-form-label">Produk</label>
                                <input type="text" class="form-control" name="produk_id" id="prdTerjualGift" readonly required>
                            </div>
                            <input type="hidden" name="prdTerjual_id" id="prdTerjualGift_id" value="">
                            <input type="hidden" name="pengirim" value="{{ $penjualans->lokasi_id}}">
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
</div>
@endsection

@section('scripts')
<script>
    // Function to update date to today's date
    function updateDate(element) {
        var today = new Date().toISOString().split('T')[0];
        element.value = today;
    }

    updateDate(document.getElementById('tanggal_retur'));
    updateDate(document.getElementById('tanggal_kirim'));

    $(document).ready(function() {
        var i = 1;
        $('#add').click(function() {
            var newRow = '<tr class="tr_clone" id="row' + i + '">' +
                '<td>' +
                '<select id="nama_produk_' + i + '" name="nama_produk[]" class="form-control select2" required>' +
                '<option value="">Pilih Produk</option>' +
                '@foreach ($juals as $index => $produk)' +
                '<option value="{{ $produk->id }}" data-harga="{{ $produk->harga_jual }}" data-kode="{{ $produk->kode }}" data-tipe="{{ $produk->tipe }}" data-deskripsi="{{ $produk->deskripsi }}">{{ $produk->nama }}</option>' +
                '@endforeach' +
                '</select>' +
                '<input type="hidden" name="kode_produk[]" id="kode_produk_' + i + '" style="display: none;">' +
                '<input type="hidden" name="tipe_produk[]" id="tipe_produk_' + i + '" style="display: none;">' +
                '<input type="hidden" name="deskripsi_komponen[]" id="deskripsi_komponen_' + i + '" style="display: none;">' +
                '</td>' +
                '<td><input type="number" name="jumlah[]" id="jumlah_' + i + '" oninput="multiply(this)" class="form-control" required></td>' +
                '<td><input type="text" name="unit_satuan[]" id="unit_satuan_' + i + '" class="form-control" required></td>' +
                '<td><input type="text" name="keterangan[]" id="keterangan_' + i + '" class="form-control" required></td>' +
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
                '<!-- Form untuk PIC Perangkai -->' +
                '</div>' +
                '<div class="modal-footer justify-content-center">' +
                '<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';
            $('body').append(picModal);

            $('#nama_produk_' + i + ', #jenisdiskon_' + i).select2();
            i++
        });

        $('#addtambah').click(function() {
            var newrowtambah = '<tr class="tr_clone" id="row' + i + '">' +
                '<td>' +
                '<select id="nama_produk2_' + i + '" name="nama_produk2[]" class="form-control select2">' +
                '<option value="">Pilih Produk</option>';

            @foreach ($juals as $produk)
                newrowtambah += '<option value="{{ $produk->kode }}" data-harga="{{ $produk->harga_jual }}" data-tipe_produk="{{ $produk->tipe_produk }}">';

                @if (substr($produk->kode, 0, 3) === 'TRD')
                    newrowtambah += '{{ $produk->nama }}';

                    @foreach ($produk->komponen as $komponen)
                        @if ($komponen->kondisi)
                            @foreach($kondisis as $kondisi)
                                @if($kondisi->id == $komponen->kondisi)
                                    newrowtambah += '- {{ $kondisi->nama }}';
                                    @php $found = true; @endphp
                                    @break
                                @endif
                            @endforeach
                        @endif
                        @if ($found) @break @endif
                    @endforeach

                @elseif (substr($produk->kode, 0, 3) === 'GFT')
                    newrowtambah += '{{ $produk->nama }}';
                @endif

                newrowtambah += '</option>';
            @endforeach

            newrowtambah += '</select>' +
                '<input type="hidden" name="kode_produk[]" id="kode_produk_' + i + '" style="display: none;">' +
                '<input type="hidden" name="tipe_produk[]" id="tipe_produk_' + i + '" style="display: none;">' +
                '<input type="hidden" name="deskripsi_komponen[]" id="deskripsi_komponen_' + i + '" style="display: none;">' +
                '</td>' +
                '<td><input type="number" name="jumlah2[]" id="jumlah2_' + i + '" oninput="multiply(this)" class="form-control"></td>' +
                '<td><input type="text" name="satuan2[]" id="satuan2_' + i + '" class="form-control"></td>' +
                '<td><input type="text" name="keterangan2[]" id="keterangan2_' + i + '" class="form-control"></td>' +
                '<td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">x</button></td>' +
                '</tr>';

            $('#dynamic_field_tambah').append(newrowtambah);
            $('#nama_produk2_' + i).select2(); // Ganti ini
            i++;
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

        $('[id^=btnGift]').click(function(e) {
                e.preventDefault();
                var produk_id = $(this).data('produk_gift');
                // console.log(produk_id);
                getDataGift(produk_id);
            });

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

        $('[id^=nama_produk_]').on('mousedown click focus', function(e) {
            e.preventDefault();
        });

        $('#jml_perangkai').on('input', function(e) {
            e.preventDefault();
            var jumlah = $(this).val();
            jumlah = parseInt(jumlah) > 10 ? 10 : parseInt(jumlah);
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

        $(document).on('change', '#komplain', handleKomplainChange);
        handleKomplainChange();

        $('#pilih_pengiriman').on('change',function (){
            var pengiriman = $(this).val();
            var biayaOngkir = parseFloat($('#biaya_pengiriman').val()) || 0;

            $('#inputOngkir').hide();
            $('#inputExspedisi').hide();

            if (pengiriman === "sameday") {
                $('#inputOngkir').show();
                $('#biaya_pengiriman').prop('readonly', true);
            } else if (pengiriman === "exspedisi") {
                $('#inputExspedisi').show();
                $('#biaya_pengiriman').prop('readonly', true);
                ongkirId();
            }
        });
        $('#pilih_pengiriman').trigger('change');
        

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
        // $('.kondisi').hide();
        $(document).ready(function() {
            $('[id^=nama_produk]').each(function() {
                var id = $(this).attr('id').split('_')[2];
                var kodeProduk = $(this).find(':selected').val();
                // console.log(kodeProduk);
                $('.kondisi').hide(); // sembunyikan semua input kondisi
                if (kodeProduk.substr(0, 3) === 'TRD') {
                    $('.kondisi-' + id).show(); // tampilkan input kondisi untuk indeks yang dipilih
                } else if (kodeProduk.substr(0, 3) === 'GFT') {
                    // sembunyikan teks GFT jika dipilih
                }
            });
        });

        $(document).on('change', '[id^=nama_produk]', function() {
            var id = $(this).attr('id').split('_')[2]; // Ambil bagian angka ID
            var selectedOption = $(this).find(':selected');
            $('#kode_produk_' + id).val(selectedOption.data('kode'));
            $('#tipe_produk_' + id).val(selectedOption.data('tipe'));
            $('#deskripsi_komponen_' + id).val(selectedOption.data('deskripsi'));

        });

        $('#status').change(function(){
            var status = $(this).val();
            if(status == 'DIBATALKAN')
            {
                $('#alasan').show();
            }else{
                $('#alasan'),hide();
            }
        });

        $('#status').trigger('change');

        

        $('#ongkir_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var ongkirValue = parseFloat(selectedOption.data('biaya_pengiriman')) || 0;
            $('#biaya_pengiriman').val(ongkirValue);
            Totaltagihan();
        });


        function handleFileInputChange(inputElement, previewElement) {
            const file = inputElement.files[0];
            if (file.size > 2 * 1024 * 1024) {
                toastr.warning('Ukuran file tidak boleh lebih dari 2mb', {
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: false,
                    progressBar: true
                });
                $(inputElement).val('');
                return;
            }
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $(previewElement).attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        }

        $('#bukti').on('change', function() {
            handleFileInputChange(this, '#preview');
        });

        $('#bukti_kirim').on('change', function() {
            handleFileInputChange(this, '#preview_kirim');
        });


        function clearFile() {
            $('#bukti').val('');
            $('#preview').attr('src', defaultImg);
        };


        function handleKomplainChange() {
            var komplain = $('#komplain').val();

            if(komplain == 'retur') {
                $('#gantiproduk').show();
                $('[id^=harga_]').each(function() {
                    var hargaSatuanInput = $(this);
                    var index = hargaSatuanInput.attr('id').split('_')[1];
                    var biayakirim = $('#biaya_pengiriman');

                    $('#tanggalkirim, #penerima, #driver, #alamat, #bukti_kirim, #biaya_pengiriman').show();
                    biayakirim.prop('readonly', false);
                    hargaSatuanInput.val(0);
                    hargaSatuanInput.prop('readonly', true);
                });

                $('[id^=totalharga_]').each(function() {
                    var totalhargaInput = $(this);
                    var index = totalhargaInput.attr('id').split('_')[1];
                    totalhargaInput.val(0);
                    totalhargaInput.prop('readonly', true);
                });

                $('[id^=diskon_]').each(function() {
                    var diskonInput = $(this);
                    var index = diskonInput.attr('id').split('_')[1];
                    diskonInput.val(0);
                    diskonInput.prop('readonly', true);
                });

                $('[id^=jumlah_]').each(function() {
                    var jumlahInput = $(this);
                    var index = jumlahInput.attr('id').split('_')[1];
                    jumlahInput.prop('readonly', true);
                });

                // updateSubTotal();
            } else {
                $('#gantiproduk').hide();
                $('[id^=harga_]').each(function() {
                    var hargaSatuanInput = $(this);
                    var index = hargaSatuanInput.attr('id').split('_')[1];
                    var biayakirim = $('#biaya_pengiriman');

                    $('#tanggalkirim, #penerima, #driver, #alamat, #bukti_kirim, #biaya_pengiriman').hide();
                    biayakirim.val(0);
                    biayakirim.prop('readonly', true);
                    var hargaProduk = $('#nama_produk_' + index + ' option:selected').data('harga');
                    var jumlah = $('#jumlah_' + index).val();
                    var harga = hargaProduk * jumlah;
                    hargaSatuanInput.val(harga);
                    hargaSatuanInput.prop('readonly', true);
                });

                $('[id^=totalharga_]').each(function() {
                    var totalhargaInput = $(this);
                    var index = totalhargaInput.attr('id').split('_')[1];
                    var hargaSatuan = $('#harga_' + index).val();
                    var jumlah = $('#jumlah_' + index).val();
                    var totalharga = hargaSatuan * jumlah;
                    totalhargaInput.val(totalharga);
                    totalhargaInput.prop('readonly', true);
                });

                $('[id^=diskon_]').each(function() {
                    var diskonInput = $(this);
                    var index = diskonInput.attr('id').split('_')[1];

                    if(komplain == 'refund') {
                        diskonInput.val(0);
                        diskonInput.prop('readonly', true);
                    } else if(komplain == 'diskon') {
                        diskonInput.prop('readonly', false);
                    } else {
                        diskonInput.val(0);
                        diskonInput.prop('readonly', true);
                    }
                });

                $('[id^=jumlah_]').each(function() {
                    var jumlahInput = $(this);
                    var index = jumlahInput.attr('id').split('_')[1];

                    if(komplain == 'refund' || komplain == 'diskon') {
                        jumlahInput.prop('readonly', false);
                    } else {
                        jumlahInput.prop('readonly', true);
                    }
                });

                updateSubTotal();
            }
        }

        

        var jumlahDO = [];

        @foreach ($penjualans->deliveryorder as $deliveryOrder)
            @foreach ($deliveryOrder->produk as $produk)
                jumlahDO.push({{ $produk->jumlah }});
            @endforeach
        @endforeach

        $('[id^=jumlah_]').on('input', function() {
            var jumlahInput = $(this);
            var index = jumlahInput.attr('id').split('_')[1];
            var komplain = $('#komplain').val();
            var inputJumlah = $(this).val();
            // console.log(jumlahDO[index]);
            if (parseInt(inputJumlah) > jumlahDO[index]) {

                alert('Jumlah Komplain harus sesuai dengan jumlah DO!');
                $(this).val(jumlahDO[index]);
            } else {
                if (komplain == 'refund') {
                    var hargaProduk = $('#nama_produk_' + index + ' option:selected').data('harga');
                    var jumlah = jumlahInput.val();

                    // Jika jumlah tidak valid, berikan harga dan total harga nilai 0
                    if (isNaN(jumlah) || jumlah <= 0) {
                        $('#harga_' + index).val(0);
                        $('#totalharga_' + index).val(0);
                    } else {
                        var harga = hargaProduk * jumlah;
                        $('#harga_' + index).val(harga);
                        $('#totalharga_' + index).val(harga);
                    }

                    // Panggil updateSubTotal setiap kali input jumlah diubah
                    updateSubTotal();
                }
            }
        });

        function updateSubTotal() {
            var subTotal = 0;
            // console.log($('input[name="totalharga[]"]'));
            $('input[name="totalharga[]"]').each(function() {
                subTotal += parseFloat($(this).val()) || 0;
            });
            $('#sub_total').val(subTotal.toFixed(2));
            $('#total').val(subTotal.toFixed(2));
        }

        $('#nama_produk_{{ $i }}').change(function(){
            var selectedOption = $(this).find(':selected');
            if(selectedOption.val().substr(0, 3) === 'TRD') {
                $('.kondisi').hide();
                selectedOption.siblings('.kondisi').show();
            } else {
                $('.kondisi').hide();
            }
        });

        function pilihjenis(){
            var jenisInput = $('[id^=jenis_diskon_]');
            var index = jenisInput.attr('id').split('_')[2]; 
            var selectedValue = jenisInput.val();
            var diskonValue = parseFloat($('#diskon_' + index).val()) || 0; 
            var hargaTotal = parseFloat($('#harga_' + index).val()) || 0; 

            $('[id^=diskon_' + index + ']').trigger('input');
        };

        $('[id^=jenis_diskon_]').on('change', pilihjenis());
        pilihjenis();

        $('[id^=diskon_]').on('input', function(){
            var hasilInput = $(this);
            var index = hasilInput.attr('id').split('_')[1]; 
            var jenisInput = $('#jenis_diskon_' + index); 
            var selectedValue = jenisInput.val(); 

            var hargaTotal = parseFloat($('#harga_' + index).val()) || 0; // Mengambil harga total berdasarkan index

            if (selectedValue === "Nominal") {
                var diskonValue = parseFloat(hasilInput.val()) || 0; // Mengambil nilai diskon berdasarkan index
                hargaTotal -= diskonValue; 
            } else if (selectedValue === "persen") {
                var diskonValue = parseFloat(hasilInput.val()) || 0; // Mengambil nilai diskon berdasarkan index
                var diskonAmount = (hargaTotal * diskonValue) / 100; // Hitung jumlah diskon berdasarkan persen
                hargaTotal -= diskonAmount; // Kurangi harga total dengan jumlah diskon
            }

            $('#totalharga_' + index).val(hargaTotal.toFixed(2));
            var subtotal = 0;
            $('input[name="totalharga[]"]').each(function() {
                subtotal += parseFloat($(this).val()) || 0;
            });

            $('#sub_total').val(subtotal.toFixed(2));
            $('#total').val(subtotal.toFixed(2));
        });

        function Totaltagihan() {
            var biayaOngkir = parseFloat($('#biaya_pengiriman').val()) || 0;
            var totalTagihan = biayaOngkir;
            

            $('#total').val(totalTagihan.toFixed(2));

        }

        $('#biaya_pengiriman').on('input', Totaltagihan);

    });
</script>

@endsection