@extends('layouts.app-von')

@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Delivery Order</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.html">Penjualan</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="index.html">Invoice Penjualan</a>
                </li>
                <li class="breadcrumb-item active">
                    DO Penjualan
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">
                Delivery Order Penjualan
            </h4>
        </div>
        <div class="card-body">
                <div class="row">
                <form action="{{ route('auditdopenjualan.update', ['dopenjualan' => $dopenjualan->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('patch')
                        <div class="col-sm">
                            <div class="row">
                                <div class="col-md-6 border rounded pt-3">
                                    <h5>Informasi Pelanggan</h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="no_do">No Delivery Order</label>
                                                <input type="text" class="form-control" id="no_do" name="no_do" placeholder="Nomor Delivery Order" value="{{ $dopenjualan->no_do}}"  required readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="penerima">Penerima</label>
                                                <input type="text" class="form-control" id="penerima" name="penerima" placeholder="Masukan Nama Penerima" value="{{ $dopenjualan->penerima}}" required >
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="handphone">No Hp/Wa</label>
                                                <input type="text" class="form-control" id="handphone" name="handphone" placeholder="Nomor Handphone" value="{{$dopenjualan->handphone}}" required >
                                            </div>
                                            <div class="form-group">
                                                <label for="driver">Driver</label>
                                                <select id="driver" name="driver" class="form-control" >
                                                    <option value=""> Pilih Nama Driver </option>
                                                    @foreach ($karyawans as $karyawan)
                                                    <option value="{{ $karyawan->id }}" {{ $karyawan->id == $dopenjualan->driver ? 'selected' : '' }}>{{ $karyawan->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="alamat">Alamat</label>
                                                <textarea type="text" class="form-control" id="alamat" name="alamat" placeholder="Alamat" value="{{ $dopenjualan->alamat}}" required >{{ $dopenjualan->alamat}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6 border rounded pt-3">
                                    <h5>Informasi Pesanan</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="no_referensi">Nomor Invoice</label>
                                                <input type="text" class="form-control" id="no_referensi" name="no_referensi" placeholder="Nomor Invoice" value="{{ $dopenjualan->no_referensi}}" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="tanggal_kirim">Tanggal Kirim</label>
                                                <input type="date" class="form-control" id="tanggal_kirim" name="tanggal_kirim" placeholder="Tanggal_kirim" value="{{ $dopenjualan->tanggal_kirim}}" required >
                                            </div>
                                            <div class="form-group">
                                                <label for="catatan">Catatan</label>
                                                <textarea class="form-control" id="catatan" name="catatan" value="{{ $dopenjualan->catatan}}" >{{ $dopenjualan->catatan}}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select id="status" name="status" class="form-control" required>
                                                @if($dopenjualan->status != 'DIKONFIRMASI')
                                                    <option value="">Pilih Status</option>
                                                    <option value="TUNDA" {{ $dopenjualan->status == 'TUNDA' ? 'selected': ''}}>TUNDA</option>
                                                    <option value="DIKONFIRMASI" {{ $dopenjualan->status == 'DIKONFIRMASI' ? 'selected': ''}}>DIKONFIRMASI</option>
                                                    @php
                                                        $user = Auth::user();
                                                    @endphp
                                                    @if($user->hasRole(['AdminGallery', 'KasirGallery', 'KasirOutlet']) && $dopenjualan->status != 'DIKONFIRMASI')
                                                        <option value="DIBATALKAN" {{ $dopenjualan->status == 'DIBATALKAN' ? 'selected': ''}}>DIBATALKAN</option>
                                                    @endif
                                                @else
                                                    <option value="DIKONFIRMASI" {{ $dopenjualan->status == 'DIKONFIRMASI' ? 'selected': ''}}>DIKONFIRMASI</option>
                                                @endif
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <div id="alasan" style="display: none;">
                                                    <label for="alasan">Alasan</label>
                                                    <textarea name="alasan" id="alasan"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nama">Tanggal Invoice</label>
                                                <input type="date" class="form-control" id="tanggal_pembuat" name="tanggal_pembuat" placeholder="Tanggal Invoice" value="{{ date('Y-m-d', strtotime($dopenjualan->tanggal_pembuat))}}" required >
                                            </div>
                                            <div class="form-group">
                                                <label for="customer_id">Pengirim</label>
                                                <select id="customer_id" name="customer_id" class="form-control"  >
                                                    <option value="">Pilih Nama Pengirim</option>
                                                    @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}" {{ $customer->id == $dopenjualan->customer_id ? 'selected' : '' }}>{{ $customer->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                            <!-- <form action="{{ route('dopenjualan.update', ['dopenjualan' => $dopenjualan->id]) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('patch') -->
                                                    <div class="custom-file-container" data-upload-id="myFirstImage">
                                                        <label>Bukti Kirim <a href="javascript:void(0)" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"></a>
                                                        </label>
                                                        <label class="custom-file-container__custom-file">
                                                            <input type="file" id="bukti" class="custom-file-container__custom-file__custom-file-input" name="file" accept="image/*" >
                                                            <span class="custom-file-container__custom-file__custom-file-control"></span>
                                                        </label>
                                                        <span class="text-danger">max 2mb</span>
                                                        <img id="preview" src="{{ $dopenjualan->file ? '/storage/' . $dopenjualan->file : '' }}" alt="your image" />
                                                    </div>
                                                    <!-- <div class="text-end mt-3"> -->
                                                        <!-- <button class="btn btn-primary" type="submit">Upload File</button> -->
                                                        <!-- <a href="{{ route('do_sewa.index') }}" class="btn btn-secondary" type="button">Back</a> -->
                                                    <!-- </div> -->
                                                <!-- </form> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm">
                            <div class="row">

                                <div class="col-md-12 border rounded pt-3 mt-2">
                                    <div class="form-row row">
                                        <div class="mb-4">
                                            <h5>Rincian Produk</h5>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Nama</th>
                                                        <th>Jumlah</th>
                                                        <th>Unit Satuan</th>
                                                        <th>Keterangan</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="dynamic_field">
                                                    @if(count($dopenjualan->produk) < 1) <tr>
                                                        <td>
                                                            <select id="nama_produk_0" name="nama_produk[]" class="form-control" readonly>
                                                                <option value="">Pilih Produk</option>
                                                                @foreach ($produkjuals as $produk)
                                                                <option value="{{ $produk->kode }}">
                                                                    @if (substr($produk->produk->kode, 0, 3) === 'TRD')
                                                                        {{ $pj->nama }}
                                                                        @foreach ($produk->komponen as $komponen)
                                                                            @if ($komponen->kondisi)
                                                                                @foreach($kondisis as $kondisi)
                                                                                    @if($kondisi->id == $komponen->kondisi)
                                                                                        - {{ $kondisi->nama }}
                                                                                        @php
                                                                                            $found = true;
                                                                                            break;
                                                                                        @endphp
                                                                                    @endif
                                                                                @endforeach
                                                                            @endif
                                                                            @if ($found) @break @endif
                                                                        @endforeach
                                                                        - {{$komponen->jumlah}}
                                                                    @elseif (substr($produk->produk->kode, 0, 3) === 'GFT')
                                                                        {{ $pj->nama }}
                                                                        @foreach ($produk->komponen as $komponen)
                                                                            - ( {{$komponen->nama_produk}}
                                                                            @if ($komponen->kondisi)
                                                                                @foreach($kondisis as $kondisi)
                                                                                    @if($kondisi->id == $komponen->kondisi)
                                                                                        - {{ $kondisi->nama }}
                                                                                        @php
                                                                                            $found = true;
                                                                                            break;
                                                                                        @endphp
                                                                                    @endif
                                                                                @endforeach
                                                                            @endif
                                                                            - {{$komponen->jumlah}} )
                                                                        @endforeach
                                                                    @endif
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="number" name="jumlah[]" id="jumlah_0" class="form-control"></td>
                                                        <td><input type="number" name="satuan[]" id="satuan_0" class="form-control"></td>
                                                        <td><input type="number" name="keterangan[]" id="keterangan_0" class="form-control"></td>
                                                        </tr>
                                                        @else
                                                        @php
                                                        $i = 0;
                                                        @endphp
                                                        @foreach ($dopenjualan->produk as $produk)
                                                        @if ($produk->jenis == null)
                                                        <tr id="row{{ $i }}">
                                                            <td>
                                                                @php
                                                                    $user = Auth::user();
                                                                @endphp
                                                                <select id="nama_produk_{{ $i }}" name="nama_produk[]" class="form-control" readonly>
                                                                    <option value="">Pilih Produk</option>
                                                                    @foreach ($produkjuals as $pj)
                                                                    <option value="{{ $produk->id }}" data-tipe_produk="{{ $pj->tipe_produk }}" {{ $pj->kode == $produk->produk->kode ? 'selected' : '' }}>
                                                                        @if (substr($produk->produk->kode, 0, 3) === 'TRD')
                                                                        {{ $pj->nama }}
                                                                        @foreach ($produk->komponen as $komponen)
                                                                            @if ($komponen->kondisi)
                                                                                @foreach($kondisis as $kondisi)
                                                                                    @if($kondisi->id == $komponen->kondisi)
                                                                                        - {{ $kondisi->nama }}
                                                                                        @php
                                                                                            $found = true;
                                                                                            break;
                                                                                        @endphp
                                                                                    @endif
                                                                                @endforeach
                                                                            @endif
                                                                            @if ($found) @break @endif
                                                                        @endforeach
                                                                        - {{$komponen->jumlah}}
                                                                    @elseif (substr($produk->produk->kode, 0, 3) === 'GFT')
                                                                        {{ $pj->nama }}
                                                                        @foreach ($produk->komponen as $komponen)
                                                                            - ( {{$komponen->nama_produk}}
                                                                            @if ($komponen->kondisi)
                                                                                @foreach($kondisis as $kondisi)
                                                                                    @if($kondisi->id == $komponen->kondisi)
                                                                                        - {{ $kondisi->nama }}
                                                                                        @php
                                                                                            $found = true;
                                                                                            break;
                                                                                        @endphp
                                                                                    @endif
                                                                                @endforeach
                                                                            @endif
                                                                            - {{$komponen->jumlah}} )
                                                                        @endforeach
                                                                    @endif
                                                                </option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            @if($user->hasRole(['Auditor', 'Finance']))
                                                            <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" class="form-control jumlah" value="{{ $produk->jumlah }}" data-produk-id="{{ $produk->id }}" readonly></td>
                                                            @else
                                                            <td><input type="number" name="jumlah[]" id="jumlah_{{ $i }}" class="form-control jumlah" value="{{ $produk->jumlah }}" data-produk-id="{{ $produk->id }}"></td>
                                                            @endif
                                                            <td><input type="text" name="satuan[]" id="satuan_{{ $i }}" class="form-control" value="{{ $produk->satuan }}" ></td>
                                                            <td><input type="text" name="keterangan[]" id="ketarangan_{{ $i }}" class="form-control" value="{{ $produk->keterangan }}" ></td>
                                                            <td>
                                                                @if(!$user->hasRole(['Auditor', 'Finance']))
                                                                <button type="button" name="remove" id="{{ $i }}" class="btn btn_remove"><img src="/assets/img/icons/delete.svg" alt="svg"></button>
                                                                @endif
                                                            </td>
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
                            </div>
                        </div>

                    @if(count($dopenjualan->produk) > 0)
                    <div class="col-sm">
                        <div class="row">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2">
                                <div class="form-row row">
                                    <div class="mb-4">
                                        <h5>Tambahan Produk</h5>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>Jumlah</th>
                                                    <th>Unit Satuan</th>
                                                    <th>Keterangan</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="dynamic_field_tambah">
                                                @if(count($dopenjualan->produk) < 1) <tr>
                                                    <td>
                                                        <select id="nama_produk2_0" name="nama_produk2[]" class="form-control" >
                                                            <option value="">Pilih Produk</option>
                                                            @foreach ($produkjuals as $produk)
                                                            <option value="{{ $produk->id }}">{{ $produk->nama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="jumlah2[]" id="jumlah2_0" class="form-control"></td>
                                                    <td><input type="number" name="satuan[]" id="satuan_0" class="form-control"></td>
                                                    <td><input type="number" name="keterangan[]" id="keterangan_0" class="form-control"></td>
                                                    </tr>
                                                    @else
                                                    @php
                                                    $i = 0;
                                                    @endphp
                                                    @foreach ($dopenjualan->produk as $produk)
                                                    @if ($produk->jenis == 'TAMBAHAN')
                                                    <tr id="row{{ $i }}">
                                                        <td>
                                                            <select id="nama_produk2_{{ $i }}" name="nama_produk2[]" class="form-control" >
                                                                <option value="">Pilih Produk</option>
                                                                @foreach ($produkjuals as $pj)
                                                                <option value="{{ $produk->id }}" data-tipe_produk="{{ $pj->tipe_produk }}" {{ $pj->kode == $produk->produk->kode ? 'selected' : '' }}>{{ $pj->nama }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="number" name="jumlah2[]" id="jumlah2_{{ $i }}" class="form-control" value="{{ $produk->jumlah }}" ></td>
                                                        <td><input type="text" name="satuan2[]" id="satuan2_{{ $i }}" class="form-control" value="{{ $produk->satuan }}" ></td>
                                                        <td><input type="text" name="keterangan2[]" id="keterangan2_{{ $i }}" class="form-control" value="{{ $produk->keterangan }}" ></td>
                                                        <td><button type="button" name="remove" id="{{ $i }}" class="btn btn_remove"><img src="/assets/img/icons/delete.svg" alt="svg"></button></td>
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
                        </div>
                    </div>
                    @endif

                    <div class="row  justify-content-center pt-3  mt-2">
                        <div class="col-lg-6 col-sm-12 border radius pt-2 pb-2">
                                    <!-- <div class="col-lg-12"> -->
                                        <table class="table table-responsive border rounded">
                                            <thead>
                                                <tr>
                                                    <th>Pembuat</th>
                                                    <th>Penyetuju</th>
                                                    <th>Pemeriksa</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $user = Auth::user();
                                                @endphp
                                                <tr>
                                                    @if($user->hasRole(['AdminGallery', 'KasirGallery', 'KasirOutlet']))
                                                        <td id="pembuat">{{ Auth::user()->name }}</td>
                                                        <td id="penyetuju">-</td>
                                                        <td id="pemeriksa">-</td>
                                                    @elseif($user->hasRole(['Finance']))
                                                        <td id="pembuat">{{ $dopenjualan->dibuat[0]->name }}</td>
                                                        <td id="penyetuju">{{ Auth::user()->name }}</td>
                                                        <td id="pemeriksa">{{ $dopenjualan->dibuku->name ?? '-' }}</td>
                                                    @elseif($user->hasRole(['Auditor']))
                                                        <td id="pembuat">{{ $dopenjualan->dibuat[0]->name }}</td>
                                                        <td id="penyetuju">{{ $dopenjualan->diperiksa->name ?? '-' }}</td>
                                                        <td id="pemeriksa">{{ Auth::user()->name }}</td>
                                                    @endif
                                                </tr>
                                                <tr>
                                                @if($user->hasRole(['AdminGallery', 'KasirGallery', 'KasirOutlet']))
                                                    <td><input type="date" class="form-control" name="tanggal_pembuat" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"></td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                @elseif($user->hasRole(['Finance']))
                                                    <td><input type="date" class="form-control" value="{{ date('Y-m-d', strtotime($dopenjualan->tanggal_pembuat)) }}" disabled ></td>
                                                    <td><input type="date" class="form-control" name="tanggal_penyetuju" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"></td>
                                                    <td><input type="date" class="form-control value="{{ date('Y-m-d', strtotime($dopenjualan->tanggal_pemeriksa)) }}" disabled></td>
                                                @elseif($user->hasRole(['Auditor']))
                                                    <td><input type="date" class="form-control" value="{{ date('Y-m-d', strtotime($dopenjualan->tanggal_pembuat)) }}" disabled></td>
                                                    <td><input type="date" class="form-control" value="{{ date('Y-m-d', strtotime($dopenjualan->tanggal_penyetuju)) }}" disabled></td>
                                                    <td><input type="date" class="form-control" name="tanggal_pemeriksa" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"></td>
                                                @endif
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <div class="text-end mt-1 mb-4 me-2">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a href="{{ route('dopenjualan.index') }}" class="btn btn-secondary" type="button">Back</a>
                    </div>
                </form>
        </div>

    </div>
</div>
</div>

</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#delivery_order_section').show();

        $('#distribusi').change(function() {
            if ($(this).val() === 'Diambil') {
                $('#delivery_order_section').hide();
            } else {
                $('#delivery_order_section').show();
            }
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('nama_customer').addEventListener('change', function() {
            var pointInput = document.getElementById('point');
            var selectedOption = this.options[this.selectedIndex];
            var pointValue = selectedOption.getAttribute('data-point');
            pointInput.value = pointValue;
            var hpInput = document.getElementById('nohandphone');
            var selectedOption = this.options[this.selectedIndex];
            var hpValue = selectedOption.getAttribute('data-hp');
            hpInput.value = hpValue;
        });
    });
</script>
<script>
    $(document).ready(function() {
        function updateHargaSatuan(select) {
            var index = select.selectedIndex;
            var hargaSatuanInput = $('#harga_satuan_0');
            var selectedOption = $(select).find('option').eq(index);
            var hargaProduk = selectedOption.data('harga');
            hargaSatuanInput.val(hargaProduk);
        }
        $('#nama_produk').on('change', function() {
            updateHargaSatuan(this);
        });
    });
</script>

<script>
    document.getElementById("pembayaran").addEventListener("change", function() {
        var pembayaran = this.value;

        document.getElementById("inputCash").style.display = "none";
        document.getElementById("inputTransfer").style.display = "none";

        if (pembayaran === "cash") {
            document.getElementById("inputCash").style.display = "block";
        } else if (pembayaran === "transfer") {
            document.getElementById("inputTransfer").style.display = "block";
        }
    });
</script>
<script>
    var usedInvoiceNumbers = [1001, 1003, 1005];
    // Function to generate the invoice based on certain criteria
    function generateInvoice() {
        var invoicePrefix = "INV";
        var currentDate = new Date();
        var year = currentDate.getFullYear();
        var month = (currentDate.getMonth() + 1).toString().padStart(2, '0'); // Adding leading zero if necessary
        var day = currentDate.getDate().toString().padStart(2, '0'); // Adding leading zero if necessary
        var nextInvoiceNumber = 2;
        while (usedInvoiceNumbers.includes(nextInvoiceNumber)) {
            nextInvoiceNumber++;
        }
        var generatedInvoice = invoicePrefix + year + month + day + nextInvoiceNumber;

        // Update the value of the invoice input field
        document.getElementById('no_invoice').value = generatedInvoice;
    }

    // Call the function to generate the invoice when the page loads
    generateInvoice();
</script>
<script>
    // Function to update date to today's date
    function updateDate(element) {
        var today = new Date().toISOString().split('T')[0];
        element.value = today;
    }

    // updateDate(document.getElementById('tanggal_pembuat'));
    // updateDate(document.getElementById('tanggal_kirim'));
</script>
<script>
    $(document).ready(function() {
        var i = 1;
        $('#add').click(function() {
            var newRow = '<tr class="tr_clone" id="row' + i + '">' +
                '<td>' +
                '<select id="nama_produk_' + i + '" name="nama_produk[]" class="form-control select2" required>' +
                '<option value="">Pilih Produk</option>' +
                '@foreach ($produkjuals as $index => $produk)' +
                '<option value="{{ $produk->id }}" data-harga="{{ $produk->harga_jual }}" data-kode="{{ $produk->kode }}" data-tipe="{{ $produk->tipe }}" data-deskripsi="{{ $produk->deskripsi }}">{{ $produk->nama }}</option>' +
                '@endforeach' +
                '</select>' +
                '<input type="hidden" name="kode_produk[]" id="kode_produk_' + i + '" style="display: none;">' +
                '<input type="hidden" name="tipe_produk[]" id="tipe_produk_' + i + '" style="display: none;">' +
                '<input type="hidden" name="deskripsi_komponen[]" id="deskripsi_komponen_' + i + '" style="display: none;">' +
                '</td>' +
                '<td><input type="number" name="jumlah[]" id="jumlah_' + i + '" oninput="multiply(this)" class="form-control" required></td>' +
                '<td><input type="text" name="unit_satuan[]" id="unit_satuan_' + i + '" class="form-control" required></td>' +
                '<td><input type="text" name="keterangan[]" id="keterangan_' + i + '" class="form-control" required></td>' +
                '<td><button type="button" name="remove" id="' + i + '" class="btn btn_remove"><img src="/assets/img/icons/delete.svg" alt="svg"></button></td>' +
                '</tr>';
            $('#dynamic_field').append(newRow);

            // Menambahkan modal untuk setiap pic
            var picModal = '<div class="modal fade" id="picModal_' + i + '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">' +
                '<div class="modal-dialog" role="document">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '<h5 class="modal-title" id="exampleModalLabel">Form PIC Perangkai ' + i + '</h5>' +
                '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>' +
                '<div class="modal-body">' +
                '<!-- Form untuk PIC Perangkai -->' +
                '</div>' +
                '<div class="modal-footer justify-content-center">' +
                '<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';
            $('body').append(picModal);

            $('#nama_produk_' + i + ', #jenisdiskon_' + i).select2();
            i++
        });

        $('#addtambah').click(function() {
            var newrowtambah = '<tr class="tr_clone" id="row' + i + '">' +
                '<td>' +
                '<select id="nama_produk2_' + i + '" name="nama_produk2[]" class="form-control select2">' +
                '<option value="">Pilih Produk</option>' +
                '@foreach ($produkjuals as $index => $produk)' +
                '<option value="{{ $produk->kode }}" data-harga="{{ $produk->harga_jual }}" data-kode="{{ $produk->kode }}" data-tipe="{{ $produk->tipe }}" data-deskripsi="{{ $produk->deskripsi }}">{{ $produk->nama }}</option>' +
                '@endforeach' +
                '</select>' +
                '<input type="hidden" name="kode_produk[]" id="kode_produk_' + i + '" style="display: none;">' +
                '<input type="hidden" name="tipe_produk[]" id="tipe_produk_' + i + '" style="display: none;">' +
                '<input type="hidden" name="deskripsi_komponen[]" id="deskripsi_komponen_' + i + '" style="display: none;">' +
                '</td>' +
                '<td><input type="number" name="jumlah2[]" id="jumlah2_' + i + '" oninput="multiply(this)" class="form-control"></td>' +
                '<td><input type="text" name="satuan2[]" id="satuan2_' + i + '" class="form-control"></td>' +
                '<td><input type="text" name="keterangan2[]" id="keterangan2_' + i + '" class="form-control"></td>' +
                '<td><button type="button" name="remove" id="' + i + '" class="btn btn_remove"><img src="/assets/img/icons/delete.svg" alt="svg"></button></td>' +
                '</tr>';
            $('#dynamic_field_tambah').append(newrowtambah);
            $('#nama_produk_' + i + ', #jenisdiskon_' + i).select2();
            i++;
        })

        function updateIndicesProduk() {
            var i = 0;

            $('#dynamic_field tr[id^="row"]').each(function() {
                $(this).attr('id', 'row' + i);
                $(this).find('[id^="nama_produk_"]').attr('id', 'nama_produk_' + i).attr('name', 'nama_produk[]').attr('data-index', i);
                $(this).find('[id^="jumlah_"]').attr('id', 'jumlah_' + i).attr('name', 'jumlah[]').attr('data-index', i);
                $(this).find('[id^="satuan_"]').attr('id', 'satuan_' + i).attr('name', 'satuan[]').attr('data-index', i);
                $(this).find('[id^="keterangan_"]').attr('id', 'keterangan_' + i).attr('name', 'keterangan[]').attr('data-index', i);
                $(this).find('.btn_remove').attr('id', i);
                i++;
            });
        }

        $('[id^=nama_produk_]').on('mousedown click focus', function(e) {
            e.preventDefault();
        });

        function updateIndicesProdukTambahan() {
            var i = 0;

            $('#dynamic_field_tambah tr[id^="row"]').each(function() {
                $(this).attr('id', 'row' + i);
                $(this).find('[id^="nama_produk2_"]').attr('id', 'nama_produk2_' + i).attr('name', 'nama_produk2[]').attr('data-index', i);
                $(this).find('[id^="jumlah2_"]').attr('id', 'jumlah2_' + i).attr('name', 'jumlah2[]').attr('data-index', i);
                $(this).find('[id^="satuan2_"]').attr('id', 'satuan2_' + i).attr('name', 'satuan2[]').attr('data-index', i);
                $(this).find('[id^="keterangan2_"]').attr('id', 'keterangan2_' + i).attr('name', 'keterangan2[]').attr('data-index', i);
                $(this).find('.btn_remove').attr('id', i);
                i++;
            });
        }

        $(document).on('click', '#dynamic_field .btn_remove', function() {
            var button_id = $(this).attr('id');
            if ($('#dynamic_field tr').length <= 1) {
                alert('Mohon Jangan Biarkan Data Delivery Order Kosong');
            } else {
                $('#row' + button_id).remove();
            }
        });
        
        $(document).on('click', '#dynamic_field_tambah .btn_remove', function() {
            var button_id = $(this).attr('id');
            $('#row' + button_id).remove();
        });

        $(document).on('change', '[id^=nama_produk]', function() {
            var id = $(this).attr('id').split('_')[2]; // Ambil bagian angka ID
            var selectedOption = $(this).find(':selected');
            $('#kode_produk_' + id).val(selectedOption.data('kode'));
            $('#tipe_produk_' + id).val(selectedOption.data('tipe'));
            $('#deskripsi_komponen_' + id).val(selectedOption.data('deskripsi'));
            updateHargaSatuan(this);
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

        $('#status').change(function(){
            var status = $(this).val();
            if(status == 'DIBATALKAN')
            {
                $('#alasan').show();
            }else{
                $('#alasan').hide();
            }
        });

        function clearFile() {
            $('#bukti').val('');
            $('#preview').attr('src', defaultImg);
        };

        var produkData = [];

        @foreach ($dopenjualan->produk as $produk)
            produkData.push({
                id: {{ $produk->id }},
                jumlah: {{ $produk->jumlah }}
            });
        @endforeach

        // console.log('Produk Data:', produkData);

        $(document).on('input', '.jumlah', function() {
            var inputId = $(this).attr('id');
            var jumlah = parseInt($(this).val(), 10); // Ensure jumlah is parsed as an integer
            var produkId = $(this).data('produk-id'); // Extract the product ID from the data attribute

            var produk = produkData.find(function(item) {
                return item.id == produkId;
            });

            if (produk) {
                if (jumlah > produk.jumlah) {
                    alert('Jumlah diterima tidak boleh lebih dari jumlah sebelumnya');
                    $(this).val(produk.jumlah);
                } else if (jumlah < 0) {
                    alert('Jumlah diterima tidak boleh kurang dari 0');
                    $(this).val(0);
                }
            } else {
                console.error('Produk not found for ID:', produkId);
            }
        });

    });
</script>

@endsection