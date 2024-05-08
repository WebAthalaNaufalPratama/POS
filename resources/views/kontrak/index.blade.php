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
                    <th>Rentang Tanggal</th>
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
                            <td>{{ $kontrak->masa_sewa }} bulan</td>
                            <td>{{ \Carbon\Carbon::parse($kontrak->tanggal_mulai)->format('d-m-Y')}} - {{ \Carbon\Carbon::parse($kontrak->tanggal_selesai)->format('d-m-Y') }}</td>
                            <td>{{ $kontrak->total_harga }}</td>
                            <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('do_sewa.create', ['kontrak' => $kontrak->id]) }}" class="dropdown-item"><img src="assets/img/icons/truck.svg" class="me-2" alt="img">Delivery Order</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('kembali_sewa.create', ['kontrak' => $kontrak->id]) }}" class="dropdown-item"><img src="assets/img/icons/return1.svg" class="me-2" alt="img">Kembali Sewa</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('invoice_sewa.create', ['kontrak' => $kontrak->id]) }}" class="dropdown-item"><img src="assets/img/icons/dollar-square.svg" class="me-2" alt="img">Invoice Sewa</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('kontrak.show', ['kontrak' => $kontrak->id]) }}" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('kontrak.edit', ['kontrak' => $kontrak->id]) }}" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item" onclick="deleteData({{ $kontrak->id }})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
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