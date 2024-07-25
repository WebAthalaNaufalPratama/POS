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
                                        <td><input type="text" name="harga_satuan[]" id="harga_satuan_0" oninput="multiply(this)" class="form-control"  required></td>
                                        <td><input type="number" name="jumlah[]" id="jumlah_0" oninput="multiply(this)" class="form-control"  required></td>
                                        <td><input type="text" name="harga_total[]" id="harga_total_0" class="form-control"  required readonly></td>
                                        <td><a href="javascript:void(0);" id="add"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></a></td>
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
                                                    @php
                                                    if($pj->produk->tipe_produk == 6){

                                                        $descArray = [];
                                                        foreach ($pj->komponen as $komponen) {
                                                            if (in_array($komponen->tipe_produk, [1, 2])) {
                                                                $descArray[] = $komponen->produk->nama;
                                                            }
                                                        }
                                                        $desc = implode(', ', $descArray);
                                                    } else {
                                                        $desc = '';
                                                    }
                                                    @endphp
                                                        <option value="{{ $pj->produk->kode }}" data-id="{{ $pj->id }}" data-tipe_produk="{{ $pj->produk->tipe_produk }}" data-harga_jual="{{ $pj->harga }}"
                                                        @if ($pj->produk->tipe_produk == 6)
                                                            data-tooltip="{{ $desc }}"
                                                        @endif    
                                                        {{ $pj->produk->kode == $produk->produk->kode ? 'selected' : '' }}>{{ $pj->produk->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" name="harga_satuan[]" id="harga_satuan_{{ $i }}" oninput="multiply(this)" value="{{ old('satuan.' . $i) ?? $produk->harga }}" class="form-control" required></td>
                                            <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" oninput="multiply(this)" class="form-control" value="{{ old('jumlah.' . $i) ?? $produk->jumlah }}"></td>
                                            <td><input type="text" name="harga_total[]" id="harga_total_{{ $i }}" class="form-control" value="{{ old('harga_total.' . $i) ?? $produk->harga_jual }}" required readonly></td>
                                            @if ($i == 0)
                                                <td><a href="javascript:void(0);" id="add"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></a></td>
                                            @else
                                                <td><a href="javascript:void(0);" class="btn_remove" id="{{ $i }}"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td>
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
                <div class="row mt-3">
                    <div class="form-row row">
                        <label>Tambahan Produk</label>
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
                                <tbody id="dynamic_field2">
                                    <tr>
                                        <td>
                                            <select id="produk2_0" name="nama_produk2[]" class="form-control">
                                                <option value="">Pilih Produk</option>
                                                @foreach ($produkjuals as $produk)
                                                    <option value="{{ $produk->kode }}" data-id="{{ $produk->id }}" data-harga_jual="{{ $produk->harga }}">{{ $produk->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" name="harga_satuan2[]" id="harga_satuan2_0" oninput="multiply2(this)" class="form-control"></td>
                                        <td><input type="number" name="jumlah2[]" id="jumlah2_0" oninput="multiply2(this)" class="form-control"></td>
                                        <td><input type="text" name="harga_total2[]" id="harga_total2_0" class="form-control" readonly></td>
                                        <td><a href="javascript:void(0);" id="add2"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></a></td>
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
                                        <div class="input-group">
                                            <input type="text" id="promo_persen" name="promo_persen" value="{{ $data->promo_persen ?? 0 }}" class="form-control" readonly aria-describedby="basic-addon3" oninput="validatePersen(this)">
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                        <input type="text" class="form-control" name="total_promo" id="total_promo" value="{{ $kontrak->total_promo ?? 0 }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">PPN</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="text" id="ppn_persen" name="ppn_persen" value="{{ $kontrak->ppn_persen ?? 11 }}" class="form-control" readonly aria-describedby="basic-addon3"
                                            oninput="validatePersen(this)">
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                        <input type="text" class="form-control" name="ppn_nominal" id="ppn_nominal" value="{{ $kontrak->ppn_nominal }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">PPH</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="number" id="pph_persen" name="pph_persen" value="{{ $kontrak->pph_persen ?? 2 }}" class="form-control" readonly oninput="validatePersen(this)" aria-describedby="basic-addon3">
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                        <input type="text" class="form-control" name="pph_nominal" id="pph_nominal" value="{{ $kontrak->pph_nominal }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Ongkir</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="ongkir_nominal" id="ongkir_nominal" value="{{ $kontrak->ongkir_nominal }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">DP</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="text" id="dp_persen" name="dp_persen" value="0" class="form-control" aria-describedby="basic-addon3"
                                            oninput="validatePersen(this)">
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                        <input type="text" id="dp" name="dp" value="0" class="form-control" required>
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
                                        <input type="text" id="sisa_bayar" name="sisa_bayar" value="" class="form-control" required readonly>
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
                    <button class="btn btn-primary" type="submit" id="btnSubmit">Submit</button>
                    <a href="{{ route('invoice_sewa.index') }}" class="btn btn-secondary" type="button">Back</a>
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
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip({ html: true });
            $('[id^=produk], #sales_id, #ongkir_id, #rekening_id').select2({
                templateResult: formatState,
                templateSelection: formatState,
            });
            $('#sales_id').trigger('change');
            var i = '{{ count($kontrak->produk) }}';
            $('#add').click(function(){
                var newRow = '<tr id="row' + i + '">' +
                '<td>' +
                    '<select id="produk_' + i + '" name="nama_produk[]" class="form-control">' +
                        '<option value="">Pilih Produk</option>';
                            @foreach ($produkSewa as $pj)
                                @php
                                if ($pj->produk->tipe_produk == 6) {
                                    $descArray = [];
                                    foreach ($pj->komponen as $komponen) {
                                        if (in_array($komponen->tipe_produk, [1, 2])) {
                                            $descArray[] = $komponen->produk->nama;
                                        }
                                    }
                                    $desc = implode(', ', $descArray);
                                } else {
                                    $desc = '';
                                }
                                @endphp

                                newRow += '<option value="{{ $pj->produk->kode }}" data-id="{{ $pj->id }}" data-harga_jual="{{ $pj->harga }}" data-tipe_produk="{{ $pj->produk->tipe_produk }}"';
                                @if ($pj->produk->tipe_produk == 6)
                                    newRow += ' data-tooltip="{{ $desc }}"';
                                @endif
                                newRow += '{{ $pj->id == $produk->id ? " selected" : "" }}>';
                                newRow += '{{ $pj->produk->nama }}</option>';
                            @endforeach

                            newRow += '</select>' +
                                            '</td>' +
                                            '<td><input type="text" name="harga_satuan[]" id="harga_satuan_' + i + '" oninput="multiply(this)" class="form-control"  required></td>' +
                                            '<td><input type="number" name="jumlah[]" id="jumlah_' + i + '" oninput="multiply(this)" class="form-control"  required></td>' +
                                            '<td><input type="text" name="harga_total[]" id="harga_total_' + i + '" class="form-control"  required readonly></td>' +
                                            '<td><a href="javascript:void(0);" class="btn_remove" id="' + i + '"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td>' +
                                        '</tr>';
                $('#dynamic_field').append(newRow);
                $('#produk_' + i).select2({
                    templateResult: formatState,
                    templateSelection: formatState
                });
                i++;
            })
            $('#add2').click(function(){
                var newRow = '<tr id="row2'+i+'"><td>' + 
                                '<select id="produk2_'+i+'" name="nama_produk2[]" class="form-control">'+
                                    '<option value="">Pilih Produk</option>'+
                                    '@foreach ($produkjuals as $pj)'+
                                        '<option value="{{ $pj->kode }}" data-id="{{ $pj->id }}" data-tipe_produk="{{ $pj->tipe_produk }}" data-harga_jual="{{ $produk->harga }}">{{ $pj->nama }}</option>'+
                                    '@endforeach'+
                                '</select>'+
                            '</td>'+
                            '<td><input type="text" name="harga_satuan[]" id="harga_satuan2_' + i + '" oninput="multiply2(this)" class="form-control"  required></td>' +
                            '<td><input type="number" name="jumlah[]" id="jumlah2_' + i + '" oninput="multiply2(this)" class="form-control"  required></td>' +
                            '<td><input type="text" name="harga_total[]" id="harga_total2_' + i + '" class="form-control"  required readonly></td>' +
                            '<td><a href="javascript:void(0);" class="btn_remove2" id="' + i + '"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td></tr>';
                $('#dynamic_field2').append(newRow);
                $('#produk2_' + i).select2();
                i++;
            })
            $(document).on('input', '[id^=harga_satuan], #dp, #ongkir_nominal, #pph_nominal, #ppn_nominal, #total_promo', function() {
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
        $(document).on('click', '.btn_remove2', function() {
            var button_id = $(this).attr("id");
            $('#row2'+button_id+'').remove();
        });
        // diskon start
        $('#total_promo').on('input', function(){
            $('#promo_persen').val(0);
            var value = $(this).val().trim();

            if (isNaN(value)) {
                return;
            }
            
            if (value === "") {
                $(this).val(0);
                value = 0;
            } else {
                if (!value.startsWith("0.")) {
                    value = value.replace(/^0+/, '');
                    $(this).val(value);
                }
            }
            var total_promo = $(this).val();
            var inputs = $('input[name="harga_total[]"]');
            var total = 0;
            inputs.each(function() {
                total += parseInt(cleanNumber($(this).val())) || 0;
            });
            $('#subtotal').val(formatNumber((total) - (cleanNumber(total_promo)))) 
            update_pajak(cleanNumber($('#subtotal').val()));
            total_harga();
        });
        $('#promo_persen').on('input', function(){
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
        $('#ppn_nominal').on('input', function(){
            $('#ppn_persen').val(0);
            var value = $(this).val().trim();

            if (isNaN(value)) {
                return;
            }
            
            if (value === "") {
                $(this).val(0);
                value = 0;
            } else {
                if (!value.startsWith("0.")) {
                    value = value.replace(/^0+/, '');
                    $(this).val(value);
                }
            }
            var total_promo = $('#total_promo').val();
            var inputs = $('input[name="harga_total[]"]');
            var total = 0;
            inputs.each(function() {
                total += parseInt(cleanNumber($(this).val())) || 0;
            });
            $('#subtotal').val(formatNumber((total) - (cleanNumber(total_promo)))) 
            total_harga();
        });
        $('#ppn_persen').on('input', function(){
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

            if (isNaN(value)) {
                return;
            }
            
            if (value === "") {
                $(this).val(0);
                value = 0;
            } else {
                if (!value.startsWith("0.")) {
                    value = value.replace(/^0+/, '');
                    $(this).val(value);
                }
            }
            var total_promo = $('#total_promo').val();
            var inputs = $('input[name="harga_total[]"]');
            var total = 0;
            inputs.each(function() {
                total += parseInt(cleanNumber($(this).val())) || 0;
            });
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

        // dp start
        $('#dp').on('input', function(){
            var dp = cleanNumber($(this).val());
            $(this).val(formatNumber(dp))
            var total_harga = cleanNumber($('#total_harga').val());
            sisa_bayar = total_harga - dp;
            $('#sisa_bayar').val(formatNumber(sisa_bayar));
        });
        $('#dp_persen').on('input', function(){
            var total_harga = cleanNumber($('#total_harga').val());

            var dp_persen = $(this).val()
            var dp_nominal = dp_persen * total_harga / 100
            sisa_bayar = total_harga - dp_nominal;
            $('#dp').val(formatNumber(dp_nominal))
            $('#sisa_bayar').val(formatNumber(sisa_bayar));
        });
        // dp end

        $('#sales_id').on('change', function() {
            var nama_sales = $("#sales_id option:selected").text();
            var val_sales = $("#sales_id option:selected").val();
            if(val_sales != ""){
                $('#sales').text(nama_sales)
            } else {
                $('#sales').text('-')
            }
        });
        
        $(document).on('change', '[id^=produk_]', function(){
            var id = $(this).attr('id');
            var parts = id.split('_');
            var nomor = parts[parts.length - 1];
            var harga_jual = $(this).find(":selected").data("harga_jual") ?? 0;
            $('#harga_satuan_' + nomor).val(formatNumber(harga_jual));
            multiply('#harga_satuan_' + nomor);
        });
        $(document).on('change', '[id^=produk2_]', function(){
            var id = $(this).attr('id');
            var parts = id.split('_');
            var nomor = parts[parts.length - 1];
            var harga_jual = $(this).find(":selected").data("harga_jual") ?? 0;
            $('#harga_satuan2_' + nomor).val(formatNumber(harga_jual));
            multiply2('#harga_satuan2_' + nomor);
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

            var inputs1 = $('input[name="harga_total[]"]');
            var total1 = 0;
            var inputs2 = $('input[name="harga_total2[]"]');
            var total2 = 0;
            inputs1.each(function() {
                total1 += parseInt(cleanNumber($(this).val())) || 0;
            });
            inputs2.each(function() {
                total2 += parseInt(cleanNumber($(this).val())) || 0;
            });
            var total = total1 + total2;
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
        function multiply2(element) {
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
                harga_satuan = cleanNumber($('#harga_satuan2_' + id).val());
                if (harga_satuan) {
                    var harga_total = harga_satuan * jumlah
                    $('#harga_total2_'+id).val(formatNumber(harga_total))
                }
            } else if(jenis.split('_').length == 3){
                id = $(element).attr('id').split('_')[2];
                harga_satuan = cleanNumber($(element).val());
                jumlah = $('#jumlah2_' + id).val();
                if (jumlah) {
                    var harga_total = harga_satuan * jumlah
                    $('#harga_total2_'+id).val(formatNumber(harga_total))
                }
            }

            var inputs1 = $('input[name="harga_total[]"]');
            var total1 = 0;
            var inputs2 = $('input[name="harga_total2[]"]');
            var total2 = 0;
            inputs1.each(function() {
                total1 += parseInt(cleanNumber($(this).val())) || 0;
            });
            inputs2.each(function() {
                total2 += parseInt(cleanNumber($(this).val())) || 0;
            });
            var total = total1 + total2;
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
            var dp = parseInt(cleanNumber($('#dp').val()));
            $('#sisa_bayar').val(formatNumber(harga_total - dp));
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
        // function isKelebihan(){
        //     var total_harga = $('#total_harga').val();
        //     var sisa_bayar = $('#sisa_bayar').val();
        //     if (total_harga > sisa_bayar){
        //         $('#btnSubmit').attr('disabled', true);
        //         $('#isKelebihan').removeClass('d-none');
        //     } else {
        //         $('#btnSubmit').attr('disabled', false);
        //         $('#isKelebihan').addClass('d-none');
        //     }
        // }
        function formatState(state) {
            if (!$(state.element).attr('data-tooltip')) {
                return state.text;
            }
            var $state = $(
                '<span>' + state.text + ' <i class="fas fa-info-circle ml-1" data-toggle="tooltip" title="' + $(state.element).attr('data-tooltip') + '"></i></span>'
            );
            return $state;
        }
    </script>
@endsection