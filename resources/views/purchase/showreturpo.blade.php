@extends('layouts.app-von')

@section('content')
<div id="form" class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Data Retur Pembelian</h5>
            </div>
            <div class="card-body">
                {{-- <form action="{{ route('returbeli.store') }}" method="POST" enctype="multipart/form-data" id="addForm"> --}}
                <div class="row">
                    <div class="col-sm">
                            @csrf
                            <div class="row justify-content-around">
                                <div class="col-md-6 border rounded pt-3">
                                    <h5 class="card-title">Informasi Supplier</h5>
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Supplier</label>
                                            <select id="supplier_id" name="supplier_id" class="form-control" required readonly>
                                                <option value="{{ $data->invoice->pembelian->supplier_id }}">{{ $data->invoice->pembelian->supplier->nama }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Lokasi</label>
                                            <select id="lokasi_id" name="lokasi_id" class="form-control" required readonly>
                                                <option value="{{ $data->invoice->pembelian->lokasi_id }}">{{ $data->invoice->pembelian->lokasi->nama }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Catatan</label>
                                            <textarea type="text" id="catatan" name="catatan" class="form-control" readonly>{{ old('catatan') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 border rounded pt-3">
                                    <h5 class="card-title">Detail Retur</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Tanggal PO</label>
                                                <input type="text" id="tanggal_po" name="tanggal_po" value="{{ old('tanggal_po') ?? tanggalindo($data->invoice->pembelian->tgl_dibuat) }}" 
                                                    class="form-control" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Invoice</label>
                                                <input type="text" id="tanggal_invoice" name="tanggal_invoice" value="{{ old('tanggal_invoice') ?? tanggalindo($data->invoice->tgl_inv) }}" class="form-control" required readonly>
                                            </div>
                                            <input type="hidden" name="invoicepo_id" value="{{ $data->invoice->id }}">
                                            <div class="form-group">
                                                <label>Tanggal Retur</label>
                                                <input type="date" id="tgl_retur" name="tgl_retur" value="{{ $data->tgl_retur }}" class="form-control" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Komplain</label>
                                                <select id="komplain" name="komplain" class="form-control" required disabled>
                                                    <option value="">Pilih Komplain</option>
                                                    @if($data->invoice->sisa == 0)
                                                        <option value="Refund" {{ $data->komplain == 'Refund' ? 'selected' : '' }}>Refund</option>
                                                    @else
                                                    <option value="Diskon" {{ $data->komplain == 'Diskon' ? 'selected' : '' }}>Diskon</option>
                                                    <option value="Retur" {{ $data->komplain == 'Retur' ? 'selected' : '' }}>Retur</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>No PO</label>
                                                <input type="text" id="no_po" name="no_po" value="{{ $data->invoice->pembelian->no_po }}" class="form-control" required readonly>
                                            </div>
                                            <!-- <div class="form-group">
                                                <label>No PO Retur</label>
                                                <input type="text" id="no_po_retur" name="no_po_retur" class="form-control" required readonly>
                                            </div> -->
                                            <div class="form-group">
                                                <label>No Invoice</label>
                                                <input type="text" id="no_invoice" name="no_invoice" value="{{ $data->invoice->no_inv }}" class="form-control" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>No Retur</label>
                                                <input type="text" id="no_retur" name="no_retur" value="{{ $data->no_retur }}" value="" class="form-control" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>File</label>
                                                {{-- <div class="input-group">
                                                    <input type="file" id="file" name="file" value="" class="form-control" accept=".pdf,image/*">
                                                </div> --}}
                                                <img id="preview" src="{{ $data->foto ? '/storage/' . $data->foto : '' }}" alt="your image" />
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
                                        <th>No</th>
                                        {{-- <th>Kode Produk</th> --}}
                                        <th>Nama Produk</th>
                                        <th>Alasan</th>
                                        <th>Jumlah</th>
                                        <th id="thDiskon">Diskon</th>
                                        <th>Harga satuan</th>
                                        <th>Harga Total</th>
                                        {{-- <th></th> --}}
                                    </tr>
                                </thead>
                                <tbody id="dynamic_field">
                                    @foreach ($data->produkretur as $item)
                                        <tr>
                                            <td>1</td>
                                            <input type="hidden" name="kode_produk[]" id="kode_produk_0" class="form-control" required readonly>
                                            <td style="width: 20%">
                                                <select id="produk_0" name="nama_produk[]" class="form-control" required disabled>
                                                    <option value="">Pilih Produk</option>
                                                    @foreach ($data->produkretur as $produk)
                                                        <option value="{{ $produk->id }}"{{ $produk->id == $item->id ? 'selected' : '' }}  data-jumlah="{{ $produk->jml_diterima }}" data-harga="{{ $produk->harga }}" data-diskon="{{ $produk->diskon }}" data-harga_total="{{ $produk->totalharga }}">{{ $produk->produkbeli->produk->nama }} ({{ $produk->produkbeli->kondisi->nama }})</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><textarea name="alasan[]" id="alasan_0" class="form-control" cols="30" readonly>{{ $item->alasan }}</textarea></td>
                                            <td><input type="number" name="jumlah[]" id="jumlah_0" class="form-control jumlah_diterima" required value="{{ $item->jumlah }}" readonly></td>
                                            <td id="tdDiskon_0"><input type="text" name="diskon[]" id="diskon_0" class="form-control" required value="{{ $item->diskon }}" readonly></td>
                                            <td><input type="text" name="harga_satuan[]" id="harga_satuan_0" class="form-control" required readonly value="{{ $item->harga }}"></td>
                                            <td><input type="text" name="harga_total[]" id="harga_total_0" class="form-control" required readonly value="{{ $item->totharga }}"></td>
                                            {{-- <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm">
                        @if ($data->komplain == 'Refund')
                        <div class="row justify-content-around">
                            <div class="col-lg-8 col-md-8 col-sm-6 col-6 border rounded mt-3 pt-3">
                                <div class="page-btn">
                                    Riwayat Pembayaran
                                    {{-- <a href="" data-toggle="modal" data-target="#myModalbayar" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Pembayaran</a> --}}
                                </div>
                                <div class="table-responsive">
                                    <table class="table datanew">
                                        <thead>
                                            <tr>
                                                <th>No Bayar</th>
                                                <th>Tanggal</th>
                                                <th>Metode</th>
                                                <th>Nominal</th>
                                                <th>Bukti</th>
                                                <th>Status</th>
                                                {{-- <th>Aksi</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- @foreach ($datapos as $datapo)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $datapo->no_po }}</td>
                                                <td>{{ $datapo->supplier->nama }}</td>
                                                <td>{{ $datapo->tgl_kirim }}</td>
                                                <td>{{ $datapo->tgl_diterima}}</td>
                                                <td>{{ $datapo->no_do_suplier}}</td>
                                                <td>{{ $datapo->lokasi->nama}}</td>
                                                <td>{{ $datapo->status_dibuat}}</td>
                                               
                                            </tr>
                                            @endforeach --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4 border rounded mt-3 pt-3">
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Subtotal</label>
                                    <div class="col-lg-9">
                                        <input type="text" id="subtotal" name="subtotal" value="{{ $data->subtotal }}" class="form-control" required readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1" id="divOngkir">
                                    <label class="col-lg-3 col-form-label">Biaya Pengiriman</label>
                                    <div class="col-lg-9">
                                        <input type="text" id="biaya_pengiriman" name="biaya_pengiriman" value="{{ $data->ongkir ?? 0 }}" class="form-control" required readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Total Harga</label>
                                    <div class="col-lg-9">
                                        <input type="text" id="total_harga" name="total_harga" value="{{$data->total ?? 0 }}" class="form-control" required readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-start">
                            <div class="col-md-6 border rounded pt-3 me-1 mt-2">
                                <table class="table table-responsive border rounded">
                                    <thead>
                                        <tr>
                                            <th>Dibuat</th>                                              
                                            <th>Dibukukan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="pembuat">
                                                <input type="hidden" name="pembuat" value="{{ Auth::user()->id ?? '' }}">
                                                <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" placeholder="{{ Auth::user()->karyawans->nama ?? '' }}" disabled>
                                            </td>
                                            <td id="pembuku">
                                                <input type="hidden" name="pembuku" value="{{ Auth::user()->id ?? '' }}">
                                                <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" placeholder="{{ Auth::user()->karyawans->nama ?? '' }}" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td id="status_dibuat">
                                                <select id="status_dibuat" name="status_dibuat" class="form-control" required disabled>
                                                    <option value="">Pilih Status</option>
                                                    <option value="draft" {{$data->status_dibuat == 'draft' ? 'selected' : '' }}>Draft</option>
                                                    <option value="publish" {{ ($data->status_dibuat == 'publish') || ($data->status_dibuat == null)  ? 'selected' : '' }}>Publish</option>
                                                </select>
                                            </td>
                                            <td id="status_dibuku">
                                                <select id="status_dibukukan" name="status_dibuku" class="form-control" disabled>
                                                    <option value="">Pilih Status</option>
                                                    <option value="pending" {{$data->status_dibukukan == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="acc" {{ ($data->status_dibukukan == 'acc') || ($data->status_dibukukan == null) ? 'selected' : '' }}>Accept</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td id="tgl_dibuat">
                                                <input type="date" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{$data->tgl_dibuat }}" readonly>
                                            </td>
                                            <td id="tgl_dibuku">
                                                <input type="date" class="form-control" id="tgl_dibuku" name="tgl_dibuku" value="{{$data->tgl_dibuku }}" readonly>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>  
                                <br>                                 
                            </div>
                        </div>
                        @else
                        <div class="row justify-content-around">
                            <div class="col-md-8 border rounded pt-3 mt-3">
                                <table class="table table-responsive border rounded">
                                    <thead>
                                        <tr>
                                            <th>Dibuat</th>                                              
                                            <th>Dibukukan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="pembuat">
                                                <input type="hidden" name="pembuat" value="{{ Auth::user()->id ?? '' }}">
                                                <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" placeholder="{{ Auth::user()->karyawans->nama ?? '' }}" disabled>
                                            </td>
                                            <td id="pembuku">
                                                <input type="hidden" name="pembuku" value="{{ Auth::user()->id ?? '' }}">
                                                <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" placeholder="{{ Auth::user()->karyawans->nama ?? '' }}" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td id="status_dibuat">
                                                <select id="status_dibuat" name="status_dibuat" class="form-control" required disabled>
                                                    <option value="">Pilih Status</option>
                                                    <option value="draft" {{$data->status_dibuat == 'draft' ? 'selected' : '' }}>Draft</option>
                                                    <option value="publish" {{ ($data->status_dibuat == 'publish') || ($data->status_dibuat == null)  ? 'selected' : '' }}>Publish</option>
                                                </select>
                                            </td>
                                            <td id="status_dibuku">
                                                <select id="status_dibukukan" name="status_dibuku" class="form-control" disabled>
                                                    <option value="">Pilih Status</option>
                                                    <option value="pending" {{$data->status_dibukukan == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="acc" {{ ($data->status_dibukukan == 'acc') || ($data->status_dibukukan == null) ? 'selected' : '' }}>Accept</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td id="tgl_dibuat">
                                                <input type="date" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{$data->tgl_dibuat }}" readonly>
                                            </td>
                                            <td id="tgl_dibuku">
                                                <input type="date" class="form-control" id="tgl_dibuku" name="tgl_dibuku" value="{{$data->tgl_dibuku }}" readonly>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>  
                                <br>                                 
                            </div>
                            <div class="col-md-4 border rounded mt-3 pt-3">
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Subtotal</label>
                                    <div class="col-lg-9">
                                        <input type="text" id="subtotal" name="subtotal" value="{{ $data->subtotal }}" class="form-control" required readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1" id="divOngkir">
                                    <label class="col-lg-3 col-form-label">Biaya Pengiriman</label>
                                    <div class="col-lg-9">
                                        <input type="text" id="biaya_pengiriman" name="biaya_pengiriman" value="{{ $data->ongkir ?? 0 }}" class="form-control" required readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Total Harga</label>
                                    <div class="col-lg-9">
                                        <input type="text" id="total_harga" name="total_harga" value="{{$data->total ?? 0 }}" class="form-control" required readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="text-end mt-3">
                    {{-- <button class="btn btn-primary" type="submit">Submit</button> --}}
                    <a href="{{ route('returbeli.index') }}" class="btn btn-secondary" type="button">Back</a>
                </div>
                {{-- </form> --}}
            </div>
        </div>
    </div>
</div>

@endsection

