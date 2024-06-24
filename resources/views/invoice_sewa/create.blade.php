@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h5 class="card-title">Buat Invoice Sewa</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('invoice_sewa.store') }}" method="POST" id="addForm">
                <div class="row">
                    <div class="col-sm">
                            @csrf
                            <div class="row justify-content-around">
                                <div class="col-md-6 border rounded pt-3">
                                    <h5 class="card-title">Informasi Pelanggan</h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Customer</label>
                                                <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') ?? $kontrak->customer->nama }}" class="form-control" required disabled>
                                                <input type="hidden" id="customer_id" name="customer_id" value="{{ old('customer_id') ?? $kontrak->customer_id }}" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>PIC</label>
                                                <input type="text" id="pic" name="pic" value="{{ old('pic') ?? $kontrak->pic }}" class="form-control" required readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Handphone</label>
                                                <input type="text" id="handhpone" name="handphone" value="{{ old('handphone') ?? $kontrak->handphone }}" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Catatan</label>
                                                <textarea type="text" id="catatan" name="catatan" class="form-control">{{ old('catatan') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 border rounded pt-3">
                                    <h5 class="card-title">Detail Invoice</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>No Invoice</label>
                                                <input type="text" id="no_invoice" name="no_invoice" value="{{ old('no_invoice') ?? $getKode }}" class="form-control" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Invoice</label>
                                                <input type="date" id="tanggal_invoice" name="tanggal_invoice" value="{{ old('tanggal_invoice') ?? date('Y-m-d') }}" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Rekening</label>
                                                <select id="rekening_id" name="rekening_id" class="form-control" required>
                                                    <option value="">Pilih Rekening</option>
                                                    @foreach ($rekening as $item)
                                                        <option value="{{ $item->id }}" {{ $kontrak->rekening_id == $item->id ? 'selected' : '' }}>{{ $item->nama_akun }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>No Kontrak</label>
                                                <input type="text" id="no_sewa" name="no_sewa" value="{{ old('no_sewa') ?? $kontrak->no_kontrak }}" class="form-control"  required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Jatuh Tempo</label>
                                                <input type="date" id="jatuh_tempo" name="jatuh_tempo" value="{{ old('jatuh_tempo') ?? date('Y-m-d') }}" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Sales</label>
                                                <select id="sales_id" name="sales" class="form-control" required>
                                                    <option value="">Pilih Sales</option>
                                                    @foreach ($sales as $item)
                                                        <option value="{{ $item->id }}" {{ $kontrak->sales == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
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
                                    @if(count($kontrak->produk) < 1)
                                    <tr>
                                        <td>
                                            <select id="produk_0" name="nama_produk[]" class="form-control">
                                                <option value="">Pilih Produk</option>
                                                @foreach ($produkSewa as $produk)
                                                    <option value="{{ $produk->produk->kode }}">{{ $produk->produk->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        td><input type="text" name="harga_satuan[]" id="harga_satuan_0" oninput="multiply(this)" class="form-control"  required></td>
                                        <td><input type="number" name="jumlah[]" id="jumlah_0" oninput="multiply(this)" class="form-control"  required></td>
                                        <td><input type="text" name="harga_total[]" id="harga_total_0" class="form-control"  required readonly></td>
                                        <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td>
                                    </tr>
                                    @else
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($kontrak->produk as $produk) 
                                        <tr id="row{{ $i }}">
                                            <td>
                                                <select id="produk_{{ $i }}" name="nama_produk[]" class="form-control">
                                                    <option value="">Pilih Produk</option>
                                                    @foreach ($produkSewa as $pj)
                                                        <option value="{{ $pj->produk->kode }}" data-tipe_produk="{{ $pj->produk->tipe_produk }}" data-harga_jual="{{ $pj->harga }}" {{ $pj->produk->kode == $produk->produk->kode ? 'selected' : '' }}>{{ $pj->produk->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" name="harga_satuan[]" id="harga_satuan_{{ $i }}" oninput="multiply(this)" value="{{ old('satuan.' . $i) ?? $produk->harga }}" class="form-control" required></td>
                                            <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" oninput="multiply(this)" class="form-control" value="{{ old('jumlah.' . $i) ?? $produk->jumlah }}"></td>
                                            <td><input type="text" name="harga_total[]" id="harga_total_{{ $i }}" class="form-control" value="{{ old('harga_total.' . $i) ?? $produk->harga_jual }}" required readonly></td>
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
                                    @endif
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
                                            <th>Sales</th>
                                            <th>Pembuat</th>
                                            <th>Penyetuju</th>
                                            <th>Pemeriksa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="sales">-</td>
                                            <td id="pembuat">{{ Auth::user()->name ?? '-' }}</td>
                                            <td id="penyetuju">-</td>
                                            <td id="pemeriksa">-</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%;">
                                                <input type="date" class="form-control" id="tgl_sales" name="tanggal_sales" value="{{ date('Y-m-d') }}">
                                            </td>
                                            <td id="tgl_pembuat" style="width: 25%;">{{ date('Y-m-d') }}</td>
                                            <td id="tgl_penyetuju" style="width: 25%;">{{ isset($kontrak->tanggal_penyetujju) ? \Carbon\Carbon::parse($kontrak->tanggal_penyetujju)->format('Y-m-d') : '-' }}</td>
                                            <td id="tgl_pemeriksa" style="width: 25%;">{{ isset($kontrak->tanggal_pemeriksa) ? \Carbon\Carbon::parse($kontrak->tanggal_pemeriksa)->format('Y-m-d') : '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4 border rounded mt-3 pt-3">
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Subtotal</label>
                                    <div class="col-lg-9">
                                        <input type="text" id="subtotal" name="subtotal" value="{{ $kontrak->subtotal }}" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Diskon</label>
                                    <div class="col-lg-9">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                <select id="promo_id" name="promo_id" class="form-control" disabled>
                                                </select>
                                            </div>
                                            <input type="hidden" id="old_promo_id" value="{{ $kontrak->promo_id }}">
                                            {{-- <div class="col-3 ps-0 mb-0">
                                                <button id="btnCheckPromo" class="btn btn-primary w-100"><i class="fa fa-search" data-bs-toggle="tooltip" title="" data-bs-original-title="fa fa-search" aria-label="fa fa-search"></i></button>
                                            </div> --}}
                                        </div>
                                        <input type="text" class="form-control" name="total_promo" id="total_promo" value="{{ old('total_promo') }}" readonly>
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
                                        <input type="text" class="form-control" name="ongkir_nominal" id="ongkir_nominal" value="{{ $kontrak->ongkir_nominal }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">DP</label>
                                    <div class="col-lg-9">
                                        <input type="text" id="dp" name="dp" value="0" class="form-control" required>
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-primary w-100 rounded-0 mr-1" onclick="dp_val(10)">10%</button>
                                            <button type="button" class="btn btn-primary w-100 rounded-0 mx-1" onclick="dp_val(20)">20%</button>
                                            <button type="button" class="btn btn-primary w-100 rounded-0 ml-1" onclick="dp_val(50)">50%</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Total Harga</label>
                                    <div class="col-lg-9">
                                        <input type="text" id="total_harga" name="total_tagihan" value="{{ $kontrak->total_harga }}" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Sisa Bayar</label>
                                    <div class="col-lg-9">
                                        <input type="text" id="sisa_bayar" name="sisa_bayar" value="{{ $sisaBayar }}" class="form-control" required readonly>
                                        <input type="hidden" id="sisa_bayar_awal" value="{{ $sisaBayar }}">
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-md-4 border rounded mt-3 pt-3">
                                <div class="custom-file-container" data-upload-id="myFirstImage">
                                    <label>Bukti Kirim (Single File) <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">clear</a></label>
                                    <label class="custom-file-container__custom-file">
                                    <input type="file" class="custom-file-container__custom-file__custom-file-input" accept="image/*">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
                                    <span class="custom-file-container__custom-file__custom-file-control"></span>
                                    </label>
                                    <div class="custom-file-container__image-preview"></div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button class="btn btn-primary" type="submit" {{ $sisaBayar == 0 ? 'disabled' : '' }}>Submit</button>
                    <a href="{{ route('invoice_sewa.index') }}" class="btn btn-secondary" type="button">Back</a>
                </div>
                @if ($sisaBayar == 0)
                <p class="text-end text-danger">Kontrak sudah lunas</p>
                @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function(){
            var total_transaksi = $('#total_harga').val();
            var old_promo_id = $('#old_promo_id').val();
            var produk = [];
            var tipe_produk = [];
            $('select[id^="produk_"]').each(function() {
                produk.push($(this).val());
                tipe_produk.push($(this).select2().find(":selected").data("tipe_produk"));
                
            });
            checkPromo(total_transaksi, tipe_produk, produk, old_promo_id);
            $('#promo_id').trigger('change');
            $('#sales').trigger('change');
            calculatePromo(old_promo_id);
            
            $('[id^=produk], #sales_id, #ongkir_id, #rekening_id').select2();
            $('#sales_id').trigger('change');
            var i = '{{ count($kontrak->produk) }}';
            $('#add').click(function(){
                var newRow = '<tr id="row'+i+'"><td>' + 
                                '<select id="produk_'+i+'" name="nama_produk[]" class="form-control">'+
                                    '<option value="">Pilih Produk</option>'+
                                    '@foreach ($produkSewa as $pj)'+
                                        '<option value="{{ $pj->produk->kode }}" data-tipe_produk="{{ $pj->produk->tipe_produk }}" data-harga_jual="{{ $pj->harga }}">{{ $pj->produk->nama }}</option>'+
                                    '@endforeach'+
                                '</select>'+
                            '</td>'+
                            '<td><input type="text" name="harga_satuan[]" id="harga_satuan_'+i+'" oninput="multiply(this)" class="form-control"></td>'+
                            '<td><input type="number" name="jumlah[]" id="jumlah_'+i+'" oninput="multiply(this)" class="form-control"></td>'+
                            '<td><input type="text" name="harga_total[]" id="harga_total_'+i+'" class="form-control" readonly></td>'+
                            '<td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">x</button></td></tr>';
                $('#dynamic_field').append(newRow);
                $('#produk_' + i).select2();
                i++;
            })
            $(document).on('input', '[id^=harga_satuan]', function() {
                let input = $(this);
                let value = input.val();
                
                if (!isNumeric(cleanNumber(value))) {
                value = value.replace(/[^\d]/g, "");
                }

                value = cleanNumber(value);
                let formattedValue = formatNumber(value);
                
                input.val(formattedValue);
            });
            $('#addForm').on('submit', function(e) {
                // Add input number cleaning for specific inputs
                let inputs = $('#addForm').find('[id^=harga_satuan], [id^=harga_total], #subtotal, #total_promo, #ppn_nominal, #pph_nominal, #ongkir_nominal, #total_harga, #sisa_bayar, #dp');
                inputs.each(function() {
                    let input = $(this);
                    let value = input.val();
                    let cleanedValue = cleanNumber(value);

                    // Set the cleaned value back to the input
                    input.val(cleanedValue);
                });

                return true;
            });
            let inputs = $('#addForm').find('[id^=harga_satuan], [id^=harga_total], #subtotal, #total_promo, #ppn_nominal, #pph_nominal, #ongkir_nominal, #total_harga, #sisa_bayar, #dp');
            inputs.each(function() {
                let input = $(this);
                let value = input.val();
                let formattedValue = formatNumber(value);

                // Set the cleaned value back to the input
                input.val(formattedValue);
            });
        })
        $(document).on('click', '.btn_remove', function() {
                var button_id = $(this).attr("id");
                $('#row'+button_id+'').remove();
                multiply($('#harga_satuan_0'))
                multiply($('#jumlah_0'))
            });
        $('#ongkir_id').on('change', function() {
            var ongkir = $("#ongkir_id option:selected").text();
            var biaya = ongkir.split('-')[1];
            $('#ongkir_nominal').val(formatNumber(biaya));
            total_harga();
        });
        $('#ongkir_nominal, #total_promo, #ppn_persen, #pph_persen').on('input', function(){
            total_harga();
        })
        $('#sales_id').on('change', function() {
            var nama_sales = $("#sales_id option:selected").text();
            var val_sales = $("#sales_id option:selected").val();
            if(val_sales != ""){
                $('#sales').text(nama_sales)
            } else {
                $('#sales').text('-')
            }
        });
        $('#promo_id').change(function() {
            var promo_id = $(this).select2().find(":selected").val()
            if(!promo_id){
                $('#total_promo').val(0);
                var inputs = $('input[name="harga_total[]"]');
                var subtotal = 0;
                inputs.each(function() {
                    subtotal += parseInt(cleanNumber($(this).val())) || 0;
                });
                $('#subtotal').val(formatNumber(subtotal))
                total_harga();
                return 0;
            } 
            calculatePromo(promo_id);
        });
        $('#dp').on('input', function(){
            var dp = $(this).val();
            $(this).val(formatNumber(dp))
            var sisa_bayar = $('#sisa_bayar_awal').val();
            sisa_bayar = sisa_bayar - dp;
            $('#sisa_bayar').val(formatNumber(sisa_bayar));
        });
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
            total_harga();
        }
        function total_harga() {
            ppn();
            pph();
            var subtotal = cleanNumber($('#subtotal').val()) || 0;
            var ppn_nominal = cleanNumber($('#ppn_nominal').val()) || 0;
            var pph_nominal = cleanNumber($('#pph_nominal').val()) || 0;
            var ongkir_nominal = cleanNumber($('#ongkir_nominal').val()) || 0;
            var harga_total = parseInt(subtotal) + parseInt(ppn_nominal) + parseInt(pph_nominal) + parseInt(ongkir_nominal);
            $('#total_harga').val(formatNumber(harga_total));
        }
        function ppn(){
            var ppn_persen = $('#ppn_persen').val()
            var subtotal = cleanNumber($('#subtotal').val())
            var ppn_nominal = ppn_persen * subtotal / 100
            $('#ppn_nominal').val(formatNumber(ppn_nominal))
        }
        function pph(){
            var pph_persen = $('#pph_persen').val()
            var subtotal = cleanNumber($('#subtotal').val())
            var pph_nominal = pph_persen * subtotal / 100
            $('#pph_nominal').val(formatNumber(pph_nominal))
        }
        function dp_val(persen){
            var sisa_bayar = $('#sisa_bayar_awal').val();
            var dp = sisa_bayar * persen / 100;
            sisa_bayar = sisa_bayar - dp;
            $('#dp').val(formatNumber(dp));
            $('#sisa_bayar').val(formatNumber(sisa_bayar));
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
                    var sub_total = parseInt(cleanNumber($('#subtotal').val()));
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
                    $('#total_promo').val(formatNumber(total_promo));

                    var inputs = $('input[name="harga_total[]"]');
                    var subtotal = 0;
                    inputs.each(function() {
                        subtotal += parseInt(cleanNumber($(this).val())) || 0;
                    });
                    $('#subtotal').val(formatNumber(subtotal))
                    
                    if (/(poin|TRD|GFT)/.test(total_promo)) {
                        total_promo = 0;
                    } else {
                        total_promo = parseInt(total_promo) || 0;
                        $('#subtotal').val(formatNumber((subtotal - total_promo)));
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