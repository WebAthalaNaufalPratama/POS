@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Detail Data Inventory Greenhouse</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4">
                        <label for="kode_produk" class="col-form-label">Produk</label>
                        <div id="div_produk" class="form-group">
                            <select id="kode_produk" name="kode_produk" class="form-control" disabled>
                                <option value="">Pilih Produk</option>
                                @foreach ($produks as $item)
                                    <option value="{{ $item->kode }}" {{ $item->kode == $data->kode_produk ? 'selected' : '' }}>{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label for="kondisi_id" class="col-form-label">Kondisi</label>
                        <div id="div_kondisi" class="form-group">
                            <select id="kondisi_id" name="kondisi_id" class="form-control" disabled>
                                <option value="">Pilih Kondisi</option>
                                @foreach ($kondisi as $item)
                                    <option value="{{ $item->id }}" {{ $item->id == $data->kondisi_id ? 'selected' : '' }}>{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label for="lokasi_id" class="col-form-label">Lokasi</label>
                        <div id="div_lokasi" class="form-group">
                            <select id="lokasi_id" name="lokasi_id" class="form-control" disabled>
                                <option value="">Pilih Gallery</option>
                                @foreach ($gallery as $item)
                                    <option value="{{ $item->id }}" {{ $item->id == $data->lokasi_id ? 'selected' : '' }}>{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label for="jumlah" class="col-form-label">Jumlah</label>
                        <input type="number" class="form-control" name="jumlah" id="jumlah" value="{{ $data->jumlah }}" disabled>
                    </div>
                    <div class="col-sm-6">
                        <label for="min_stok" class="col-form-label">Minimal Stok</label>
                        <input type="number" class="form-control" name="min_stok" id="min_stok" value="{{ $data->min_stok }}" disabled>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button class="btn btn-primary" type="submit">Submit</button>
                    <a href="{{ route('inven_greenhouse.index') }}" class="btn btn-secondary" type="button">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function() {
            $('#kode_produk, #kondisi_id, #lokasi_id').select2();
        });
    </script>
@endsection