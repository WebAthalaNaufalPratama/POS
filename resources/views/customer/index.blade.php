@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Customer/h4>
                </div>
                <div class="page-btn">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addcustomer" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Customer</a>
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
                    <th>Tipe Customer</th>
                    <th>Handphone</th>
                    <th>Alamat</th>
                    <th>Tanggal Lahir</th>
                    <th>Loyalty Point</th>
                    <th>Status Piutang</th>
                    <th>Tanggal Bergabung</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($customer as $custome)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $custome->nama }}</td>
                            <td>{{ $custome->tipe }}</td>
                            <td>{{ $custome->handphone}}</td>
                            <td>{{ $custome->alamat }}</td>
                            <td>{{ $custome->tanggal_lahir }}</td>
                            <td>{{ $custome->poin_loyalty }}</td>
                            <td>{{ $custome->status_piutang}}</td>
                            <td>{{ $custome->tanggal_bergabung }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="bu tton" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="getData({{ $custome->id }})" data-bs-toggle="modal" data-bs-target="#editcustomer">Edit</a>
                                        <a class="dropdown-item" href="javascript:void(0);"onclick="deleteData({{ $custome->id }})">Delete</a>
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
<div class="modal fade" id="addcustomer" tabindex="-1" aria-labelledby="addcustomerlabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addcustomerlabel">Tambah Customer</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form action="{{ route('customer.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="add_nama" required>
            </div>
            <div class="mb-3">
              <label for="tipe" class="col-form-label">Tipe Customer</label>
              <div class="form-group">
                <select class="select2" name="tipe" id="add_tipe" required>
                  <option value="">Pilih Tipe</option>
                  <option value="tradisional">tradisional</option>
                  <option value="sewa">sewa</option>
                  <option value="premium">premium</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="handphone" class="col-form-label"> No Handphone</label>
              <input type="text" class="form-control" name="handphone" id="add_handphone" required>
            </div>
            <div class="mb-3">
              <label for="alamat" class="col-form-label">Alamat</label>
              <textarea class="form-control" name="alamat" id="add_alamat" required></textarea>
            </div>
            <div class="mb-3">
              <label for="tanggal_lahir" class="col-form-label">Tanggal Lahir</label>
              <input type="date" class="form-control" name="tanggal_lahir" id="add_tanggal_lahir" required>
            </div>
            <div class="mb-3">
              <label for="tanggal_bergabung" class="col-form-label">Tanggal Gabung</label>
              <input type="date" class="form-control" name="tanggal_bergabung" id="add_tanggal_bergabung" required>
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
<div class="modal fade" id="editcustomer" tabindex="-1" aria-labelledby="editcustomerlabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editcustomerlabel">Edit Customer</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="editForm" action="customer/0/update" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="edit_nama" required>
            </div>
            <div class="mb-3">
              <label for="tipe" class="col-form-label">Tipe Customer</label>
              <div class="form-group">
                <select class="select2" name="tipe" id="edit_tipe" required>
                  <option value="">Pilih Tipe</option>
                  <option value="tradisional">tradisional</option>
                  <option value="sewa">sewa</option>
                  <option value="premium">premium</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="handphone" class="col-form-label"> No Handphone</label>
              <input type="integer" class="form-control" name="handphone" id="edit_handphone" required>
            </div>
            <div class="mb-3">
              <label for="alamat" class="col-form-label">Alamat</label>
              <textarea class="form-control" name="alamat" id="edit_alamat" required></textarea>
            </div>
            <div class="mb-3">
              <label for="tanggal_lahir" class="col-form-label">Tanggal Lahir</label>
              <input type="date" class="form-control" name="tanggal_lahir" id="edit_tanggal_lahir" required>
            </div>
            <div class="mb-3">
              <label for="tanggal_bergabung" class="col-form-label">Tanggal Gabung</label>
              <input type="date" class="form-control" name="tanggal_bergabung" id="edit_tanggal_bergabung" required>
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
        $('#add_tipe_customer, #edit_tipe_customer').select2()
    });

    function getData(id){
        $.ajax({
            type: "GET",
            url: "/customer/"+id+"/edit",
            success: function(response) {
                $('#editForm').attr('action', 'customer/'+id+'/update');
                $('#edit_nama').val(response.nama)
                $('#edit_tipe').val(response.tipe).trigger('change')
                $('#edit_handphone').val(response.handphone)
                $('#edit_alamat').val(response.alamat)
                $('#edit_tanggal_lahir').val(response.tanggal_lahir)
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
        $.ajax({
            type: "GET",
            url: "/customer/"+id+"/delete",
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