@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Rekening</h4>
                </div>
                <div class="page-btn">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addrekening" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Rekening</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table datanew">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Bank</th>
                    <th>Nomor Rekening</th>
                    <th>Nama Akun</th>
                    <th>Lokasi</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($rekenings as $rekening)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $rekening->bank }}</td>
                            <td>{{ $rekening->nomor_rekening }}</td>
                            <td>{{ $rekening->nama_akun}}</td>
                            <td>{{ $rekening->lokasi->nama }}</td>
                            <td class="text-center">
                              <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                  <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                              </a>
                              <ul class="dropdown-menu">
                                  <li>
                                      <a href="javascript:void(0);" onclick="getData({{ $rekening->id }})" data-bs-toggle="modal" data-bs-target="#editrekening" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                  </li>
                                  <li>
                                      <a href="#" class="dropdown-item" href="javascript:void(0);" onclick="deleteData({{ $rekening->id }})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
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
<div class="modal fade" id="addrekening" tabindex="-1" aria-labelledby="addrekeninglabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addrekeninglabel">Tambah Rekening</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form action="{{ route('rekening.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="bank" class="col-form-label">Bank</label>
              <input type="text" class="form-control" name="bank" id="add_bank" required>
            </div>
            <div class="mb-3">
              <label for="nomor_rekening" class="col-form-label">Nomor Rekening</label>
              <input type="number" class="form-control hide-arrow" name="nomor_rekening" id="add_nomor_rekening" required>
            </div>
            <div class="mb-3">
              <label for="nama_akun" class="col-form-label">Nama Akun</label>
              <input type="text" class="form-control" name="nama_akun" id="add_nama_akun" required>
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
        </div>
        <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
      </div>
    </div>
</div>
<div class="modal fade" id="editrekening" tabindex="-1" aria-labelledby="editrekeninglabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editrekeninglabel">Edit Rekening</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="editForm" action="rekening/0/update" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label for="bank" class="col-form-label">Bank</label>
              <input type="text" class="form-control" name="bank" id="edit_bank" required>
            </div>
            <div class="mb-3">
              <label for="nomor_rekening" class="col-form-label">Nomor Rekening</label>
              <input type="number" class="form-control hide-arrow" name="nomor_rekening" id="edit_nomor_rekening" required>
            </div>
            <div class="mb-3">
              <label for="nama_akun" class="col-form-label">Nama Akun</label>
              <input type="text" class="form-control" name="nama_akun" id="edit_nama_akun" required>
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
        $('#add_lokasi_id, #edit_lokasi_id').select2()
    });

    function getData(id){
        $.ajax({
            type: "GET",
            url: "/rekening/"+id+"/edit",
            success: function(response) {
                // console.log(response)
                $('#editForm').attr('action', 'rekening/'+id+'/update');
                $('#edit_bank').val(response.bank)
                $('#edit_nomor_rekening').val(response.nomor_rekening)
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
                url: "/rekening/"+id+"/delete",
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