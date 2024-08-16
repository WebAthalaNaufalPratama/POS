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
                    <th>Saldo Awal</th>
                    <th class="text-center">Aksi</th>
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
                            <td>{{ formatRupiah($rekening->saldo_awal) }}</td>
                            <td class="text-center">
                              <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                  <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                              </a>
                              <ul class="dropdown-menu">
                                  <li>
                                      <a href="javascript:void(0);" onclick="getData({{ $rekening->id }})" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
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
          <form id="addForm" action="{{ route('rekening.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="bank" class="col-form-label">Bank</label>
              <input type="text" class="form-control" name="bank" id="add_bank" required>
            </div>
            <div class="mb-3">
              <label for="nomor_rekening" class="col-form-label">Nomor Rekening</label>
              <input type="text" class="form-control hide-arrow" name="nomor_rekening" id="add_nomor_rekening" oninput="validateDigit(this, 16)" required>
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
            <div class="mb-3">
              <label for="saldo_awal" class="col-form-label">Saldo Awal</label>
              <input type="text" class="form-control" name="saldo_awal" id="add_saldo_awal">
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
              <input type="text" class="form-control hide-arrow" name="nomor_rekening" id="edit_nomor_rekening" oninput="validateDigit(this, 16)" required>
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
            <div class="mb-3">
              <label for="saldo_awal" class="col-form-label">Saldo Awal</label>
              <input type="text" class="form-control" name="saldo_awal" id="edit_saldo_awal">
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
    $(document).on('input', '#add_nomor_rekening, #edit_nomor_rekening', function() {
        let input = $(this);
        let value = input.val();
        
        if (!isNumeric(value)) {
        value = value.replace(/[^\d]/g, "");
        }

        input.val(value);
    });

    $(document).on('input', '#add_saldo_awal, #edit_saldo_awal', function() {
        let input = $(this);
        let value = input.val();
        let cursorPosition = this.selectionStart;
        
        if (!isNumeric(cleanNumber(value))) {
          value = value.replace(/[^\d]/g, "");
        }

        value = cleanNumber(value);
        let formattedValue = formatNumber(value);
        
        input.val(formattedValue);
        this.setSelectionRange(cursorPosition, cursorPosition);
      });

      $('#addForm').on('submit', function(e) {
          // Add input number cleaning for specific inputs
          let inputs = $('#addForm').find('#add_saldo_awal');
          inputs.each(function() {
              let input = $(this);
              let value = input.val();
              let cleanedValue = cleanNumber(value);

              // Set the cleaned value back to the input
              input.val(cleanedValue);
          });

          return true;
      });

      $('#editForm').on('submit', function(e) {
          // Add input number cleaning for specific inputs
          let inputs = $('#editForm').find('#edit_saldo_awal');
          inputs.each(function() {
              let input = $(this);
              let value = input.val();
              let cleanedValue = cleanNumber(value);

              // Set the cleaned value back to the input
              input.val(cleanedValue);
          });

          return true;
      });

    function getData(id){
        $.ajax({
            type: "GET",
            url: "/rekening/"+id+"/edit",
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            beforeSend: function() {
                $('#global-loader-transparent').show();
            },
            success: function(response) {
                $('#global-loader-transparent').hide();
                $('#editForm').attr('action', 'rekening/'+id+'/update');
                $('#edit_bank').val(response.bank)
                $('#edit_nomor_rekening').val(response.nomor_rekening)
                $('#edit_nama_akun').val(response.nama_akun)
                $('#edit_lokasi_id').val(response.lokasi_id).trigger('change')
                $('#edit_saldo_awal').val(formatNumber(response.saldo_awal))

                $('#editrekening').modal('show');
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