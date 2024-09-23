@extends('layouts.app-von')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="dash-count" style="background-color: rgb(255, 159, 67)">
      <div class="row w-100">
        <div class="col-lg-6 col-md-6 col-sm-12 mb-2 mb-md-0">
          <h2>Kas Pusat</h2>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-end gap-2">
          <div class="d-flex flex-column flex-md-row w-100">
            <select class="form-select select2 w-100 w-md-auto" name="rekening" id="filterRekening">
              <option value="">Semua Rekening</option>
              @foreach($rekenings as $rekening)
              <option value="{{ $rekening->id }}" {{ $rekening->id == request()->input('rekening') ? 'selected' : '' }}>{{ $rekening->nama_akun ?? 'Cash' }}</option>
            @endforeach
            </select>
          </div>
          <div class="d-flex flex-column flex-md-row gap-2 w-100">
            <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('kas_pusat.index') }}" class="btn btn-info w-100">Filter</a>
            <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('kas_pusat.index') }}" class="btn btn-warning w-100">Clear</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-sm-12">
    <div class="row">
      <div class="col-lg-4 col-md-4 col-sm-12 mb-lg-4 mb-md-0 mb-sm-0">
        <div class="card rounded-3 overflow-hidden">
            <div class="row g-0">
                <div class="col-4 d-flex align-items-center justify-content-center" style="background-color: #e0f7e9;">
                    <img src="assets/img/icons/dash2.svg" alt="img" style="width: 48px; height: 48px;">
                </div>
                <div class="col-8" style="background-color: #28a745; color: white;">
                    <div class="card-body">
                        <h3 class="mb-0">Rp. <span class="counters-rupiah" data-count="{{ $saldoMasuk }}"></span></h3>
                        <p class="mb-0">Kas Masuk</p>
                        <hr class="my-2">
                        <div class="small">
                            <p class="mb-1">Rekening: Rp. <span class="counters-rupiah" data-count="{{ $saldoMasukRekening }}"></span></p>
                            <p class="mb-0">Cash: Rp. <span class="counters-rupiah" data-count="{{ $saldoMasukCash }}"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-12 mb-lg-4 mb-md-0 mb-sm-0">
        <div class="card rounded-3 overflow-hidden">
            <div class="row g-0">
                <div class="col-4 d-flex align-items-center justify-content-center" style="background-color: #ffe6e6;">
                  <img src="assets/img/money-send-svgrepo-com.svg" alt="img" style="width: 48px; height: 48px;">
                </div>
                <div class="col-8" style="background-color: #ff006a; color: white;">
                    <div class="card-body">
                        <h3 class="mb-0">Rp. <span class="counters-rupiah" data-count="{{ $saldoKeluar }}"></span></h3>
                        <p class="mb-0">Kas Keluar</p>
                        <hr class="my-2">
                        <div class="small">
                            <p class="mb-1">Rekening: Rp. <span class="counters-rupiah" data-count="{{ $saldoKeluarRekening }}"></span></p>
                            <p class="mb-0">Cash: Rp. <span class="counters-rupiah" data-count="{{ $saldoKeluarCash }}"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-12 mb-lg-4 mb-md-0 mb-sm-0">
        <div class="card rounded-3 overflow-hidden">
            <div class="row g-0">
                <div class="col-4 d-flex align-items-center justify-content-center" style="background-color: #e6f0ff;">
                  <img src="assets/img/balance-sheet.png" alt="img" style="width: 48px; height: 48px;">
                </div>
                <div class="col-8" style="background-color: #0131c3; color: white;">
                    <div class="card-body">
                        <h3 class="mb-0">Rp. <span class="counters-rupiah" data-count="{{ $saldo }}"></span></h3>
                        <p class="mb-0">Saldo </p>
                        <hr class="my-2">
                        <div class="small">
                            <p class="mb-1">Rekening: Rp. <span class="counters-rupiah" data-count="{{ $saldoRekening }}"></span></p>
                            <p class="mb-0">Cash: Rp. <span class="counters-rupiah" data-count="{{ $saldoCash }}"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-12">
      <div class="card">
      <div class="card-header">
          <div class="page-header">
              <div class="page-title">
                  <h4>Kas Masuk</h4>
              </div>
              <div class="page-btn">
                @if(in_array('kas_pusat.create', $thisUserPermissions) && in_array('kas_pusat.store', $thisUserPermissions))
                  <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addkasmasuk" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Kas Masuk</a>
                @endif
              </div>
          </div>
      </div>
      <div class="card-body">
          <div class="table-responsive">
          <table class="table" id="masuk" style="width: 100%">
              <thead>
              <tr>
                  <th>No</th>
                  <th>Metode</th>
                  <th>Pengirim</th>
                  <th>Rekening Pengirim</th>
                  <th>Penerima</th>
                  <th>Rekening Penerima</th>
                  <th>Tanggal</th>
                  <th>Nominal</th>
                  <th>Biaya Lainnya</th>
                  <th>Keterangan</th>
                  <th>Status</th>
                  <th>Aksi</th>
              </tr>
              </thead>
              <tbody>
                  {{-- @foreach ($dataMasuk as $item)
                      <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $item->metode ?? '-' }}</td>
                          <td>{{ $item->lok_pengirim->nama ?? '-' }}</td>
                          <td>{{ $item->rek_pengirim->nama_akun ?? '-' }}</td>
                          <td>{{ $item->lok_penerima->nama ?? '-' }}</td>
                          <td>{{ $item->rek_penerima->nama_akun ?? '-' }}</td>
                          <td>{{ $item->tanggal ? formatTanggal($item->tanggal) : '-' }}</td>
                          <td>{{ $item->nominal ? formatRupiah($item->nominal) : '-' }}</td>
                          <td>{{ $item->biaya_lain ? formatRupiah($item->biaya_lain) : '-' }}</td>
                          <td>{{ $item->keterangan ?? '-' }}</td>
                          <td>
                            <span class="badges {{ $item->status == 'DIKONFIRMASI' ? 'bg-lightgreen' : 'bg-lightgrey' }}">{{ $item->status ?? '-' }}</span>
                          </td>
                          <td class="text-center">
                            <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu">
                              @if(in_array('kas_pusat.edit', $thisUserPermissions) && in_array('kas_pusat.update', $thisUserPermissions))
                                <li>
                                    <a href="javascript:void(0);" onclick="edit_masuk({{ $item->id }})" data-bs-toggle="modal" data-bs-target="#editkas" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                </li>
                              @endif
                                <li>
                                    <a href="javascript:void(0);" onclick="bukti('{{ $item->file }}')" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Bukti</a>
                                </li>
                            </ul>
                          </td>
                      </tr>
                  @endforeach --}}
              </tbody>
          </table>
          </div>
      </div>
      </div>
  </div>
  <div class="col-sm-12">
      <div class="card">
      <div class="card-header">
          <div class="page-header">
              <div class="page-title">
                  <h4>Kas Keluar</h4>
              </div>
              <div class="page-btn">
                @if(in_array('kas_pusat.create', $thisUserPermissions) && in_array('kas_pusat.store', $thisUserPermissions))
                  <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addkaskeluar" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Kas Keluar</a>
                @endif
              </div>
          </div>
      </div>
      <div class="card-body">
          <div class="table-responsive">
          <table class="table" id="keluar" style="width: 100%">
              <thead>
              <tr>
                  <th>No</th>
                  <th>Metode</th>
                  <th>Jenis</th>
                  <th>Rekening</th>
                  <th>Tanggal</th>
                  <th>Nominal</th>
                  <th>Biaya Lainnya</th>
                  <th>Keterangan</th>
                  <th>Status</th>
                  <th>Aksi</th>
              </tr>
              </thead>
              <tbody>
                  {{-- @foreach ($dataKeluar as $item)
                      <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $item->metode ?? '-' }}</td>
                          <td>{{ $item->jenis ?? '-' }}</td>
                          <td>{{ $item->metode ?? '-' }}</td>
                          <td>{{ $item->rek_pengirim->nama_akun ?? '-' }}</td>
                          <td>{{ $item->tanggal ? formatTanggal($item->tanggal) : '-' }}</td>
                          <td>{{ $item->nominal ? formatRupiah($item->nominal) : '-' }}</td>
                          <td>{{ $item->biaya_lain ? formatRupiah($item->biaya_lain) : '-' }}</td>
                          <td>{{ $item->keterangan ?? '-' }}</td>
                          <td>
                            <span class="badges {{ $item->status == 'DIKONFIRMASI' ? 'bg-lightgreen' : 'bg-lightgrey' }}">{{ $item->status ?? '-' }}</span>
                          </td>
                          <td class="text-center">
                            <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu">
                              @if(in_array('kas_pusat.edit', $thisUserPermissions) && in_array('kas_pusat.update', $thisUserPermissions))
                                <li>
                                  <a href="javascript:void(0);" onclick="edit_keluar({{ $item->id }})" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                </li>
                              @endif
                              <li>
                                  <a href="javascript:void(0);" onclick="bukti('{{ $item->file }}')" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Bukti</a>
                              </li>
                            </ul>
                          </td>
                      </tr>
                  @endforeach --}}
              </tbody>
          </table>
          </div>
      </div>
      </div>
  </div>
</div>

{{-- modal start --}}
<div class="modal fade" id="addkasmasuk" tabindex="-1" aria-labelledby="addkasmasuklabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addkasmasuklabel">Tambah Transaksi Masuk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="addFormMasuk" action="{{ route('kas_pusat.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="jenis" value="Pemindahan Saldo">
            <div class="row">
              <div class="col-6">
                <div class="row">
                  <div class="col-12 mb-2">
                    <label for="metode" class="col-form-label">Metode</label>
                    <select class="select2" name="metode" id="masuk_metode" required>
                      <option value="Transfer">Transfer</option>
                      <option value="Cash">Cash</option>
                    </select>
                  </div>
                  <div class="col-6">
                    <label for="lokasi_pengirim" class="col-form-label">Lokasi Pengirim</label>
                    <div class="form-group mb-0">
                      <select class="select2" name="lokasi_pengirim" id="masuk_lokasi_pengirim" onchange="getRekening(this, 'masuk_rekening_pengirim')" required>
                        <option value="">Lokasi Pengirim</option>
                        @foreach($lokasis as $lokasi)
                          <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-6">
                    <label for="lokasi_penerima" class="col-form-label">Lokasi Penerima</label>
                    <div class="form-group mb-0">
                      <select class="select2" name="lokasi_penerima" id="masuk_lokasi_penerima" required>
                        <option value="">Lokasi Penerima</option>
                        @foreach($lokasis as $lokasi)
                          @if($lokasi->operasional_id == 1)
                            <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                          @endif
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-6" id="div_rekening_pengirim_masuk">
                    <label for="rekening_pengirim" class="col-form-label">Rekening Pengirim</label>
                    <div class="form-group mb-0">
                      <select class="select2" name="rekening_pengirim" id="masuk_rekening_pengirim" disabled>
                        <option value="">Rekening Pengirim</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-6" id="div_rekening_penerima_masuk">
                    <label for="rekening_penerima" class="col-form-label">Rekening Penerima</label>
                    <div class="form-group mb-0">
                      <select class="select2" name="rekening_penerima" id="masuk_rekening_penerima" required>
                        <option value="">Rekening Penerima</option>
                        @foreach($rekenings as $rekening)
                          @if($rekening->lokasi->operasional_id == 1)
                            <option value="{{ $rekening->id }}">{{ $rekening->nama_akun }}</option>
                          @endif
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-12">
                    <label for="akun_id" class="col-form-label">Akun</label>
                    <div class="form-group mb-0">
                      <select class="select2" name="akun_id" id="masuk_akun_id" required>
                        <option value="">Pilih Akun</option>
                        @foreach($akuns as $akun)
                          <option value="{{ $akun->id }}">{{ $akun->no_akun }} - {{ $akun->nama_akun }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-12">
                    <label for="tanggal" class="col-form-label">Tanggal</label>
                    <input type="date" class="form-control" name="tanggal" id="masuk_tanggal" value="{{ date('Y-m-d') }}" required>
                  </div>
                  <div class="col-12">
                    <label for="nominal" class="col-form-label">Nominal</label>
                    <input type="text" class="form-control" name="nominal" id="masuk_nominal" required>
                  </div>
                  <div class="col-sm-12 col-md-12 col-lg-12 mb-2">
                    <label for="biaya_lain" class="col-form-label">Biaya Lain</label>
                    <input type="text" class="form-control" name="biaya_lain" id="masuk_biaya_lain">
                  </div>
                  <div class="col-12">
                    <label for="keterangan" class="col-form-label">Keterangan</label>
                    <input type="text" class="form-control" name="keterangan" id="masuk_keterangan" required>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="mb-3">
                  <label for="file" class="col-form-label">File</label>
                  <input type="file" class="form-control" name="file" id="masuk_file" accept="image/*" required onchange="previewImage(this, 'masuk_preview')">
                  <img class="mt-2" src="" alt="" id="masuk_preview" style="width: 100%;height:auto;">
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
<div class="modal fade" id="addkaskeluar" tabindex="-1" aria-labelledby="addkaskeluarlabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addkaskeluarlabel">Tambah Transaksi Keluar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="addFormKeluar" action="{{ route('kas_pusat.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
              <div class="col-6">
                <div class="row">
                  <div class="col-12">
                    <label for="jenis" class="col-form-label">Jenis</label>
                    <select class="select2" name="jenis" id="keluar_jenis" required>
                      <option value="Pemindahan Saldo">Pemindahan Saldo</option>
                      <option value="Lainnya">Lainnya</option>
                    </select>
                  </div>
                </div>
                <div class="col-12 mb-2">
                  <label for="metode" class="col-form-label">Metode</label>
                  <select class="select2" name="metode" id="keluar_metode" required>
                    <option value="Transfer">Transfer</option>
                    <option value="Cash">Cash</option>
                  </select>
                </div>
                <div class="row pemindahan_saldo">
                  <div class="col-6">
                    <label for="lokasi_pengirim" class="col-form-label">Lokasi Pengirim</label>
                    <select class="select2" name="lokasi_pengirim" id="keluar_lokasi_pengirim" onchange="getRekening(this, 'keluar_rekening_pengirim')" required>
                      <option value="">Lokasi Pengirim</option>
                      @foreach($lokasis as $lokasi)
                        @if($lokasi->operasional_id == 1)
                          <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>
                  <div class="col-6">
                    <label for="lokasi_penerima" class="col-form-label">Lokasi Penerima</label>
                    <select class="select2" name="lokasi_penerima" id="keluar_lokasi_penerima" onchange="getRekening(this, 'keluar_rekening_penerima')" required>
                      <option value="">Lokasi Penerima</option>
                      @foreach($lokasis as $lokasi)
                        <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="row pemindahan_saldo">
                  <div class="col-6" id="div_rekening_pengirim_keluar">
                    <label for="rekening_pengirim" class="col-form-label">Rekening Pengirim</label>
                    <select class="select2" name="rekening_pengirim" id="keluar_rekening_pengirim" disabled>
                      <option value="">Rekening Pengirim</option>
                    </select>
                  </div>
                  <div class="col-6" id="div_rekening_penerima_keluar">
                    <label for="rekening_penerima" class="col-form-label">Rekening Penerima</label>
                    <select class="select2" name="rekening_penerima" id="keluar_rekening_penerima" disabled>
                      <option value="">Rekening Penerima</option>
                    </select>
                  </div>
                </div>
                <div class="row lainnya d-none">
                  <div class="col-6" id="div_lokasi_keluar">
                    <label for="lokasi_pengirim" class="col-form-label">Lokasi</label>
                    <select class="select2" name="lokasi_pengirim" id="keluar_lokasi" disabled>
                      <option value="">Lokasi</option>
                      @foreach($lokasis as $lokasi)
                          @if($lokasi->operasional_id == 1)
                            <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                          @endif
                        @endforeach
                    </select>
                  </div>
                  <div class="col-6" id="div_rekening_keluar">
                    <label for="rekening_pengirim" class="col-form-label">Rekening</label>
                    <select class="select2" name="rekening_pengirim" id="keluar_rekening" disabled>
                      <option value="">Rekening</option>
                      @foreach($rekenings as $rekening)
                          @if($rekening->lokasi->operasional_id == 1)
                            <option value="{{ $rekening->id }}">{{ $rekening->nama_akun }}</option>
                          @endif
                        @endforeach
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <label for="akun_id" class="col-form-label">Akun</label>
                    <div class="form-group mb-0">
                      <select class="select2" name="akun_id" id="keluar_akun_id" required>
                        <option value="">Pilih Akun</option>
                        @foreach($akuns as $akun)
                          <option value="{{ $akun->id }}">{{ $akun->no_akun }} - {{ $akun->nama_akun }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-12">
                    <label for="tanggal" class="col-form-label">Tanggal</label>
                    <input type="date" class="form-control" name="tanggal" id="keluar_tanggal" value="{{ date('Y-m-d') }}" required>
                  </div>
                  <div class="col-12">
                    <label for="nominal" class="col-form-label">Nominal</label>
                    <input type="text" class="form-control" name="nominal" id="keluar_nominal" required>
                  </div>
                  <div class="col-sm-12 col-md-12 col-lg-12 mb-2">
                    <label for="biaya_lain" class="col-form-label">Biaya Lain</label>
                    <input type="text" class="form-control" name="biaya_lain" id="keluar_biaya_lain">
                  </div>
                  <div class="col-12">
                    <label for="keterangan" class="col-form-label">Keterangan</label>
                    <input type="text" class="form-control" name="keterangan" id="keluar_keterangan" required>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="row">
                  <div class="col-12">
                    <label for="file" class="col-form-label">File</label>
                    <input type="file" class="form-control" name="file" id="keluar_file" accept="image/*" required onchange="previewImage(this, 'keluar_preview')">
                    <img class="mt-2" src="" alt="" id="keluar_preview" style="width: 100%;height:auto;">
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
<div class="modal fade" id="editkasmasuk" tabindex="-1" aria-labelledby="editkasmasuklabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editkasmasuklabel">Edit Transaksi Masuk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="editFormMasuk" action="" method="POST" enctype="multipart/form-data">
            @csrf
            @method('patch')
            <input type="hidden" name="jenis" value="Pemindahan Saldo">
            <div class="row">
              <div class="col-6">
                <div class="row">
                  <div class="col-12">
                    <label for="status" class="col-form-label">Status</label>
                    <select class="select2" name="status" id="edit_masuk_status" required>
                      <option value="BATAL">BATAL</option>
                      <option value="DIKONFIRMASI">DIKONFIRMASI</option>
                    </select>
                  </div>
                  <div class="col-12 mb-2">
                    <label for="metode" class="col-form-label">Metode</label>
                    <select class="select2" name="metode" id="edit_masuk_metode" required>
                      <option value="Transfer">Transfer</option>
                      <option value="Cash">Cash</option>
                    </select>
                  </div>
                  <div class="col-6 mb-0">
                    <label for="lokasi_pengirim" class="col-form-label">Lokasi Pengirim</label>
                    <div class="form-group mb-0">
                      <select class="select2" name="lokasi_pengirim" id="edit_masuk_lokasi_pengirim" onchange="getRekening(this, 'edit_masuk_rekening_pengirim')" required>
                        <option value="">Lokasi Pengirim</option>
                        @foreach($lokasis as $lokasi)
                          <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-6 mb-0">
                    <label for="lokasi_penerima" class="col-form-label">Lokasi Penerima</label>
                    <div class="form-group mb-0">
                      <select class="select2" name="lokasi_penerima" id="edit_masuk_lokasi_penerima" required>
                        <option value="">Lokasi Penerima</option>
                        @foreach($lokasis as $lokasi)
                          @if($lokasi->operasional_id == 1)
                            <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                          @endif
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row mb-0">
                  <div class="col-6 mb-0" id="edit_div_rekening_pengirim_masuk">
                    <label for="rekening_pengirim" class="col-form-label">Rekening Pengirim</label>
                    <div class="form-group mb-0">
                      <select class="select2" name="rekening_pengirim" id="edit_masuk_rekening_pengirim" disabled>
                        <option value="">Rekening Pengirim</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-6 mb-0" id="edit_div_rekening_penerima_masuk">
                    <label for="rekening_penerima" class="col-form-label">Rekening Penerima</label>
                    <div class="form-group mb-0">
                      <select class="select2" name="rekening_penerima" id="edit_masuk_rekening_penerima" required>
                        <option value="">Rekening Penerima</option>
                        @foreach($rekenings as $rekening)
                          @if($rekening->lokasi->operasional_id == 1)
                            <option value="{{ $rekening->id }}">{{ $rekening->nama_akun }}</option>
                          @endif
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <label for="akun_id" class="col-form-label">Akun</label>
                    <div class="form-group mb-0">
                      <select class="select2" name="akun_id" id="edit_masuk_akun_id" required>
                        <option value="">Pilih Akun</option>
                        @foreach($akuns as $akun)
                          <option value="{{ $akun->id }}">{{ $akun->no_akun }} - {{ $akun->nama_akun }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-12">
                    <label for="tanggal" class="col-form-label">Tanggal</label>
                    <input type="date" class="form-control" name="tanggal" id="edit_masuk_tanggal" value="{{ date('Y-m-d') }}" required>
                  </div>
                  <div class="col-12">
                    <label for="nominal" class="col-form-label">Nominal</label>
                    <input type="text" class="form-control" name="nominal" id="edit_masuk_nominal" required>
                  </div>
                  <div class="col-12">
                    <label for="biaya_lain" class="col-form-label">Biaya Lain</label>
                    <input type="text" class="form-control" name="biaya_lain" id="edit_masuk_biaya_lain">
                  </div>
                  <div class="col-12">
                    <label for="keterangan" class="col-form-label">Keterangan</label>
                    <input type="text" class="form-control" name="keterangan" id="edit_masuk_keterangan" required>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="row">
                  <div class="col-12">
                    <label for="file" class="col-form-label">File</label>
                    <input type="file" class="form-control" name="file" id="edit_masuk_file" accept="image/*" onchange="previewImage(this, 'edit_masuk_preview')">
                    <img class="mt-2" src="" alt="" id="edit_masuk_preview" style="width: 100%;height:auto;">
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
<div class="modal fade" id="editkaskeluar" tabindex="-1" aria-labelledby="editkaskeluarlabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editkaskeluarlabel">Edit Transaksi Keluar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="editFormKeluar" action="{{ route('kas_pusat.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('patch')
            <div class="row">
              <div class="col-6">
                <div class="row">
                  <div class="col-12">
                    <label for="status" class="col-form-label">Status</label>
                    <select class="select2" name="status" id="edit_keluar_status" required>
                      <option value="BATAL">BATAL</option>
                      <option value="DIKONFIRMASI">DIKONFIRMASI</option>
                    </select>
                  </div>
                  <div class="col-12 mb-2">
                    <label for="metode" class="col-form-label">Metode</label>
                    <select class="select2" name="metode" id="edit_keluar_metode" required>
                      <option value="Transfer">Transfer</option>
                      <option value="Cash">Cash</option>
                    </select>
                  </div>
                  <div class="col-12">
                    <label for="jenis" class="col-form-label">Jenis</label>
                    <div class="form-group mb-0">
                      <select class="select2" name="jenis" id="edit_keluar_jenis" required>
                        <option value="Pemindahan Saldo">Pemindahan Saldo</option>
                        <option value="Lainnya">Lainnya</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row pemindahan_saldo">
                  <div class="col-6">
                    <label for="lokasi_pengirim" class="col-form-label">Lokasi Pengirim</label>
                    <div class="form-group mb-0">
                      <select class="select2" name="lokasi_pengirim" id="edit_keluar_lokasi_pengirim" onchange="getRekening(this, 'edit_keluar_rekening_pengirim')" required>
                        <option value="">Lokasi Pengirim</option>
                        @foreach($lokasis as $lokasi)
                          @if($lokasi->operasional_id == 1)
                            <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                          @endif
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-6">
                    <label for="lokasi_penerima" class="col-form-label">Lokasi Penerima</label>
                    <div class="form-group mb-0">
                      <select class="select2" name="lokasi_penerima" id="edit_keluar_lokasi_penerima" onchange="getRekening(this, 'edit_keluar_rekening_penerima')" required>
                        <option value="">Lokasi Penerima</option>
                        @foreach($lokasis as $lokasi)
                          <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row pemindahan_saldo">
                  <div class="col-6" id="edit_div_rekening_pengirim_keluar">
                    <label for="rekening_pengirim" class="col-form-label">Rekening Pengirim</label>
                    <div class="form-group mb-0">
                      <select class="select2" name="rekening_pengirim" id="edit_keluar_rekening_pengirim" disabled>
                        <option value="">Rekening Pengirim</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-6" id="edit_div_rekening_penerima_keluar">
                    <label for="rekening_penerima" class="col-form-label">Rekening Penerima</label>
                    <div class="form-group mb-0">
                      <select class="select2" name="rekening_penerima" id="edit_keluar_rekening_penerima" disabled>
                        <option value="">Rekening Penerima</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row lainnya d-none">
                  <div class="col-6" id="edit_div_lokasi_keluar">
                    <label for="lokasi_pengirim" class="col-form-label">Lokasi</label>
                    <select class="select2" name="lokasi_pengirim" id="edit_keluar_lokasi" disabled>
                      <option value="">Lokasi</option>
                      @foreach($lokasis as $lokasi)
                          @if($lokasi->operasional_id == 1)
                            <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                          @endif
                        @endforeach
                    </select>
                  </div>
                  <div class="col-6" id="edit_div_rekening_keluar">
                    <label for="rekening_pengirim" class="col-form-label">Rekening</label>
                    <select class="select2" name="rekening_pengirim" id="edit_keluar_rekening" disabled>
                      <option value="">Rekening</option>
                      @foreach($rekenings as $rekening)
                          @if($rekening->lokasi->operasional_id == 1)
                            <option value="{{ $rekening->id }}">{{ $rekening->nama_akun }}</option>
                          @endif
                        @endforeach
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <label for="akun_id" class="col-form-label">Akun</label>
                    <div class="form-group mb-0">
                      <select class="select2" name="akun_id" id="edit_keluar_akun_id" required>
                        <option value="">Pilih Akun</option>
                        @foreach($akuns as $akun)
                          <option value="{{ $akun->id }}">{{ $akun->no_akun }} - {{ $akun->nama_akun }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-12">
                    <label for="tanggal" class="col-form-label">Tanggal</label>
                    <input type="date" class="form-control" name="tanggal" id="edit_keluar_tanggal" value="{{ date('Y-m-d') }}" required>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <label for="nominal" class="col-form-label">Nominal</label>
                    <input type="text" class="form-control" name="nominal" id="edit_keluar_nominal" required>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <label for="biaya_lain" class="col-form-label">Biaya Lain</label>
                    <input type="text" class="form-control" name="biaya_lain" id="edit_keluar_biaya_lain">
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <label for="keterangan" class="col-form-label">Keterangan</label>
                    <input type="text" class="form-control" name="keterangan" id="edit_keluar_keterangan" required>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="row">
                  <div class="col-12">
                    <label for="file" class="col-form-label">File</label>
                    <input type="file" class="form-control" name="file" id="edit_keluar_file" accept="image/*" onchange="previewImage(this, 'edit_keluar_preview')">
                    <img class="mt-2" src="" alt="" id="edit_keluar_preview" style="width: 100%;height:auto;">
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
<div class="modal fade" id="modalBukti" tabindex="-1" aria-labelledby="editAkunlabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addAkunlabel">Bukti</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
      </div>
      <div class="modal-body">
          <img id="imgBukti" src="" alt="" style="max-width: 100%;height: auto;">
      </div>
      <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modalLog" tabindex="-1" aria-labelledby="modalLogLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLogLabel">Log Kas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table id="logTable" class="table" style="width: 100%">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Pengubah</th>
                <th>Deskripsi</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

{{-- modal end --}}
@endsection

@section('scripts')
    <script>
      var rekening_pengirim;
    $(document).ready(function() {
        // getRekening($('#filterLokasi'), 'filterRekening')
        $('[id^=masuk_rekening_], [id^=keluar_rekening_], [id^=masuk_lokasi_], [id^=keluar_lokasi_], [id^=filterRekening], [id^=filterLokasi], #keluar_jenis, #keluar_rekening, #keluar_lokasi, [id^=edit_masuk_rekening_], [id^=edit_keluar_rekening_], [id^=edit_masuk_lokasi_], [id^=edit_keluar_lokasi_], [id^=filterRekening], [id^=filterLokasi], #edit_keluar_jenis, #edit_keluar_rekening, #edit_keluar_lokasi, #edit_keluar_status, #edit_masuk_status, #masuk_metode, #keluar_metode, #edit_masuk_metode, #edit_keluar_metode, #masuk_akun_id, #keluar_akun_id, #edit_masuk_akun_id, #edit_keluar_akun_id').select2()

        // Start Datatable Masuk
          const columns1 = [
              { data: 'no', name: 'no', orderable: false },
              { 
                  data: 'metode',
                  name: 'metode',
                  render: function(data, type, row) {
                      let badgeClass;
                      switch (data) {
                          case 'Cash':
                              badgeClass = 'bg-lightgreen';
                              break;
                          case 'Transfer':
                              badgeClass = 'bg-lightblue';
                              break;
                          default:
                              badgeClass = 'bg-lightgrey';
                              break;
                      }
                      
                      return `<div class="text-center">
                          <span class="badges ${badgeClass}">
                              ${data ?? '-'}
                          </span></div>
                      `;
                  }
              },
              { data: 'lok_pengirim.nama', name: 'lok_pengirim.nama' },
              { data: 'nama_rek_pengirim', name: 'nama_rek_pengirim', orderable: false, defaultContent: '' },
              { data: 'lok_penerima.nama', name: 'lok_penerima.nama', orderable: false },
              { data: 'nama_rek_penerima', name: 'nama_rek_penerima', orderable: false, defaultContent: '' },
              { data: 'tanggal', name: 'tanggal' },
              { data: 'nominal', name: 'nominal' },
              { data: 'biaya_lain', name: 'biaya_lain' },
              { data: 'keterangan', name: 'keterangan' },
              { 
                  data: 'status',
                  name: 'status',
                  render: function(data, type, row) {
                      let badgeClass;
                      switch (data) {
                          case 'DIKONFIRMASI':
                              badgeClass = 'bg-lightgreen';
                              break;
                          case 'TUNDA':
                              badgeClass = 'bg-lightred';
                              break;
                          default:
                              badgeClass = 'bg-lightgrey';
                              break;
                      }
                      
                      return `
                          <span class="badges ${badgeClass}">
                              ${data ?? '-'}
                          </span>
                      `;
                  }
              },
              {
                  data: 'action',
                  name: 'action',
                  orderable: false,
                  searchable: false,
                  render: function(data, type, row) {
                      let actionsHtml = `
                          <div class="text-center">
                              <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                  <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                              </a>
                              <ul class="dropdown-menu">
                      `;

                      // Cek izin untuk edit dan update
                      if (userPermissions.includes('kas_pusat.edit') && userPermissions.includes('kas_pusat.update')) {
                          actionsHtml += `
                              <li>
                                  <a href="javascript:void(0);" onclick="edit_masuk(${row.id})" data-bs-toggle="modal" data-bs-target="#editkas" class="dropdown-item">
                                      <img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit
                                  </a>
                              </li>
                          `;
                      }

                      // Cek izin untuk lihat log
                      if (userPermissions.includes('kas.log')) {
                          actionsHtml += `
                              <li>
                                  <a href="javascript:void(0);" onclick="log(${row.id})" class="dropdown-item">
                                      <img src="assets/img/icons/purchase1.svg" class="me-2" alt="img">Log
                                  </a>
                              </li>
                          `;
                      }

                      // Opsi untuk menampilkan bukti
                      actionsHtml += `
                          <li>
                              <a href="javascript:void(0);" onclick="bukti('${row.file}')" class="dropdown-item">
                                  <img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Bukti
                              </a>
                          </li>
                      `;

                      actionsHtml += `
                              </ul>
                          </div>
                      `;

                      return actionsHtml;
                  }
              }
          ];

          let table1 = initDataTable('#masuk', {
              ajaxUrl: "{{ route('kas_pusat.index') }}",
              columns: columns1,
              order: [[1, 'asc']],
              searching: true,
              lengthChange: true,
              pageLength: 5
          }, {
            rekening: '#filterRekening',
          }, 'masuk'); 
        // End Datatable Masuk

        // Start Datatable Keluar
          const columns2 = [
              { data: 'no', name: 'no', orderable: false },
              { 
                  data: 'metode',
                  name: 'metode',
                  render: function(data, type, row) {
                      let badgeClass;
                      switch (data) {
                          case 'Cash':
                              badgeClass = 'bg-lightgreen';
                              break;
                          case 'Transfer':
                              badgeClass = 'bg-lightblue';
                              break;
                          default:
                              badgeClass = 'bg-lightgrey';
                              break;
                      }
                      
                      return `<div class="text-center">
                          <span class="badges ${badgeClass}">
                              ${data ?? '-'}
                          </span></div>
                      `;
                  }
              },
              { data: 'jenis', name: 'jenis' },
              { data: 'nama_rek_pengirim', name: 'nama_rek_pengirim', orderable: false },
              { data: 'tanggal', name: 'tanggal' },
              { data: 'nominal', name: 'nominal' },
              { data: 'biaya_lain', name: 'biaya_lain' },
              { data: 'keterangan', name: 'keterangan' },
              { 
                  data: 'status',
                  name: 'status',
                  render: function(data, type, row) {
                      let badgeClass;
                      switch (data) {
                          case 'DIKONFIRMASI':
                              badgeClass = 'bg-lightgreen';
                              break;
                          case 'TUNDA':
                              badgeClass = 'bg-lightred';
                              break;
                          default:
                              badgeClass = 'bg-lightgrey';
                              break;
                      }
                      
                      return `
                          <span class="badges ${badgeClass}">
                              ${data ?? '-'}
                          </span>
                      `;
                  }
              },
              {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    let actionsHtml = `
                        <div class="text-center">
                            <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu">
                    `;

                    if (userPermissions.includes('kas_pusat.edit') && userPermissions.includes('kas_pusat.update')) {
                        actionsHtml += `
                            <li>
                                <a href="javascript:void(0);" onclick="edit_keluar(${row.id})" class="dropdown-item">
                                    <img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit
                                </a>
                            </li>
                        `;
                    }

                    // Cek izin untuk lihat log
                    if (userPermissions.includes('kas.log')) {
                        actionsHtml += `
                            <li>
                                <a href="javascript:void(0);" onclick="log(${row.id})" class="dropdown-item">
                                    <img src="assets/img/icons/purchase1.svg" class="me-2" alt="img">Log
                                </a>
                            </li>
                        `;
                    }

                    actionsHtml += `
                        <li>
                            <a href="javascript:void(0);" onclick="bukti('${row.file}')" class="dropdown-item">
                                <img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Bukti
                            </a>
                        </li>
                    `;

                    actionsHtml += `
                            </ul>
                        </div>
                    `;

                    return actionsHtml;
                }
              }
          ];

          let table2 = initDataTable('#keluar', {
              ajaxUrl: "{{ route('kas_pusat.index') }}",
              columns: columns2,
              order: [[1, 'asc']],
              searching: true,
              lengthChange: true,
              pageLength: 5
          }, {
            rekening: '#filterRekening',
          }, 'keluar'); 
        // End Datatable Keluar
    });
    function bukti(src){
        var baseUrl = window.location.origin;
        var fullUrl = baseUrl + '/storage/' + src;
        $('#imgBukti').attr('src', fullUrl);
        $('#modalBukti').modal('show');
    }
    $(document).on('input', '#keluar_nominal, #keluar_biaya_lain, #masuk_nominal, #masuk_biaya_lain, #edit_keluar_nominal, #edit_keluar_biaya_lain, #edit_masuk_nominal, #edit_masuk_biaya_lain', function() {
        let input = $(this);
        let value = input.val();
        if (!isNumeric(cleanNumber(value))) {
            value = value.replace(/[^\d]/g, "");
        }
        value = cleanNumber(value);
        let formattedValue = formatNumber(value);
        
        input.val(formattedValue);
    });
    $('#addFormMasuk, #addFormKeluar, #editFormMasuk, #editFormKeluar').on('submit', function(e) {
        let inputs = $('#addFormMasuk, #addFormKeluar, #editFormMasuk, #editFormKeluar').find('#keluar_nominal, #keluar_biaya_lain, #masuk_nominal, #masuk_biaya_lain, #edit_keluar_nominal, #edit_keluar_biaya_lain, #edit_masuk_nominal, #edit_masuk_biaya_lain');
        inputs.each(function() {
            let input = $(this);
            let value = input.val();
            let cleanedValue = cleanNumber(value);

            input.val(cleanedValue);
        });

        return true;
    });
    $(document).on('change', '#edit_keluar_jenis, #keluar_jenis', function(){
        var jenis = $(this).val();
        if ($(this).is('#edit_keluar_jenis')){
          var metode = $('#edit_keluar_metode').val();
        } else {
          var metode = $('#keluar_metode').val();
        }
        if(jenis != 'Pemindahan Saldo') {
          $('div.pemindahan_saldo').addClass('d-none');
          $('div.pemindahan_saldo select').each(function(){
              $(this).prop('disabled', true).prop('required', false);
          });
          $('div.lainnya').removeClass('d-none');
          $('#keluar_lokasi').prop('disabled', false).prop('required', true);
          $('#edit_keluar_lokasi').prop('disabled', false).prop('required', true);
          if(metode == 'Cash'){
              $('#keluar_rekening').prop('disabled', false).prop('required', true);
              $('#edit_keluar_rekening').prop('disabled', false).prop('required', true);
          } else {
              $('#keluar_rekening').prop('disabled', false).prop('required', true);
              $('#edit_keluar_rekening').prop('disabled', false).prop('required', true);
          }
        } else {
          $('div.lainnya').addClass('d-none');
          $('div.lainnya select').each(function(){
              $(this).attr('disabled', true).prop('required', false);
          });
          
          $('div.pemindahan_saldo').removeClass('d-none');
          $('div.pemindahan_saldo select').each(function(){
              $(this).attr('disabled', false).prop('required', true);
          });
          if(metode == 'Cash'){
            console.log('cash')
              $('#edit_keluar_rekening_penerima').prop('disabled', true).prop('required', false);
              $('#edit_keluar_rekening_pengirim').prop('disabled', true).prop('required', false);
              $('keluar_rekening_penerima').prop('disabled', true).prop('required', false);
              $('keluar_rekening_pengirim').prop('disabled', true).prop('required', false);
          } else {
              $('#edit_keluar_rekening_penerima').prop('disabled', false).prop('required', true);
              $('#edit_keluar_rekening_pengirim').prop('disabled', false).prop('required', true);
              $('keluar_rekening_penerima').prop('disabled', false).prop('required', true);
              $('keluar_rekening_pengirim').prop('disabled', false).prop('required', true);
          }
        }
    });
    $('#masuk_metode').on('change', function() {
      var value = $(this).val();
      if(value == 'Transfer'){
        $('#masuk_rekening_pengirim').attr('disabled', false);
        $('#masuk_rekening_penerima').attr('disabled', false);
        $('#masuk_rekening_pengirim').prop('required', true);
        $('#masuk_rekening_penerima').prop('required', true);
        $('#div_rekening_pengirim_masuk').show();
        $('#div_rekening_penerima_masuk').show();
        } else {
          $('#masuk_rekening_pengirim').attr('disabled', true);
          $('#masuk_rekening_penerima').attr('disabled', true);
          $('#masuk_rekening_pengirim').prop('required', false);
          $('#masuk_rekening_penerima').prop('required', false);
          $('#div_rekening_pengirim_masuk').hide();
          $('#div_rekening_penerima_masuk').hide();
      }
    });
    $('#edit_masuk_metode').on('change', function() {
      var value = $(this).val();
      if(value == 'Transfer'){
        $('#edit_masuk_rekening_pengirim').attr('disabled', false);
        $('#edit_masuk_rekening_penerima').attr('disabled', false);
        $('#edit_masuk_rekening_pengirim').prop('required', true);
        $('#edit_masuk_rekening_penerima').prop('required', true);
        $('#edit_div_rekening_pengirim_masuk').show();
        $('#edit_div_rekening_penerima_masuk').show();
      } else {
        $('#edit_masuk_rekening_pengirim').attr('disabled', true);
        $('#edit_masuk_rekening_penerima').attr('disabled', true);
        $('#edit_masuk_rekening_pengirim').prop('required', false);
        $('#edit_masuk_rekening_penerima').prop('required', false);
        $('#edit_div_rekening_pengirim_masuk').hide();
        $('#edit_div_rekening_penerima_masuk').hide();
      }
    });
    $('#keluar_metode').on('change', function() {
      var value = $(this).val();
      if(value == 'Transfer'){
        $('#keluar_rekening_pengirim').prop('disabled', false);
        $('#keluar_rekening_penerima').prop('disabled', false);
        $('#keluar_rekening').prop('disabled', false);
        $('#keluar_rekening_pengirim').prop('required', true);
        $('#keluar_rekening_penerima').prop('required', true);
        $('#keluar_rekening').prop('required', true);
        $('#div_rekening_pengirim_keluar').show();
        $('#div_rekening_penerima_keluar').show();
        $('#div_rekening_keluar').show();
        $('#div_lokasi_keluar')
          .removeClass('col-12')
          .addClass('col-6');
      } else {
        $('#keluar_rekening_pengirim').prop('disabled', true);
        $('#keluar_rekening_penerima').prop('disabled', true);
        $('#keluar_rekening').prop('disabled', true);
        $('#keluar_rekening_pengirim').prop('required', false);
        $('#keluar_rekening_penerima').prop('required', false);
        $('#keluar_rekening').prop('required', false);
        $('#div_rekening_pengirim_keluar').hide();
        $('#div_rekening_penerima_keluar').hide();
        $('#div_rekening_keluar').hide();
        $('#div_lokasi_keluar')
            .removeClass('col-6')
            .addClass('col-12');
      }
    });
    $('#edit_keluar_metode').on('change', function() {
      var value = $(this).val();
      if(value == 'Transfer'){
        $('#edit_keluar_rekening_pengirim').attr('disabled', false);
        $('#edit_keluar_rekening_penerima').attr('disabled', false);
        // $('#edit_keluar_rekening').attr('disabled', false);
        $('#edit_keluar_rekening_pengirim').prop('required', true);
        $('#edit_keluar_rekening_penerima').prop('required', true);
        // $('#edit_keluar_rekening').prop('required', true);
        $('#edit_div_rekening_pengirim_keluar').show();
        $('#edit_div_rekening_penerima_keluar').show();
        $('#edit_div_rekening_keluar').show();
        $('#edit_div_lokasi_keluar')
          .removeClass('col-12')
          .addClass('col-6');
      } else {
        $('#edit_keluar_rekening_pengirim').attr('disabled', true);
        $('#edit_keluar_rekening_penerima').attr('disabled', true);
        // $('#edit_keluar_rekening').attr('disabled', true);
        $('#edit_keluar_rekening_pengirim').prop('required', false);
        $('#edit_keluar_rekening_penerima').prop('required', false);
        // $('#edit_keluar_rekening').prop('required', false);
        $('#edit_div_rekening_pengirim_keluar').hide();
        $('#edit_div_rekening_penerima_keluar').hide();
        $('#edit_div_rekening_keluar').hide();
        $('#edit_div_lokasi_keluar')
            .removeClass('col-6')
            .addClass('col-12');
      }
    });

    function edit_keluar(id){
        $.ajax({
            type: "GET",
            url: "/kas_pusat/"+id+"/edit",
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            beforeSend: function() {
                $('#global-loader-transparent').show();
            },
            success: function(response) {
                $('#editFormKeluar').attr('action', 'kas_pusat/'+id+'/update');

                $('#edit_keluar_rekening').val(response.rekening_pengirim).trigger('change')
                $('#edit_keluar_lokasi').val(response.lokasi_pengirim).trigger('change')
                $('#edit_keluar_metode').val(response.metode).trigger('change')
                $('#edit_keluar_jenis').val(response.jenis).trigger('change')
                $('#edit_keluar_akun_id').val(response.akun_id).trigger('change')

                var rekeningPengirim = response.rekening_pengirim;
                var rekeningPenerima = response.rekening_penerima;
                
                $('#edit_keluar_lokasi_pengirim').val(response.lokasi_pengirim).trigger('change');
                $('#edit_keluar_lokasi_penerima').val(response.lokasi_penerima).trigger('change');
                
                $('#edit_keluar_tanggal').val(response.tanggal)
                $('#edit_keluar_nominal').val(response.nominal)
                $('#edit_keluar_biaya_lain').val(response.biaya_lain)
                $('#edit_keluar_keterangan').val(response.keterangan)
                $('#edit_keluar_status').val(response.status).trigger('change')
                $('#edit_keluar_preview').attr('src', 'storage/' + response.file)


                getRekening($('#edit_keluar_lokasi_pengirim')[0], 'edit_keluar_rekening_pengirim', rekeningPengirim, function() {
                    getRekening($('#edit_keluar_lokasi_penerima')[0], 'edit_keluar_rekening_penerima', rekeningPenerima, function() {
                        $('#global-loader-transparent').hide();
                        $('#editkaskeluar').modal('show');
                    });
                });

            },
            error: function(error) {
                $('#global-loader-transparent').hide();
                toastr.error('Ambil data error', 'Error', {
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: false,
                    progressBar: true
                });
            }
        });
    }

    function edit_masuk(id){
        $.ajax({
            type: "GET",
            url: "/kas_pusat/"+id+"/edit",
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            beforeSend: function() {
                $('#global-loader-transparent').show();
            },
            success: function(response) {
              $('#editFormMasuk').attr('action', 'kas_pusat/'+id+'/update');
              
              
              $('#edit_masuk_rekening').val(response.rekening_pengirim).trigger('change')
              $('#edit_masuk_lokasi').val(response.lokasi_pengirim).trigger('change')
              $('#edit_masuk_metode').val(response.metode).trigger('change')
              $('#edit_masuk_jenis').val(response.jenis).trigger('change')
              $('#edit_masuk_akun_id').val(response.akun_id).trigger('change')
              
              var rekeningPengirim = response.rekening_pengirim;
              var rekeningPenerima = response.rekening_penerima;
              
              $('#edit_masuk_lokasi_pengirim').val(response.lokasi_pengirim).trigger('change')
              $('#edit_masuk_lokasi_penerima').val(response.lokasi_penerima).trigger('change')

              $('#edit_masuk_tanggal').val(response.tanggal)
              $('#edit_masuk_nominal').val(response.nominal)
              $('#edit_masuk_biaya_lain').val(response.biaya_lain)
              $('#edit_masuk_keterangan').val(response.keterangan)
              $('#edit_masuk_status').val(response.status).trigger('change')
              $('#edit_masuk_preview').attr('src', 'storage/' + response.file)

              getRekening($('#edit_masuk_lokasi_pengirim')[0], 'edit_masuk_rekening_pengirim', rekeningPengirim, function() {
                  getRekening($('#edit_masuk_lokasi_penerima')[0], 'edit_masuk_rekening_penerima', rekeningPenerima, function() {
                      $('#global-loader-transparent').hide();
                      $('#editkasmasuk').modal('show');
                  });
              });
            },
            error: function(error) {
                $('#global-loader-transparent').hide();
                toastr.error('Ambil data error', 'Error', {
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: false,
                    progressBar: true
                });
            }
        });
    }

    function deleteData(id){
      Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                  type: "GET",
                  url: "/kas_pusat/"+id+"/delete",
                  headers: {
                      'X-CSRF-TOKEN': csrfToken
                  },
                  success: function(response) {
                      toastr.success(response.msg, 'Success', {
                          closeButton: true,
                          tapToDismiss: false,
                          rtl: false,
                          progressBar: true
                      });
      
                      setTimeout(() => {
                          location.reload()
                      }, 2000);
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
        });
    }
    function getRekening(element, id, selectedRekening = '', callback = null) {
        var lokasi_id = element.value;
        if(!lokasi_id){
            var rekeningElement = $('#'+id);
            rekeningElement.attr('disabled', true);
            rekeningElement.attr('required', false);
            rekeningElement.empty();
            rekeningElement.append('<option value="">Rekening</option>');
            if(callback) callback();
            return false;
        }
        $.ajax({
            type: "GET",
            url: "/rekeningPerLokasi/",
            data: {
                lokasi_id: lokasi_id,
            },
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                populateRekening(response, id, selectedRekening);
                if(callback) callback();
            },
            error: function(error) {
                var rekeningElement = $('#'+id);
                rekeningElement.attr('disabled', true);
                rekeningElement.attr('required', false);
                rekeningElement.empty();
                rekeningElement.append('<option value="">Rekening</option>');
                if(callback) callback();
                toastr.error('Ambil data error', 'Error', {
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: false,
                    progressBar: true
                });
            }
        });
    }

    function populateRekening(data, id, selectedRekening) {
      var rekeningElement = $('#'+id);
      rekeningElement.attr('disabled', false);
      rekeningElement.attr('required', false);
      rekeningElement.empty();
      rekeningElement.append('<option value="">Rekening</option>');
      
      data.forEach(function(item) {
          var isSelected = selectedRekening == item['id'] ? 'selected' : '';
          rekeningElement.append('<option value="'+item['id']+'" '+isSelected+'>'+item['nama_akun']+'</option>');
      });
    }
    $('#filterBtn').click(function(){
        var baseUrl = $(this).data('base-url');
        var urlString = baseUrl;
        var first = true;
        var symbol = '';

        var Rekening = $('#filterRekening').val();
        if (Rekening) {
            var filterRekening = 'rekening=' + Rekening;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterRekening;
        }
        window.location.href = urlString;
    });
    $('#clearBtn').click(function(){
        var baseUrl = $(this).data('base-url');
        var url = window.location.href;
        if(url.indexOf('?') !== -1){
            window.location.href = baseUrl;
        }
        return 0;
    });
    function previewImage(element, preview_id) {
        const file = $(element)[0].files[0];
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
                $('#' + preview_id).attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    };

    function log(id) {
      if ($.fn.DataTable.isDataTable('#logTable')) {
          $('#logTable').DataTable().clear().destroy();
      }
      const logColumns = [
          { data: 'no', name: 'no', orderable: false },
          { data: 'created_at', name: 'created_at' },
          { 
            data: 'causer_id', 
            name: 'causer_id', 
            orderable: false, 
            render: function(data, type, row){
              return row.pengubah;
            } 
          },
          { 
              data: 'description_log', 
              name: 'description_log', 
              orderable: false,
              render: function(data, type, row) {
                  let descriptionLogHtml = '';
                  if (Array.isArray(row.description_log)) {
                      row.description_log.forEach(log => {
                          if (log.message) {
                              descriptionLogHtml += `<div>${log.message}</div>`;
                          } else {
                              descriptionLogHtml += `<div>${log.field}: <span class='text-danger'>${log.old}</span> => <span class='text-success'>${log.new}</span></div>`;
                          }
                          if(row.description_log.length > 1){
                            descriptionLogHtml += `<br>`
                          }
                      });
                  }
                  return descriptionLogHtml;
              }
          }
      ];

      let tableLog = initDataTable('#logTable', {
          ajaxUrl: "log/"+id,
          columns: logColumns,
          order: [[1, 'asc']],
          searching: true,
          lengthChange: true,
          pageLength: 5
      }, {
      }, 'logTable'); 

      $('#modalLog').modal('show');
    }
    </script>
@endsection