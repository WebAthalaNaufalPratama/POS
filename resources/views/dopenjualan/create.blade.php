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

                        <div class="row justify-content-around">
                            <div class="col-md-6 border rounded pt-3 me-1">
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
                                            <select id="driver" name="driver" class="form-control">
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


                            <div class="col-md-5 border rounded pt-3 ms-1">
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
                                            <label for="file">Bukti Pengiriman</label>
                                            <input type="file" id="file" name="file" class="form-control" aria-describedby="inputGroupPrepend2" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-around">
                            <div class="col-md-12">
                                <label for=""></label>
                                <div class="add-icon text-end">
                                    <button type="button" class="btn btn-primary">Cetak DO</button>
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
                                                                <option value="{{ $produk->kode }}" data-harga="{{ $produk->harga_jual }}">{{ $produk->nama }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="number" name="jumlah[]" id="jumlah_0" oninput="multiply($(this))" class="form-control" required></td>
                                                        <td><input type="text" name="satuan[]" id="satuan_0" class="form-control" required></td>
                                                        <td><input type="text" name="keterangan[]" id="keterangan_0" class="form-control" required></td>
                                                        <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td>
                                                    </tr>
                                                    @else
                                                    @php
                                                    $i = 0;
                                                    @endphp
                                                    @foreach ($penjualans->produk as $produk)
                                                    <tr id="row{{ $i }}">
                                                        <td>
                                                            <select id="nama_produk_{{ $i }}" name="nama_produk[]" class="form-control" required>
                                                                <option value="">Pilih Produk</option>
                                                                @foreach ($produkjuals as $pj)
                                                                <option value="{{ $pj->kode }}" data-tipe_produk="{{ $pj->tipe_produk }}" {{ $pj->kode == $produk->produk->kode ? 'selected' : '' }}>{{ $pj->nama }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" class="form-control" value="{{ old('jumlah.' . $i) ?? $produk->jumlah }}" required></td>
                                                        <td><input type="text" name="satuan[]" id="satuan_{{ $i }}" class="form-control" value="{{ old('satuan.' . $i) ?? 'pcs' }}" required></td>
                                                        <td><input type="text" name="keterangan[]" id="keterangan_{{ $i }}" class="form-control" value="{{ old('ketarangan.' . $i) }}" required></td>
                                                        @if ($i == 0)
                                                        <td><button type="button" name="remove" id="{{ $i }}" class="btn btn-danger btn_remove">x</button></td>
                                                        <!-- <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td> -->
                                                        @else
                                                        <td><button type="button" name="remove" id="{{ $i }}" class="btn btn-danger btn_remove">x</button></td>
                                                        @endif
                                                        @php
                                                        $i++;
                                                        @endphp
                                                    </tr>
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
                                                            <option value="{{ $produk->kode }}">{{ $produk->nama }}</option>
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
    // Function to update date to today's date
    function updateDate(element) {
        var today = new Date().toISOString().split('T')[0];
        element.value = today;
    }

    updateDate(document.getElementById('tanggal_pembuat'));
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
                '<td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">x</button></td>' +
                '</tr>';
            $('#dynamic_field_tambah').append(newrowtambah);
            $('#nama_produk_' + i + ', #jenisdiskon_' + i).select2();
            i++;
        })

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


    });
</script>

@endsection