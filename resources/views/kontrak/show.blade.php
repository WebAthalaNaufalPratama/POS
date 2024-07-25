@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Detail Kontrak</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('kontrak.update', ['kontrak' => $kontraks->id]) }}" method="POST" id="editForm" enctype="multipart/form-data">
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
                                            <input type="number" id="handhpone" name="handphone" value="{{ $kontraks->handphone }}" class="form-control hide-arrow" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Nama NPWP</label>
                                            <input type="text" id="nama_npwp" name="nama_npwp" value="{{ $kontraks->nama_npwp }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select id="status" name="status" class="form-control" disabled>
                                                <option value="TUNDA" {{ $kontraks->status == 'TUNDA' ? 'selected' : '' }}>TUNDA</option>
                                                    <option value="DIKONFIRMASI" {{ $kontraks->status == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                    <option value="BATAL" {{ $kontraks->status == 'BATAL' ? 'selected' : '' }}>BATAL</option>
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
                                            <label>File Kontrak</label>
                                            <div class="input-group">
                                                <input type="file" id="file" name="file" value="" class="form-control" accept="application/pdf" disabled>
                                            </div>
                                        </div>
                                        @if ($kontraks->file)
                                            <a href="/storage/{{ $kontraks->file }}" target="_balnk">Lihat File</a>
                                        @endif
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
                                        <div class="form-group">
                                            <label>Catatan</label>
                                            <textarea type="text" id="catatan" name="catatan" class="form-control" disabled>{{ $kontraks->catatan }}</textarea>
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
                                        <td><input type="text" name="harga_satuan[]" id="harga_satuan_0" oninput="multiply(this)" class="form-control" disabled></td>
                                        <td><input type="number" name="jumlah[]" id="jumlah_0" oninput="multiply(this)" class="form-control" disabled></td>
                                        <td><input type="text" name="harga_total[]" id="harga_total_0" class="form-control" disabled></td>
                                        <td></td>
                                        <td><button id="btnPerangkai_0" data-produk="" class="btn btn-primary">Perangkai</button></td>
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
                                            <td><input type="text" name="harga_satuan[]" id="harga_satuan_{{ $i }}" oninput="multiply(this)" class="form-control" value="{{ $komponen->harga }}" disabled></td>
                                            <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" oninput="multiply(this)" class="form-control" value="{{ $komponen->jumlah }}" disabled></td>
                                            <td><input type="text" name="harga_total[]" id="harga_total_{{ $i }}" class="form-control" value="{{ $komponen->harga_jual }}" readonly></td>
                                            @if( in_array('getProdukTerjual', $thisUserPermissions))
                                            <td>
                                                @if ($komponen->produk->tipe_produk == 6)
                                                <button id="btnGift_{{ $i }}" data-produk="{{ $komponen->id }}" class="btn btn-info w-100">Set Gift</button>
                                                @endif
                                            </td>
                                            <td><button id="btnPerangkai_{{ $i }}" data-produk="{{ $komponen->id }}" class="btn btn-primary">Perangkai</button></td>
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
                                            <td id="pengaju">{{ $kontraks->data_sales->name }}</td>
                                            <td id="pembuat">{{ $kontraks->data_pembuat->name ?? '-' }}</td>
                                            <td id="penyetuju">{{ $kontraks->data_penyetuju->name ?? (Auth::user()->hasRole('Auditor') ? Auth::user()->name : '') }}</td>
                                            <td id="pemeriksa">{{ $kontraks->data_pemeriksa->name ?? (Auth::user()->hasRole('Finance') ? Auth::user()->name : '') }}</td>
                                        </tr>
                                        <tr>
                                            <td id="tgl_sales" class="col-md-3">
                                                <label id="tanggal_sales" name="tanggal_sales">{{ isset($kontraks->tanggal_sales) ? \Carbon\Carbon::parse($kontraks->tanggal_sales)->format('d-m-Y') : '' }}</label>
                                            </td>
                                            <td id="tgl_pembuat" class="col-md-3">
                                                <label id="tanggal_pembuat" name="tanggal_pembuat">{{ isset($kontraks->tanggal_pembuat) ? \Carbon\Carbon::parse($kontraks->tanggal_pembuat)->format('d-m-Y') : '-' }}</label>
                                            </td>
                                            <td id="tgl_penyetuju" class="col-md-3">
                                                @if(Auth::user()->hasRole('Auditor') && !$kontraks->tanggal_penyetuju)
                                                <input type="date" class="form-control" name="tanggal_penyetuju" id="tanggal_penyetuju" required value="{{ $kontraks->tanggal_penyetuju ? \Carbon\Carbon::parse($kontraks->tanggal_penyetuju)->format('Y-m-d') : date('Y-m-d') }}">
                                                @else
                                                <label id="tanggal_penyetuju" name="tanggal_penyetuju">{{ isset($kontraks->tanggal_penyetuju) ? \Carbon\Carbon::parse($kontraks->tanggal_penyetuju)->format('d-m-Y') : '-' }}</label>
                                                @endif
                                            </td>
                                            <td id="tgl_pemeriksa" class="col-md-3">
                                                 @if(Auth::user()->hasRole('Finance') && !$kontraks->tanggal_pemeriksa)
                                                 <input type="date" class="form-control" name="tanggal_pemeriksa" id="tanggal_pemeriksa" value="{{ date('Y-m-d') }}">
                                                 @else
                                                 <label id="tanggal_pemeriksa" name="tanggal_pemeriksa">{{ isset($kontraks->tanggal_pemeriksa) ? \Carbon\Carbon::parse($kontraks->tanggal_pemeriksa)->format('d-m-Y') : '-' }}</label>
                                                @endif
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
                                        <div class="input-group">
                                            <input type="text" id="promo_persen" name="promo_persen" value="{{ $kontraks->promo_persen ?? 0 }}" class="form-control" readonly aria-describedby="basic-addon3" oninput="validatePersen(this)">
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                        <input type="text" class="form-control" name="total_promo" id="total_promo" value="{{ $kontraks->total_promo }}" readonly>
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
                                        <input type="text" class="form-control" name="ongkir_nominal" id="ongkir_nominal" value="{{ $kontraks->ongkir_nominal }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Total Harga</label>
                                    <div class="col-lg-9">
                                        <input type="text" id="total_harga" name="total_harga" value="{{ $kontraks->total_harga }}" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <input type="hidden" name="konfirmasi" id="hiddenActionInput" value="">
                    @if((Auth::user()->hasRole('AdminGallery') && $kontraks->status == 'TUNDA') || (Auth::user()->hasRole('Auditor') && $kontraks->status == 'DIKONFIRMASI' && !$kontraks->tanggal_penyetuju) || (Auth::user()->hasRole('Finance') && $kontraks->status == 'DIKONFIRMASI' && !$kontraks->tanggal_pemeriksa))
                    <button class="btn btn-success confirm-btn" data-action="confirm" type="button">Konfirmasi</button>
                    @endif
                    @if(Auth::user()->hasRole('AdminGallery') && $kontraks->status == 'TUNDA')
                    <button class="btn btn-danger confirm-btn" data-action="cancel" type="button">Batal</button>
                    @endif
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
                            '<td><input type="text" name="harga_satuan[]" id="harga_satuan_'+i+'" oninput="multiply(this)" class="form-control"></td>'+
                            '<td><input type="number" name="jumlah[]" id="jumlah_'+i+'" oninput="multiply(this)" class="form-control"></td>'+
                            '<td><input type="text" name="harga_total[]" id="harga_total_'+i+'" class="form-control" readonly></td>'+
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
            let inputs = $('.card-body').find('[id^=harga_satuan], [id^=harga_total], #subtotal, #total_promo, #ppn_nominal, #pph_nominal, #ongkir_nominal, #total_harga');
            inputs.each(function() {
                let input = $(this);
                let value = input.val();
                let formattedValue = formatNumber(value);

                // Set the cleaned value back to the input
                input.val(formattedValue);
            });
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
        $('#form_perangkai').on('submit', function(e){
            e.preventDefault();

            var jml_perangkai = $('#jml_perangkai').val();
            var formValid = true;

            if(jml_perangkai == 0){
                toastr.error('Jumlah perangkai harus lebih dari 1', {
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: false,
                    progressBar: true
                });
                formValid = false;
            }

            $('[id^=perangkai_id_]').each(function(){
                if(!$(this).val()){
                    toastr.error('Perangkai tidak boleh ada yang kosong', {
                        closeButton: true,
                        tapToDismiss: false,
                        rtl: false,
                        progressBar: true
                    });
                    formValid = false;
                    return false;
                }
            });

            if (formValid) {
                this.submit();
            }
        });
        function multiply(element) {
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