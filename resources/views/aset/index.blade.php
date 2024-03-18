@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Aset</h4>
                </div>
                <div class="page-btn">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addaset" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Aset</a>
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
                    <th>Deskripsi</th>
                    <th>Lokasi</th>
                    <th>Jumlah</th>
                    <th>Tahun Beli</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($asets as $aset)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $aset->nama }}</td>
                            <td>{{ $aset->deskripsi }}</td>
                            <td>{{ $aset->lokasi->nama}}</td>
                            <td>{{ $aset->jumlah }}</td>
                            <td>{{ $aset->tahun_beli }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="bu tton" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="getData({{ $aset->id }})" data-bs-toggle="modal" data-bs-target="#editaset">Edit</a>
                                        <a class="dropdown-item" href="javascript:void(0);"onclick="deleteData({{ $aset->id }})">Delete</a>
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
<div class="modal fade" id="addaset" tabindex="-1" aria-labelledby="addasetlabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addasetlabel">Tambah Aset</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('aset.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="add_nama" required>
            </div>
            <div class="mb-3">
              <label for="deskripsi" class="col-form-label">Deskripsi</label>
              <input type="text" class="form-control" name="deskripsi" id="add_deskripsi" required>
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
            <div class="mb-3">
              <label for="jumlah" class="col-form-label">Jumlah</label>
              <input type="text" class="form-control" name="jumlah" id="add_jumlah" required>
            </div>
            <div class="mb-3">
              <label for="tahun_beli" class="col-form-label">Tahun Beli</label>
              <input type="date" class="form-control" name="tahun_beli" id="add_tahun_beli" required>
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
<div class="modal fade" id="editaset" tabindex="-1" aria-labelledby="editasetlabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editasetlabel">Edit Aset</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editForm" action="aset/0/update" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="edit_nama" required>
            </div>
            <div class="mb-3">
              <label for="deskripsi" class="col-form-label">Deskripsi</label>
              <input type="text" class="form-control" name="deskripsi" id="edit_deskripsi" required>
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
            <div class="mb-3">
              <label for="jumlah" class="col-form-label">Jumlah</label>
              <input type="text" class="form-control" name="jumlah" id="edit_jumlah" required>
            </div>
            <div class="mb-3">
              <label for="tahun_beli" class="col-form-label">Tahun Beli</label>
              <input type="date" class="form-control" name="tahun_beli" id="edit_tahun_beli" required>
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
        $('#add_tipe_aset, #edit_tipe_aset').select2()
    });

    function getData(id){
        $.ajax({
            type: "GET",
            url: "/aset/"+id+"/edit",
            success: function(response) {
                // console.log(response)
                $('#editForm').attr('action', 'aset/'+id+'/update');
                $('#edit_nama').val(response.nama)
                $('#edit_deskripsi').val(response.deskripsi)
                $('#edit_lokasi_id').val(response.lokasi_id).trigger('change')
                $('#edit_jumlah').val(response.jumlah)
                $('#edit_tahun_beli').val(response.tahun_beli)
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
            url: "/aset/"+id+"/delete",
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