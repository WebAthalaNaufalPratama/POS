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
                <div class="row">
                    <div class="col-sm">
                        @csrf

                        <div class="row justify-content-around">
                            <div class="col-md-6 border rounded pt-3 me-1">
                                <h5>Informasi Pelanggan</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="no_retur">No Retur Penjualan</label>
                                            <input type="text" class="form-control" id="no_retur" name="no_retur" placeholder="Nomor Delivery Order" value="{{ $returpenjualans->no_retur}}" readonly required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                        <label for="customer_id">Nama Customer</label>
                                            <select id="customer_id" name="customer_id" class="form-control" disabled>
                                                <option value=""> Pilih Nama Customer </option>
                                                @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}" {{ $returpenjualans->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="lokasi_id">Lokasi</label>
                                            <select id="lokasi_id" name="lokasi_id" class="form-control" requiredn disabled>
                                                <option value=""> Pilih Lokasi </option>
                                                @foreach ($lokasis as $lokasi)
                                                <option value="{{ $lokasi->id }}" {{ $returpenjualans->lokasi_id == $lokasi->id ? 'selected' : '' }}>{{ $lokasi->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group"  id="penerima">
                                            <label for="penerima">Nama Penerima</label>
                                            <input type="text" class="form-control" placeholder="Nama Penerima" name="penerima" id="penerima" value=" @foreach($returpenjualans->deliveryorder as $delivery){{ $delivery->penerima }}@endforeach" disabled>
                                        </div>
                                        <div class="form-group"  id="tanggalkirim">
                                            <label for="tanggal_kirim">Tanggal Kirim</label>
                                            <input type="date" class="form-control" placeholder="Tanggal Kirim" id="tanggal_kirim" name="tanggal_kirim" value="@foreach($returpenjualans->deliveryorder as $delivery){{ $delivery->tanggal_kirim }}@endforeach" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                        <label for="supplier_id">Supplier</label>
                                            <select id="supplier_id" name="supplier_id" class="form-control" disabled>
                                                <option value=""> Pilih Nama Supplier </option>
                                                @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ $returpenjualans->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <form action="{{ route('returpenjualan.update', ['returpenjualan' => $returpenjualans->id]) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('patch')
                                                <div class="custom-file-container" data-upload-id="myFirstImage">
                                                <label>Bukti Retur <a href="javascript:void(0)" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"></a>
                                                </label>
                                                <label class="custom-file-container__custom-file">
                                                    <input type="file" id="bukti" class="custom-file-container__custom-file__custom-file-input" name="bukti" accept="image/*">
                                                    <span class="custom-file-container__custom-file__custom-file-control"></span>
                                                </label>
                                                <span class="text-danger">max 2mb</span>
                                                <img id="preview" src="{{ $returpenjualans->bukti ? '/storage/' . $returpenjualans->bukti : '' }}" alt="your image"/>
                                            </div>
                                            <div class="text-end mt-3">
                                                    <button class="btn btn-primary" type="submit">Upload File</button>
                                                    <!-- <a href="{{ route('do_sewa.index') }}" class="btn btn-secondary" type="button">Back</a> -->
                                                </div>
                                        </form>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-5 border rounded pt-3 ms-1">
                                <h5>Informasi Komplain</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal_invoice">Tanggal Invoice</label>
                                            <input type="date" class="form-control" id="tanggal_invoice" name="tanggal_invoice" placeholder="Nomor Invoice" value="{{ $returpenjualans->tanggal_invoice}}" required disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="tanggal_retur">Tanggal Retur</label>
                                            <input type="date" class="form-control" id="tanggal_retur" name="tanggal_retur" placeholder="Tanggal_retur" value="{{ $returpenjualans->tanggal_retur}}" required disabled>
                                        </div>
                                        <div class="form-group">
                                        <label for="komplain">Komplain</label>
                                            <select id="komplain" name="komplain" class="form-control" disabled>
                                                <option value=""> Pilih Komplain </option>
                                                <option value="refund" {{$returpenjualans->komplain == 'refund' ? 'selected' : ''}}>Refund</option>
                                                <option value="diskon" {{$returpenjualans->komplain == 'diskon' ? 'selected' : ''}}>Diskon</option>
                                                <option value="retur" {{$returpenjualans->komplain == 'retur' ? 'selected' : ''}}>Retur</option>
                                            </select>
                                        </div>
                                        <div class="form-group" id="driver">
                                            <label for="driver">Driver</label>
                                            <select id="driver" name="driver" class="form-control" disabled>
                                                <option value=""> Pilih Driver </option>
                                                @foreach ($drivers as $driver)
                                                <option value="{{ $driver->id }}" @foreach($returpenjualans->deliveryorder as $delivery){{$delivery->driver == $driver->id ? 'selected' : ''}}@endforeach>{{ $driver->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group"  id="alamat">
                                            <label for="alamat">Alamat Pengiriman</label>
                                            <textarea id="alamat" name="alamat" value="@foreach($returpenjualans->deliveryorder as $delivery){{ $delivery->alamat}}@endforeach" disabled>@foreach($returpenjualans->deliveryorder as $delivery){{ $delivery->alamat}}@endforeach</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="no_invoice">No Invoice</label>
                                            <input type="text" class="form-control" id="no_invoice" name="no_invoice" placeholder="Nomor Invoice" value="{{ $returpenjualans->no_invoice}}" required readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="no_do">No Delivery Order</label>
                                            <input type="text" class="form-control" id="no_do" name="no_do" placeholder="Nomor Invoice" value="{{ $returpenjualans->no_do}}" required readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="catatan_komplain">Catatan</label>
                                            <textarea id="catatan_komplain" name="catatan_komplain" value="{{ $returpenjualans->catatan_komplain}}" disabled>{{ $returpenjualans->catatan_komplain}}</textarea>
                                        </div>
                                        <div class="form-group"  id="bukti_kirim">
                                            <div class="custom-file-container" data-upload-id="mySecondImage">
                                                <label>Bukti Kirim <a href="javascript:void(0)" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"></a>
                                                </label>
                                                <label class="custom-file-container__custom-file">
                                                    <input type="file" id="bukti_kirim" class="custom-file-container__custom-file__custom-file-input_2" name="file" accept="image/*">
                                                    <span class="custom-file-container__custom-file__custom-file-control_2"></span>
                                                </label>
                                                <span class="text-danger">max 2mb</span>
                                                <img id="preview_kirim" src="@foreach($returpenjualans->deliveryorder as $delivery){{ $delivery->file ? '/storage/' . $delivery->file : '' }}@endforeach" alt="your image" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-around">
                            <div class="col-md-12">
                                <label for=""></label>
                                <div class="add-icon text-end">
                                    <button type="button" class="btn btn-primary">Cetak Retur</button>
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
                                                @if($returpenjualans->komplain == 'retur')
                                                    @foreach ($penjualans->deliveryorder as $deliveryOrder)
                                                        @foreach ($deliveryOrder->produk_retur as $produk)
                                                            @if ($produk->jenis == null)
                                                                <tr id="row{{ $i }}">
                                                                    <td><input type="text" name="no_do1[]" id="no_do_{{ $i }}" class="form-control" value="{{ $deliveryOrder->no_do }}" required disabled></td>
                                                                    <td>
                                                                        <select id="nama_produk_{{ $i }}" name="nama_produk[]" class="form-control" required readonly>
                                                                            <option value="">Pilih Produk</option>
                                                                            @foreach ($produkjuals as $pj)
                                                                                <option value="{{ $pj->kode }}" data-harga="{{ $pj->harga_jual }}" data-tipe_produk="{{ $pj->tipe_produk }}" {{ $pj->kode == $produk->produk->kode ? 'selected' : '' }}>{{ $pj->nama }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" class="form-control" data-index="{{ $i }}" value="{{ old('jumlah.' . $i) ?? $produk->jumlah }}" required disabled></td>
                                                                    <td><input type="text" name="alasan[]" id="alasan_{{ $i }}" class="form-control" value="{{ old('alasan.' . $i) ?? $produk->alasan}}" required disabled></td>
                                                                    <td>
                                                                        <select id="jenis_diskon_{{ $i }}" name="jenis_diskon[]" class="form-control" disabled>
                                                                            <option value="0">Pilih Diskon</option>
                                                                            <option value="Nominal" {{ $produk->jenis_diskon == 'Nominal' ? 'selected' : '' }}>Nominal</option>
                                                                            <option value="persen" {{ $produk->jenis_diskon == 'persen' ? 'selected' : '' }}>Persen</option>
                                                                        </select>
                                                                        <div>
                                                                            <div class="input-group">
                                                                                <input type="number" name="diskon[]" id="diskon_{{ $i }}" value="{{ $produk->diskon }}" class="form-control" aria-label="Recipient's username" aria-describedby="basic-addon3" readonly disabled>
                                                                                <span class="input-group-text" id="nominalInput_{{ $i }}" style="display:none;">.00</span>
                                                                                <span class="input-group-text" id="persenInput_{{ $i }}" style="display:none;">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td><input type="text" name="harga[]" id="harga_{{ $i }}" class="form-control" value="{{ $produk->harga }}" required disabled></td>
                                                                    <td><input type="text" name="totalharga[]" id="totalharga_{{ $i }}" class="form-control" value="{{ $produk->total_harga }}" required disabled></td>
                                                                    <td>
                                                                        <button type="button" name="remove" id="{{ $i }}" class="btn btn-danger btn_remove">x</button>
                                                                    </td>
                                                                </tr>
                                                                @php
                                                                    $i++;
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                @else
                                                    @foreach ($penjualans->produk_retur as $produk)
                                                        @if ($produk->jenis == null)
                                                            <tr id="row{{ $i }}">
                                                                <td><input type="text" name="no_do1[]" id="no_do_{{ $i }}" class="form-control" value="Tidak Ada DO" required disabled></td>
                                                                <td>
                                                                    <select id="nama_produk_{{ $i }}" name="nama_produk[]" class="form-control" required readonly>
                                                                        <option value="">Pilih Produk</option>
                                                                        @foreach ($produkjuals as $pj)
                                                                            <option value="{{ $pj->kode }}" data-harga="{{ $pj->harga_jual }}" data-tipe_produk="{{ $pj->tipe_produk }}" {{ $pj->kode == $produk->produk->kode ? 'selected' : '' }}>{{ $pj->nama }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" class="form-control" data-index="{{ $i }}" value="{{ old('jumlah.' . $i) ?? $produk->jumlah }}" required disabled></td>
                                                                <td><input type="text" name="alasan[]" id="alasan_{{ $i }}" class="form-control" value="{{ old('alasan.' . $i) ?? $produk->alasan}}" required disabled></td>
                                                                <td>
                                                                    <select id="jenis_diskon_{{ $i }}" name="jenis_diskon[]" class="form-control" disabled>
                                                                        <option value="0">Pilih Diskon</option>
                                                                        <option value="Nominal" {{ $produk->jenis_diskon == 'Nominal' ? 'selected' : '' }}>Nominal</option>
                                                                        <option value="persen" {{ $produk->jenis_diskon == 'persen' ? 'selected' : '' }}>Persen</option>
                                                                    </select>
                                                                    <div>
                                                                        <div class="input-group">
                                                                            <input type="number" name="diskon[]" id="diskon_{{ $i }}" value="{{ $produk->diskon }}" class="form-control" aria-label="Recipient's username" aria-describedby="basic-addon3" readonly>
                                                                            <span class="input-group-text" id="nominalInput_{{ $i }}" style="display:none;">.00</span>
                                                                            <span class="input-group-text" id="persenInput_{{ $i }}" style="display:none;">%</span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td><input type="text" name="harga[]" id="harga_{{ $i }}" class="form-control" value="{{ $produk->harga }}" required disabled></td>
                                                                <td><input type="text" name="totalharga[]" id="totalharga_{{ $i }}" class="form-control" value="{{ $produk->total_harga }}" required disabled></td>
                                                                <td>
                                                                    <button type="button" name="remove" id="{{ $i }}" class="btn btn-danger btn_remove">x</button>
                                                                </td>
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
                                        <tbody id="dynamic_field">
                                            @if(count($returpenjualans->produk_retur) < 1) <tr>
                                                <td>
                                                    <select id="nama_produk_0" name="nama_produk[]" class="form-control" disabled>
                                                        <option value="">Pilih Produk</option>
                                                        @foreach ($produkjuals as $produk)
                                                        <option value="{{ $produk->kode }}" >{{ $produk->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="number" name="jumlah[]" id="jumlah_0" class="form-control"></td>
                                                <td><input type="number" name="satuan[]" id="satuan_0" class="form-control"></td>
                                                <td><input type="number" name="keterangan[]" id="keterangan_0" class="form-control"></td>
                                                </tr>
                                                @else
                                                @php
                                                $i = 0;
                                                @endphp
                                                @foreach ($returpenjualans->produk_retur as $produk)
                                                @if ($produk->jenis == 'RETUR')
                                                <tr id="row{{ $i }}">
                                                    <td>
                                                        <select id="nama_produk_{{ $i }}" name="nama_produk[]" class="form-control" disabled>
                                                            <option value="">Pilih Produk</option>
                                                            @foreach ($produkjuals as $pj)
                                                            <option value="{{ $pj->kode }}" data-tipe_produk="{{ $pj->tipe_produk }}" {{ $pj->kode == $produk->produk->kode ? 'selected' : '' }}>{{ $pj->nama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" class="form-control" value="{{ $produk->jumlah }}" disabled></td>
                                                    <td><input type="text" name="satuan[]" id="satuan_{{ $i }}" class="form-control" value="{{ $produk->satuan }}" disabled></td>
                                                    <td><input type="text" name="keterangan[]" id="ketarangan_{{ $i }}" class="form-control" value="{{ $produk->keterangan }}" disabled></td>
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
                                    <div class="col-lg-3 col-sm-6 col-12 mt-4">
                                        
                                    </div>
                                    <div class="col-lg-3 col-sm-6 col-6 mt-4 ">
                                        
                                    </div>
                                    <div class="col-lg-6 float-md-right">
                                        <div class="total-order">
                                            <ul>
                                                <li>
                                                    <h4>Sub Total</h4>
                                                    <h5><input type="text" id="sub_total" name="sub_total" class="form-control" onchange="calculateTotal(0)" value="{{ $returpenjualans->sub_total}}" readonly required></h5>
                                                </li>
                                                <li>
                                                    <h4 id="biaya_pengiriman">Biaya Pengiriman</h4>
                                                    <h5><input type="text" id="biaya_pengiriman" name="biaya_pengiriman" class="form-control" value="{{ $returpenjualans->biaya_pengiriman}}" disabled></h5>
                                                </li>
                                                <li>
                                                    <h4>Total</h4>
                                                    <h5><input type="text" id="total" name="total" class="form-control" value="{{ $returpenjualans->total}}" readonly required></h5>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <!-- <button class="btn btn-primary" type="submit">Submit</button> -->
                            <a href="{{ route('returpenjualan.index') }}" class="btn btn-secondary" type="button">Back</a>
                        </div>
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
    var cekInvoiceNumbers = "<?php echo $cekretur ?>";
    var nextInvoiceNumber = parseInt(cekInvoiceNumbers) + 1;

    function generateRTP() {
        var invoicePrefix = "RTP";
        var currentDate = new Date();
        var year = currentDate.getFullYear();
        var month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
        var day = currentDate.getDate().toString().padStart(2, '0');
        var formattedNextInvoiceNumber = nextInvoiceNumber.toString().padStart(3, '0');

        var generatedInvoice = invoicePrefix + year + month + day + formattedNextInvoiceNumber;
        $('#no_retur').val(generatedInvoice);
    }

    generateRTP();
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
                '@foreach ($produkjuals as $index => $produk)' +
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

            @foreach ($produkjuals as $produk)
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
        $(document).on('change', '[id^=nama_produk]', function() {
            var id = $(this).attr('id').split('_')[2]; // Ambil bagian angka ID
            var selectedOption = $(this).find(':selected');
            $('#kode_produk_' + id).val(selectedOption.data('kode'));
            $('#tipe_produk_' + id).val(selectedOption.data('tipe'));
            $('#deskripsi_komponen_' + id).val(selectedOption.data('deskripsi'));
            updateHargaSatuan(this);
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
                    $('#tanggalkirim, #penerima, #driver, #alamat, #bukti_kirim, #biaya_pengiriman').show();
                    biayakirim.prop('readonly', false);
                    hargaSatuanInput.val(0);
                    hargaSatuanInput.prop('readonly', true);
                } else {
                    $('#tanggalkirim, #penerima, #driver, #alamat, #bukti_kirim, #biaya_pengiriman').hide();
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
                    $('#tanggalkirim, #penerima, #driver, #alamat, #bukti_kirim, #biaya_pengiriman').hide();
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
            }
        });

        var jumlahDO = [];

        

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

    });
</script>

@endsection