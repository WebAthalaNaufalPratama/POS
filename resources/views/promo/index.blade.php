@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Promo</h4>
                </div>
                <div class="page-btn">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addpromo" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Promo</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table datanew">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Berakhir</th>
                    <th>Ketentuan</th>
                    <th>Diskon</th>
                    <th>Lokasi</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($promos as $promo)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $promo->nama }}</td>
                            <td>{{ $promo->tanggal_mulai }}</td>
                            <td>{{ $promo->tanggal_berakhir }}</td>
                            <td>{{ $promo->ketentuan }}</td>
                            <td>{{ $promo->diskon }}</td>
                            <td>{{ $promo->lokasi->nama}}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="bu tton" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="getData({{ $promo->id }})" data-bs-toggle="modal" data-bs-target="#editpromo">Edit</a>
                                        <a class="dropdown-item" href="javascript:void(0);"onclick="deleteData({{ $promo->id }})">Delete</a>
                                    </div>
                                </div>
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
<div class="modal fade" id="addpromo" tabindex="-1" aria-labelledby="addpromolabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addpromolabel">Tambah Promo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('promo.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="add_nama" required>
            </div>
            <div class="mb-3">
              <label for="tanggal_mulai" class="col-form-label">Tanggal Mulai</label>
              <input type="date" class="form-control" name="tanggal_mulai" id="add_tanggal_mulai" required>
            </div>
            <div class="mb-3">
              <label for="tanggal_berakhir" class="col-form-label">Tanggal Berakhir</label>
              <input type="date" class="form-control" name="tanggal_berakhir" id="add_tanggal_berakhir" required>
            </div>
            <div class="mb-3">
              <label for="ketentuan" class="col-form-label">Ketentuan</label>
              <input type="text" class="form-control" name="ketentuan" id="add_ketentuan" required>
            </div>
            <div class="mb-3">
              <label for="diskon" class="col-form-label">Diskon</label>
              <input type="text" class="form-control" name="diskon" id="add_diskon" required>
            </div>
            <div class="mb-3">
              <label for="lokasi_id" class="col-form-label">Lokasi</label>
              <div class="form-group">
                <select class="select2" name="lokasi_id" id="add_lokasi_id" required>
                  <option value="">Pilih Tipe</option>
                  @foreach($lokasis as $lokasi)
                    <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
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
<div class="modal fade" id="editpromo" tabindex="-1" aria-labelledby="editpromolabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editpromolabel">Edit Promo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editForm" action="promo/0/update" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="edit_nama" required>
            </div>
            <div class="mb-3">
              <label for="tanggal_mulai" class="col-form-label">Tanggal Mulai</label>
              <input type="date" class="form-control" name="tanggal_mulai" id="edit_tanggal_mulai" required>
            </div>
            <div class="mb-3">
              <label for="tanggal_berakhir" class="col-form-label">Tanggal Berakhir</label>
              <input type="date" class="form-control" name="tanggal_berakhir" id="edit_tanggal_berakhir" required>
            </div>
            <div class="mb-3">
              <label for="ketentuan" class="col-form-label">Ketentuan</label>
              <input type="text" class="form-control" name="ketentuan" id="edit_ketentuan" required>
            </div>
            <div class="mb-3">
              <label for="diskon" class="col-form-label">Diskon</label>
              <input type="text" class="form-control" name="diskon" id="edit_diskon" required>
            </div>
            <div class="mb-3">
              <label for="lokasi_id" class="col-form-label">Lokasi</label>
              <div class="form-group">
                <select class="select2" name="lokasi_id" id="edit_lokasi_id" required>
                  <option value="">Pilih Tipe</option>
                  @foreach($lokasis as $lokasi)
                    <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
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
{{-- modal end --}}
@endsection

@section('scripts')
    <script>
    $(document).ready(function() {
        $('#add_tipe_promo, #edit_tipe_promo').select2()
    });

    function getData(id){
        $.ajax({
            type: "GET",
            url: "/promo/"+id+"/edit",
            success: function(response) {
                // console.log(response)
                $('#editForm').attr('action', 'promo/'+id+'/update');
                $('#edit_nama').val(response.nama)
                $('#edit_tanggal_mulai').val(response.tanggal_mulai)
                $('#edit_tanggal_berakhir').val(response.tanggal_berakhir)
                $('#edit_ketentuan').val(response.ketentuan)
                $('#edit_diskon').val(response.diskon)
                $('#edit_lokasi_id').val(response.lokasi_id).trigger('change')
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
        $.ajax({
            type: "GET",
            url: "/promo/"+id+"/delete",
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
    </script>
@endsection