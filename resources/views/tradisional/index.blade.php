@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Produk Tradisional</h4>
                </div>
                <div class="page-btn">
                    <a href="{{ route('tradisional.create') }}" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Produk</a>
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
                    <th>Tipe Produk</th>
                    <th>Harga</th>
                    <th>Harga Jual</th>
                    <th>Deskripsi</th>
                    <th class="text-center">Komponen</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($tradisionals as $tradisional)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $tradisional->nama }}</td>
                            <td>{{ $tradisional->tipe->nama }}</td>
                            <td>{{ formatRupiah($tradisional->harga) }}</td>
                            <td>{{ formatRupiah($tradisional->harga_jual) }}</td>
                            <td>{{ $tradisional->deskripsi }}</td>
                            <td>
                                <table class="table table-bordered">
                                    @foreach ($tradisional->komponen as $komponen)
                                    <tr>
                                        <td>{{ $komponen->kode_produk }}</td>
                                        <td>{{ $komponen->nama_produk }}</td>
                                        <td>{{ $komponen->jumlah }}</td>
                                    </tr>
                                    @endforeach
                                </table>
                            </td>
                            <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('tradisional.edit', ['tradisional' => $tradisional->id]) }}" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item" onclick="deleteData({{ $tradisional->id }})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
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
@endsection

@section('scripts')
    <script>

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
                    url: "/tradisional/"+id+"/delete",
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