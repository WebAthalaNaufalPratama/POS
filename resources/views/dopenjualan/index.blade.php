@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Produk Penjualan</h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No DO</th>
                                <th>No Invoice</th>
                                <th>Customer</th>
                                <th>Tanggal Kirim</th>
                                <th>Status</th>
                                <th>Driver</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dopenjualans as $dopenjualan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $dopenjualan->no_do }}</td>
                                <td>{{ $dopenjualan->no_referensi }}</td>
                                <td>{{ $dopenjualan->customer->nama }}</td>
                                <td>{{ $dopenjualan->tanggal_kirim }}</td>
                                <td>{{ $dopenjualan->status }}</td>
                                <td>{{ $dopenjualan->data_driver->nama }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('dopenjualan.show', ['dopenjualan' => $dopenjualan->id]) }}">Perangkai</a>
                                            <!-- <a class="dropdown-item" href="javascript:void(0);" onclick="deleteData({{ $dopenjualan->id }})">Delete</a> -->
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
    function deleteData(id) {
        $.ajax({
            type: "GET",
            url: "/penjualan/" + id + "/delete",
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