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
            <div class="table-responsive">
            <table class="table datanew">
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
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if ($item->jenis === 'Produk Terjual')
                                        {{ $item->produk_terjual->no_mutasigg ?? '-' }}
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
                                    @elseif ($item->jenis === 'Produk Beli')
                                        @php
                                            $komponen = $item->produkbeli->first();
                                            $produkNama = $komponen ? \App\Models\Produk::where('id', $komponen->produk_id)->value('nama') : null;
                                            $kondisiNama = $komponen ? \App\Models\Kondisi::where('id', $komponen->kondisi_id)->value('nama') : null;
                                        @endphp
                                        {{ $produkNama ?? '-' }} - {{ $kondisiNama ?? '-' }}
                                    @else
                                        @php
                                            $produkNama = \App\Models\Produk::where('id', $properties['attributes']['kode_produk'] ?? $properties['attributes']['produk_id'] ?? null)->value('nama');
                                            $kondisiNama = \App\Models\Kondisi::where('id', $properties['attributes']['kondisi'] ?? $properties['attributes']['kondisi_id'] ?? null)->value('nama');
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
                                    @elseif ($item->jenis === 'Produk Beli' && isset($properties['attributes']['pembelian_id']))
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