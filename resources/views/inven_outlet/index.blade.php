@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Inventory Outlet</h4>
                </div>
                <div class="d-flex align-items-center">
                    <div class="page-btn">
                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#loginven" class="btn btn-secondary me-2 same-size-btn">
                        <img width="100" height="100" src="https://img.icons8.com/ios-filled/100/000000/edit-property.png" alt="edit-property" class="me-2" alt="img">Log Inventory
                        </a>
                    </div>
                    <div class="page-btn">
                        <a href="{{ route('inven_outlet.create') }}" class="btn btn-added same-size-btn">
                            <img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Inventory
                        </a>
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
                @if(!Auth::user()->hasRole('KasirOutlet'))
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterOutlet" name="filterOutlet" class="form-control" title="Outlet">
                            <option value="">Pilih Outlet</option>
                            @foreach ($outlets as $item)
                                <option value="{{ $item->id }}" {{ $item->id == request()->input('outlet') ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-sm-6">
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('inven_outlet.index') }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('inven_outlet.index') }}" class="btn btn-warning">Clear</a>
                </div>
            </div>
            <div class="table-responsive">
            <table class="table datanew" style="width: 100%">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    @if(!Auth::user()->hasRole('KasirOutlet'))
                    <th>Outlet</th>
                    @endif
                    <th>Jumlah</th>
                    <th>Minimal Stok</th>
                    <th class="text-center">Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->kode_produk ?? '-' }}</td>
                            <td>{{ $item->produk->nama ?? '-' }}</td>
                            @if(!Auth::user()->hasRole('KasirOutlet'))
                            <td>{{ $item->outlet->nama ?? '-' }}</td>
                            @endif
                            <td>{{ $item->jumlah ?? '-' }}</td>
                            <td>{{ $item->min_stok ?? '-' }}</td>
                            <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('inven_outlet.show', ['inven_outlet' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('inven_outlet.edit', ['inven_outlet' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                    </li>
                                    {{-- <li>
                                        <a href="#" class="dropdown-item" onclick="deleteData({{ $item->id }})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
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

<div class="modal fade" id="loginven" tabindex="-1" aria-labelledby="loginvenlabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editcustomerlabel">LOG INVENTORY</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
            <div class="card-body">
                <div class="table-responsive">
                <table class="table datanew">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>no referensi</th>
                        <th>Produk Jual</th>
                        <th>Subjek</th>
                        <th>Masuk</th>
                        <th>Keluar</th>
                        <th>Pengubah</th>
                        <th>Tanggal</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($riwayat as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            @php
                                    $properties = json_decode($item->properties, true);
                                    
                            @endphp
                            <td>@if($properties['attributes']['no_invoice'] != null && $properties['attributes']['no_do'] == null)
                                    {{$properties['attributes']['no_invoice']}}
                                @elseif($properties['attributes']['no_do'] != null && $properties['attributes']['no_retur'] == null)
                                    {{$properties['attributes']['no_do']}}
                                @elseif($properties['attributes']['no_retur'] != null && $properties['attributes']['no_mutasiog'] == null)
                                    {{$properties['attributes']['no_retur']}}
                                @elseif($properties['attributes']['no_retur'] != null && $properties['attributes']['no_mutasiog'] != null)
                                    {{$properties['attributes']['no_mutasiog']}}
                                @elseif($properties['attributes']['no_mutasigo'] != null)
                                    {{ $properties['attributes']['no_mutasigo'] ?? '-' }}
                                @endif
                            </td>
                            <td>{{ \App\Models\Produk_Jual::where('id', $properties['attributes']['produk_jual_id'])->value('nama') ?? '-' }}</td>
                            <td>@if($properties['attributes']['no_invoice'] != null)
                                    Penjualan
                                @elseif($properties['attributes']['no_do'] != null)
                                    Delivery Order
                                @elseif($properties['attributes']['no_retur'] != null)
                                    Retur Penjualan
                                @elseif($properties['attributes']['no_mutasigo'] != null)
                                    Mutasi Galery Outlet
                                @endif
                            <td>
                                @if($properties['attributes']['no_retur'] != null && $properties['attributes']['jenis'] == 'RETUR')
                                    {{ $properties['attributes']['jumlah'] ?? '-'  }}
                                @elseif($properties['attributes']['no_mutasigo'] != null)
                                    {{ $properties['attributes']['jumlah_diterima'] ?? '-' }}
                                @else
                                    0
                                @endif
                            </td>
                            <td>
                                @if($properties['attributes']['no_invoice'] != null)
                                    {{ $properties['attributes']['jumlah'] ?? '-' }}
                                @elseif($properties['attributes']['no_do'] != null)
                                    {{ $properties['attributes']['jumlah'] ?? '-' }}
                                @elseif($properties['attributes']['no_retur'] != null && $properties['attributes']['jenis'] == 'GANTI')
                                    {{ $properties['attributes']['jumlah'] ?? '-' }}
                                @else
                                    0
                                @endif
                            </td>
                            <td>{{ $item->causer->name ?? '-' }}</td>
                            <td>@if($properties['attributes']['no_invoice'] != null)
                                    {{ $item->created_at ?? '-' }}
                                @elseif($properties['attributes']['no_do'] != null)
                                    {{ $item->created_at ?? '-' }}
                                @elseif($properties['attributes']['no_retur'] != null && $properties['attributes']['jenis'] == 'GANTI')
                                    {{ $item->created_at ?? '-' }}
                                @elseif($properties['attributes']['no_mutasigo'] != null)
                                    {{ $item->updated_at ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </form>
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
            // console.log(Produk);
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

            var Outlet = $('#filterOutlet').val();
            if (Outlet) {
                var filterOutlet = 'outlet=' + Outlet;
                if (first == true) {
                    symbol = '?';
                    first = false;
                } else {
                    symbol = '&';
                }
                urlString += symbol;
                urlString += filterOutlet;
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
                        url: "/inven_outlet/"+id+"/delete",
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