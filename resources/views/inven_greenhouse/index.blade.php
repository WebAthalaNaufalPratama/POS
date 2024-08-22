@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Inventory Greenhouse</h4>
                </div>
                <div class="d-flex align-items-center">
                    <div class="page-btn">
                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#loginven" class="btn btn-secondary me-2 same-size-btn">
                        <img width="100" height="100" src="https://img.icons8.com/ios-filled/100/000000/edit-property.png" alt="edit-property" class="me-2" alt="img">Log Inventory
                        </a>
                    </div>
                    <div class="page-btn">
                        <a href="{{ route('inven_greenhouse.create') }}" class="btn btn-added same-size-btn"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Inventory</a>
                    </div>
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
                    <select id="filterKondisi" name="filterKondisi" class="form-control" title="Kondisi">
                        <option value="">Pilih Kondisi</option>
                        @foreach ($kondisis as $item)
                            <option value="{{ $item->id }}" {{ $item->id == request()->input('kondisi') ? 'selected' : '' }}>{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 ps-0 pe-0">
                    <select id="filterGudang" name="filterGudang" class="form-control" title="Gudang">
                        <option value="">Pilih Greenhouse</option>
                        @foreach ($greenhouses as $item)
                            <option value="{{ $item->id }}" {{ $item->id == request()->input('greenhouse') ? 'selected' : '' }}>{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('inven_greenhouse.index') }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('inven_greenhouse.index') }}" class="btn btn-warning">Clear</a>
                </div>
            </div>
            <div class="table-responsive">
            <table class="table datanew" style="width: 100%">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Kondisi</th>
                    <th>Greenhouse</th>
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
                            <td>{{ $item->kondisi->nama ?? '-' }}</td>
                            <td>{{ $item->gallery->nama ?? '-' }}</td>
                            <td>{{ $item->jumlah ?? '-' }}</td>
                            <td>{{ $item->min_stok ?? '-' }}</td>
                            <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('inven_greenhouse.show', ['inven_greenhouse' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('inven_greenhouse.edit', ['inven_greenhouse' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
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
                        <th>Produk</th>
                        <th>Subjek</th>
                        <th>Masuk</th>
                        <th>Keluar</th>
                        <th>Pengubah</th>
                        <th>Tanggal</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($riwayat as $item)
                            @php
                                $properties = json_decode($item->properties, true);
                               
                            @endphp
                            @if($item->jenis === 'Produk Terjual')
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($item->jenis === 'Produk Terjual')
                                            {{ $properties['attributes']['no_mutasigg'] ?? '-' }}
                                        @else
                                            {{ $properties['attributes']['no_mutasigg'] ?? '-' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->jenis === 'Produk Terjual')
                                            @php
                                                $komponen = $item->komponen->first();
                                                $produkNama = $komponen ? \App\Models\Produk::where('kode', $komponen->kode_produk)->value('nama') : null;
                                                $kondisiNama = $komponen ? \App\Models\Kondisi::where('id', $komponen->kondisi)->value('nama') : null;
                                            @endphp
                                            {{ $produkNama ?? '-' }} - {{ $kondisiNama ?? '-' }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $item->jenis === 'Produk Terjual' ? 'Mutasi GH / Pusat' : ($item->jenis === 'Produk Beli' ? 'Purchase Order' : '-') }}
                                    </td>
                                    <td>
                                        @if ($item->jenis === 'Produk Terjual' && isset($properties['attributes']['no_mutasigg']) && Str::startsWith($properties['attributes']['no_mutasigg'], 'MPG'))
                                            {{ $properties['attributes']['jumlah_diterima'] ?? '0' }}
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->jenis === 'Produk Terjual' && isset($properties['attributes']['no_mutasigg']) && Str::startsWith($properties['attributes']['no_mutasigg'], 'MGG'))
                                            {{ $properties['attributes']['jumlah'] ?? '0' }}
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td>{{ $item->causer->name ?? '-' }}</td>
                                    <td>{{ $item->updated_at ?? '-' }}</td>
                                </tr>
                            @elseif($item->jenis === 'Produk Beli' && $item->produkbeli->count() > 0)
                                @foreach ($item->produkbeli as $komponen)
                                    <tr>
                                        <td>{{ $loop->parent->iteration }}</td>
                                        <td>{{ $properties['attributes']['no_po'] ?? '-' }}</td>
                                        <td>
                                            @php
                                                $produkNama = \App\Models\Produk::where('id', $komponen->produk_id)->value('nama');
                                                $kondisiNama = \App\Models\Kondisi::where('id', $komponen->kondisi_id)->value('nama');
                                            @endphp
                                            {{ $produkNama ?? '-' }} - {{ $kondisiNama ?? '-' }}
                                        </td>
                                        <td>Purchase Order</td>
                                        <td>{{ $komponen->jml_diterima ?? '0' }}</td>
                                        <td>0</td>
                                        <td>{{ $item->causer->name ?? '-' }}</td>
                                        <td>{{ $item->updated_at ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            @endif
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

            var Kondisi = $('#filterKondisi').val();
            if (Kondisi) {
                var filterKondisi = 'kondisi=' + Kondisi;
                if (first == true) {
                    symbol = '?';
                    first = false;
                } else {
                    symbol = '&';
                }
                urlString += symbol;
                urlString += filterKondisi;
            }

            var Gudang = $('#filterGudang').val();
            if (Gudang) {
                var filterGudang = 'greenhouse=' + Gudang;
                if (first == true) {
                    symbol = '?';
                    first = false;
                } else {
                    symbol = '&';
                }
                urlString += symbol;
                urlString += filterGudang;
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
    </script>
@endsection