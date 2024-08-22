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
                    <a href="{{ route('inven_galeri.create') }}" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Inventory</a>
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
            </div>
            <div class="table-responsive">
            <table class="table" id="inventory" style="width: 100%">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Kondisi</th>
                    @if(!Auth::user()->hasRole('AdminGallery'))
                    <th>Gallery</th>
                    @endif
                    <th>Jumlah</th>
                    <th>Minimal Stok</th>
                    <th class="text-center">Aksi</th>
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
                <table class="table" id="log" style="width: 100%">
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
{{-- modal end --}}
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#produk_inven_id, #karyawan_id, #lokasi_id, select[id^=filter]').select2()
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

            // Start Datatable Inventory
                const columns = [
                    { data: 'no', name: 'no', orderable: false },
                    { data: 'kode_produk', name: 'kode_produk' },
                    { data: 'produk.nama', name: 'produk.nama', orderable: false },
                    { data: 'kondisi.nama', name: 'kondisi.nama', orderable: false },
                    @if(!Auth::user()->hasRole('AdminGallery'))
                    { data: 'gallery.nama', name: 'gallery.nama', orderable: false },
                    @endif
                    { data: 'jumlah', name: 'jumlah' },
                    { data: 'min_stok', name: 'min_stok' },
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
                ];

                let table = initDataTable('#inventory', {
                    ajaxUrl: "{{ route('inven_galeri.index') }}",
                    columns: columns,
                    order: [[1, 'asc']],
                    searching: true,
                    lengthChange: true,
                    pageLength: 5
                }, {
                    produk: '#filterProduk',
                    kondisi: '#filterKondisi',
                    gallery: '#filterGallery',
                    dateStart: '#filterDateStart',
                    dateEnd: '#filterDateEnd'
                }, 'inventory'); 

                const handleSearch = debounce(function() {
                    table.ajax.reload();
                }, 5000); // Adjust the debounce delay as needed

                // Event listeners for search filters
                $('#filterProduk, #filterKondisi, #filterGallery, #filterDateStart, #filterDateEnd').on('input', handleSearch);

                $('#filterBtn').on('click', function() {
                    table.ajax.reload();
                });

                $('#clearBtn').on('click', function() {
                    $('#filterProduk').val('').trigger('change');
                    $('#filterKondisi').val('').trigger('change');
                    $('#filterGallery').val('').trigger('change');
                    $('#filterDateStart').val('');
                    $('#filterDateEnd').val('');
                    table.ajax.reload();
                });
            // End Datatable Inventory

            // Start Datatable Pemakaian Sendiri
                const columns2 = [
                    { data: 'no', name: 'no', orderable: false },
                    { data: 'id', name: 'id', visible: false },
                    @if(!Auth::user()->hasRole('AdminGallery'))
                    { data: 'nama_gallery', name: 'nama_gallery', orderable: false },
                    @endif
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
                    pageLength: 5
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
        });   
    </script>
@endsection