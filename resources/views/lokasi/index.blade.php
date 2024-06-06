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
                    <th>Operasional</th>
                    <th>Alamat</th>
                    <th>Pic</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($lokasi as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->tipe->nama ?? '-' }}</td>
                            <td>{{ $item->operasional->nama ?? '-' }}</td>
                            <td>{{ $item->alamat }}</td>
                            <td>{{ $item->pic }}</td>
                            <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="javascript:void(0);" onclick="getData({{ $item->id }})" data-bs-toggle="modal" data-bs-target="#editlokasi" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
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
<div class="modal fade" id="addlokasi" tabindex="-1" aria-labelledby="addlokasilabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addlokasilabel">Tambah Lokasi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form action="{{ route('lokasi.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="add_nama" required>
            </div>
            <div class="mb-3">
                <label for="tipe_lokasi" class="col-form-label">Tipe Lokasi</label>
                <div class="form-group">
                  <select class="select2" name="tipe_lokasi" id="add_tipe_lokasi" required>
                    <option value="">Pilih Tipe Lokasi</option>
                    @foreach ($tipe_lokasis as $tipe_lokasi)
                      <option value="{{ $tipe_lokasi->id }}">{{ $tipe_lokasi->nama }}</option>
                    @endforeach
                  </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="operasional_id" class="col-form-label">Operasional</label>
                <div class="form-group">
                  <select class="select2" name="operasional_id" id="add_operasional_id" required>
                    <option value="">Pilih Tipe Lokasi</option>
                    @foreach ($operasionals as $item)
                      <option value="{{ $item->id }}">{{ $item->nama }}</option>
                    @endforeach
                  </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="alamat" class="col-form-label">Alamat</label>
                <textarea class="form-control" name="alamat" id="add_alamat" required></textarea>
            </div>
            <div class="mb-3">
              <label for="pic" class="col-form-label">Pic</label>
              <input type="text" class="form-control" name="pic" id="add_pic" required>
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
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="editForm" action="lokasi/0/update" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="edit_nama" value="" required>
            </div>
            <div class="mb-3">
                <label for="tipe_lokasi" class="col-form-label">Tipe Lokasi</label>
                <div class="form-group">
                  <select class="select2" name="tipe_lokasi" id="edit_tipe_lokasi" required>
                    <option value="">Pilih Tipe Lokasi</option>
                    @foreach ($tipe_lokasis as $tipe_lokasi)
                      <option value="{{ $tipe_lokasi->id }}">{{ $tipe_lokasi->nama }}</option>
                    @endforeach
                  </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="operasional_id" class="col-form-label">Operasional</label>
                <div class="form-group">
                  <select class="select2" name="operasional_id" id="edit_operasional_id" required>
                    <option value="">Pilih Tipe Lokasi</option>
                    @foreach ($operasionals as $item)
                      <option value="{{ $item->id }}">{{ $item->nama }}</option>
                    @endforeach
                  </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="alamat" class="col-form-label">Alamat</label>
                <textarea class="form-control" name="alamat" id="edit_alamat" value="" required></textarea>
            </div>
            <div class="mb-3">
              <label for="pic" class="col-form-label">Pic</label>
              <input type="text" class="form-control" name="pic" id="edit_pic" value="" required>
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
        $('#add_tipe_lokasi, #edit_tipe_lokasi, #add_operasional_id, #edit_operasional_id').select2()
    });

    function getData(id){
        $.ajax({
            type: "GET",
            url: "/lokasi/"+id+"/edit",
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                $('#editForm').attr('action', 'lokasi/'+id+'/update');
                $('#edit_nama').val(response.nama)
                $('#edit_tipe_lokasi').val(response.tipe_lokasi).trigger('change')
                $('#edit_operasional_id').val(response.operasional_id).trigger('change')
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
                    url: "/lokasi/"+id+"/delete",
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