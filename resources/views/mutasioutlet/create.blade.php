@extends('layouts.app-von')

@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Mutasi Outlet ke Galery</h3>
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
        <form action="{{ route('mutasioutlet.store', ['returpenjualan' => $penjualans->id]) }}" method="POST" enctype="multipart/form-data">
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
                                                <option value="DIKONFIRMASI">DIKONFIRMASI</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-file-container" data-upload-id="myFirstImage">
                                                <label>Bukti<a href="javascript:void(0)" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"></a>
                                                </label>
                                                <label class="custom-file-container__custom-file">
                                                    <input type="file" id="bukti_file" class="custom-file-container__custom-file__custom-file-input" name="bukti" accept="image/*,.pdf">
                                                    <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
                                                    <span class="custom-file-container__custom-file__custom-file-control"></span>
                                                </label>
                                                <span class="text-danger">max 2mb</span>
                                                <div id="filePreview"></div>
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
                                                    <th>Nama Komponen</th>
                                                    <th>Kondisi Komponen</th>
                                                    <th>Jumlah Komponen</th>
                                                    <th>Jumlah Dikirim</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="dynamic_field">
                                            @if(count($penjualans->produk_retur) > 0)
                                                    @php
                                                    $i = 0;
                                                    @endphp
                                                    @foreach ($penjualans->produk_retur as $produk)
                                                    @if($produk->jenis == 'RETUR' && $produk->jumlah_dikirim != 0 || $produk->jumlah_dikirim != null)
                                                    <tr id="row{{ $i }}">
                                                    <td>
                                                    @php
                                                        $isTRDSelected = false;
                                                    @endphp
                                                        <select id="nama_produk_{{ $i }}" name="nama_produk[]" class="form-control pilih-produk" data-index="{{ $i }}" required readonly>
                                                        <option value="">Pilih Produk</option>
                                                        @php
                                                            $isTRDSelected = false; 
                                                            $selectedTRDKode = ''; 
                                                            $selectedGFTKode = ''; 
                                                            if($cekpenjualan->distribusi == 'Diambil'){
                                                                $harga = \App\Models\Produk_Terjual::where('id', $produk->no_do)->first();
                                                            }elseif($cekpenjualan->distribusi == 'Dikirim') {
                                                                $do = \App\Models\Produk_Terjual::where('id', $produk->no_do)->first();
                                                                $harga = \App\Models\Produk_Terjual::where('id', $do->no_invoice)->first();
                                                            }
                                                            
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
                                                            <option value="{{ $produk->id }}" data-harga="{{ $harga->harga_jual }}" data-jumlahproduk="{{ $harga->jumlah}}" data-diskon="{{ $harga->diskon}}" {{ $isSelectedTRD || $isSelectedGFT ? 'selected' : '' }}>
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

                                                    <td><input type="number" name="jumlah_dikirim[]" id="jumlah_{{ $i }}" class="form-control" data-index="{{ $i }}" value="{{ old('jumlah.' . $i) ?? $produk->jumlah }}" required></td>
                                                    <td>
                                                        @if ($i == 0)
                                                        <button type="button" name="remove" id="{{ $i }}" class="btn btn_remove"><img src="/assets/img/icons/delete.svg" alt="svg"></button>
                                                        @else
                                                        <button type="button" name="remove" id="{{ $i }}" class="btn btn_remove"><img src="/assets/img/icons/delete.svg" alt="svg"></button>
                                                        @endif
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
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="row justify-content-around">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2"> -->
                                <div class="row">
                                    <div class="col-lg-8 col-sm-6 col-12 border radius">
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
                                    <div class="col-lg-4 float-md-right border radius">
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



</div>
@endsection

@section('scripts')
<script>

</script>
<script>
    var cekInvoiceNumbers = "<?php echo $cekInvoice ?>";
    // console.log(cekInvoiceNumbers);
    var nextInvoiceNumber = parseInt(cekInvoiceNumbers) + 1;

    function generateInvoice() {
        var invoicePrefix = "MOG";
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
        $('#pengirim').select2();
        $('#penerima').select2();
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
                            <td><button type="button" name="remove" id="${i}" class="btn btn_remove"><img src="/assets/img/icons/delete.svg" alt="svg"></button></td>
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

        $('#bukti_file').on('change', function(event) {
            var file = event.target.files[0];
            var filePreviewContainer = $('#filePreview');

            filePreviewContainer.html('');

            if (file) {
                var fileType = file.type;

                if (fileType.includes('image')) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var img = $('<img />', {
                            src: e.target.result,
                            style: 'max-width: 100%;'
                        });
                        filePreviewContainer.append(img);
                    };
                    reader.readAsDataURL(file);
                }
                else if (fileType === 'application/pdf') {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var embed = $('<embed />', {
                            src: e.target.result,
                            type: 'application/pdf',
                            width: '100%',
                            height: '500px' 
                        });
                        filePreviewContainer.append(embed);
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert('Unsupported file type! Please select an image or a PDF file.');
                }
            }
        });

        function clearFile() {
            $('#bukti_file').val(''); 
            $('#filePreview').html(''); 
        }

        $('[id^=nama_produk_]').on('mousedown click focus', function(e) {
            e.preventDefault();
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

        var jumlahDO = [];

        @if($cekpenjualan->distribusi == 'Diambil')
                @foreach ($penjualans->produk_retur as $produk)
                    jumlahDO.push({{ $produk->jumlah }});
                @endforeach
        @elseif($cekpenjualan->distribusi == 'Dikirim')
            @foreach ($penjualans->deliveryorder as $deliveryOrder)
                @foreach ($deliveryOrder->produk as $produk)
                    jumlahDO.push({{ $produk->jumlah }});
                @endforeach
            @endforeach
        @endif

        // console.log(jumlahDO);

        $('[id^=jumlah_]').on('input', function() {
            var jumlahInput = $(this);
            var index = jumlahInput.attr('id').split('_')[1];
            var inputJumlah = $(this).val();
            // console.log(jumlahDO[index]);
            if (parseInt(inputJumlah) > jumlahDO[index]) {

                alert('Jumlah Komplain harus sesuai dengan jumlah DO!');
                $(this).val(jumlahDO[index]);
            } else if (parseInt(inputJumlah) < 0) {
                alert('Jumlah Komplain tidak boleh kurang dari 0');
                $(this).val(0);
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

    });
</script>

@endsection