@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Inventory Gallery</h4>
                </div>
                <div class="page-btn">
                    <div class="d-flex">
                        <a href="javascript::void(0);" data-bs-target="#modalUbahKondisi" data-bs-toggle="modal" class="btn btn-info me-2 d-flex justify-content-center align-items-center"><img src="assets/img/icons/loop.svg" alt="img" class="me-1" /></a>
                        <a href="{{ route('inven_galeri.create') }}" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Inventory</a>

                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            {{-- <div class="row ps-2 pe-2">
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
                @if(!Auth::user()->hasRole('AdminGallery'))
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterGallery" name="filterGallery" class="form-control" title="Gallery">
                            <option value="">Pilih Gallery</option>
                            @foreach ($galleries as $item)
                                <option value="{{ $item->id }}" {{ $item->id == request()->input('gallery') ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-sm-6">
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('inven_galeri.index') }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('inven_galeri.index') }}" class="btn btn-warning">Clear</a>
                </div>
            </div> --}}
            <div class="table-responsive">
                <div class="row mb-2">
                    <div class="col-12 d-flex justify-content-between align-items-center">
                      <!-- Tombol Filter di Kiri -->
                      <div class="col-auto pe-0">
                        <a href="javascript:void(0);" class="btn btn-primary p-1 d-flex justify-content-center align-items-center" data-bs-toggle="modal" data-bs-target="#filterModalInventory">
                          <img src="{{ asset('assets/img/icons/filter.svg') }}" alt="filter">
                        </a>
                      </div>
                  
                      <!-- Tombol PDF & Excel di Kanan -->
                      {{-- <div class="col-auto">
                        @if(in_array('produks.pdf', $thisUserPermissions))
                        <button class="btn btn-outline-danger" style="height: 2.5rem; padding: 0.5rem 1rem; font-size: 1rem;" onclick="pdf()">
                          <img src="/assets/img/icons/pdf.svg" alt="PDF" style="height: 1rem;" /> PDF
                        </button>
                        @endif
                        @if(in_array('produks.excel', $thisUserPermissions))
                        <button class="btn btn-outline-success" style="height: 2.5rem; padding: 0.5rem 1rem; font-size: 1rem;" onclick="excel()">
                          <img src="/assets/img/icons/excel.svg" alt="EXCEL" style="height: 1rem;" /> EXCEL
                        </button>
                        @endif
                      </div> --}}
                    </div>
                  </div>
            <table class="table" id="inventory" style="width: 100%">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Tipe Produk</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Kondisi</th>
                    @if(!Auth::user()->hasRole('AdminGallery'))
                    <th>Gallery</th>
                    @endif
                    <th>Minimal Stok</th>
                    <th>Jumlah</th>
                    @if(Auth::user()->hasRole('AdminGallery'))
                    <th class="text-center">Aksi</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                    {{-- @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->kode_produk ?? '-' }}</td>
                            <td>{{ $item->produk->nama ?? '-' }}</td>
                            <td>{{ $item->kondisi->nama ?? '-' }}</td>
                            @if(!Auth::user()->hasRole('AdminGallery'))
                            <td>{{ $item->gallery->nama ?? '-' }}</td>
                            @endif
                            <td>{{ $item->jumlah ?? '-' }}</td>
                            <td>{{ $item->min_stok ?? '-' }}</td>
                            <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('inven_galeri.show', ['inven_galeri' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('inven_galeri.edit', ['inven_galeri' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    @endforeach --}}
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        @if(!Auth::user()->hasRole('AdminGallery'))
                        <th></th>
                        @endif
                        <th></th>
                        <th></th>
                        @if(Auth::user()->hasRole('AdminGallery'))
                        <th></th>
                        @endif
                    </tr>
                </tfoot>
            </table>
            </div>
        </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Pemakaian Sendiri</h4>
                </div>
                <div class="page-btn">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalPemakaianSendiri" class="btn btn-added d-flex justify-content-center align-items-center mt-1"><img src="assets/img/icons/plus.svg" style="filter: brightness(0) invert(1);" alt="img" class="me-1" />
                        Tambah Pemakaian
                    </a>                    
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalLog" class="btn btn-secondary d-flex justify-content-center align-items-center mt-1">
                        Log
                    </a>                    
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row ps-2 pe-2">
                <div class="col-sm-2 ps-0 pe-0">
                    <select id="filterProduk2" name="filterProduk2" class="form-control" title="Produk">
                        <option value="">Pilih Produk</option>
                        @foreach ($namaproduks as $item)
                            <option value="{{ $item->produk->id }}" {{ $item->produk->id == request()->input('produk2') ? 'selected' : '' }}>{{ $item->produk->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 ps-0 pe-0">
                    <select id="filterKondisi2" name="filterKondisi2" class="form-control" title="Kondisi">
                        <option value="">Pilih Kondisi</option>
                        @foreach ($kondisis as $item)
                            <option value="{{ $item->id }}" {{ $item->id == request()->input('kondisi2') ? 'selected' : '' }}>{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                @if(!Auth::user()->hasRole('AdminGallery'))
                    <div class="col-sm-2 ps-0 pe-0">
                        <select id="filterGallery2" name="filterGallery2" class="form-control" title="Gallery">
                            <option value="">Pilih Gallery</option>
                            @foreach ($galleries as $item)
                                <option value="{{ $item->id }}" {{ $item->id == request()->input('gallery2') ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-sm-2 ps-0 pe-0">
                    <input type="text" class="form-control" name="filterDateStart2" id="filterDateStart2" value="{{ request()->input('dateStart2') }}" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Awal Pemakaian" title="Tanggal Awal">
                </div>
                <div class="col-sm-2 ps-0 pe-0">
                    <input type="text" class="form-control" name="filterDateEnd2" id="filterDateEnd2" value="{{ request()->input('dateEnd2') }}" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Akhir Pemakaian" title="Tanggal Akhir">
                </div>
                <div class="col-sm-2">
                    <a href="javascript:void(0);" id="filterBtn2" data-base-url="{{ route('inven_galeri.index') }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn2" data-base-url="{{ route('inven_galeri.index') }}" class="btn btn-warning">Clear</a>
                </div>
            </div>
            <div class="table-responsive">
            <table class="table" id="pemakaian_sendiri" style="width: 100%">
                <thead>
                <tr>
                    <th>No</th>
                    @if(!Auth::user()->hasRole('AdminGallery'))
                    <th>Lokasi</th>
                    @endif
                    <th>Id</th>
                    <th>Tipe Produk</th>
                    <th>Nama Produk</th>
                    <th>Kondisi</th>
                    <th>Pemakai</th>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                    <th>Alasan</th>
                </tr>
                </thead>
                <tbody>
                    {{-- @foreach ($pemakaian_sendiri as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            @if(!Auth::user()->hasRole('AdminGallery'))
                            <td>{{ $item->lokasi->nama ?? '-' }}</td>
                            @endif
                            <td>{{ $item->produk->nama ?? '-' }}</td>
                            <td>{{ $item->kondisi->nama ?? '-' }}</td>
                            <td>{{ $item->karyawan->nama ?? '-' }}</td>
                            <td>{{ $item->tanggal ? formatTanggal($item->tanggal) : '-' }}</td>
                            <td>{{ $item->jumlah ?? '-' }}</td>
                            <td>{{ $item->alasan ?? '-' }}</td>
                        </tr>
                    @endforeach --}}
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        @if(!Auth::user()->hasRole('AdminGallery'))
                        <th></th>
                        @endif
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            </div>
        </div>
        </div>
    </div>
</div>

{{-- modal start --}}
<div class="modal fade" id="modalPemakaianSendiri" tabindex="-1" aria-labelledby="modalPemakaianSendirilabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalPemakaianSendirilabel">Pemakaian Sendiri</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form action="{{ route('pemakaian_sendiri.store') }}" method="POST">
            @csrf
            <label for="lokasi_id" class="col-form-label">Lokasi</label>
            <div class="row mb-2">
                <div class="col-4">
                    <select id="lokasi_id" name="lokasi_id" class="form-control" required>
                        @if (Auth::user()->roles()->value('name') == 'AdminGallery')
                        <option value="{{ Auth::user()->karyawans->lokasi_id }}">{{ Auth::user()->karyawans->lokasi->nama }}</option>
                        @else
                        <option value="">Pilih Lokasi</option>
                        @foreach ($lokasis as $lokasi)
                        <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table" style="width: 100%">
                    <thead>
                        <tr>
                           <th style="width: 5%">No</th> 
                           <th style="width: 15%">Tanggal</th> 
                           <th style="width: 25%">Produk</th> 
                           <th style="width: 10%">Jumlah</th> 
                           <th style="width: 20%">Pemakai</th> 
                           <th style="width: 20%">Alasan</th>
                           <th style="width: 5%" class="text-center"><a href="javascript:void(0);" id="add"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></a></th>
                        </tr>
                    </thead>
                    <tbody id="t_body_pemakaian">
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
      </div>
    </div>
</div>
<div class="modal fade" id="modalLog" tabindex="-1" aria-labelledby="modalLoglabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLoglabel">Log</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table" id="log" style="min-width: 100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Waktu</th>
                            <th>Pengubah</th>
                            <th>Referensi</th>
                            <th>Produk</th>
                            <th>Komponen</th>
                            <th>Kondisi</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @foreach ($mergedCollection as $key => $item)
                            <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item['Waktu'] }}</td>
                                    <td>{{ $item['Pengubah'] }}</td>
                                    <td>{{ $item['No Referensi'] }}</td>
                                    <td>({{ $item['Kode Produk Jual'] }}) {{ $item['Nama Produk Jual'] }}</td>
                                <td>({{ $item['Kode Komponen'] }}) {{ $item['Nama Komponen'] }}</td>
                                <td>{{ $item['Kondisi'] }}</td>
                                <td>{{ $item['Masuk'] }}</td>
                                <td>{{ $item['Keluar'] }}</td>
                            </tr>
                        @endforeach --}}
                    </tbody>
                </table>                
            </div>
        </div>
        <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="modalUbahKondisi" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Ubah Kondisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="form_ubah_kondisi" action="{{ route('inven_galeri.ubahKondisi') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 40%">Nama Produk</th>
                                    <th style="width: 15%">Stok</th>
                                    <th style="min-width: 50px"></th>
                                    <th style="width: 15%">Kondisi Akhir</th>
                                    <th>Jumlah</th>
                                    <th style="min-width: 50px"></th>
                                </tr>
                            </thead>
                            <tbody id="dynamic_field">
                                <tr id="row_0">
                                    <td>
                                        <select id="ubah_produk_0" name="produk_id[]" class="form-control" data-id="0" required>
                                            <option value="">Pilih Produk</option>
                                            @foreach ($produks as $produk)
                                                <option value="{{ $produk->id }}" data-stok="{{ $produk->jumlah }}" data-kondisi_id="{{ $produk->kondisi_id }}">{{ $produk->produk->nama }} - {{ $produk->kondisi->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="stok" id="stok_0" class="form-control" disabled>
                                    </td>
                                    <td class="text-center">
                                        <img src="assets/img/icons/arrow-right.svg" alt="Arrow">
                                    </td>
                                    <td>
                                        <select name="kondisi_akhir[]" id="kondisi_akhir_0" required>
                                            <option value="">Kondisi Akhir</option>
                                            @foreach ($kondisis as $kondisi)
                                            <option value="{{ $kondisi->id }}">{{ $kondisi->nama }}</option>
                                            @endforeach
                                        </select>
                                        <br><span id="reminder_kondisi_0" class="text-danger d-none">Kondisi tidak berubah</span>
                                    </td>
                                    <td>
                                        <input type="number" name="jumlah[]" id="jumlah_ubah_kondisi_0" class="form-control" required>
                                        <span id="reminder_jumlah_0" class="text-danger d-none">Jumlah melebihi stok</span>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" id="add2"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a href="javascript:void(0);" class="btn btn-secondary" data-bs-dismiss="modal" type="button">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="filterModalInventory" tabindex="-1" aria-labelledby="filterModalInvenlabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalInvenlabel">Filter Inventory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                  <img src="assets/img/icons/closes.svg" alt="">
                </button>
            </div>
            <div class="modal-body">
                <!-- Checklist Nama Produk -->
                <div class="mb-3" >
                  <label for="namaProdukChecklist" class="form-label me-3">Pilih Nama Produk</label>
                  <a href="javascript:void(0);" id="checkAllProduk">
                    <span class="text-primary">Select All</span>
                  </a>
                  <a href="javascript:void(0);" class="d-none" id="uncheckAllProduk">
                    <span class="text-danger">Deselect All</span>
                  </a>
                  <div id="namaProdukChecklist" class="row" style="max-height: 300px; overflow-y: auto;border: 1px solid #ddd;">
                    @foreach ($uniqueProduks as $produk)
                      <div class="col-lg-3 col-md-4 col-sm-6">
                          <div class="form-check">
                              <input class="form-check-input" type="checkbox" value="{{ $produk['kode_produk'] }}" id="{{ $produk['kode_produk'] }}">
                              <label class="form-check-label" for="{{ $produk['kode_produk'] }}">
                                  {{ $produk['nama_produk'] }}
                              </label>
                          </div>
                      </div>
                      @endforeach
                  </div>
                </div>

                <!-- Select Tipe Produk -->
                <div class="mb-3" >
                  <label for="tipeProdukChecklist" class="form-label me-3">Pilih Tipe Produk</label>
                  <a href="javascript:void(0);" id="checkAllTipe">
                    <span class="text-primary">Select All</span>
                  </a>
                  <a href="javascript:void(0);" class="d-none" id="uncheckAllTipe">
                    <span class="text-danger">Deselect All</span>
                  </a>
                  <div id="tipeProdukChecklist" class="row" style="max-height: 300px; overflow-y: auto;border: 1px solid #ddd;">
                    @foreach ($tipe_produks as $item)
                      <div class="col-lg-3 col-md-4 col-sm-6">
                          <div class="form-check">
                              <input class="form-check-input" type="checkbox" value="{{ $item->id }}" id="{{ $item->id }}">
                              <label class="form-check-label" for="{{ $item->id }}">
                                  {{ $item->nama }}
                              </label>
                          </div>
                      </div>
                      @endforeach
                  </div>
                </div>

                <!-- Select Kondisi -->
                <div class="mb-3" >
                  <label for="kondisiChecklist" class="form-label me-3">Pilih Kondisi</label>
                  <a href="javascript:void(0);" id="checkAllKondisi">
                    <span class="text-primary">Select All</span>
                  </a>
                  <a href="javascript:void(0);" class="d-none" id="uncheckAllKondisi">
                    <span class="text-danger">Deselect All</span>
                  </a>
                  <div id="kondisiChecklist" class="row" style="max-height: 300px; overflow-y: auto;border: 1px solid #ddd;">
                    @foreach ($kondisis as $item)
                      <div class="col-lg-3 col-md-4 col-sm-6">
                          <div class="form-check">
                              <input class="form-check-input" type="checkbox" value="{{ $item->id }}" id="{{ $item->id }}">
                              <label class="form-check-label" for="{{ $item->id }}">
                                  {{ $item->nama }}
                              </label>
                          </div>
                      </div>
                      @endforeach
                  </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" id="clearBtnInventory">Clear</button>
                <button type="button" class="btn btn-primary" id="filterBtnInventory">Filter</button>
            </div>
        </div>
    </div>
  </div>
{{-- modal end --}}
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('select').select2()
            var i = 1;
            $('#add').click(function() {
                if($('#t_body_pemakaian tr').length < 10){
                    var newRow = '<tr id="row' + i + '">'+
                            '<td>' + i + '</td>'+
                            '<td>'+
                                '<input type="date" class="form-control" name="tanggal[]" id="tanggal_' + i + '" value="{{ date('Y-m-d') }}" required>'+
                            '</td>'+
                            '<td>'+
                                '<select id="produk_inven_id_' + i + '" name="produk_inven_id[]" class="form-control" required>'+
                                    '<option value="">Pilih Produk</option>'+
                                    '@foreach ($produks as $produk)'+
                                    '<option value="{{ $produk->id }}">{{ $produk->produk->nama }} ({{ $produk->kondisi->nama }})</option>'+
                                    '@endforeach'+
                                '</select>'+
                            '</td>'+
                            '<td>'+
                                '<input type="number" class="form-control" name="jumlah[]" id="jumlah_' + i + '" required>'+
                            '</td>'+
                            '<td>'+
                                '<select id="karyawan_id_' + i + '" name="karyawan_id[]" class="form-control" required>'+
                                    '<option value="">Pilih Karyawan</option>'+
                                    '@foreach ($karyawans as $karyawan)'+
                                        '<option value="{{ $karyawan->id }}">{{ $karyawan->nama }}</option>'+
                                        '@endforeach'+
                                '</select>'+
                            '</td>'+
                            '<td>'+
                                '<textarea name="alasan[]" id="alasan_' + i + '" class="form-control" style="min-width:10rem" required></textarea>'+
                            '</td>'+
                            '<td class="text-center"><a href="javascript:void(0);" class="btn_remove" id="'+ i +'"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td>' +
                            '</tr>';
                    $('#t_body_pemakaian').append(newRow);
                    $('#produk_inven_id_' + i + ', #karyawan_id_' + i).select2();
                    i++
                }
            });
            $(document).on('click', '.btn_remove', function() {
                var button_id = $(this).attr("id");
                $('#row'+button_id+'').remove();
                multiply($('#harga_satuan_0'))
                multiply($('#jumlah_0'))
            });

            let rowCount = 1;

            $('#dynamic_field').on('click', '#add2', function() {
                // Buat baris baru dengan ID yang unik
                const newRow = `
                    <tr id="row_${rowCount}">
                        <td>
                            <select id="ubah_produk_${rowCount}" name="produk_id[]" class="form-control" data-id="${rowCount}" required>
                                <option value="">Pilih Produk</option>
                                @foreach ($produks as $produk)
                                    <option value="{{ $produk->id }}" data-stok="{{ $produk->jumlah }}" data-kondisi_id="{{ $produk->kondisi_id }}">{{ $produk->produk->nama }} - {{ $produk->kondisi->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="stok" id="stok_${rowCount}" class="form-control" disabled>
                        </td>
                        <td class="text-center">
                            <img src="assets/img/icons/arrow-right.svg" alt="Arrow">
                        </td>
                        <td>
                            <select name="kondisi_akhir[]" id="kondisi_akhir_${rowCount}">
                                <option value="">Kondisi Akhir</option>
                                @foreach ($kondisis as $kondisi)
                                <option value="{{ $kondisi->id }}">{{ $kondisi->nama }}</option>
                                @endforeach
                            </select>
                            <br><span id="reminder_kondisi_${rowCount}" class="text-danger d-none">Kondisi tidak berubah</span>
                        </td>
                        <td>
                            <input type="number" name="jumlah[]" id="jumlah_ubah_kondisi_${rowCount}" min="1" class="form-control" required>
                        </td>
                        <td>
                            <a href="javascript:void(0);" id="${rowCount}" class="remove"><img src="/assets/img/icons/delete.svg" style="color: #ff6666" alt="svg"></a>
                        </td>
                    </tr>
                `;

                $('#dynamic_field').append(newRow);
                $('select').select2()
                rowCount++;
            });
            $(document).on('click', '.remove', function() {
                var button_id = $(this).attr("id");
                $('#row_'+button_id+'').remove();
            });

            $(document).on('change', '[id^=ubah_produk_]', function() {
                let $this = $(this); // Cache $(this)
                let id = $this.data('id');
                
                if (id !== undefined) {
                    let stok = $this.find(':selected').data('stok');
                    $('#stok_' + id).val(stok); // Set stock value
                }

                // Reset and trigger the change event on #kondisi_akhir_
                $('#kondisi_akhir_' + id).val('').trigger('change');
            });

            $(document).on('change', '[id^=kondisi_akhir_]', function() {
                let element_id = $(this).attr('id'); // Cache $(this)
                let id = element_id.split('_')[2];

                let $ubahProduk = $('#ubah_produk_' + id); // Cache #ubah_produk_ selector
                let kondisi_awal_id = $ubahProduk.find(':selected').data('kondisi_id');
                let kondisi_akhir_id = $(this).val();
                
                // Toggle the reminder based on condition
                if (kondisi_awal_id && kondisi_akhir_id && kondisi_awal_id == kondisi_akhir_id) {
                    $('#reminder_kondisi_' + id).removeClass('d-none');
                } else {
                    $('#reminder_kondisi_' + id).addClass('d-none');
                }
            });

            $(document).on('input', '[id^=jumlah_ubah_kondisi_]', function() {
                let element_id = $(this).attr('id');
                let id = element_id.split('_')[3];

                let stok = $('#stok_' + id).val();
                let jumlah_perubahan = $(this).val();
                
                // Toggle the reminder based on quantity
                if (stok && jumlah_perubahan && parseInt(stok) < parseInt(jumlah_perubahan)) {
                    $('#reminder_jumlah_' + id).removeClass('d-none');
                } else {
                    $('#reminder_jumlah_' + id).addClass('d-none');
                }
            });

            $(document).on('submit', '#form_ubah_kondisi', function(e) {
                let hasUnchangedCondition = false;
                let hasQuantityError = false;

                $('#dynamic_field tr').each(function() {
                    let rowId = $(this).attr('id').split('_')[1];
                    let kondisiAwal = $('#ubah_produk_' + rowId).find(':selected').data('kondisi_id');
                    let kondisiAkhir = $('#kondisi_akhir_' + rowId).val();
                    let stok = $('#stok_' + rowId).val();
                    let jumlah = $('#jumlah_ubah_kondisi_' + rowId).val();
                    
                    // Check if condition is unchanged
                    if (kondisiAwal && kondisiAkhir && kondisiAwal == kondisiAkhir) {
                        $('#reminder_kondisi_' + rowId).removeClass('d-none');
                        hasUnchangedCondition = true;
                    } else {
                        $('#reminder_kondisi_' + rowId).addClass('d-none');
                    }

                    // Check if the quantity exceeds the available stock
                    if (stok && jumlah && parseInt(stok) < parseInt(jumlah)) {
                        $('#reminder_jumlah_' + rowId).removeClass('d-none');
                        hasQuantityError = true;
                    } else {
                        $('#reminder_jumlah_' + rowId).addClass('d-none');
                    }
                });
                
                // If any unchanged conditions or quantity errors are found, prevent form submission
                if (hasUnchangedCondition || hasQuantityError) {
                    e.preventDefault();
                    
                    // Display warning message for unchanged conditions or quantity errors
                    if (hasUnchangedCondition) {
                        toastr.warning('Ubah kondisi yang masih sama', 'Warning', {
                            closeButton: true,
                            tapToDismiss: false,
                            rtl: false,
                            progressBar: true
                        });
                    }
                    
                    if (hasQuantityError) {
                        toastr.warning('Jumlah melebihi stok yang tersedia', 'Warning', {
                            closeButton: true,
                            tapToDismiss: false,
                            rtl: false,
                            progressBar: true
                        });
                    }
                }
            });

            // Start Datatable Inventory
                const columns = [
                    { data: 'no', name: 'no', orderable: false },
                    { data: 'tipe_produk', name: 'tipe_produk', orderable: false },
                    { data: 'kode_produk', name: 'kode_produk' },
                    { data: 'produk.nama', name: 'produk.nama', orderable: false },
                    { data: 'kondisi.nama', name: 'kondisi.nama', orderable: false },
                    @if(!Auth::user()->hasRole('AdminGallery'))
                    { data: 'gallery.nama', name: 'gallery.nama', orderable: false },
                    @endif
                    { data: 'min_stok', name: 'min_stok' },
                    { data: 'jumlah', name: 'jumlah' },
                    @if(Auth::user()->hasRole('AdminGallery'))
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="text-center">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="inven_galeri/${row.id}/show" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                        </li>
                                        <li>
                                            <a href="inven_galeri/${row.id}/edit" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                        </li>
                                    </ul>
                                </div>
                            `;
                        }
                    }
                    @endif
                ];

                let table = initDataTable('#inventory', {
                    ajaxUrl: "{{ route('inven_galeri.index') }}",
                    columns: columns,
                    order: [[2, 'asc']],
                    searching: true,
                    lengthChange: true,
                    pageLength: 5,
                    drawCallback: function(settings) {
                        let api = this.api();
                        updateFooterTotals(api);
                    }
                }, {
                    produk: '#namaProdukChecklist',
                    tipe_produk: '#tipeProdukChecklist',
                    kondisi: '#kondisiChecklist',
                }, 'inventory'); 

                const handleSearch = debounce(function() {
                    table.ajax.reload();
                }, 5000); // Adjust the debounce delay as needed

                $('#filterBtnInventory').on('click', function() {
                    table.ajax.reload();
                    $('#filterModalInventory').modal('hide');
                });

                $('#clearBtnInventory').on('click', function() {
                    $('#filterModalInventory input[type="checkbox"]').prop('checked', false);
                    table.ajax.reload();
                    $('#uncheckAllProduk').addClass('d-none');
                    $('#checkAllProduk').removeClass('d-none');
                    $('#uncheckAllTipe').addClass('d-none');
                    $('#checkAllTipe').removeClass('d-none');
                    $('#uncheckAllKondisi').addClass('d-none');
                    $('#checkAllKondisi').removeClass('d-none');
                });

                $('#checkAllProduk').on('click', function() {
                    $('#namaProdukChecklist input').prop('checked', true);
                    $(this).addClass('d-none');
                    $('#uncheckAllProduk').removeClass('d-none');
                });
                
                $('#uncheckAllProduk').on('click', function() {
                    $('#namaProdukChecklist input').prop('checked', false);
                    $(this).addClass('d-none');
                    $('#checkAllProduk').removeClass('d-none');
                });

                $('#checkAllTipe').on('click', function() {
                    $('#tipeProdukChecklist input').prop('checked', true);
                    $(this).addClass('d-none');
                    $('#uncheckAllTipe').removeClass('d-none');
                });
                
                $('#uncheckAllTipe').on('click', function() {
                    $('#tipeProdukChecklist input').prop('checked', false);
                    $(this).addClass('d-none');
                    $('#checkAllTipe').removeClass('d-none');
                });

                $('#checkAllKondisi').on('click', function() {
                    $('#kondisiChecklist input').prop('checked', true);
                    $(this).addClass('d-none');
                    $('#uncheckAllKondisi').removeClass('d-none');
                });
                
                $('#uncheckAllKondisi').on('click', function() {
                    $('#kondisiChecklist input').prop('checked', false);
                    $(this).addClass('d-none');
                    $('#checkAllKondisi').removeClass('d-none');
                });
            // End Datatable Inventory

            // Start Datatable Pemakaian Sendiri
                const columns2 = [
                    { data: 'no', name: 'no', orderable: false },
                    { data: 'id', name: 'id', visible: false },
                    @if(!Auth::user()->hasRole('AdminGallery'))
                    { data: 'nama_gallery', name: 'nama_gallery', orderable: false },
                    @endif
                    { data: 'tipe_produk', name: 'tipe_produk', orderable: false },
                    { data: 'nama_produk', name: 'nama_produk', orderable: false },
                    { data: 'nama_kondisi', name: 'nama_kondisi', orderable: false },
                    { data: 'nama_karyawan', name: 'nama_karyawan', orderable: false },
                    { data: 'tanggal', name: 'tanggal', orderable: false },
                    { data: 'jumlah', name: 'jumlah' },
                    { data: 'alasan', name: 'alasan' },
                ];

                let table2 = initDataTable('#pemakaian_sendiri', {
                    ajaxUrl: "{{ route('inven_galeri.index') }}",
                    columns: columns2,
                    order: [[1, 'asc']],
                    searching: true,
                    lengthChange: true,
                    pageLength: 5,
                    drawCallback: function(settings) {
                        let api = this.api();
                        updateFooterTotals2(api);
                    }
                }, {
                    produk2: '#filterProduk2',
                    kondisi2: '#filterKondisi2',
                    gallery2: '#filterGallery2',
                    dateStart2: '#filterDateStart2',
                    dateEnd2: '#filterDateEnd2'
                }, 'pemakaian_sendiri'); 

                const handleSearch2 = debounce(function() {
                    table2.ajax.reload();
                }, 5000); // Adjust the debounce delay as needed

                // Event listeners for search filters
                $('#filterProduk2, #filterKondisi2, #filterGallery2, #filterDateStart2, #filterDateEnd2').on('input', handleSearch2);

                $('#filterBtn2').on('click', function() {
                    table2.ajax.reload();
                });

                $('#clearBtn2').on('click', function() {
                    $('#filterProduk2').val('').trigger('change');
                    $('#filterKondisi2').val('').trigger('change');
                    $('#filterGallery2').val('').trigger('change');
                    $('#filterDateStart2').val('');
                    $('#filterDateEnd2').val('');
                    table2.ajax.reload();
                });
            // End Datatable Pemakaian Sendiri

            // Start Datatable Log
                const columns3 = [
                    { data: 'no', name: 'no', orderable: false },
                    { data: 'Waktu', name: 'Waktu' },
                    { data: 'Pengubah', name: 'Pengubah', orderable: false },
                    { data: 'No Referensi', name: 'No Referensi', orderable: false },
                    { data: 'Nama Produk Jual', name: 'Nama Produk Jual', orderable: false },
                    { data: 'Nama Komponen', name: 'Nama Komponen', orderable: false },
                    { data: 'Kondisi', name: 'Kondisi' },
                    { data: 'Masuk', name: 'Masuk' },
                    { data: 'Keluar', name: 'Keluar' },
                ];

                let table3 = initDataTable('#log', {
                    ajaxUrl: "{{ route('inven_galeri.index') }}",
                    columns: columns3,
                    order: [[1, 'asc']],
                    searching: true,
                    lengthChange: true,
                    pageLength: 5
                }, {
                }, 'log'); 
            // End Datatable Log

            function updateFooterTotals(api) {
                let totalJumlah = api.ajax.json().total_jumlah;

                $(api.column(5).footer()).html('<strong>Jumlah Total:</strong>');
                $(api.column(6).footer()).html('<strong>' + (totalJumlah ? totalJumlah : 0) + '</strong>');
            }
            function updateFooterTotals2(api) {
                let totalJumlah = api.ajax.json().total_jumlah;

                $(api.column(6).footer()).html('<strong>Jumlah Total:</strong>');
                $(api.column(7).footer()).html('<strong>' + (totalJumlah ? totalJumlah : 0) + '</strong>');
            }
        });   
    </script>
@endsection