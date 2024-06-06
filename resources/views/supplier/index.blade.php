@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Supplier</h4>
                </div>
                <div class="page-btn">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addsupplier" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Supplier</a>
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
                    <th>PIC</th>
                    <th>Tipe Supplier</th>
                    <th>Handphone</th>
                    <th>Alamat</th>
                    <th>Tanggal Bergabung</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($suppliers as $supplier)
                        <tr>
                            <td>{{ $loop->iteration ?? '-' }}</td>
                            <td>{{ $supplier->nama ?? '-' }}</td>
                            <td>{{ $supplier->pic ?? '-' }}</td>
                            <td>{{ $supplier->tipe_supplier ?? '-' }}</td>
                            <td>{{ $supplier->handphone ?? '-' }}</td>
                            <td>{{ $supplier->alamat ?? '-' }}</td>
                            <td>{{ $supplier->tanggal_bergabung ? formatTanggal($supplier->tanggal_bergabung) : '-' }}</td>
                            <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="javascript:void(0);" onclick="getData({{ $supplier->id }})" data-bs-toggle="modal" data-bs-target="#editsupplier" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item" href="javascript:void(0);" onclick="deleteData({{ $supplier->id }})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
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
<div class="modal fade" id="addsupplier" tabindex="-1" aria-labelledby="addsupplierlabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addsupplierlabel">Tambah Supplier</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form action="{{ route('supplier.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="add_nama" required>
            </div>
            <div class="mb-3">
              <label for="pic" class="col-form-label">PIC</label>
              <input type="text" class="form-control" name="pic" id="add_pic" required>
            </div>
            <div class="mb-3">
                <label for="tipe_supplier" class="col-form-label">Tipe Supplier</label>
                <div class="form-group">
                    <select class="select2" name="tipe_supplier" id="add_tipe_supplier" value="" required>
                        <option value="">Pilih Tipe</option>
                        <option value="tradisional">Tradisional</option>
                        <option value="inden">Inden</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
              <label for="handphone" class="col-form-label">Handphone</label>
              <input type="number" class="form-control hide-arrow " name="handphone" id="add_handphone" required>
            </div>
            <div class="mb-3">
                <label for="alamat" class="col-form-label">Alamat</label>
                <textarea class="form-control" name="alamat" id="add_alamat" required></textarea>
            </div>
            <div class="mb-3">
              <label for="tanggal_bergabung" class="col-form-label">Tanggal Bergabung</label>
              <input type="date" class="form-control" name="tanggal_bergabung" id="add_tanggal_bergabung" value="{{ date('Y-m-d') }}" required>
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
<div class="modal fade" id="editsupplier" tabindex="-1" aria-labelledby="editproduklabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editproduklabel">Edit Supplier</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="editForm" action="supplier/0/update" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <label for="nama" class="col-form-label">Nama</label>
                <input type="text" class="form-control" name="nama" id="edit_nama" required>
              </div>
              <div class="mb-3">
                <label for="pic" class="col-form-label">PIC</label>
                <input type="text" class="form-control" name="pic" id="edit_pic" required>
              </div>
              <div class="mb-3">
                  <label for="tipe_supplier" class="col-form-label">Tipe Supplier</label>
                  <div class="form-group">
                      <select class="select2" name="tipe_supplier" id="edit_tipe_supplier" value="" required>
                          <option value="">Pilih Tipe</option>
                          <option value="tradisional">Tradisional</option>
                          <option value="inden">Inden</option>
                      </select>
                  </div>
              </div>
              <div class="mb-3">
                <label for="handphone" class="col-form-label">Handphone</label>
                <input type="number" class="form-control hide-arrow" name="handphone" id="edit_handphone" required>
              </div>
              <div class="mb-3">
                  <label for="alamat" class="col-form-label">Alamat</label>
                  <textarea class="form-control" name="alamat" id="edit_alamat" required></textarea>
              </div>
              <div class="mb-3">
                <label for="tanggal_bergabung" class="col-form-label">Tanggal Bergabung</label>
                <input type="date" class="form-control" name="tanggal_bergabung" id="edit_tanggal_bergabung" value="" required>
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
        $('#add_tipe_supplier, #edit_tipe_supplier').select2()
    });

    function getData(id){
        $.ajax({
            type: "GET",
            url: "/supplier/"+id+"/edit",
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                $('#editForm').attr('action', 'supplier/'+id+'/update');
                $('#edit_nama').val(response.nama)
                $('#edit_pic').val(response.pic)
                $('#edit_tipe_supplier').val(response.tipe_supplier).trigger('change')
                $('#edit_handphone').val(response.handphone)
                $('#edit_alamat').val(response.alamat)
                $('#edit_tanggal_bergabung').val(response.tanggal_bergabung)
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
                    url: "/supplier/"+id+"/delete",
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
    </script>
@endsection