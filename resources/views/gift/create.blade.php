@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Tambah Produk Gift</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm">
                        <form id="addForm" action="{{ route('gift.store') }}" method="POST">
                            @csrf
                            <div class="form-row row">
                                <div class="col-md-4 mb-3">
                                    <label for="kode">Kode</label>
                                    <input type="text" class="form-control" id="kode" name="kode" placeholder="Kode Produk" value="{{ $getKode }}" readonly required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="nama">Nama</label>
                                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Produk" value="{{ old('nama') }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="tipe_produk">Tipe Produk</label>
                                    <input type="text" class="form-control" id="tipe_produk" name="tipe_produk" value="gift" readonly required>
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="col-md-6 mb-3">
                                    <label for="harga">Harga Pokok</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="inputGroupPrepend2">Rp</span>
                                        <input type="text" class="form-control" id="harga" name="harga" placeholder="Harga Produk" value="{{ old('harga') }}" aria-describedby="inputGroupPrepend2" required readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="harga_jual">Harga Jual</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="inputGroupPrepend2">Rp</span>
                                        <input type="text" class="form-control" id="harga_jual" name="harga_jual" placeholder="Harga Jual Produk" value="{{ old('harga_jual') }}" aria-describedby="inputGroupPrepend2" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea rows="5" cols="5" class="form-control" placeholder="Deskripsi Produk" name="deskripsi" required>{{ old('deskripsi') }}</textarea>
                                </div>
                            </div>
                            <div class="form-row row">
                                <label>List Produk</label>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Kondisi</th>
                                                <th>Harga Satuan</th>
                                                <th>Jumlah</th>
                                                <th>Harga Total</th>
                                                <th style="min-width: 50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="dynamic_field">
                                            @foreach(old('kode_produk', ['']) as $key => $kodeProdukOld)
                                            <tr>
                                                <td>
                                                    <select id="kode_produk_{{ $key }}" name="kode_produk[]" class="form-control" required>
                                                        <option value="">Pilih Produk</option>
                                                        @foreach ($produks as $produk)
                                                            <option value="{{ $produk->kode }}" {{ $produk->kode == old('kode_produk.'.$key) ? 'selected' : '' }}>
                                                                {{ $produk->nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select id="kondisi_{{ $key }}" name="kondisi[]" class="form-control" required>
                                                        <option value="">Pilih Kondisi</option>
                                                        @foreach ($kondisi as $item)
                                                            <option value="{{ $item->id }}" {{ $item->id == old('kondisi.'.$key) ? 'selected' : '' }}>
                                                                {{ $item->nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="harga_satuan[]" id="harga_satuan_{{ $key }}" value="{{ old('harga_satuan.'.$key) }}" oninput="multiply({{ $key }})" class="form-control" required>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="jumlah[]" id="jumlah_{{ $key }}" value="{{ old('jumlah.'.$key) }}" oninput="multiply({{ $key }})" class="form-control" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="harga_total[]" id="harga_total_{{ $key }}" value="{{ old('harga_total.'.$key) }}" class="form-control" required readonly>
                                                </td>
                                                <td>
                                                    @if($key == 0)
                                                    <a href="javascript:void(0);" id="add"><img src="{{ asset('assets/img/icons/plus.svg') }}" style="color: #90ee90" alt="svg"></a>
                                                    @else
                                                    <a href="javascript:void(0);" class="btn_remove" id="{{ $key }}"><img src="{{ asset('assets/img/icons/delete.svg') }}" alt="svg"></a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <a href="{{ route('gift.index') }}" class="btn btn-secondary" type="button">Back</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('[id^=kode_produk], [id^=kondisi]').select2();
            var i = 1;
            $('#add').click(function() {
                var newRow = '<tr class="tr_clone" id="row' + i + '">' +
                '<td>' +
                '<select id="kode_produk_' + i + '" name="kode_produk[]" class="form-control select2" required>' +
                '<option value="">Pilih Produk</option>' +
                '@foreach ($produks as $produk)' +
                '<option value="{{ $produk->kode }}">{{ $produk->nama }}</option>' +
                '@endforeach' +
                '</select>' +
                '</td>' +
                '<td>' +
                '<select id="kondisi_' + i + '" name="kondisi[]" class="form-control select2" required>' +
                '<option value="">Pilih Kondisi</option>' +
                '@foreach ($kondisi as $item)' +
                '<option value="{{ $item->id }}">{{ $item->nama }}</option>' +
                '@endforeach' +
                '</select>' +
                '</td>' +
                '<td><input type="text" name="harga_satuan[]" id="harga_satuan_' + i + '" oninput="multiply(' + i + ')" class="form-control" required></td>' +
                '<td><input type="number" step="0.01" name="jumlah[]" id="jumlah_' + i + '" oninput="multiply(' + i + ')" class="form-control" required></td>' +
                '<td><input type="text" name="harga_total[]" id="harga_total_' + i + '" class="form-control" required readonly></td>' +
                '<td><a href="javascript:void(0);" class="btn_remove" id="'+ i +'"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td>' +
                '</tr>';
                $('#dynamic_field').append(newRow);
                $('#kode_produk_' + i + ', #kondisi_' + i).select2();
                i++
            });
            $(document).on('input', '[id^=harga]', function() {
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
                multiply(0)
            });
            $('#addForm').on('submit', function(e) {
                let inputs = $('#addForm').find('[id^=harga]');
                inputs.each(function() {
                    let input = $(this);
                    let value = input.val();
                    let cleanedValue = cleanNumber(value);
                    let formattedHargaPokok = cleanedValue.replace(',', '.');

                    input.val(formattedHargaPokok);
                });

                return true;
            });
        });

        function multiply(id){
            let harga_satuan = cleanNumber($('#harga_satuan_'+id).val())
            let jumlah = $('#jumlah_'+id).val()

            let harga_total = (harga_satuan * jumlah);
            let decimalPlaces = harga_total % 1 !== 0 ? 2 : 0;
            harga_total = harga_total.toFixed(decimalPlaces);

            let parts = harga_total.split('.');
            let formattedInteger = formatNumber(parts[0]);
            let formattedTotal = formattedInteger;
            if (parts[1] && parts[1] !== '00') { 
                formattedTotal += ',' + parts[1];
            }
            $('#harga_total_' + id).val(formattedTotal); 

            calculateHargaPokok()
        }

        function calculateHargaPokok(){
            var inputs = $('input[name="harga_total[]"]');
            var total = 0;
            inputs.each(function() {
                let harga_total = cleanNumber($(this).val());
                let formattedHargaTotal = harga_total.replace(',', '.');
                total += parseFloat(formattedHargaTotal);
            });
            let decimalPlaces = total % 1 !== 0 ? 2 : 0;
            total = total.toFixed(decimalPlaces);
            let parts = total.split('.');
            let formattedInteger = formatNumber(parts[0]);
            let formattedTotal = formattedInteger;
            if (parts[1] && parts[1] !== '00') { 
                formattedTotal += ',' + parts[1];
            }
            $('#harga').val(formattedTotal)
        }
    </script>
@endsection