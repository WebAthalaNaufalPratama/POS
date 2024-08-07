@extends('layouts.app-von')

@section('css')
<style>
    .accordion-head:hover {
        background: rgba(0, 0, 0, 0.1);
        transition: background 0.3s ease;
    }
</style>
@endsection
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h5 class="card-title">Detail Invoice Sewa</h5>
                    </div>
                </div>
            </div>
            <form action="{{ route('invoice_sewa.update', ['invoice_sewa' => $data->id]) }}" method="POST" id="editForm">
            @csrf
            @method('patch')
            <div class="card-body">
                @if ($data->sisa_bayar == 0)
                <div class="ribbon ribbon-success ribbon-right">Lunas</div>
                @endif
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
                                                <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') ?? $data->kontrak->customer->nama }}" class="form-control" disabled disabled>
                                                <input type="hidden" id="customer_id" name="customer_id" value="{{ old('customer_id') ?? $data->kontrak->customer_id }}" class="form-control" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>PIC</label>
                                                <input type="text" id="pic" name="pic" value="{{ old('pic') ?? $data->kontrak->pic }}" class="form-control" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Handphone</label>
                                                <input type="text" id="handhpone" name="handphone" value="{{ old('handphone') ?? $data->kontrak->handphone }}" class="form-control" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Catatan</label>
                                                <textarea type="text" id="catatan" name="catatan" class="form-control" disabled>{{ old('catatan') ?? $data->catatan }}</textarea>
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
                                                <input type="text" id="no_invoice" name="no_invoice" value="{{ old('no_invoice') ?? $data->no_invoice }}" class="form-control" disabled>
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Invoice</label>
                                                <input type="date" id="tanggal_invoice" name="tanggal_invoice" value="{{ old('tanggal_invoice') ?? $data->tanggal_invoice }}" class="form-control" disabled>
                                            </div>
                                            <div class="form-group">
                                                <label>Rekening</label>
                                                <select id="rekening" name="rekening_id" class="form-control" disabled>
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
                                                <input type="text" id="no_sewa" name="no_sewa" value="{{ old('no_sewa') ?? $data->no_sewa }}" class="form-control"  disabled>
                                            </div>
                                            <div class="form-group">
                                                <label>Jatuh Tempo</label>
                                                <input type="date" id="jatuh_tempo" name="jatuh_tempo" value="{{ old('jatuh_tempo') ?? $data->jatuh_tempo }}" class="form-control" disabled>
                                            </div>
                                            <div class="form-group">
                                                <label>Sales</label>
                                                <select id="sales_id" name="sales" class="form-control" disabled>
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
                                            <select id="produk_0" name="nama_produk[]" class="form-control" disabled>
                                                <option value="">Pilih Produk</option>
                                                @foreach ($produkSewa as $produk)
                                                    <option value="{{ $produk->produk->kode }}">{{ $produk->produk->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        td><input type="text" name="harga_satuan[]" id="harga_satuan_0" oninput="multiply(this)" class="form-control" disabled></td>
                                        <td><input type="text" name="jumlah[]" id="jumlah_0" oninput="multiply(this)" class="form-control" disabled></td>
                                        <td><input type="text" name="harga_total[]" id="harga_total_0" class="form-control" disabled></td>
                                    </tr>
                                    @else
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($data->produk as $produk) 
                                    @if($produk->jenis != 'TAMBAHAN')
                                        <tr id="row{{ $i }}">
                                            <td>
                                                <select id="produk_{{ $i }}" name="nama_produk[]" class="form-control" disabled>
                                                    <option value="">Pilih Produk</option>
                                                    @foreach ($produkSewa as $pj)
                                                        <option value="{{ $pj->produk->kode }}" data-tipe_produk="{{ $pj->produk->tipe_produk }}" {{ $pj->produk->kode == $produk->produk->kode ? 'selected' : '' }}>{{ $pj->produk->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" name="harga_satuan[]" id="harga_satuan_{{ $i }}" oninput="multiply(this)" value="{{ old('satuan.' . $i) ?? $produk->harga }}" class="form-control" disabled></td>
                                            <td><input type="text" name="jumlah[]" id="jumlah_{{ $i }}" oninput="multiply(this)" class="form-control" value="{{ old('jumlah.' . $i) ?? $produk->jumlah }}" disabled></td>
                                            <td><input type="text" name="harga_total[]" id="harga_total_{{ $i }}" class="form-control" value="{{ old('harga_total.' . $i) ?? $produk->harga_jual }}" disabled></td>
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
                                    </tr>
                                </thead>
                                <tbody id="dynamic_field2">
                                    @if(count($data->produk) < 1)
                                    <tr>
                                        <td>
                                            <select id="produk2_0" name="nama_produk2[]" class="form-control" disabled>
                                                <option value="">Pilih Produk</option>
                                                @foreach ($produkjuals as $produk)
                                                    <option value="{{ $produk->kode }}" data-id="{{ $produk->id }}" data-harga_jual="{{ $produk->harga }}">{{ $produk->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" name="harga_satuan2[]" id="harga_satuan2_0" oninput="multiply2(this)" class="form-control" disabled></td>
                                        <td><input type="text" name="jumlah2[]" id="jumlah2_0" oninput="multiply2(this)" class="form-control" disabled></td>
                                        <td><input type="text" name="harga_total2[]" id="harga_total2_0" class="form-control" disabled readonly></td>
                                    </tr>
                                    @else
                                    @php
                                        $j = 0;
                                    @endphp
                                    @foreach ($data->produk as $produk)
                                        @if ($produk->jenis == 'TAMBAHAN')
                                        <tr id="row2{{ $j }}">
                                            <td>
                                                <select id="produk2_{{ $j }}" name="nama_produk2[]" class="form-control" disabled>
                                                    <option value="">Pilih Produk</option>
                                                    @foreach ($produkjuals as $pj)
                                                        <option value="{{ $pj->kode }}" data-id="{{ $pj->id }}" data-harga_jual="{{ $pj->harga }}" {{ $produk->produk->kode == $pj->kode ? 'selected' : '' }}>{{ $pj->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" name="harga_satuan2[]" id="harga_satuan2_{{ $j }}" oninput="multiply2(this)" class="form-control" value="{{ $produk->harga }}" disabled></td>
                                            <td><input type="text" name="jumlah2[]" id="jumlah2_{{ $j }}" oninput="multiply2(this)" class="form-control" value="{{ $produk->jumlah }}" disabled></td>
                                            <td><input type="text" name="harga_total2[]" id="harga_total2_{{ $j }}" class="form-control" value="{{ $produk->harga_jual }}" readonly disabled></td>
                                        </tr>
                                        @php
                                            $j++;
                                        @endphp
                                        @endif
                                    @endforeach
                                    @endif
                                    @if($j == 0)
                                    <tr>
                                        <td>
                                            <select id="produk2_{{ $j }}" name="nama_produk2[]" class="form-control" disabled>
                                                <option value="">Pilih Produk</option>
                                                @foreach ($produkjuals as $pj)
                                                    <option value="{{ $pj->kode }}" data-id="{{ $pj->id }}" data-harga_jual="{{ $pj->harga }}">{{ $pj->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" name="harga_satuan2[]" id="harga_satuan2_{{ $j }}" oninput="multiply2(this)" class="form-control" value="" disabled></td>
                                        <td><input type="text" name="jumlah2[]" id="jumlah2_{{ $j }}" oninput="multiply2(this)" class="form-control" value="" disabled></td>
                                        <td><input type="text" name="harga_total2[]" id="harga_total2_{{ $j }}" class="form-control" value="" disabled></td>
                                        {{-- <td><button type="button" name="add2" id="add2" class="btn btn-success">+</button></td> --}}
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
                                            <td id="penyetuju">{{ $kontraks->data_penyetuju->name ?? (Auth::user()->hasRole('Auditor') ? Auth::user()->name : '') }}</td>
                                            <td id="pemeriksa">{{ $kontraks->data_pemeriksa->name ?? (Auth::user()->hasRole('Finance') ? Auth::user()->name : '') }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%;">{{ isset($data->tanggal_sales) ? formatTanggal($data->tanggal_sales) : '-' }}</td>
                                            <td id="tgl_pembuat" style="width: 25%;">{{ isset($data->tanggal_pembuat) ? formatTanggal($data->tanggal_pembuat) : '-' }}</td>
                                            <td id="tgl_penyetuju"  style="width: 25%;">
                                                @if(Auth::user()->hasRole('Auditor') && !$data->tanggal_penyetuju)
                                                <input type="date" class="form-control" name="tanggal_penyetuju" id="tanggal_penyetuju" required value="{{ $data->tanggal_penyetuju ? \Carbon\Carbon::parse($data->tanggal_penyetuju)->format('Y-m-d') : date('Y-m-d') }}">
                                                @else
                                                <label id="tanggal_penyetuju" name="tanggal_penyetuju">{{ isset($data->tanggal_penyetuju) ? formatTanggal($data->tanggal_penyetuju) : '-' }}</label>
                                                @endif
                                            </td>
                                            <td id="tgl_pemeriksa"  style="width: 25%;">
                                                 @if(Auth::user()->hasRole('Finance') && !$data->tanggal_pemeriksa)
                                                 <input type="date" class="form-control" name="tanggal_pemeriksa" id="tanggal_pemeriksa" value="{{ date('Y-m-d') }}">
                                                 @else
                                                 <label id="tanggal_pemeriksa" name="tanggal_pemeriksa">{{ isset($data->tanggal_pemeriksa) ? formatTanggal($data->tanggal_pemeriksa) : '-' }}</label>
                                                @endif
                                            </td>
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
                                        <input type="text" id="subtotal" name="subtotal" value="{{ $data->kontrak->subtotal }}" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Diskon</label>
                                    <div class="col-lg-9">
                                        {{-- <div class="row align-items-center">
                                            <div class="col-12">
                                                <select id="promo_id" name="promo_id" class="form-control" disabled>
                                                </select>
                                            </div>
                                            <input type="hidden" id="old_promo_id" value="{{ $data->kontrak->promo_id }}"> --}}
                                            {{-- <div class="col-3 ps-0 mb-0">
                                                <button id="btnCheckPromo" class="btn btn-primary w-100"><i class="fa fa-search" data-bs-toggle="tooltip" title="" data-bs-original-title="fa fa-search" aria-label="fa fa-search"></i></button>
                                            </div> --}}
                                        {{-- </div> --}}
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
                                            <input type="number" id="ppn_persen" name="ppn_persen" value="{{ $data->kontrak->ppn_persen ?? 11 }}" class="form-control" aria-label="Recipient's username" aria-describedby="basic-addon3" disabled>
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                        <input type="text" class="form-control" name="ppn_nominal" id="ppn_nominal" value="{{ $data->kontrak->ppn_nominal }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">PPH</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="number" id="pph_persen" name="pph_persen" value="{{ $data->kontrak->pph_persen ?? 2 }}" class="form-control" aria-label="Recipient's username" aria-describedby="basic-addon3" disabled>
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                        <input type="text" class="form-control" name="pph_nominal" id="pph_nominal" value="{{ $data->kontrak->pph_nominal }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Ongkir</label>
                                    <div class="col-lg-9">
                                        {{-- <div class="input-group">
                                            <select id="ongkir_id" name="ongkir_id" class="form-control" disabled>
                                                <option value="">Pilih Ongkir</option>
                                                @foreach ($ongkirs as $ongkir)
                                                    <option value="{{ $ongkir->id }}" {{ $ongkir->id == $data->kontrak->ongkir_id ? 'selected' : '' }}>{{ $ongkir->nama }}-{{ $ongkir->biaya }}</option>
                                                @endforeach
                                            </select>
                                        </div> --}}
                                        <input type="text" class="form-control" name="ongkir_nominal" id="ongkir_nominal" value="{{ $isFirst ? $data->kontrak->ongkir_nominal : 0 }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">DP</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="text" id="dp_persen" name="dp_persen" value="0" class="form-control" aria-describedby="basic-addon3"
                                            oninput="validatePersen(this)" disabled>
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                        <input type="text" id="dp" name="dp" value="{{ $data->dp }}" class="form-control" disabled>
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
                                        <input type="text" id="sisa_bayar" name="sisa_bayar" value="{{ $data->sisa_bayar }}" class="form-control" disabled>
                                        <input type="hidden" id="sisa_bayar_awal" value="{{ $data->sisa_bayar }}">
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
                    <input type="hidden" name="konfirmasi" id="hiddenActionInput" value="">
                    @if((Auth::user()->hasRole('AdminGallery') && $data->status == 'TUNDA') || (Auth::user()->hasRole('Auditor') && $data->status == 'DIKONFIRMASI' && !$data->tanggal_penyetuju) || (Auth::user()->hasRole('Finance') && $data->status == 'DIKONFIRMASI' && !$data->tanggal_pemeriksa))
                    <button class="btn btn-success confirm-btn" data-action="confirm" type="button">Konfirmasi</button>
                    @endif
                    @if(Auth::user()->hasRole('AdminGallery') && $data->status == 'TUNDA')
                    <button class="btn btn-danger confirm-btn" data-action="cancel" type="button">Batal</button>
                    @endif
                    <a href="{{ route('invoice_sewa.index') }}" class="btn btn-secondary" type="button">Back</a>
                </div>
            </div>
            </form>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    @if ($data->sisa_bayar != 0)
                    <div class="page-header">
                        <div class="page-title">
                            <h4 class="card-title">Pembayaran</h4>
                        </div>
                        @if(in_array('pembayaran_sewa.store', $thisUserPermissions) && $data->status == 'DIKONFIRMASI')
                        <div class="page-btn">
                            <a href="javascript:void(0);" onclick="bayar({{ $data }})" class="btn btn-added">Tambah Pembayaran</a>
                        </div>
                        @endif
                    </div>
                    @else
                    <h4 class="card-title text-center">Pembayaran</h4>
                    @endif
                </div>
                <hr>
                <div class="card-body collapse show" id="table_pembayaran">
                    <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>No Invoice Pembayaran</th>
                            <th>Nominal</th>
                            <th>Tanggal Bayar</th>
                            <th>Metode</th>
                            <th>Rekening</th>
                            <th class="text-center">Status</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = $pembayaran->where('status_bayar', 'BELUM LUNAS')->count();
                            @endphp
                            @foreach ($pembayaran as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->no_invoice_bayar }}</td>
                                <td>{{ formatRupiah($item->nominal) }}</td>
                                <td>{{ formatTanggal($item->tanggal_bayar) }}</td>
                                <td>{{ $item->cara_bayar }}</td>
                                <td>{{ $item->cara_bayar == 'transfer' ? $item->rekening->nama_akun : '-' }}</td>
                                <td class="text-center">
                                    @if ($item->status_bayar == 'LUNAS')
                                        <span class="badges bg-lightgreen">{{ $item->status_bayar }}</span>
                                    @elseif ($item->status_bayar == 'BELUM LUNAS')
                                        <span class="badges bg-lightgrey">Pembayaran {{ $i }}</span>
                                        @php
                                            $i--;
                                        @endphp
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);"  onclick="bukti('{{ $item->bukti }}')" class="dropdown-item"><img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Bukti</a>
                                        </li>
                                        @if(in_array('pembayaran.edit', $thisUserPermissions))
                                            <li>
                                                <a href="javascript:void(0);" onclick="editbayar({{ $item->id }})" class="dropdown-item"><img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                            </li>
                                        @endif
                                    </ul>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBayar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form Pembayaran</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="bayarForm" action="{{ route('pembayaran_sewa.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="no_invoice">Nomor Kontrak</label>
                                    <input type="text" class="form-control" id="no_kontrak" name="no_kontrak" placeholder="Nomor Kontrak" required readonly>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label for="no_invoice">Nomor Invoice</label>
                                    <input type="text" class="form-control" id="no_invoice_bayar" name="no_invoice_bayar" placeholder="Nomor Invoice" value="{{ $invoice_bayar }}" required readonly>
                                    <input type="hidden" id="invoice_sewa_id" name="invoice_sewa_id" value="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="no_invoice">Total Tagihan</label>
                                    <input type="text" class="form-control" id="total_tagihan" name="total_tagihan" placeholder="Total Taqgihan" required readonly>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label for="no_invoice">Sisa Tagihan</label>
                                    <input type="text" class="form-control" id="sisa_tagihan" name="sisa_tagihan" placeholder="Sisa Taqgihan" required readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="bayar">Cara Bayar</label>
                                    <select class="form-control" id="bayar" name="cara_bayar" required>
                                        <option value="">Pilih Cara Bayar</option>
                                        <option value="cash">Cash</option>
                                        <option value="transfer">Transfer</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-12" id="div_rekening" style="display: none">
                                    <label for="bankpenerima">Rekening Vonflorist</label>
                                    <select class="form-control" id="rekening_id" name="rekening_id" required>
                                        <option value="">Pilih Rekening Von</option>
                                        @foreach ($bankpens as $bankpen)
                                        <option value="{{ $bankpen->id }}">{{ $bankpen->nama_akun }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="nominal">Nominal</label>
                                    <input type="text" class="form-control" id="nominal" name="nominal" value="" placeholder="Nominal Bayar" required>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label for="tanggalbayar">Tanggal</label>
                                    <input type="date" class="form-control" id="tanggal_bayar" name="tanggal_bayar" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="buktibayar">Unggah Bukti</label>
                                    <input type="file" class="form-control" id="bukti" name="bukti" required onchange="previewImage(this, 'add_preview')" accept="image/*">
                                    <img class="mt-2" src="" alt="" id="add_preview" style="width: 100%;height:auto;">
                                </div>
                            </div>
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
<div class="modal fade" id="modalEditBayar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Form Pembayaran</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST" id="editBayarForm" enctype="multipart/form-data">
                @csrf
                @method('patch')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="no_invoice">Nomor Kontrak</label>
                                    <input type="text" class="form-control" id="edit_no_kontrak" name="no_kontrak" placeholder="Nomor Kontrak" required readonly>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label for="no_invoice">Nomor Invoice</label>
                                    <input type="text" class="form-control" id="edit_no_invoice_bayar" name="no_invoice_bayar" placeholder="Nomor Invoice" value="" required readonly>
                                    <input type="hidden" id="edit_invoice_sewa_id" name="invoice_sewa_id" value="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="no_invoice">Total Tagihan</label>
                                    <input type="text" class="form-control" id="edit_total_tagihan" name="total_tagihan" placeholder="Total Taqgihan" required readonly>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label for="no_invoice">Sisa Tagihan</label>
                                    <input type="text" class="form-control" id="edit_sisa_tagihan" name="sisa_tagihan" placeholder="Sisa Taqgihan" required readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="bayar">Cara Bayar</label>
                                    <select class="form-control" id="edit_bayar" name="cara_bayar" required>
                                        <option value="">Pilih Cara Bayar</option>
                                        <option value="cash">Cash</option>
                                        <option value="transfer">Transfer</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-12" id="edit_rekening" style="display: none">
                                    <label for="bankpenerima">Rekening Vonflorist</label>
                                    <select class="form-control" id="edit_rekening_id" name="rekening_id" required>
                                        <option value="">Pilih Rekening Von</option>
                                        @foreach ($bankpens as $bankpen)
                                        <option value="{{ $bankpen->id }}">{{ $bankpen->nama_akun }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="nominal">Nominal</label>
                                    <input type="text" class="form-control" id="edit_nominal" name="nominal" value="" placeholder="Nominal Bayar" required>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label for="tanggalbayar">Tanggal</label>
                                    <input type="date" class="form-control" id="edit_tanggal_bayar" name="tanggal_bayar" value="" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="buktibayar">Unggah Bukti</label>
                                    <input type="file" class="form-control" id="edit_bukti" name="bukti" accept="image/*" onchange="previewImage(this, 'edit_preview')">
                                </div>
                                <img id="edit_preview" src="" alt="your image" style="max-width: 100%"/>
                            </div>
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
<div class="modal fade" id="modalBukti" tabindex="-1" aria-labelledby="addAkunlabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addAkunlabel">Bukti</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body d-flex justify-content-center">
            <img id="imgBukti" src="" alt="" style="max-width: 100%;height: auto;">
        </div>
        <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        var cekInvoiceNumbers = "{{ $invoice_bayar }}";
        var nextInvoiceNumber = parseInt(cekInvoiceNumbers) + 1;
        $(document).ready(function(){
            if ($('#add_preview').attr('src') === '') {
                $('#add_preview').attr('src', defaultImg);
            }
            if ($('#edit_preview').attr('src') === '') {
                $('#edit_preview').attr('src', defaultImg);
            }
            multiply($('#jumlah_0'))
            $('[id^=produk], #sales_id, #ongkir_id, #rekening, #rekening_id, #bayar').select2();
            var i = '{{ count($data->kontrak->produk) }}';
            $('#add').click(function(){
                var newRow = '<tr id="row'+i+'"><td>' + 
                                '<select id="produk_'+i+'" name="nama_produk[]" class="form-control">'+
                                    '<option value="">Pilih Produk</option>'+
                                    '@foreach ($produkSewa as $pj)'+
                                        '<option value="{{ $pj->produk->kode }}" data-tipe_produk="{{ $pj->produk->tipe_produk }}">{{ $pj->produk->nama }}</option>'+
                                    '@endforeach'+
                                '</select>'+
                            '</td>'+
                            '<td><input type="number" name="harga_satuan[]" id="harga_satuan_'+i+'" oninput="multiply(this)" class="form-control"></td>'+
                            '<td><input type="text" name="jumlah[]" id="jumlah_'+i+'" oninput="multiply(this)" class="form-control"></td>'+
                            '<td><input type="number" name="harga_total[]" id="harga_total_'+i+'" class="form-control" readonly></td>'+
                            '<td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">x</button></td></tr>';
                $('#dynamic_field').append(newRow);
                $('#produk_' + i).select2();
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
                            '<td><input type="text" name="harga_satuan2[]" id="harga_satuan2_' + i + '" oninput="multiply2(this)" class="form-control"  required></td>' +
                            '<td><input type="text" name="jumlah2[]" id="jumlah2_' + i + '" oninput="multiply2(this)" class="form-control"  required></td>' +
                            '<td><input type="text" name="harga_total2[]" id="harga_total2_' + i + '" class="form-control"  required readonly></td>' +
                            '<td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove2">x</button></td></tr>';
                $('#dynamic_field2').append(newRow);
                $('#produk2_' + i).select2();
                i++;
            })
            $(document).on('input', '[id^=nominal]', function() {
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
            var dp = $(this).val();
            $(this).val(formatNumber(dp))
            var total_harga = cleanNumber($('#total_harga').val());
            sisa_bayar = sisa_bayar - dp;
            $('#sisa_bayar').val(formatNumber(sisa_bayar));
        });
        $('#dp_persen').on('input', function(){
            var total_harga = cleanNumber($('#total_harga').val());

            var dp_persen = $(this).val()
            var dp_nominal = dp_persen * total_harga / 100
            $('#dp').val(formatNumber(dp_nominal))
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
            if ($('#add_preview').attr('src') === '') {
                $('#add_preview').attr('src', defaultImg);
            }
        });
        $('#edit_bayar').on('change', function() {
            var caraBayar = $(this).val();
            if (caraBayar == 'transfer') {
                $('#edit_rekening').show();
                $('#edit_rekening_id').attr('disabled', false);
                $('#edit_bukti').attr('disabled', false);
            } else {
                $('#edit_rekening').hide();
                $('#edit_rekening_id').attr('disabled', true);
                $('#edit_bukti').attr('disabled', true);
            }
        });
        $('#nominal').on('input', function() {
            var nominal = parseFloat(cleanNumber($(this).val()));
            var sisaTagihan = parseFloat(cleanNumber($('#sisa_tagihan').val()));
            if(nominal < 0) {
                $(this).val(0);
            }
            if(nominal > sisaTagihan) {
                $(this).val(formatNumber(sisaTagihan));
            }
        });
        $('#edit_nominal').on('input', function() {
            var nominal = parseFloat(cleanNumber($(this).val()));
            var sisaTagihan = parseFloat(cleanNumber($('#edit_sisa_tagihan').val()));
            if(nominal < 0) {
                $(this).val(0);
            }
            if(nominal > sisaTagihan) {
                $(this).val(formatNumber(sisaTagihan));
            }
        });
        $(document).on('input', '#nominal, #edit_nominal', function() {
            let input = $(this);
            let value = input.val();
            
            if (!isNumeric(cleanNumber(value))) {
            value = value.replace(/[^\d]/g, "");
            }

            value = cleanNumber(value);
            let formattedValue = formatNumber(value);
            
            input.val(formattedValue);
        });
        $('#bayarForm').on('submit', function(e) {
            let inputs = $('#bayarForm').find('#nominal');
            inputs.each(function() {
                let input = $(this);
                let value = input.val();
                let cleanedValue = cleanNumber(value);

                input.val(cleanedValue);
            });

            return true;
        });
        $('#editBayarForm').on('submit', function(e) {
            let inputs = $('#editBayarForm').find('#edit_nominal');
            inputs.each(function() {
                let input = $(this);
                let value = input.val();
                let cleanedValue = cleanNumber(value);

                input.val(cleanedValue);
            });

            return true;
        });
        $('.confirm-btn').on('click', function() {
            var action = $(this).data('action');
            var message = (action === 'confirm') 
                        ? "Apakah Anda yakin ingin mengkonfirmasi kontrak ini?" 
                        : "Apakah Anda yakin ingin membatalkan kontrak ini?";
            var confirmButtonText = (action === 'confirm') ? "Ya, Konfirmasi!" : "Ya, Batalkan!";
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: confirmButtonText,
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (action === 'confirm') {
                        $('#hiddenActionInput').val('confirm');
                    } else if (action === 'cancel') {
                        $('#hiddenActionInput').val('cancel');
                    }

                    $('#editForm').submit();
                }
            });
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
                    $('#add_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
        $('#edit_bukti').on('change', function() {
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
                    $('#edit_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
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
            $('#total_tagihan').val(formatNumber(invoice.total_tagihan));
            $('#sisa_tagihan').val(formatNumber(invoice.sisa_bayar));
            $('#nominal').val(formatNumber(invoice.sisa_bayar));
            $('#rekening_id').select2({
                dropdownParent: $("#modalBayar")
            });
            $('#bayar').select2({
                dropdownParent: $("#modalBayar")
            });
            $('#modalBayar').modal('show');
            $('#bayar').trigger('change');
        }
        function bukti(src){
            var baseUrl = window.location.origin;
            var fullUrl = baseUrl + '/storage/' + src;
            $('#imgBukti').attr('src', fullUrl);
            $('#modalBukti').modal('show');
        }
        function editbayar(id){
            $.ajax({
                    type: "GET",
                    url: "/pembayaran_sewa/"+id+"/show",
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        $('#editBayarForm').attr('action', `{{ route("pembayaran_sewa.update", ":id") }}`.replace(':id', id));
                        $('#edit_no_kontrak').val(response.sewa.no_sewa);
                        $('#edit_no_invoice_bayar').val(response.no_invoice_bayar);
                        $('#edit_invoice_sewa_id').val(response.sewa.id);
                        $('#edit_total_tagihan').val(formatNumber(response.sewa.total_tagihan));
                        $('#edit_sisa_tagihan').val(formatNumber(parseInt(response.sewa.sisa_bayar) + parseInt(response.nominal)));
                        $('#edit_nominal').val(formatNumber(response.nominal));
                        $('#edit_tanggal_bayar').val(response.tanggal_bayar);
                        $('#edit_rekening_id').val(response.rekening_id).change();
                        $('#edit_bayar').val(response.cara_bayar).change();
                        if(response.bukti){
                            $('#edit_preview').attr('src', '/storage/'+response.bukti);
                        } else {
                            $('#edit_preview').attr('src', defaultImg);
                        }
                        $('#edit_rekening_id').select2({
                            dropdownParent: $("#modalEditBayar")
                        });
                        $('#edit_bayar').select2({
                            dropdownParent: $("#modalEditBayar")
                        });
                        $('#modalEditBayar').modal('show');
                        $('#edit_bayar').trigger('change');
                    },
                    error: function(error) {
                        toastr.error(JSON.parse(error.responseText).msg, 'Error', {
                            closeButton: true,
                            tapToDismiss: false,
                            rtl: false,
                            progressBar: true
                        });
                    }
                });
        }
    </script>
@endsection