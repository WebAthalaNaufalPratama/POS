@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Detail Data Inventory Inden</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                        <label for="kode_produk" class="col-form-label">Produk</label>
                        <input type="text" class="form-control" name="jumlah" id="jumlah" value="{{ $data->produk->nama }}" readonly>

                        {{-- <div id="div_produk" class="form-group">
                            <select id="kode_produk" name="kode_produk" class="form-control" readonly>
                                <option value="">Pilih Produk</option>
                                @foreach ($produks as $item)
                                    <option value="{{ $item->kode }}" {{ $data->kode_produk == $item->kode ? 'selected' : '' }}>{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                    </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="bulan_inden">Bulan Inden</label>
                            <input type="text" class="form-control" name="jumlah" id="jumlah" value="{{ $data->bulan_inden }}" readonly>

                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label for="lokasi_id" class="col-form-label">Supplier</label>
                        <div id="div_lokasi" class="form-group">
                            <select id="supplier_id" name="supplier_id" class="form-control" readonly>
                                <option value="">Pilih Supplier</option>
                                @foreach ($suppliers as $item)
                                    <option value="{{ $item->id }}"  {{ $data->supplier_id == $item->id ? 'selected' : ''}}>{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label for="kode_produk_inden" class="col-form-label">Kode Produk Inden</label>
                        <input type="text" class="form-control" name="kode_produk_inden" id="kode_produk_inden"  value="{{ $data->kode_produk_inden }}" readonly>
                    </div>
                    <div class="col-sm-6">
                        <label for="jumlah" class="col-form-label">Jumlah</label>
                        <input type="number" class="form-control" name="jumlah" id="jumlah" value="{{ $data->jumlah }}" readonly>
                    </div>
                </div>
                {{-- <div class="text-end mt-3">
                    <button class="btn btn-primary" type="submit">Submit</button>
                    <a href="{{ route('inven_inden.index') }}" class="btn btn-secondary" type="button">Back</a>
                </div> --}}
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