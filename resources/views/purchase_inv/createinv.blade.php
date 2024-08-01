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
                Invoice Pembelian
            </h4>
        </div>
        <div class="card-body">
            <form action="{{ route('invoicepo.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-sm">
                        <div class="row justify-content-start">
                            <div class="col-md-12 border rounded pt-3 me-1">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="no_inv">No. Invoice</label>
                                            <input type="text" class="form-control" id="no_inv" name="no_inv" placeholder="Nomor Invoice" value="" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="tg_inv">Tanggal Invoice</label>
                                            <input type="date" class="form-control" id="tgl_inv" name="tgl_inv" 
                                            value="{{ old('tgl_inv', now()->format('Y-m-d')) }}" 
                                            min="{{ now()->format('Y-m-d') }}" 
                                            max="{{ now()->addYear()->format('Y-m-d') }}">
                                         </div>
                                     
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="no_po">No. Purchase Order</label>
                                            <input type="text" class="form-control" id="no_po" name="id_po" value="{{ $beli->id }}" readonly hidden>
                                            <input type="hidden" name="type" value="pembelian">
                                            <input type="text" class="form-control" id="no_po" name="no_po" value="{{ $beli->no_po }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="tgl_po">Tanggal PO</label>
                                                <input type="text" class="form-control" id="tgl_po" name="tgl_po" value="{{ \Carbon\Carbon::parse($beli->created_at)->translatedFormat('d F Y') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="suplier">Supplier</label>
                                            <input type="text" class="form-control" id="suplier" name="suplier" value="{{ $beli->supplier->nama }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="lokasi">Lokasi</label>
                                                <input type="text" class="form-control" id="lokasi" name="lokasi" value="{{ $beli->lokasi->nama }}" readonly>
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
                                                    <th>Kode Produk</th>
                                                    <th>Nama Produk</th>
                                                    <th>QTY</th>
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
                                                    <td><input type="text" name="kode[]" id="kode_{{ $index }}" class="form-control" value="{{ $item->produk->kode }}" readonly></td>
                                                    <td><input type="text" name="nama[]" id="nama_{{ $index }}" class="form-control" value="{{ $item->produk->nama }}" readonly></td>
                                                    <td><input type="number" name="qtytrm[]" id="qtytrm_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{ $item->jml_diterima }}" readonly></td>
                                                    <td>
                                                      
                                                            <div class="input-group">
                                                                <span class="input-group-text">Rp. </span> 
                                                                <input type="text" name="harga_display[]" id="harga2_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{ old('harga_display.'.$index) }}" required>
                                                                <input type="hidden" name="harga[]" id="harga_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{ old('harga.'.$index) }}" required>
                                                            </div>
                                                    </td>
                                                    <td>
                                                       
                                                            <div class="input-group">
                                                                <span class="input-group-text">Rp. </span> 
                                                                <input type="text"  name="diskon_display[]" id="diskon2_{{ $index }}" class="form-control" oninput="limitDiskon({{ $index }}), calculateTotal({{ $index }})" value="{{ old('diskon_display.'.$index) }}">
                                                                <input type="hidden" name="diskon[]" id="diskon_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{ old('diskon.'.$index) }}">
                                                            </div>
                                                    </td>
                                                    {{-- <input type="hidden" name="distot_display[]" id="distot_{{ $index }}" class="form-control" value="{{ old('distot_display.'.$index) }}" readonly></td> --}}
                                                    <input type="hidden" name="distot[]" id="distot_int_{{ $index }}" class="form-control" value="{{ old('distot.'.$index) }}" readonly></td>
                                                    
                                                    {{-- <td> 
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                        </div>
                                                     </td>  --}}
                                                    <td>
                                                        
                                                            <div class="input-group">
                                                                <span class="input-group-text">Rp. </span> 
                                                                <input type="text" name="jumlah_display[]" id="jumlah_{{ $index }}" class="form-control" value="{{ old('jumlah_display.'.$index) }}" readonly></td>
                                                                <input type="hidden" name="jumlah[]" id="jumlahint_{{ $index }}" class="form-control" value="{{ old('jumlah.'.$index) }}" readonly></td>
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
                                    <div class="col-lg-7 col-md-6 col-sm-12 mt-4">
                                        <div class="page-btn">
                                            Riwayat Pembayaran
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
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {{-- Data pembayaran --}}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-6 col-sm-12 mt-4">
                                        <div class="total-order">
                                            <ul>
                                                <li>
                                                    <h4>Sub Total</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="sub_total" name="sub_total_dis" class="form-control" onchange="calculateTotal(0)" value="{{ old('sub_total_dis') }}" readonly required>
                                                            <input type="hidden" id="sub_total_int" name="sub_total" class="form-control" onchange="calculateTotal(0)" value="{{ old('sub_total') }}" readonly required>
                                                        </div>
                                                    </h5>
                                                </li>
                                                
                                                <li>
                                                    <h4>PPN
                                                        <select id="jenis_ppn" name="jenis_ppn" class="form-control" required>
                                                            <option value=""> Pilih Jenis PPN</option>
                                                            <option value="exclude">EXCLUDE</option>
                                                            <option value="include" selected>INCLUDE</option>
                                                        </select>
                                                    </h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <input type="text" id="persen_ppn" name="persen_ppn" class="form-control" value="{{ old('persen_ppn') }}" oninput="calculatePPN(this), validatePersen(this)" readonly>
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="nominal_ppn" name="nominal_ppn" class="form-control" value="{{ old('nominal_ppn') }}" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Biaya Pengiriman</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="biaya_ongkir2" name="biaya_ongkir_dis" class="form-control" oninput="calculateTotal(0)" value="{{ old('biaya_ongkir_dis') }}" required>
                                                            <input type="hidden" id="biaya_ongkir" name="biaya_ongkir" class="form-control" oninput="calculateTotal(0)" value="{{ old('biaya_ongkir') }}" required>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li class="total">
                                                    <h4>Total Tagihan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="total_tagihan" name="total_tagihan_dis" class="form-control" readonly value="{{ old('total_tagihan_dis') }}" required>
                                                            <input type="hidden" id="total_tagihan_int" name="total_tagihan" class="form-control" readonly value="{{ old('total_tagihan') }}" required>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Total Diskon</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" class="form-control" required name="total_diskon_display" id="total_diskon_display" oninput="calculateTotal(0)" value="{{ old('total_diskon_display') }}" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>DP</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="dp" name="dp" class="form-control" required readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Sisa Tagihan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="sisa_bayar" name="sisa_bayar" class="form-control" readonly required>
                                                        </div>
                                                    </h5>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row justify-content-end">
                            <div class="col-md-3 col-12 border rounded pt-3 me-1 mt-2">
                                <div class="table-responsive">
                                    <table class="table border rounded">
                                            <thead>
                                                <tr>
                                                    <th>Dibuat :</th>                                              
                                                    {{-- <th>Dibukukan :</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td id="pembuat">
                                                        <input type="hidden" name="pembuat" value="{{ Auth::user()->id ?? '' }}">
                                                        <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" placeholder="{{ Auth::user()->karyawans->nama ?? '' }}" disabled>
                                                    </td>
                                                    {{-- <td id="pembuku">
                                                        <input type="hidden" name="pembuku" value="{{ Auth::user()->id ?? '' }}">
                                                        <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" placeholder="{{ Auth::user()->karyawans->nama ?? '' }}" disabled>
                                                    </td> --}}
                                                </tr>
                                                <tr>
                                                    <td id="status_dibuat">
                                                        <select id="status" name="status_dibuat" class="form-control select2" required>
                                                            <option disabled>Pilih Status</option>
                                                            <option value="TUNDA" {{ old('status_dibuat') == 'TUNDA' || old('status_dibuat') == null ? 'selected' : '' }}>TUNDA</option>
                                                            <option value="DIKONFIRMASI" {{ old('status_dibuat') == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                            {{-- <option value="BATAL" {{ old('status_dibuat') == 'BATAL' ? 'selected' : '' }}>BATAL</option> --}}
                                                        </select>
                                                        
                                                    </td>
                                                    {{-- <td id="status_dibuku">
                                                        <select id="status_dibukukan" name="status_dibuku" class="form-control" disabled>
                                                            <option disabled selected>Pilih Status</option>
                                                            <option value="pending" disabled {{ old('status_dibukukan') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                            <option value="acc" disabled {{ old('status_dibukukan') == 'acc' ? 'selected' : '' }}>Accept</option>
                                                        </select>
                                                    </td> --}}
                                                </tr>
                                                <tr>
                                                    <td id="tgl_dibuat">
                                                        <input type="date" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{ old('tgl_dibuat', now()->format('Y-m-d')) }}" >
                                                    </td>
                                                    {{-- <td id="tgl_dibuku">
                                                        <input type="date" class="form-control" id="tgl_dibukukan" name="tgl_dibukukan" value="" disabled>
                                                    </td> --}}
                                                </tr>
                                            </tbody>
                                        </table>  
                                </div>
                                        <br>                                 
                               </div>
                         </div>

                        <div class="text-end mt-3">
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <a href="{{ route('invoicebeli.index') }}" class="btn btn-secondary" type="button">Back</a>
                        </div>
            </form>
        </div>

    </div>
</div>
</div>



</div>
<div class="modal fade" id="myModalbayar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Tambah Pembayaran</h5>
          <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="supplierForm" action="" method="POST">
                @csrf
            <div class="mb-3">
              <label for="nobay" class="form-label">No Bayar</label>
              {{-- <input type="text" class="form-control" id="invoice_purchase_id" name="invoice_purchase_id" value="{{ $no_invpo }}" hidden> --}}
              <input type="text" class="form-control" id="nobay" name="nobay" value="{{ $no_invpo }}" readonly>
            </div>
            <div class="mb-3">
              <label for="tgl" class="form-label">Tanggal</label>
              <input type="date" class="form-control" id="tgl" name="tgl" value="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="mb-3">
              <label for="metode" class="form-label">Metode</label>
              <select class="form-control select2" id="metode" name="metode">
                <option value="cash">cash</option>
                @foreach ($rekenings as $item)
                <option value="transfer-{{ $item->id }}">transfer - {{ $item->bank }} | {{ $item->nomor_rekening }}</option>
                @endforeach
            </select>
            </div>
            <div class="mb-3">
              <label for="nominal" class="form-label">Nominal</label>
              <input type="number" class="form-control" id="nominal" name="nominal">
            </div>
            <div class="mb-3">
              <label for="bukti" class="form-label">Bukti</label>
              <input type="file" class="form-control" id="bukti" name="bukti">
            </div>
            
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </form>
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

        // Event listener untuk biaya_ongkir2
        document.getElementById('biaya_ongkir2').addEventListener('input', function(e) {
            var rupiah = this.value.replace(/[^\d]/g, ''); // hanya ambil angka
            if (rupiah === "") {
                this.value = "";
                document.getElementById('biaya_ongkir').value = "";
            } else {
                this.value = formatRupiah(rupiah);
                // Set nilai ke input hidden
                document.getElementById('biaya_ongkir').value = unformatRupiah(this.value);
            }
            calculateTotalAll(); // Recalculate total on change
        });

        


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
    // var hargasungguh = qtytrm * harga;
    var distot = (qtytrm * diskon); 
    var jumlah = (qtytrm * harga) - distot;

    // if (diskon > harga) {
    //     // alert('Harga diskon tidak boleh melebihi harga');
    //     toastr.warning('Harga diskon tidak boleh melebihi harga', 'Warning', {
    //         closeButton: true,
    //         tapToDismiss: false,
    //         rtl: false,
    //         progressBar: true
    //     });
    // }

    document.getElementById('jumlahint_' + index).value = jumlah;
    document.getElementById('jumlah_' + index).value = formatRupiah(jumlah.toString());
    document.getElementById('distot_int_' + index).value = distot;
    // document.getElementById('distot_' + index).value = formatRupiah(distot.toString());


    calculateTotalAll(); // Memanggil fungsi untuk menghitung total keseluruhan
    calculatePPN();
}

// Fungsi untuk menghitung total tagihan
function calculateTotalAll() {
    var subTotal = 0;
    var Totaldis = 0;
    // var diskonTotal = parseFloat(unformatRupiah(document.getElementById('diskon_total').value)) || 0;
    var biayaOngkir = parseFloat(unformatRupiah(document.getElementById('biaya_ongkir').value)) || 0;
    var persenPpn = parseFloat(document.getElementById('persen_ppn').value) || 0;

    // Menghitung sub total
    document.querySelectorAll('input[id^="jumlahint_"]').forEach(function(input) {
        subTotal += parseFloat(input.value) || 0;
    });

     // Menghitung tot_disk
     document.querySelectorAll('input[id^="distot_int_"]').forEach(function(input) {
        Totaldis += parseFloat(input.value) || 0;
    });

    // Menghitung PPN berdasarkan jenis_ppn
    var ppn = 0;
    var jenisPpn = document.getElementById('jenis_ppn').value;
    if (jenisPpn === 'exclude') {
        ppn = subTotal * persenPpn / 100;
    }

    // Menghitung total tagihan
    var totalTagihan = subTotal + ppn + biayaOngkir;

    document.getElementById('sub_total').value = formatRupiah(subTotal.toString());
    document.getElementById('total_diskon_display').value = formatRupiah(Totaldis.toString());
    document.getElementById('sub_total_int').value = subTotal;

    document.getElementById('total_tagihan').value = formatRupiah(totalTagihan.toString());
    document.getElementById('total_tagihan_int').value = totalTagihan;
}

// Event listener untuk perubahan jenis PPN
document.getElementById('jenis_ppn').addEventListener('change', function() {
    var selectedOption = this.value;
    var persenPpnInput = document.getElementById('persen_ppn');
    var nominalppn = document.getElementById('nominal_ppn');

    if (selectedOption === 'exclude') {
        persenPpnInput.readOnly = false;
    } else {
        persenPpnInput.readOnly = true;
        persenPpnInput.value = '';
        nominalppn.value = '';

         // Set nilai input menjadi string kosong
    }
    calculateTotalAll(); // Memanggil fungsi untuk menghitung total keseluruhan
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

function calculatePPN()
{
    let ppn_persen = $('#persen_ppn').val();
    let subtotal = $('#sub_total_int').val();
    if(isNaN(ppn_persen) || isNaN(subtotal) || ppn_persen > 100) return;
    let nominal_ppn = ppn_persen * subtotal / 100;
    $('#nominal_ppn').val(formatNumber(nominal_ppn));
}

function limitDiskon(index) 
{
    let diskon = parseInt(unformatRupiah($('#diskon2_' + index).val()));
    let harga_satuan = parseInt(unformatRupiah($('#harga2_' + index).val()));

    if (diskon > harga_satuan) {
        $('#diskon2_' + index).val(formatRupiah(harga_satuan));
        return;
    }

    $('#diskon2_' + index).val(formatRupiah(diskon));
}


</script>
@endsection