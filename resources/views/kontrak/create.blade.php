@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Buat Kontrak</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('kontrak.store') }}" method="POST">
                <div class="row">
                    <div class="col-sm">
                            @csrf
                            <div class="row justify-content-around">
                                <div class="col-md-6 border rounded pt-3 me-1">
                                    <h5 class="card-title">Informasi Pelanggan</h5>
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Customer</label>
                                            <select id="customer_id" name="customer_id" class="form-control">
                                                <option value="">Pilih Customer</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}">{{ $customer->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>PIC</label>
                                                <input type="text" id="pic" name="pic" value="{{ old('pic') }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>No NPWP</label>
                                                <input type="text" id="no_npwp" name="no_npwp" value="{{ old('no_npwp') }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>Alamat</label>
                                                <textarea type="text" id="alamat" name="alamat" value="{{ old('alamat') }}" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Handphone</label>
                                                <input type="text" id="handhpone" name="handphone" value="{{ old('handphone') }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>Nama NPWP</label>
                                                <input type="text" id="nama_npwp" name="nama_npwp" value="{{ old('nama_npwp') }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>Status</label>
                                                <input type="text" id="status" name="status" value="{{ old('status') }}" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 border rounded pt-3 ms-1">
                                    <h5 class="card-title">Detail Kontrak</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>No Kontrak</label>
                                                <input type="text" id="no_kontrak" name="no_kontrak" value="{{ $getKode }}" value="{{ old('no_kontrak') }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Mulai</label>
                                                <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>Masa Sewa</label>
                                                <div class="input-group">
                                                    <input type="text" id="masa_sewa" name="masa_sewa" value="{{ old('masa_sewa') }}" class="form-control" placeholder="Masa sewa" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                                    <span class="input-group-text" id="basic-addon2">bulan</span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Catatan</label>
                                                <textarea type="text" id="catatan" name="catatan" value="{{ old('catatan') }}" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Tanggal Kontrak</label>
                                                <input type="date" id="tanggal_kontrak" name="tanggal_kontrak" value="{{ old('tanggal_kontrak') }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Selesai</label>
                                                <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>Sales</label>
                                                <select id="sales" name="sales" class="form-control">
                                                    <option value="">Pilih Sales</option>
                                                    @foreach ($sales as $item)
                                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Rekening</label>
                                                <select id="rekening_id" name="rekening_id" class="form-control">
                                                    <option value="">Pilih Rekening</option>
                                                    @foreach ($rekenings as $rekening)
                                                        <option value="{{ $rekening->id }}">{{ $rekening->nama_akun }}</option>
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
                                        <th>Nama</th>
                                        <th>Harga Satuan</th>
                                        <th>Jumlah</th>
                                        <th>Harga Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="dynamic_field">
                                    <tr>
                                        <td>
                                            <select id="produk_0" name="nama_produk[]" class="form-control">
                                                <option value="">Pilih Produk</option>
                                                @foreach ($produkjuals as $produk)
                                                    <option value="{{ $produk->kode }}">{{ $produk->nama }}</option>
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
                </div>
                <div class="row">
                    <div class="col-sm">
                        <div class="row justify-content-around">
                            <div class="col-md-7 border rounded pt-3 me-1">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Pengaju</th>
                                            <th>Pembuat</th>
                                            <th>Penyetuju</th>
                                            <th>Pemeriksa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ date('Y-m-d') }}</td>
                                            <td>{{ date('Y-m-d') }}</td>
                                            <td>{{ date('Y-m-d') }}</td>
                                            <td>{{ date('Y-m-d') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4 border rounded pt-3 me-1">
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Subtotal</label>
                                    <div class="col-lg-9">
                                        <input type="text" id="subtotal" name="subtotal" value="{{ old('subtotal') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">PPN</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="number" id="ppn_persen" name="ppn_persen" value="{{ old('ppn_persen') }}" class="form-control" aria-label="Recipient's username" aria-describedby="basic-addon3">
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                        <input type="text" name="ppn_nominal" id="ppn_nominal" value="{{ old('ppn_nominal') }}">
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">PPH</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="number" id="pph_persen" name="pph_persen" value="{{ old('pph_persen') }}" class="form-control" aria-label="Recipient's username" aria-describedby="basic-addon3">
                                            <span class="input-group-text" id="basic-addon3">%</span>
                                        </div>
                                        <input type="text" name="pph_nominal" id="pph_nominal" value="{{ old('pph_nominal') }}">
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Diskon</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Nominal Diskon</label>
                                    <div class="col-lg-9">
                                        <input type="number" id="total_promo" name="total_promo" value="{{ old('total_promo') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Ongkir</label>
                                    <div class="col-lg-9">
                                        <input type="number" id="ongkir_nominal" name="ongkir_nominal" value="{{ old('ongkir_nominal') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row mt-1">
                                    <label class="col-lg-3 col-form-label">Total Harga</label>
                                    <div class="col-lg-9">
                                        <input type="number" id="total_harga" name="total_harga" value="{{ old('total_harga') }}" class="form-control">
                                    </div>
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
        $(document).ready(function() {
            $('[id^=produk], #customer_id, #sales, #rekening_id').select2();
            var i = 1;
            $('#add').click(function(){
            var newRow = '<tr id="row'+i+'"><td>' + 
                                '<select id="produk_'+i+'" name="nama_produk[]" class="form-control">'+
                                    '<option value="">Pilih Produk</option>'+
                                    '@foreach ($produkjuals as $produk)'+
                                        '<option value="{{ $produk->kode }}">{{ $produk->nama }}</option>'+
                                    '@endforeach'+
                                '</select>'+
                            '</td>'+
                            '<td><input type="number" name="harga_satuan[]" id="harga_satuan_'+i+'" oninput="multiply(this)" class="form-control"></td>'+
                            '<td><input type="number" name="jumlah[]" id="jumlah_'+i+'" oninput="multiply(this)" class="form-control"></td>'+
                            '<td><input type="number" name="harga_total[]" id="harga_total_'+i+'" class="form-control"></td>'+
                            '<td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">x</button></td></tr>';
                $('#dynamic_field').append(newRow);
                $('#produk_' + i).select2();
                i++;
           })
           $(document).on('click', '.btn_remove', function() {
                var button_id = $(this).attr("id");
                $('#row'+button_id+'').remove();
                multiply($('#harga_satuan_0'))
                multiply($('#jumlah_0'))
            });
            $('#ongkir, #total_promo, #ppn_persen, #pph_persen').on('input', function(){
                total_harga();
            })
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
            $('#subtotal').val(total)
            total_harga();
        }
        function total_harga() {
            ppn();
            pph();
            var subtotal = $('#subtotal').val();
            var ppn_nominal = $('#ppn_nominal').val() || 0;
            var pph_nominal = $('#pph_nominal').val() || 0;
            var ongkir_nominal = $('#ongkir').val() || 0;
            var diskon_nominal = $('#total_promo').val() || 0;

            var harga_total = parseInt(subtotal) + parseInt(ppn_nominal) + parseInt(pph_nominal) + parseInt(ongkir_nominal) - parseInt(diskon_nominal);
            $('#total_harga').val(harga_total);
        }
        function ppn(){
            var ppn_persen = $('#ppn_persen').val()
            var subtotal = $('#subtotal').val()
            var ppn_nominal = ppn_persen * subtotal / 100
            $('#ppn_nominal').val(ppn_nominal)
        }
        function pph(){
            var pph_persen = $('#pph_persen').val()
            var subtotal = $('#subtotal').val()
            var pph_nominal = pph_persen * subtotal / 100
            $('#pph_nominal').val(pph_nominal)
        }
    </script>
@endsection