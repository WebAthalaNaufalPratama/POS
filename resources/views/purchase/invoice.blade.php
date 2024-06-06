@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Invoice Pembelian</h4>
                    </div>
                    <div class="page-btn">
                        {{-- <a href="{{ route('pembelian.create') }}" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Invoice</a> --}}
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
                                <th>No PO</th>
                                <th>Supplier</th>
                                <th>lokasi</th>
                                <th>Tanggal Invoice</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Sisa Tagihan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                         <tbody>
                            @foreach ($invoices as $inv)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $inv->no_inv }}</td>
                                <td>{{ $inv->pembelian->no_po }}</td>
                                <td>{{ $inv->pembelian->supplier->nama }}</td>
                                <td>{{ $inv->pembelian->lokasi->nama}}</td>
                                <td>{{ $inv->tgl_inv }}</td>
                                <td>{{ formatRupiah($inv->total_tagihan) }}</td>
                                <td>
                                    @if ( $inv->sisa == 0)
                                        Lunas
                                    @else
                                        Belum Lunas
                                    @endif

                                </td>
                                <td>
                                {{ formatRupiah($inv->sisa) }}
                                </td>
                                <td class="text-center">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('returbeli.create', ['invoice' => $inv->id]) }}" class="dropdown-item"><img src="/assets/img/icons/return1.svg" class="me-2" alt="img">Retur</a>
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
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Invoice Pembelian Inden</h4>
                    </div>
                    <div class="page-btn">
                        {{-- <a href="{{ route('pembelianinden.create') }}" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Invoice</a> --}}
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
                                <th>No PO</th>
                                <th>Supplier</th>
                                <th>Bulan Inden</th>
                                <th>Tanggal Invoice</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Sisa Tagihan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                         <tbody>
                            @foreach ($invoiceinden as $inv)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $inv->no_inv }}</td>
                                <td>{{ $inv->poinden->no_po }}</td>
                                <td>{{ $inv->poinden->supplier->nama }}</td>
                                <td>{{ $inv->poinden->bulan_inden}}</td>
                                <td>{{ $inv->tgl_inv}}</td>
                                <td>{{ formatRupiah($inv->total_tagihan) }}</td>
                                <td>
                                    @if ( $inv->sisa == 0)
                                    Lunas
                                    @else
                                    Belum Lunas
                                    @endif
                                </td>
                                <td>{{ formatRupiah($inv->sisa) }}</td>
                                <td></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div
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