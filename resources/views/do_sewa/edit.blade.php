@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h5 class="card-title">Edit Delivery order</h5>
                    </div>
                    <div class="page-btn">
                        <a href="#" class="btn btn-added">Cetak DO</a>
                    </div>
                </div>
            </div>
            <form action="{{ route('do_sewa.update', ['do_sewa' => $data->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('patch')
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
                                            <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') ?? $data->customer->nama }}" class="form-control" readonly>
                                            <input type="hidden" id="customer_id" name="customer_id" value="{{ old('customer_id') ?? $data->customer_id }}" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>PIC</label>
                                            <input type="text" id="pic" name="pic" value="{{ old('pic') ?? $data->pic }}" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Handphone</label>
                                            <input type="text" id="handhpone" name="handphone" value="{{ old('handphone') ?? $data->handphone }}" class="form-control" required oninput="validatePhoneNumber(this)">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Alamat</label>
                                            <textarea type="text" id="alamat" name="alamat" class="form-control" required>{{ old('alamat') ?? $data->alamat }}</textarea>
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
                                            <input type="text" id="no_do" name="no_do" value="{{ old('no_do') ?? $data->no_do }}" class="form-control" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Tanggal Kirim</label>
                                            <input type="date" id="tanggal_kirim" name="tanggal_kirim" value="{{ old('tanggal_kirim') ?? $data->tanggal_kirim }}" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>No Kontrak</label>
                                            <input type="text" id="no_referensi" name="no_referensi" value="{{ old('no_referensi') ?? $data->no_referensi }}" class="form-control"  readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Driver</label>
                                            <select id="driver_id" name="driver" class="form-control" required>
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
                                            <textarea type="text" id="catatan" name="catatan" class="form-control">{{ old('catatan') ?? $data->catatan }}</textarea>
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
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="dynamic_field">
                                @if(count($data->produk) < 1)
                                <tr>
                                    <td>
                                        <select id="produk_0" name="nama_produk[]" class="form-control" required>
                                            <option value="">Pilih Produk</option>
                                            @foreach ($produkSewa as $produk)
                                                <option value="{{ $produk->kode }}" data-id="{{ $produk->id }}">{{ $produk->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="jumlah[]" id="jumlah_0" class="form-control"></td>
                                    <td><input type="number" name="satuan[]" id="satuan_0" class="form-control"></td>
                                    <td><input type="number" name="detail_lokasi[]" id="detail_lokasi_0" class="form-control"></td>
                                    <td><a href="javascript:void(0);" id="add"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></a></td>
                                </tr>
                                @else
                                @php
                                $i = 0;
                                @endphp
                                @foreach ($data->produk as $produk)
                                @if ($produk->jenis == null)
                                    <tr id="row{{ $i }}">
                                        <td>
                                            <select id="produk_{{ $i }}" name="nama_produk[]" class="form-control" required>
                                                <option value="">Pilih Produk</option>
                                                @foreach ($produkSewa as $ps)
                                                @php
                                                if($ps->produk->tipe_produk == 6){

                                                    $descArray = [];
                                                    foreach ($ps->komponen as $komponen) {
                                                        if (in_array($komponen->tipe_produk, [1, 2])) {
                                                            $descArray[] = $komponen->produk->nama;
                                                        }
                                                    }
                                                    $desc = implode(', ', $descArray);
                                                } else {
                                                    $desc = '';
                                                }
                                                @endphp
                                                    <option value="{{ $ps->produk->kode }}" data-id="{{ $ps->id }}" data-tipe_produk="{{ $ps->produk->tipe_produk }}"
                                                    @if ($ps->produk->tipe_produk == 6)
                                                        data-tooltip="{{ $desc }}"
                                                    @endif    
                                                    {{ $ps->produk->kode == $produk->produk->kode ? 'selected' : '' }}>{{ $ps->produk->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" class="form-control" value="{{ $produk->jumlah }}" required></td>
                                        <td><input type="text" name="satuan[]" id="satuan_{{ $i }}" class="form-control" value="{{ $produk->satuan }}" required></td>
                                        <td><input type="text" name="detail_lokasi[]" id="detail_lokasi_{{ $i }}" class="form-control" value="{{ $produk->detail_lokasi }}" required></td>
                                        @if ($i == 0)
                                            <td><a href="javascript:void(0);" id="add"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></a></td>
                                        @else
                                            <td><a href="javascript:void(0);" class="btn_remove" id="{{ $i }}"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td>
                                        @endif
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
                                    <th><a href="javascript:void(0);" id="add2"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></th>
                                </tr>
                            </thead>
                            <tbody id="dynamic_field2">
                                @if(count($data->produk) < 1)
                                {{-- <tr id="row2{{ $i }}">
                                    <td>
                                        <select id="produk2_{{ $i }}" name="nama_produk2[]" class="form-control" disabled>
                                            <option value="">Pilih Produk</option>
                                            @foreach ($produkJuals as $produk)
                                                <option value="{{ $produk->kode }}">{{ $produk->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="jumlah2[]" id="jumlah2_{{ $i }}" class="form-control"></td>
                                    <td><input type="number" name="satuan[]" id="satuan_{{ $i }}" class="form-control"></td>
                                    <td><input type="number" name="keterangan[]" id="keterangan_{{ $i }}" class="form-control"></td>
                                </tr> --}}
                                @else
                                @php
                                $hasTambahan = false;
                                @endphp
                                @foreach ($data->produk as $produk)
                                @if ($produk->jenis == 'TAMBAHAN')
                                    <tr id="row2{{ $i }}">
                                        <td>
                                            <select id="produk2_{{ $i }}" name="nama_produk2[]" class="form-control">
                                                <option value="">Pilih Produk</option>
                                                @foreach ($produkJuals as $pj)
                                                    <option value="{{ $pj->kode }}" data-id="{{ $pj->id }}" data-tipe_produk="{{ $pj->tipe_produk }}" {{ $pj->kode == $produk->produk->kode ? 'selected' : '' }}>{{ $pj->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="jumlah2[]" id="jumlah2_{{ $i }}" class="form-control" value="{{ $produk->jumlah }}"></td>
                                        <td><input type="text" name="satuan2[]" id="satuan2_{{ $i }}" class="form-control" value="{{ $produk->satuan }}"></td>
                                        <td><input type="text" name="keterangan2[]" id="keterangan2_{{ $i }}" class="form-control" value="{{ $produk->keterangan }}"></td>
                                        <td><a href="javascript:void(0);" class="btn_remove2" id="{{ $i }}"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td>
                                        @php
                                            $i++;
                                            $hasTambahan = true;
                                        @endphp
                                    </tr>
                                @endif 
                                @php
                                    $i++;
                                @endphp
                                @endforeach
                                @if(!$hasTambahan)
                                {{-- <tr id="row2{{ $i }}">
                                    <td>
                                        <select id="produk2_{{ $i }}" name="nama_produk2[]" class="form-control">
                                            <option value="">Pilih Produk</option>
                                            @foreach ($produkJuals as $produk)
                                                <option value="{{ $produk->kode }}" data-id="{{ $produk->id }}">{{ $produk->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="jumlah2[]" id="jumlah2_{{ $i }}" class="form-control"></td>
                                    <td><input type="text" name="satuan2[]" id="satuan2_{{ $i }}" class="form-control"></td>
                                    <td><input type="text" name="keterangan2[]" id="keterangan2_{{ $i }}" class="form-control"></td>
                                    <td><a href="javascript:void(0);" class="btn_remove2" id="{{ $i }}"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td>
                                </tr> --}}
                                @endif
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
                                                    <td>{{ $item->subject->kontrak->customer->nama ?? '-' }}</td>
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
                                                                echo 'Data Delivery Order Terbuat';
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
                <a href="{{ route('do_sewa.index') }}" class="btn btn-secondary" type="button">Back</a>
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
            $('[data-toggle="tooltip"]').tooltip({ html: true });
            $('form').on('submit', function(event) {
                $('select[name="nama_produk[]"]').each(function() {
                    var selectedOption = $(this).find('option:selected');
                    var productId = selectedOption.data('id');
                    var hiddenInput = $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'produk_id[]')
                        .val(productId);
                    $(this).closest('form').append(hiddenInput);
                });
                $('select[name="nama_produk2[]"]').each(function() {
                    var selectedOption = $(this).find('option:selected');
                    var productId = selectedOption.data('id');
                    var hiddenInput = $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'produk_id2[]')
                        .val(productId);
                    $(this).closest('form').append(hiddenInput);
                });
            });
            if ($('#preview').attr('src') === '') {
                $('#preview').attr('src', defaultImg);
            }
            $('[id^=produk], #driver_id').select2({
                templateResult: formatState,
                templateSelection: formatState,
            });
            var i = '{{ count($data->produk) }}';
            $('#add').click(function(){
                var newRow = '<tr id="row' + i + '">' +
                '<td>' +
                    '<select id="produk_' + i + '" name="nama_produk[]" class="form-control">' +
                        '<option value="">Pilih Produk</option>';
                @foreach ($produkSewa as $ps)
                    @php
                    $desc = '';
                    if ($ps->produk->tipe_produk == 6) {
                        $descArray = [];
                        foreach ($ps->komponen as $komponen) {
                            if (in_array($komponen->tipe_produk, [1, 2])) {
                                $descArray[] = $komponen->produk->nama;
                            }
                        }
                        $desc = implode(', ', $descArray);
                    }
                    @endphp

                    newRow += '<option value="{{ $ps->produk->kode }}" data-id="{{ $ps->id }}" data-tipe_produk="{{ $ps->produk->tipe_produk }}"';
                    @if ($ps->produk->tipe_produk == 6)
                        newRow += ' data-tooltip="{{ $desc }}"';
                    @endif
                    newRow += '>';
                    newRow += '{{ $ps->produk->nama }}</option>';
                @endforeach

                newRow += '</select>' +
                                '</td>' +
                                '<td><input type="number" name="jumlah[]" id="jumlah_' + i + '" class="form-control"></td>' +
                                '<td><input type="text" name="satuan[]" id="satuan_' + i + '" class="form-control"></td>' +
                                '<td><input type="text" name="detail_lokasi[]" id="detail_lokasi_' + i + '" class="form-control"></td>' +
                                '<td><a href="javascript:void(0);" class="btn_remove" id="'+ i +'"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td>' +
                            '</tr>';
                $('#dynamic_field').append(newRow);
                $('#produk_' + i).select2({
                    templateResult: formatState,
                    templateSelection: formatState
                });
                i++;
            });
            $('#add2').click(function(){
                var newRow = '<tr id="row2'+i+'"><td>' + 
                                '<select id="produk2_'+i+'" name="nama_produk2[]" class="form-control">'+
                                    '<option value="">Pilih Produk</option>'+
                                    '@foreach ($produkJuals as $pj)'+
                                        '<option value="{{ $pj->kode }}" data-id="{{ $pj->id }}" data-tipe_produk="{{ $pj->tipe_produk }}">{{ $pj->nama }}</option>'+
                                    '@endforeach'+
                                '</select>'+
                            '</td>'+
                            '<td><input type="number" name="jumlah2[]" id="jumlah2_'+i+'" class="form-control"></td>'+
                            '<td><input type="text" name="satuan2[]" id="satuan2_'+i+'" class="form-control"></td>'+
                            '<td><input type="text" name="keterangan2[]" id="keterangan2_'+i+'" class="form-control"></td>'+
                            '<td><a href="javascript:void(0);" class="btn_remove2" id="'+ i +'"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td></tr>';
                $('#dynamic_field2').append(newRow);
                $('#produk2_' + i).select2();
                i++;
            });
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
        function clearFile(){
            $('#bukti').val('');
            $('#preview').attr('src', defaultImg);
        };
        function formatState(state) {
            if (!$(state.element).attr('data-tooltip')) {
                return state.text;
            }
            var $state = $(
                '<span>' + state.text + ' <i class="fas fa-info-circle ml-1" data-toggle="tooltip" title="' + $(state.element).attr('data-tooltip') + '"></i></span>'
            );
            return $state;
        }
    </script>
@endsection