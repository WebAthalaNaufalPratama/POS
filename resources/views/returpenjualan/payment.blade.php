@extends('layouts.app-von')

@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Pembayaran Retur</h3>
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
                <li class="breadcrumb-item active">
                    Pembayaran Retur
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">
                Pembayaran Retur
            </h4>
        </div>
        <div class="card-body">
            <form action="{{ route('returpenjualan.store', ['penjualan' => $penjualans->id]) }}" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm">
                        @csrf

                        <div class="row justify-content-around">
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
                                        @if(!empty($penjualans->deliveryorder[0]))
                                        <div class="form-group" style="display:none;" id="penerima">
                                            <label for="penerima">Nama Penerima</label>
                                            <input type="text" class="form-control" placeholder="Nama Penerima" name="penerima" id="penerima" value="{{ $penjualans->deliveryorder[0]->penerima}}" disabled>
                                        </div>
                                        @endif
                                        <div class="form-group" style="display:none;" id="tanggalkirim">
                                            <label for="tanggal_kirim">Tanggal Kirim</label>
                                            <input type="date" class="form-control" placeholder="Tanggal Kirim" id="tanggal_kirim" name="tanggal_kirim" value="{{$penjualans->tanggal_kirim}}" disabled>
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
                                            <label for="harga_jual">Input File</label>
                                            <div class="input-group mt-3">
                                                <!-- <input type="file" id="bukti_file" name="bukti_file" placeholder="Bukti File Invoice" aria-describedby="inputGroupPrepend2" required disabled> -->
                                                <div class="custom-file-container" data-upload-id="myFirstImage">
                                                    <label>Bukti Retur<a href="javascript:void(0)" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"></a>
                                                    </label>
                                                    <label class="custom-file-container__custom-file">
                                                        <input type="file" id="bukti" class="custom-file-container__custom-file__custom-file-input" name="bukti" accept="image/*" required disabled>
                                                        <span class="custom-file-container__custom-file__custom-file-control"></span>
                                                    </label>
                                                    <span class="text-danger">max 2mb</span>
                                                    <img id="preview" src="{{ $penjualans->bukti ? '/storage/' . $penjualans->bukti : '' }}" alt="your image" />
                                                </div>
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
                                        @if(!empty($penjualans->deliveryorder[0]))
                                        <div class="form-group" style="display:none;" id="driver">
                                            <label for="driver">Driver</label>
                                            <select id="driver" name="driver" class="form-control" readonly>
                                                <option value=""> Pilih Driver </option>
                                                @foreach ($drivers as $driver)
                                                <option value="{{ $driver->id }}" {{$driver->id == $penjualans->deliveryorder[0]->driver ? 'selected' : ''}}>{{ $driver->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group" style="display:none;" id="alamat">
                                            <label for="alamat">Alamat Pengiriman</label>
                                            <textarea id="alamat" name="alamat" value="{{ $penjualans->deliveryorder[0]->alamat}}" disabled>{{ $penjualans->deliveryorder[0]->alamat}}</textarea>
                                        </div>
                                        @endif
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
                                        @if(!empty($penjualans->deliveryorder[0]))
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
                                                    <th>Nama</th>
                                                    <th>Jumlah</th>
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
                                                    @if($isTRDSelected)
                                                        <div class="row mt-2">
                                                            <div class="col">
                                                                <select name="kondisitradproduk_{{ $i }}[]" id="kondisitradproduk_{{ $i }}" data-produk="{{ $selectedTRDKode }}" class="form-control kondisitrad-{{ $i }}" readonly>
                                                                    <option value=""> Pilih Kondisi </option>
                                                                    @foreach ($kondisis as $kondisi)
                                                                    <option value="{{ $kondisi->nama }}" {{ $kondisi->nama == $selectedTRDKode ? 'selected' : ''}}>{{ $kondisi->nama }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col">
                                                                <input type="text" name="jumlahtradproduk_{{ $i }}[]" id="jumlahtradproduk_{{ $i }}" class="form-control jumlahtrad-{{ $i }}" placeholder="Kondisi Produk" data-produk="{{ $selectedTRDKode }}" value="{{ $selectedTRDJumlah }}" readonly>
                                                            </div>
                                                        </div>
                                                    @elseif($perPendapatan)
                                                        @foreach ($perPendapatan as $noRETUR => $items)
                                                            @if($noRETUR == $produk->no_retur)
                                                                @foreach ($items as $komponen)
                                                                    <div class="row mt-2">
                                                                        <div class="col">
                                                                        <input type="hidden" name="kodegiftproduk_{{ $i }}[]" id="kodegiftproduk_{{ $i }}" class="form-control" value="{{ $komponen['kode'] }}" readonly>
                                                                            <input type="text" name="komponengiftproduk_{{ $i }}[]" id="komponengiftproduk_{{ $i }}" class="form-control komponengift-{{ $i }}" value="{{ $komponen['nama'] }}" readonly>
                                                                            </div>
                                                                            <div class="col">
                                                                            <select name="kondisigiftproduk_{{ $i }}[]" id="kondisigiftproduk_{{ $i }}" class="form-control kondisigift-{{ $i }}">
                                                                                <option value=""> Pilih Kondisi </option>
                                                                                @foreach ($kondisis as $kondisi)
                                                                                <option value="{{ $kondisi->nama }}" {{ $kondisi->nama == $komponen['kondisi'] ? 'selected' : ''}}>{{ $kondisi->nama }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col">
                                                                            <input type="number" name="jumlahgiftproduk_{{ $i }}[]" id="jumlahgiftproduk_{{ $i }}" class="form-control jumlahgift-{{ $i }}" data-index="{{ $i }}" value="{{ $komponen['jumlah'] }}" required>
                                                                            <!-- <button type="button" name="remove" id="{{ $i }}" class="btn btn-danger btn_remove">x</button>    -->
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    </td>
                                                    <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" class="form-control" data-index="{{ $i }}" value="{{ old('jumlah.' . $i) ?? $produk->jumlah }}" required readonly></td>
                                                    <td><input type="text" name="alasan[]" id="alasan_{{ $i }}" class="form-control" value="{{ $produk->alasan}}" required readonly></td>
                                                    <td>
                                                        <select id="jenis_diskon_{{ $i }}" name="jenis_diskon[]" class="form-control" readonly>
                                                        <option value="0">Pilih Diskon</option>
                                                        <option value="Nominal" {{ $produk->jenis_diskon == 'Nominal' ? 'selected' : ''}}>Nominal</option>
                                                        <option value="persen" {{ $produk->jenis_diskon == 'persen' ? 'selected' : ''}}>Persen</option>
                                                    </select>
                                                    <div>
                                                        <div class="input-group">
                                                            <input type="number" name="diskon[]" id="diskon_{{ $i }}" value="" class="form-control" aria-label="Recipient's username" aria-describedby="basic-addon3" readonly>
                                                            <span class="input-group-text" id="nominalInput_{{ $i }}" style="display:none;">.00</span>
                                                            <span class="input-group-text" id="persenInput_{{ $i }}" style="display:none;">%</span>
                                                        </div>
                                                    </div>
                                                </td>

                                                    <td><input type="text" name="harga[]" id="harga_{{ $i }}" class="form-control" value="{{ $produk->harga}}" required readonly></td>
                                                    <td><input type="text" name="totalharga[]" id="totalharga_{{ $i }}" class="form-control" value="{{ $produk->totalharga}}" required readonly></td>
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
                                                                @foreach ($produkreturjuals as $pj)
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
                                                        <!-- <td>
                                                            <button id="btnGift_{{$i}}" data-produk_gift="{{ $produk->id}}" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalGiftCoba">
                                                                Set Gift
                                                            </button>
                                                        </td> -->
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
                        <div class="row justify-content-around">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2">
                                <div class="row">
                                    <div class="col-lg-8 col-sm-6 col-12 ">
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
                                                            @elseif($user->hasRole(['AdminGallery', 'KasirAdmin', 'KasirOutlet']))
                                                                <td id="pembuat">{{ $penjualans->dibuat->name }}</td>
                                                                <td id="penyetuju" >{{ $penjualans->diperiksa->name ?? '-' }}</td>
                                                                <td id="pemeriksa">{{ $penjualans->dibuku->name ?? '-' }}</td>
                                                            @endif
                                                        </tr>
                                                        <tr>
                                                            @if($user->hasRole(['AdminGallery', 'KasirAdmin', 'KasirOutlet']))
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
                                    <div class="col-lg-4 float-md-right">
                                        <div class="total-order">
                                            <ul>
                                                <li>
                                                    <h4>Sub Total</h4>
                                                    <h5><input type="text" id="sub_total" name="sub_total" class="form-control" onchange="calculateTotal(0)" value="0" readonly required></h5>
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
                                                        <input type="text" id="alamat_tujuan" name="alamat_tujuan" class="form-control">
                                                    </div>
                                                    <div id="inputExspedisi" style="display: none;">
                                                        <select id="ongkir_id" name="ongkir_id" class="form-control" readonly>
                                                            <option value="">Pilih Alamat Tujuan</option>
                                                            @foreach($ongkirs as $ongkir)
                                                            <option value="{{ $ongkir->id }}" data-biaya_pengiriman="{{ $ongkir->biaya}}" {{ $penjualans->ongkir_id == $ongkir->id ? 'selected' : ''}}>{{ $ongkir->nama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div> 
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Biaya Ongkir</h4>
                                                    <h5><input type="number" id="biaya_pengiriman" name="biaya_pengiriman" class="form-control" value="{{ $penjualans->biaya_pengiriman}}" readonly required></h5>
                                                </li>
                                                <li>
                                                    <h4>Total</h4>
                                                    <h5><input type="text" id="total" name="total" class="form-control" value="{{ $penjualans->total}}" readonly required></h5>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row justify-content-around">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-1">
                                <div class="form-row row">
                                    <h5>Riwayat Pembayaran</h5>
                                        <div class="row justify-content-between">
                                            <div class="col-md-12">
                                                <label for=""></label>
                                                <div class="add-icon text-end">
                                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalBayar">add +</button>
                                                </div>
                                            </div>
                                        </div>
                                    <div class="mb-4">
                                        
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
                        </div>

                        <div class="text-end mt-3">
                            <!-- <button class="btn btn-primary" type="submit">Submit</button> -->
                            <a href="{{ route('returpenjualan.index') }}" class="btn btn-secondary" type="button">Back</a>
                        </div>
            </form>
        </div>

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

<div class="modal fade" id="modalBayar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('returpenjualan.paymentretur', ['invoice_penjualan_id' => $penjualans->id])}}" method="POST" enctype="multipart/form-data">
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
                        <input type="number" class="form-control" id="nominal" name="nominal" value="{{ $penjualans->total }}" placeholder="Nominal Bayar" required>
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
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        var cekInvoiceNumbers = "<?php echo $cekInvoice; ?>";
        var nextInvoiceNumber = parseInt(cekInvoiceNumbers) + 1;

        function generateInvoice() {
            var invoicePrefix = "BRP";
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
    // Function to update date to today's date
    function updateDate(element) {
        var today = new Date().toISOString().split('T')[0];
        element.value = today;
    }

    updateDate(document.getElementById('tanggal_retur'));
    updateDate(document.getElementById('tanggal_kirim'));
    updateDate(document.getElementById('tanggal_bayar'));
</script>
<script>
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

        $('[id^=nama_produk_]').on('mousedown click focus', function(e) {
            e.preventDefault();
        });

        var initialKomplain = $('#komplain').val();
        handleKomplainChange(initialKomplain);

        pilihkirim();

        function pilihkirim() {
            var pengiriman = $('#pilih_pengiriman').val();
            var biayaOngkir = parseFloat($('#biaya_pengiriman').val()) || 0;

            $('#inputOngkir').hide();
            $('#inputExspedisi').hide();

            if (pengiriman === "sameday") {
                $('#inputOngkir').show();
                $('#biaya_pengiriman').prop('readonly', false);
            } else if (pengiriman === "exspedisi") {
                $('#inputExspedisi').show();
                $('#biaya_pengiriman').prop('readonly', true);
                ongkirId();
            }
        };

        
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

        $('#komplain').on('change', function() {
            var komplain = $(this).val();
            handleKomplainChange(komplain);
        });

        function handleKomplainChange(komplain) {
            if (komplain == 'retur') {
                $('#gantiproduk').show();
                $('#tanggalkirim, #penerima, #driver, #alamat, #bukti_kirim, #biaya_pengiriman').show();
                $('#biaya_pengiriman').prop('readonly', false);
            } else {
                $('#gantiproduk').hide();
                $('#tanggalkirim, #penerima, #driver, #alamat, #bukti_kirim, #biaya_pengiriman').hide();
                $('#biaya_pengiriman').val(0).prop('readonly', true);
            }

            updateHargaFields(komplain);
            updateTotalHargaFields(komplain);
            updateDiskonFields(komplain);
            updateJumlahFields(komplain);
            // updateSubTotal();
        }

        

        function updateHargaFields(komplain) {
            $('[id^=harga_]').each(function() {
                var hargaSatuanInput = $(this);
                var index = hargaSatuanInput.attr('id').split('_')[1];
                if (komplain == 'retur') {
                    hargaSatuanInput.val(0).prop('readonly', true);
                } else {
                    var hargaProduk = $('#nama_produk_' + index + ' option:selected').data('harga');
                    var jumlah = $('#jumlah_' + index).val();
                    var harga = hargaProduk * jumlah;
                    hargaSatuanInput.val(harga).prop('readonly', true);
                }
            });
        }

        function updateTotalHargaFields(komplain) {
            $('[id^=totalharga_]').each(function() {
                var totalhargaInput = $(this);
                var index = totalhargaInput.attr('id').split('_')[1];
                if (komplain == 'retur') {
                    totalhargaInput.val(0).prop('readonly', true);
                } else if (komplain == 'refund' || komplain == 'diskon') {
                    var hargaSatuan = $('#harga_' + index).val();
                    var jumlah = $('#jumlah_' + index).val();
                    var totalharga = hargaSatuan * jumlah;
                    totalhargaInput.val(totalharga).prop('readonly', true);
                }
            });
        }

        function updateDiskonFields(komplain) {
            $('[id^=diskon_]').each(function() {
                var diskonInput = $(this);
                var index = diskonInput.attr('id').split('_')[1];
                if (komplain == 'refund' || komplain == 'retur') {
                    diskonInput.val(0).prop('readonly', true);
                } else if (komplain == 'diskon') {
                    diskonInput.prop('readonly', false);
                }
            });
        }

        function updateJumlahFields(komplain) {
            $('[id^=jumlah_]').each(function() {
                var jumlahInput = $(this);
                var index = jumlahInput.attr('id').split('_')[1];
                if (komplain == 'refund' || komplain == 'diskon') {
                    jumlahInput.prop('readonly', false);
                } else {
                    jumlahInput.prop('readonly', true);
                }
            });
        }

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

        $('#bayar').on('change', function() {
            var caraBayar = $(this).val();
            console.log(caraBayar);
            if (caraBayar == 'cash') {
                $('#rekening').hide();
            } else if (caraBayar == 'transfer') {
                $('#rekening').show();
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

        var sisaBayar = {{ $penjualans->total}};

        $('#nominal').on('input', function() {
            var inputNominal = $(this).val();

            if (parseInt(inputNominal) > sisaBayar) {
                alert('Nominal pembayaran tidak boleh lebih dari sisa bayar!');
                $(this).val(sisaBayar);
            }
        });

        $('[id^=jenis_diskon_]').on('change', function() {
            var jenisInput = $(this);
            var index = jenisInput.attr('id').split('_')[2]; 
            var selectedValue = jenisInput.val();
            var diskonValue = parseFloat($('#diskon_' + index).val()) || 0; 
            var hargaTotal = parseFloat($('#harga_' + index).val()) || 0; 

            $('[id^=diskon_' + index + ']').trigger('input');
        });

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