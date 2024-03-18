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
                    <th>Komponen</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($tradisionals as $tradisional)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $tradisional->nama }}</td>
                            <td>{{ $tradisional->tipe->nama }}</td>
                            <td>{{ $tradisional->harga }}</td>
                            <td>{{ $tradisional->harga_jual }}</td>
                            <td>{{ $tradisional->deskripsi }}</td>
                            <td>
                                @foreach ($tradisional->komponen as $komponen)
                                    {{ $komponen->kode_produk }} - {{ $komponen->nama_produk ?? '-' }} x {{ $komponen->jumlah ?? '-' }} <br>
                                @endforeach
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:void(0);">Edit</a>
                                        <a class="dropdown-item" href="javascript:void(0);"onclick="deleteData({{ $tradisional->id }})">Delete</a>
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
@endsection

@section('scripts')
    <script>

    function deleteData(id){
        $.ajax({
            type: "GET",
            url: "/tradisional/"+id+"/delete",
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