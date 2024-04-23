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
            <form action="{{ route('dopenjualan.store') }}" method="POST">
                <div class="row">
                    <div class="col-sm">
                        @csrf

                        <div class="row justify-content-around">
                            <div class="col-md-6 border rounded pt-3 me-1">
                                <h5>Informasi Pelanggan</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="nama">No Delivery Order</label>
                                            <input type="text" class="form-control" id="no_dop" name="no_dop" placeholder="Nomor Delivery Order" value="" onchange="generateDOP(this)" readonly required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="nama_customer">Penerima</label>
                                            <select id="nama_customer" name="nama_customer" class="form-control">
                                                <option value="">Pilih Nama Penerima</option>
                                                @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}" data-point="{{ $customer->poin_loyalty }}" data-hp="{{ $customer->handphone }}">{{ $customer->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama">No Hp/Wa</label>
                                            <input type="text" class="form-control" id="nohandphone" name="nohandphone" placeholder="Nomor Handphone" value="" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="lokasi_beli">Driver</label>
                                            <select id="lokasi_beli" name="lokasi_beli" class="form-control">
                                                <option value=""> Pilih Nama Driver </option>
                                                @foreach ($lokasis as $lokasi)
                                                <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama">Alamat</label>
                                            <input type="text" class="form-control" id="nohandphone" name="nohandphone" placeholder="Nomor Handphone" value="" required>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-5 border rounded pt-3 ms-1">
                                <h5>Informasi Pesanan</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="no_invoice">Nomor Invoice</label>
                                            <input type="text" class="form-control" id="no_invoice" name="no_invoice" placeholder="Nomor Invoice" onchange="generateInvoice(this)" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="nama">Tanggal Kirim</label>
                                            <input type="date" class="form-control" id="tanggal_kirim" name="tanggal_kirim" placeholder="Tanggal_kirim" onchange="updateDate(this)" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Catatan</label>
                                            <textarea id="catatan" name="catatan"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama">Tanggal Invoice</label>
                                            <input type="date" class="form-control" id="tanggal_invoice" name="tanggal_invoice" placeholder="Tanggal_Invoice" onchange="updateDate(this)" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="sales">Pengirim</label>
                                            <select id="sales" name="sales" class="form-control">
                                                <option value="">Pilih Nama Pengirim</option>
                                                @foreach ($karyawans as $karyawan)
                                                <option value="{{ $karyawan->id }}">{{ $karyawan->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="buktikirim">Bukti Pengiriman</label>
                                            <input type="file" id="buktikirim" name="buktikirim" class="form-control" aria-describedby="inputGroupPrepend2" required>
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
                                                    <td><input type="number" name="jumlah[]" id="jumlah_0" oninput="multiply($(this))" class="form-control"></td>
                                                    <td><input type="text" name="unit_satuan[]" id="unit_satuan_0" class="form-control"></td>
                                                    <td><input type="text" name="keterangan[]" id="keterangan_0" class="form-control"></td>
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
                                                    <td><input type="number" name="jumlah[]" id="jumlah_0" oninput="multiply($(this))" class="form-control"></td>
                                                    <td><input type="text" name="unit_satuan[]" id="unit_satuan_0" class="form-control"></td>
                                                    <td><input type="text" name="keterangan[]" id="keterangan_0" class="form-control"></td>
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
    var used = [1001, 1003, 1005];
    function generateDOP() {
        var dopPrefix = "DOP";
        var currentDate = new Date();
        var year = currentDate.getFullYear();
        var month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
        var day = currentDate.getDate().toString().padStart(2, '0');
        var nextDOP = 1;
        while (used.includes(nextDOP)) {
            nextDOP++;
        }

        var generatedDOP = dopPrefix + year +month +day +nextDOP ;

        document.getElementById('no_dop').value =generatedDOP;
    }

    generateDOP();

    </script>
<script>
    // Function to update date to today's date
    function updateDate(element) {
        var today = new Date().toISOString().split('T')[0];
        element.value = today;
    }

    // Call the function to set the date to today's date initially
    updateDate(document.getElementById('tanggal_invoice'));
    updateDate(document.getElementById('tanggal_kirim'));
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
    function calculateTotal(index) {
        var diskonType = document.getElementById('jenisdiskon_' + index).value;
        var diskonValue = parseFloat(document.getElementById('diskon_' + index).value);
        var hargaTotal = parseFloat(document.getElementById('harga_total_' + index).value);

        if (!isNaN(hargaTotal)) {
            if (diskonType === "Nominal" && !isNaN(diskonValue)) {
                hargaTotal -= diskonValue;
            } else if (diskonType === "persen" && !isNaN(diskonValue)) {
                hargaTotal -= (hargaTotal * diskonValue / 100);
            }
        }

        // Update the total price field
        document.getElementById('harga_total_' + index).value = hargaTotal.toFixed(2);
    }
</script>

<script>
    $(document).ready(function() {
        var i = 1;
        $('#add').click(function() {
            var newRow = '<tr class="tr_clone" id="row' + i + '">' +
                '<td>' +
                '<select id="nama_produk_' + i + '" name="nama_produk[]" class="form-control select2">' +
                '<option value="">Pilih Produk</option>' +
                '@foreach ($produks as $index => $produk)' +
                '<option value="{{ $produk->id }}" data-harga="{{ $produk->harga_jual }}" data-kode="{{ $produk->kode }}" data-tipe="{{ $produk->tipe }}" data-deskripsi="{{ $produk->deskripsi }}">{{ $produk->nama }}</option>' +
                '@endforeach' +
                '</select>' +
                '<input type="hidden" name="kode_produk[]" id="kode_produk_' + i + '" style="display: none;">' +
                '<input type="hidden" name="tipe_produk[]" id="tipe_produk_' + i + '" style="display: none;">' +
                '<input type="hidden" name="deskripsi_komponen[]" id="deskripsi_komponen_' + i + '" style="display: none;">' +
                '</td>' +
                '<td><input type="number" name="jumlah[]" id="jumlah_' + i + '" oninput="multiply(this)" class="form-control"></td>' +
                '<td><input type="text" name="unit_satuan[]" id="unit_satuan_' + i + '" class="form-control"></td>' +
                '<td><input type="text" name="keterangan[]" id="keterangan_' + i + '" class="form-control"></td>' +
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
                '<select id="nama_produk_' + i + '" name="nama_produk[]" class="form-control select2">' +
                '<option value="">Pilih Produk</option>' +
                '@foreach ($produks as $index => $produk)' +
                '<option value="{{ $produk->id }}" data-harga="{{ $produk->harga_jual }}" data-kode="{{ $produk->kode }}" data-tipe="{{ $produk->tipe }}" data-deskripsi="{{ $produk->deskripsi }}">{{ $produk->nama }}</option>' +
                '@endforeach' +
                '</select>' +
                '<input type="hidden" name="kode_produk[]" id="kode_produk_' + i + '" style="display: none;">' +
                '<input type="hidden" name="tipe_produk[]" id="tipe_produk_' + i + '" style="display: none;">' +
                '<input type="hidden" name="deskripsi_komponen[]" id="deskripsi_komponen_' + i + '" style="display: none;">' +
                '</td>' +
                '<td><input type="number" name="jumlah[]" id="jumlah_' + i + '" oninput="multiply(this)" class="form-control"></td>' +
                '<td><input type="text" name="unit_satuan[]" id="unit_satuan_' + i + '" class="form-control"></td>' +
                '<td><input type="text" name="keterangan[]" id="keterangan_' + i + '" class="form-control"></td>' +
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

            // Isi nilai subtotal dengan nilai total yang sudah dihitung
            $('#subTotal').val(total);
        }

        function hitungSisaBayar() {
            var totalTagihan = parseFloat($('#totaltagihan').val()) || 0; // Mengambil nilai total tagihan, default 0 jika kosong
            var dp = parseFloat($('#dp').val()) || 0; // Mengambil nilai DP, default 0 jika kosong

            var sisaBayar = totalTagihan - dp;

            $('#sisa').val(sisaBayar.toFixed(2)); // Mengisi nilai sisa bayar ke dalam input dengan id 'sisa', dengan pembulatan 2 angka di belakang koma
        }

        // Panggil fungsi hitungSisaBayar saat nilai total tagihan atau DP berubah
        $('#totaltagihan, #dp').on('input', hitungSisaBayar);


        function updateSubTotal() {
            var subTotalInput = $('#subTotal');
            var hargaTotalInputs = $('input[name="harga_total[]"]');
            var subTotal = 0;

            hargaTotalInputs.each(function() {
                subTotal += parseFloat($(this).val()) || 0;
            });

            subTotalInput.val(subTotal.toFixed(2));
        }


    });
</script>

@endsection