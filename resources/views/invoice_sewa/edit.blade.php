@extends('layouts.app-von')

@section('css')
@endsection
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h5 class="card-title">Edit Invoice Sewa</h5>
                    </div>
                </div>
            </div>
            <form action="{{ route('invoice_sewa.update', ['invoice_sewa' => $data->id]) }}" method="POST" id="addForm">
            @csrf
            @method('patch')
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm">
                                <div class="row justify-content-around">
                                    <div class="col-md-6 border rounded pt-3">
                                        <h5 class="card-title">Informasi Pelanggan</h5>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Customer</label>
                                                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') ?? $data->kontrak->customer->nama }}" class="form-control" readonly>
                                                    <input type="hidden" id="customer_id" name="customer_id" value="{{ old('customer_id') ?? $data->kontrak->customer_id }}" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>PIC</label>
                                                    <input type="text" id="pic" name="pic" value="{{ old('pic') ?? $data->kontrak->pic }}" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Handphone</label>
                                                    <input type="text" id="handhpone" name="handphone" value="{{ old('handphone') ?? $data->kontrak->handphone }}" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Catatan</label>
                                                    <textarea type="text" id="catatan" name="catatan" class="form-control">{{ old('catatan') ?? $data->catatan }}</textarea>
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
                                                    <input type="text" id="no_invoice" name="no_invoice" value="{{ old('no_invoice') ?? $data->no_invoice }}" class="form-control" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label>Tanggal Invoice</label>
                                                    <input type="date" id="tanggal_invoice" name="tanggal_invoice" value="{{ old('tanggal_invoice') ?? $data->tanggal_invoice }}" class="form-control" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Rekening</label>
                                                    <select id="rekening" name="rekening_id" class="form-control" required>
                                                        <option value="">Pilih Rekening</option>
                                                        @foreach ($rekening as $item)
                                                            <option value="{{ $item->id }}" {{ $data->kontrak->rekening_id == $item->id ? 'selected' : '' }}>{{ $item->nama_akun }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>No Kontrak</label>
                                                    <input type="text" id="no_sewa" name="no_sewa" value="{{ old('no_sewa') ?? $data->no_sewa }}" class="form-control"  readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label>Jatuh Tempo</label>
                                                    <input type="date" id="jatuh_tempo" name="jatuh_tempo" value="{{ old('jatuh_tempo') ?? $data->jatuh_tempo }}" class="form-control" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Sales</label>
                                                    <select id="sales_id" name="sales" class="form-control" required>
                                                        <option value="">Pilih Sales</option>
                                                        @foreach ($sales as $item)
                                                            <option value="{{ $item->id }}" {{ $data->sales == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
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
                                        </tr>
                                    </thead>
                                    <tbody id="dynamic_field">
                                        @if(count($data->produk) < 1)
                                        <tr>
                                            <td>
                                                <select id="produk_0" name="nama_produk[]" class="form-control" required>
                                                    <option value="">Pilih Produk</option>
                                                    @foreach ($produkSewa as $produk)
                                                        <option value="{{ $produk->produk->kode }}">{{ $produk->produk->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            td><input type="text" name="harga_satuan[]" id="harga_satuan_0" oninput="multiply(this)" class="form-control" required></td>
                                            <td><input type="text" name="jumlah[]" id="jumlah_0" oninput="multiply(this)" class="form-control" required></td>
                                            <td><input type="text" name="harga_total[]" id="harga_total_0" class="form-control" required></td>
                                        </tr>
                                        @else
                                        @php
                                        $i = 0;
                                        @endphp
                                        @foreach ($data->produk as $produk) 
                                        @if($produk->jenis != 'TAMBAHAN')
                                            <tr id="row{{ $i }}">
                                                <td>
                                                    <select id="produk_{{ $i }}" name="nama_produk[]" class="form-control" required>
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
                                                            <option value="{{ $pj->produk->kode }}" data-id="{{ $pj->id }}" data-tipe_produk="{{ $pj->produk->tipe_produk }}"
                                                            @if ($pj->produk->tipe_produk == 6)
                                                                data-tooltip="{{ $desc }}"
                                                            @endif    
                                                            {{ $pj->produk->kode == $produk->produk->kode ? 'selected' : '' }}>{{ $pj->produk->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="text" name="harga_satuan[]" id="harga_satuan_{{ $i }}" oninput="multiply(this)" value="{{ old('satuan.' . $i) ?? $produk->harga }}" class="form-control" required></td>
                                                <td><input type="text" name="jumlah[]" id="jumlah_{{ $i }}" oninput="multiply(this)" class="form-control" value="{{ old('jumlah.' . $i) ?? $produk->jumlah }}" required></td>
                                                <td><input type="text" name="harga_total[]" id="harga_total_{{ $i }}" class="form-control" value="{{ old('harga_total.' . $i) ?? $produk->harga_jual }}" required></td>
                                                @php
                                                    $i++;
                                                @endphp
                                            </tr>
                                        @endif
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
                                            <th><a href="javascript:void(0);" id="add2"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></a></th>
                                        </tr>
                                    </thead>
                                    <tbody id="dynamic_field2">
                                        @if(count($data->produk) < 1)
                                        <tr>
                                            <td>
                                                <select id="produk2_0" name="nama_produk2[]" class="form-control">
                                                    <option value="">Pilih Produk</option>
                                                    @foreach ($produkjuals as $produk)
                                                        <option value="{{ $produk->kode }}" data-id="{{ $produk->id }}" data-harga_jual="{{ $produk->harga }}">{{ $produk->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" name="harga_satuan2[]" id="harga_satuan2_0" oninput="multiply2(this)" class="form-control"  required></td>
                                            <td><input type="text" name="jumlah2[]" id="jumlah2_0" oninput="multiply2(this)" class="form-control"  required></td>
                                            <td><input type="text" name="harga_total2[]" id="harga_total2_0" class="form-control"  required readonly></td>
                                            <td><a href="javascript:void(0);" class="btn_remove2" id="0"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td>
                                        </tr>
                                        @else
                                        @php
                                            $j = 0;
                                        @endphp
                                        @foreach ($data->produk as $produk)
                                            @if ($produk->jenis == 'TAMBAHAN')
                                            <tr id="row2{{ $j }}">
                                                <td>
                                                    <select id="produk2_{{ $j }}" name="nama_produk2[]" class="form-control">
                                                        <option value="">Pilih Produk</option>
                                                        @foreach ($produkjuals as $pj)
                                                            <option value="{{ $pj->kode }}" data-id="{{ $pj->id }}" data-harga_jual="{{ $pj->harga }}" {{ $produk->produk->kode == $pj->kode ? 'selected' : '' }}>{{ $pj->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="text" name="harga_satuan2[]" id="harga_satuan2_{{ $j }}" oninput="multiply2(this)" class="form-control" value="{{ $produk->harga }}"></td>
                                                <td><input type="text" name="jumlah2[]" id="jumlah2_{{ $j }}" oninput="multiply2(this)" class="form-control" value="{{ $produk->jumlah }}"></td>
                                                <td><input type="text" name="harga_total2[]" id="harga_total2_{{ $j }}" class="form-control" value="{{ $produk->harga_jual }}" readonly></td>
                                                <td><a href="javascript:void(0);" class="btn_remove2" id="{{ $j }}"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td>
                                            </tr>
                                            @php
                                                $j++;
                                            @endphp
                                            @endif
                                        @endforeach
                                        @endif
                                        @if($j == 0)
                                        <tr id="row2{{ $j }}">
                                            <td>
                                                <select id="produk2_{{ $j }}" name="nama_produk2[]" class="form-control">
                                                    <option value="">Pilih Produk</option>
                                                    @foreach ($produkjuals as $pj)
                                                        <option value="{{ $pj->kode }}" data-id="{{ $pj->id }}" data-harga_jual="{{ $pj->harga }}">{{ $pj->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" name="harga_satuan2[]" id="harga_satuan2_{{ $j }}" oninput="multiply2(this)" class="form-control" value=""></td>
                                            <td><input type="text" name="jumlah2[]" id="jumlah2_{{ $j }}" oninput="multiply2(this)" class="form-control" value=""></td>
                                            <td><input type="text" name="harga_total2[]" id="harga_total2_{{ $j }}" class="form-control" value="" readonly></td>
                                            <td><a href="javascript:void(0);" class="btn_remove2" id="{{ $j }}"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td>
                                        </tr>
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
                                                <td id="sales">{{ $data->data_sales->nama }}</td>
                                                <td id="pembuat">{{ $data->data_pembuat->nama ?? '-' }}</td>
                                                <td id="penyetuju">{{ $data->data_penyetuju->nama ?? '-' }}</td>
                                                <td id="pemeriksa">{{ $data->data_pemeriksa->nama ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%;">{{ isset($data->tanggal_sales) ? formatTanggal($data->tanggal_sales) : '-' }}</td>
                                                <td id="tgl_pembuat" style="width: 25%;">{{ isset($data->tanggal_pembuat) ? formatTanggal($data->tanggal_pembuat) : '-' }}</td>
                                                <td id="tgl_penyetuju" style="width: 25%;">{{ isset($data->tanggal_penyetuju) ? formatTanggal($data->tanggal_penyetuju) : '-' }}</td>
                                                <td id="tgl_pemeriksa" style="width: 25%;">{{ isset($data->tanggal_pemeriksa) ? formatTanggal($data->tanggal_pemeriksa) : '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title text-center">Riwayat</h4>
                                        </div>
                                        <hr>
                                        <div class="card-body collpase show" id="table_log">
                                            <div class="table-responsive">
                                            <table class="table datanew">
                                                <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Tanggal Perubahan</th>
                                                    <th>Customer</th>
                                                    <th>Pengubah</th>
                                                    <th>Log</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($riwayat as $item)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $item->created_at ?? '-' }}</td>
                                                        <td>{{ $item->subject->kontrak->customer->nama ?? '-' }}</td>
                                                        <td>{{ $item->causer->name ?? '-' }}</td>
                                                        <td>
                                                            @php
                                                                $changes = $item->changes();
                                                                if(isset($changes['old'])){
                                                                    $diff = array_keys(array_diff_assoc($changes['attributes'], $changes['old']));
                                                                    foreach ($diff as $key => $value) {
                                                                        echo "$value: <span class='text-danger'>{$changes['old'][$value]}</span> => <span class='text-success'>{$changes['attributes'][$value]}</span>" . "<br>";
                                                                    }
                                                                } else {
                                                                    echo 'Data Invoice Terbuat';
                                                                }
                                                            @endphp
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 border rounded mt-3 pt-3">
                                    <div class="form-group row mt-1">
                                        <label class="col-lg-3 col-form-label">Subtotal</label>
                                        <div class="col-lg-9">
                                            <input type="text" id="subtotal" name="subtotal" value="" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row mt-1">
                                        <label class="col-lg-3 col-form-label">Diskon</label>
                                        <div class="col-lg-9">
                                            <div class="input-group">
                                                <input type="text" id="promo_persen" name="promo_persen" value="{{ $data->promo_persen ?? 0 }}" class="form-control" readonly aria-describedby="basic-addon3" oninput="validatePersen(this)">
                                                <span class="input-group-text" id="basic-addon3">%</span>
                                            </div>
                                            <input type="text" class="form-control" name="total_promo" id="total_promo" value="{{ $data->kontrak->total_promo }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row mt-1">
                                        <label class="col-lg-3 col-form-label">PPN</label>
                                        <div class="col-lg-9">
                                            <div class="input-group">
                                                <input type="number" id="ppn_persen" name="ppn_persen" value="{{ $data->kontrak->ppn_persen ?? 11 }}" class="form-control" aria-label="Recipient's username" aria-describedby="basic-addon3" readonly>
                                                <span class="input-group-text" id="basic-addon3">%</span>
                                            </div>
                                            <input type="text" class="form-control" name="ppn_nominal" id="ppn_nominal" value="{{ $data->kontrak->ppn_nominal }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row mt-1">
                                        <label class="col-lg-3 col-form-label">PPH</label>
                                        <div class="col-lg-9">
                                            <div class="input-group">
                                                <input type="number" id="pph_persen" name="pph_persen" value="{{ $data->kontrak->pph_persen ?? 2 }}" class="form-control" aria-label="Recipient's username" aria-describedby="basic-addon3" readonly>
                                                <span class="input-group-text" id="basic-addon3">%</span>
                                            </div>
                                            <input type="text" class="form-control" name="pph_nominal" id="pph_nominal" value="{{ $data->kontrak->pph_nominal }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row mt-1">
                                        <label class="col-lg-3 col-form-label">Ongkir</label>
                                        <div class="col-lg-9">
                                            <input type="text" class="form-control" name="ongkir_nominal" id="ongkir_nominal" value="{{ $isFirst ? $data->kontrak->ongkir_nominal : 0 }}" readonly>
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
                                            <input type="text" id="dp" name="dp" value="{{ $data->dp }}" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group row mt-1">
                                        <label class="col-lg-3 col-form-label">Total Harga</label>
                                        <div class="col-lg-9">
                                            <input type="text" id="total_harga" name="total_tagihan" value="{{ $data->total_tagihan }}" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row mt-1">
                                        <label class="col-lg-3 col-form-label">Sisa Bayar</label>
                                        <div class="col-lg-9">
                                            <input type="text" id="sisa_bayar" name="sisa_bayar" value="{{ $data->sisa_bayar }}" class="form-control" required readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button class="btn btn-primary" type="submit" id="btnSubmit">Submit</button>
                        <a href="{{ route('invoice_sewa.index') }}" class="btn btn-secondary" type="button">Back</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        var cekInvoiceNumbers = "{{ $invoice_bayar }}";
        var nextInvoiceNumber = parseInt(cekInvoiceNumbers) + 1;
        $(document).ready(function(){
            multiply($('#jumlah_0'))
            $('[data-toggle="tooltip"]').tooltip({ html: true });
            $('[id^=produk], #sales_id, #ongkir_id, #rekening, #rekening_id, #bayar').select2({
                templateResult: formatState,
                templateSelection: formatState,
            });
            var i = '{{ count($data->kontrak->produk) }}';
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

                                newRow += '<option value="{{ $pj->produk->kode }}" data-id="{{ $pj->id }}" data-tipe_produk="{{ $pj->produk->tipe_produk }}"';
                                @if ($pj->produk->tipe_produk == 6)
                                    newRow += ' data-tooltip="{{ $desc }}"';
                                @endif
                                newRow += '{{ $pj->id == $produk->id ? " selected" : "" }}>';
                                newRow += '{{ $pj->produk->nama }}</option>';
                            @endforeach

                            newRow += '</select>' +
                                            '</td>' +
                                            '<td><input type="text" name="jumlah[]" id="jumlah_' + i + '" class="form-control"></td>' +
                                            '<td><input type="text" name="satuan[]" id="satuan_' + i + '" class="form-control"></td>' +
                                            '<td><input type="text" name="detail_lokasi[]" id="detail_lokasi_' + i + '" class="form-control"></td>' +
                                            '<td><a href="javascript:void(0);" class="btn_remove" id="'+ i +'"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td>' +
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
                                        '<option value="{{ $pj->kode }}" data-id="{{ $pj->id }}" data-tipe_produk="{{ $pj->tipe_produk }}" data-harga_jual="{{ $pj->harga }}">{{ $pj->nama }}</option>'+
                                    '@endforeach'+
                                '</select>'+
                            '</td>'+
                            '<td><input type="text" name="harga_satuan2[]" id="harga_satuan2_' + i + '" oninput="multiply2(this)" class="form-control"  required></td>' +
                            '<td><input type="text" name="jumlah2[]" id="jumlah2_' + i + '" oninput="multiply2(this)" class="form-control"  required></td>' +
                            '<td><input type="text" name="harga_total2[]" id="harga_total2_' + i + '" class="form-control"  required readonly></td>' +
                            '<td><a href="javascript:void(0);" class="btn_remove2" id="'+ i +'"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td></tr>';
                $('#dynamic_field2').append(newRow);
                $('#produk2_' + i).select2();
                i++;
            })
            $(document).on('input', '[id^=harga_satuan], #dp, #ongkir_nominal, #pph_nominal, #ppn_nominal, #total_promo, [id^=jumlah]', function() {
                let input = $(this);
                let value = input.val();
                
                if (!isNumeric(cleanNumber(value))) {
                value = value.replace(/[^\d]/g, "");
                }

                value = cleanNumber(value);
                let formattedValue = formatNumber(value);
                
                input.val(formattedValue);
            });
            let inputs = $('.card-body').find('[id^=harga_satuan], [id^=harga_total], #subtotal, #total_promo, #ppn_nominal, #pph_nominal, #ongkir_nominal, #total_harga, #sisa_bayar, #dp, #nominal, [id^=jumlah]');
            inputs.each(function() {
                let input = $(this);
                let value = input.val();
                let formattedValue = formatNumber(value);

                // Set the cleaned value back to the input
                input.val(formattedValue);
            });
            $('#addForm').on('submit', function(e) {
                // Add input number cleaning for specific inputs
                let inputs = $('#addForm').find('[id^=harga_satuan], [id^=harga_total], #subtotal, #total_promo, #ppn_nominal, #pph_nominal, #ongkir_nominal, #total_harga, #sisa_bayar, #dp, [id^=jumlah]');
                inputs.each(function() {
                    let input = $(this);
                    let value = input.val();
                    let cleanedValue = cleanNumber(value);

                    // Set the cleaned value back to the input
                    input.val(cleanedValue);
                });

                return true;
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
            multiply($('#harga_satuan_0'))
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
            // sisa_bayar = total_harga - dp;
            // $('#sisa_bayar').val(formatNumber(sisa_bayar));
        });
        $('#dp_persen').on('input', function(){
            var total_harga = cleanNumber($('#total_harga').val());

            var dp_persen = $(this).val()
            var dp_nominal = dp_persen * total_harga / 100
            sisa_bayar = total_harga - dp_nominal;
            // $('#dp').val(formatNumber(dp_nominal))
            // $('#sisa_bayar').val(formatNumber(sisa_bayar));
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
        $('#bayar').on('change', function() {
            var caraBayar = $(this).val();
            if (caraBayar == 'transfer') {
                $('#div_rekening').show();
                $('#rekening_id').attr('disabled', false);
                $('#bukti').attr('disabled', false);
            } else {
                $('#div_rekening').hide();
                $('#rekening_id').attr('disabled', true);
                $('#bukti').attr('disabled', true);
            }
        });
        $('#nominal').on('input', function() {
            var nominal = cleanNumber($(this).val());
            var sisaTagihan = cleanNumber($('#sisa_tagihan').val());
            if(nominal < 0) {
                $(this).val(0);
            }
            if(nominal > sisaTagihan) {
                $(this).val(formatNumber(sisaTagihan));
            }
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
            // $('#sisa_bayar').val(formatNumber(harga_total - dp));
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
        function bayar(invoice){
            $('#no_kontrak').val(invoice.no_sewa);
            $('#invoice_sewa_id').val(invoice.id);
            $('#total_tagihan').val(invoice.total_tagihan);
            $('#sisa_tagihan').val(invoice.sisa_bayar);
            $('#nominal').val(formatNumber(invoice.sisa_bayar));
            $('#rekening_id').select2({
                dropdownParent: $("#modalBayar")
            });
            $('#bayar').select2({
                dropdownParent: $("#modalBayar")
            });
            $('#modalBayar').modal('show');
            generateInvoice();
        }
        function generateInvoice() {
            var invoicePrefix = "BYR";
            var currentDate = new Date();
            var year = currentDate.getFullYear();
            var month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
            var day = currentDate.getDate().toString().padStart(2, '0');
            var formattedNextInvoiceNumber = nextInvoiceNumber.toString().padStart(3, '0');

            var generatedInvoice = invoicePrefix + year + month + day + formattedNextInvoiceNumber;
            $('#no_invoice_bayar').val(generatedInvoice);
        }
        function bukti(src){
            var baseUrl = window.location.origin;
            var fullUrl = baseUrl + '/storage/' + src;
            $('#imgBukti').attr('src', fullUrl);
            $('#modalBukti').modal('show');
        }
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