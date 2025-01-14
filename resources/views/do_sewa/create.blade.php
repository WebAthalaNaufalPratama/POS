@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="page-header">
                    <div class="page-title">
                        <h5 class="card-title">Buat Delivery Order</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('do_sewa.store') }}" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm">
                            @csrf
                            <div class="row justify-content-around">
                                <div class="col-md-6 border rounded pt-3">
                                    <h5 class="card-title">Informasi Customer</h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Customer</label>
                                                <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') ?? $kontrak->customer->nama }}" class="form-control" required disabled>
                                                <input type="hidden" id="customer_id" name="customer_id" value="{{ old('customer_id') ?? $kontrak->customer_id }}" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>PIC</label>
                                                <input type="text" id="pic" name="pic" value="{{ old('pic') ?? $kontrak->pic }}" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Handphone</label>
                                                <input type="text" id="handhpone" name="handphone" value="{{ old('handphone') ?? $kontrak->handphone }}" class="form-control" required oninput="validatePhoneNumber(this)">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Alamat</label>
                                                <textarea type="text" id="alamat" name="alamat" class="form-control" required>{{ old('alamat') ?? $kontrak->alamat }}</textarea>
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
                                                <input type="text" id="no_do" name="no_do" value="{{ old('no_do') ?? $getKode }}" class="form-control" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Kirim</label>
                                                <input type="date" id="tanggal_kirim" name="tanggal_kirim" value="{{ old('tanggal_kirim') ?? $kontrak->tanggal_mulai }}" class="form-control" required min="{{ $kontrak->tanggal_mulai }}" max="{{ $kontrak->tanggal_selesai }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>No Kontrak</label>
                                                <input type="text" id="no_referensi" name="no_referensi" value="{{ old('no_referensi') ?? $kontrak->no_kontrak }}" class="form-control" required readonly>
                                            </div>
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
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Catatan</label>
                                                <textarea type="text" id="catatan" name="catatan" class="form-control">{{ old('catatan') }}</textarea>
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
                                    @if(count($kontrak->produk) < 1)
                                    <tr>
                                        <td>
                                            <select id="produk_0" name="nama_produk[]" class="form-control">
                                                <option value="">Pilih Produk</option>
                                                @foreach ($produkSewa as $produk)
                                                    <option value="{{ $produk->produk->kode }}" data-id="{{ $pj->id }}" data-tooltip="test">{{ $produk->produk->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="jumlah[]" id="jumlah_0" class="form-control"></td>
                                        <td><input type="number" name="harga_satuan[]" id="harga_satuan_0" class="form-control"></td>
                                        <td><input type="number" name="harga_total[]" id="harga_total_0" class="form-control"></td>
                                        <td><a href="javascript:void(0);" id="add"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></a></td>
                                    </tr>
                                    @else
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($kontrak->produk as $produk) 
                                    <tr id="row{{ $i }}">
                                        <td>
                                            <select id="produk_{{ $i }}" name="nama_produk[]" class="form-control">
                                                <option value="">Pilih Produk</option>
                                                @foreach ($produkSewa as $pj)
                                                    @php
                                                    if($pj->produk->tipe_produk == 6){

                                                        $descArray = [];
                                                        foreach ($pj->komponen as $komponen) {
                                                            if (in_array($komponen->tipe_produk, [1, 2])) {
                                                                $descArray[] = $komponen->produk->nama;
                                                            }
                                                        }
                                                        $desc = implode(', ', $descArray);
                                                    } else {
                                                        $desc = '';
                                                    }
                                                    @endphp
                                                     <option value="{{ $pj->produk->kode }}" data-id="{{ $pj->id }}" data-tipe_produk="{{ $pj->produk->tipe_produk }}"
                                                        @if ($pj->produk->tipe_produk == 6)
                                                            data-tooltip="{{ $desc }}"
                                                        @endif
                                                        {{ $pj->id == $produk->id ? 'selected' : '' }}>{{ $pj->produk->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" class="form-control" value="{{ old('jumlah.' . $i) ?? $produk->jumlah }}"></td>
                                        <td><input type="text" name="satuan[]" id="satuan_{{ $i }}" class="form-control" value="{{ old('satuan.' . $i) ?? 'pcs' }}"></td>
                                        <td><input type="text" name="detail_lokasi[]" id="detail_lokasi_{{ $i }}" class="form-control" value="{{ old('detail_lokasi.' . $i) }}" required></td>
                                        @if ($i == 0)
                                            <td><a href="javascript:void(0);" id="add"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></a></td>
                                        @else
                                            <td><a href="javascript:void(0);" class="btn_remove" id="{{ $i }}"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td>
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
                                    {{-- <tr id="row2{{ $i }}">
                                        <td>
                                            <select id="produk2_{{ $i }}" name="nama_produk2[]" class="form-control">
                                                <option value="">Pilih Produk</option>
                                                @foreach ($produkjuals as $produk)
                                                    <option value="{{ $produk->kode }}" data-id="{{ $produk->id }}">{{ $produk->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="jumlah2[]" id="jumlah2_{{ $i }}" class="form-control"></td>
                                        <td><input type="text" name="satuan2[]" id="satuan2_{{ $i }}" class="form-control"></td>
                                        <td><input type="text" name="keterangan2[]" id="keterangan2_{{ $i }}" class="form-control"></td>
                                        <td><a href="javascript:void(0);" class="btn_remove2" id="{{ $i }}"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td>
                                    </tr> --}}
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
                                            <td id="tgl_penyetuju" style="width: 25%;">{{ isset($kontrak->tanggal_penyetujju) ? \Carbon\Carbon::parse($kontrak->tanggal_penyetujju)->format('Y-m-d') : '-' }}</td>
                                            <td id="tgl_pemeriksa" style="width: 25%;">{{ isset($kontrak->tanggal_pemeriksa) ? \Carbon\Carbon::parse($kontrak->tanggal_pemeriksa)->format('Y-m-d') : '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4 border rounded mt-3 pt-3">
                                <div class="custom-file-container" data-upload-id="myFirstImage">
                                    <label>Bukti Kirim (Single File) <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">clear</a></label>
                                    <label class="custom-file-container__custom-file">
                                    <input type="file" name="file" class="custom-file-container__custom-file__custom-file-input" accept="image/*">
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
            $('[data-toggle="tooltip"]').tooltip({ html: true });
            $('form').on('submit', function(event) { // add request id
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

            $('[id^=produk], #driver_id').select2({
                templateResult: formatState,
                templateSelection: formatState,
            });
            $('#driver_id').trigger('change');
            var i = '{{ count($kontrak->produk) }}';
            $('#add').click(function(){
                var newRow = '<tr id="row' + i + '">' +
                '<td>' +
                    '<select id="produk_' + i + '" name="nama_produk[]" class="form-control">' +
                        '<option value="">Pilih Produk</option>';
                            @foreach ($produkSewa as $pj)
                                @php
                                if ($pj->produk->tipe_produk == 6) {
                                    $descArray = [];
                                    foreach ($pj->komponen as $komponen) {
                                        if (in_array($komponen->tipe_produk, [1, 2])) {
                                            $descArray[] = $komponen->produk->nama;
                                        }
                                    }
                                    $desc = implode(', ', $descArray);
                                } else {
                                    $desc = '';
                                }
                                @endphp

                                newRow += '<option value="{{ $pj->produk->kode }}" data-id="{{ $pj->id }}" data-tipe_produk="{{ $pj->produk->tipe_produk }}"';
                                @if ($pj->produk->tipe_produk == 6)
                                    newRow += ' data-tooltip="{{ $desc }}"';
                                @endif
                                newRow += '{{ $pj->id == $produk->id ? " selected" : "" }}>';
                                newRow += '{{ $pj->produk->nama }}</option>';
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
            })
            $('#add2').click(function(){
                var newRow = '<tr id="row2'+i+'"><td>' + 
                                '<select id="produk2_'+i+'" name="nama_produk2[]" class="form-control">'+
                                    '<option value="">Pilih Produk</option>'+
                                    '@foreach ($produkjuals as $pj)'+
                                        '<option value="{{ $pj->kode }}" data-id="{{ $pj->id }}" data-tipe_produk="{{ $pj->tipe_produk }}">{{ $pj->nama }}</option>'+
                                    '@endforeach'+
                                '</select>'+
                            '</td>'+
                            '<td><input type="number" name="jumlah2[]" id="jumlah2_'+i+'" class="form-control"></td>'+
                            '<td><input type="text" name="satuan2[]" id="satuan2_'+i+'" class="form-control"></td>'+
                            '<td><input type="text" name="detail_lokasi2[]" id="detail_lokasi2_'+i+'" class="form-control"></td>'+
                            '<td><a href="javascript:void(0);" class="btn_remove2" id="'+ i +'"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td></tr>';
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
            var nama_driver = $("#driver_id option:selected").text();
            var val_driver = $("#driver_id option:selected").val();
            if(val_driver != ""){
                $('#driver').text(nama_driver)
            } else {
                $('#driver').text('-')
            }
        });
        $(document).on('input', '[id^=handhpone]', function() {
            let input = $(this);
            let value = input.val();
            
            if (!isNumeric(value)) {
            value = value.replace(/[^\d]/g, "");
            }

            input.val(value);
        });
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