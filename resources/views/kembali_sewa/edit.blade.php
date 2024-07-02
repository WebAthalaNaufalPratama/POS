@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h5 class="card-title">Edit Kembali Sewa</h5>
                    </div>
                </div>
            </div>
            <form action="{{ route('kembali_sewa.update', ['kembali_sewa' => $data->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('patch')
                <div class="card-body">
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
                                                    <input type="text" id="no_sewa" name="no_sewa" value="{{ old('no_sewa') ?? $kontrak->no_kontrak }}" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Customer</label>
                                                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') ?? $kontrak->customer->nama }}" class="form-control" readonly>
                                                    <input type="hidden" id="customer_id" name="customer_id" value="{{ old('customer_id') ?? $kontrak->customer_id }}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>PIC</label>
                                                    <input type="text" id="pic" name="pic" value="{{ old('pic') ?? $kontrak->pic }}" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Handphone</label>
                                                    <input type="text" id="handhpone" name="handphone" value="{{ old('handphone') ?? $kontrak->handphone }}" class="form-control" readonly>
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
                                                    <input type="text" id="no_kembali" name="no_kembali" value="{{ $data->no_kembali }}" class="form-control" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label>Tanggal kembali</label>
                                                    <input type="date" id="tanggal_kembali" name="tanggal_kembali" value="{{ $data->tanggal_kembali }}" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Driver</label>
                                                    <select id="driver_id" name="driver" class="form-control" required>
                                                        <option value="">Pilih Driver</option>
                                                        @foreach ($drivers as $driver)
                                                            <option value="{{ $driver->id }}" {{ $data->driver == $driver->id ? 'selected' : '' }}>{{ $driver->nama }}</option>
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
                                                <select id="produk_0" name="nama_produk[]" class="form-control" disabled></select>
                                                    <div id="komponen_0" class="row mt-2"></div>
                                            </td>
                                            <td><input type="number" name="jumlah[]" id="jumlah_0" class="form-control"></td>
                                            <td>
                                                <select id="lokasi_{{ $i }}" name="lokasi[]" class="form-control" disabled>
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
                                        @foreach ($data2 as $produk) 
                                            <tr id="row{{ $i }}">
                                                <td>
                                                    <select id="no_do_produk_{{ $i }}" name="no_do_produk[]" class="form-control" required>
                                                        <option value="">Pilih DO</option>
                                                        @foreach ($do as $item)
                                                            <option value="{{ $item->no_do }}" data-produk="{{ $item }}" {{ $produk->no_do == $item->no_do ? 'selected' : '' }}>{{ $item->no_do }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select id="produk_{{ $i }}" name="nama_produk[]" class="form-control" required>
                                                        <option value="">Pilih Produk</option>
                                                        <option value="{{ $produk->produk->kode }}" data-id="{{ $produk->id }}" selected>({{ $produk->id }}) {{ $produk->produk->nama }}</option>
                                                    </select>
                                                    <div id="komponen_{{ $i }}" class="row mt-2">
                                                        @for ($j = 0; $j < count($produk->komponen); $j++)
                                                            <div class="row mt-2">
                                                                <div class="col">
                                                                    <select id="namaKomponen_{{ $i }}_{{ $j }}" name="namaKomponen[]" class="form-control" disabled>
                                                                        <option value="{{ $produk->komponen[$j]->kode_produk }}">{{ $produk->komponen[$j]->nama_produk }}</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col">
                                                                    <select id="kondisiKomponen_{{ $i }}_{{ $j }}" name="kondisiKomponen[]" class="form-control" required>
                                                                        <option value="">Pilih Kondisi</option>
                                                                        @foreach ($kondisi as $item)
                                                                            <option value="{{ $item->id }}" {{ $produk->komponen[$j]->kondisi == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col">
                                                                    <input type="number" name="jumlahKomponen[]" id="jumlahKomponen_{{ $i }}_{{ $j }}" class="form-control" value="{{ $produk->komponen[$j]->jumlah }}" required required>
                                                                </div>
                                                            </div>
                                                        @endfor
                                                    </div>
                                                </td>
                                                <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" class="form-control" value="{{ $produk->jumlah }}" required></td>
                                                <td>
                                                    <select id="lokasi_{{ $i }}" name="lokasi[]" class="form-control" disabled>
                                                        <option value="{{ $produk->detail_lokasi }}">{{ $produk->detail_lokasi }}</option>
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
                                                <td style="width: 25%;">{{ isset($data->tanggal_driver) ? formatTanggal($data->tanggal_driver) : '-' }}</td>
                                                <td id="tgl_pembuat" style="width: 25%;">{{ isset($data->tanggal_pembuat) ? formatTanggal($data->tanggal_pembuat) : '-' }}</td>
                                                <td id="tgl_penyetuju" style="width: 25%;">{{ isset($data->tanggal_penyetuju) ? formatTanggal($data->tanggal_penyetuju) : '-' }}</td>
                                                <td id="tgl_pemeriksa" style="width: 25%;">{{ isset($data->tanggal_pemeriksa) ? formatTanggal($data->tanggal_pemeriksa) : '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="col-sm-12 mt-3">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title">Riwayat</h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                <table class="table datanew">
                                                    <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Tanggal Perubahan</th>
                                                        <th>Customer</th>
                                                        <th>Pengubah</th>
                                                        <th>Log</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($riwayat as $item)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $item->created_at ?? '-' }}</td>
                                                            <td>{{ $item->subject->sewa->customer->nama ?? '-' }}</td>
                                                            <td>{{ $item->causer->name ?? '-' }}</td>
                                                            <td>
                                                                @php
                                                                    $changes = $item->changes();
                                                                    if(isset($changes['old'])){
                                                                        $diff = array_keys(array_diff_assoc($changes['attributes'], $changes['old']));
                                                                        foreach ($diff as $key => $value) {
                                                                            echo "$value: <span class='text-danger'>{$changes['old'][$value]}</span> => <span class='text-success'>{$changes['attributes'][$value]}</span>" . "<br>";
                                                                        }
                                                                    } else {
                                                                        echo 'Data Kembali Sewa Terbuat';
                                                                    }
                                                                @endphp
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
                                <div class="col-md-4 border rounded mt-3 pt-3">
                                    <div class="custom-file-container" data-upload-id="myFirstImage">
                                        <label>Bukti Kirim <a href="javascript:void(0)" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image">clear</a>
                                        </label>
                                        <label class="custom-file-container__custom-file">
                                            <input type="file" id="bukti" class="custom-file-container__custom-file__custom-file-input" name="file" accept="image/*">
                                            <span class="custom-file-container__custom-file__custom-file-control"></span>
                                        </label>
                                        <span class="text-danger">max 2mb</span>
                                        <img id="preview" src="{{ $data->file ? '/storage/' . $data->file : '' }}" alt="your image" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a href="{{ route('kembali_sewa.index') }}" class="btn btn-secondary" type="button">Back</a>
                    </div>
                </div>
            </form>
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
            $('[id^=produk], #driver_id, [id^=no_do_produk], [id^=kondisi], [id^=lokasi]').select2();
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

        $(document).on('change', '[id^=no_do_produk_]', async function() {
            var id = $(this).attr('id').split('_')[3];
            if ($(this).val()) {
                var jumlahProduk = $('#jumlah_' + id);
                var lokasiProduk = $('#lokasi_' + id);
                try {
                    var data = await getProdukDo(id);
                    populateSelectProduk(data, id)
                } catch (error) {
                    toastr.danger('Gagal mengambil data produk DO', {
                        closeButton: true,
                        tapToDismiss: false,
                        rtl: false,
                        progressBar: true
                    });
                    resetFields(id);
                }
            } else {
                resetProduk(id);
            }
        });

        $(document).on('change', '[id^=produk_]',  async function() {
            var id = $(this).attr('id').split('_')[1];
            var idProdukTerjual = $(this).find(':selected').data('id');
            
            try {
                var data = await getProdukDo(id);
                if ($(this).val()) { // Check if value is not empty
                    $('#komponen_' + id).empty();
                    data.forEach(function(item) {
                        populateLokasiProduk(data, id)
                        if (item.id == idProdukTerjual) {
                            $('#jumlah_' + id).attr('disabled', false);
                            $('#jumlah_' + id).val(item.jumlah);
                            populateKomponen(item, id);
                        }
                    });
                } else { // Clear inputs if value is empty
                    resetFields(id);
                }
            } catch (error) {
                toastr.danger('Gagal mengambil data produk DO', {
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: false,
                    progressBar: true
                });
                resetFields(id);
            }
        });

        function populateLokasiProduk(response, id) {
           $('#lokasi_' + id).empty();
           $('#lokasi_' + id).attr('disabled', false);
           $('#lokasi_' + id).append('<option value="">Pilih Detail Lokasi</option>');
            response.forEach(function(item) {
                if (item.jenis != 'TAMBAHAN' && item.no_kembali_sewa == null) {
                   $('#lokasi_' + id).append('<option value="' + item.detail_lokasi + '">' + item.detail_lokasi + '</option>');
                }
            });
        }

        function populateSelectProduk(response, id) {
            var selectProduk = $('#produk_' + id);

            selectProduk.attr('disabled', false);
            selectProduk.empty();
            selectProduk.append('<option value="">Pilih Produk</option>');

            response.forEach(function(item) {
                if (item.jenis != 'TAMBAHAN' && item.no_kembali_sewa == null) {
                    selectProduk.append('<option value="' + item.produk.kode + '" data-id="' + item.id + '">' + item.produk.nama + '</option>');
                }
            });
        }

        function populateKomponen(item, id) {
            var komponenContainer = $('#komponen_' + id);
            komponenContainer.empty();

            var jmlKomponen = 0;
            var jumlah_kembali = {{ count($data2) }};
            item.komponen.forEach(function(komponen, index) {
                if ((item.jenis == null || id < jumlah_kembali) && (komponen.tipe_produk == 1 || komponen.tipe_produk == 2) && (item.no_kembali_sewa == null || id < jumlah_kembali)) {
                    var komponenRow = '<div class="row mt-2">' +
                                        '<div class="col">' +
                                            '<select id="namaKomponen_' + id + '_' + index + '" name="namaKomponen[]" class="form-control" required readonly>' +
                                                '<option value="' + komponen.kode_produk + '">' + komponen.nama_produk + '</option>' +
                                            '</select>' +
                                        '</div>' +
                                        '<div class="col">' +
                                            '<select id="kondisiKomponen_' + id + '_' + index + '" name="kondisiKomponen[]" class="form-control">' +
                                                '<option value="">Pilih Kondisi</option>' +
                                                '@foreach ($kondisi as $item)' +
                                                    '<option value="{{ $item->id }}">{{ $item->nama }}</option>' +
                                                '@endforeach' +
                                            '</select>' +
                                        '</div>' +
                                        '<div class="col">' +
                                            '<input type="number" name="jumlahKomponen[]" id="jumlahKomponen_' + id + '_' + index + '" class="form-control" value="" required>' +
                                        '</div>' +
                                    '</div>';
                    komponenContainer.append(komponenRow);
                    jmlKomponen++;
                    $('#jumlahKomponen_' + id + '_' + index).val(komponen.jumlah);
                    $('#kondisiKomponen_' + id + '_' + index).val(komponen.kondisi).trigger('change');
                    $('#kondisiKomponen_' + id + '_' + index).select2();
                }
            });

            if (item.jenis == null) {
                komponenContainer.append('<input type="hidden" name="indexKomponen[]" value="' + jmlKomponen + '">');
            }
        }

        function resetFields(id) {
            $('#komponen_' + id).empty();
            $('#jumlah_' + id).attr('disabled', true);
            $('#lokasi_' + id).attr('disabled', true);
            $('#jumlah_' + id).val(0);
            $('#lokasi_' + id).val('').trigger('change');
        }

        function resetProduk(id){
            $('#produk_' + id).attr('disabled', true);
            $('#produk_' + id).val('').trigger('change');
            resetFields(id)
        }

        async function getProdukDo(id) {
            var data = {
                no_do: $('#no_do_produk_' + id).find(':selected').val(),
            };

            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '/getProdukDo',
                    type: 'GET',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        resolve(response);
                    },
                    error: function(xhr, status, error) {
                        reject(error);
                    }
                });
            });
        }

        function clearFile(){
            $('#bukti').val('');
            $('#preview').attr('src', defaultImg);
        };
    </script>
@endsection