@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Kontrak</h4>
                </div>
                <div class="page-btn">
                    <a href="{{ route('kontrak.create') }}" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Kontrak</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table datanew">
                <thead>
                <tr>
                    <th>No</th>
                    <th>No Kontrak</th>
                    <th>Pelanggan</th>
                    <th>PIC</th>
                    <th>Handphone</th>
                    <th>Masa Kontrak</th>
                    <th>Total Biaya</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($kontraks as $kontrak)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $kontrak->no_kontrak }}</td>
                            <td>{{ $kontrak->customer->nama }}</td>
                            <td>{{ $kontrak->pic }}</td>
                            <td>{{ $kontrak->handphone }}</td>
                            <td>{{ $kontrak->masa_kontrak }} bulan</td>
                            <td>{{ $kontrak->total_harga }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('kontrak.edit', ['kontrak' => $kontrak->id]) }}">Edit</a>
                                        <a class="dropdown-item" href="javascript:void(0);"onclick="deleteData({{ $kontrak->id }})">Delete</a>
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
            url: "/kontrak/"+id+"/delete",
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