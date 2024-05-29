@extends('layouts.app-von')

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="row">
      <div class="col-lg-3 col-sm-6 col-12">
        <div class="dash-widget dash1">
          <div class="dash-widgetimg">
            <span><img src="assets/img/icons/dash2.svg" alt="img" /></span>
          </div>
          <div class="dash-widgetcontent">
            <h5>Rp. <span class="counters-rupiah" data-count="{{ $saldo }}"></span></h5>
            <h6>Saldo Kas</h6>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6 col-12">
        <div class="dash-widget dash2">
          <div class="dash-widgetimg">
            <span><img src="assets/img/money-receive-svgrepo-com.svg" alt="img" style="width: 50%" alt="img" /></span>
          </div>
          <div class="dash-widgetcontent">
            <h5>Rp. <span class="counters-rupiah" data-count="{{ $totalPenjualan }}"></span></h5>
            <h6>Total Penjualan</h6>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6 col-12">
        <div class="dash-widget dash4">
          <div class="dash-widgetimg">
            <span><img src="assets/img/balance-sheet.png" style="width: 50%" alt="img" /></span>
          </div>
          <div class="dash-widgetcontent">
            <h5>Rp. <span class="counters-rupiah" data-count="{{ $totalSewa }}"></span></h5>
            <h6>Total Sewa</h6>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6 col-12">
        <div class="dash-widget dash3">
          <div class="dash-widgetimg">
            <span><img src="assets/img/money-send-svgrepo-com.svg" alt="img" style="width: 50%" /></span>
          </div>
          <div class="dash-widgetcontent">
            <h5>Rp. <span class="counters-rupiah" data-count="{{ $totalOperasional }}"></span></h5>
            <h6>Total Operasional</h6>
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
                    <h4>Kas Pusat</h4>
                </div>
                <div class="page-btn">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addkas" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Kas</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table datanew">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Akun</th>
                    <th>Keterangan</th>
                    <th>Kuantitas</th>
                    <th>Harga Satuan</th>
                    <th>Harga Total</th>
                    <th>Lokasi</th>
                    <th>Tanggal</th>
                    <th>Bukti</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->akun->nama_akun ?? '-' }}</td>
                            <td>{{ $item->keterangan ?? '-' }}</td>
                            <td>{{ $item->kuantitas ?? '-' }}</td>
                            <td>{{ $item->harga_satuan ? formatRupiah($item->harga_satuan) : '-' }}</td>
                            <td>{{ $item->harga_total ? formatRupiah($item->harga_total) : '-' }}</td>
                            <td>{{ $item->lokasi->nama ?? '-' }}</td>
                            <td>{{ $item->tanggal_transaksi ? formatTanggal($item->tanggal_transaksi) : '-' }}</td>
                            <td>
                              @if ($item->bukti)
                                  <button onclick="bukti('{{ $item->bukti }}')" class="btn btn-info">Bukti</button>
                              @endif
                          </td>
                            <td>{{ $item->status ?? '-' }}</td>
                            <td class="text-center">
                              <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                  <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                              </a>
                              <ul class="dropdown-menu">
                                  <li>
                                      <a href="javascript:void(0);" onclick="getData({{ $item->id }})" data-bs-toggle="modal" data-bs-target="#editkas" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                  </li>
                                  <li>
                                      <a href="#" class="dropdown-item" href="javascript:void(0);" onclick="deleteData({{ $item->id }})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
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
<div class="modal fade" id="addkas" tabindex="-1" aria-labelledby="addkaslabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addkaslabel">Tambah Transaksi Kas</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form action="{{ route('kas_pusat.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label for="nama" class="col-form-label">Akun</label>
              <div class="form-group">
                <select class="select2" name="akun_id" id="add_akun_id" required>
                  <option value="">Pilih Akun</option>
                  @foreach($akuns as $akun)
                    <option value="{{ $akun->id }}">{{ $akun->no_akun }} - {{ $akun->nama_akun }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="keterangan" class="col-form-label">Keterangan</label>
              <input type="text" class="form-control" name="keterangan" id="add_keterangan" required>
            </div>
            <div class="mb-3">
              <label for="kuantitas" class="col-form-label">Kuantitas</label>
              <input type="number" class="form-control" name="kuantitas" id="add_kuantitas" required>
            </div>
            <div class="mb-3">
              <label for="harga_satuan" class="col-form-label">Harga Satuan</label>
              <input type="number" class="form-control" name="harga_satuan" id="add_harga_satuan" required>
            </div>
            <div class="mb-3">
              <label for="harga_total" class="col-form-label">Harga Total</label>
              <input type="number" class="form-control" name="harga_total" id="add_harga_total" readonly required>
            </div>
            <div class="mb-3">
              <label for="lokasi_id" class="col-form-label">Lokasi</label>
              <div class="form-group">
                <select class="select2" name="lokasi_id" id="add_lokasi_id" required>
                  <option value="">Pilih Lokasi</option>
                  @foreach($lokasis as $lokasi)
                    <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="tanggal_transaksi" class="col-form-label">Tanggal Transaksi</label>
              <input type="date" class="form-control" name="tanggal_transaksi" id="add_tanggal_transaksi" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="mb-3">
              <label for="bukti" class="col-form-label">Bukti</label>
              <input type="file" class="form-control" name="bukti" id="add_bukti" required>
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
<div class="modal fade" id="editkas" tabindex="-1" aria-labelledby="editkaslabel" aria-hidden="true">
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
        $('#add_lokasi_id, #edit_lokasi_id, #add_akun_id, #edit_akun_id, #edit_status').select2()
    });
    $(document).on('input', '#add_harga_satuan, #add_kuantitas', function(){
      var harga = $('#add_harga_satuan').val() ?? 0;
      var qty = $('#add_kuantitas').val() ?? 0;
      $('#add_harga_total').val(harga * qty);
    })
    $(document).on('input', '#edit_harga_satuan, #edit_kuantitas', function(){
      var harga = $('#edit_harga_satuan').val() ?? 0;
      var qty = $('#edit_kuantitas').val() ?? 0;
      $('#edit_harga_total').val(harga * qty);
    })
    function bukti(src){
            var baseUrl = window.location.origin;
            var fullUrl = baseUrl + '/storage/' + src;
            $('#imgBukti').attr('src', fullUrl);
            $('#modalBukti').modal('show');
        }

    function getData(id){
        $.ajax({
            type: "GET",
            url: "/kas_pusat/"+id+"/edit",
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
    </script>
@endsection