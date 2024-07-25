@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Inventory Inden</h4>
                </div>
                <div class="page-btn">
                    <a href="{{ route('inven_inden.create') }}" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Inventory</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row ps-2 pe-2">
                <div class="col-sm-2 ps-0 pe-0">
                    <select id="filterProduk" name="filterProduk" class="form-control" title="Produk">
                        <option value="">Pilih Produk</option>
                        @foreach ($namaproduks as $item)
                            <option value="{{ $item->produk->kode }}" {{ $item->produk->kode == request()->input('produk') ? 'selected' : '' }}>{{ $item->produk->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 ps-0 pe-0">
                    <select id="filterSupplier" name="filterSupplier" class="form-control" title="Supplier">
                        <option value="">Pilih Supplier</option>
                        @foreach ($suppliers as $item)
                            <option value="{{ $item->id }}" {{ $item->id == request()->input('supplier') ? 'selected' : '' }}>{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 ps-0 pe-0">
                    <select id="filterPeriode" name="filterPeriode" class="form-control" title="Periode">
                        <option value="">Pilih Periode</option>
                        @foreach ($periodes as $item)
                            <option value="{{ $item->id }}" {{ $item->id == request()->input('periode') ? 'selected' : '' }}>{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('inven_inden.index') }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('inven_inden.index') }}" class="btn btn-warning">Clear</a>
                </div>
            </div>
            <div class="table-responsive">
            <table class="table datanew">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Supplier</th>
                    <th>Bulan Inden</th>
                    <th>Kode Produk Inden</th>
                    <th>Nama Produk</th>
                    <th>Jumlah</th>
                    {{-- <th>Minimal Stok</th> --}}
                    <th class="text-center">Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->supplier->nama ?? '-' }}</td>
                            <td>{{ $item->bulan_inden ?? '-' }}</td>
                            <td>{{ $item->kode_produk_inden ?? '-' }}</td>
                            <td>{{ $item->produk->nama ?? '-' }}</td>
                            <td>{{ $item->jumlah ?? '-' }}</td>
                            {{-- <td>{{ $item->min_stok ?? '-' }}</td> --}}
                            <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('inven_inden.show', ['inven_inden' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('inven_inden.edit', ['inven_inden' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                    </li>
                                    {{-- <li>
                                        <a href="#" class="dropdown-item" onclick="deleteData({{ $item->id }})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
                                        <a href="#" class="dropdown-item"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
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
            $('select[id^=filter]').select2()
        })
        $('#filterBtn').click(function(){
            var baseUrl = $(this).data('base-url');
            var urlString = baseUrl;
            var first = true;
            var symbol = '';

            var Produk = $('#filterProduk').val();
            if (Produk) {
                var filterProduk = 'produk=' + Produk;
                if (first == true) {
                    symbol = '?';
                    first = false;
                } else {
                    symbol = '&';
                }
                urlString += symbol;
                urlString += filterProduk;
            }

            var Supplier = $('#filterSupplier').val();
            if (Supplier) {
                var filterSupplier = 'supplier=' + Supplier;
                if (first == true) {
                    symbol = '?';
                    first = false;
                } else {
                    symbol = '&';
                }
                urlString += symbol;
                urlString += filterSupplier;
            }

            var Periode = $('#filterPeriode').val();
            if (Periode) {
                var filterPeriode = 'periode=' + Periode;
                if (first == true) {
                    symbol = '?';
                    first = false;
                } else {
                    symbol = '&';
                }
                urlString += symbol;
                urlString += filterPeriode;
            }

            window.location.href = urlString;
        });
        $('#clearBtn').click(function(){
            var baseUrl = $(this).data('base-url');
            var url = window.location.href;
            if(url.indexOf('?') !== -1){
                window.location.href = baseUrl;
            }
            return 0;
        });
        function deleteData(id){
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "GET",
                        url: "/inven_inden/"+id+"/delete",
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
            });
        }
    </script>
@endsection