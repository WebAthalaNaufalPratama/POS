@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Lokasi</h4>
                </div>
                <div class="page-btn">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addlokasi" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Lokasi</a>
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
                    <th>Tipe</th>
                    <th>Alamat</th>
                    <th>Pic</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($lokasi as $produk)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $produk->nama }}</td>
                            <td>{{ $produk->tipe->nama ?? '-' }}</td>
                            <td>{{ $produk->alamat }}</td>
                            <td>{{ $produk->pic }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="getData({{ $produk->id }})" data-bs-toggle="modal" data-bs-target="#editlokasi">Edit</a>
                                        <a class="dropdown-item" href="javascript:void(0);"onclick="deleteData({{ $produk->id }})">Delete</a>
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
<div class="modal fade" id="addlokasi" tabindex="-1" aria-labelledby="addlokasilabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addlokasilabel">Tambah Lokasi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('lokasi.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="add_nama">
            </div>
            <div class="mb-3">
                <label for="tipe_lokasi" class="col-form-label">Tipe Lokasi</label>
                <div class="form-group">
                  <select class="select2" name="tipe_lokasi" id="add_tipe_lokasi">
                    <option>Pilih Tipe Lokasi</option>
                    @foreach ($tipe_lokasis as $tipe_lokasi)
                      <option value="{{ $tipe_lokasi->id }}">{{ $tipe_lokasi->nama }}</option>
                    @endforeach
                  </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="alamat" class="col-form-label">Alamat</label>
                <textarea class="form-control" name="alamat" id="add_alamat"></textarea>
            </div>
            <div class="mb-3">
              <label for="pic" class="col-form-label">Pic</label>
              <input type="text" class="form-control" name="pic" id="add_pic">
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
<div class="modal fade" id="editlokasi" tabindex="-1" aria-labelledby="editlokasilabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editlokasilabel">Edit Lokasi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editForm" action="lokasi/0/update" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="edit_nama" value="">
            </div>
            <div class="mb-3">
                <label for="tipe_lokasi" class="col-form-label">Tipe Lokasi</label>
                <div class="form-group">
                  <select class="select2" name="tipe_lokasi" id="edit_tipe_lokasi">
                    <option>Pilih Tipe Lokasi</option>
                    @foreach ($tipe_lokasis as $tipe_lokasi)
                      <option value="{{ $tipe_lokasi->id }}">{{ $tipe_lokasi->nama }}</option>
                    @endforeach
                  </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="alamat" class="col-form-label">Alamat</label>
                <textarea class="form-control" name="alamat" id="edit_alamat" value=""></textarea>
            </div>
            <div class="mb-3">
              <label for="pic" class="col-form-label">Pic</label>
              <input type="text" class="form-control" name="pic" id="edit_pic" value="">
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
        $('#add_tipe_lokasi, #edit_tipe_lokasi').select2()
    });

    function getData(id){
        $.ajax({
            type: "GET",
            url: "/lokasi/"+id+"/edit",
            success: function(response) {
                $('#editForm').attr('action', 'lokasi/'+id+'/update');
                $('#edit_nama').val(response.nama)
                $('#edit_tipe_lokasi').val(response.tipe_lokasi).trigger('change')
                $('#edit_alamat').val(response.alamat)
                $('#edit_pic').val(response.pic)
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
            url: "/lokasi/"+id+"/delete",
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