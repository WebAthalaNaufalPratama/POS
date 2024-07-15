<?php
use Carbon\Carbon;

setlocale(LC_TIME, 'id_ID');
Carbon::setLocale('id');
?>
@extends('layouts.app-von')

@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Create Invoice</h3>
        </div>
    </div>
</div>

<div class="row">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">
                Invoice Pembelian Inden
            </h4>
        </div>
        <div class="card-body">
            <form action="{{ route('editinvoice.update',['datapo' => $inv_po->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('patch')
                <div class="row">
                    <div class="col-sm">
                        <div class="row justify-content-start">
                            <div class="col-md-12 border rounded pt-3 me-1">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="suplier">Supplier</label>
                                            <input type="text" class="form-control" id="suplier" name="suplier" value="{{ $inv_po->poinden->supplier->nama }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="bulan_inden">Bulan Inden</label>
                                            <input type="text" class="form-control" id="bulan_inden" name="bulan_inden" value="{{ $inv_po->poinden->bulan_inden }}" readonly>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="no_inv">No. Invoice</label>
                                            <input type="text" class="form-control" id="no_inv" name="no_inv" placeholder="Nomor Invoice" value="{{ $inv_po->no_inv }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="tg_inv">Tanggal Invoice</label>
                                            <input type="date" class="form-control" id="tgl_inv" name="tgl_inv" 
                                            value="{{ $inv_po->tgl_inv }}">
                                         </div>
                                        
                                        {{-- <div class="form-group">
                                            <label for="supplier">Supplier</label>
                                            <div class="input-group">
                                                <select id="id_supplier" name="id_supplier" class="form-control" required>
                                                    <option value="">Pilih Nama Supplier</option>
                                                    @foreach ($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                                        <img src="/assets/img/icons/plus1.svg" alt="img" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div> --}}
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="no_po">No. Purchase Order inden</label>
                                            <input type="text" class="form-control" id="no_po" name="id_po" value="{{ $beli->id }}" readonly hidden>
                                            <input type="hidden" name="type" value="poinden">
                                            <input type="text" class="form-control" id="no_po" name="no_po" value="{{ $beli->no_po }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="tgl_po">Tanggal PO</label>
                                                <input type="text" class="form-control" id="tgl_po" name="tgl_po" value="{{ \Carbon\Carbon::parse($beli->created_at)->translatedFormat('d F Y') }}" readonly>
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
                                                    <th>No</th>
                                                    <th hidden>id</th>
                                                    <th>Kode Inden</th>
                                                    <th style="width: 200px">Kategori Produk</th>
                                                    {{-- <th>Kode Produk</th> --}}
                                                    {{-- <th>Keterangan</th> --}}
                                                    <th>Jumlah</th>
                                                    <th>Harga</th>
                                                    <th>Diskon/item</th>
                                                    {{-- <th>Diskon Total</th> --}}
                                                    <th>Total Harga</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="dynamic_field">
                                                @foreach ($produkbelis as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td hidden><input type="text" name="id[]" id="id{{ $index }}" class="form-control" value="{{ $item->id }}" readonly hidden></td>
                                                    <td><input type="text" name="kodeinden[]" id="kodeinden_{{ $index }}" class="form-control" value="{{ $item->kode_produk_inden  }}" readonly></td>
                                                    <td><input type="text" name="kategori[]" id="kategori_{{ $index }}" class="form-control" value="{{ $item->produk->nama }}" readonly></td>
                                                    {{-- <td><input type="text" name="kode[]" id="kode_{{ $index }}" class="form-control" value="{{ $item->produk->kode }}" readonly></td> --}}
                                                    {{-- <td><input type="number" name="ket[]" id="ket_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{ $item->keterangan }}" readonly></td> --}}
                                                    <td><input type="number" name="qtytrm[]" id="qtytrm_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{ $item->jumlahInden }}" readonly></td>

                                                    <td>
                                                      
                                                            <div class="input-group">
                                                                <span class="input-group-text">Rp. </span> 
                                                                <input type="text" name="harga_display[]" id="harga2_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{formatRupiah2($item->harga) }}" required>
                                                                <input type="hidden" name="harga[]" id="harga_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{ $item->harga }}" required>
                                                            </div>
                                                    </td>
                                                    <td>
                                                       
                                                            <div class="input-group">
                                                                <span class="input-group-text">Rp. </span> 
                                                                <input type="text"  name="diskon_display[]" id="diskon2_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{ formatRupiah2($item->diskon) }}">
                                                                <input type="hidden" name="diskon[]" id="diskon_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{ $item->diskon }}">
                                                            </div>
                                                    </td>
                                                    <input type="hidden" name="distot_display[]" id="distot_{{ $index }}" class="form-control" value="{{ old('distot_display.'.$index) }}" readonly></td>
                                                    <input type="hidden" name="distot[]" id="distot_int_{{ $index }}" class="form-control" value="{{ old('distot.'.$index) }}" readonly></td>
                                                    {{-- <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                        </div>
                                                    </td> --}}
                                                    <td>
                                                        
                                                            <div class="input-group">
                                                                <span class="input-group-text">Rp. </span> 
                                                                <input type="text" name="jumlah_display[]" id="jumlah_{{ $index }}" class="form-control" value="{{ formatRupiah2($item->totalharga) }}" readonly></td>
                                                                <input type="hidden" name="jumlah[]" id="jumlahint_{{ $index }}" class="form-control" value="{{ $item->totalharga }}" readonly></td>
                                                            </div>
                                                    </td> 
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-around">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2">
                                <div class="row">
                                    <div class="col-lg-7 col-sm-6 col-6 mt-4 ">
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
                                    <div class="col-lg-5 float-md-right">
                                        <div class="total-order">
                                            <ul>
                                                <li>
                                                    <h4>Sub Total</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            
                                                            <input type="text" id="sub_total" name="sub_total_dis" class="form-control" onchange="calculateTotal(0)" value="{{ formatRupiah2($inv_po->subtotal) }}"readonly required>
                                                            <input type="hidden" id="sub_total_int" name="sub_total" class="form-control" onchange="calculateTotal(0)" value="{{ old('sub_total') }}"readonly required>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Total Diskon</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" class="form-control" required name="diskon_total_dis" id="diskon_total" oninput="calculateTotal(0)"  value="{{ $totalDis }}" readonly>
                                                            {{-- <input type="hidden" class="form-control" required name="diskon_total" id="diskon_total" oninput="calculateTotal(0)"  value="{{ old('diskon_total') }}" > --}}
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>PPN
                                                        <select id="jenis_ppn" name="jenis_ppn" class="form-control" required>
                                                            <option value=""> Pilih Jenis PPN</option>
                                                            <option value="exclude" {{ $inv_po->ppn != null ? 'selected' : ''}}>EXCLUDE</option>
                                                            <option value="include" {{ $inv_po->ppn == null ? 'selected' : ''}}>INCLUDE</option>
                                                        </select>
                                                    </h4>
                                                    <h5 class="col-lg-5">
                                                        <div class="input-group">
                                                            <input type="text" id="persen_ppn" name="persen_ppn" class="form-control" value="11" readonly>
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </h5>
                                                </li>
                                                {{-- <li>
                                                    <h4>Biaya Pengiriman</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="biaya_ongkir2" name="biaya_ongkir_dis"  class="form-control" oninput="calculateTotal(0)" value="{{ old('biaya_ongkir_dis') }}" required>
                                                            <input type="hidden" id="biaya_ongkir" name="biaya_ongkir" class="form-control" oninput="calculateTotal(0)" value="{{ old('biaya_ongkir') }}" required>
                                                        </div>
                                                    </h5>
                                                </li> --}}
                                                <li class="total">
                                                    <h4>Total Tagihan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="total_tagihan" name="total_tagihan_dis" class="form-control" readonly value="{{ formatRupiah2($inv_po->total_tagihan) }}" required>
                                                            <input type="hidden" id="total_tagihan_int" name="total_tagihan" class="form-control" readonly value="{{ old('total_tagihan') }}" required>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>DP</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="dp" name="dp" class="form-control" value="{{ formatRupiah2($inv_po->dp) }}" required readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Sisa Tagihan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="sisa_bayar" name="sisa_bayar" class="form-control" value="{{ $inv_po->sisa }}" readonly required>
                                                        </div>
                                                    </h5>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <div class="row justify-content-start">
                            <div class="col-md-3 border rounded pt-3 me-1 mt-2">
                                @php
                                    $user = Auth::user();
                                @endphp
                             
                                        <table class="table table-responsive border rounded">
                                            <thead>
                                                <tr>
                                                    @if($user->hasRole(['Purchasing']))
                                                    <th>Dibuat</th>
                                                    @elseif($user->hasRole(['Auditor']))                                              
                                                    <th>Dibukukan</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                @if($user->hasRole(['Purchasing']))
                                                    <td id="pembuat">
                                                        <input type="hidden" name="pembuat" value="{{ Auth::user()->id ?? '' }}">
                                                        <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" placeholder="{{ Auth::user()->karyawans->nama ?? '' }}" disabled>
                                                    </td>
                                                @elseif($user->hasRole(['Auditor']))
                                                    <td id="pembuku">
                                                        <input type="hidden" name="pembuku" value="{{ Auth::user()->id ?? '' }}">
                                                        <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" placeholder="{{ Auth::user()->karyawans->nama ?? '' }}" disabled>
                                                    </td>
                                                @endif
                                                </tr>
                                                <tr>
                                                @if($user->hasRole(['Purchasing']))
                                                    <td id="status_dibuat">
                                                        <select id="status_dibuat" name="status_dibuat" class="form-control" required>
                                                            <option disabled selected>Pilih Status</option>
                                                            <option value="TUNDA" {{ $inv_po->status_dibuat == 'TUNDA' ? 'selected' : '' }}>TUNDA</option>
                                                            <option value="DIKONFIRMASI" {{ $inv_po->status_dibuat == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                        </select>
                                                    </td>
                                                @elseif($user->hasRole(['Auditor']))
                                                    <td id="status_dibuku">
                                                        <select id="status_dibukukan" name="status_dibuku" class="form-control">
                                                            <option disabled selected>Pilih Status</option>
                                                            <option value="TUNDA" {{ $inv_po->status_dibukukan == 'TUNDA' ? 'selected' : '' }}>TUNDA</option>
                                                            <option value="DIKONFIRMASI" {{ $inv_po->status_dibukukan == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                        </select>
                                                    </td>
                                                @endif
                                                </tr>
                                                <tr>
                                                @if($user->hasRole(['Purchasing']))
                                                    <td id="tgl_dibuat">
                                                        <input type="date" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{ now()->format('Y-m-d') }}" >
                                                    </td>
                                                @elseif($user->hasRole(['Auditor']))
                                                    <td id="tgl_dibuku">
                                                        <input type="date" class="form-control" id="tgl_dibukukan" name="tgl_dibukukan" value="{{ now()->format('Y-m-d') }}">
                                                    </td>
                                                @endif
                                                </tr>
                                            </tbody>
                                        </table>  
                                        <br>                                 
                               </div>
                         </div>

                        <div class="text-end mt-3">
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <a href="" class="btn btn-secondary" type="button">Back</a>
                        </div>
            </form>
        </div>

    </div>
</div>
</div>



</div>


{{-- <input type="text" name="rupiah" id="rupiah"> --}}

@endsection

@section('scripts')
<script>

function formatRupiah(angka) {
    var reverse = angka.toString().split('').reverse().join('');
    var ribuan = reverse.match(/\d{1,3}/g);
    ribuan = ribuan.join('.').split('').reverse().join('');
    return ribuan;
}

function unformatRupiah(formattedValue) {
    return formattedValue.replace(/\./g, '');
}

    // Event listener untuk diskon_total2

    // document.getElementById('diskon_total2').addEventListener('input', function(e) {
    //         var rupiah = this.value.replace(/[^\d]/g, ''); // hanya ambil angka
    //         if (rupiah === "") {
    //             this.value = "";
    //             document.getElementById('diskon_total').value = "";
    //         } else {
    //             this.value = formatRupiah(rupiah);
    //             // Set nilai ke input hidden
    //             document.getElementById('diskon_total').value = unformatRupiah(this.value);
    //         }
    //         calculateTotalAll(); // Recalculate total on change
    //     });

        // // Event listener untuk biaya_ongkir2
        // document.getElementById('biaya_ongkir2').addEventListener('input', function(e) {
        //     var rupiah = this.value.replace(/[^\d]/g, ''); // hanya ambil angka
        //     if (rupiah === "") {
        //         this.value = "";
        //         document.getElementById('biaya_ongkir').value = "";
        //     } else {
        //         this.value = formatRupiah(rupiah);
        //         // Set nilai ke input hidden
        //         document.getElementById('biaya_ongkir').value = unformatRupiah(this.value);
        //     }
        //     calculateTotalAll(); // Recalculate total on change
        // });

        


document.addEventListener('DOMContentLoaded', function() {
    @foreach ($produkbelis as $index => $item)
    // Event listener untuk harga2_{{ $index }}
    document.getElementById('harga2_{{ $index }}').addEventListener('input', function(e) {
        var rupiah = this.value.replace(/[^\d]/g, ''); // hanya ambil angka
        if (rupiah === "") {
            this.value = "";
            document.getElementById('harga_{{ $index }}').value = "";
        } else {
            this.value = formatRupiah(rupiah);
            // Set nilai ke input hidden
            document.getElementById('harga_{{ $index }}').value = unformatRupiah(this.value);
        }
        calculateTotal({{ $index }}); // Recalculate total on change
    });

    // Event listener untuk diskon2_{{ $index }}

    document.getElementById('diskon2_{{ $index }}').addEventListener('input', function(e) {
        var rupiah = this.value.replace(/[^\d]/g, ''); // hanya ambil angka
        if (rupiah === "") {
            this.value = "";
            document.getElementById('diskon_{{ $index }}').value = "";
        } else {
            this.value = formatRupiah(rupiah);
            // Set nilai ke input hidden
            document.getElementById('diskon_{{ $index }}').value = unformatRupiah(this.value);
        }
        calculateTotal({{ $index }}); // Recalculate total on change
    });

    @endforeach
});

// Fungsi untuk menghitung total harga per baris
function calculateTotal(index) {
    var qtytrm = parseFloat(document.getElementById('qtytrm_' + index).value) || 0;
    var harga = parseFloat(unformatRupiah(document.getElementById('harga_' + index).value)) || 0;
    var diskon = parseFloat(unformatRupiah(document.getElementById('diskon_' + index).value)) || 0;
    console.log(harga);
   
    var distot = (qtytrm * diskon); 
    var jumlah = (qtytrm * harga) - distot;

    if (diskon > harga) {
        alert('Harga diskon tidak boleh melebihi harga');
    }

    document.getElementById('jumlahint_' + index).value = jumlah;
    document.getElementById('jumlah_' + index).value = formatRupiah(jumlah.toString());
    document.getElementById('distot_int_' + index).value = distot;
    document.getElementById('distot_' + index).value = formatRupiah(distot.toString());

    calculateTotalAll(); // Memanggil fungsi untuk menghitung total keseluruhan
}

// Fungsi untuk menghitung total tagihan
function calculateTotalAll() {
    var subTotal = 0;
    var Totaldis = 0;
    
    // var diskonTotal = parseFloat(unformatRupiah(document.getElementById('diskon_total').value)) || 0;
    // var biayaOngkir = parseFloat(unformatRupiah(document.getElementById('biaya_ongkir').value)) || 0;
    var persenPpn = parseFloat(document.getElementById('persen_ppn').value) || 0;

    // Menghitung sub total
    document.querySelectorAll('input[id^="jumlahint_"]').forEach(function(input) {
        subTotal += parseFloat(input.value) || 0;
    });
    document.querySelectorAll('input[id^="distot_int_"]').forEach(function(input) {
        Totaldis += parseFloat(input.value) || 0;
    });
    console.log(subTotal);

    // Menghitung PPN berdasarkan jenis_ppn
    var ppn = 0;
    var jenisPpn = document.getElementById('jenis_ppn').value;
    if (jenisPpn === 'exclude') {
        ppn = subTotal * persenPpn / 100;
    }

    // Menghitung total tagihan
    // var totalTagihan = subTotal - diskonTotal + ppn + biayaOngkir;
    var totalTagihan = subTotal + ppn ;

    // Menampilkan hasil yang benar jika diskon lebih besar dari subtotal
    // if (diskonTotal > subTotal) {
    //     totalTagihan = -Math.abs(totalTagihan);
    // }

    document.getElementById('sub_total').value = formatRupiah(subTotal.toString());
    document.getElementById('diskon_total').value = formatRupiah(Totaldis.toString());
    document.getElementById('sub_total_int').value = subTotal;

    document.getElementById('total_tagihan').value = formatRupiah(totalTagihan.toString());
    document.getElementById('total_tagihan_int').value = totalTagihan;
}

// Event listener untuk perubahan jenis PPN
function jenisPPN() {
    var selectedOption = $('#jenis_ppn').val();
    var persenPpnInput = document.getElementById('persen_ppn');
    if (selectedOption === 'exclude') {
        persenPpnInput.readOnly = false;
    } else {
        persenPpnInput.readOnly = true;
        persenPpnInput.value = ''; // Set nilai input menjadi string kosong
    }
    calculateTotalAll(); // Memanggil fungsi untuk menghitung total keseluruhan
};

$(document).ready(function () {
    $('#jenis_ppn').change(jenisPPN());
});

// Panggil fungsi calculateTotal ketika ada perubahan pada input harga atau diskon per baris
document.querySelectorAll('input[id^="harga_"], input[id^="diskon_"]').forEach(function(input) {
    input.addEventListener('input', function() {
        var index = this.id.split('_')[1];
        calculateTotal(index);
    });
});

// Panggil fungsi calculateTotalAll ketika ada perubahan pada input jumlah, diskon total, biaya ongkir, atau persen PPN
document.querySelectorAll('input[name^="jumlah"], #diskon_total, #biaya_ongkir, #persen_ppn').forEach(function(input) {
    input.addEventListener('input', function() {
        calculateTotalAll(); // Memanggil fungsi untuk menghitung total keseluruhan
    });
});
    


// function unformatRupiah(rupiah) {
//     // Menghapus titik sebagai pemisah ribuan
//     var unformatted = rupiah.replace(/\./g, '');
//     // Menghapus 'Rp.' jika ada
//     unformatted = unformatted.replace('Rp. ', '');
//     // Mengonversi ke integer
//     return parseInt(unformatted);
// }

// $(document).ready(function() {
//         $("#metode").select2({
//         dropdownParent: $("#myModalbayar")
//         });
    
//          $('#jenis_ppn').change(function() {
//             var selectedOption = $(this).val();
//             if (selectedOption === 'exclude') {
//                 $('#persen_ppn').prop('readonly', false);
//             } else {
//                 $('#persen_ppn').prop('readonly', true);
//                 $('#persen_ppn').val(''); // Set nilai input menjadi string kosong
//             }
//             calculateTotalAll(); // Memanggil fungsi untuk menghitung total keseluruhan
//         });
    
//         // Fungsi untuk menghitung total tagihan
//         function calculateTotalAll() {
//             var subTotal = 0;
//             var diskonTotal = parseFloat($('#diskon_total').val()) || 0;
//             var biayaOngkir = parseFloat($('#biaya_ongkir').val()) || 0;
//             var persenPpn = parseFloat($('#persen_ppn').val()) || 0;
    
//             // Menghitung sub total
//             $('input[name^="jumlah"]').each(function() {
//                 subTotal += parseFloat($(this).val()) || 0;
//             });
    
//             // Menghitung PPN berdasarkan jenis_ppn
//             var ppn = 0;
//             var jenisPpn = $('#jenis_ppn').val();
//             if (jenisPpn === 'exclude') {
//                 ppn = (subTotal - diskonTotal) * persenPpn / 100;
//             }
    
//             // Menghitung total tagihan
//             var totalTagihan = subTotal - diskonTotal + ppn + biayaOngkir;
    
//             // Memperbarui nilai total tagihan
//             $('#sub_total').val(subTotal);
            
//             $('#total_tagihan').val(formatRupiah(totalTagihan));
//             $('#total_tagihan_int').val(totalTagihan);
//         }
    
//         // Panggil fungsi calculateTotal ketika ada perubahan pada input jumlah atau diskon
//         $('input[name^="jumlah"], #diskon_total, #biaya_ongkir, #persen_ppn').on('input', function() {
//             calculateTotalAll(); // Memanggil fungsi untuk menghitung total keseluruhan
//         });
    
//         // Fungsi untuk menghitung total harga per baris
//         function calculateTotal(index) {
//             var qtytrm = parseFloat($('#qtytrm_' + index).val()) || 0;
//             var harga = parseFloat($('#harga_' + index).val()) || 0;
//             var diskon = parseFloat($('#diskon_' + index).val()) || 0;
//             var jumlah = qtytrm * harga - diskon;
    
//             $('#jumlah_' + index).val(jumlah);
//             calculateTotalAll(); // Memanggil fungsi untuk menghitung total keseluruhan
//         }
    
//         // Panggil fungsi calculateTotal ketika ada perubahan pada input harga atau diskon per baris
//         $('input[name^="harga"], input[name^="diskon"]').on('input', function() {
//             var index = $(this).attr('id').split('_')[1];
//             calculateTotal(index);
//         });
//      });



</script>
@endsection