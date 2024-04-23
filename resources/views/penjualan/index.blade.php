@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Invoice Penjualan</h4>
                    </div>
                    <div class="page-btn">
                        <a href="{{ route('penjualan.create') }}" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Penjualan</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Invoice</th>
                                <th>Sales</th>
                                <th>Tanggal Invoice</th>
                                <th>Jatuh Tempo</th>
                                <!-- <th>Status Bayar</th> -->
                                <th>Total Tagihan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penjualans as $penjualan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $penjualan->no_invoice }}</td>
                                <td>{{ $penjualan->karyawan->nama }}</td>
                                <td>{{ $penjualan->tanggal_invoice }}</td>
                                <td>{{ $penjualan->jatuh_tempo }}</td>
                                <!-- <td>{{ $penjualan->status_bayar }}</td> -->
                                <td>{{ $penjualan->total_tagihan }}</td>
                                <td>{{ $penjualan->status }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('penjualan.payment', ['penjualan' => $penjualan->id]) }}">Pembayaran</a>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="deleteData({{ $penjualan->id }})">Delete</a>
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