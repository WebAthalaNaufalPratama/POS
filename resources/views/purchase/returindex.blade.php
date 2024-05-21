@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Retur Pembelian</h4>
                    </div>
                    <div class="page-btn">
                        <a href="{{ route('returbeli.create') }}" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Retur Pembelian</a>
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
                                <th>Lokasi</th>
                                <th>Produk</th>
                                <th>Alasan</th>
                                <th>Jumlah</th>
                                <th>Komplain</th>
                                <th>Harga</th>
                                <th>Tanggal</th>
                                <th>Suplier</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($datapos as $datapo)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $datapo->no_po }}</td>
                                <td>{{ $datapo->supplier->nama }}</td>
                                <td>{{ $datapo->tgl_kirim }}</td>
                                <td>{{ $datapo->tgl_diterima}}</td>
                                <td>{{ $datapo->no_do_suplier}}</td>
                                <td>{{ $datapo->lokasi->nama}}</td>
                                <td>{{ $datapo->status_dibuat}}</td>
                                <td>
                                @if ($datapo->invoice !== null && $datapo->invoice->sisa == 0 )
                                    LUNAS
                                @elseif($datapo->invoice == null || $datapo->invoice->sisa !== 0  )
                                    BELUM LUNAS
                                @endif
                                </td>
                                
                            
                                <td class="text-center">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            @php
                                                $invoiceExists = $datainv->contains('pembelian_id', $datapo->id);
                                            @endphp
                                
                                            @if ($invoiceExists)
                                                <a href="{{ route('invoice.edit',['datapo' => $datapo->id, 'type' => 'pembelian']) }}" class="dropdown-item">
                                                    <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Pembayaran Invoice
                                                </a>
                                            @else
                                            <a href="{{ route('invoicebiasa.create', ['type' => 'pembelian', 'datapo' => $datapo->id]) }}" class="dropdown-item"><img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Create Invoice
                                                </a>
                                            @endif
                                        </li>
                                        <li>
                                            <a href="{{ route('pembelian.show', ['datapo' => $datapo->id]) }}" class="dropdown-item"><img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                        </li>
                                        <li>
                                            <a href="" class="dropdown-item"><img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                        </li>
                                        <li>
                                            <a href="#" class="dropdown-item" onclick="deleteData({{ $datapo->id }})"><img src="/assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection