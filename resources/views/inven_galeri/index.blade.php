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
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalPemakaianSendiri" class="btn btn-secondary d-flex justify-content-center align-items-center mt-1">
                        Pemakaian Sendiri
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
          <form action="#" method="POST">
            @csrf
            <label for="lokasi_id" class="col-form-label">Lokasi</label>
            <div class="row">
                <div class="col">
                    <select id="lokasi_id" name="lokasi_id" class="form-control" required>
                        <option value="">Pilih Lokasi</option>
                        @foreach ($lokasis as $lokasi)
                        <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <button class="btn btn-info"><img src="assets/img/icons/plus.svg" alt="img" /></button>
                </div>
            </div>
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
                    <tr>
                        <td>1</td>
                        <td>
                            <input type="date" class="form-control" name="tanggal[]" id="tanggal" value="{{ date('Y-m-d') }}" required>
                        </td>
                        <td>
                            <select id="produk_jual_id" name="produk_jual_id[]" class="form-control" required>
                                <option value="">Pilih Produk</option>
                                @foreach ($produkJuals as $produkJual)
                                <option value="{{ $produkJual->id }}">{{ $produkJual->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control" name="jumlah[]" id="jumlah" required>
                        </td>
                        <td>
                            <select id="karyawan_id" name="karyawan_id[]" class="form-control" required>
                                <option value="">Pilih Karyawan</option>
                                @foreach ($karyawans as $karyawan)
                                    <option value="{{ $karyawan->id }}">{{ $karyawan->nama }}</option>
                                    @endforeach
                            </select>
                        </td>
                        <td>
                            <textarea name="alasan[]" id="alasan" class="form-control" required></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
            {{-- <div class="mb-3">
              <label for="lokasi_id" class="col-form-label">Lokasi</label>
              <select id="lokasi_id" name="lokasi_id" class="form-control" required>
                <option value="">Pilih Lokasi</option>
                @foreach ($lokasis as $lokasi)
                    <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
              <label for="produk_jual_id" class="col-form-label">Produk</label>
              <select id="produk_jual_id" name="produk_jual_id" class="form-control" required>
                <option value="">Pilih Produk</option>
                @foreach ($produkJuals as $produkJual)
                    <option value="{{ $produkJual->id }}">{{ $produkJual->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="jumlah" class="col-form-label">Jumlah Pemakaian</label>
                <input type="number" class="form-control" name="jumlah" id="jumlah" required>
            </div>
            <div class="mb-3">
                <label for="tanggal" class="col-form-label">Tanggal Pemakaian</label>
                <input type="date" class="form-control" name="tanggal" id="tanggal" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="mb-3">
                <label for="karyawan_id" class="col-form-label">Karyawan Pemakai</label>
                <select id="karyawan_id" name="karyawan_id" class="form-control" required>
                    <option value="">Pilih Karyawan</option>
                    @foreach ($karyawans as $karyawan)
                        <option value="{{ $karyawan->id }}">{{ $karyawan->nama }}</option>
                        @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="alasan" class="col-form-label">Alasan Pemakaian</label>
                <textarea name="alasan" id="alasan" class="form-control" required></textarea>
            </div> --}}
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
            $('#produk_jual_id, #karyawan_id, #lokasi_id').select2()
        });
    </script>
@endsection