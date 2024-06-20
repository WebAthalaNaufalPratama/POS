@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Inventory Greenhouse</h4>
                </div>
                <div class="page-btn">
                    <a href="{{ route('inven_greenhouse.create') }}" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Inventory</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table datanew">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Kondisi</th>
                    <th>Gallery</th>
                    <th>Jumlah</th>
                    <th>Minimal Stok</th>
                    <th class="text-center">Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->kode_produk ?? '-' }}</td>
                            <td>{{ $item->produk->nama ?? '-' }}</td>
                            <td>{{ $item->kondisi->nama ?? '-' }}</td>
                            <td>{{ $item->gallery->nama ?? '-' }}</td>
                            <td>{{ $item->jumlah ?? '-' }}</td>
                            <td>{{ $item->min_stok ?? '-' }}</td>
                            <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('inven_greenhouse.show', ['inven_greenhouse' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('inven_greenhouse.edit', ['inven_greenhouse' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item" onclick="deleteData({{ $item->id }})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
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
            url: "/inven_outlet/"+id+"/delete",
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