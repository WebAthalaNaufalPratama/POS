@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Akun</h4>
                </div>
                <div class="page-btn">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addAkun" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Akun</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table datanew">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor akun</th>
                    <th>Nama Akun</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($akuns as $akun)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $akun->no_akun }}</td>
                            <td>{{ $akun->nama_akun}}</td>
                            <td class="text-center">
                              <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                  <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                              </a>
                              <ul class="dropdown-menu">
                                  <li>
                                      <a href="javascript:void(0);" onclick="getData({{ $akun->id }})" data-bs-toggle="modal" data-bs-target="#editAkun" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                  </li>
                                  <li>
                                      <a href="#" class="dropdown-item" href="javascript:void(0);" onclick="deleteData({{ $akun->id }})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
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
<div class="modal fade" id="addAkun" tabindex="-1" aria-labelledby="addAkunlabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addAkunlabel">Tambah akun</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form action="{{ route('akun.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="no_akun" class="col-form-label">Nomor akun</label>
              <input type="text" class="form-control" name="no_akun" id="add_no_akun" required>
            </div>
            <div class="mb-3">
              <label for="nama_akun" class="col-form-label">Nama Akun</label>
              <input type="text" class="form-control" name="nama_akun" id="add_nama_akun" required>
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
<div class="modal fade" id="editAkun" tabindex="-1" aria-labelledby="editAkunLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editAkunLabel">Edit akun</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="editForm" action="akun/0/update" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label for="no_akun" class="col-form-label">Nomor akun</label>
              <input type="text" class="form-control" name="no_akun" id="edit_no_akun" required>
            </div>
            <div class="mb-3">
              <label for="nama_akun" class="col-form-label">Nama Akun</label>
              <input type="text" class="form-control" name="nama_akun" id="edit_nama_akun" required>
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
    });
    $(document).on('input', '#add_no_akun, #edit_no_akun', function() {
        let input = $(this);
        let value = input.val();
        
        if (!isNumeric(value)) {
        value = value.replace(/[^\d]/g, "");
        }

        input.val(value);
    });

    function getData(id){
        $.ajax({
            type: "GET",
            url: "/akun/"+id+"/edit",
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                // console.log(response)
                $('#editForm').attr('action', 'akun/'+id+'/update');
                $('#edit_bank').val(response.bank)
                $('#edit_no_akun').val(response.no_akun)
                $('#edit_nama_akun').val(response.nama_akun)
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
                    url: "/akun/"+id+"/delete",
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