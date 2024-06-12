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
            <form action="{{ route('returpenjualan.store', ['penjualan' => $penjualans->id]) }}" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 border rounded pt-3 ">
                                <h5>Informasi Pelanggan</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="no_retur">No Retur Penjualan</label>
                                            <input type="text" class="form-control" id="no_retur" name="no_retur" placeholder="Silahkan Pilih Lokasi Terlebih Dahulu" value="" onchange="generateDOP(this)" readonly required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                        <label for="customer_id">Nama Customer</label>
                                            <select id="customer_id" name="customer_id" class="form-control">
                                                <option value=""> Pilih Nama Customer </option>
                                                @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="lokasi_id">Lokasi</label>
                                            <select id="lokasi_id" name="lokasi_id" class="form-control" required>
                                                <option value=""> Pilih Lokasi </option>
                                                @foreach ($lokasis as $lokasi)
                                                <option value="{{ $lokasi->id }}" data-tipe="{{ $lokasi->tipe_lokasi }}">{{ $lokasi->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group" style="display:none;" id="penerima">
                                            <label for="penerima">Nama Penerima</label>
                                            <input type="text" class="form-control" placeholder="Nama Penerima" name="penerima" id="penerima">
                                        </div>
                                        <div class="form-group" style="display:none;" id="tanggalkirim">
                                            <label for="tanggal_kirim">Tanggal Kirim</label>
                                            <input type="date" class="form-control" placeholder="Tanggal Kirim" id="tanggal_kirim" name="tanggal_kirim">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                        <label for="supplier_id">Supplier</label>
                                            <select id="supplier_id" name="supplier_id" class="form-control">
                                                <option value=""> Pilih Nama Supplier </option>
                                                @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
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
                                                <img id="preview" />
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
                                            <input type="date" class="form-control" id="tanggal_invoice" name="tanggal_invoice" placeholder="Nomor Invoice" value="{{ $penjualans->tanggal_invoice}}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="tanggal_retur">Tanggal Retur</label>
                                            <input type="date" class="form-control" id="tanggal_retur" name="tanggal_retur" placeholder="Tanggal_retur" onchange="updateDate(this)" required>
                                        </div>
                                        <div class="form-group">
                                        <label for="komplain">Komplain</label>
                                            <select id="komplain" name="komplain" class="form-control">
                                                <option value=""> Pilih Komplain </option>
                                                <option value="refund">Refund</option>
                                                <option value="diskon">Diskon</option>
                                                <option value="retur">Retur</option>
                                            </select>
                                        </div>
                                        <div class="form-group" style="display:none;" id="driver">
                                            <label for="driver">Driver</label>
                                            <select id="driver" name="driver" class="form-control">
                                                <option value=""> Pilih Driver </option>
                                                @foreach ($drivers as $driver)
                                                <option value="{{ $driver->id }}">{{ $driver->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group" style="display:none;" id="alamat">
                                            <label for="alamat">Alamat Pengiriman</label>
                                            <textarea id="alamat" name="alamat"></textarea>
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
                                            <textarea id="catatan_komplain" name="catatan_komplain"></textarea>
                                        </div>
                                        <div class="form-group" style="display:none;" id="bukti_kirim">
                                            <div class="custom-file-container" data-upload-id="mySecondImage">
                                                <label>Bukti Kirim <a href="javascript:void(0)" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"></a>
                                                </label>
                                                <label class="custom-file-container__custom-file">
                                                    <input type="file" id="bukti_kirim" class="custom-file-container__custom-file__custom-file-input_2" name="file" accept="image/*">
                                                    <span class="custom-file-container__custom-file__custom-file-control_2"></span>
                                                </label>
                                                <span class="text-danger">max 2mb</span>
                                                <img id="preview_kirim" />
                                            </div>
                                        </div>
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
                                                    <th>No Delivery Order</th>
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
                                                @php
                                                $i = 0;
                                                @endphp
                                                @foreach ($penjualans->deliveryorder as $deliveryOrder)
                                                @foreach ($deliveryOrder->produk as $produk)
                                                @if ($produk->jenis != 'TAMBAHAN')
                                                <tr id="row{{ $i }}">
                                                    <td><input type="text" name="no_do1[]" id="no_do_{{ $i }}" class="form-control" value="{{ $deliveryOrder->no_do }}" required></td>
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
                                                                $isSelectedTRD = ($pj->produk->kode == $produk->produk->kode && substr($pj->produk->kode, 0, 3) === 'TRD' && $pj->no_do ==  $deliveryOrder->no_do && $pj->jenis != 'TAMBAHAN');
                                                                $isSelectedGFT = ($pj->produk->kode == $produk->produk->kode && substr($pj->produk->kode, 0, 3) === 'GFT' && $pj->no_do ==  $deliveryOrder->no_do && $pj->jenis != 'TAMBAHAN');
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
                                                            <option value="{{ $produk->id }}" data-harga="{{ $pj->produk->harga_jual }}" {{ $isSelectedTRD || $isSelectedGFT ? 'selected' : '' }}>
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
                                                                <select name="kondisitradproduk_{{ $i }}[]" id="kondisitradproduk_{{ $i }}" data-produk="{{ $selectedTRDKode }}" class="form-control kondisitrad-{{ $i }}">
                                                                    <option value=""> Pilih Kondisi </option>
                                                                    @foreach ($kondisis as $kondisi)
                                                                    <option value="{{ $kondisi->nama }}" {{ $kondisi->nama == $selectedTRDKode ? 'selected' : ''}}>{{ $kondisi->nama }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col">
                                                                <input type="text" name="jumlahtradproduk_{{ $i }}[]" id="jumlahtradproduk_{{ $i }}" class="form-control jumlahtrad-{{ $i }}" placeholder="Kondisi Produk" data-produk="{{ $selectedTRDKode }}" value="{{ $selectedTRDJumlah }}">
                                                            </div>
                                                        </div>
                                                    @elseif($perPendapatan)
                                                        @foreach ($perPendapatan as $noDO => $items)
                                                            @if($noDO == $deliveryOrder->no_do)
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
                                                    <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" class="form-control" data-index="{{ $i }}" value="{{ old('jumlah.' . $i) ?? $produk->jumlah }}" required></td>
                                                    <td><input type="text" name="alasan[]" id="alasan_{{ $i }}" class="form-control" required></td>
                                                    <td>
                                                        <select id="jenis_diskon_{{ $i }}" name="jenis_diskon[]" class="form-control">
                                                        <option value="0">Pilih Diskon</option>
                                                        <option value="Nominal">Nominal</option>
                                                        <option value="persen">Persen</option>
                                                    </select>
                                                    <div>
                                                        <div class="input-group">
                                                            <input type="text" name="diskon[]" id="diskon_{{ $i }}" value="" class="form-control" aria-label="Recipient's username" aria-describedby="basic-addon3" readonly>
                                                            <span class="input-group-text" id="nominalInput_{{ $i }}" style="display:none;">.00</span>
                                                            <span class="input-group-text" id="persenInput_{{ $i }}" style="display:none;">%</span>
                                                        </div>
                                                    </div>
                                                </td>

                                                    <td><input type="text" name="harga[]" id="harga_{{ $i }}" class="form-control" required></td>
                                                    <td><input type="text" name="totalharga[]" id="totalharga_{{ $i }}" class="form-control" required></td>
                                                    <td>
                                                        @if ($i == 0)
                                                        <button type="button" name="remove" id="{{ $i }}" class="btn btn-danger btn_remove">x</button>
                                                        @else
                                                        <button type="button" name="remove" id="{{ $i }}" class="btn btn-danger btn_remove">x</button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @php
                                                $i++;
                                                @endphp
                                                @endif
                                                @endforeach
                                                @endforeach
                                                
                                                
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-around">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2">
                                <div class="form-row row" id="gantiproduk" style="display:none;">
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
                                                <tr>
                                                    <td>
                                                        <select id="nama_produk2_0" name="nama_produk2[]" class="form-control">
                                                            <option value="">Pilih Produk</option>
                                                            @foreach ($juals as $produk)
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
                                                    <td><input type="number" name="jumlah2[]" id="jumlah2_0" class="form-control" ></td>
                                                    <td><input type="text" name="satuan2[]" id="satuan2_0" class="form-control" ></td>
                                                    <td><input type="text" name="keterangan2[]" id="keterangan2_0" class="form-control" ></td>
                                                    <td><button type="button" name="addtambah" id="addtambah" class="btn btn-success">+</button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="row justify-content-around"> -->
                            <!-- <div class="col-md-12 border rounded pt-3 me-1 mt-2"> -->
                                <div class="row">
                                    <div class="col-lg-8 col-sm-6 col-12 border rounded">
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
                                    <!-- <div class="col-lg-3 col-sm-6 col-6 mt-4 ">
                                        
                                    </div> -->
                                    <div class="col-lg-4 float-md-right border rounded">
                                        <div class="total-order">
                                            <ul>
                                                <li>
                                                    <h4>Sub Total</h4>
                                                    <h5><input type="text" id="sub_total" name="sub_total" class="form-control" onchange="calculateTotal(0)" value="0" readonly required></h5>
                                                </li>
                                                <li id="cekretur" style="display:none;">
                                                    <h4>Pengiriman
                                                    <select id="pilih_pengiriman" name="pilih_pengiriman" class="form-control">
                                                        <option value="">Pilih Jenis Pengiriman</option>
                                                        <option value="exspedisi">Ekspedisi</option>
                                                        <option value="sameday">SameDay</option>
                                                    </select>
                                                    </h4>
                                                    <h5>
                                                    <div id="inputOngkir" style="display: none;">
                                                        <!-- <label for="alamat_tujuan">Alamat Tujuan </label> -->
                                                        <input type="text" id="alamat_tujuan" name="alamat_tujuan" class="form-control">
                                                    </div>
                                                    <div id="inputExspedisi" style="display: none;">
                                                        <!-- <label>Alamat Pengiriman</label> -->
                                                        <select id="ongkir_id" name="ongkir_id" class="form-control">
                                                            <option value="">Pilih Alamat Tujuan</option>
                                                            @foreach($ongkirs as $ongkir)
                                                            <option value="{{ $ongkir->id }}" data-biaya_pengiriman="{{ $ongkir->biaya}}">{{ $ongkir->nama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div> 
                                                    </h5>
                                                </li>
                                                <li id="cekretur" style="display:none;">
                                                    <h4>Biaya Ongkir</h4>
                                                    <h5><input type="text " id="biaya_pengiriman" name="biaya_pengiriman" class="form-control" readonly></h5>
                                                </li>
                                                <li>
                                                    <h4>Total</h4>
                                                    <h5><input type="text" id="total" name="total" class="form-control" value="0" readonly required></h5>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <!-- </div>
                        </div> -->

                        <div class="text-end mt-3">
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <a href="{{ route('returpenjualan.index') }}" class="btn btn-secondary" type="button">Back</a>
                        </div>
            </form>
        </div>

    </div>
</div>
</div>

</div>
@endsection

@section('scripts')
<script>
    var cekInvoiceNumbers = "<?php echo $cekInvoice ?>";
    var nextInvoiceNumber = parseInt(cekInvoiceNumbers) + 1;

    function generateDOP() {
        var invoicePrefix = "DOP";
        var currentDate = new Date();
        var year = currentDate.getFullYear();
        var month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
        var day = currentDate.getDate().toString().padStart(2, '0');
        var formattedNextInvoiceNumber = nextInvoiceNumber.toString().padStart(3, '0');

        var generatedInvoice = invoicePrefix + year + month + day + formattedNextInvoiceNumber;
        $('#no_do').val(generatedInvoice);
    }

    generateDOP();
</script>
<script>
    $(document).ready(function() {
        var cekInvoiceNumbers = "<?php echo $cekretur ?>";
        var nextInvoiceNumber = parseInt(cekInvoiceNumbers) + 1;

        // Function to generate the invoice number
        function generateRTP(kode) {
            var currentDate = new Date();
            var year = currentDate.getFullYear();
            var month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
            var day = currentDate.getDate().toString().padStart(2, '0');
            var formattedNextInvoiceNumber = nextInvoiceNumber.toString().padStart(3, '0');

            var generatedInvoice = kode + year + month + day + formattedNextInvoiceNumber;
            $('#no_retur').val(generatedInvoice);
        }

        // Handle location change
        $('#lokasi_id').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var cektipelokasi = selectedOption.data('tipe');
            
            var kode;
            if (cektipelokasi == 1) {
                kode = "RTP";
            } else if (cektipelokasi == 2) {
                kode = "RTO";
            } else {
                kode = ""; // Handle unexpected values of cektipelokasi
            }

            generateRTP(kode); // Generate the invoice number with the selected prefix
        });

        // Optionally, generate the invoice number on page load if a location is pre-selected
        var initialOption = $('#lokasi_id').find('option:selected');
        if (initialOption.val()) {
            var initialTipe = initialOption.data('tipe');
            var initialKode = initialTipe == 1 ? "RTP" : (initialTipe == 2 ? "RTO" : "");
            generateRTP(initialKode);
        }
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

        // $('#pilih_pengiriman').change(function() {
        //         var pengiriman = $(this).val();
        //         var biayaOngkir = parseFloat($('#biaya_pengiriman').val()) || 0;

        //         $('#inputOngkir').hide();
        //         $('#inputExspedisi').hide();

        //         if (pengiriman === "sameday") {
        //             $('#inputOngkir').show();
        //             $('#biaya_pengiriman').prop('readonly', false);
        //         } else if (pengiriman === "exspedisi") {
        //             $('#inputExspedisi').show();
        //             $('#biaya_pengiriman').prop('readonly', true);
        //             ongkirId();
        //         }
        //     });

        // $('#ongkir_id').change(function() {
        //     var selectedOption = $(this).find('option:selected');
        //     var ongkirValue = parseFloat(selectedOption.data('biaya_pengiriman')) || 0;
        //     $('#biaya_pengiriman').val(ongkirValue);
        //     Totaltagihan();
        // });


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

        $('#pilih_pengiriman').change(function() {
            var pengiriman = $(this).val();
            var biayaOngkir = parseFloat($('#biaya_pengiriman').val()) || 0;

            $('#inputOngkir').hide();
            $('#inputExspedisi').hide();

            if (pengiriman === "sameday") {
                $('#inputOngkir').show();
                $('#biaya_pengiriman').prop('readonly', false);
                $('#total').val(0);
                $('#biaya_pengiriman').val(0);
                InputOngkir();
            } else if (pengiriman === "exspedisi") {
                $('#inputExspedisi').show();
                $('#biaya_pengiriman').prop('readonly', true);
                $('#total').val(0);
                $('#biaya_pengiriman').val(0);
                ongkirId();
            }
        });

        function OngkirId(){
            var Ongkir = $(this).val();
            $(this).val(formatRupiah(Ongkir, 'Rp '));
            $('#total_biaya').val(formatRupiah(Ongkir, 'Rp '));
        }
        

        $('#ongkir_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var ongkirValue = parseFloat(selectedOption.data('biaya_pengiriman')) || 0;
            $('#biaya_pengiriman').val(formatRupiah(ongkirValue, 'Rp '));
            Totaltagihan();
        });

        function Totaltagihan() {
            var biayaOngkir = parseFloat(parseRupiahToNumber($('#biaya_pengiriman').val())) || 0;
            var totalTagihan = biayaOngkir;
            $('#biaya_pengiriman').val(formatRupiah(totalTagihan, 'Rp '));
            $('#total').val(formatRupiah(totalTagihan, 'Rp '));
        }

        $('#komplain').on('change', function(){
            var komplain = $(this).val();

            if(komplain == 'retur')
            {
                $('#gantiproduk').show();
                $('[id^=harga_]').each(function() {
                var hargaSatuanInput = $(this);
                var index = hargaSatuanInput.attr('id').split('_')[1];
                var biayakirim = $('#biaya_pengiriman');

                if(komplain == 'retur') {
                    $('#tanggalkirim, #penerima, #driver, #alamat, #bukti_kirim, #biaya_pengiriman, #cekretur').show();
                    biayakirim.prop('readonly', false);
                    hargaSatuanInput.val(0);
                    hargaSatuanInput.prop('readonly', true);
                } else {
                    $('#tanggalkirim, #penerima, #driver, #alamat, #bukti_kirim, #biaya_pengiriman, #cekretur').hide();
                    biayakirim.val(0);
                    biayakirim.prop('readonly', true);
                    var hargaProduk = $('#nama_produk_' + index + ' option:selected').data('harga');
                    var jumlah = $('#jumlah_' + index).val();
                    var harga = hargaProduk * jumlah;
                    hargaSatuanInput.val(harga);
                    hargaSatuanInput.prop('readonly', true);
                }
            });

            $('[id^=totalharga_]').each(function() {
                var totalhargaInput = $(this);
                var index = totalhargaInput.attr('id').split('_')[1];

                if(komplain == 'refund' || komplain == 'diskon') {
                    var hargaSatuan = $('#harga_' + index).val();
                    var jumlah = $('#jumlah_' + index).val();
                    var totalharga = hargaSatuan * jumlah;
                    totalhargaInput.val(totalharga);
                    totalhargaInput.prop('readonly', true); 
                } else if(komplain == 'retur'){
                    totalhargaInput.val(0);
                    totalhargaInput.prop('readonly', true);
                }
            });

            $('[id^=diskon_]').each(function() {
                var diskonInput = $(this);
                var index = diskonInput.attr('id').split('_')[1];

                if(komplain == 'refund') {
                    diskonInput.val(0);
                    diskonInput.prop('readonly', true);
                } else if(komplain == 'diskon') {
                    diskonInput.prop('readonly', false);
                    // showInputType(index);
                } else if(komplain == 'retur'){
                    diskonInput.val(0);
                    diskonInput.prop('readonly', true);
                }
            });

            $('[id^=jumlah_]').each(function() {
                var jumlahInput = $(this);
                var index = jumlahInput.attr('id').split('_')[1];

                if(komplain == 'refund' || komplain == 'diskon') { // Jika refund atau diskon, aktifkan input jumlah
                    jumlahInput.prop('readonly', false);
                } else {
                    jumlahInput.prop('readonly', true); // Jika selain refund atau diskon, nonaktifkan input jumlah
                }
            });

            updateSubTotal();
            } else{
                $('#gantiproduk').hide();
                $('[id^=harga_]').each(function() {
                var hargaSatuanInput = $(this);
                var index = hargaSatuanInput.attr('id').split('_')[1];
                var biayakirim = $('#biaya_pengiriman');

                if(komplain == 'retur') {
                    $('#tanggalkirim, #penerima, #driver, #alamat, #bukti_kirim, #biaya_pengiriman').show();
                    biayakirim.prop('readonly', false);
                    hargaSatuanInput.val(0);
                    hargaSatuanInput.prop('readonly', true);
                } else {
                    $('#tanggalkirim, #penerima, #driver, #alamat, #bukti_kirim, #biaya_pengiriman, #cekretur').hide();
                    biayakirim.val(0);
                    biayakirim.prop('readonly', true);
                    var hargaProduk = $('#nama_produk_' + index + ' option:selected').data('harga');
                    var jumlah = $('#jumlah_' + index).val();
                    var harga = hargaProduk * jumlah;
                    hargaSatuanInput.val(formatRupiah(harga, 'Rp'));
                    hargaSatuanInput.prop('readonly', true);
                }
            });

            $('[id^=totalharga_]').each(function() {
                var totalhargaInput = $(this);
                var index = totalhargaInput.attr('id').split('_')[1];

                if(komplain == 'refund' || komplain == 'diskon') {
                    var hargaSatuan = parseRupiahToNumber($('#harga_' + index).val());
                    var jumlah = $('#jumlah_' + index).val();
                    var totalharga = hargaSatuan * jumlah;
                    totalhargaInput.val(formatRupiah(totalharga, 'Rp '));
                    totalhargaInput.prop('readonly', true); 
                } else if(komplain == 'retur'){
                    totalhargaInput.val(0);
                    totalhargaInput.prop('readonly', true);
                }
            });

            

            $('[id^=diskon_]').each(function() {
                var diskonInput = $(this);
                var index = diskonInput.attr('id').split('_')[1];

                if(komplain == 'refund') {
                    diskonInput.val(0);
                    diskonInput.prop('readonly', true);
                } else if(komplain == 'diskon') {
                    diskonInput.prop('readonly', false);
                    // showInputType(index);
                } else if(komplain == 'retur'){
                    diskonInput.val(0);
                    diskonInput.prop('readonly', true);
                }
            });

            $('[id^=jumlah_]').each(function() {
                var jumlahInput = $(this);
                var index = jumlahInput.attr('id').split('_')[1];

                if(komplain == 'refund' || komplain == 'diskon') { // Jika refund atau diskon, aktifkan input jumlah
                    jumlahInput.prop('readonly', false);
                } else {
                    jumlahInput.prop('readonly', true); // Jika selain refund atau diskon, nonaktifkan input jumlah
                }
            });

            updateSubTotal();
            }
        });

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
                        $('#harga_' + index).val(formatRupiah(harga, 'Rp '));
                        $('#totalharga_' + index).val(formatRupiah(harga, 'Rp '));
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
                subTotal += parseFloat(parseRupiahToNumber($(this).val())) || 0;
            });
            $('#sub_total').val(formatRupiah(subTotal, 'Rp '));
            $('#total').val(formatRupiah(subTotal, 'Rp '));
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

        $('[id^=jenis_diskon_]').on('change', function() {
            var jenisInput = $(this);
            var index = jenisInput.attr('id').split('_')[2]; 
            var selectedValue = jenisInput.val();
            var diskonValue = parseFloat($('#diskon_' + index).val()) || 0; 
            var hargaTotal = parseFloat($('#harga_' + index).val()) || 0; 

            $('[id^=diskon_' + index + ']').trigger('input');
        });

        $('[id^=diskon_]').change('input', function(){
            var hasilInput = $(this);
            var index = hasilInput.attr('id').split('_')[1]; 
            var jenisInput = $('#jenis_diskon_' + index); 
            var selectedValue = jenisInput.val(); 

            var hargaTotal = parseFloat(parseRupiahToNumber($('#harga_' + index).val())) || 0; 
            if (selectedValue === "Nominal") {
                var diskonValue = parseFloat(hasilInput.val()) || 0; // Mengambil nilai diskon berdasarkan index
                hargaTotal -= diskonValue; 
                $(this).val(formatRupiah(diskonValue));
            } else if (selectedValue === "persen") {
                var diskonValue = parseFloat(hasilInput.val()) || 0; // Mengambil nilai diskon berdasarkan index
                var diskonAmount = (hargaTotal * diskonValue) / 100; // Hitung jumlah diskon berdasarkan persen
                hargaTotal -= diskonAmount; // Kurangi harga total dengan jumlah diskon
            }

            $('#totalharga_' + index).val(formatRupiah(hargaTotal, 'Rp '));
            var subtotal = 0;
            $('input[name="totalharga[]"]').each(function() {
                subtotal += parseFloat(parseRupiahToNumber($(this).val())) || 0;
            });

            $('#sub_total').val(formatRupiah(subtotal, 'Rp '));
            $('#total').val(formatRupiah(subtotal, 'Rp '));
        });
        
        $('form').on('submit', function(e){
            $('#biaya_pengiriman').val(parseRupiahToNumber($('#biaya_pengiriman').val()));
            $('#sub_total').val(parseRupiahToNumber($('#sub_total').val()));
            $('#total').val(parseRupiahToNumber($('#total').val()));

            $('input[id^="diskon_"], input[id^="harga_"], input[id^="totalharga_"]').each(function() {
                var id = $(this).attr('id').split('_')[2];
                var value = $(this).val();
                var hargaRupiah = $(this).val();
                $(this).val(parseRupiahToNumber(hargaRupiah));
            });
        });

        $('#biaya_pengiriman').change('input', Totaltagihan);

        

    });
</script>

@endsection