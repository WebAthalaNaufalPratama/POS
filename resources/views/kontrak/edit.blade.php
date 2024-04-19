@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Edit Kontrak</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('kontrak.update', ['kontrak' => $kontrak->id]) }}" method="POST">
                <div class="row">
                    <div class="col-sm">
                            @csrf
                            @method('PATCH')
                            <div class="row justify-content-around">
                                <div class="col-md-6 border rounded pt-3">
                                    <h5 class="card-title">Informasi Pelanggan</h5>
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Customer</label>
                                            <select id="customer_id" name="customer_id" class="form-control">
                                                <option value="">Pilih Customer</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}" {{ $kontrak->customer_id == $customer->id ? 'selected' : ''}}>{{ $customer->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>PIC</label>
                                                <input type="text" id="pic" name="pic" value="{{ $kontrak->pic }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>No NPWP</label>
                                                <input type="text" id="no_npwp" name="no_npwp" value="{{ $kontrak->no_npwp }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>Alamat</label>
                                                <textarea type="text" id="alamat" name="alamat" class="form-control">{{ $kontrak->alamat }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Handphone</label>
                                                <input type="text" id="handhpone" name="handphone" value="{{ $kontrak->handphone }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>Nama NPWP</label>
                                                <input type="text" id="nama_npwp" name="nama_npwp" value="{{ $kontrak->nama_npwp }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select id="status" name="status" class="form-control">
                                                    <option value="">Pilih Status</option>
                                                    <option value="DRAFT" {{ $kontrak->status == 'DRAFT' ? 'selected' : '' }}>Draft</option>
                                                    <option value="AKTIF" {{ $kontrak->status == 'AKTIF' ? 'selected' : '' }}>Aktif</option>
                                                    <option value="TIDAK AKTIF" {{ $kontrak->status == 'TIDAK AKTIF' ? 'selected' : '' }}>Tidak Aktif</option>
                                                    <option value="SELESAI" {{ $kontrak->status == 'SELESAI' ? 'selected' : '' }}>Selesai</option>
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
                                                <input type="text" id="no_kontrak" name="no_kontrak" value="{{ $kontrak->no_kontrak }}" class="form-control" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Mulai</label>
                                                <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ $kontrak->tanggal_mulai }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>Masa Sewa</label>
                                                <div class="input-group">
                                                    <input type="text" id="masa_sewa" name="masa_sewa" value="{{ $kontrak->masa_sewa }}" class="form-control" placeholder="Masa sewa" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                                    <span class="input-group-text" id="basic-addon2">bulan</span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Catatan</label>
                                                <textarea type="text" id="catatan" name="catatan" class="form-control">{{ $kontrak->catatan }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Tanggal Kontrak</label>
                                                <input type="date" id="tanggal_kontrak" name="tanggal_kontrak" value="{{ $kontrak->tanggal_kontrak }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Selesai</label>
                                                <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ $kontrak->tanggal_selesai }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>Sales</label>
                                                <select id="sales" name="sales" class="form-control">
                                                    <option value="">Pilih Sales</option>
                                                    @foreach ($sales as $item)
                                                        <option value="{{ $item->id }}" {{ $kontrak->sales == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Rekening</label>
                                                <select id="rekening_id" name="rekening_id" class="form-control">
                                                    <option value="">Pilih Rekening</option>
                                                    @foreach ($rekenings as $rekening)
                                                        <option value="{{ $rekening->id }}" {{ $kontrak->rekening_id == $rekening->id ? 'selected' : '' }}>{{ $rekening->nama_akun }}</option>
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
                                    @if(count($produks) < 1)
                                    <tr>
                                        <td>
                                            <select id="produk_0" name="nama_produk[]" class="form-control">
                                                <option value="">Pilih Produk</option>
                                                @foreach ($produkjuals as $produk)
                                                    <option value="{{ $produk->kode }}">{{ $produk->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="harga_satuan[]" id="harga_satuan_0" oninput="multiply(this)" class="form-control"></td>
                                        <td><input type="number" name="jumlah[]" id="jumlah_0" oninput="multiply(this)" class="form-control"></td>
                                        <td><input type="number" name="harga_total[]" id="harga_total_0" class="form-control"></td>
                                        <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td>
                                    </tr>
                                    @endif
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($produks as $komponen) 
                                        <tr>
                                            <td>
                                                <select id="produk_{{ $i }}" name="nama_produk[]" class="form-control">
                                                    <option value="">Pilih Produk</option>
                                                    @foreach ($produkjuals as $produk)
                                                        <option value="{{ $produk->kode }}" data-tipe_produk="{{ $produk->tipe_produk }}" {{ $komponen->produk->kode == $produk->kode ? 'selected' : '' }}>{{ $produk->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" name="harga_satuan[]" id="harga_satuan_{{ $i }}" oninput="multiply(this)" class="form-control" value="{{ $komponen->harga }}"></td>
                                            <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" oninput="multiply(this)" class="form-control" value="{{ $komponen->jumlah }}"></td>
                                            <td><input type="number" name="harga_total[]" id="harga_total_{{ $i }}" class="form-control" value="{{ $komponen->harga_jual }}" readonly></td>
                                            @if ($i == 0)
                                                <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td>
                                            @else
                                                <td><button type="button" name="remove" id="{{ $i }}" class="btn btn-danger btn_remove">x</button></td>
                                            @endif
                                            @php
                                                $i++;
                                            @endphp
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm">
                        <div class="row justify-content-around">
                            <div class="col-md-8 border rounded pt-3">
                                <table class="table">
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
                                            <td id="pengaju">{{ $kontrak->pengaju->nama ?? '-' }}</td>
                                            <td id="pembuat">{{ $kontrak->pembuat->nama ?? '-' }}</td>
                                            <td id="penyetuju">{{ $kontrak->penyetuju->nama ?? '-' }}</td>
                                            <td id="pemeriksa">{{ $kontrak->pemeriksa->nama ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%;">
                                                <input type="date" id="tanggal_sales" name="tanggal_sales" value="{{ isset($kontrak->tanggal_sales) ? \Carbon\Carbon::parse($kontrak->tanggal_sales)->format('Y-m-d') : '-' }}" class="form-control">

                                            </td>
                                            <td id="tgl_pembuat" style="width: 25%;">
                                                <input type="date" id="tanggal_pembuat" name="tanggal_pembuat" value="{{ isset($kontrak->tanggal_pembuat) ? \Carbon\Carbon::parse($kontrak->tanggal_pembuat)->format('Y-m-d') : '-' }}" class="form-control">
                                            </td>
                                            <td id="tgl_penyetuju" style="width: 25%;">
                                                <input type="date" id="tgl_penyetuju" name="tgl_penyetuju" value="{{ isset($kontrak->tanggal_penyetuju) ? \Carbon\Carbon::parse($kontrak->tanggal_penyetuju)->format('Y-m-d') : '-' }}" class="form-control">

                                            </td>
                                            <td id="tgl_pemeriksa" style="width: 25%;">
                                                <input type="date" id="tgl_pemeriksa" name="tgl_pemeriksa" value="{{ isset($kontrak->tanggal_pemeriksa) ? \Carbon\Carbon::parse($kontrak->tanggal_pemeriksa)->format('Y-m-d') : '-' }}" class="form-control">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4 border rounded pt-3">
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Subtotal</label>
                                    <div class="col-lg-9">
                                        <input type="text" id="subtotal" name="subtotal" value="{{ $kontrak->subtotal }}" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">PPN</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="number" id="ppn_persen" name="ppn_persen" value="{{ $kontrak->ppn_persen ?? 11 }}" class="form-control" aria-label="Recipient's username" aria-describedby="basic-addon3">
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                        <input type="text" class="form-control" name="ppn_nominal" id="ppn_nominal" value="{{ $kontrak->ppn_nominal }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">PPH</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="number" id="pph_persen" name="pph_persen" value="{{ $kontrak->pph_persen ?? 2 }}" class="form-control" aria-label="Recipient's username" aria-describedby="basic-addon3">
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                        <input type="text" class="form-control" name="pph_nominal" id="pph_nominal" value="{{ $kontrak->pph_nominal }}" readonly>
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
                                            <input type="hidden" id="old_promo_id" value="{{ $kontrak->promo_id }}">
                                            <div class="col-3 ps-0 mb-0">
                                                <button id="btnCheckPromo" class="btn btn-primary w-100"><i class="fa fa-search" data-bs-toggle="tooltip" title="" data-bs-original-title="fa fa-search" aria-label="fa fa-search"></i></button>
                                            </div>
                                        </div>                                        
                                        <input type="text" class="form-control" name="total_promo" id="total_promo" value="{{ old('total_promo') }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Ongkir</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <select id="ongkir_id" name="ongkir_id" class="form-control">
                                                <option value="">Pilih Ongkir</option>
                                                @foreach ($ongkirs as $ongkir)
                                                    <option value="{{ $ongkir->id }}" {{ $ongkir->id == $kontrak->ongkir_id ? 'selected' : '' }}>{{ $ongkir->nama }}-{{ $ongkir->biaya }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="number" class="form-control" name="ongkir_nominal" id="ongkir_nominal" value="{{ $kontrak->ongkir_nominal }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Total Harga</label>
                                    <div class="col-lg-9">
                                        <input type="number" id="total_harga" name="total_harga" value="{{ $kontrak->total_harga }}" class="form-control" readonly>
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
@endsection

@section('scripts')
    <script>
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function() {
            var total_transaksi = $('#total_harga').val();
            var old_promo_id = $('#old_promo_id').val();
            var produk = [];
            var tipe_produk = [];
            $('select[id^="produk_"]').each(function() {
                produk.push($(this).val());
                tipe_produk.push($(this).select2().find(":selected").data("tipe_produk"));

            });
            checkPromo(total_transaksi, tipe_produk, produk, old_promo_id);
            calculatePromo(old_promo_id);

            $('[id^=produk], #customer_id, #sales, #rekening_id, #status, #ongkir_id, #promo_id').select2();
            var i = 1;
            $('#add').click(function(){
            var newRow = '<tr id="row'+i+'"><td>' + 
                                '<select id="produk_'+i+'" name="nama_produk[]" class="form-control">'+
                                    '<option value="">Pilih Produk</option>'+
                                    '@foreach ($produkjuals as $produk)'+
                                        '<option value="{{ $produk->kode }}" data-tipe_produk="{{ $produk->tipe_produk }}">{{ $produk->nama }}</option>'+
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
            var total_transaksi = $('#total_harga').val();
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
                total_harga();
                return 0;
            } 
            calculatePromo(promo_id);
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
            var diskon_nominal = $('#total_promo').val();
            if (/(poin|TRD|GFT)/.test(diskon_nominal)) {
                diskon_nominal = 0;
            } else {
                diskon_nominal = parseInt(diskon_nominal) || 0;
            }
            var harga_total = parseInt(subtotal) + parseInt(ppn_nominal) + parseInt(pph_nominal) + parseInt(ongkir_nominal) - parseInt(diskon_nominal);
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
        function checkPromo(total_transaksi, tipe_produk, produk, old_promo_id){
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
                        var selectvalue = promo.id == old_promo_id ? 'selected' : '';
                        $('#promo_id').append('<option value="' + promo.id + '" '+ selectvalue +'>' + promo.nama + '</option>');
                    }
                    var tipe_produk = response.tipe_produk;
                    for (var j = 0; j < tipe_produk.length; j++) {
                        var promo = tipe_produk[j];
                        var selectvalue = promo.id == old_promo_id ? 'selected' : '';
                        $('#promo_id').append('<option value="' + promo.id + '" '+ selectvalue +'>' + promo.nama + '</option>');
                    }
                    var produk = response.produk;
                    for (var j = 0; j < produk.length; j++) {
                        var promo = produk[j];
                        var selectvalue = promo.id == old_promo_id ? 'selected' : '';
                        $('#promo_id').append('<option value="' + promo.id + '" '+ selectvalue +'>' + promo.nama + '</option>');
                    }
                    $('#promo_id').attr('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.log(error)
                },
                complete: function() {
                    $('#btnCheckPromo').html('<i class="fa fa-search" data-bs-toggle="tooltip" title="" data-bs-original-title="fa fa-search" aria-label="fa fa-search"></i>')
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
                    var total_transaksi = parseInt($('#total_harga').val());
                    var total_promo;
                    switch (response.diskon) {
                        case 'persen':
                            total_promo = total_transaksi * parseInt(response.diskon_persen) / 100;
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
                    total_harga();
                },
                error: function(xhr, status, error) {
                    console.log(error)
                }
            });
        }
    </script>
@endsection