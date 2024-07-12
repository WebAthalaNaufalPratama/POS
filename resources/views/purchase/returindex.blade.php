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
                        {{-- <a href="{{ route('returbeli.create') }}" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Retur Pembelian</a> --}}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row ps-2 pe-2">
                    <div class="col-sm-2 ps-0 pe-0">
                        <input type="date" class="form-control" name="filterDateStart" id="filterDateStart" value="{{ request()->input('dateStart') }}" title="Tanggal Awal">
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <input type="date" class="form-control" name="filterDateEnd" id="filterDateEnd" value="{{ request()->input('dateEnd') }}" title="Tanggal Akhir">
                    </div>
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterGallery" name="filterGallery" class="form-control" title="Gallery">
                            <option value="">Pilih Gallery</option>
                            @foreach ($gallery as $item)
                                <option value="{{ $item->id }}" {{ $item->id == request()->input('gallery') ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('returbeli.index') }}" class="btn btn-info">Filter</a>
                        <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('returbeli.index') }}" class="btn btn-warning">Clear</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Retur</th>
                                <th>Tanggal</th>
                                <th>Suplier</th>
                                <th>Lokasi</th>
                                <th>Produk</th>
                                <th>Alasan</th>
                                <th>Jumlah</th>
                                <th>Komplain</th>
                                <th>Total Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @foreach ($dataretur as $data)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $data->no_retur }}</td>
                                <td>{{ tanggalindo($data->tgl_retur)}}</td>
                                <td>{{ $data->invoice->pembelian->supplier->nama }}</td>
                                <td>{{ $data->invoice->pembelian->lokasi->nama }}</td>
                                <td>
                                    <ul>
                                        @foreach($data->produkretur as $produkretur)
                                            <li>{{ $produkretur->produkbeli->produk->nama }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        @foreach($data->produkretur as $produkretur)
                                            <li>{{ $produkretur->alasan }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        @foreach($data->produkretur as $produkretur)
                                            <li>{{ $produkretur->jumlah }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>{{ $data->komplain }} 
                                    @if($data->komplain == "Refund")
                                        @if($data->sisa == 0)
                                           | Lunas
                                        @else
                                           | Belum Lunas
                                        @endif
                                    @endif
                                </td>
                                <td>{{ formatRupiah($data->subtotal)}}</td>
                                <td class="text-center">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        {{-- <li>
                                            @php
                                                $invoiceExists = $datainv->contains('pembelian_id', $datapo->id);
                                            @endphp
                                
                                            @if ($invoiceExists)
                                                <a href="{{ route('invoice.edit',['datapo' => $datapo->id, 'type' => 'pembelian', 'id' => $datainv->id]) }}" class="dropdown-item">
                                                    <img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Pembayaran Invoice
                                                </a>
                                            @else
                                            <a href="{{ route('invoicebiasa.create', ['type' => 'pembelian', 'datapo' => $datapo->id]) }}" class="dropdown-item"><img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> Create Invoice
                                                </a>
                                            @endif
                                        </li> --}}
                                        
                                        <li>
                                            <a href="{{ route('returbeli.show', ['retur_id' => $data->id]) }}" class="dropdown-item"><img src="/assets/img/icons/transcation.svg" class="me-2" alt="img"> @if($data->komplain == "Refund") Input Refund @else Detail Retur @endif</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('invoice.show', ['datapo' => $data->invoice->pembelian_id, 'type'=>"pembelian", 'id' => $data->invoice->id]) }}" class="dropdown-item"><img src="/assets/img/icons/eye1.svg" class="me-2" alt="img">Detail Invoice</a>
                                        </li>
                                        {{-- <li>
                                            <a href="#" class="dropdown-item" onclick="deleteData({{ $datapo->id }})"><img src="/assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
                                        </li> --}}
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
        $(document).ready(function(){
        $('#filterSupplier, #filterGallery, #filterStatus').select2();
    });
    $('[id^=filterBtn]').click(function(){
        var baseUrl = $(this).data('base-url');
        var urlString = baseUrl;
        var first = true;
        var symbol = '';

        var gallery = $('#filterGallery').val();
        if (gallery) {
            var filtergallery = 'gallery=' + gallery;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filtergallery;
        }

        var dateStart = $('#filterDateStart').val();
        if (dateStart) {
            var filterDateStart = 'dateStart=' + dateStart;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterDateStart;
        }

        var dateEnd = $('#filterDateEnd').val();
        if (dateEnd) {
            var filterDateEnd = 'dateEnd=' + dateEnd;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterDateEnd;
        }

        window.location.href = urlString;
    });
    $('[id^=clearBtn]').click(function(){
        var baseUrl = $(this).data('base-url');
        var url = window.location.href;
        if(url.indexOf('?') !== -1){
            window.location.href = baseUrl;
        }
        return 0;
    });
    </script>
@endsection