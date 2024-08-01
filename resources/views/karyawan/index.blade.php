@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Karyawan</h4>
                </div>
                <div class="page-btn">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addkaryawan" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Karyawan</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table datanew">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Lokasi</th>
                    <th>Handphone</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($karyawans as $karyawan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $karyawan->user->username ?? '-'}}</td>
                            <td>{{ $karyawan->nama ?? '-'}}</td>
                            <td>{{ $karyawan->jabatan ?? '-'}}</td>
                            <td>{{ $karyawan->lokasi->nama?? '-'}}</td>
                            <td>{{ $karyawan->handphone ?? '-'}}</td>
                            <td>{{ $karyawan->alamat ?? '-'}}</td>
                            <td class="text-center">
                              <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                  <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                              </a>
                              <ul class="dropdown-menu">
                                  <li>
                                      <a href="javascript:void(0);" onclick="getData({{ $karyawan->id }})" data-bs-toggle="modal" data-bs-target="#editkaryawan" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                  </li>
                                  <li>
                                      <a href="#" class="dropdown-item" href="javascript:void(0);" onclick="deleteData({{ $karyawan->id }})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
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
<div class="modal fade" id="addkaryawan" tabindex="-1" aria-labelledby="addkaryawanlabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addkaryawanlabel">Tambah Karyawan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form action="{{ route('karyawan.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="add_nama" oninput="validateName(this)" required>
            </div>
            <div class="mb-3">
              <label for="jabatan" class="col-form-label">Jabatan</label>
              <div class="form-group">
                <select class="select2" name="jabatan" id="add_jabatan" required>
                  <option value="">Pilih Jabatan</option>
                  @foreach($jabatans as $jabatan)
                    <option value="{{ $jabatan->nama }}">{{ $jabatan->nama }}</option>
                  @endforeach
                </select>
              </div>
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
              <label for="handphone" class="col-form-label">No Handphone</label>
              <input type="text" class="form-control hide-arrow" name="handphone" id="add_handphone" oninput="validatePhoneNumber(this)" required>
            </div>
            <div class="mb-3">
              <label for="alamat" class="col-form-label">Alamat</label>
              <textarea class="form-control" name="alamat" id="add_alamat" required></textarea>
            </div>
            <div class="mb-3">
              <label for="user_id" class="col-form-label">User</label>
              <div class="form-group">
                <select class="select2" name="user_id" id="add_user_id">
                  <option value="">Pilih user</option>
                  @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->username }}</option>
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
<div class="modal fade" id="editkaryawan" tabindex="-1" aria-labelledby="editkaryawanlabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editkaryawanlabel">Edit Karyawan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="editForm" action="karyawan/0/update" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="edit_nama" oninput="validateName(this)" required>
            </div>
            <div class="mb-3">
              <label for="jabatan" class="col-form-label">Jabatan</label>
              <div class="form-group">
                <select class="select2" name="jabatan" id="edit_jabatan" required>
                  <option value="">Pilih Jabatan</option>
                  @foreach($jabatans as $jabatan)
                    <option value="{{ $jabatan->nama }}">{{ $jabatan->nama }}</option>
                  @endforeach
                </select>
              </div>
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
              <label for="handphone" class="col-form-label"> No Handphone</label>
              <input type="number" class="form-control hide-arrow" name="handphone" id="edit_handphone" oninput="validatePhoneNumber(this)" required>
            </div>
            <div class="mb-3">
              <label for="alamat" class="col-form-label">Alamat</label>
              <textarea class="form-control" name="alamat" id="edit_alamat" required></textarea>
            </div>
            <div class="mb-3">
              <label for="user_id" class="col-form-label">User</label>
              <div class="form-group">
                <select class="select2" name="user_id" id="edit_user_id">
                  <option value="">Pilih user</option>
                  @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->username }}</option>
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
        $('#add_lokasi_id, #edit_lokasi_id, #add_jabatan, #edit_jabatan, #add_user_id, #edit_user_id').select2()
    });
    $(document).on('input', '#add_handphone, #edit_handphone', function() {
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
            url: "/karyawan/"+id+"/edit",
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                // console.log(response)
                $('#editForm').attr('action', 'karyawan/'+id+'/update');
                $('#edit_nama').val(response.nama)
                $('#edit_jabatan').val(response.jabatan).trigger('change')
                $('#edit_lokasi_id').val(response.lokasi_id).trigger('change')
                $('#edit_handphone').val(response.handphone)
                $('#edit_alamat').val(response.alamat)
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
                url: "/karyawan/"+id+"/delete",
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