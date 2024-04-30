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
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>
                                    <div class="dropdown-menu">
                                        {{-- <a class="dropdown-item" href="{{ route('do_sewa.edit', ['do_sewa' => $item->id]) }}">Edit</a> --}}
                                        <a class="dropdown-item" href="{{ route('form.show', ['form' => $item->id]) }}">Detail</a>
                                        {{-- <a class="dropdown-item" href="javascript:void(0);"onclick="deleteData({{ $item->id }})">Delete</a> --}}
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