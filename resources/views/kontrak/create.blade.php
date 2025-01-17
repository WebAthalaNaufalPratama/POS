@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Buat Kontrak</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('kontrak.store') }}" method="POST" enctype="multipart/form-data" id="addForm">
                <div class="row">
                    <div class="col-sm">
                            @csrf
                            <div class="row justify-content-around">
                                <div class="col-md-6 border rounded pt-3">
                                    <h5 class="card-title">Informasi Pelanggan</h5>
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Customer</label>
                                            <div class="row align-items-center">
                                                <div class="col-10 pe-0">
                                                    <select id="customer_id" name="customer_id" class="form-control" required>
                                                        <option value="">Pilih Customer</option>
                                                        @foreach ($customers as $customer)
                                                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-2 ps-0 mb-0">
                                                    <button id="btnAddCustomer" class="btn btn-primary w-100"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>PIC</label>
                                                <input type="text" id="pic" name="pic" value="{{ old('pic') }}" class="form-control" oninput="validateName(this)" placeholder="contoh: Ahmad Al Mansyur" required>
                                            </div>
                                            <div class="form-group">
                                                <label>No NPWP</label>
                                                <input type="text" id="no_npwp" name="no_npwp" value="{{ old('no_npwp') }}" class="form-control" oninput="validateDotStripNumber(this)" placeholder="contoh: 12.345.678.9-501.000" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Alamat</label>
                                                <textarea type="text" id="alamat" name="alamat" class="form-control" placeholder="contoh: Jalan Merpati no.1" required>{{ old('alamat') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Handphone</label>
                                                <input type="text" id="handhpone" name="handphone" value="{{ old('handphone') }}" class="form-control" placeholder="contoh: 081234567890" required oninput="validatePhoneNumber(this)">
                                            </div>
                                            <div class="form-group">
                                                <label>Nama NPWP</label>
                                                <input type="text" id="nama_npwp" name="nama_npwp" value="{{ old('nama_npwp') }}" class="form-control" placeholder="contoh: Ahmad Al Mansyur" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select id="status" name="status" class="form-control" required>
                                                    <option value="TUNDA" {{ old('status') == 'TUNDA' ? 'selected' : '' }}>TUNDA</option>
                                                    {{-- <option value="DIKONFIRMASI" {{ old('status') == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                    <option value="BATAL"  {{ old('status') == 'BATAL' ? 'selected' : '' }}>BATAL</option> --}}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 border rounded pt-3">
                                    <h5 class="card-title">Detail Kontrak</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>No Kontrak</label>
                                                <input type="text" id="no_kontrak" name="no_kontrak" value="{{ $getKode }}" value="{{ old('no_kontrak') }}" class="form-control" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Mulai</label>
                                                <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Masa Sewa</label>
                                                <div class="input-group">
                                                    <input type="text" id="masa_sewa" name="masa_sewa" value="{{ old('masa_sewa') }}" class="form-control" required readonly placeholder="Masa sewa" aria-describedby="basic-addon2">
                                                    <span class="input-group-text" id="basic-addon2">bulan</span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>File Kontrak</label>
                                                <div class="input-group">
                                                    <input type="file" id="file" name="file" value="" class="form-control" accept="application/pdf">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Tanggal Kontrak</label>
                                                <input type="date" id="tanggal_kontrak" name="tanggal_kontrak" value="{{ old('tanggal_kontrak') ?? date('Y-m-d') }}" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Selesai</label>
                                                <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Sales</label>
                                                <select id="sales" name="sales" class="form-control" required>
                                                    <option value="">Pilih Sales</option>
                                                    @foreach ($sales as $item)
                                                        <option value="{{ $item->id }}" {{ old('sales') == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Rekening</label>
                                                <select id="rekening_id" name="rekening_id" class="form-control" required>
                                                    <option value="">Pilih Rekening</option>
                                                    @foreach ($rekenings as $rekening)
                                                        <option value="{{ $rekening->id }}" {{ old('rekening_id') == $rekening->id ? 'selected' : '' }}>{{ $rekening->nama_akun }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Catatan</label>
                                                <textarea type="text" id="catatan" name="catatan" placeholder="Masukkan catatan terkait kontrak" class="form-control">{{ old('catatan') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="form-row row">
                        <label>List Produk</label>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Harga Satuan</th>
                                        <th>Jumlah</th>
                                        <th>Harga Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="dynamic_field">
                                    <tr>
                                        <td>
                                            <select id="produk_0" name="nama_produk[]" class="form-control"  required>
                                                <option value="">Pilih Produk</option>
                                                @foreach ($produkjuals as $produk)
                                                    <option value="{{ $produk->kode }}" data-tipe_produk="{{ $produk->tipe_produk }}" data-harga_jual="{{ $produk->harga_jual }}">{{ $produk->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" name="harga_satuan[]" id="harga_satuan_0" oninput="multiply(this)" class="form-control"  required></td>
                                        <td><input type="text" name="jumlah[]" id="jumlah_0" oninput="multiply(this)" class="form-control"  required></td>
                                        <td><input type="text" name="harga_total[]" id="harga_total_0" class="form-control"  required readonly></td>
                                        <td><a href="javascript:void(0);" id="add"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm">
                        <div class="row justify-content-around">
                            <div class="col-md-8 pt-3 ps-0 pe-0">
                                <table class="table table-responsive border rounded">
                                    <thead>
                                        <tr>
                                            <th>Pengaju</th>
                                            <th>Pembuat</th>
                                            <th>Penyetuju</th>
                                            <th>Pemeriksa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="pengaju">-</td>
                                            <td id="pembuat">{{ Auth::user()->name }}</td>
                                            <td id="penyetuju">-</td>
                                            <td id="pemeriksa">-</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%;">
                                                <input type="date" id="tanggal_sales" name="tanggal_sales" value="{{ date('Y-m-d') }}" class="form-control"  required>
                                            </td>
                                            <td id="tgl_pembuat" style="width: 25%;">{{ date('d-m-Y') }}</td>
                                            <td id="tgl_penyetuju" style="width: 25%;">-</td>
                                            <td id="tgl_pemeriksa" style="width: 25%;">-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4 border rounded mt-3 pt-3">
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Subtotal</label>
                                    <div class="col-lg-9">
                                        <input type="text" id="subtotal" name="subtotal" value="{{ old('subtotal') }}" class="form-control"  required readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Diskon</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="text" id="promo_persen" name="promo_persen" value="{{ old('promo_persen') ?? 0 }}" class="form-control" required aria-describedby="basic-addon3" oninput="validatePersen(this)">
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                        <input type="text" class="form-control" name="total_promo" id="total_promo" value="{{ old('total_promo') ?? 0 }}" placeholder="contoh: 10.000" required>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">PPN</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="text" id="ppn_persen" name="ppn_persen" value="{{ old('ppn_persen') ?? 11 }}" class="form-control" required aria-describedby="basic-addon3" oninput="validatePersen(this)">
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                        <input type="text" class="form-control" name="ppn_nominal" id="ppn_nominal" value="{{ old('ppn_nominal') }}" placeholder="contoh: 10.000" required>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">PPH</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="text" id="pph_persen" name="pph_persen" value="{{ old('pph_persen') ?? 2 }}" class="form-control"  required aria-describedby="basic-addon3" oninput="validatePersen(this)">
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                        <input type="text" class="form-control" name="pph_nominal" id="pph_nominal" value="{{ old('pph_nominal') }}" placeholder="contoh: 10.000" required>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Ongkir</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="ongkir_nominal" id="ongkir_nominal" value="{{ old('ongkir_nominal') }}" placeholder="contoh: 10.000" required>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Total Harga</label>
                                    <div class="col-lg-9">
                                        <input type="text" id="total_harga" name="total_harga" value="{{ old('total_harga') }}" class="form-control"  required readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button class="btn btn-primary" type="submit">Submit</button>
                    <a href="{{ route('kontrak.index') }}" class="btn btn-secondary" type="button">Back</a>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- modal start --}}
<div class="modal fade" id="addcustomer" tabindex="-1" aria-labelledby="addcustomerlabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addcustomerlabel">Tambah Customer</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form action="{{ route('customer.store') }}" method="POST">
            @csrf
            <input type="hidden" name="route" value="{{ request()->route()->getName() }}">
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="add_nama" oninput="validateName(this)" required>
            </div>
            <div class="mb-3">
              <label for="tipe" class="col-form-label">Tipe Customer</label>
              <div class="form-group">
                <select id="add_tipe" name="tipe" class="form-control" required>
                    <option value="sewa">Sewa</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="handphone" class="col-form-label"> No Handphone</label>
              <input type="text" class="form-control" name="handphone" id="add_handphone" oninput="validatePhoneNumber(this)" placeholder="081234567890" required>
            </div>
            <div class="mb-3">
              <label for="alamat" class="col-form-label">Alamat</label>
              <textarea class="form-control" name="alamat" id="add_alamat" required placeholder="contoh: Jalan Merpati no.1"></textarea>
            </div>
            <div class="mb-3">
              <label for="tanggal_lahir" class="col-form-label">Tanggal Lahir</label>
              <input type="date" class="form-control" name="tanggal_lahir" id="add_tanggal_lahir" max="{{ date('Y-m-d') }}" required>
            </div>
            <div class="mb-3">
              <label for="tanggal_bergabung" class="col-form-label">Tanggal Gabung</label>
              <input type="date" class="form-control" name="tanggal_bergabung" id="add_tanggal_bergabung" max="{{ date('Y-m-d') }}" required disabled>
            </div>
            <div class="mb-3">
                <label for="lokasi_id" class="col-form-label">Lokasi</label>
                <div class="form-group">
                  <select id="add_lokasi_id" name="lokasi_id" class="form-control" required>
                    @foreach ($lokasis as $item)
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
{{-- modal end --}}
@endsection

@section('scripts')
    <script>
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function() {
            $('[id^=produk], #customer_id, #sales, #rekening_id, #status, #ongkir_id, #promo_id, #add_tipe, #add_lokasi_id').select2();
            $('#sales').trigger('change');
            var i = 1;
            $('#add').click(function(){
            var newRow = '<tr id="row'+i+'"><td>' + 
                                '<select id="produk_'+i+'" name="nama_produk[]" class="form-control">'+
                                    '<option value="">Pilih Produk</option>'+
                                    '@foreach ($produkjuals as $produk)'+
                                        '<option value="{{ $produk->kode }}" data-tipe_produk="{{ $produk->tipe_produk }}" data-harga_jual="{{ $produk->harga_jual }}">{{ $produk->nama }}</option>'+
                                    '@endforeach'+
                                '</select>'+
                            '</td>'+
                            '<td><input type="text" name="harga_satuan[]" id="harga_satuan_'+i+'" oninput="multiply(this)" class="form-control"></td>'+
                            '<td><input type="text" name="jumlah[]" id="jumlah_'+i+'" oninput="multiply(this)" class="form-control"></td>'+
                            '<td><input type="text" name="harga_total[]" id="harga_total_'+i+'" class="form-control" readonly></td>'+
                            '<td><a href="javascript:void(0);" class="btn_remove" id="'+ i +'"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td></tr>';
                $('#dynamic_field').append(newRow);
                $('#produk_' + i).select2();
                i++;
            })
        });
        $(document).on('input', '[id^=handhpone], #add_handphone, #ppn_persen, #pph_persen', function() {
            let input = $(this);
            let value = input.val();
            
            if (!isNumeric(value)) {
            value = value.replace(/[^\d]/g, "");
            }

            input.val(value);
        });
        $(document).on('input', '[id^=harga_satuan], #total_promo, #pph_nominal, #ppn_nominal, #ongkir_nominal, [id^=jumlah]', function() {
            let input = $(this);
            let value = input.val();
            if (!isNumeric(cleanNumber(value))) {
                value = value.replace(/[^\d]/g, "");
            }
            value = cleanNumber(value);
            let formattedValue = formatNumber(value);
            
            input.val(formattedValue);
        });
        $(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
            $('#row'+button_id+'').remove();
            multiply($('#harga_satuan_0'))
            multiply($('#jumlah_0'))
        });

        // diskon start
        $(document).on('input', '#total_promo', function(){
            $('#promo_persen').val(0);
            var value = $(this).val().trim();

            if (value === "") {
                $(this).val(0);
                value = 0;
            } else {
                if (!value.startsWith("0.")) {
                    value = value.replace(/^0+/, '');
                    $(this).val(value);
                }
            }

            if (!isNumeric(cleanNumber(value))) {
                return;
            }

            var inputs = $('input[name="harga_total[]"]');
            var total = 0;
            inputs.each(function() {
                total += parseInt(cleanNumber($(this).val())) || 0;
            });
            var total_promo = cleanNumber($(this).val());
            validateCantExceed($(this), total);
            nilai = cleanNumber($(this).val());
            $('#subtotal').val(formatNumber((total) - (nilai))) 
            update_pajak(cleanNumber($('#subtotal').val()));
            total_harga();
        });
        $(document).on('input', '#promo_persen', function(){
            var promo_persen = $(this).val()
            var inputs = $('input[name="harga_total[]"]');
            var total = 0;
            inputs.each(function() {
                total += parseInt(cleanNumber($(this).val())) || 0;
            });
            var total_promo = promo_persen * total / 100
            $('#total_promo').val(formatNumber(total_promo))
            $('#subtotal').val(formatNumber((total) - (total_promo)))
            update_pajak(cleanNumber($('#subtotal').val()));
            total_harga();
        });
        // diskon end


        // ppn start
        $(document).on('input', '#ppn_nominal', function(){
            $('#ppn_persen').val(0);
            var value = $(this).val().trim();
            
            if (value === "") {
                $(this).val(0);
                value = 0;
            } else {
                if (!value.startsWith("0.")) {
                    value = value.replace(/^0+/, '');
                    $(this).val(value);
                }
            }

            if (!isNumeric(cleanNumber(value))) {
                return;
            }

            var total_promo = $('#total_promo').val();
            var inputs = $('input[name="harga_total[]"]');
            var total = 0;
            inputs.each(function() {
                total += parseInt(cleanNumber($(this).val())) || 0;
            });
            validateCantExceed($(this), total);
            $('#subtotal').val(formatNumber((total) - (cleanNumber(total_promo)))) 
            total_harga();
        });
        $(document).on('input', '#ppn_persen', function(){
            var total_promo = $('#total_promo').val();
            var inputs = $('input[name="harga_total[]"]');
            var total = 0;
            inputs.each(function() {
                total += parseInt(cleanNumber($(this).val())) || 0;
            });
            $('#subtotal').val(formatNumber((total) - cleanNumber(total_promo)))

            var subtotal = $('#subtotal').val();
            var ppn_persen = $(this).val()
            var ppn_nominal = ppn_persen * cleanNumber(subtotal) / 100
            $('#ppn_nominal').val(formatNumber(ppn_nominal))
            total_harga();
        });
        // ppn end


        // pph start
        $('#pph_nominal').on('input', function(){
            $('#pph_persen').val(0);
            var value = $(this).val().trim();
            
            if (value === "") {
                $(this).val(0);
                value = 0;
            } else {
                if (!value.startsWith("0.")) {
                    value = value.replace(/^0+/, '');
                    $(this).val(value);
                }
            }

            if (!isNumeric(cleanNumber(value))) {
                return;
            }

            var total_promo = $('#total_promo').val();
            var inputs = $('input[name="harga_total[]"]');
            var total = 0;
            inputs.each(function() {
                total += parseInt(cleanNumber($(this).val())) || 0;
            });
            validateCantExceed($(this), total);
            $('#subtotal').val(formatNumber((total) - (cleanNumber(total_promo)))) 
            total_harga();
        });
        $('#pph_persen').on('input', function(){
            var total_promo = $('#total_promo').val();
            var inputs = $('input[name="harga_total[]"]');
            var total = 0;
            inputs.each(function() {
                total += parseInt(cleanNumber($(this).val())) || 0;
            });
            $('#subtotal').val(formatNumber((total) - cleanNumber(total_promo)))

            var subtotal = $('#subtotal').val();
            var pph_persen = $(this).val()
            var pph_nominal = pph_persen * cleanNumber(subtotal) / 100
            $('#pph_nominal').val(formatNumber(pph_nominal))
            total_harga();
        });
        // pph end


        // ongkir start
        $('#ongkir_nominal').on('input', function(){
            total_harga();
        });
        // ongkir end

        $('#addForm').on('submit', function(e) {
            // Add input number cleaning for specific inputs
            let inputs = $('#addForm').find('[id^=harga_satuan], [id^=harga_total], #subtotal, #total_promo, #ppn_nominal, #pph_nominal, #ongkir_nominal, #total_harga, [id^=jumlah]');
            inputs.each(function() {
                let input = $(this);
                let value = input.val();
                let cleanedValue = cleanNumber(value);

                // Set the cleaned value back to the input
                input.val(cleanedValue);
            });

            return true;
        });
        $('#sales').on('change', function() {
            var nama_sales = $("#sales option:selected").text();
            var val_sales = $("#sales option:selected").val();
            if(val_sales != ""){
                $('#pengaju').text(nama_sales)
            } else {
                $('#pengaju').text('-')
            }
        });
        $('#tanggal_mulai').on('input', function() {
            if ($(this).val()) {
                var tgl_mulai = new Date($(this).val());
                tgl_mulai.setFullYear(tgl_mulai.getFullYear() + 1);
                var tanggalAkhir = tgl_mulai.toISOString().slice(0,10);
                $('#tanggal_selesai').val(tanggalAkhir);
                $('#tanggal_selesai').attr('min', $(this).val());
                $('#masa_sewa').val(12);
            } else {
                $('#masa_sewa').val(0);
                $('#tanggal_selesai').attr('min', 0);
            }
        });
        $('#tanggal_selesai').on('input', function() {
            var tanggal_mulai = $('#tanggal_mulai').val();
            var tanggal_selesai = $(this).val();
            
            if (!tanggal_selesai || !tanggal_mulai) {
                $('#masa_sewa').val(0);
                return;
            }
            
            var mulai = new Date(tanggal_mulai);
            var selesai = new Date(tanggal_selesai);
            
            var tahunMulai = mulai.getFullYear();
            var tahunSelesai = selesai.getFullYear();
            var bulanMulai = mulai.getMonth();
            var bulanSelesai = selesai.getMonth();
            
            var bulan = (tahunSelesai - tahunMulai) * 12 + (bulanSelesai - bulanMulai);
            
            if (bulan < 0) {
                bulan = 0;
            }
            
            $('#masa_sewa').val(bulan);
        });
        $('#btnAddCustomer').click(function(e) {
            e.preventDefault()
            $('#addcustomer').modal('show');
        });
        $('#add_tanggal_lahir').on('change', function(){
            if($(this).val()){
                $('#add_tanggal_bergabung').attr('disabled', false)
                $('#add_tanggal_bergabung').attr('min', $(this).val())
            } else {
                $('#add_tanggal_bergabung').attr('disabled', true)
                $('#add_tanggal_bergabung').attr('min', 0)
            }
        })
        $(document).on('change', '[id^=produk_]', function(){
            var id = $(this).attr('id');
            var parts = id.split('_');
            var nomor = parts[parts.length - 1];
            var harga_jual = $(this).find(":selected").data("harga_jual") ?? 0;
            $('#harga_satuan_' + nomor).val(formatNumber(harga_jual));
            $('#btnCheckPromo').trigger('click')
            multiply('#harga_satuan_' + nomor);
        });
        function multiply(element) {
            let input = $(element);
            let value = input.val();
            
            if (!isNumeric(cleanNumber(value))) {
            return false;
            }

            var id = 0
            var jumlah = 0
            var harga_satuan = 0
            var jenis = $(element).attr('id')
            if(jenis.split('_').length == 2){
                id = $(element).attr('id').split('_')[1];
                jumlah = $(element).val();
                harga_satuan = cleanNumber($('#harga_satuan_' + id).val());
                if (harga_satuan) {
                    var harga_total = harga_satuan * jumlah
                    $('#harga_total_'+id).val(formatNumber(harga_total))
                }
            } else if(jenis.split('_').length == 3){
                id = $(element).attr('id').split('_')[2];
                harga_satuan = cleanNumber($(element).val());
                jumlah = $('#jumlah_' + id).val();
                if (jumlah) {
                    var harga_total = harga_satuan * jumlah
                    $('#harga_total_'+id).val(formatNumber(harga_total))
                }
            }

            var inputs = $('input[name="harga_total[]"]');
            var total = 0;
            inputs.each(function() {
                total += parseInt(cleanNumber($(this).val())) || 0;
            });
            var promo = cleanNumber($('#total_promo').val() ?? 0);
            $('#subtotal').val(formatNumber((total - promo)))

            var ppn_persen = $('#ppn_persen').val();
            var ppn_nominal = ppn_persen * (total - promo) / 100
            $('#ppn_nominal').val(formatNumber(ppn_nominal))

            var pph_persen = $('#pph_persen').val();
            var pph_nominal = pph_persen * (total - promo) / 100
            $('#pph_nominal').val(formatNumber(pph_nominal))
            
            total_harga();
        }
        function total_harga() {
            var subtotal = cleanNumber($('#subtotal').val()) || 0;
            var ppn_nominal = cleanNumber($('#ppn_nominal').val()) || 0;
            var pph_nominal = cleanNumber($('#pph_nominal').val()) || 0;
            var ongkir_nominal = cleanNumber($('#ongkir_nominal').val()) || 0;
            var harga_total = parseInt(subtotal) + parseInt(ppn_nominal) + parseInt(pph_nominal) + parseInt(ongkir_nominal);
            $('#total_harga').val(formatNumber(harga_total));
        }
        function update_pajak(subtotal){
            var ppn_persen = $('#ppn_persen').val() || 0;
            var pph_persen = $('#pph_persen').val() || 0;
            if(ppn_persen != 0){
                var ppn_nominal = ppn_persen * subtotal / 100;
                $('#ppn_nominal').val(formatNumber(parseInt(ppn_nominal)));
            }
            if(pph_persen != 0){
                var pph_nominal = pph_persen * subtotal / 100;
                $('#pph_nominal').val(formatNumber(parseInt(pph_nominal)));
            }
        }
    </script>
@endsection