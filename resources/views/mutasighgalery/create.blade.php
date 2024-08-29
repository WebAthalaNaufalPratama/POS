@extends('layouts.app-von')

@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Mutasi GreenHouse ke Galery</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.html">Mutasi</a>
                </li>
                <li class="breadcrumb-item active">
                    GreenHouse Ke Galery
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">
                mutasi Barang
            </h4>
        </div>
        <div class="card-body">
            <form action="{{ route('mutasighgalery.store') }}" method="POST" enctype="multipart/form-data">
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
                                                <option value="{{ $lokasi->id }}" data-tipe-lokasi="{{ $lokasi->tipe_lokasi }}">{{ $lokasi->nama }}</option>
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
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="rekening_id">Rekening</label>
                                            <select id="rekening_id" name="rekening_id" class="form-control" required>
                                                <option value="">Pilih Rekening</option>
                                                @foreach ($bankpens as $rekening)
                                                <option value="{{ $rekening->id }}">{{ $rekening->bank }} -{{ $rekening->nama_akun}}({{$rekening->nomor_rekening}})</option>
                                                @endforeach
                                            </select>
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
                                                <option value="TUNDA">TUNDA</option>
                                                <!-- <option value="DIKONFIRMASI">DIKONFIRMASI</option> -->
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-file-container" data-upload-id="myFirstImage">
                                                <label>Bukti<a href="javascript:void(0)" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"></a>
                                                </label>
                                                <label class="custom-file-container__custom-file">
                                                    <input type="file" id="bukti_file" class="custom-file-container__custom-file__custom-file-input" name="bukti" accept="image/*">
                                                    <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
                                                    <span class="custom-file-container__custom-file__custom-file-control"></span>
                                                </label>
                                                <span class="text-danger">max 2mb</span>
                                                <div class="custom-file-container__image-preview"></div>
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
                                                    <!-- <th>Jumlah Diterima</th> -->
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="dynamic_field">
                                                <tr>
                                                    <td>
                                                        <select id="nama_produk_0" name="nama_produk[]" class="form-control select2">
                                                            <option value="">Pilih Produk</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="jumlah_dikirim[]" id="jumlah_dikirim_0"  class="form-control" ></td>
                                                    <!-- <td><input type="number" name="jumlah_diterima[]" id="jumlah_diterima_0" oninput="multiply($(this))" class="form-control" onchange="calculateTotal(0)" readonly></td> -->
                                                    <td><button type="button" name="add" id="add" class="btn"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></button></td>
                                                </tr>
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
                                                            <th>Penerima</th>
                                                            <th>Penyetuju</th>
                                                            <th>Pemeriksa</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td id="pembuat">{{ Auth::user()->name }}</td>
                                                            <td id="penerima">-</td>
                                                            <td id="penyetuju">-</td>
                                                            <td id="pemeriksa">-</td>
                                                        </tr>
                                                        <tr>
                                                            <td id="tgl_pembuat"><input type="date" class="form-control" name="tanggal_pembuat" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"></td>
                                                            <td id="tgl_penerima" style="width: 25%;">-</td>
                                                            <td id="tgl_penyetuju" style="width: 25%;">-</td>
                                                            <td id="tgl_pemeriksa" style="width: 25%;">-</td>
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
                            </div>
                        </div>


                        <div class="text-end mt-3">
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <a href="{{ route('mutasighgalery.index') }}" class="btn btn-secondary" type="button">Back</a>
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

    function updateDate(element) {
        var today = new Date().toISOString().split('T')[0];
        $(element).val(today);
    }

    // Call the function to set the date to today's date initially
    $(document).ready(function() {
        updateDate($('#tanggal_kirim'));
        updateDate($('#tanggal_diterima'));
    });

    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $(document).ready(function() {
        $('#pengirim').change(function() {
            var tipeLokasi = $(this).find(':selected').data('tipe-lokasi');
            
            $.ajax({
                url: '{{ route("getProductsByLokasi") }}',
                type: 'GET',
                data: { tipe_lokasi: tipeLokasi },
                success: function(data) {
                    updateProductOptions(data, 0);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
        $('#pengirim').select2();
        $('#penerima').select2();
        var i = 1;
        $('#add').click(function() {
            var newRow = `<tr id="row${i}">
                            <td>
                                <select id="nama_produk_${i}" name="nama_produk[]" class="form-control select2">
                                    <option value="">Pilih Produk</option>
                                </select>
                            </td>
                            <td><input type="number" name="jumlah_dikirim[]" id="jumlah_dikirim_${i}" class="form-control"></td>
                            <td><button type="button" name="remove" id="${i}" class="btn btn_remove"><img src="/assets/img/icons/delete.svg" alt="svg"></button></td>
                        </tr>`;

            $('#dynamic_field').append(newRow);
            $('.select2').select2();
            updateProductOptionsForAllRows(i);
            i++;
        });

        function updateProductOptions(produks, rowId) {
            var $produkSelect = $('#nama_produk_' + rowId);
            $produkSelect.empty();
            $produkSelect.append('<option value="">Pilih Produk</option>');

            $.each(produks, function(index, produk) {
                $produkSelect.append(
                    '<option value="' + produk.id + '" data-harga="' + produk.harga_jual + '" data-tipe_produk="' + produk.tipe_produk + '">' +
                    produk.produk.nama + ' - ' + produk.kondisi.nama +
                    '</option>'
                );
            });
        }

        function updateProductOptionsForAllRows(rowId) {
            var tipeLokasi = $('#pengirim').find(':selected').data('tipe-lokasi');
            
            $.ajax({
                url: '{{ route("getProductsByLokasi") }}',
                type: 'GET',
                data: { tipe_lokasi: tipeLokasi },
                success: function(data) {
                    updateProductOptions(data, rowId);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

        var cekInvoiceNumbers = "<?php echo $cekInvoice; ?>";
        var cekInvoiceNumberspusat = "<?php echo $cekInvoicep; ?>";

        function generateInvoice() {
            var tipeLokasi = $('#pengirim').find(':selected').data('tipe-lokasi');

            var invoicePrefix = '';
            var nextInvoiceNumber = 0;

            if (tipeLokasi == 3) {
                invoicePrefix = "MGG";
                nextInvoiceNumber = parseInt(cekInvoiceNumbers) + 1;
            } else if (tipeLokasi == 4) {
                invoicePrefix = "MPG";
                nextInvoiceNumber = parseInt(cekInvoiceNumberspusat) + 1;
            }

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

            $('#pengirim').change(function() {
                generateInvoice();
            });
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

        function validateNumericInput() {
            $('#biaya_pengiriman').on('input', function() {
                var value = $(this).val();
                var numericValue = value.replace(/[^0-9.]/g, '');

                if (numericValue !== value) {
                    $(this).val(numericValue);
                }
            });
        }
        validateNumericInput();

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
    });


</script>

@endsection