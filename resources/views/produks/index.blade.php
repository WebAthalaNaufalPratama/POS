@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Produk</h4>
                </div>
                <div class="page-btn">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addproduk" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Produk</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table datanew">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Tipe</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($produks as $produk)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $produk->kode }}</td>
                            <td>{{ $produk->nama }}</td>
                            <td>{{ $produk->tipe_produk }}</td>
                            <td>{{ $produk->deskripsi }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="getData({{ $produk->id }})" data-bs-toggle="modal" data-bs-target="#editproduk">Edit</a>
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
<div class="modal fade" id="addproduk" tabindex="-1" aria-labelledby="addproduklabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addproduklabel">Tambah Produk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('produks.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="add_nama">
            </div>
            <div class="mb-3">
              <label for="tipe_produk" class="col-form-label">Tipe Produk</label>
              <div class="form-group">
                <select class="select2" name="tipe_produk" id="add_tipe_produk">
                  <option>Choose Product</option>
                  <option value="1">Macbook pro</option>
                  <option value="2">Orange</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="deskripsi" class="col-form-label">Deskripsi</label>
              <textarea class="form-control" name="deskripsi" id="add_deskripsi"></textarea>
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
<div class="modal fade" id="editproduk" tabindex="-1" aria-labelledby="editproduklabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editproduklabel">Edit Produk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editForm" action="produks/0/update" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="edit_nama" value="">
            </div>
            <div class="mb-3">
              <label for="tipe_produk" class="col-form-label">Tipe Produk</label>
              <div class="form-group">
                <select class="select2" name="tipe_produk" id="edit_tipe_produk" value="">
                  <option>Choose Product</option>
                  <option value="1">Macbook pro</option>
                  <option value="2">Orange</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="deskripsi" class="col-form-label">Deskripsi</label>
              <textarea class="form-control" name="deskripsi" id="edit_deskripsi" value=""></textarea>
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
        $('#add_tipe_produk, #edit_tipe_produk').select2()
    });

    function getData(id){
        $.ajax({
            type: "GET",
            url: "/produks/"+id+"/edit",
            success: function(response) {
                $('#editForm').attr('action', 'produks/'+id+'/update');
                $('#edit_nama').val(response[0].nama)
                $('#edit_tipe_produk').val(response[0].tipe_produk).trigger('change')
                $('#edit_deskripsi').val(response[0].deskripsi)
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
            url: "/produks/"+id+"/delete",
            success: function(response) {
                toastr.error('Data berhasil dihapus', 'Success', {
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
                toastr.error('Ambil data error', 'Error', {
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