@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Ongkir</h4>
                </div>
                <div class="page-btn">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addongkir" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Ongkir</a>
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
                    <th>Lokasi</th>
                    <th>Biaya</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($ongkirs as $ongkir)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $ongkir->nama }}</td>
                            <td>{{ $ongkir->lokasi->nama ?? '-' }}</td>
                            <td>{{ $ongkir->biaya }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="getData({{ $ongkir->id }})" data-bs-toggle="modal" data-bs-target="#editongkir">Edit</a>
                                        <a class="dropdown-item" href="javascript:void(0);"onclick="deleteData({{ $ongkir->id }})">Delete</a>
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
<div class="modal fade" id="addongkir" tabindex="-1" aria-labelledby="addongkirlabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addongkirlabel">Tambah ongkir</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('ongkir.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="add_nama">
            </div>
            <div class="mb-3">
              <label for="lokasi_id" class="col-form-label">Lokasi</label>
              <div class="form-group">
                <select class="select2" name="lokasi_id" id="add_lokasi">
                  <option>Pilih Lokasi</option>
                  @foreach ($lokasis as $lokasi)
                    <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3">
                <label for="biaya" class="col-form-label">Biaya</label>
                <input type="text" class="form-control" name="biaya" id="add_biaya">
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
<div class="modal fade" id="editongkir" tabindex="-1" aria-labelledby="editongkirlabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editongkirlabel">Edit ongkir</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editForm" action="ongkir/0/update" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="edit_nama" value="">
            </div>
            <div class="mb-3">
                <label for="lokasi_id" class="col-form-label">Lokasi</label>
                <div class="form-group">
                  <select class="select2" name="lokasi_id" id="edit_lokasi">
                    <option>Pilih Lokasi</option>
                    @foreach ($lokasis as $lokasi)
                      <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            <div class="mb-3">
                <label for="biaya" class="col-form-label">Biaya</label>
                <input type="text" class="form-control" name="biaya" id="edit_biaya" value="">
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
        $('#add_lokasi, #edit_lokasi').select2()
    });

    function getData(id){
        $.ajax({
            type: "GET",
            url: "/ongkir/"+id+"/edit",
            success: function(response) {
                $('#editForm').attr('action', 'ongkir/'+id+'/update');
                $('#edit_nama').val(response.nama)
                $('#edit_lokasi').val(response.lokasi_id).trigger('change')
                $('#edit_biaya').val(response.biaya)
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
            url: "/ongkir/"+id+"/delete",
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