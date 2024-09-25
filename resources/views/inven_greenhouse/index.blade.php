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
                        @if(in_array('inven_greenhouse.ubahKondisi', $thisUserPermissions))
                        <a href="javascript::void(0);" data-bs-target="#modalUbahKondisi" data-bs-toggle="modal" class="btn btn-info me-2 d-flex justify-content-center align-items-center"><img src="assets/img/icons/loop.svg" alt="img" class="me-1" /></a>
                        @endif
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
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Kode Produk</th>
                        <th>Tipe Produk</th>
                        <th>Kondisi</th>
                        <th>Greenhouse</th>
                        <th>Minimal Stok</th>
                        <th>Jumlah</th>
                        @if(in_array('inven_greenhouse.show', $thisUserPermissions) || in_array('inven_greenhouse.edit', $thisUserPermissions))
                        <th class="text-center">Aksi</th>
                        @endif
                    </tr>
                </tr>
                </thead>
                <tbody>
                    {{-- @foreach ($data as $item)
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
                        <th></th>
                        <th></th>
                        <th></th>
                        @if(in_array('inven_greenhouse.show', $thisUserPermissions) || in_array('inven_greenhouse.edit', $thisUserPermissions))
                        <th></th>
                        @endif
                    </tr>
                </tfoot>
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
                      @foreach ($greenhouses as $item)
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
<div class="modal fade" id="modalUbahKondisi" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Ubah Kondisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="form_ubah_kondisi" action="{{ route('inven_greenhouse.ubahKondisi') }}" method="POST">
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
@endsection
@section('scripts')
    <script>
        $(document).ready(function(){
            $('select').select2()

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
                    { data: 'produk.nama', name: 'produk.nama' },
                    { data: 'kode_produk', name: 'kode_produk' },
                    { data: 'tipe_produk', name: 'tipe_produk', orderable: false },
                    { data: 'kondisi.nama', name: 'kondisi.nama', orderable: false },
                    { data: 'gallery.nama', name: 'gallery.nama', orderable: false },
                    { data: 'min_stok', name: 'min_stok' },
                    { data: 'jumlah', name: 'jumlah' },
                    @if(in_array('inven_greenhouse.show', $thisUserPermissions) || in_array('inven_greenhouse.edit', $thisUserPermissions))
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
                                            <a href="inven_greenhouse/${row.id}/show" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                        </li>
                                        <li>
                                            <a href="inven_greenhouse/${row.id}/edit" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                        </li>
                                    </ul>
                                </div>
                            `;
                        }
                    }
                    @endif
                ];

                let table = initDataTable('#inventory', {
                    ajaxUrl: "{{ route('inven_greenhouse.index') }}",
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
                    $('#uncheckAllKondisi').addClass('d-none');
                    $('#checkAllKondisi').removeClass('d-none');
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