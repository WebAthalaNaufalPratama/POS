@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Produk Gift</h4>
                </div>
                <div class="page-btn">
                    <a href="{{ route('gift.create') }}" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Produk</a>
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
                    <th>Komponen</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($gifts as $gift)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $gift->nama }}</td>
                            <td>{{ $gift->tipe->nama }}</td>
                            <td>{{ $gift->harga }}</td>
                            <td>{{ $gift->harga_jual }}</td>
                            <td>{{ $gift->deskripsi }}</td>
                            <td>
                                <table class="table table-bordered">
                                    @foreach ($gift->komponen as $komponen)
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
                                        <a href="{{ route('gift.edit', ['gift' => $gift->id]) }}" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item" onclick="deleteData({{ $gift->id }})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
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
        $.ajax({
            type: "GET",
            url: "/gift/"+id+"/delete",
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