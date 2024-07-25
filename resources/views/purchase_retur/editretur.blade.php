@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Edit Retur Pembelian (Purchasing)</h5>
            </div>
            <div class="card-body">
               <form action="{{ route('retur_purchase.update', ['retur_id' => $data->id]) }}" method="POST" enctype="multipart/form-data" id="addForm">
                <div class="row">
                    <div class="col-sm">
                            @csrf
                            @method('PATCH')
                            <div class="row justify-content-around">
                                <div class="col-md-6 border rounded pt-3">
                                    <h5 class="card-title">Informasi Supplier</h5>
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Supplier</label>
                                            <select id="supplier_id" name="supplier_id" class="form-control" required readonly>
                                                <option value="{{ $invoice->pembelian->supplier_id }}">{{ $invoice->pembelian->supplier->nama }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Lokasi</label>
                                            <select id="lokasi_id" name="lokasi_id" class="form-control" required readonly>
                                                <option value="{{ $invoice->pembelian->lokasi_id }}">{{ $invoice->pembelian->lokasi->nama }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Catatan</label>
                                            <textarea type="text" id="catatan" name="catatan" class="form-control">{{ old('catatan', $data->catatan) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 border rounded pt-3">
                                    <h5 class="card-title">Detail Retur</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Tanggal PO</label>
                                                <input type="text" id="tanggal_po" name="tanggal_po" value="{{ old('tanggal_po') ?? tanggalindo($invoice->pembelian->tgl_dibuat) }}" class="form-control" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Invoice</label>
                                                <input type="text" id="tanggal_invoice" name="tanggal_invoice" value="{{ old('tanggal_invoice') ?? tanggalindo($invoice->tgl_inv) }}" class="form-control" required readonly>
                                            </div>
                                            <input type="hidden" name="invoicepo_id" value="{{ $invoice->id }}">
                                            <div class="form-group">
                                                <label>Tanggal Retur</label>
                                                <input type="date" id="tgl_retur" name="tgl_retur" value="{{ $data->tgl_retur}}" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Komplain</label>
                                                <select id="komplain" name="komplain" class="form-control" required>
                                                    @if($data->komplain == 'Refund')
                                                    <option value="Refund" {{ $data->komplain == 'Refund' ? 'selected' : '' }}>Refund</option>
                                                    @endif
                                                    @if($data->komplain == 'Diskon' || $data->komplain == 'Retur')
                                                    <option value="Diskon" {{ $data->komplain == 'Diskon' ? 'selected' : '' }}>Diskon</option>
                                                    <option value="Retur" {{ $data->komplain == 'Retur' ? 'selected' : '' }}>Retur</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>No PO</label>
                                                <input type="text" id="no_po" name="no_po" value="{{ $invoice->pembelian->no_po }}" class="form-control" required readonly>
                                            </div>
                                            <!-- <div class="form-group">
                                                <label>No PO Retur</label>
                                                <input type="text" id="no_po_retur" name="no_po_retur" class="form-control" required readonly>
                                            </div> -->
                                            <div class="form-group">
                                                <label>No Invoice</label>
                                                <input type="text" id="no_invoice" name="no_invoice" value="{{ $invoice->no_inv }}" class="form-control" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>No Retur</label>
                                                <input type="text" id="no_retur" name="no_retur" value="{{ $data->no_retur }}" class="form-control" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <div class="custom-file-container" data-upload-id="myFirstImage">
                                                    <label>File <a href="javascript:void(0)" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image">clear</a>
                                                    </label>
                                                    <label class="custom-file-container__custom-file">
                                                        <input type="file" id="file" class="custom-file-container__custom-file__custom-file-input" name="file" accept="image/*">
                                                        <span class="custom-file-container__custom-file__custom-file-control"></span>
                                                    </label>
                                                    <span class="text-danger">max 2mb</span>
                                                    <img id="preview" src="{{ $data->foto ? '/storage/' . $data->foto : '' }}" alt="your image" />
                                                </div>
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
                                        <th>No</th>
                                        {{-- <th>Kode Produk</th> --}}
                                        <th>Nama Produk</th>
                                        <th>Alasan</th>
                                        <th>Jumlah</th>
                                        <th id="thDiskon">Diskon</th>
                                        <th>Harga satuan</th>
                                        <th>Harga Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="dynamic_field">
                                @php $i = 0; @endphp
                                @foreach($data->produkretur as $komponen)
                                    <tr>
                                        
                                        <td>1</td>
                                        <input type="hidden" name="kode_produk[]" id="kode_produk_{{ $i }}" class="form-control" required readonly>
                                        <td style="width: 250px;">
                                            <select id="produk_{{ $i }}" name="nama_produk[]" class="form-control produk-dropdown" required>
                                                <option value="">Pilih Produk</option>
                                                @foreach ($invoice->pembelian->produkbeli as $produk)
                                                    <option value="{{ $produk->id }}" data-jumlah="{{ $produk->jml_diterima }}" data-harga="{{ $produk->harga }}" data-diskon="{{ $produk->diskon }}" data-harga_total="{{ $produk->totalharga }}" {{ $produk->id == $komponen->produkbeli->id ? 'selected' : '' }}>
                                                    {{ $produk->produk->nama }} ({{$produk->kondisi->nama}})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><textarea name="alasan[]" id="alasan_{{ $i }}" class="form-control" cols="30" value="{{ $komponen->alasan}}">{{ $komponen->alasan}}</textarea></td>
                                        <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" oninput="multiply(this)" class="form-control jumlah_diterima"   value="{{ $komponen->jumlah}}" data-produk-id="{{ $produk->id }}" required></td>
                                        <td id="tdDiskon_{{ $i }}"><input type="text" name="diskon[]" id="diskon_{{ $i }}" oninput="multiply(this)" class="form-control" required></td>
                                        <td><input type="text" name="harga_satuan[]" id="harga_satuan_{{ $i }}" oninput="multiply(this)" class="form-control" value="{{ $komponen->harga}}" required readonly></td>
                                        <td><input type="text" name="harga_total[]" id="harga_total_{{ $i }}" class="form-control" value="{{ $komponen->totharga}}" required readonly></td>
                                        <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td>
                                       
                                    </tr>
                                    @php $i++; @endphp
                                    @endforeach
                                    
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm">
                        <div class="row justify-content-around">
                            <div class="col-lg-8 col-md-8 col-sm-6 col-6 border rounded mt-3 pt-3">
                                <div class="page-btn">
                                    Riwayat Pembayaran
                                    {{-- <a href="" data-toggle="modal" data-target="#myModalbayar" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Pembayaran</a> --}}
                                </div>
                                <div class="table-responsive">
                                    <table class="table datanew">
                                        <thead>
                                            <tr>
                                                <th>No Bayar</th>
                                                <th>Tanggal</th>
                                                <th>Metode</th>
                                                <th>Nominal</th>
                                                <th>Bukti</th>
                                                <th>Status</th>
                                                {{-- <th>Aksi</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- @foreach ($datapos as $datapo)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $datapo->no_po }}</td>
                                                <td>{{ $datapo->supplier->nama }}</td>
                                                <td>{{ $datapo->tgl_kirim }}</td>
                                                <td>{{ $datapo->tgl_diterima}}</td>
                                                <td>{{ $datapo->no_do_suplier}}</td>
                                                <td>{{ $datapo->lokasi->nama}}</td>
                                                <td>{{ $datapo->status_dibuat}}</td>
                                               
                                            </tr>
                                            @endforeach --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4 border rounded mt-3 pt-3">
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Subtotal</label>
                                    <div class="col-lg-9">
                                        <input type="text" id="subtotal" name="subtotal" value="{{ $data->subtotal }}" class="form-control"  required readonly>
                                    </div>
                                </div>
                                <div class="form-group row mt-1" id="divOngkir">
                                    <label class="col-lg-3 col-form-label">Biaya Pengiriman</label>
                                    <div class="col-lg-9">
                                        <input type="text" id="biaya_pengiriman" name="biaya_pengiriman" value="{{ number_format($data->ongkir, 0, ',', '.',) }}" class="form-control"  required>
                                    </div>
                                </div>

                                <div class="form-group row mt-1" style="display: none;">
                                    <label class="col-lg-3 col-form-label">Total Harga</label>
                                    <div class="col-lg-9">
                                        <input type="text" id="total_harga" name="total_harga" value="{{ old('total_harga') }}" class="form-control"  required readonly>
                                    </div>
                                </div>
                                

                            </div>
                        </div>
                        <div class="row justify-content-start">
                            <div class="col-md-6 border rounded pt-3 me-1 mt-2">
                                <div class="table-responsive">
                                    <table class="table table-responsive border rounded">
                                            @php
                                                $user = Auth::user();
                                            @endphp
                                            <thead>
                                                <tr>
                                                    @if($user->hasRole(['Purchasing']))
                                                        <th>Dibuat</th>  
                                                    @elseif($user->hasRole(['Finance']))                                     
                                                        <th>Dibukukan</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    
                                                    @if($user->hasRole(['Purchasing']))
                                                    <td id="pembuat">
                                                        <input type="text" class="form-control" value="{{ $pembuat }} ({{$pembuatjbt}})" >
                                                    </td>
                                                    <td id="pembuku">
                                                        @if (!$pembuku )
                                                        <input type="text" class="form-control" value="Nama (Finance)"  disabled>
                                                        @else
                                                        <input type="text" class="form-control" value="{{ $pembuku }} ({{ $pembukujbt }})"  disabled>
                                                        @endif
                                                    </td>
                                                    @elseif($user->hasRole(['Finance']))
                                                    <td id="pembuat">
                                                        <input type="text" class="form-control" value="{{ $pembuat }} ({{$pembuatjbt}})"  disabled>
                                                    </td>
                                                    <td id="pembuku">
                                                        <input type="text" class="form-control" value="{{ Auth::user()->name}}"  >
                                                    </td>
                                                    @endif
                                                </tr>
                                                <tr>
                                                    @if($user->hasRole(['Purchasing']))
                                                    <td id="status_dibuat">
                                                        <select id="status" name="status_dibuat" class="form-control select2" required>
                                                            <option value="">Pilih Status</option>
                                                            <option value="TUNDA" {{$data->status_dibuat == 'TUNDA' ||$data->status_dibuat == '' ? 'selected' : '' }}>TUNDA</option>
                                                            <option value="DIKONFIRMASI" {{$data->status_dibuat == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                        </select>
                                                    </td>
                                                    <td id="status_dibuku">
                                                        <input type="text" class="form-control" id="status_dibuku" value="{{ $data->status_dibuku ?? '-' }}" disabled>
                                                    </td>
                                                    @elseif($user->hasRole(['Finance']))
                                                    <td id="status_dibuat">
                                                        <select id="status" name="status_dibuat" class="form-control select2" required disabled>
                                                            <option value="">Pilih Status</option>
                                                            <option value="TUNDA" {{$data->status_dibuat == 'TUNDA' ||$data->status_dibuat == '' ? 'selected' : '' }}>TUNDA</option>
                                                            <option value="DIKONFIRMASI" {{$data->status_dibuat == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                        </select>
                                                    </td>
                                                    <td id="status_dibuku">
                                                        <!-- <input type="text" class="form-control" id="status_dibuku" value="{{ $data->status_dibuku ?? '-' }}" readonly> -->
                                                        <select id="status_dibuku" name="status_dibuku" class="form-control select2" required>
                                                            <option value="">Pilih Status</option>
                                                            <option value="TUNDA" {{$data->status_dibuku == 'TUNDA' ||$data->status_dibuku == '' ? 'selected' : '' }}>TUNDA</option>
                                                            <option value="TUNGGUBAYAR" {{$data->status_dibuku == 'TUNGGUBAYAR' ||$data->status_dibuku == '' ? 'selected' : '' }}>TUNGGU BAYAR</option>
                                                            <option value="DIKONFIRMASI" {{$data->status_dibuku == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                        </select>
                                                        
                                                    </td>
                                                    @endif
                                                </tr>
                                                <tr>
                                                    @if($user->hasRole(['Purchasing']))
                                                    <td id="tgl_dibuat">
                                                        <input type="date" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{ $data->tgl_dibuat ?? now()->format('Y-m-d') }}" >
                                                    </td>
                                                    <td id="tgl_dibuku">
                                                        <input type="text" class="form-control" id="tgl_dibuku" name="tgl_dibuku" value="{{$data->tgl_dibuku ?? '-'}}" disabled>
                                                    </td>
                                                    @elseif($user->hasRole(['Finance']))
                                                    <td id="tgl_dibuat">
                                                        <input type="date" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{ $data->tgl_dibuat ?? now()->format('Y-m-d') }}" disabled>
                                                    </td>
                                                    <td id="tgl_dibuku">
                                                        <input type="text" class="form-control" id="tgl_dibuku" name="tgl_dibuku" value="{{$data->tgl_dibuku ?? now()->format('Y-m-d')}}">
                                                    </td>
                                                    @endif
                                                </tr>
                                            </tbody>
                                        </table>  
                                        <br>
                                </div>                                 
                               </div>
                         </div>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button class="btn btn-primary" type="submit">Submit</button>
                    <a href="{{ route('returbeli.index') }}" class="btn btn-secondary" type="button">Back</a>
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
        $(document).ready(function() {
            if ($('#preview').attr('src') === '') {
                $('#preview').attr('src', defaultImg);
            }
            displayDskon(false);
            $('[id^=produk], #ongkir_id, #add_tipe, #komplain').select2();
            $('#komplain').trigger('change');
            var limitRow = {{ count($invoice->pembelian->produkbeli) }};
            var i = 1;
            $('#add').click(function(){
                if($('[id^=produk_]').length < limitRow){
                    var newRow = '<tr id="row'+i+'">'+
                                    '<td>'+(i + 1)+'</td>'+
                                    '<input type="hidden" name="kode_produk[]" id="kode_produk_'+i+'" class="form-control" required readonly>' +
                                    '<td>' + 
                                        '<select id="produk_'+i+'" name="nama_produk[]" class="form-control">'+
                                            '<option value="">Pilih Produk</option>'+
                                                '@foreach ($invoice->pembelian->produkbeli as $produk)' +
                                                    '<option value="{{ $produk->id }}" data-jumlah="{{ $produk->jml_diterima }}" data-harga="{{ $produk->harga }}" data-diskon="{{ $produk->diskon }}" data-harga_total="{{ $produk->totalharga }}">{{ $produk->produk->nama }} ({{$produk->kondisi->nama}})</option>' +
                                                '@endforeach' +
                                        '</select>'+
                                    '</td>'+
                                    '<td><textarea name="alasan[]" id="alasan_'+i+'" class="form-control" cols="30"></textarea></td>' +
                                    '<td><input type="number" name="jumlah[]" id="jumlah_'+i+'" oninput="multiply(this)" class="form-control"></td>'+
                                    '<td id="tdDiskon_'+i+'"><input type="text" name="diskon[]" id="diskon_'+i+'" oninput="multiply(this)" class="form-control" required></td>' +
                                    '<td><input type="text" name="harga_satuan[]" id="harga_satuan_'+i+'" oninput="multiply(this)" class="form-control" required readonly></td>'+
                                    '<td><input type="text" name="harga_total[]" id="harga_total_'+i+'" class="form-control" readonly></td>'+
                                    '<td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">x</button></td></tr>';
                        $('#dynamic_field').append(newRow);
                        $('#produk_' + i).select2();
                        i++;
                    var jenis = $('#komplain').val();
                        if(jenis == 'Diskon'){
                            displayDskon(true);
                        } else {
                            displayDskon(false);
                        }
                }
            })
            $(document).on('input', '[id^=biaya_pengiriman], [id^=diskon_]', function() {
                let input = $(this);
                let value = input.val();
                
                if (!isNumeric(cleanNumber(value))) {
                value = value.replace(/[^\d]/g, "");
                }

                value = cleanNumber(value);
                let formattedValue = formatNumber(value);
                
                input.val(formattedValue);
            });
            $(document).on('click', '.btn_remove', function() {
                var button_id = $(this).attr("id");
                $('#row'+button_id+'').remove();
                multiply($('#diskon_0'));
            });
            // $('#total_promo, #ppn_persen, #pph_persen').on('input', function(){
            //     // total_harga();
            // })
            $('#addForm').on('submit', function(e) {
                // Add input number cleaning for specific inputs
                let inputs = $('#addForm').find('[id^=harga_satuan], [id^=harga_total], [id^=diskon_], #subtotal, #total_promo, #ppn_nominal, #pph_nominal, #total_harga, #biaya_pengiriman');
                inputs.each(function() {
                    let input = $(this);
                    let value = input.val();
                    let cleanedValue = cleanNumber(value);

                    // Set the cleaned value back to the input
                    input.val(cleanedValue);
                });

                return true;
            });
        });

        var status_dibuku = "{{ $data->status_dibuku }}";
        $('#status_dibuku').val(status_dibuku).trigger('change');
        statusdibuku();


        function statusdibuku() {
            var status = $('#status_dibuku').val();
            if(status == "TUNGGUBAYAR") {
                $('#catatan, #tgl_retur, #biaya_pengiriman').prop('readonly', true);
                $('#komplain , #file').prop('disabled', true);
                $('[id^=produk]').prop('disabled', true);
                $('[id^=alasan]').prop('disabled', true);
                $('[id^=jumlah]').prop('disabled', true);
            }else if(status == "TUNDA") {
                $('#catatan, #tgl_retur, #biaya_pengiriman').prop('readonly', false);
                $('#komplain, #file').prop('disabled', false);
                $('[id^=produk]').prop('disabled', false);
                $('[id^=alasan]').prop('disabled', false);
                $('[id^=jumlah]').prop('disabled', false);
            }
        }

        $(document).on('change', '[id^=produk_]', function(){
            var id = $(this).attr('id');
            var parts = id.split('_');
            var nomor = parts[parts.length - 1];
            var kode = $(this).val();
            if(kode){
                var jumlah = $(this).find(':selected').data('jumlah');
                var diskon = $(this).find(':selected').data('diskon');
                var harga = $(this).find(':selected').data('harga');
                var harga_total = $(this).find(':selected').data('harga_total');
                $('#kode_produk_' + nomor).val(kode);
                $('#jumlah_' + nomor).val(jumlah);
                $('#harga_satuan_' + nomor).val(formatNumber((harga - diskon)));
                $('#harga_total_' + nomor).val(formatNumber(harga_total));
            } else {
                $('#kode_produk_' + nomor).val('');
                $('#jumlah_' + nomor).val('');
                $('#harga_satuan_' + nomor).val('');
                $('#harga_total_' + nomor).val('');
            }
            multiply(this);
        });
        $(document).on('change', '#komplain', function(){
            var jenis = $(this).val();
            if(jenis == 'Diskon'){
                displayDskon(true);
                displayOngkir(false);
            } else {
                displayDskon(false);
                displayOngkir(true);
            }
            inputDiskon = $('[id^=diskon_]').each(function(){
                $(this).val('');
                multiply(this);
            })
        })
        $(document).on('input', '#biaya_pengiriman', function(){
            total_harga();
        })
        $(document).on('input', '[id^=diskon_]', function(){
            multiply(this);
        })
        function displayDskon(isDisplay) {
            if (isDisplay) {
                $('#thDiskon').show();
                $('[id^=tdDiskon_]').show();
                $('[id^=diskon_]').attr('required', true);
            } else {
                $('#thDiskon').hide();
                $('[id^=tdDiskon_]').hide();
                $('[id^=diskon_]').attr('required', false);
            }
        }

        function displayOngkir(isDisplay){
            if (isDisplay) {
                $('[id^=divOngkir]').show();
                $('[id^=biaya_pengiriman]').attr('required', true);
            } else {
                $('[id^=divOngkir]').hide();
                $('[id^=biaya_pengiriman]').attr('required', false);
            }
        }

        //disabled option dari select
        function updateOptions() {
            let selectedValues = [];
            $('.produk-dropdown').each(function() {
                if ($(this).val()) {
                    selectedValues.push($(this).val());
                }
            });

            $('.produk-dropdown option').prop('disabled', false);

            $('.produk-dropdown').each(function() {
                let $dropdown = $(this);
                selectedValues.forEach(function(value) {
                    if ($dropdown.val() !== value) {
                        $dropdown.find('option[value="' + value + '"]').prop('disabled', true);
                    }
                });
            });
        }

        updateOptions();

        $('.produk-dropdown').on('change', function() {
            updateOptions();
        });

        function multiply(element) {
            var id = 0
            var jenis = $(element).attr('id')
            var diskon = 0;
            var new_harga_total = 0;
            if(jenis.split('_').length == 2){
                id = $(element).attr('id').split('_')[1];
                var harga_total = $('#produk_' + id).find(':selected').data('harga_total');
                diskon = cleanNumber($('#diskon_' + id).val()) || 0;
                jumlah = $('#jumlah_' + id).val() || 0;
                harga_satuan = cleanNumber($('#harga_satuan_' + id).val());
                if (harga_satuan) {
                    new_harga_total = (harga_satuan - diskon) * jumlah
                    $('#harga_total_'+id).val(formatNumber(new_harga_total))
                }
            }

            var inputs = $('input[name="harga_total[]"]');
            var total = 0;
            var test = 0
            inputs.each(function() {
                test++;
                total += parseInt(cleanNumber($(this).val())) || 0;
            });
            $('#subtotal').val(formatNumber(total))
            total_harga();
        }
        function total_harga() {
            var subtotal = cleanNumber($('#subtotal').val()) || 0;
            var biaya_pengiriman = cleanNumber($('#biaya_pengiriman').val()) || 0;
            var harga_total = parseInt(subtotal) + parseInt(biaya_pengiriman);
            $('#total_harga').val(formatNumber(harga_total));
        }

      

        var produkData = [];

        @foreach ($invoice->pembelian->produkbeli as $produk)
            produkData.push({
                id: {{ $produk->id }},
                jumlah: {{ $produk->jml_diterima }}
            });
        @endforeach

        // console.log('Produk Data:', produkData);

        $(document).on('input', '.jumlah_diterima', function() {
            var inputId = $(this).attr('id');
            var jumlah = parseInt($(this).val(), 10); 
            var produkId = $(this).data('produk-id');

            var produk = produkData.find(function(item) {
                return item.id == produkId;
            });

            if (produk) {
                if (jumlah > produk.jumlah) {
                    toastr.warning('jumlah tidak boleh lebih dari jumlah diterima', {
                        closeButton: true,
                        tapToDismiss: false,
                        rtl: false,
                        progressBar: true
                    });
                    $(this).val(produk.jumlah);
                } else if (jumlah < 0) {
                    toastr.warning('jumlah tidak boleh kurang dari 0', {
                        closeButton: true,
                        tapToDismiss: false,
                        rtl: false,
                        progressBar: true
                    });
                    $(this).val(0);
                }
            } else {
                console.error('Produk not found for ID:', produkId);
            }
        });
        $('#file').on('change', function() {
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
    </script>
@endsection