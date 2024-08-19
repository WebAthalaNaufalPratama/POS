@extends('layouts.app-von')

@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Mutasi GreenHouse ke Galery</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.html">Mutasi</a>
                </li>
                <li class="breadcrumb-item active">
                    GreenHouse Ke Galery
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">
                Atur Komponen Barang
            </h4>
        </div>
        <div class="card-body">
                <div class="row">
                    <div class="col-sm">
                    <form action="{{ route('auditmutasighgalery.update', ['mutasiGG' => $mutasis->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('patch')
                        <div class="row justify-content-around">
                            <div class="col-md-6 border rounded pt-3">
                                <!-- <h5>Informasi Mutasi</h5> -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="pengirim">Nama Pengirim</label>
                                            
                                            <select id="pengirim" name="pengirim" class="form-control" required readonly>
                                                <option value="">Pilih Nama Pengirim</option>
                                                @foreach ($lokasis as $lokasi)
                                                <option value="{{ $lokasi->id }}" data-tipe-lokasi="{{ $lokasi->tipe_lokasi }}" {{ $lokasi->id == $mutasis->pengirim ? 'selected' : ''}}>{{ $lokasi->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="penerima">Nama Penerima</label>
                                            <select id="penerima" name="penerima" class="form-control" required >
                                                <option value="">Pilih Nama Penerima</option>
                                                @foreach ($lokasispenerima as $lokasi)
                                                <option value="{{ $lokasi->id }}" {{ $lokasi->id == $mutasis->penerima ? 'selected' : ''}}>{{ $lokasi->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="no_mutasi">No Mutasi</label>
                                            <input type="text" id="no_mutasi" name="no_mutasi" class="form-control" value="{{ $mutasis->no_mutasi}}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="rekening_id">Rekening</label>
                                            <select id="rekening_id" name="rekening_id" class="form-control" required>
                                                <option value="">Pilih Rekening</option>
                                                @foreach ($bankpens as $rekening)
                                                <option value="{{ $rekening->id }}" {{ $rekening->id == $mutasis->rekening_id ? 'selected':''}}>{{ $rekening->bank }} -{{ $rekening->nama_akun}}({{$rekening->nomor_rekening}})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6 border rounded pt-3">
                                <!-- <h5>Informasi Invoice</h5> -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="nama">Tanggal Pengiriman</label>
                                            <input type="date" class="form-control" id="tanggal_kirim" name="tanggal_kirim" placeholder="Tanggal_Invoice" value="{{ $mutasis->tanggal_kirim}}" required >
                                        </div>
                                        <div class="form-group">
                                            <label for="nama">Tanggal Diterima</label>
                                            <input type="date" class="form-control" id="tanggal_diterima" name="tanggal_diterima" placeholder="Tanggal_Invoice" value="{{ $mutasis->tanggal_diterima}}" required >
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select id="status" name="status" class="form-control" required >
                                                <option value="">Pilih Status</option>
                                                @php
                                                    $user = Auth::user();
                                                @endphp
                                                @if($user->hasRole(['Purchasing']) && $mutasis->status != 'DIKONFIRMASI')
                                                <option value="TUNDA" {{ $mutasis->status == 'TUNDA' ? 'selected' : ''}}>TUNDA</option>
                                                @endif
                                                <option value="DIKONFIRMASI" {{ $mutasis->status == 'DIKONFIRMASI' ? 'selected' : ''}}>DIKONFIRMASI</option>
                                                @if($user->hasRole(['Purchasing']) && $mutasis->status != 'DIKONFIRMASI')
                                                <option value="DIBATALKAN" {{ $mutasis->status == 'DIBATALKAN' ? 'selected' : ''}}>DIBATALKAN</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div id="alasan" style="display: none;">
                                                <label for="alasan">Alasan</label>
                                                <textarea name="alasan" id="alasan"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <!-- <input type="file" id="bukti_file" name="bukti_file" placeholder="Bukti File Invoice" aria-describedby="inputGroupPrepend2" required disabled> -->
                                            <div class="custom-file-container" data-upload-id="myFirstImage">
                                                <label>Bukti Mutasi <a href="javascript:void(0)" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"></a>
                                                </label>
                                                <label class="custom-file-container__custom-file">
                                                    <input type="file" id="bukti_file" class="custom-file-container__custom-file__custom-file-input" name="bukti" accept="image/*" >
                                                    <span class="custom-file-container__custom-file__custom-file-control"></span>
                                                </label>
                                                <span class="text-danger">max 2mb</span>
                                                <div class="image-preview">
                                                    <img id="imagePreview" src="{{ $mutasis->bukti ? '/storage/' . $mutasis->bukti : '' }}" />
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-around">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2">
                                <div class="form-row row">
                                    <div class="mb-4">
                                        <h5>List Produk</h5>
                                    </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Nama</th>
                                                        <th>Jumlah Dikirim</th>
                                                        <!-- <th>Jumlah Diterima</th> -->
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="dynamic_field">
                                                @if(count($produks) > 0)
                                                    @foreach ($produks as $i => $produk)
                                                        <tr id="row{{ $i }}">
                                                            <td>
                                                                <input type="hidden" id="nama_produk_{{ $i }}" name="nama_produk[]" class="form-control" value="{{ $produk->id }}">
                                                                <select id="kode_produk_{{ $i }}" name="kode_produk[]" class="form-control select2">
                                                                    <option value="">Pilih Produk</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="number" name="jumlah_dikirim[]" id="jumlah_dikirim_{{ $i }}" class="form-control" value="{{ $produk->jumlah }}">
                                                            </td>
                                                            @if($i == 0)
                                                                <td><button type="button" name="add" id="add" class="btn"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></button></td>
                                                            @endif
                                                            @php
                                                                $user = Auth::user();
                                                            @endphp
                                                            @if($user->hasRole(['Purchasing']) && $i != 0) 
                                                                <td><button type="button" name="remove" id="${i}" class="btn btn_remove"><img src="/assets/img/icons/delete.svg" alt="svg"></button></td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-around">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2">
                                <div class="row">
                                    <div class="col-lg-8 col-sm-6 col-12 ">
                                        <div class="row mt-4">
                                            <div class="col-lg-12">
                                            <table class="table table-responsive border rounded">
                                                    @php
                                                        $user = Auth::user();
                                                    @endphp
                                                    <thead>
                                                        <tr>
                                                            <th>Pembuat</th>
                                                            <th>Penerima</th>
                                                            <th>Penyetuju</th>
                                                            <th>Pemeriksa</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            
                                                            @if($mutasis->status == 'DIKONFIRMASI' && $user->hasRole(['Finance']))
                                                                <td id="pembuat">{{ $mutasis->dibuat->name }}</td>
                                                                <td id="penerima" >{{ $mutasis->diterima->name ?? '-'}}</td>
                                                                <td id="penyetuju" >{{ Auth::user()->name}}</td>
                                                                <td id="pemeriksa" >{{ $mutasis->dibuku->name ?? '-'}}</td>
                                                            @elseif($mutasis->status == 'DIKONFIRMASI' && $user->hasRole(['Auditor']))
                                                                <td id="pembuat">{{ $mutasis->dibuat->name }}</td>
                                                                <td id="penerima" >{{ $mutasis->diterima->name ?? '-'}}</td>
                                                                <td id="penyetuju" >{{ $mutasis->diperiksa->name ?? '-'}}</td>
                                                                <td id="pemeriksa">{{ Auth::user()->name}}</td>
                                                            @elseif($user->hasRole(['Purchasing', 'SuperAdmin']))
                                                                <td id="pembuat">{{ $mutasis->dibuat ? $mutasis->dibuat->name : '-' }}</td>
                                                                <td id="penerima" >-</td>
                                                                <td id="penyetuju" >-</td>
                                                                <td id="pemeriksa">-</td>
                                                            @endif
                                                        </tr>
                                                        <tr>
                                                            @if($user->hasRole(['Purchasing', 'SuperAdmin']))
                                                                <td><input type="date" class="form-control" name="tanggal_pembuat"  value="{{ $mutasis->tanggal_pembuat ? $mutasis->tanggal_pembuat : '-' }}"></td>
                                                                <td id="tgl_penerima">-</td>
                                                                <td id="tgl_penyetuju">-</td>
                                                                <td id="tgl_pemeriksa">-</td>
                                                            @elseif($mutasis->status == 'DIKONFIRMASI' && $user->hasRole(['Finance']))
                                                                <td><input type="date" class="form-control" name="tanggal_pembuat"  value="{{ $mutasis->tanggal_pembuat }}" readonly></td>
                                                                <td><input type="date" class="form-control" name="tanggal_penerima"  value="{{ $mutasis->tanggal_penerima ?? '-' }}" readonly></td>
                                                                <td><input type="date" class="form-control" name="tanggal_diperiksa" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" ></td>
                                                                <td><input type="date" class="form-control" name="tanggal_dibukukan"  value="{{ $mutasis->tanggal_dibukukan ?? '-' }}" readonly></td></td>
                                                            @elseif($mutasis->status == 'DIKONFIRMASI' && $user->hasRole(['Auditor']))
                                                                <td><input type="date" class="form-control" name="tanggal_pembuat"  value="{{ $mutasis->tanggal_pembuat }}" readonly></td>
                                                                <td><input type="date" class="form-control" name="tanggal_penerima"  value="{{ $mutasis->tanggal_penerima ?? '-' }}" readonly></td>
                                                                <td><input type="date" class="form-control" name="tanggal_diperiksa" value="{{ $mutasis->tanggal_diperiksa ?? '-'}}" readonly></td>
                                                                <td><input type="date" class="form-control" name="tanggal_dibukukan" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"></td>
                                                            @endif
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 float-md-right">
                                        <div class="total-order">
                                            <ul>
                                                <li>
                                                    <h4>Pengiriman
                                                    <select id="pilih_pengiriman" name="pilih_pengiriman" class="form-control" >
                                                        <option value="">Pilih Jenis Pengiriman</option>
                                                        <option value="exspedisi" {{ $mutasis->pilih_pengiriman == 'exspedisi' ? 'selected' : ''}}>Ekspedisi</option>
                                                        <option value="sameday" {{ $mutasis->pilih_pengiriman == 'sameday' ? 'selected' : ''}}>SameDay</option>
                                                    </select>
                                                    </h4>
                                                    <h5>
                                                    <div id="inputOngkir" style="display: none;">
                                                        <!-- <label for="alamat_tujuan">Alamat Tujuan </label> -->
                                                        <input type="text" id="alamat_tujuan" name="alamat_tujuan" value="{{ $mutasis->alamat_tujuan}}" class="form-control" >
                                                    </div>
                                                    <div id="inputExspedisi" style="display: none;">
                                                        <!-- <label>Alamat Pengiriman</label> -->
                                                        <select id="ongkir_id" name="ongkir_id" class="form-control" >
                                                            <option value="">Pilih Alamat Tujuan</option>
                                                            @foreach($ongkirs as $ongkir)
                                                            <option value="{{ $ongkir->id }}" data-biaya_pengiriman="{{ $ongkir->biaya}}" {{ $mutasis->ongkir_id == $ongkir->id ? 'selected' : ''}}>{{ $ongkir->nama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div> 
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Biaya Ongkir (Rp)</h4>
                                                    <h5><input type="text" id="biaya_pengiriman" name="biaya_pengiriman" class="form-control" value="{{ $mutasis->biaya_pengiriman ?? '-'}}"  required></h5>
                                                </li>
                                                <li class="total">
                                                    <h4>Total Biaya (Rp)</h4>
                                                    <h5><input type="text" id="total_biaya" name="total_biaya" class="form-control" value="{{ $mutasis->total_biaya ?? '-'}}"  required readonly></h5>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <a href="{{ route('mutasighgalery.index') }}" class="btn btn-secondary" type="button">Back</a>
                        </div>
            </form>
        </div>

    </div>
</div>



<div class="modal fade" id="modalGiftCoba" tabindex="-1" aria-labelledby="modalGiftLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalGiftLabel">Atur Komponen Gift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="form_gift" action="{{ route('komponenmutasi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="route" value="{{ request()->route()->getName() }},penjualan,{{ request()->route()->parameter('penjualan') }}">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-sm-8">
                                <label for="prdTerjualGift" class="col-form-label">Produk</label>
                                <input type="text" class="form-control" name="produk_id" id="prdTerjualGift"  required>
                            </div>
                            <input type="hidden" name="prdTerjual_id" id="prdTerjualGift_id" value="">
                            <div class="col-sm-4">
                                <label for="jmlGift_produk" class="col-form-label">Jumlah</label>
                                <input type="number" class="form-control" name="jml_produk" id="jmlGift_produk"  required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="jml_komponen" class="col-form-label">Jumlah Bunga/POT</label>
                        <input type="number" class="form-control" name="jml_komponen" id="jml_komponen" required>
                    </div>
                    <div class="mb-3">
                        <label for="komponen_id" class="col-form-label">Bunga/POT</label>
                        <div id="div_komponen" class="form-group">
                            <div id="div_produk_jumlah_0" class="row">
                                <div class="col-sm-4">
                                    <select id="komponen_id_0" name="komponen_id[]" class="form-control" required>
                                        <option value="">Pilih Bunga/POT</option>
                                        @foreach ($produkKomponens as $itemkomponen)
                                        <option value="{{ $itemkomponen->id }}">{{ $itemkomponen->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <select id="kondisi_id_0" name="kondisi_id[]" class="form-control" required>
                                        <option value="">kondisi</option>
                                        @foreach ($kondisis as $kondisi)
                                        <option value="{{ $kondisi->id }}">{{ $kondisi->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <input type="number" class="form-control" name="jumlahproduk_id[]" id="jumlahproduk_id_0">
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>



</div>
@endsection

@section('scripts')
<script>
    // Function to update date to today's date
    function updateDate(element) {
        var today = new Date().toISOString().split('T')[0];
        element.value = today;
    }

    // Call the function to set the date to today's date initially
    updateDate(document.getElementById('tanggal_kirim'));
    updateDate(document.getElementById('tanggal_diterima'));
    @foreach($produks as $index => $produk)
    updateDate(document.getElementById('tglrangkai_{{ $index }}'), '{{ $index }}');
    @endforeach
    updateDate(document.getElementById('tanggalbayar'));
</script>
<script>
    var produkData = [];

    @foreach ($produks as $produk)
        produkData.push({
            id: {{ $produk->id }},
            jumlah: {{ $produk->jumlah }}
        });
    @endforeach

    // console.log('Produk Data:', produkData);

    $(document).on('input', '.jumlah_diterima', function() {
        var inputId = $(this).attr('id');
        var jumlah = parseInt($(this).val(), 10); // Ensure jumlah is parsed as an integer
        var produkId = $(this).data('produk-id'); // Extract the product ID from the data attribute

        var produk = produkData.find(function(item) {
            return item.id == produkId;
        });

        if (produk) {
            if (jumlah > produk.jumlah) {
                alert('jumlah diterima tidak boleh lebih dari jumlah dikirim');
                $(this).val(produk.jumlah);
            } else if (jumlah < 0) {
                alert('jumlah diterima tidak boleh kurang dari 0');
                $(this).val(0);
            }
        } else {
            console.error('Produk not found for ID:', produkId);
        }
    });
</script>

<script>
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function() {
            function fetchProducts(tipeLokasi, rowId, selectedProdukId = null, selectedKondisiId = null) {

            $.ajax({
                url: '{{ route("getProductsByLokasi") }}',
                type: 'GET',
                data: { tipe_lokasi: tipeLokasi },
                success: function(data) {
                    updateProductOptions(data, rowId, selectedProdukId, selectedKondisiId);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching products:', error);
                }
            });
        }

        function updateProductOptions(produks, rowId, selectedProdukId = null, selectedKondisiId = null) {
            var $produkSelect = $('#kode_produk_' + rowId);

            $produkSelect.empty();
            $produkSelect.append('<option value="">Pilih Produk</option>');

            $.each(produks, function(index, produk) {
                var selected = (selectedProdukId == produk.kode_produk && selectedKondisiId == produk.kondisi_id) ? 'selected' : '';

                $produkSelect.append(
                    '<option value="' + produk.id + '" data-harga="' + produk.harga_jual + '" data-tipe_produk="' + produk.tipe_produk + '" ' + selected + '>' +
                    produk.produk.nama + ' - ' + produk.kondisi.nama +
                    '</option>'
                );
            });

            // Additional debug: Check if the option is correctly selected
            var selectedOption = $produkSelect.find('option:selected');
        }

        $('#pengirim').change(function() {
            var tipeLokasi = $(this).find(':selected').data('tipe-lokasi');
            fetchProducts(tipeLokasi, 0);
        });

        var i = "{{ count($produks) }}";
        $('#add').click(function() {
            var newRow = `<tr id="row${i}">
                            <td>
                                <select id="kode_produk_${i}" name="kode_produk[]" class="form-control select2">
                                    <option value="">Pilih Produk</option>
                                </select>
                            </td>
                            <td><input type="number" name="jumlah_dikirim[]" id="jumlah_dikirim_${i}" class="form-control"></td>
                            <td><button type="button" name="remove" id="${i}" class="btn btn_remove"><img src="/assets/img/icons/delete.svg" alt="svg"></button></td>
                        </tr>`;

            $('#dynamic_field').append(newRow);
            $('.select2').select2();
            updateProductOptionsForAllRows(i);
            i++;
        });

        function updateProductOptionsForAllRows(rowId) {
            var tipeLokasi = $('#pengirim').find(':selected').data('tipe-lokasi');
            fetchProducts(tipeLokasi, rowId);
        }

        $(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
            $('#row' + button_id).remove();
            calculateTotal();
        });
        @foreach ($produks as $index => $produk)
            @foreach ($produk->komponen as $komponen)
                    $(document).ready(function() {
                        var tipeLokasi = $('#pengirim').find(':selected').data('tipe-lokasi');
                        var selectedProdukId = "{{ $komponen->kode_produk }}"; 
                        var selectedKondisiId = "{{ $komponen->kondisi }}";
                        fetchProducts(tipeLokasi, {{ $index }}, selectedProdukId, selectedKondisiId);
                    });
            @endforeach
        @endforeach

        // Trigger change event to initialize the first row
        $('#pengirim').trigger('change');

        function formatRupiah(angka, prefix) {
            var numberString = angka.toString().replace(/[^,\d]/g, ''),
                split = numberString.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                var separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix === undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
        }

        $('#pilih_pengiriman').change(function() {
            var pengiriman = $(this).val();
            var biayaOngkir = parseFloat($('#biaya_pengiriman').val()) || 0;

            $('#inputOngkir').hide();
            $('#inputExspedisi').hide();

            if (pengiriman === "sameday") {
                $('#inputOngkir').show();
                $('#biaya_pengiriman').prop('readonly', false);
            } else if (pengiriman === "exspedisi") {
                $('#inputExspedisi').show();
                $('#biaya_pengiriman').prop('readonly', true);
                $('#ongkir_id').trigger('change');
            }
        });

         $('#pilih_pengiriman').trigger('change');

         $('#ongkir_id').on('change',function() {
            var selectedOption = $(this).find('option:selected');
            var ongkirValue = parseFloat(selectedOption.data('biaya_pengiriman')) || 0;
            $('#biaya_pengiriman').val(formatRupiah(ongkirValue));
            Totaltagihan();
        });

        if ($('#pilih_pengiriman').val() === "exspedisi") {
            $('#ongkir_id').trigger('change');
        }

        Totaltagihan();

        function Totaltagihan() {
            var biayaOngkir = parseFloat(parseRupiahToNumber($('#biaya_pengiriman').val())) || 0;
            var totalTagihan = biayaOngkir;

            $('#total_biaya').val(formatRupiah(totalTagihan));
            $('#biaya_pengiriman').val(formatRupiah(biayaOngkir));
        }

        $('#biaya_pengiriman').on('input', Totaltagihan);
    
        function parseRupiahToNumber(rupiah) {
            return parseInt(rupiah.replace(/[^\d]/g, ''));
        }

        $('form').on('submit', function(e) {
            // Parse semua nilai input yang diformat Rupiah ke angka numerik
            $('#biaya_pengiriman').val(parseRupiahToNumber($('#biaya_pengiriman').val()));
            $('#total_biaya').val(parseRupiahToNumber($('#total_biaya').val()));

        });


        $(document).on('change', '[id^=nama_produk]', function() {
            var id = $(this).attr('id').split('_')[2];
            var selectedOption = $(this).find(':selected');

            var kodeProduk = selectedOption.data('kode');
            var tipeProduk = selectedOption.data('tipe');
            var deskripsiProduk = selectedOption.data('deskripsi');
            // console.log(kodeProduk);
            $('#kode_produk_' + id).val(kodeProduk);
            $('#tipe_produk_' + id).val(tipeProduk);
            $('#deskripsi_komponen_' + id).val(deskripsiProduk);

            // Panggil fungsi updateHargaSatuan
            updateHargaSatuan(this);
        });

        $('#pengirim').on('mousedown click focus', function(e) {
            e.preventDefault();
        });

        $('#status').change(function(){
            var status = $(this).val();
            if(status == 'DIBATALKAN')
            {
                $('#alasan').show();
            }else{
                $('#alasan').hide();
            }
        });

        $('input[id^="jumlah_dikirim"]').on('input', function() {
            var inputDiterima = $(this).val();

            if (inputDiterima < 0) {
                alert('Jumlah Dikirim tidak boleh kurang dari 0!');
                $(this).val(0);
            }
        });

        function updateSubTotal() {
            var subTotalInput = $('#sub_total');
            var hargaTotalInputs = $('input[name="harga_total[]"]');
            var subTotal = 0;

            hargaTotalInputs.each(function() {
                subTotal += parseFloat($(this).val()) || 0;
            });

            subTotalInput.val(subTotal.toFixed(2));
        }

        $('#bukti_file').on('change', function() {
            const file = $(this)[0].files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(event.target.files[0]);
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

        function clearFile() {
            $('#bukti_file').val('');
            $('#preview').attr('src', defaultImg);
        };


       
    });
</script>

@endsection