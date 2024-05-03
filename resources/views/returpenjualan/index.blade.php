@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Retur Penjualan</h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Retur</th>
                                <th>No Invoice</th>
                                <th>No DO</th>
                                <th>Customer</th>
                                <th>Lokasi</th>
                                <th>Supplier</th>
                                <th>Tanggal Retur</th>
                                <th>Komplain</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($returs as $retur)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $retur->no_retur }}</td>
                                <td>{{ $retur->no_invoice }}</td>
                                <td>@if($retur->komplain == 'retur')
                                    {{ $retur->no_do }}
                                    @else
                                    Bukan Retur
                                    @endif
                                </td>
                                <td>{{ $retur->customer->nama }}</td>
                                <td>{{ $retur->lokasi->nama }}</td>
                                <td>{{ $retur->supplier->nama }}</td>
                                <td>{{ $retur->tanggal_retur }}</td>
                                <td>{{ $retur->komplain }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('returpenjualan.show', ['returpenjualan' => $retur->id]) }}">Show</a>
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