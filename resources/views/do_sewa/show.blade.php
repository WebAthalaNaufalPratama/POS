@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h5 class="card-title">Detail Delivery order</h5>
                    </div>
                    <div class="page-btn">
                        <a href="#" class="btn btn-added">Cetak DO</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm">
                        <div class="row justify-content-around">
                            <div class="col-md-6 border rounded pt-3">
                                <h5 class="card-title">Informasi Pelanggan</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Customer</label>
                                            <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') ?? $data->customer->nama }}" class="form-control" disabled>
                                            <input type="hidden" id="customer_id" name="customer_id" value="{{ old('customer_id') ?? $data->customer_id }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>PIC</label>
                                            <input type="text" id="pic" name="pic" value="{{ old('pic') ?? $data->penerima }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Handphone</label>
                                            <input type="text" id="handhpone" name="handphone" value="{{ old('handphone') ?? $data->handphone }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Alamat</label>
                                            <textarea type="text" id="alamat" name="alamat" class="form-control" disabled>{{ old('alamat') ?? $data->alamat }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 border rounded pt-3">
                                <h5 class="card-title">Detail Pesanan</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>No Delivery Order</label>
                                            <input type="text" id="no_do" name="no_do" value="{{ old('no_do') ?? $data->no_do }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Tanggal Mulai</label>
                                            <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') ?? $data->tanggal_mulai }}" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>No Kontrak</label>
                                            <input type="text" id="no_sewa" name="no_sewa" value="{{ old('no_sewa') ?? $data->no_sewa }}" class="form-control"  disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Tanggal Selesai</label>
                                            <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') ?? $data->tanggal_selesai }}" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Driver</label>
                                            <select id="driver_id" name="driver_id" class="form-control" disabled>
                                                <option value="">Pilih Driver</option>
                                                @foreach ($drivers as $driver)
                                                    <option value="{{ $driver->id }}" {{ $driver-> id == $data->driver ? 'selected' : '' }}>{{ $driver->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Catatan</label>
                                            <textarea type="text" id="catatan" name="catatan" class="form-control" disabled>{{ old('catatan') ?? $data->catatan }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="form-row row">
                    <label>List Produk</label>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Detail Lokasi</th>
                                </tr>
                            </thead>
                            <tbody id="dynamic_field">
                                @if(count($data->produk) < 1)
                                <tr>
                                    <td>
                                        <select id="produk_0" name="nama_produk[]" class="form-control" disabled>
                                            <option value="">Pilih Produk</option>
                                            @foreach ($produkJuals as $produk)
                                                <option value="{{ $produk->kode }}">{{ $produk->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="jumlah[]" id="jumlah_0" class="form-control"></td>
                                    <td><input type="number" name="satuan[]" id="satuan_0" class="form-control"></td>
                                    <td><input type="number" name="detail_lokasi[]" id="detail_lokasi_0" class="form-control"></td>
                                </tr>
                                @else
                                @php
                                $i = 0;
                                @endphp
                                @foreach ($data->produk as $produk)
                                @if ($produk->jenis == null)
                                    <tr id="row{{ $i }}">
                                        <td>
                                            <select id="produk_{{ $i }}" name="nama_produk[]" class="form-control" disabled>
                                                <option value="">Pilih Produk</option>
                                                @foreach ($produkJuals as $pj)
                                                    <option value="{{ $pj->kode }}" data-tipe_produk="{{ $pj->tipe_produk }}" {{ $pj->kode == $produk->produk->kode ? 'selected' : '' }}>{{ $pj->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" class="form-control" value="{{ $produk->jumlah }}" disabled></td>
                                        <td><input type="text" name="satuan[]" id="satuan_{{ $i }}" class="form-control" value="{{ $produk->satuan }}" disabled></td>
                                        <td><input type="text" name="detail_lokasi[]" id="detail_lokasi_{{ $i }}" class="form-control" value="{{ $produk->detail_lokasi }}" disabled></td>
                                    </tr>
                                @endif 
                                @php
                                    $i++;
                                @endphp
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="form-row row">
                    <label>Tambahan Produk</label>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Detail Lokasi</th>
                                </tr>
                            </thead>
                            <tbody id="dynamic_field2">
                                @if(count($data->produk) < 1)
                                <tr>
                                    <td>
                                        <select id="produk2_0" name="nama_produk2[]" class="form-control" disabled>
                                            <option value="">Pilih Produk</option>
                                            @foreach ($produkJuals as $produk)
                                                <option value="{{ $produk->kode }}">{{ $produk->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="jumlah2[]" id="jumlah2_0" class="form-control"></td>
                                    <td><input type="number" name="satuan[]" id="satuan_0" class="form-control"></td>
                                    <td><input type="number" name="detail_lokasi[]" id="detail_lokasi_0" class="form-control"></td>
                                </tr>
                                @else
                                @php
                                $i = 0;
                                @endphp
                                @foreach ($data->produk as $produk)
                                @if ($produk->jenis == 'TAMBAHAN')
                                    <tr id="row{{ $i }}">
                                        <td>
                                            <select id="produk2_{{ $i }}" name="nama_produk2[]" class="form-control" disabled>
                                                <option value="">Pilih Produk</option>
                                                @foreach ($produkJuals as $pj)
                                                    <option value="{{ $pj->kode }}" data-tipe_produk="{{ $pj->tipe_produk }}" {{ $pj->kode == $produk->produk->kode ? 'selected' : '' }}>{{ $pj->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="jumlah2[]" id="jumlah2_{{ $i }}" class="form-control" value="{{ $produk->jumlah }}" disabled></td>
                                        <td><input type="text" name="satuan2[]" id="satuan2_{{ $i }}" class="form-control" value="{{ $produk->satuan }}" disabled></td>
                                        <td><input type="text" name="detail_lokasi2[]" id="detail_lokasi2_{{ $i }}" class="form-control" value="{{ $produk->detail_lokasi }}" disabled></td>
                                    </tr>
                                @endif 
                                @php
                                    $i++;
                                @endphp
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <div class="row justify-content-around">
                        <div class="col-md-8 pt-3 ps-0 pe-0">
                            <table class="table table-responsive border rounded">
                                <thead>
                                    <tr>
                                        <th>Driver</th>
                                        <th>Pembuat</th>
                                        <th>Penyetuju</th>
                                        <th>Pemeriksa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td id="driver">{{ $data->data_driver->nama ?? '-' }}</td>
                                        <td id="pembuat">{{ $data->data_pembuat->name ?? '-' }}</td>
                                        <td id="penyetuju">{{ $data->data_penyetuju->nama ?? '-' }}</td>
                                        <td id="pemeriksa">{{ $data->data_pemeriksa->nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 25%;">
                                            {{ isset($data->tanggal_driver) ? \Carbon\Carbon::parse($data->tanggal_driver)->format('Y-m-d') : '-' }}
                                        </td>
                                        <td id="tgl_pembuat" style="width: 25%;">{{ isset($data->tanggal_pembuat) ? \Carbon\Carbon::parse($data->tanggal_pembuat)->format('Y-m-d') : '-' }}</td>
                                        <td id="tgl_penyetuju" style="width: 25%;">{{ isset($data->tanggal_penyetuju) ? \Carbon\Carbon::parse($data->tanggal_penyetuju)->format('Y-m-d') : '-' }}</td>
                                        <td id="tgl_pemeriksa" style="width: 25%;">{{ isset($data->tanggal_pemeriksa) ? \Carbon\Carbon::parse($data->tanggal_pemeriksa)->format('Y-m-d') : '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-4 border rounded mt-3 pt-3">
                            <div class="custom-file-container" data-upload-id="myFirstImage">
                                <label>Bukti Kirim (Single File) <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">clear</a></label>
                                <label class="custom-file-container__custom-file">
                                <input type="file" class="custom-file-container__custom-file__custom-file-input" accept="image/*">
                                <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
                                <span class="custom-file-container__custom-file__custom-file-control"></span>
                                </label>
                                <div class="custom-file-container__image-preview"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end mt-3">
                <a href="{{ route('do_sewa.index') }}" class="btn btn-secondary" type="button">Back</a>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function(){
            $('[id^=produk], #driver_id').select2();
            var i = '{{ count($data->produk) }}';
            $('#add').click(function(){
                var newRow = '<tr id="row'+i+'"><td>' + 
                                '<select id="produk_'+i+'" name="nama_produk[]" class="form-control">'+
                                    '<option value="">Pilih Produk</option>'+
                                    '@foreach ($produkJuals as $pj)'+
                                        '<option value="{{ $pj->kode }}" data-tipe_produk="{{ $pj->tipe_produk }}">{{ $pj->nama }}</option>'+
                                    '@endforeach'+
                                '</select>'+
                            '</td>'+
                            '<td><input type="number" name="satuan[]" id="satuan_'+i+'" class="form-control"></td>'+
                            '<td><input type="number" name="jumlah[]" id="jumlah_'+i+'" class="form-control"></td>'+
                            '<td><input type="number" name="detail_lokasis[]" id="detail_lokasis_'+i+'" class="form-control"></td>'+
                            '<td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">x</button></td></tr>';
                $('#dynamic_field').append(newRow);
                $('#produk_' + i).select2();
                i++;
            })
            $('#add2').click(function(){
                var newRow = '<tr id="row2'+i+'"><td>' + 
                                '<select id="produk2_'+i+'" name="nama_produk2[]" class="form-control">'+
                                    '<option value="">Pilih Produk</option>'+
                                    '@foreach ($produkJuals as $pj)'+
                                        '<option value="{{ $pj->kode }}" data-tipe_produk="{{ $pj->tipe_produk }}">{{ $pj->nama }}</option>'+
                                    '@endforeach'+
                                '</select>'+
                            '</td>'+
                            '<td><input type="number" name="satuan2[]" id="satuan2_'+i+'" class="form-control"></td>'+
                            '<td><input type="number" name="jumlah2[]" id="jumlah2_'+i+'" class="form-control"></td>'+
                            '<td><input type="number" name="detail_lokasis2[]" id="detail_lokasis2_'+i+'" class="form-control"></td>'+
                            '<td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove2">x</button></td></tr>';
                $('#dynamic_field2').append(newRow);
                $('#produk2_' + i).select2();
                i++;
            })
        })
        $(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
            $('#row'+button_id+'').remove();
        });
        $(document).on('click', '.btn_remove2', function() {
            var button_id = $(this).attr("id");
            $('#row2'+button_id+'').remove();
        });
        $('#driver_id').on('change', function() {
            console.log('fe')
            var nama_driver = $("#driver_id option:selected").text();
            var val_driver = $("#driver_id option:selected").val();
            if(val_driver != ""){
                $('#driver').text(nama_driver)
            } else {
                $('#driver').text('-')
            }
        });
    </script>
@endsection