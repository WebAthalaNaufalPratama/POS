@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h5 class="card-title">Buat Kembali Sewa</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('kembali_sewa.store') }}" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm">
                            @csrf
                            <div class="row justify-content-around">
                                <div class="col-md-6 border rounded pt-3">
                                    <h5 class="card-title">Informasi Pelanggan</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>No Kontrak</label>
                                                <input type="text" id="no_sewa" name="no_sewa" value="{{ old('no_sewa') ?? $kontrak->no_kontrak }}" class="form-control" required readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Customer</label>
                                                <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') ?? $kontrak->customer->nama }}" class="form-control" required readonly>
                                                <input type="hidden" id="customer_id" name="customer_id" value="{{ old('customer_id') ?? $kontrak->customer_id }}" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>PIC</label>
                                                <input type="text" id="pic" name="pic" value="{{ old('pic') ?? $kontrak->pic }}" class="form-control" required readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Handphone</label>
                                                <input type="text" id="handhpone" name="handphone" value="{{ old('handphone') ?? $kontrak->handphone }}" class="form-control" required readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 border rounded pt-3">
                                    <h5 class="card-title">Detail Kembali Sewa</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>No Kembali Sewa</label>
                                                <input type="text" id="no_kembali" name="no_kembali" value="{{ old('no_kembali') ?? $getKode }}" class="form-control" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal kembali</label>
                                                <input type="date" id="tanggal_kembali" name="tanggal_kembali" value="{{ old('tanggal_kembali') ?? date('Y-m-d') }}" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Driver</label>
                                                <select id="driver_id" name="driver" class="form-control" required>
                                                    <option value="">Pilih Driver</option>
                                                    @foreach ($drivers as $driver)
                                                        <option value="{{ $driver->id }}" {{ old('driver') == $driver->id ? 'selected' : '' }}>{{ $driver->nama }}</option>
                                                    @endforeach
                                                </select>
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
                                        <th style="width: 20%">No DO</th>
                                        <th style="width: 50%">Nama</th>
                                        <th style="width: 10%">Jumlah</th>
                                        <th style="width: 20%">Detail Lokasi</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="dynamic_field">
                                    @if(count($kontrak->produk) < 1)
                                    <tr>
                                        <td>
                                            <select id="no_do_produk_0" name="no_do_produk[]" class="form-control">
                                                <option value="">Pilih DO</option>
                                                @foreach ($do as $item)
                                                    <option value="{{ $item->no_do }}">{{ $item->no_do }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select id="produk_0" name="nama_produk[]" class="form-control" required disabled></select>
                                                <div id="komponen_0" class="row mt-2"></div>
                                        </td>
                                        <td><input type="number" name="jumlah[]" id="jumlah_0" class="form-control"></td>
                                        <td>
                                            <select id="lokasi_{{ $i }}" name="lokasi[]" class="form-control" required>
                                                <option value="">Pilih Detail Lokasi</option>
                                                @foreach ($detail_lokasi as $item)
                                                    <option value="{{ $item->detail_lokasi }}" {{ $item->detail_lokasi == old('lokasi.' . $i) ? 'selected' : '' }}>{{ $item->detail_lokasi }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td>
                                    </tr>
                                    @else
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($kontrak->produk as $produk) 
                                        <tr id="row{{ $i }}">
                                            <td>
                                                <select id="no_do_produk_{{ $i }}" name="no_do_produk[]" class="form-control" required>
                                                    <option value="">Pilih DO</option>
                                                    @foreach ($do as $item)
                                                        <option value="{{ $item->no_do }}" data-produk="{{ $item }}">{{ $item->no_do }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select id="produk_{{ $i }}" name="nama_produk[]" class="form-control" required disabled></select>
                                                <div id="komponen_{{ $i }}" class="row mt-2"></div>
                                            </td>
                                            <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" class="form-control" value="{{ old('jumlah.' . $i) }}" required disabled></td>
                                            <td>
                                                <select id="lokasi_{{ $i }}" name="lokasi[]" class="form-control" required disabled>
                                                    <option value="">Pilih Detail Lokasi</option>
                                                </select>
                                            </td>
                                            @if ($i == 0)
                                                <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td>
                                            @else
                                                <td><button type="button" name="remove" id="{{ $i }}" class="btn btn-danger btn_remove">x</button></td>
                                            @endif
                                            @php
                                                $i++;
                                            @endphp
                                        </tr>
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
                                            <td id="driver">-</td>
                                            <td id="pembuat">{{ Auth::user()->name ?? '-' }}</td>
                                            <td id="penyetuju">-</td>
                                            <td id="pemeriksa">-</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%;">
                                                <input type="date" class="form-control" id="tgl_driver" name="tanggal_driver" value="{{ date('Y-m-d') }}">
                                            </td>
                                            <td id="tgl_pembuat" style="width: 25%;">{{ date('Y-m-d') }}</td>
                                            <td id="tgl_penyetuju" style="width: 25%;">{{ isset($kontrak->tanggal_penyetuju) ? \Carbon\Carbon::parse($kontrak->tanggal_penyetujju)->format('Y-m-d') : '-' }}</td>
                                            <td id="tgl_pemeriksa" style="width: 25%;">{{ isset($kontrak->tanggal_pemeriksa) ? \Carbon\Carbon::parse($kontrak->tanggal_pemeriksa)->format('Y-m-d') : '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4 border rounded mt-3 pt-3">
                                <div class="custom-file-container" data-upload-id="myFirstImage">
                                    <label>Bukti Kirim <a href="javascript:void(0)" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image">clear</a>
                                    </label>
                                    <label class="custom-file-container__custom-file">
                                        <input type="file" id="bukti" class="custom-file-container__custom-file__custom-file-input" name="file" accept="image/*" required>
                                        <span class="custom-file-container__custom-file__custom-file-control"></span>
                                    </label>
                                    <span class="text-danger">max 2mb</span>
                                    <img id="preview" src="" alt="your image" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button class="btn btn-primary" type="submit">Submit</button>
                    <a href="{{ route('kontrak.index') }}" class="btn btn-secondary" type="button">Back</a>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function(){
            if ($('#preview').attr('src') === '') {
                $('#preview').attr('src', defaultImg);
            }
            $('[id^=produk], #driver_id, [id^=no_do_produk], [id^=kondisi], [id^=lokasi], [id^=namaKomponen]').select2();
            $('#driver_id').trigger('change');
            var i = '{{ count($kontrak->produk) }}';
            $('#add').click(function(){
                var newRow = '<tr id="row'+i+'">' + 
                               '<td>' +
                                    '<select id="no_do_produk_'+i+'" name="no_do_produk[]" class="form-control">' +
                                        '<option value="">Pilih DO</option>'+
                                        '@foreach ($do as $item)' +
                                            '<option value="{{ $item->no_do }}" data-produk="{{ $item }}">{{ $item->no_do }}</option>' +
                                        '@endforeach' +
                                    '</select>' +
                                '</td>' +
                                '<td>' +
                                '<select id="produk_'+i+'" name="nama_produk[]" class="form-control" required disabled></select>' +
                                    '<div id="komponen_'+i+'" class="row mt-2"></div>'+
                            '</td>'+
                            '<td><input type="number" name="jumlah[]" id="jumlah_'+i+'" class="form-control" disabled></td>'+
                            '<td>' +
                                '<select id="lokasi_'+i+'" name="lokasi[]" class="form-control" required disabled>' +
                                    '<option value="">Pilih Detail Lokasi</option>' +
                                '</select>' +
                            '</td>' +
                            '<td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">x</button></td>' +
                            '</tr>';
                $('#dynamic_field').append(newRow);
                $('#produk_' + i).select2();
                $('#no_do_produk_' + i).select2();
                $('#kondisi_' + i).select2();
                $('#lokasi_' + i).select2();
                i++;
            })
        })
        $(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
            $('#row'+button_id+'').remove();
        });
        $('#driver_id').on('change', function() {
            var nama_driver = $("#driver_id option:selected").text();
            var val_driver = $("#driver_id option:selected").val();
            if(val_driver != ""){
                $('#driver').text(nama_driver)
            } else {
                $('#driver').text('-')
            }
        });
        $('#bukti').on('change', function() {
            const file = $(this)[0].files[0];
            if (file.size > 2 * 1024 * 1024) { 
                toastr.warning('Ukuran file tidak boleh lebih dari 2mb', {
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: false,
                    progressBar: true
                });
                $(this).val(''); 
                return;
            }
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
        $(document).on('change', '[id^=no_do_produk_]', function() {
            var id = $(this).attr('id').split('_')[3];
            var selectProduk = $('#produk_' + id);
            var jumlahProduk = $('#jumlah_' + id);
            var lokasiProduk = $('#lokasi_' + id);
            $(jumlahProduk).val(0);
            $('#komponen_' + id).empty();

            if($(this).val()){ // cek jika value kosong
                var data = {
                    no_do: $(this).val(),
                };
                $.ajax({
                    url: '/getProdukDo',
                    type: 'GET',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        $(lokasiProduk).empty()
                        $(lokasiProduk).append('<option value="">Pilih Detail Lokasi</option>')
                        $(selectProduk).attr('disabled', false);
                        $(selectProduk).empty()
                        $(selectProduk).append('<option value="">Pilih Produk</option>')
                        for (let i = 0; i < response.length; i++) {
                            if(response[i].jenis != 'TAMBAHAN'){
                                $(selectProduk).append('<option value="' + response[i].produk.kode + '" data-id="'+response[i].id+'">' + response[i].produk.nama + '</option>');
                                $(lokasiProduk).append('<option value="' + response[i].detail_lokasi + '">' + response[i].detail_lokasi + '</option>');
                            }
                        }
                        $(document).on('change', '[id^=produk_]', function() {
                            var id = $(this).attr('id').split('_')[1];
                            var jumlahProduk = $('#jumlah_' + id);
                            var lokasiProduk = $('#lokasi_' + id);
                            if($(this).val()){ // cek jika value kosong
                                var idProdukTerjual = $('#produk_' + id).find(':selected').data('id');
                                $('#komponen_' + id).empty();
                                console.log(response)
                                response.forEach(function(item){
                                    if(item.id == idProdukTerjual){
                                        $(jumlahProduk).attr('disabled', false);
                                        $(lokasiProduk).attr('disabled', false);
                                        $(jumlahProduk).val(item.jumlah);
                                        $(lokasiProduk).val(item.detail_lokasi).trigger('change');

                                        // komponen
                                        var jmlKomponen = 0;
                                        for (let j = 0; j < item.komponen.length; j++) {
                                            if(item.jenis == null && (item.komponen[j].tipe_produk == 1 || item.komponen[j].tipe_produk == 2)){
                                                var komponenRow = '<div id="komponen_'+id+'" class="row mt-2">'+
                                                                        '<div class="col">'+
                                                                            '<select id="namaKomponen_'+id+'_'+j+'" name="namaKomponen[]" class="form-control" required readonly>'+
                                                                                '<option value="' + item.komponen[j].kode_produk + '">' + item.komponen[j].nama_produk + '</option>'+
                                                                            '</select>'+
                                                                        '</div>'+
                                                                        '<div class="col">'+
                                                                            '<select id="kondisiKomponen_'+id+'_'+j+'" name="kondisiKomponen[]" class="form-control">' +
                                                                            '<option value="">Pilih Kondisi</option>' +
                                                                            '@foreach ($kondisi as $item)' +
                                                                                '<option value="{{ $item->id }}">{{ $item->nama }}</option>' +
                                                                            '@endforeach' +
                                                                        '</select>' +
                                                                        '</div>' +
                                                                        '<div class="col">'+
                                                                            '<input type="number" name="jumlahKomponen[]" id="jumlahKomponen_'+id+'_'+j+'" class="form-control" value="" required>'+
                                                                        '</div>'+
                                                                    '</div>';
                                                    $('#komponen_' + id).append(komponenRow);
                                                    jmlKomponen++;
                                                    $('#jumlahKomponen_'+id+'_'+j+'').val(item.komponen[j].jumlah);
                                                    $('#kondisiKomponen_'+id+'_'+j+'').val(item.komponen[j].kondisi).trigger('change');
                                                    $('#kondisiKomponen_'+id+'_'+j+'').select2();
// console.log('s')
                                            }
                                        }
                                        if(item.jenis == null) {
                                        $('#komponen_' + id).append('<input type="hidden" name="indexKomponen[]" value="'+jmlKomponen+'">');
                                    }
                                    }
                                })
                            } else { // kosongkan input jika value kosong
                                $('#komponen_' + id).empty();
                                $(jumlahProduk).attr('disabled', true);
                                $(lokasiProduk).attr('disabled', true);
                                $(jumlahProduk).val(0);
                                $(lokasiProduk).val('').trigger('change');
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.log(error)
                    }
                });
            } else { // kosongkan input jika value kosong
                $(selectProduk).attr('disabled', true);
                $(jumlahProduk).attr('disabled', true);
                $(lokasiProduk).attr('disabled', true);
                $(selectProduk).empty();
                $(jumlahProduk).val(0);
                $(lokasiProduk).val('').trigger('change');
            }
        });
        
        function clearFile(){
            $('#bukti').val('');
            $('#preview').attr('src', defaultImg);
        };
    </script>
@endsection