@extends('layouts.app-von')

@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Mutasi Galery ke Outlet</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.html">Mutasi</a>
                </li>
                <li class="breadcrumb-item active">
                    Galery Ke Outlet
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
            <form action="{{ route('mutasigalery.store') }}" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 border rounded pt-3">
                                <!-- <h5>Informasi Mutasi</h5> -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="pengirim">Nama Pengirim</label>
                                            <select id="pengirim" name="pengirim" class="form-control" required>
                                                <option value="">Pilih Nama Pengirim</option>
                                                @foreach ($lokasipengirim as $lokasi)
                                                <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="penerima">Nama Penerima</label>
                                            <select id="penerima" name="penerima" class="form-control" required>
                                                <option value="">Pilih Nama Penerima</option>
                                                @foreach ($lokasipenerima as $lokasi)
                                                <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="no_mutasi">No Mutasi</label>
                                            <input type="text" id="no_mutasi" name="no_mutasi" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6 border rounded pt-3">
                                <!-- <h5>Informasi Invoice</h5> -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="nama">Tanggal Pengiriman</label>
                                            <input type="date" class="form-control" id="tanggal_kirim" name="tanggal_kirim" placeholder="Tanggal_Invoice" onchange="updateDate(this)" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="nama">Tanggal Diterima</label>
                                            <input type="date" class="form-control" id="tanggal_diterima" name="tanggal_diterima" placeholder="Tanggal_Invoice" onchange="updateDate(this)" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select id="status" name="status" class="form-control" required>
                                                <option value="">Pilih Status</option>
                                                <option value="DRAFT">DRAFT</option>
                                                <option value="PUBLISH">PUBLISH</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-file-container" data-upload-id="myFirstImage">
                                                <label>Bukti<a href="javascript:void(0)" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"></a>
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
                                                    <th>Jumlah Dikirim</th>
                                                    <th>Jumlah Diterima</th>
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
                                                    <td><input type="number" name="jumlah_dikirim[]" id="jumlah_dikirim_0" oninput="multiply($(this))" class="form-control" onchange="calculateTotal(0)"></td>
                                                    <td><input type="number" name="jumlah_diterima[]" id="jumlah_diterima_0" oninput="multiply($(this))" class="form-control" onchange="calculateTotal(0)" readonly></td>
                                                    <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="row justify-content-around">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2"> -->
                                <div class="row">
                                <div class="col-lg-8 col-sm-12 col-12 border radius mt-2">
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
                                    <div class="col-lg-4 float-md-right border radius mt-2">
                                        <div class="total-order">
                                            <ul>
                                                <li>
                                                    <h4>Pengiriman
                                                    <select id="pilih_pengiriman" name="pilih_pengiriman" class="form-control" required>
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
                                                <li>
                                                    <h4>Biaya Ongkir</h4>
                                                    <h5><input type="text" id="biaya_pengiriman" name="biaya_pengiriman" class="form-control" readonly required></h5>
                                                </li>
                                                <li class="total">
                                                    <h4>Total Biaya</h4>
                                                    <h5><input type="text" id="total_biaya" name="total_biaya" class="form-control" readonly required></h5>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <!-- </div>
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
                            <select class="select2 form-control" name="tipe" id="add_tipe" required>
                                <option value="">Pilih Tipe</option>
                                <option value="tradisional">tradisional</option>
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



</div>
@endsection

@section('scripts')
<script>
    var cekInvoiceNumbers = "<?php echo $cekInvoice ?>";
    // console.log(cekInvoiceNumbers);
    var nextInvoiceNumber = parseInt(cekInvoiceNumbers) + 1;

    function generateInvoice() {
        var invoicePrefix = "MGO";
        var currentDate = new Date();
        var year = currentDate.getFullYear();
        var month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
        var day = currentDate.getDate().toString().padStart(2, '0');
        var formattedNextInvoiceNumber = nextInvoiceNumber.toString().padStart(3, '0');

        var generatedInvoice = invoicePrefix + year + month + day + formattedNextInvoiceNumber;
        $('#no_mutasi').val(generatedInvoice);
    }

    $(document).ready(function() {
        generateInvoice();
    });

    function updateDate(element) {
        var today = new Date().toISOString().split('T')[0];
        $(element).val(today);
    }

    // Call the function to set the date to today's date initially
    $(document).ready(function() {
        updateDate($('#tanggal_kirim'));
        updateDate($('#tanggal_diterima'));
        @foreach($produks as $index => $produk)
        updateDate($('#tglrangkai_{{ $index }}'));
        @endforeach
        updateDate($('#tanggalbayar'));
    });

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
                            <td><input type="number" name="jumlah_dikirim[]" id="jumlah_dikirim_${i}" oninput="multiply($(this))" class="form-control" onchange="calculateTotal(0)"></td>
                            <td><input type="number" name="jumlah_diterima[]" id="jumlah_diterima_${i}" oninput="multiply($(this))" class="form-control" onchange="calculateTotal(0)" readonly></td>
                            <td><button type="button" name="remove" id="${i}" class="btn btn-danger btn_remove">x</button></td>
                        </tr>`;

            $('#dynamic_field').append(newRow);

            $('#nama_produk_' + i + ', #jenis_diskon_' + i).select2();
            i++
        });

        $(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
            $('#row' + button_id + '').remove();
            calculateTotal(0);
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

        $('#pilih_pengiriman').change(function() {
            var pengiriman = $(this).val();
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
        });

        $('#biaya_pengiriman').change(function(){
            var Ongkir = $(this).val();
            $(this).val(formatRupiah(Ongkir, 'Rp '));
            $('#total_biaya').val(formatRupiah(Ongkir, 'Rp '));
        });

        $('#ongkir_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var ongkirValue = parseFloat(selectedOption.data('biaya_pengiriman')) || 0;
            $('#biaya_pengiriman').val(formatRupiah(ongkirValue,'Rp '));
            Totaltagihan();
        });

        function Totaltagihan() {
            var biayaOngkir = parseFloat(parseRupiahToNumber($('#biaya_pengiriman').val())) || 0;
            var totalTagihan = biayaOngkir;

            $('#total_biaya').val(formatRupiah(totalTagihan, 'Rp '));
        }

        $('#biaya_pengiriman', '#total_biaya').on('input', Totaltagihan);

        $('form').on('submit', function(e){
            $('#total_biaya').val(parseRupiahToNumber($('#total_biaya').val()));
            $('#biaya_pengiriman').val(parseRupiahToNumber($('#biaya_pengiriman').val()));
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
<!-- <script>
    var cekInvoiceNumbers = "<?php echo $cekInvoice ?>";
    // console.log(cekInvoiceNumbers);
    var nextInvoiceNumber = parseInt(cekInvoiceNumbers) + 1;

    function generateInvoice() {
        var invoicePrefix = "MGO";
        var currentDate = new Date();
        var year = currentDate.getFullYear();
        var month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
        var day = currentDate.getDate().toString().padStart(2, '0');
        var formattedNextInvoiceNumber = nextInvoiceNumber.toString().padStart(3, '0');

        var generatedInvoice = invoicePrefix + year + month + day + formattedNextInvoiceNumber;
        $('#no_mutasi').val(generatedInvoice);
    }

    generateInvoice();
</script>
<script>
    var cekInvoiceNumbers = "0";
    // console.log(cekInvoiceNumbers);
    var nextInvoiceNumber = parseInt(cekInvoiceNumbers) + 1;

    function generateInvoice() {
        var invoicePrefix = "MGO";
        var currentDate = new Date();
        var year = currentDate.getFullYear();
        var month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
        var day = currentDate.getDate().toString().padStart(2, '0');
        var formattedNextInvoiceNumber = nextInvoiceNumber.toString().padStart(3, '0');

        var generatedInvoice = invoicePrefix + year + month + day + formattedNextInvoiceNumber;
        $('#no_invoice_bayar').val(generatedInvoice);
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
    updateDate(document.getElementById('tanggal_kirim'));
    updateDate(document.getElementById('tanggal_diterima'));
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
                            <td><input type="number" name="jumlah_dikirim[]" id="jumlah_dikirim_${i}" oninput="multiply($(this))" class="form-control" onchange="calculateTotal(0)"></td>
                            <td><input type="number" name="jumlah_diterima[]" id="jumlah_diterima_${i}" oninput="multiply($(this))" class="form-control" onchange="calculateTotal(0)" readonly></td>
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
        });

        $('#ongkir_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var ongkirValue = parseFloat(selectedOption.data('biaya_pengiriman')) || 0;
            $('#biaya_pengiriman').val(ongkirValue);
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

        $('#dp').on('input', function() {
            var inputNominal = $(this).val();
            var dpValue = parseFloat($(this).val());

            if (parseInt(inputNominal) > 0) {
                $('#inputPembayaran').show();
                $('#inputRekening').show();
                $('#inputTanggalBayar').show();
                $('#inputBuktiBayar').show();
                $('#nominal').val(dpValue);
                // alert('Nominal pembayaran tidak boleh lebih dari sisa bayar!');
                // $(this).val(0);
            } else {
                $('#inputPembayaran').hide();
                $('#inputRekening').hide();
                $('#inputTanggalBayar').hide();
                $('#inputBuktiBayar').hide();
            }
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

        function updateSubTotal() {
            var subTotalInput = $('#sub_total');
            var hargaTotalInputs = $('input[name="harga_total[]"]');
            var subTotal = 0;

            hargaTotalInputs.each(function() {
                subTotal += parseFloat($(this).val()) || 0;
            });

            subTotalInput.val(subTotal.toFixed(2));
        }

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

        function Totaltagihan() {
            var biayaOngkir = parseFloat($('#biaya_pengiriman').val()) || 0;
            var totalTagihan = biayaOngkir;

            $('#total_biaya').val(totalTagihan.toFixed(2));
            $('#sisa_bayar').val(sisaBayar.toFixed(2));
            $('#jumlah_ppn').val(ppn.toFixed(2));
        }

        $('#biaya_pengiriman', '#total_biaya').on('input', Totaltagihan);
    });
</script> -->

@endsection