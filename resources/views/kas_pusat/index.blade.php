@extends('layouts.app-von')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="dash-count">
      <div class="col-4">
        <h2>Kas Pusat</h2>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-4">
        <select class="select2" name="rekening" id="filterRekening">
          <option value="">Rekening</option>
          @foreach($rekenings as $rekening)
            <option value="{{ $rekening->id }}" {{ $rekening->id == request()->input('rekening') ? 'selected' : '' }}>{{ $rekening->nama_akun }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-lg-2 col-md-2 col-sm-2">
        <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('kas_pusat.index') }}" class="btn btn-info">Filter</a>
      </div>
      <div class="col-lg-2 col-md-2 col-sm-2">
        <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('kas_pusat.index') }}" class="btn btn-warning">Clear</a>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-sm-12">
    <div class="row">
      <div class="col-lg-4 col-md-4 col-sm-12">
        <div class="dash-widget dash1">
          <div class="dash-widgetimg">
            <span><img src="assets/img/icons/dash2.svg" alt="img" /></span>
          </div>
          <div class="dash-widgetcontent">
            <h5>Rp. <span class="counters-rupiah" data-count="{{ $dataMasuk->sum('nominal') }}"></span></h5>
            <h6>Saldo Masuk</h6>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-12">
        <div class="dash-widget dash3">
          <div class="dash-widgetimg">
            <span><img src="assets/img/money-send-svgrepo-com.svg" alt="img" style="width: 50%" /></span>
          </div>
          <div class="dash-widgetcontent">
            <h5>Rp. <span class="counters-rupiah" data-count="{{ ($dataKeluar->sum('nominal') + $dataKeluar->sum('biaya_lain')) }}"></span></h5>
            <h6>Saldo Keluar</h6>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-12">
        <div class="dash-widget dash4">
          <div class="dash-widgetimg">
            <span><img src="assets/img/balance-sheet.png" style="width: 50%" alt="img" /></span>
          </div>
          <div class="dash-widgetcontent">
            <h5>Rp. <span class="counters-rupiah" data-count="{{ ($dataMasuk->sum('nominal') - ($dataKeluar->sum('nominal') + $dataKeluar->sum('biaya_lain'))) }}"></span></h5>
            <h6>Saldo</h6>
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
                  <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addkasmasuk" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Kas Masuk</a>
              </div>
          </div>
      </div>
      <div class="card-body">
          <div class="table-responsive">
          <table class="table datanew">
              <thead>
              <tr>
                  <th>No</th>
                  <th>Jenis</th>
                  <th>Pengirim</th>
                  <th>Rekening Pengirim</th>
                  <th>Penerima</th>
                  <th>Rekening Penerima</th>
                  <th>Tanggal</th>
                  <th>Nominal</th>
                  <th>Biaya Lain</th>
                  <th>Keterangan</th>
                  <th>Status</th>
                  <th>Aksi</th>
              </tr>
              </thead>
              <tbody>
                  @foreach ($dataMasuk as $item)
                      <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $item->jenis ?? '-' }}</td>
                          <td>{{ $item->lok_pengirim->nama ?? '-' }}</td>
                          <td>{{ $item->rek_pengirim->nama_akun ?? '-' }}</td>
                          <td>{{ $item->lok_penerima->nama ?? '-' }}</td>
                          <td>{{ $item->rek_penerima->nama_akun ?? '-' }}</td>
                          <td>{{ $item->tanggal ? formatTanggal($item->tanggal) : '-' }}</td>
                          <td>{{ $item->nominal ? formatRupiah($item->nominal) : '-' }}</td>
                          <td>{{ $item->biaya_lain ? formatRupiah($item->biaya_lain) : '-' }}</td>
                          <td>{{ $item->keterangan ?? '-' }}</td>
                          <td>{{ $item->status ?? '-' }}</td>
                          <td class="text-center">
                            <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="javascript:void(0);" onclick="getData({{ $item->id }})" data-bs-toggle="modal" data-bs-target="#editkas" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                </li>
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
  <div class="col-sm-12">
      <div class="card">
      <div class="card-header">
          <div class="page-header">
              <div class="page-title">
                  <h4>Kas Keluar</h4>
              </div>
              <div class="page-btn">
                  <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addkaskeluar" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Kas Keluar</a>
              </div>
          </div>
      </div>
      <div class="card-body">
          <div class="table-responsive">
          <table class="table datanew">
              <thead>
              <tr>
                  <th>No</th>
                  <th>Jenis</th>
                  <th>Pengirim</th>
                  <th>Rekening Pengirim</th>
                  <th>Penerima</th>
                  <th>Rekening Penerima</th>
                  <th>Tanggal</th>
                  <th>Nominal</th>
                  <th>Biaya Lain</th>
                  <th>Keterangan</th>
                  <th>Status</th>
                  <th>Aksi</th>
              </tr>
              </thead>
              <tbody>
                  @foreach ($dataKeluar as $item)
                      <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $item->jenis ?? '-' }}</td>
                          <td>{{ $item->lok_pengirim->nama ?? '-' }}</td>
                          <td>{{ $item->rek_pengirim->nama_akun ?? '-' }}</td>
                          <td>{{ $item->lok_penerima->nama ?? '-' }}</td>
                          <td>{{ $item->rek_penerima->nama_akun ?? '-' }}</td>
                          <td>{{ $item->tanggal ? formatTanggal($item->tanggal) : '-' }}</td>
                          <td>{{ $item->nominal ? formatRupiah($item->nominal) : '-' }}</td>
                          <td>{{ $item->biaya_lain ? formatRupiah($item->biaya_lain) : '-' }}</td>
                          <td>{{ $item->keterangan ?? '-' }}</td>
                          <td>{{ $item->status ?? '-' }}</td>
                          <td class="text-center">
                            <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="javascript:void(0);" onclick="getData({{ $item->id }})" data-bs-toggle="modal" data-bs-target="#editkas" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                </li>
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

{{-- modal start --}}
<div class="modal fade" id="addkasmasuk" tabindex="-1" aria-labelledby="addkasmasuklabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addkasmasuklabel">Tambah Transaksi Masuk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="addFormMasuk" action="{{ route('kas_pusat.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="jenis" value="Pemindahan Saldo">
            <div class="row mb-3">
              <div class="col-6">
                <label for="lokasi_pengirim" class="col-form-label">Lokasi Pengirim</label>
                <div class="form-group">
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
                <div class="form-group">
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
            </div>
            <div class="row mb-3">
              <div class="col-6">
                <label for="rekening_pengirim" class="col-form-label">Rekening Pengirim</label>
                <div class="form-group">
                  <select class="select2" name="rekening_pengirim" id="masuk_rekening_pengirim" disabled>
                    <option value="">Rekening Pengirim</option>
                  </select>
                </div>
              </div>
              <div class="col-6">
                <label for="rekening_penerima" class="col-form-label">Rekening Penerima</label>
                <div class="form-group">
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
            </div>
            <div class="mb-3">
              <label for="tanggal" class="col-form-label">Tanggal</label>
              <input type="date" class="form-control" name="tanggal" id="masuk_tanggal" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="mb-3">
              <label for="nominal" class="col-form-label">Nominal</label>
              <input type="text" class="form-control" name="nominal" id="masuk_nominal" required>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-12 mb-2">
              <label for="biaya_lain" class="col-form-label">Biaya Lain</label>
              <input type="text" class="form-control" name="biaya_lain" id="masuk_biaya_lain">
            </div>
            <div class="mb-3">
              <label for="keterangan" class="col-form-label">Keterangan</label>
              <input type="text" class="form-control" name="keterangan" id="masuk_keterangan" required>
            </div>
            <div class="mb-3">
              <label for="file" class="col-form-label">File</label>
              <input type="file" class="form-control" name="file" id="masuk_file" accept="image/*" required>
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
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addkaskeluarlabel">Tambah Transaksi Keluar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="addFormKeluar" action="{{ route('kas_pusat.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
              <div class="col-12">
                <label for="jenis" class="col-form-label">Jenis</label>
                <div class="form-group">
                  <select class="select2" name="jenis" id="keluar_jenis" required>
                    <option value="Pemindahan Saldo">Pemindahan Saldo</option>
                    <option value="Lainnya">Lainnya</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row pemindahan_saldo">
              <div class="col-6">
                <label for="lokasi_pengirim" class="col-form-label">Lokasi Pengirim</label>
                <div class="form-group">
                  <select class="select2" name="lokasi_pengirim" id="keluar_lokasi_pengirim" onchange="getRekening(this, 'keluar_rekening_pengirim')" required>
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
                <div class="form-group">
                  <select class="select2" name="lokasi_penerima" id="keluar_lokasi_penerima" onchange="getRekening(this, 'keluar_rekening_penerima')" required>
                    <option value="">Lokasi Penerima</option>
                    @foreach($lokasis as $lokasi)
                      <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="row pemindahan_saldo">
              <div class="col-6">
                <label for="rekening_pengirim" class="col-form-label">Rekening Pengirim</label>
                <div class="form-group">
                  <select class="select2" name="rekening_pengirim" id="keluar_rekening_pengirim" disabled>
                    <option value="">Rekening Pengirim</option>
                  </select>
                </div>
              </div>
              <div class="col-6">
                <label for="rekening_penerima" class="col-form-label">Rekening Penerima</label>
                <div class="form-group">
                  <select class="select2" name="rekening_penerima" id="keluar_rekening_penerima" disabled>
                    <option value="">Rekening Penerima</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row lainnya d-none">
              <div class="col-6 mb-3">
                <label for="lokasi_pengirim" class="col-form-label">lokasi</label>
                <select class="select2" name="lokasi_pengirim" id="keluar_lokasi" disabled>
                  <option value="">lokasi</option>
                  @foreach($lokasis as $lokasi)
                      @if($lokasi->operasional_id == 1)
                        <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                      @endif
                    @endforeach
                </select>
              </div>
              <div class="col-6 mb-3">
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
            <div class="mb-3">
              <label for="tanggal" class="col-form-label">Tanggal</label>
              <input type="date" class="form-control" name="tanggal" id="keluar_tanggal" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="mb-3">
              <label for="nominal" class="col-form-label">Nominal</label>
              <input type="text" class="form-control" name="nominal" id="keluar_nominal" required>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-12 mb-2">
              <label for="biaya_lain" class="col-form-label">Biaya Lain</label>
              <input type="text" class="form-control" name="biaya_lain" id="keluar_biaya_lain">
            </div>
            <div class="mb-3">
              <label for="keterangan" class="col-form-label">Keterangan</label>
              <input type="text" class="form-control" name="keterangan" id="keluar_keterangan" required>
            </div>
            <div class="mb-3">
              <label for="file" class="col-form-label">File</label>
              <input type="file" class="form-control" name="file" id="keluar_file" accept="image/*" required>
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
{{-- <div class="modal fade" id="editkas" tabindex="-1" aria-labelledby="editkaslabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editkaslabel">Edit Transaksi Kas</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="editForm" action="kas_pusat/0/update" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label for="nama" class="col-form-label">Akun</label>
              <div class="form-group">
                <select class="select2" name="akun_id" id="edit_akun_id" required>
                  <option value="">Pilih Akun</option>
                  @foreach($akuns as $akun)
                    <option value="{{ $akun->id }}">{{ $akun->no_akun }} - {{ $akun->nama_akun }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="keterangan" class="col-form-label">Keterangan</label>
              <input type="text" class="form-control" name="keterangan" id="edit_keterangan" required>
            </div>
            <div class="mb-3">
              <label for="kuantitas" class="col-form-label">Kuantitas</label>
              <input type="number" class="form-control" name="kuantitas" id="edit_kuantitas" required>
            </div>
            <div class="mb-3">
              <label for="harga_satuan" class="col-form-label">Harga Satuan</label>
              <input type="number" class="form-control" name="harga_satuan" id="edit_harga_satuan" required>
            </div>
            <div class="mb-3">
              <label for="harga_total" class="col-form-label">Harga Total</label>
              <input type="number" class="form-control" name="harga_total" id="edit_harga_total" readonly required>
            </div>
            <div class="mb-3">
              <label for="lokasi_id" class="col-form-label">Lokasi</label>
              <div class="form-group">
                <select class="select2" name="lokasi_id" id="edit_lokasi_id" required>
                  <option value="">Pilih Lokasi</option>
                  @foreach($lokasis as $lokasi)
                    <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="tanggal_transaksi" class="col-form-label">Tanggal Transaksi</label>
              <input type="date" class="form-control" name="tanggal_transaksi" id="edit_tanggal_transaksi" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="mb-3">
              <label for="bukti" class="col-form-label">Bukti</label>
              <input type="file" class="form-control" name="bukti" id="edit_bukti">
            </div>
            <div class="mb-3">
              <label for="status" class="col-form-label">Status</label>
              <div class="form-group">
                <select class="select2" name="status" id="edit_status" required>
                  <option value="AKTIF">Aktif</option>
                  <option value="TIDAK AKTIF">Tidak Aktif</option>
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
</div> --}}
<div class="modal fade" id="modalBukti" tabindex="-1" aria-labelledby="addAkunlabel" aria-hidden="true">
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
          <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </div>
  </div>
</div>
{{-- modal end --}}
@endsection

@section('scripts')
    <script>
    $(document).ready(function() {
        // getRekening($('#filterLokasi'), 'filterRekening')
        $('[id^=masuk_rekening_], [id^=keluar_rekening_], [id^=masuk_lokasi_], [id^=keluar_lokasi_], [id^=filterRekening], [id^=filterLokasi], #keluar_jenis, #keluar_rekening, #keluar_lokasi').select2()
    });
    function bukti(src){
        var baseUrl = window.location.origin;
        var fullUrl = baseUrl + '/storage/' + src;
        $('#imgBukti').attr('src', fullUrl);
        $('#modalBukti').modal('show');
    }
    $(document).on('input', '#keluar_nominal, #keluar_biaya_lain, #masuk_nominal, #masuk_biaya_lain', function() {
        let input = $(this);
        let value = input.val();
        if (!isNumeric(cleanNumber(value))) {
            value = value.replace(/[^\d]/g, "");
        }
        value = cleanNumber(value);
        let formattedValue = formatNumber(value);
        
        input.val(formattedValue);
    });
    $('#addFormMasuk, #addFormKeluar').on('submit', function(e) {
        let inputs = $('#addFormMasuk, #addFormKeluar').find('#keluar_nominal, #keluar_biaya_lain, #masuk_nominal, #masuk_biaya_lain');
        inputs.each(function() {
            let input = $(this);
            let value = input.val();
            let cleanedValue = cleanNumber(value);

            input.val(cleanedValue);
        });

        return true;
    });
    $(document).on('change', '#keluar_jenis', function(){
        var jenis = $(this).val();
        if(jenis != 'Pemindahan Saldo') {
            $('div.pemindahan_saldo').addClass('d-none');
            $('div.pemindahan_saldo select').each(function(){
                $(this).attr('disabled', true).attr('required', false);
            });
            
            $('div.lainnya').removeClass('d-none');
            $('div.lainnya select').each(function(){
                $(this).attr('disabled', false).attr('required', true);
            });
        } else {
            $('div.lainnya').addClass('d-none');
            $('div.lainnya select').each(function(){
                $(this).attr('disabled', true).attr('required', false);
            });
            
            $('div.pemindahan_saldo').removeClass('d-none');
            $('div.pemindahan_saldo select').each(function(){
                $(this).attr('disabled', false).attr('required', true);
            });
        }
    });

    function getData(id){
        $.ajax({
            type: "GET",
            url: "/kas_pusat/"+id+"/edit",
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                // console.log(response)
                $('#editForm').attr('action', 'kas_pusat/'+id+'/update');
                $('#edit_akun_id').val(response.akun_id).trigger('change')
                $('#edit_keterangan').val(response.keterangan)
                $('#edit_kuantitas').val(response.kuantitas)
                $('#edit_harga_satuan').val(response.harga_satuan)
                $('#edit_harga_total').val(response.harga_total)
                $('#edit_lokasi_id').val(response.lokasi_id).trigger('change')
                $('#edit_tanggal_transaksi').val(response.tanggal_transaksi)
                $('#edit_status').val(response.status).trigger('change')
            },
            error: function(error) {
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
    function getRekening(element, id) {
      var lokasi_id = element.value;
      if(!lokasi_id){
        var rekeningElement = $('#'+id);
        rekeningElement.attr('disabled', true);
        rekeningElement.attr('required', false);
        rekeningElement.empty();
        rekeningElement.append('<option value="">Rekening</option>');
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
                populateRekening(response, id)
            },
            error: function(error) {
              var rekeningElement = $('#'+id);
              rekeningElement.attr('disabled', true);
              rekeningElement.attr('required', false);
              rekeningElement.empty();
              rekeningElement.append('<option value="">Rekening</option>');
              toastr.error('Ambil data error', 'Error', {
                  closeButton: true,
                  tapToDismiss: false,
                  rtl: false,
                  progressBar: true
              });
            }
        });
    }
    function populateRekening(data, id) {
      var rekeningElement = $('#'+id);
      rekeningElement.attr('disabled', false);
      rekeningElement.attr('required', true);
      rekeningElement.empty();
      rekeningElement.append('<option value="">Rekening</option>');
      data.forEach(function(item){
        rekeningElement.append('<option value="'+item['id']+'">'+item['nama_akun']+'</option>')
      })
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
    </script>
@endsection