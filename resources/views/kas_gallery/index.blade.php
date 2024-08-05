@extends('layouts.app-von')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="dash-count" style="background-color: rgb(255, 159, 67)">
      @role('Finance')
      <div class="row w-100">
        <div class="col-lg-4 col-md-4 col-sm-12 mb-2 mb-md-0">
          <h2>Kas Gallery</h2>
        </div>
        <div class="col-lg-8 col-md-8 col-sm-12 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-end gap-2">
          <div class="w-100 mb-2 mb-md-0">
            <select class="select2 form-select w-100" name="lokasi" id="filterLokasi" onchange="getRekening(this, 'filterRekening')">
              <option value="">Lokasi</option>
              @foreach($lokasis as $lokasi)
                <option value="{{ $lokasi->id }}" {{ $lokasi->id == $lokasi_pengirim ? 'selected' : '' }}>{{ $lokasi->nama }}</option>
              @endforeach
            </select>
          </div>
          <div class="w-100 mb-2 mb-md-0">
            <select class="select2 form-select w-100" name="rekening" id="filterRekening" disabled>
              <option value="">Rekening</option>
              @foreach($rekenings as $rekening)
              @if($rekening->lokasi_id == $lokasi_pengirim)
                <option value="{{ $rekening->id }}" {{ $rekening->id == request()->input('rekening') ? 'selected' : '' }}>{{ $rekening->nama_akun }}</option>
              @endif
              @endforeach
            </select>
          </div>
          <div class="d-flex flex-column flex-md-row gap-2 w-100">
            <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('kas_gallery.index') }}" class="btn btn-info w-100 w-md-auto">Filter</a>
            <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('kas_gallery.index') }}" class="btn btn-warning w-100 w-md-auto">Clear</a>
          </div>
        </div>
      </div>
      @endrole
      @role('AdminGallery')
      <div class="row w-100">
        <div class="col-lg-6 col-md-6 col-sm-12 mb-2 mb-md-0">
          <h2>Kas Gallery</h2>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-end gap-2">
          <div class="d-flex flex-column flex-md-row w-100">
            <select class="form-select select2 w-100" name="rekening" id="filterRekening">
              <option value="">Rekening</option>
              @foreach($rekenings as $rekening)
              @if($rekening->lokasi_id == Auth::user()->karyawans->lokasi_id)
                <option value="{{ $rekening->id }}" {{ $rekening->id == request()->input('rekening') ? 'selected' : '' }}>{{ $rekening->nama_akun }}</option>
              @endif
              @endforeach
            </select>
          </div>
          <div class="d-flex flex-column flex-md-row gap-2 w-100">
            <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('kas_gallery.index') }}" class="btn btn-info w-100">Filter</a>
            <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('kas_gallery.index') }}" class="btn btn-warning w-100">Clear</a>
          </div>
        </div>
      </div>
      @endrole
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
            <div class="col-8 d-flex flex-column justify-content-center" style="background-color: #28a745; color: white;">
              <div class="card-body">
                <h3>Rp. <span class="counters-rupiah" data-count="{{ $saldoMasuk }}"></span></h3>
                <h6>Saldo Masuk</h6>
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
            <div class="col-8 d-flex flex-column justify-content-center" style="background-color: #ff006a; color: white;">
              <div class="card-body">
                <h3>Rp. <span class="counters-rupiah" data-count="{{ $saldoKeluar }}"></span></h3>
                <h6>Saldo Keluar</h6>
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
            <div class="col-8 d-flex flex-column justify-content-center" style="background-color: #0131c3; color: white;">
              <div class="card-body">
                <h3>Rp. <span class="counters-rupiah" data-count="{{ $saldoMasuk - $saldoKeluar }}"></span></h3>
                <h6>Saldo</h6>
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
                  <th>Biaya Lainnya</th>
                  <th>Keterangan</th>
                  <th>Status</th>
                  {{-- <th>Aksi</th> --}}
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
                          <td>{{ $item->nominal ? formatRupiah($item->nominal) : 0 }}</td>
                          <td>{{ $item->biaya_lain ? formatRupiah($item->biaya_lain) : 0 }}</td>
                          <td>{{ $item->keterangan ?? '-' }}</td>
                          <td>
                            <span class="badges {{ $item->status == 'DIKONFIRMASI' ? 'bg-lightgreen' : 'bg-lightgrey' }}">{{ $item->status ?? '-' }}</span>
                          </td>
                          {{-- <td class="text-center">
                            <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="javascript:void(0);" onclick="edit({{ $item->id }})" data-bs-toggle="modal" data-bs-target="#editkas" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                </li>
                            </ul>
                          </td> --}}
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
                @if(in_array('kas_gallery.create', $thisUserPermissions) && in_array('kas_gallery.store', $thisUserPermissions))
                  <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addkaskeluar" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Kas Keluar</a>
                @endif
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
                  <th>Metode</th>
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
                  @foreach ($dataKeluar as $item)
                      <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $item->jenis ?? '-' }}</td>
                          <td>{{ $item->metode ?? '-' }}</td>
                          <td>{{ $item->rek_pengirim->nama_akun ?? '-' }}</td>
                          <td>{{ $item->tanggal ? formatTanggal($item->tanggal) : '-' }}</td>
                          <td>{{ $item->nominal ? formatRupiah($item->nominal) : 0 }}</td>
                          <td>{{ $item->biaya_lain ? formatRupiah($item->biaya_lain) : 0 }}</td>
                          <td>{{ $item->keterangan ?? '-' }}</td>
                          <td>
                            <span class="badges {{ $item->status == 'DIKONFIRMASI' ? 'bg-lightgreen' : 'bg-lightgrey' }}">{{ $item->status ?? '-' }}</span>
                          </td>
                          <td class="text-center">
                            <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu">
                              @if(in_array('kas_gallery.edit', $thisUserPermissions) && in_array('kas_gallery.update', $thisUserPermissions))
                                <li>
                                  <a href="javascript:void(0);" onclick="edit({{ $item->id }})" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                </li>
                              @endif
                              <li>
                                  <a href="javascript:void(0);" onclick="bukti('{{ $item->file }}')" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Bukti</a>
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
<div class="modal fade" id="addkaskeluar" tabindex="-1" aria-labelledby="addkaskeluarlabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addkaskeluarlabel">Tambah Transaksi Keluar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="addForm" action="{{ route('kas_gallery.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="lokasi_pengirim" value="{{ $lokasi_pengirim ?? '' }}">
            <input type="hidden" name="jenis" value="Lainnya">
            <div class="row">
              <div class="col-6">
                <div class="col-12 mb-2">
                  <label for="metode" class="col-form-label">Metode</label>
                  <select class="select2" name="metode" id="keluar_metode" required>
                    <option value="Transfer">Transfer</option>
                    <option value="Cash">Cash</option>
                  </select>
                </div>
                <div class="col-12 mb-2" id="div_add_rekening_keluar">
                  <label for="rekening_pengirim" class="col-form-label">Rekening</label>
                  <select class="select2" name="rekening_pengirim" id="keluar_rekening_pengirim" required>
                    <option value="">Rekening</option>
                    @foreach($rekeningKeluar as $rekening)
                        <option value="{{ $rekening->id }}">{{ $rekening->nama_akun }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-12 mb-2">
                  <label for="tanggal" class="col-form-label">Tanggal</label>
                  <input type="date" class="form-control" name="tanggal" id="keluar_tanggal" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-12 mb-2">
                  <label for="nominal" class="col-form-label">Nominal</label>
                  <input type="text" class="form-control" name="nominal" id="keluar_nominal" required>
                </div>
                <div class="col-12 mb-2">
                  <label for="biaya_lain" class="col-form-label">Biaya Lain</label>
                  <input type="text" class="form-control" name="biaya_lain" id="keluar_biaya_lain">
                </div>
                <div class="col-12 mb-2">
                  <label for="keterangan" class="col-form-label">Keterangan</label>
                  <input type="text" class="form-control" name="keterangan" id="keluar_keterangan" required>
                </div>
              </div>
              <div class="col-6">
                <div class="col-12 mb-2">
                  <label for="file" class="col-form-label">File</label>
                  <input type="file" class="form-control" name="file" id="keluar_file" accept="image/*" required onchange="previewImage(this, 'preview')">
                  <img class="mt-2" src="" alt="" id="preview" style="width: 100%;height:auto;">
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
        <h5 class="modal-title" id="editkaskeluarlabel">Edit Transaksi Masuk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
      </div>
      <div class="modal-body">
        <form id="editForm" action="" method="POST" enctype="multipart/form-data">
          @csrf
          @method('patch')
          <input type="hidden" name="lokasi_pengirim" value="{{ $lokasi_pengirim ?? '' }}">
          <input type="hidden" name="jenis" value="Lainnya">
          <div class="row">
            <div class="col-6">
              <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 mb-2">
                  <label for="status" class="col-form-label">Status</label>
                  <select class="select2" name="status" id="edit_keluar_status" required>
                    <option value="BATAL">BATAL</option>
                    <option value="DIKONFIRMASI">DIKONFIRMASI</option>
                  </select>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12 mb-2">
                  <label for="metode" class="col-form-label">Metode</label>
                  <select class="select2" name="metode" id="edit_keluar_metode" required>
                    <option value="Transfer">Transfer</option>
                    <option value="Cash">Cash</option>
                  </select>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-6 mb-2" id="div_edit_rekening_keluar">
                  <label for="rekening_pengirim" class="col-form-label">Rekening</label>
                  <select class="select2" name="rekening_pengirim" id="edit_keluar_rekening_pengirim" required>
                    <option value="">Rekening</option>
                    @foreach($rekeningKeluar as $rekening)
                        <option value="{{ $rekening->id }}">{{ $rekening->nama_akun }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-6 mb-2" id="div_edit_keluar_tanggal">
                  <label for="tanggal" class="col-form-label">Tanggal</label>
                  <input type="date" class="form-control" name="tanggal" id="edit_keluar_tanggal" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12 mb-2">
                  <label for="nominal" class="col-form-label">Nominal</label>
                  <input type="text" class="form-control" name="nominal" id="edit_keluar_nominal" required>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12 mb-2">
                  <label for="biaya_lain" class="col-form-label">Biaya Lain</label>
                  <input type="text" class="form-control" name="biaya_lain" id="edit_keluar_biaya_lain">
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12 mb-2">
                  <label for="keterangan" class="col-form-label">Keterangan</label>
                  <input type="text" class="form-control" name="keterangan" id="edit_keluar_keterangan" required>
                </div>
              </div>
            </div>
            <div class="col-6">
              <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 mb-2">
                  <label for="file" class="col-form-label">File</label>
                  <input type="file" class="form-control" name="file" id="edit_keluar_file" accept="image/*" onchange="previewImage(this, 'edit_preview')">
                  <img class="mt-2" src="" alt="" id="edit_preview" style="width: 100%;height:auto;">
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
      </div>
    </div>
  </div>
</div>
{{-- modal end --}}
@endsection

@section('scripts')
    <script>
    $(document).ready(function() {
        $('[id^=masuk_rekening_], [id^=keluar_rekening_], [id^=masuk_lokasi_], [id^=keluar_lokasi_], [id^=filterRekening], [id^=filterLokasi], #edit_keluar_status, #edit_keluar_rekening_pengirim, #keluar_metode, #edit_keluar_metode').select2()
    });
    function bukti(src){
        var baseUrl = window.location.origin;
        var fullUrl = baseUrl + '/storage/' + src;
        $('#imgBukti').attr('src', fullUrl);
        $('#modalBukti').modal('show');
    }
    $(document).on('input', '#keluar_nominal, #keluar_biaya_lain, #edit_keluar_nominal, #edit_keluar_biaya_lain', function() {
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
        let inputs = $('#addForm').find('#keluar_nominal, #keluar_biaya_lain, #edit_keluar_nominal, #edit_keluar_biaya_lain');
        inputs.each(function() {
            let input = $(this);
            let value = input.val();
            let cleanedValue = cleanNumber(value);

            input.val(cleanedValue);
        });

        return true;
    });
    $('#editForm').on('submit', function(e) {
        let inputs = $('#editForm').find('#keluar_nominal, #keluar_biaya_lain, #edit_keluar_nominal, #edit_keluar_biaya_lain');
        inputs.each(function() {
            let input = $(this);
            let value = input.val();
            let cleanedValue = cleanNumber(value);

            input.val(cleanedValue);
        });

        return true;
    });
    $('#keluar_metode').on('change', function() {
      var value = $(this).val();
      if(value == 'Transfer'){
        $('#div_add_rekening_keluar').show();
        $('#keluar_rekening_pengirim').attr('disabled', false);
      } else {
        $('#div_add_rekening_keluar').hide();
        $('#keluar_rekening_pengirim').attr('disabled', true);
      }
    });
    $('#edit_keluar_metode').on('change', function() {
      var value = $(this).val();
      if(value == 'Transfer'){
        $('#div_edit_rekening_keluar').show();
        $('#edit_keluar_rekening_pengirim').attr('disabled', false);
        $('#div_edit_keluar_tanggal')
          .removeClass('col-sm-12 col-md-12 col-lg-12 mb-2')
          .addClass('col-sm-12 col-md-6 col-lg-6 mb-2');
        } else {
          $('#div_edit_rekening_keluar').hide();
          $('#edit_keluar_rekening_pengirim').attr('disabled', true);
          $('#div_edit_keluar_tanggal')
            .removeClass('col-sm-12 col-md-6 col-lg-6 mb-2')
            .addClass('col-sm-12 col-md-12 col-lg-12 mb-2');
      }
    });

    function edit(id){
        $.ajax({
            type: "GET",
            url: "/kas_gallery/"+id+"/edit",
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                // console.log(response)
                $('#editForm').attr('action', 'kas_gallery/'+id+'/update');
                $('#edit_keluar_rekening_pengirim').val(response.rekening_pengirim).trigger('change')
                $('#edit_keluar_metode').val(response.metode).trigger('change')
                $('#edit_keluar_tanggal').val(response.tanggal)
                $('#edit_keluar_nominal').val(response.nominal)
                $('#edit_keluar_biaya_lain').val(response.biaya_lain)
                $('#edit_keluar_keterangan').val(response.keterangan)
                $('#edit_keluar_status').val(response.status).trigger('change')
                $('#edit_preview').attr('src', 'storage/'+response.file)
                $('#editkaskeluar').modal('show');
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
                  url: "/kas_gallery/"+id+"/delete",
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

        var Lokasi = $('#filterLokasi').val();
        if (Lokasi) {
            var filterLokasi = 'lokasi=' + Lokasi;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterLokasi;
        }

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
    </script>
@endsection