@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Detail Kontrak</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm">
                        <div class="row justify-content-around">
                            <div class="col-md-6 border rounded pt-3">
                                <h5 class="card-title">Informasi Pelanggan</h5>
                                <div class="row">
                                    <div class="form-group">
                                        <label>Customer</label>
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                <select id="customer_id" name="customer_id" class="form-control" disabled>
                                                    <option value="">Pilih Customer</option>
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}" {{ $kontraks->customer_id == $customer->id ? 'selected' : ''}}>{{ $customer->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>PIC</label>
                                            <input type="text" id="pic" name="pic" value="{{ $kontraks->pic }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>No NPWP</label>
                                            <input type="text" id="no_npwp" name="no_npwp" value="{{ $kontraks->no_npwp }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Alamat</label>
                                            <textarea type="text" id="alamat" name="alamat" class="form-control" disabled>{{ $kontraks->alamat }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Handphone</label>
                                            <input type="text" id="handhpone" name="handphone" value="{{ $kontraks->handphone }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Nama NPWP</label>
                                            <input type="text" id="nama_npwp" name="nama_npwp" value="{{ $kontraks->nama_npwp }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select id="status" name="status" class="form-control" disabled>
                                                <option value="">Pilih Status</option>
                                                <option value="DRAFT" {{ $kontraks->status == 'DRAFT' ? 'selected' : '' }}>Draft</option>
                                                <option value="AKTIF" {{ $kontraks->status == 'AKTIF' ? 'selected' : '' }}>Aktif</option>
                                                <option value="TIDAK AKTIF" {{ $kontraks->status == 'TIDAK AKTIF' ? 'selected' : '' }}>Tidak Aktif</option>
                                                <option value="SELESAI" {{ $kontraks->status == 'SELESAI' ? 'selected' : '' }}>Selesai</option>
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
                                            <input type="text" id="no_kontrak" name="no_kontrak" value="{{ $kontraks->no_kontrak }}" class="form-control" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Tanggal Mulai</label>
                                            <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ $kontraks->tanggal_mulai }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Masa Sewa</label>
                                            <div class="input-group">
                                                <input type="text" id="masa_sewa" name="masa_sewa" value="{{ $kontraks->masa_sewa }}" class="form-control" placeholder="Masa sewa" aria-label="Recipient's username" aria-describedby="basic-addon2" disabled>
                                                <span class="input-group-text" id="basic-addon2">bulan</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Catatan</label>
                                            <textarea type="text" id="catatan" name="catatan" class="form-control" disabled>{{ $kontraks->catatan }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tanggal Kontrak</label>
                                            <input type="date" id="tanggal_kontrak" name="tanggal_kontrak" value="{{ $kontraks->tanggal_kontrak }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Tanggal Selesai</label>
                                            <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ $kontraks->tanggal_selesai }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Sales</label>
                                            <select id="sales" name="sales" class="form-control" disabled>
                                                <option value="">Pilih Sales</option>
                                                @foreach ($sales as $item)
                                                    <option value="{{ $item->id }}" {{ $kontraks->sales == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Rekening</label>
                                            <select id="rekening_id" name="rekening_id" class="form-control" disabled>
                                                <option value="">Pilih Rekening</option>
                                                @foreach ($rekenings as $rekening)
                                                    <option value="{{ $rekening->id }}" {{ $kontraks->rekening_id == $rekening->id ? 'selected' : '' }}>{{ $rekening->nama_akun }}</option>
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
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="dynamic_field">
                                    @if(count($produks) < 1)
                                    <tr>
                                        <td>
                                            <select id="produk_0" name="nama_produk[]" class="form-control" disabled>
                                                <option value="">Pilih Produk</option>
                                                @foreach ($produkjuals as $produk)
                                                    <option value="{{ $produk->kode }}">{{ $produk->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="harga_satuan[]" id="harga_satuan_0" oninput="multiply(this)" class="form-control" disabled></td>
                                        <td><input type="number" name="jumlah[]" id="jumlah_0" oninput="multiply(this)" class="form-control" disabled></td>
                                        <td><input type="number" name="harga_total[]" id="harga_total_0" class="form-control" disabled></td>
                                        <td></td>
                                        <td><button id="btnPerangkai_0" data-produk="" class="btn btn-primary">Perangkai</button></td>
                                        {{-- <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td> --}}
                                    </tr>
                                    @endif
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($produks as $komponen) 
                                        <tr>
                                            <td>
                                                <select id="produk_{{ $i }}" name="nama_produk[]" class="form-control" disabled>
                                                    <option value="">Pilih Produk</option>
                                                    @foreach ($produkjuals as $produk)
                                                        <option value="{{ $produk->kode }}" data-tipe_produk="{{ $produk->tipe_produk }}" {{ $komponen->produk->kode == $produk->kode ? 'selected' : '' }}>{{ $produk->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" name="harga_satuan[]" id="harga_satuan_{{ $i }}" oninput="multiply(this)" class="form-control" value="{{ $komponen->harga }}" disabled></td>
                                            <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" oninput="multiply(this)" class="form-control" value="{{ $komponen->jumlah }}" disabled></td>
                                            <td><input type="number" name="harga_total[]" id="harga_total_{{ $i }}" class="form-control" value="{{ $komponen->harga_jual }}" readonly></td>
                                            <td>
                                                @if ($komponen->produk->tipe_produk == 6)
                                                <button id="btnGift_{{ $i }}" data-produk="{{ $komponen->id }}" class="btn btn-info w-100">Set Gift</button>
                                                @endif
                                            </td>
                                            <td><button id="btnPerangkai_{{ $i }}" data-produk="{{ $komponen->id }}" class="btn btn-primary">Perangkai</button></td>
                                            {{-- @if ($i == 0)
                                                <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td>
                                            @else
                                                <td><button type="button" name="remove" id="{{ $i }}" class="btn btn-danger btn_remove">x</button></td>
                                            @endif --}}
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
                                            <td id="pengaju">{{ $kontraks->pengaju->nama ?? '-' }}</td>
                                            <td id="pembuat">{{ $kontraks->pembuat->nama ?? '-' }}</td>
                                            <td id="penyetuju">{{ $kontraks->penyetuju->nama ?? '-' }}</td>
                                            <td id="pemeriksa">{{ $kontraks->pemeriksa->nama ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td id="tgl_sales" class="col-md-3">
                                                <label id="tanggal_sales" name="tanggal_sales">{{ isset($kontraks->tanggal_sales) ? \Carbon\Carbon::parse($kontraks->tanggal_sales)->format('Y-m-d') : '' }}</label>
                                            </td>
                                            <td id="tgl_pembuat" class="col-md-3">
                                                <label id="tanggal_pembuat" name="tanggal_pembuat">{{ isset($kontraks->tanggal_pembuat) ? \Carbon\Carbon::parse($kontraks->tanggal_pembuat)->format('Y-m-d') : '-' }}</label>
                                            </td>
                                            <td id="tgl_penyetuju" class="col-md-3">
                                                <label id="tanggal_penyetuju" name="tanggal_penyetuju">{{ isset($kontraks->tanggal_penyetuju) ? \Carbon\Carbon::parse($kontraks->tanggal_penyetuju)->format('Y-m-d') : '-' }}</label>
                                            </td>
                                            <td id="tgl_pemeriksa" class="col-md-3">
                                                <label id="tanggal_pemeriksa" name="tanggal_pemeriksa">{{ isset($kontraks->tanggal_pemeriksa) ? \Carbon\Carbon::parse($kontraks->tanggal_pemeriksa)->format('Y-m-d') : '-' }}</label>
                                            </td>
                                        </tr>                                        
                                    </tbody>
                                </table>
                                <div class="col-sm-12 mt-3">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Riwayat</h4>
                                        </div>
                                        <div class="card-body">
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
                                                        <td>{{ $item->subject->customer->nama ?? '-' }}</td>
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
                                                                    echo 'Data Kontrak Terbuat';
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
                            </div>
                            <div class="col-md-4 border rounded mt-3 pt-3">
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Subtotal</label>
                                    <div class="col-lg-9">
                                        <input type="text" id="subtotal" name="subtotal" value="{{ $kontraks->subtotal }}" class="form-control" readonly>
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
                                            <input type="hidden" id="old_promo_id" value="{{ $kontraks->promo_id }}">
                                            <div class="col-3 ps-0 mb-0">
                                                <button id="btnCheckPromo" class="btn btn-primary w-100"><i class="fa fa-search" data-bs-toggle="tooltip" title="" data-bs-original-title="fa fa-search" aria-label="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control" name="total_promo" id="total_promo" value="{{ old('total_promo') }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">PPN</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="number" id="ppn_persen" name="ppn_persen" value="{{ $kontraks->ppn_persen ?? 11 }}" class="form-control" aria-label="Recipient's username" aria-describedby="basic-addon3" disabled>
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                        <input type="text" class="form-control" name="ppn_nominal" id="ppn_nominal" value="{{ $kontraks->ppn_nominal }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">PPH</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="number" id="pph_persen" name="pph_persen" value="{{ $kontraks->pph_persen ?? 2 }}" class="form-control" aria-label="Recipient's username" aria-describedby="basic-addon3" disabled>
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                        <input type="text" class="form-control" name="pph_nominal" id="pph_nominal" value="{{ $kontraks->pph_nominal }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Ongkir</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <select id="ongkir_id" name="ongkir_id" class="form-control" disabled>
                                                <option value="">Pilih Ongkir</option>
                                                @foreach ($ongkirs as $ongkir)
                                                    <option value="{{ $ongkir->id }}" {{ $ongkir->id == $kontraks->ongkir_id ? 'selected' : '' }}>{{ $ongkir->nama }}-{{ $ongkir->biaya }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="number" class="form-control" name="ongkir_nominal" id="ongkir_nominal" value="{{ $kontraks->ongkir_nominal }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Total Harga</label>
                                    <div class="col-lg-9">
                                        <input type="number" id="total_harga" name="total_harga" value="{{ $kontraks->total_harga }}" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <a href="{{ route('kontrak.index') }}" class="btn btn-secondary" type="button">Back</a>
                </div>
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
            <input type="hidden" name="route" value="{{ request()->route()->getName() }},kontrak,{{ request()->route()->parameter('kontrak') }}">
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
<div class="modal fade" id="modalPerangkai" tabindex="-1" aria-labelledby="modalPerangkaiLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalPerangkaiLabel">Atur Perangkai</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="form_perangkai" action="{{ route('form.store') }}" method="POST">
            @csrf
            <input type="hidden" name="route" value="{{ request()->route()->getName() }},kontrak,{{ request()->route()->parameter('kontrak') }}">
            <div class="mb-3">
                <div class="row">
                    <div class="col-sm-8">
                    <label for="prdTerjual" class="col-form-label">Produk</label>
                    <input type="text" class="form-control" name="produk_id" id="prdTerjual" readonly required>
                </div>
                <input type="hidden" name="prdTerjual_id" id="prdTerjual_id" value="">
                <div class="col-sm-4">
                    <label for="jml_produk" class="col-form-label">Jumlah</label>
                    <input type="number" class="form-control" name="jml_produk" id="jml_produk" readonly required>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label for="no_form" class="col-form-label">No Form Perangkai</label>
              <input type="text" class="form-control" name="no_form" id="no_form" readonly required>
            </div>
            <div class="mb-3">
              <label for="jenis_rangkaian" class="col-form-label">Jenis Rangkaian</label>
              <input type="text" class="form-control" name="jenis_rangkaian" id="jenis_rangkaian" value="Sewa" readonly required>
              </div>
            <div class="mb-3">
              <label for="tanggal" class="col-form-label">Tanggal</label>
              <input type="date" class="form-control" name="tanggal" id="add_tanggal" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="mb-3">
              <label for="jml_perangkai" class="col-form-label">Jumlah Perangkai</label>
              <input type="number" class="form-control" name="jml_perangkai" id="jml_perangkai" required>
            </div>
            <div class="mb-3">
                <label for="perangkai_id" class="col-form-label">Perangkai</label>
                <div id="div_perangkai" class="form-group">
                  <select id="perangkai_id_0" name="perangkai_id[]" class="form-control" required>
                      <option value="">Pilih Perangkai</option>
                      @foreach ($perangkai as $item)
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
<div class="modal fade" id="modalSetGift" aria-labelledby="modalSetGift" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalSetGift">Atur Gift</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="form_setGift" action="{{ route('addKomponen') }}" method="POST">
            @csrf
            <input type="hidden" name="route" value="{{ request()->route()->getName() }},kontrak,{{ request()->route()->parameter('kontrak') }}">
            <div class="mb-3">
                <div class="row">
                    <div class="col-sm-8">
                    <label for="data_produk" class="col-form-label">Produk</label>
                    <input type="text" class="form-control" id="data_produk" readonly required>
                </div>
                <input type="hidden" name="data_produk_id" id="data_produk_id" value="">
                <div class="col-sm-4">
                    <label for="jml_data_produk" class="col-form-label">Jumlah</label>
                    <input type="number" class="form-control" id="jml_data_produk" readonly required>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label for="jml_new_produk" class="col-form-label">Jumlah Bunga/Pot</label>
              <input type="number" class="form-control" id="jml_new_produk" required>
            </div>
            <div class="mb-3">
                <label for="new_produk" class="col-form-label">Bunga/Pot</label>
                <div id="div_new_produk" class="form-group">
                    <div id="div_produk_jumlah_0" class="row">
                        <div class="col-sm-6">
                            <select id="new_produk_0" name="new_produk[]" class="form-control" required>
                                <option value="">Pilih Bunga/Produk</option>
                                @foreach ($bungapot as $item)
                                  <option value="{{ $item->id }}">({{ $item->tipe->nama }}) {{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <select id="new_produk_kondisi_0" name="new_produk_kondisi[]" class="form-control" required>
                                @foreach ($kondisi as $item)
                                  <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <input type="number" class="form-control" id="jml_tambahan_0" name="jml_tambahan[]" placeholder="Jumlah">
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
{{-- modal end --}}
@endsection

@section('scripts')
    <script>
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function() {
            $('#sales').trigger('change');
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

            $('[id^=produk], #customer_id, #sales, #rekening_id, #status, #ongkir_id, #promo_id, #add_tipe').select2();
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
                            '<td><button id="btnGift_'+i+'" data-produk="{{ $komponen }}" class="btn btn-info">Set Gift</button></td>' +
                            '<td><button id="btnPerangkai_'+i+'" data-produk="{{ $komponen->id }}" class="btn btn-primary">Perangkai</button></td>' +
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
        $('#btnAddCustomer').click(function(e) {
            e.preventDefault()
            $('#addcustomer').modal('show');
        });
        $('[id^=btnPerangkai]').click(function(e) {
            e.preventDefault();
            var produk_id = $(this).data('produk');
            getDataPerangkai(produk_id);
        });
        $('#jml_perangkai').on('input', function(e) {
            e.preventDefault();
            var jumlah = $(this).val();
            jumlah = parseInt(jumlah) > 10 ? 10 : parseInt(jumlah);
            $('[id^="perangkai_id_"]').each(function() {
                $(this).select2('destroy');
                $(this).remove();
            });
            if(jumlah < 1) return 0;
            for(var i = 0; i < jumlah; i++){
                var rowPerangkai = 
                    '<select id="perangkai_id_' + i + '" name="perangkai_id[]" class="form-control">' +
                    '<option value="">Pilih Perangkai</option>' +
                    '@foreach ($perangkai as $item)' +
                    '<option value="{{ $item->id }}">{{ $item->nama }}</option>' +
                    '@endforeach' +
                    '</select>';
                $('#div_perangkai').append(rowPerangkai);
                $('#perangkai_id_' + i).select2({
                    dropdownParent: $("#modalPerangkai")
                });
            }
        })
        $('[id^=btnGift]').click(function(e) {
            e.preventDefault();
            var produk_id = $(this).data('produk');
            getKomponenProduk(produk_id);
        });
        $('#jml_new_produk').on('input', function(e) {
            e.preventDefault();
            var jumlah = $(this).val();
            jumlah = parseInt(jumlah) > 10 ? 10 : parseInt(jumlah);
            $('[id^="div_produk_jumlah_"]').each(function() {
                $(this).remove();
            });
            if(jumlah < 1) return 0;
            for(var i = 0; i < jumlah; i++){
                var row_bungapot = 
                    '<div id="div_produk_jumlah_'+i+'" class="row g-0">' +
                    '<div class="col-sm-6"">' +
                    '<select id="new_produk_' + i + '" name="new_produk[]" class="form-control">' +
                    '<option value="">Pilih Bunga/Produk</option>' +
                    '@foreach ($bungapot as $item)' +
                    '<option value="{{ $item->id }}">({{ $item->tipe->nama }}){{ $item->nama }}</option>' +
                    '@endforeach' +
                    '</select>' +
                    '</div>' +
                    '<div class="col-sm-4"">' +
                    '<select id="new_produk_kondisi_' + i + '" name="new_produk_kondisi[]" class="form-control">' +
                    '@foreach ($kondisi as $item)' +
                    '<option value="{{ $item->id }}">{{ $item->nama }}</option>' +
                    '@endforeach' +
                    '</select>' +
                    '</div>' +
                    '<div class="col-sm-2">' +
                    '<input type="number" class="form-control" id="jml_tambahan_'+i+'" name="jml_tambahan[]" placeholder="Jumlah">' +
                    '</div>' +
                    '</div>';
                $('#div_new_produk').append(row_bungapot);
                $('#new_produk_' + i).select2({
                    dropdownParent: $("#modalSetGift")
                });
                $('#new_produk_kondisi_' + i).select2({
                    dropdownParent: $("#modalSetGift")
                });
            }
        })
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
        function getDataPerangkai(produk_id){
            var data = {
                produk_id: produk_id,
            };
            $.ajax({
                url: '/getProdukTerjual',
                type: 'GET',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    $('#prdTerjual').val(response.produk.nama);
                    $('#prdTerjual_id').val(response.id);
                    $('#jml_produk').val(response.jumlah);
                    $('#no_form').val(response.kode_form);
                    $('#jml_perangkai').val(response.perangkai.length);
                    $('[id^="perangkai_id_"]').each(function() {
                        $(this).remove();
                    });
                    if(response.perangkai.length > 0){
                        for(var i = 0; i < response.perangkai.length; i++){
                            var rowPerangkai = 
                                '<select id="perangkai_id_' + i + '" name="perangkai_id[]" class="form-control">' +
                                '<option value="">Pilih Perangkai</option>' +
                                '@foreach ($perangkai as $item)' +
                                '<option value="{{ $item->id }}">{{ $item->nama }}</option>' +
                                '@endforeach' +
                                '</select>';
                            $('#div_perangkai').append(rowPerangkai);
                            $('#div_perangkai select').each(function(index) {
                                $(this).val(response.perangkai[index].perangkai_id);
                            });
                            $('#perangkai_id_' + i).select2({
                                dropdownParent: $("#modalPerangkai")
                            });
                        }
                    }
                    $('#modalPerangkai').modal('show');
                },
                error: function(xhr, status, error) {
                    console.log(error)
                }
            });
        }
        function getKomponenProduk(produk_id){
            var data = {
                produk_id: produk_id,
            };
            $.ajax({
                url: '/getProdukTerjual',
                type: 'GET',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    console.log(response);
                    $('#data_produk').val(response.produk.nama);
                    $('#data_produk_id').val(response.id);
                    $('#jml_data_produk').val(response.jumlah);
                    $('#modalSetGift').modal('show');
                    $('[id^="div_produk_jumlah_"]').each(function() {
                        $(this).remove();
                    });

                    var pot_bunga = 0
                    if(response.komponen.length > 0){
                        for(var i = 0; i < response.komponen.length; i++){
                            if(response.komponen[i].tipe_produk == 1 || response.komponen[i].tipe_produk == 2){
                                pot_bunga++;
                                var row_bungapot = 
                                '<div id="div_produk_jumlah_'+i+'" class="row g-0">' +
                                '<div class="col-sm-6"">' +
                                '<select id="new_produk_' + i + '" name="new_produk[]" class="form-control">' +
                                '<option value="">Pilih Bunga/Produk</option>' +
                                '@foreach ($bungapot as $item)' +
                                '<option value="{{ $item->id }}">({{ $item->tipe->nama }}){{ $item->nama }}</option>' +
                                '@endforeach' +
                                '</select>' +
                                '</div>' +
                                '<div class="col-sm-4"">' +
                                '<select id="new_produk_kondisi_' + i + '" name="new_produk_kondisi[]" class="form-control">' +
                                '@foreach ($kondisi as $item)' +
                                '<option value="{{ $item->id }}">{{ $item->nama }}</option>' +
                                '@endforeach' +
                                '</select>' +
                                '</div>' +
                                '<div class="col-sm-2">' +
                                '<input type="number" class="form-control" id="jml_tambahan_'+i+'" name="jml_tambahan[]" placeholder="Jumlah">' +
                                '</div>' +
                                '</div>';
                                $('#div_new_produk').append(row_bungapot);
                                $('#new_produk_kondisi_' + i).val(response.komponen[i].kondisi);
                                $('#jml_tambahan_' + i).val(response.komponen[i].jumlah);
                                $('#new_produk_' + i).val(response.komponen[i].produk.id);
                                $('#new_produk_' + i).select2({
                                    dropdownParent: $("#modalSetGift")
                                });
                                $('#new_produk_kondisi_' + i).select2({
                                    dropdownParent: $("#modalSetGift")
                                });
                            }
                        }
                    }
                    $('#jml_new_produk').val(pot_bunga);
                    $('#modalSetGift').modal('show');
                },
                error: function(xhr, status, error) {
                    console.log(error)
                }
            });
        }
    </script>
@endsection