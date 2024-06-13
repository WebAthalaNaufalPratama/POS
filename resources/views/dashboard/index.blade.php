@extends('layouts.app-von')

@section('content')

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        {{ formatTanggal(now()) }}
                    </div>
                </div>
            </div>
        <select class="form-control">
        @foreach($lokasis as $lokasi)
            <option value="{{ $lokasi->id}}">{{$lokasi->nama}}</option>
        @endforeach
        </select>
        </div>
    </div>
    <div class="col-sm-6 text-right">
        
    </div>
</div>
@endsection

@section('scripts')
    <script>
    $(document).ready(function() {
        $('#add_lokasi_id, #edit_lokasi_id').select2()
    });

    function getData(id){
        $.ajax({
            type: "GET",
            url: "/aset/"+id+"/edit",
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                // console.log(response)
                $('#editForm').attr('action', 'aset/'+id+'/update');
                $('#edit_nama').val(response.nama)
                $('#edit_deskripsi').val(response.deskripsi)
                $('#edit_lokasi_id').val(response.lokasi_id).trigger('change')
                $('#edit_jumlah').val(response.jumlah)
                $('#edit_tahun_beli').val(response.tahun_beli)
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
                  url: "/aset/"+id+"/delete",
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