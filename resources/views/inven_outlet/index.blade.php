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
                      <div class="col-auto">
                        {{-- @if(in_array('produks.pdf', $thisUserPermissions))
                        <button class="btn btn-outline-danger" style="height: 2.5rem; padding: 0.5rem 1rem; font-size: 1rem;" onclick="pdf()">
                          <img src="/assets/img/icons/pdf.svg" alt="PDF" style="height: 1rem;" /> PDF
                        </button>
                        @endif
                        @if(in_array('produks.excel', $thisUserPermissions))
                        <button class="btn btn-outline-success" style="height: 2.5rem; padding: 0.5rem 1rem; font-size: 1rem;" onclick="excel()">
                          <img src="/assets/img/icons/excel.svg" alt="EXCEL" style="height: 1rem;" /> EXCEL
                        </button>
                        @endif --}}
                      </div>
                    </div>
                </div>
                <table class="table" id="inventory" style="width: 100%">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Kode Produk</th>
                        <th>Tipe Produk</th>
                        @if(!Auth::user()->hasRole('KasirOutlet'))
                        <th>Outlet</th>
                        @endif
                        <th>Minimal Stok</th>
                        <th>Jumlah</th>
                        @if(Auth::user()->hasRole('KasirOutlet'))
                        <th class="text-center">Aksi</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody id="dynamic_field">
                        {{-- @foreach ($data as $item)
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
                            @if(!Auth::user()->hasRole('KasirOutlet'))
                            <th></th>
                            @endif
                            <th></th>
                            <th></th>
                            @if(Auth::user()->hasRole('KasirOutlet'))
                            <th></th>
                            @endif
                        </tr>
                    </tfoot>
                </table>
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

                @unlessrole('KasirOutlet')
                <!-- Select Lokasi -->
                <div class="mb-3" >
                    <label for="lokasiChecklist" class="form-label me-3">Pilih Lokasi</label>
                    <a href="javascript:void(0);" id="checkAllLokasi">
                      <span class="text-primary">Select All</span>
                    </a>
                    <a href="javascript:void(0);" class="d-none" id="uncheckAllLokasi">
                      <span class="text-danger">Deselect All</span>
                    </a>
                    <div id="lokasiChecklist" class="row" style="max-height: 300px; overflow-y: auto;border: 1px solid #ddd;">
                      @foreach ($outlets as $item)
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
                @endunlessrole
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" id="clearBtnInventory">Clear</button>
                <button type="button" class="btn btn-primary" id="filterBtnInventory">Filter</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            $('select').select2()

            // Start Datatable Inventory
            const columns = [
                    { data: 'no', name: 'no', orderable: false },
                    { data: 'produk.nama', name: 'produk.nama', orderable: false },
                    { data: 'kode_produk', name: 'kode_produk' },
                    { data: 'tipe_produk', name: 'tipe_produk', orderable: false },
                    @if(!Auth::user()->hasRole('KasirOutlet'))
                    { data: 'outlet.nama', name: 'outlet.nama', orderable: false },
                    @endif
                    { data: 'min_stok', name: 'min_stok' },
                    { data: 'jumlah', name: 'jumlah' },
                    @if(Auth::user()->hasRole('KasirOutlet'))
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
                                            <a href="inven_outlet/${row.id}/show" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                        </li>
                                        <li>
                                            <a href="inven_outlet/${row.id}/edit" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                        </li>
                                    </ul>
                                </div>
                            `;
                        }
                    }
                    @endif
                ];

                let table = initDataTable('#inventory', {
                    ajaxUrl: "{{ route('inven_outlet.index') }}",
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
                    lokasi: '#lokasiChecklist',
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
                    $('#uncheckAllLokasi').addClass('d-none');
                    $('#checkAllLokasi').removeClass('d-none');
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

                $('#checkAllLokasi').on('click', function() {
                    $('#lokasiChecklist input').prop('checked', true);
                    $(this).addClass('d-none');
                    $('#uncheckAllLokasi').removeClass('d-none');
                });
                
                $('#uncheckAllLokasi').on('click', function() {
                    $('#lokasiChecklist input').prop('checked', false);
                    $(this).addClass('d-none');
                    $('#checkAllLokasi').removeClass('d-none');
                });
            // End Datatable Inventory
        })
        function updateFooterTotals(api) {
            let totalJumlah = api.ajax.json().total_jumlah;
            let totalColumn = api.columns().count();
            $(api.column(totalColumn - 2).footer()).html('<strong>Jumlah Total:</strong>');
            $(api.column(totalColumn - 1).footer()).html('<strong>' + (totalJumlah ? totalJumlah : 0) + '</strong>');
        }
    </script>
@endsection