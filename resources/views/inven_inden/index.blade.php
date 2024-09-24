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
                <div class="d-flex align-items-center">
                <div class="page-btn">
                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#loginven" class="btn btn-secondary me-2 same-size-btn">
                        <img width="100" height="100" src="https://img.icons8.com/ios-filled/100/000000/edit-property.png" alt="edit-property" class="me-2" alt="img">Log Inventory
                        </a>
                    </div>
                <div class="page-btn">
                    <a href="{{ route('inven_inden.create') }}" class="btn btn-added same-size-btn"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Inventory</a>
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
                            <option value="{{ $item->bulan_inden }}" {{ $item->bulan_inden == request()->input('periode') ? 'selected' : '' }}>{{ $item->bulan_inden }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('inven_inden.index') }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('inven_inden.index') }}" class="btn btn-warning">Clear</a>
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
                    <th>Supplier</th>
                    <th>Bulan Inden</th>
                    <th>Kode Produk Inden</th>
                    <th>Nama Produk</th>
                    <th>Tipe Produk</th>
                    <th>Jumlah</th>
                    <th class="text-center">Aksi</th>
                </tr>
                </thead>
                <tbody>
                    {{-- @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->supplier->nama ?? '-' }}</td>
                            <td>{{ $item->bulan_inden ?? '-' }}</td>
                            <td>{{ $item->kode_produk_inden ?? '-' }}</td>
                            <td>{{ $item->produk->nama ?? '-' }}</td>
                            <td>{{ $item->jumlah ?? '-' }}</td>
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
                                if($item->jenis == 'Produk Mutasi') {
                                    $komponen = $item->produkmutasi->first();
                                }else{
                                    $komponenbeli = $item->produkbeli->first();
                                }
                            @endphp
                            @if($item->jenis == 'Produk Mutasi')
                                 <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $properties['attributes']['no_mutasi'] ?? '-' }}
                                    </td>
                                    <td>
                                        @php
                                            $produkNama = $komponen ? $komponen->produk->produk->nama : null;
                                        @endphp 
                                        {{ $produkNama ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $item->jenis == 'Produk Mutasi' ? 'Mutasi Inden': '-' }}
                                    </td>
                                    <td>
                                        0
                                    </td>
                                    <td>
                                        {{ $komponen->jml_dikirim ?? '0' }}
                                    </td>
                                    <td>{{ $item->causer->name ?? '-' }}</td>
                                    <td>{{ $item->updated_at ?? '-' }}</td>
                                </tr> 
                            @elseif($item->jenis === 'Produk Beli' && $item->produkbeli->count() > 0)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $properties['attributes']['no_po'] ?? '-' }}</td>
                                        <td>
                                            @php
                                                $produkNama = $komponenbeli->produk->nama;
                                            @endphp
                                            {{ $produkNama ?? '-' }}
                                        </td>
                                        <td>Purchase Order</td>
                                        <td>{{ $komponenbeli->jumlahInden ?? '0' }}</td>
                                        <td>0</td>
                                        <td>{{ $item->causer->name ?? '-' }}</td>
                                        <td>{{ $item->updated_at ?? '-' }}</td>
                                    </tr>
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

                <!-- Select Periode -->
                <div class="mb-3" >
                  <label for="periodeChecklist" class="form-label me-3">Pilih Periode</label>
                  <a href="javascript:void(0);" id="checkAllPeriode">
                    <span class="text-primary">Select All</span>
                  </a>
                  <a href="javascript:void(0);" class="d-none" id="uncheckAllPeriode">
                    <span class="text-danger">Deselect All</span>
                  </a>
                  <div id="periodeChecklist" class="row" style="max-height: 300px; overflow-y: auto;border: 1px solid #ddd;">
                    @foreach ($periodes as $item)
                      <div class="col-lg-3 col-md-4 col-sm-6">
                          <div class="form-check">
                              <input class="form-check-input" type="checkbox" value="{{ $item->bulan_inden }}" id="{{ $item->bulan_inden }}">
                              <label class="form-check-label" for="{{ $item->bulan_inden }}">
                                  {{ $item->bulan_inden }}
                              </label>
                          </div>
                      </div>
                      @endforeach
                  </div>
                </div>

                <!-- Select Supplier -->
                <div class="mb-3" >
                    <label for="supplierChecklist" class="form-label me-3">Pilih Supplier</label>
                    <a href="javascript:void(0);" id="checkAllSupplier">
                      <span class="text-primary">Select All</span>
                    </a>
                    <a href="javascript:void(0);" class="d-none" id="uncheckAllSupplier">
                      <span class="text-danger">Deselect All</span>
                    </a>
                    <div id="supplierChecklist" class="row" style="max-height: 300px; overflow-y: auto;border: 1px solid #ddd;">
                      @foreach ($suppliers as $item)
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
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            $('select').select2()

            // Start Datatable Inventory
                const columns = [
                    { data: 'no', name: 'no', orderable: false },
                    { data: 'supplier.nama', name: 'supplier.nama', orderable: false },
                    { data: 'bulan_inden', name: 'bulan_inden' },
                    { data: 'kode_produk_inden', name: 'kode_produk_inden', orderable: false },
                    { data: 'produk.nama', name: 'produk.nama', orderable: false },
                    { data: 'tipe_produk', name: 'tipe_produk', orderable: false },
                    { data: 'jumlah', name: 'jumlah' },
                    @if(Auth::user()->hasRole('Purchasing'))
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
                                            <a href="inven_inden/${row.id}/show" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                        </li>
                                        <li>
                                            <a href="inven_inden/${row.id}/edit" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                        </li>
                                    </ul>
                                </div>
                            `;
                        }
                    }
                    @endif
                ];

                let table = initDataTable('#inventory', {
                    ajaxUrl: "{{ route('inven_inden.index') }}",
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
                    periode: '#periodeChecklist',
                    supplier: '#supplierChecklist',
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
                    $('#uncheckAllSupplier').addClass('d-none');
                    $('#checkAllSupplier').removeClass('d-none');
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

                $('#checkAllPeriode').on('click', function() {
                    $('#periodeChecklist input').prop('checked', true);
                    $(this).addClass('d-none');
                    $('#uncheckAllPeriode').removeClass('d-none');
                });
                
                $('#uncheckAllPeriode').on('click', function() {
                    $('#periodeChecklist input').prop('checked', false);
                    $(this).addClass('d-none');
                    $('#checkAllPeriode').removeClass('d-none');
                });

                $('#checkAllSupplier').on('click', function() {
                    $('#supplierChecklist input').prop('checked', true);
                    $(this).addClass('d-none');
                    $('#uncheckAllSupplier').removeClass('d-none');
                });
                
                $('#uncheckAllSupplier').on('click', function() {
                    $('#supplierChecklist input').prop('checked', false);
                    $(this).addClass('d-none');
                    $('#checkAllSupplier').removeClass('d-none');
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