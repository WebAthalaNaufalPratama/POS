@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Tipe Produk</h4>
                </div>
                <div class="page-btn">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addtipeproduk" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Tipe Produk</a>
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
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($tipe_produks as $tipe_produk)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $tipe_produk->nama }}</td>
                            <td>{{ $tipe_produk->deskripsi }}</td>
                            <td>{{ $tipe_produk->kategori }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="getData({{ $tipe_produk->id }})" data-bs-toggle="modal" data-bs-target="#edittipeproduk">Edit</a>
                                        <a class="dropdown-item" href="javascript:void(0);"onclick="deleteData({{ $tipe_produk->id }})">Delete</a>
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
<div class="modal fade" id="addtipeproduk" tabindex="-1" aria-labelledby="addtipeproduklabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addtipeproduklabel">Tambah Tipe Produk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form action="{{ route('tipe_produk.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="add_nama" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="col-form-label">Deskripsi</label>
                <textarea class="form-control" name="deskripsi" id="add_deskripsi" required></textarea>
              </div>
            <div class="mb-3">
              <label for="tipe_produk" class="col-form-label">Kategori</label>
              <div class="form-group">
                <select class="select2" name="kategori" id="add_kategori" required>
                  <option value="">Pilih Kategori</option>
                  <option value="master">Master</option>
                  <option value="jual">Jual</option>
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
<div class="modal fade" id="edittipeproduk" tabindex="-1" aria-labelledby="editproduklabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editproduklabel">Edit Tipe Produk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="editForm" action="tipe_produk/0/update" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="edit_nama" value="" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="col-form-label">Deskripsi</label>
                <textarea class="form-control" name="deskripsi" id="edit_deskripsi" value="" required></textarea>
              </div>
            <div class="mb-3">
              <label for="tipe_produk" class="col-form-label">Kategori</label>
              <div class="form-group">
                <select class="select2" name="kategori" id="edit_kategori" value="" required>
                  <option value="">Pilih Kategori</option>
                  <option value="master">Master</option>
                  <option value="jual">Jual</option>
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
        $('#add_kategori, #edit_kategori').select2()
    });

    function getData(id){
        $.ajax({
            type: "GET",
            url: "/tipe_produks/"+id+"/edit",
            success: function(response) {
                $('#editForm').attr('action', 'tipe_produks/'+id+'/update');
                $('#edit_nama').val(response.nama)
                $('#edit_kategori').val(response.kategori).trigger('change')
                $('#edit_deskripsi').val(response.deskripsi)
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
            url: "/tipe_produks/"+id+"/delete",
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