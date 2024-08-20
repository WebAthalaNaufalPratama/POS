@extends('layouts.app-von')

@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Delivery Order</h3>
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
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">
                Delivery Order Penjualan
            </h4>
        </div>
        <div class="card-body">
            <form action="{{ route('dopenjualan.store', ['penjualan' => $penjualans->id]) }}" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm">
                        @csrf

                        <div class="row ">
                            <div class="col-md-6 border rounded pt-3">
                                <h5>Informasi Pelanggan</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="no_do">No Delivery Order</label>
                                            <input type="text" class="form-control" id="no_do" name="no_do" placeholder="Nomor Delivery Order" value="" onchange="generateDOP(this)" readonly required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="penerima">Penerima</label>
                                            <input type="text" class="form-control" id="penerima" name="penerima" placeholder="Masukan Nama Penerima" value="" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="handphone">No Hp/Wa</label>
                                            <input type="text" class="form-control" id="handphone" name="handphone" placeholder="Nomor Handphone" value="" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="driver">Driver</label>
                                            <select id="driver" name="driver" class="form-control" required>
                                                <option value=""> Pilih Nama Driver </option>
                                                @foreach ($karyawans as $karyawan)
                                                <option value="{{ $karyawan->id }}">{{ $karyawan->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="alamat">Alamat</label>
                                            <textarea type="text" class="form-control" id="alamat" name="alamat" placeholder="Alamat" value="" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6 border rounded pt-3 ">
                                <h5>Informasi Pesanan</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="no_referensi">Nomor Invoice</label>
                                            <input type="text" class="form-control" id="no_referensi" name="no_referensi" placeholder="Nomor Invoice" value="{{ $penjualans->no_invoice}}" required readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="tanggal_kirim">Tanggal Kirim</label>
                                            <input type="date" class="form-control" id="tanggal_kirim" name="tanggal_kirim" placeholder="Tanggal_kirim" onchange="updateDate(this)" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="catatan">Catatan</label>
                                            <textarea id="catatan" name="catatan"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select id="status" name="status" class="form-control" required>
                                                <option value="">Pilih Status</option>
                                                <option value="TUNDA">TUNDA</option>
                                                <!-- <option value="DIKONFIRMASI">DIKONFRIMASI</option> -->
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama">Tanggal Invoice</label>
                                            <input type="date" class="form-control" id="tanggal_pembuat" name="tanggal_pembuat" placeholder="Tanggal Invoice" onchange="updateDate(this)" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="customer_id">Pengirim</label>
                                            <select id="customer_id" name="customer_id" class="form-control" value="{{ $penjualans->id_customer}}" readonly>
                                                <!-- <option value="">Pilih Nama Pengirim</option> -->
                                                @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-file-container" data-upload-id="myFirstImage">
                                                <label>Bukti Kirim <a href="javascript:void(0)" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"></a>
                                                </label>
                                                <label class="custom-file-container__custom-file">
                                                    <input type="file" id="bukti" class="custom-file-container__custom-file__custom-file-input" name="file" accept="image/*">
                                                    <span class="custom-file-container__custom-file__custom-file-control"></span>
                                                </label>
                                                <span class="text-danger">max 2mb</span>
                                                <img id="preview" />
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
                                        <h5>Rincian Produk</h5>
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
                                                @if(count($penjualans->produk) < 1) <tr>
                                                    <tr>
                                                        <td>
                                                            <select id="nama_produk_0" name="nama_produk[]" class="form-control" required>
                                                                <option value="">Pilih Produk</option>
                                                                @foreach ($produkjuals as $produk)
                                                                <option value="{{ $produk->id }}" data-harga="{{ $produk->harga_jual }}">{{ $produk->nama }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="number" name="jumlah[]" id="jumlah_0" class="form-control" required></td>
                                                        <td><input type="text" name="satuan[]" id="satuan_0" class="form-control" required></td>
                                                        <td><input type="text" name="keterangan[]" id="keterangan_0" class="form-control" required></td>
                                                        <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td>
                                                    </tr>
                                                    @else
                                                    @php
                                                    $i = 0;
                                                    @endphp
                                                    @if($penjualans->auditor_id != null && $penjualans->dibukukan_id !== null)
                                                    @foreach ($penjualans->produk as $produk)
                                                    @if($produk->jumlah_dikirim != 0)
                                                    <tr id="row{{ $i }}">
                                                        <td>
                                                            <select id="nama_produk_{{ $i }}" name="nama_produk[]" class="form-control" required readonly>
                                                                <option value="">Pilih Produk</option>
                                                                @foreach ($produkjuals as $pj)
                                                                <option value="{{ $produk->id }}" data-tipe_produk="{{ $pj->tipe_produk }}" {{ $pj->kode == $produk->produk->kode ? 'selected' : '' }}>
                                                                    @if (substr($produk->produk->kode, 0, 3) === 'TRD')
                                                                        {{ $pj->nama }}
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
                                                                        - {{$komponen->jumlah}}
                                                                    @elseif (substr($produk->produk->kode, 0, 3) === 'GFT')
                                                                        {{ $pj->nama }}
                                                                        @foreach ($produk->komponen as $komponen)
                                                                            - ( {{$komponen->nama_produk}}
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
                                                                            - {{$komponen->jumlah}} )
                                                                        @endforeach
                                                                    @endif
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" class="form-control jumlah" value="{{ old('jumlah.' . $i) ?? $produk->jumlah_dikirim }}" data-produk-id="{{ $produk->id }}" required></td>
                                                        <td><input type="text" name="satuan[]" id="satuan_{{ $i }}" class="form-control" value="{{ old('satuan.' . $i) ?? 'pcs' }}" required></td>
                                                        <td><input type="text" name="keterangan[]" id="keterangan_{{ $i }}" class="form-control" value="{{ old('ketarangan.' . $i) }}" required></td>
                                                        @if ($i == 0)
                                                        <td><button type="button" name="remove" id="{{ $i }}" class="btn btn_remove"><img src="/assets/img/icons/delete.svg" alt="svg"></button></td>
                                                        <!-- <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td> -->
                                                        @else
                                                        <td><button type="button" name="remove" id="{{ $i }}" class="btn btn_remove"><img src="/assets/img/icons/delete.svg" alt="svg"></button></td>
                                                        @endif
                                                        @php
                                                        $i++;
                                                        @endphp
                                                    </tr>
                                                    @endif 
                                                    @endforeach
                                                    @endif
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
                                        <h5>Tambahan Produk</h5>
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
                                                            @foreach ($produkjuals as $produk)
                                                            <option value="{{ $produk->kode }}">
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
                                                    <td><button type="button" name="addtambah" id="addtambah" class="btn"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="row justify-content-around"> -->
                            <!-- <div class="col-md-12 border rounded pt-3 me-1 mt-2"> -->
                            <div class="row justify-content-center mt-4 ">
                                <div class="col-lg-6 col-sm-12 border rounded pt-2 pb-2">
                                            <!-- <div class="col-lg-12"> -->
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
                                                            <td><input type="date" class="form-control" name="tanggal_pembuat" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"></td>
                                                            <td>-</td>
                                                            <td>-</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <!-- </div> -->

                        <div class="text-end mt-3">
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <a href="{{ route('dopenjualan.index') }}" class="btn btn-secondary" type="button">Back</a>
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
    $(document).ready(function() {
        $('#delivery_order_section').show();

        $('#distribusi').change(function() {
            if ($(this).val() === 'Diambil') {
                $('#delivery_order_section').hide();
            } else {
                $('#delivery_order_section').show();
            }
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('nama_customer').addEventListener('change', function() {
            var pointInput = document.getElementById('point');
            var selectedOption = this.options[this.selectedIndex];
            var pointValue = selectedOption.getAttribute('data-point');
            pointInput.value = pointValue;
            var hpInput = document.getElementById('nohandphone');
            var selectedOption = this.options[this.selectedIndex];
            var hpValue = selectedOption.getAttribute('data-hp');
            hpInput.value = hpValue;
        });
    });
</script>
<script>
    $(document).ready(function() {
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
    var usedInvoiceNumbers = [1001, 1003, 1005];
    // Function to generate the invoice based on certain criteria
    function generateInvoice() {
        var invoicePrefix = "INV";
        var currentDate = new Date();
        var year = currentDate.getFullYear();
        var month = (currentDate.getMonth() + 1).toString().padStart(2, '0'); // Adding leading zero if necessary
        var day = currentDate.getDate().toString().padStart(2, '0'); // Adding leading zero if necessary
        var nextInvoiceNumber = 2;
        while (usedInvoiceNumbers.includes(nextInvoiceNumber)) {
            nextInvoiceNumber++;
        }
        var generatedInvoice = invoicePrefix + year + month + day + nextInvoiceNumber;

        // Update the value of the invoice input field
        document.getElementById('no_invoice').value = generatedInvoice;
    }

    // Call the function to generate the invoice when the page loads
    generateInvoice();
</script>
<script>
    $(document).ready(function() {
        // Retrieve and parse the next invoice number
        var cekInvoiceNumbers = "<?php echo $cekInvoice ?>";
        var nextInvoiceNumber = parseInt(cekInvoiceNumbers) + 1;

        // Function to generate the invoice number
        function generateInvoice(kode) {
            var currentDate = new Date();
            var year = currentDate.getFullYear();
            var month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
            var day = currentDate.getDate().toString().padStart(2, '0');
            var formattedNextInvoiceNumber = nextInvoiceNumber.toString().padStart(3, '0');

            var generatedInvoice = kode + year + month + day + formattedNextInvoiceNumber;
            $('#no_do').val(generatedInvoice);
        }

        // Get the location type from the server-rendered value
        var cektipelokasi = "{{ $penjualans->lokasi->tipe_lokasi }}";

        // console.log(cektipelokasi);

        // Determine the prefix based on the location type
        var kode;
        if (cektipelokasi == 1) {
            kode = "DOP";
        } else if (cektipelokasi == 2) {
            kode = "DVO";
        } else {
            kode = ""; // Handle unexpected values of cektipelokasi
        }

        // Generate the invoice number with the determined prefix
        generateInvoice(kode);
    });
</script>
<script>
    // Function to update date to today's date
    function updateDate(element) {
        var today = new Date().toISOString().split('T')[0];
        element.value = today;
    }

    updateDate(document.getElementById('tanggal_pembuat'));
    updateDate(document.getElementById('tanggal_kirim'));
</script>
<script>
    var produkData = [];

    @foreach ($penjualans->produk as $produk)
        produkData.push({
            id: {{ $produk->id }},
            jumlah: {{ $produk->jumlah_dikirim }}
        });
    @endforeach

    // console.log('Produk Data:', produkData);

    $(document).on('input', '.jumlah', function() {
        var inputId = $(this).attr('id');
        var jumlah = parseInt($(this).val(), 10); // Ensure jumlah is parsed as an integer
        var produkId = $(this).data('produk-id'); // Extract the product ID from the data attribute

        var produk = produkData.find(function(item) {
            return item.id == produkId;
        });

        if (produk) {
            if (jumlah > produk.jumlah) {
                alert('Jumlah diterima tidak boleh lebih dari jumlah dikirim');
                $(this).val(produk.jumlah);
            } else if (jumlah < 0) {
                alert('Jumlah diterima tidak boleh kurang dari 0');
                $(this).val(0);
            }
        } else {
            console.error('Produk not found for ID:', produkId);
        }
    });
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
                '<td><button type="button" name="remove" id="' + i + '" class="btn btn_remove"><img src="/assets/img/icons/delete.svg" alt="svg"></button></td>' +
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
            i++;
        });

        $('#addtambah').click(function() {
            var newrowtambah = '<tr class="tr_clone" id="row_tambah' + i + '">' +
                '<td>' +
                '<select id="nama_produk2_' + i + '" name="nama_produk2[]" class="form-control select2">' +
                '<option value="">Pilih Produk</option>' +
                '@foreach ($produkjuals as $index => $produk)' +
                '<option value="{{ $produk->kode }}" data-harga="{{ $produk->harga_jual }}" data-kode="{{ $produk->kode }}" data-tipe="{{ $produk->tipe }}" data-deskripsi="{{ $produk->deskripsi }}">{{ $produk->nama }}</option>' +
                '@endforeach' +
                '</select>' +
                '<input type="hidden" name="kode_produk[]" id="kode_produk_' + i + '" style="display: none;">' +
                '<input type="hidden" name="tipe_produk[]" id="tipe_produk_' + i + '" style="display: none;">' +
                '<input type="hidden" name="deskripsi_komponen[]" id="deskripsi_komponen_' + i + '" style="display: none;">' +
                '</td>' +
                '<td><input type="number" name="jumlah2[]" id="jumlah2_' + i + '" oninput="multiply(this)" class="form-control"></td>' +
                '<td><input type="text" name="satuan2[]" id="satuan2_' + i + '" class="form-control"></td>' +
                '<td><input type="text" name="keterangan2[]" id="keterangan2_' + i + '" class="form-control"></td>' +
                '<td><button type="button" name="remove" id="' + i + '" class="btn btn_remove_tambah"><img src="/assets/img/icons/delete.svg" alt="svg"></button></td>' +
                '</tr>';
            $('#dynamic_field_tambah').append(newrowtambah);
            $('#nama_produk_' + i + ', #jenisdiskon_' + i).select2();
            i++;
        });

        function updateIndicesProduk() {
            $('#dynamic_field tr').each(function(index) {
                var newId = 'row' + index;
                $(this).attr('id', newId);
                $(this).find('[id^="nama_produk_"]').attr('id', 'nama_produk_' + index).attr('name', 'nama_produk[]').attr('data-index', index);
                $(this).find('[id^="jumlah_"]').attr('id', 'jumlah_' + index).attr('name', 'jumlah[]').attr('data-index', index);
                $(this).find('[id^="satuan_"]').attr('id', 'satuan_' + index).attr('name', 'satuan[]').attr('data-index', index);
                $(this).find('[id^="keterangan_"]').attr('id', 'keterangan_' + index).attr('name', 'keterangan[]').attr('data-index', index);
                $(this).find('.btn_remove').attr('id', index);
            });
        }

        function updateIndicesProdukTambahan() {
            var i = 1;

            $('#dynamic_field_tambah tr.tr_clone').each(function() {
                $(this).attr('id', 'row_tambah' + i);
                $(this).find('[id^="nama_produk2_"]').attr('id', 'nama_produk2_' + i).attr('name', 'nama_produk2[]').attr('data-index', i);
                $(this).find('[id^="jumlah2_"]').attr('id', 'jumlah2_' + i).attr('name', 'jumlah2[]').attr('data-index', i);
                $(this).find('[id^="satuan2_"]').attr('id', 'satuan2_' + i).attr('name', 'satuan2[]').attr('data-index', i);
                $(this).find('[id^="keterangan2_"]').attr('id', 'keterangan2_' + i).attr('name', 'keterangan2[]').attr('data-index', i);
                $(this).find('.btn_remove_tambah').attr('id', 'remove_tambah_' + i); // Pastikan ID tombol penghapusan diupdate dengan benar
                i++;
            });
        }

        $(document).on('click', '#dynamic_field .btn_remove', function() {
            var button_id = $(this).attr('id');
            if ($('#dynamic_field tr').length <= 1) {
                alert('Mohon Jangan Biarkan Data Delivery Order Kosong');
            } else {
                $('#row' + button_id).remove();
                updateIndicesProduk();
            }
        });

        $(document).on('click', '#dynamic_field_tambah .btn_remove_tambah', function() {
            var button_id = $(this).attr('id').split('_').pop(); // Ambil indeks dari ID tombol penghapusan
            $('#row_tambah' + button_id).remove();
            updateIndicesProdukTambahan();
            i = 0;
            $('#dynamic_field_tambah tr').each(function() {
                $(this).attr('id', 'row_tambah' + i);
                $(this).find('[id^="nama_produk2_"]').attr('id', 'nama_produk2_' + i).attr('name', 'nama_produk2[]').attr('data-index', i);
                $(this).find('[id^="jumlah2_"]').attr('id', 'jumlah2_' + i).attr('name', 'jumlah2[]').attr('data-index', i);
                $(this).find('[id^="satuan2_"]').attr('id', 'satuan2_' + i).attr('name', 'satuan2[]').attr('data-index', i);
                $(this).find('[id^="keterangan2_"]').attr('id', 'keterangan2_' + i).attr('name', 'keterangan2[]').attr('data-index', i);
                $(this).find('.btn_remove_tambah').attr('id', 'remove_tambah_' + i); // Update ID tombol penghapusan dengan benar
                i++;
            });
        });

        $('[id^=nama_produk_]').on('mousedown click focus', function(e) {
            e.preventDefault();
        });


        $(document).on('change', '[id^=nama_produk]', function() {
            var id = $(this).attr('id').split('_')[2]; 
            var selectedOption = $(this).find(':selected');
            $('#kode_produk_' + id).val(selectedOption.data('kode'));
            $('#tipe_produk_' + id).val(selectedOption.data('tipe'));
            $('#deskripsi_komponen_' + id).val(selectedOption.data('deskripsi'));
            updateHargaSatuan(this);
        });
        $('#bukti').on('change', function() {
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
            $('#bukti').val('');
            $('#preview').attr('src', defaultImg);
        };

    });
</script>

@endsection