@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Form Perangkai</h4>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table datanew">
                <thead>
                <tr>
                    <th>No</th>
                    <th>No From</th>
                    <th>No Kontrak</th>
                    <th>Produk</th>
                    <th>Perangkai</th>
                    <th>Tanggal Dirangkai</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->no_form ?? '-' }}</td>
                            <td>{{ $item->produk_terjual->no_sewa ?? '-' }}</td>
                            <td>{{ $item->produk_terjual->produk->nama ?? '-' }}</td>
                            <td>{{ $item->perangkai->nama ?? '-' }}</td>
                            <td>{{ $item->tanggal ?? '-' }}</td>
                            <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('form.show', ['form' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
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
            url: "/do_sewa/"+id+"/delete",
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