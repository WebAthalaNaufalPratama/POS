@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Buat Kontrak</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('kontrak.store') }}" method="POST">
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
                                                <input type="text" id="pic" name="pic" value="{{ old('pic') }}" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>No NPWP</label>
                                                <input type="text" id="no_npwp" name="no_npwp" value="{{ old('no_npwp') }}" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Alamat</label>
                                                <textarea type="text" id="alamat" name="alamat" class="form-control" required>{{ old('alamat') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Handphone</label>
                                                <input type="text" id="handhpone" name="handphone" value="{{ old('handphone') }}" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Nama NPWP</label>
                                                <input type="text" id="nama_npwp" name="nama_npwp" value="{{ old('nama_npwp') }}" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select id="status" name="status" class="form-control" required>
                                                    <option value="">Pilih Status</option>
                                                    <option value="DRAFT" {{ old('status') == 'DRAFT' ? 'selected' : '' }}>Draft</option>
                                                    <option value="AKTIF" {{ old('status') == 'AKTIF' ? 'selected' : '' }}>Aktif</option>
                                                    <option value="TIDAK AKTIF"  {{ old('status') == 'TIDAK AKTIF' ? 'selected' : '' }}>Tidak Aktif</option>
                                                    <option value="SELESAI" {{ old('status') == 'SELESAI' ? 'selected' : '' }}>Selesai</option>
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
                                                    <input type="text" id="masa_sewa" name="masa_sewa" value="{{ old('masa_sewa') }}" class="form-control" required placeholder="Masa sewa" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                                    <span class="input-group-text" id="basic-addon2">bulan</span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Catatan</label>
                                                <textarea type="text" id="catatan" name="catatan" class="form-control">{{ old('catatan') }}</textarea>
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
                                        <td><input type="number" name="harga_satuan[]" id="harga_satuan_0" oninput="multiply(this)" class="form-control"  required></td>
                                        <td><input type="number" name="jumlah[]" id="jumlah_0" oninput="multiply(this)" class="form-control"  required></td>
                                        <td><input type="number" name="harga_total[]" id="harga_total_0" class="form-control"  required readonly></td>
                                        <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td>
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
                                        <div class="row align-items-center">
                                            <div class="col-9 pe-0">
                                                <select id="promo_id" name="promo_id" class="form-control" disabled>
                                                </select>
                                            </div>
                                            <div class="col-3 ps-0 mb-0">
                                                <button id="btnCheckPromo" class="btn btn-primary w-100"><i class="fa fa-search" data-bs-toggle="tooltip"></i></button>
                                            </div>
                                        </div>                                        
                                        <input type="text" class="form-control" required name="total_promo" id="total_promo" value="{{ old('total_promo') ?? 0 }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">PPN</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="number" id="ppn_persen" name="ppn_persen" value="{{ old('ppn_persen') ?? 11 }}" class="form-control"  required aria-label="Recipient's username" aria-describedby="basic-addon3">
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                        <input type="text" class="form-control"  required name="ppn_nominal" id="ppn_nominal" value="{{ old('ppn_nominal') }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">PPH</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="number" id="pph_persen" name="pph_persen" value="{{ old('pph_persen') ?? 2 }}" class="form-control"  required aria-label="Recipient's username" aria-describedby="basic-addon3">
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                        <input type="text" class="form-control"  required name="pph_nominal" id="pph_nominal" value="{{ old('pph_nominal') }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Ongkir</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <select id="ongkir_id" name="ongkir_id" class="form-control" required>
                                                <option value="">Pilih Ongkir</option>
                                                @foreach ($ongkirs as $ongkir)
                                                    <option value="{{ $ongkir->id }}" {{ old('ongkir_id') == $ongkir->id ? 'selected' : '' }}>{{ $ongkir->nama }}-{{ $ongkir->biaya }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="number" class="form-control"  required name="ongkir_nominal" id="ongkir_nominal" value="{{ old('ongkir_nominal') }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Total Harga</label>
                                    <div class="col-lg-9">
                                        <input type="number" id="total_harga" name="total_harga" value="{{ old('total_harga') }}" class="form-control"  required readonly>
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
              <input type="text" class="form-control" name="nama" id="add_nama" required>
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
{{-- modal end --}}
@endsection

@section('scripts')
    <script>
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function() {
            $('[id^=produk], #customer_id, #sales, #rekening_id, #status, #ongkir_id, #promo_id, #add_tipe').select2();
            $('#sales').trigger('cahnge');
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
                            '<td><input type="number" name="harga_satuan[]" id="harga_satuan_'+i+'" oninput="multiply(this)" class="form-control"></td>'+
                            '<td><input type="number" name="jumlah[]" id="jumlah_'+i+'" oninput="multiply(this)" class="form-control"></td>'+
                            '<td><input type="number" name="harga_total[]" id="harga_total_'+i+'" class="form-control" readonly></td>'+
                            '<td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">x</button></td></tr>';
                $('#dynamic_field').append(newRow);
                $('#produk_' + i).select2();
                i++;
           })
           $(document).on('click', '.btn_remove', function() {
                var button_id = $(this).attr("id");
                $('#row'+button_id+'').remove();
                multiply($('#harga_satuan_0'))
                multiply($('#jumlah_0'))
            });
            $('#ongkir_nominal, #total_promo, #ppn_persen, #pph_persen').on('input', function(){
                total_harga();
            })
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
            var tgl_mulai = new Date($(this).val());
            tgl_mulai.setFullYear(tgl_mulai.getFullYear() + 1);
            var tanggalAkhir = tgl_mulai.toISOString().slice(0,10);
            $('#tanggal_selesai').val(tanggalAkhir);
            $('#masa_sewa').val(12);
        });
        $('#ongkir_id').on('change', function() {
            var ongkir = $("#ongkir_id option:selected").text();
            var biaya = ongkir.split('-')[1];
            $('#ongkir_nominal').val(parseInt(biaya));
            total_harga();
        });
        $('#btnCheckPromo').click(function(e) {
            e.preventDefault();
            var total_transaksi = $('#subtotal').val();
            var produk = [];
            var tipe_produk = [];
            $('select[id^="produk_"]').each(function() {
                produk.push($(this).val());
                tipe_produk.push($(this).select2().find(":selected").data("tipe_produk"));

            });
            $(this).html('<span class="spinner-border spinner-border-sm me-2">')
            checkPromo(total_transaksi, tipe_produk, produk);
        });
        $('#promo_id').change(function() {
            var promo_id = $(this).select2().find(":selected").val()
            if(!promo_id){
                $('#total_promo').val(0);
                var inputs = $('input[name="harga_total[]"]');
                var subtotal = 0;
                inputs.each(function() {
                    subtotal += parseInt($(this).val()) || 0;
                });
                $('#subtotal').val(subtotal)
                total_harga();
                return 0;
            } 
            calculatePromo(promo_id);
        });
        $('#btnAddCustomer').click(function(e) {
            e.preventDefault()
            $('#addcustomer').modal('show');
        });
        $(document).on('change', '[id^=produk_]', function(){
            var id = $(this).attr('id');
            var parts = id.split('_');
            var nomor = parts[parts.length - 1];
            var harga_jual = $(this).find(":selected").data("harga_jual");
            $('#harga_satuan_' + nomor).val(harga_jual);
        });
        function multiply(element) {
            var id = 0
            var jumlah = 0
            var harga_satuan = 0
            var jenis = $(element).attr('id')
            if(jenis.split('_').length == 2){
                id = $(element).attr('id').split('_')[1];
                jumlah = $(element).val();
                harga_satuan = $('#harga_satuan_' + id).val();
                if (harga_satuan) {
                    $('#harga_total_'+id).val(harga_satuan * jumlah)
                }
            } else if(jenis.split('_').length == 3){
                id = $(element).attr('id').split('_')[2];
                harga_satuan = $(element).val();
                jumlah = $('#jumlah_' + id).val();
                if (jumlah) {
                    $('#harga_total_'+id).val(harga_satuan * jumlah)
                }
            }

            var inputs = $('input[name="harga_total[]"]');
            var total = 0;
            inputs.each(function() {
                total += parseInt($(this).val()) || 0;
            });
            $('#subtotal').val(total)
            total_harga();
        }
        function total_harga() {
            ppn();
            pph();
            var subtotal = $('#subtotal').val();
            var ppn_nominal = $('#ppn_nominal').val() || 0;
            var pph_nominal = $('#pph_nominal').val() || 0;
            var ongkir_nominal = $('#ongkir_nominal').val() || 0;
            var harga_total = parseInt(subtotal) + parseInt(ppn_nominal) + parseInt(pph_nominal) + parseInt(ongkir_nominal);
            $('#total_harga').val(harga_total);
        }
        function ppn(){
            var ppn_persen = $('#ppn_persen').val()
            var subtotal = $('#subtotal').val()
            var ppn_nominal = ppn_persen * subtotal / 100
            $('#ppn_nominal').val(ppn_nominal)
        }
        function pph(){
            var pph_persen = $('#pph_persen').val()
            var subtotal = $('#subtotal').val()
            var pph_nominal = pph_persen * subtotal / 100
            $('#pph_nominal').val(pph_nominal)
        }
        function checkPromo(total_transaksi, tipe_produk, produk){
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
        function calculatePromo(promo_id){
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
                    var sub_total = parseInt($('#subtotal').val());
                    var total_promo;
                    switch (response.diskon) {
                        case 'persen':
                            total_promo = sub_total * parseInt(response.diskon_persen) / 100;
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

                    var inputs = $('input[name="harga_total[]"]');
                    var subtotal = 0;
                    inputs.each(function() {
                        subtotal += parseInt($(this).val()) || 0;
                    });
                    $('#subtotal').val(subtotal)
                    
                    if (/(poin|TRD|GFT)/.test(total_promo)) {
                        total_promo = 0;
                    } else {
                        total_promo = parseInt(total_promo) || 0;
                        $('#subtotal').val(subtotal - total_promo);
                    }
                    total_harga();
                },
                error: function(xhr, status, error) {
                    console.log(error)
                }
            });
        }
    </script>
@endsection