@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Tambah Produk Tradisional</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm">
                        <form action="{{ route('tradisional.store') }}" method="POST">
                            @csrf
                            <div class="form-row row">
                                <div class="col-md-4 mb-3">
                                    <label for="kode">Kode</label>
                                    <input type="text" class="form-control" id="kode" name="kode" placeholder="Kode Produk" value="{{ $getKode }}" readonly required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="nama">Nama</label>
                                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Produk" value="" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="tipe_produk">Tipe Produk</label>
                                    <input type="text" class="form-control" id="tipe_produk" name="tipe_produk" value="Tradisional" readonly required>
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="col-md-6 mb-3">
                                    <label for="harga">Harga</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="inputGroupPrepend2">Rp</span>
                                        <input type="number" class="form-control" id="harga" name="harga" placeholder="Harga Produk" value="" aria-describedby="inputGroupPrepend2" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="harga_jual">Harga Jual</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="inputGroupPrepend2">Rp</span>
                                        <input type="number" class="form-control" id="harga_jual" name="harga_jual" placeholder="Harga Jual Produk" value="" aria-describedby="inputGroupPrepend2" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea rows="5" cols="5" class="form-control" placeholder="Deskripsi Produk" name="deskripsi" required></textarea>
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
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="dynamic_field">
                                            <tr>
                                                <td>
                                                    <select id="nama_produk" name="nama_produk[]" class="form-control">
                                                        <option value="">Pilih Produk</option>
                                                        @foreach ($produks as $produk)
                                                            <option value="{{ $produk->id }}">{{ $produk->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="kode_produk[]" style="display: none;">
                                                    <input type="hidden" name="tipe_produk[]" style="display: none;">
                                                    <input type="hidden" name="deskripsi_komponen[]" style="display: none;">
                                                </td>
                                                <td>
                                                    <select id="kondisi" name="kondisi[]" class="form-control">
                                                        <option value="">Pilih Kondisi</option>
                                                        @foreach ($kondisi as $item)
                                                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="number" name="harga_satuan[]" id="harga_satuan_0" oninput="multiply(this)" class="form-control"></td>
                                                <td><input type="number" name="jumlah[]" id="jumlah_0" oninput="multiply(this)" class="form-control"></td>
                                                <td><input type="number" name="harga_total[]" id="harga_total_0" class="form-control"></td>
                                                <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <a href="{{ route('tradisional.index') }}" class="btn btn-secondary" type="button">Back</a>
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
            $('[id^=nama_produk], [id^=kondisi]').select2();
            var i = 1;
            $('#add').click(function() {
                var newRow = '<tr class="tr_clone" id="row' + i + '">' +
                '<td>' +
                '<select id="nama_produk_' + i + '" name="nama_produk[]" class="form-control select2">' +
                '<option value="">Pilih Produk</option>' +
                '@foreach ($produks as $produk)' +
                '<option value="{{ $produk->id }}">{{ $produk->nama }}</option>' +
                '@endforeach' +
                '</select>' +
                '<input type="hidden" name="kode_produk[]" id="kode_produk_' + i + '" style="display: none;">' +
                '<input type="hidden" name="tipe_produk[]" id="tipe_produk_' + i + '" style="display: none;">' +
                '<input type="hidden" name="deskripsi_komponen[]" id="deskripsi_komponen_' + i + '" style="display: none;">' +
                '</td>' +
                '<td>' +
                '<select id="kondisi_' + i + '" name="kondisi[]" class="form-control select2">' +
                '<option value="">Pilih Kondisi</option>' +
                '@foreach ($kondisi as $item)' +
                '<option value="{{ $item->id }}">{{ $item->nama }}</option>' +
                '@endforeach' +
                '</select>' +
                '</td>' +
                '<td><input type="number" name="harga_satuan[]" id="harga_satuan_' + i + '" oninput="multiply(this)" class="form-control"></td>' +
                '<td><input type="number" name="jumlah[]" id="jumlah_' + i + '" oninput="multiply(this)" class="form-control"></td>' +
                '<td><input type="number" name="harga_total[]" id="harga_total_' + i + '" class="form-control"></td>' +
                '<td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">x</button></td>' +
                '</tr>';
                $('#dynamic_field').append(newRow);
                $('#nama_produk_' + i + ', #kondisi_' + i).select2();
                i++
            });
            $(document).on('click', '.btn_remove', function() {
                var button_id = $(this).attr("id");
                $('#row'+button_id+'').remove();
            });
            $(document).on('change', '[id^=nama_produk]', function() {
                var id = $(this).attr('id').split('_')[2]; // Ambil bagian angka ID
                var selectedValue = $(this).val(); // Nilai yang dipilih
                // Set nilai input tersembunyi yang sesuai
                $('#kode_produk_' + id).val($(this).data('kode'));
                $('#tipe_produk_' + id).val($(this).data('tipe'));
                $('#deskripsi_komponen_' + id).val($(this).data('deskripsi'));
            });
        });
        function multiply(element) {
            var id = 0
            var jumlah = 0
            var harga_satuan = 0
            var jenis = $(element).attr('id')
            if(jenis.split('_').length == 2){
                id = $(element).attr('id').split('_')[1];
                jumlah = $(element).val();
                harga_satuan = $('#harga_satuan_' + id).val();
                if (harga_satuan) {
                    $('#harga_total_'+id).val(harga_satuan * jumlah)
                }
            } else if(jenis.split('_').length == 3){
                id = $(element).attr('id').split('_')[2];
                harga_satuan = $(element).val();
                jumlah = $('#jumlah_' + id).val();
                if (jumlah) {
                    $('#harga_total_'+id).val(harga_satuan * jumlah)
                }
            }

            var inputs = $('input[name="harga_total[]"]');
            var total = 0;
            inputs.each(function() {
                total += parseInt($(this).val()) || 0;
            });
            $('#harga').val(total)
        }
    </script>
@endsection