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
            <div class="table-responsive">
            <table class="table datanew">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Kondisi</th>
                    <th>Gallery</th>
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
                                        <a href="{{ route('inven_galeri.show', ['inven_galeri' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('inven_galeri.edit', ['inven_galeri' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item" onclick="deleteData({{ $item->id }})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
                                    </li>
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
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Pemakaian Sendiri</h4>
                </div>
                <div class="page-btn">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalPemakaianSendiri" class="btn btn-secondary d-flex justify-content-center align-items-center mt-1"><img src="assets/img/icons/plus.svg" style="filter: brightness(0) invert(1);" alt="img" class="me-1" />
                        Tambah Pemakaian
                    </a>                    
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table datanew">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Lokasi</th>
                    <th>Nama Produk</th>
                    <th>Kondisi</th>
                    <th>Pemakai</th>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                    <th>Alasan</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($pemakaian_sendiri as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->lokasi->nama ?? '-' }}</td>
                            <td>{{ $item->produk->nama ?? '-' }}</td>
                            <td>{{ $item->kondisi->nama ?? '-' }}</td>
                            <td>{{ $item->karyawan->nama ?? '-' }}</td>
                            <td>{{ $item->tanggal ? formatTanggal($item->tanggal) : '-' }}</td>
                            <td>{{ $item->jumlah ?? '-' }}</td>
                            <td>{{ $item->alasan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
        </div>
    </div>
</div>

{{-- modal start --}}
<div class="modal fade" id="modalPemakaianSendiri" tabindex="-1" aria-labelledby="modalPemakaianSendirilabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalPemakaianSendirilabel">Pemakaian Sendiri</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form action="{{ route('pemakaian_sendiri.store') }}" method="POST">
            @csrf
            <label for="lokasi_id" class="col-form-label">Lokasi</label>
            <div class="row">
                <div class="col">
                    <select id="lokasi_id" name="lokasi_id" class="form-control" required>
                        @if (Auth::user()->roles()->value('name') != 'admin' && Auth::user()->roles()->value('name') != 'Purchasing')
                        <option value="{{ Auth::user()->karyawans->lokasi_id }}">{{ Auth::user()->karyawans->lokasi->nama }}</option>
                        @else
                        <option value="">Pilih Lokasi</option>
                        @foreach ($lokasis as $lokasi)
                        <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
                <div class="col text-end">
                    <button type="button" class="btn btn-info" id="add"><img src="assets/img/icons/plus.svg" alt="img" /></button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                           <th style="width: 5%">No</th> 
                           <th>Tanggal</th> 
                           <th>Produk</th> 
                           <th style="width: 5%">Jumlah</th> 
                           <th>Pemakai</th> 
                           <th>Alasan</th> 
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
{{-- modal end --}}
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#produk_inven_id, #karyawan_id, #lokasi_id').select2()
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
                            '<td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">x</button></td>' +
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
        });
    </script>
@endsection