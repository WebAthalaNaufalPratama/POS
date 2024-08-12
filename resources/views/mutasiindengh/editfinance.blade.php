
@extends('layouts.app-von')

@section('content')
<style>
    .form-control {
        min-width: 200px; /* Adjust as necessary */
    }
    .form-control-banyak{
        min-width: 200px; /* Adjust as necessary */
    }
    input[readonly] {
    background-color: #e9ecef; /* Warna latar belakang abu-abu */
    color: #6c757d; /* Warna teks abu-abu */
    }
    .input-group .form-control-banyak {
        border: 1px solid #ced4da; /* Nilai border default */
        border-radius: 0.25rem; /* Radius default untuk border */
    }

</style>
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Edit Mutasi Inden ke Galery/GreenHouse (Finance)</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{route('mutasiindengh.index')}}">Mutasi</a>
                </li>
                <li class="breadcrumb-item active">
                    Inden ke {{ $data->lokasi->nama }}
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="card">
       
        <div class="card-body">
            <form action="{{ route('mutasiindengh.update', $data->id ) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('patch')
                <div class="row">
                    <div class="col-sm">
                        <div class="row justify-content-start">
                            <div class="col-md-12 border rounded pt-3 me-1">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="no_mutasi">No Mutasi</label>
                                            <input type="text" id="no_mutasi" name="no_mutasi" class="form-control" value="{{ $data->no_mutasi }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="tgl_kirim">Tanggal Kirim</label>
                                            <input type="date" class="form-control" id="tgl_kirim" name="tgl_dikirim" value="{{ $data->tgl_dikirim }}" readonly>
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
                                    {{-- <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="supplier">Supplier Pengirim</label>
                                            <select id="supplier" name="supplier_id" class="form-control select2" readonly>
                                                <option value="">Pilih Supplier</option>
                                                @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ $supplier->id == $data->supplier->id  ? 'selected' : ''}} disabled>{{ $supplier->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="penerima">Lokasi Penerima</label>
                                            <select id="penerima" name="lokasi_id" class="form-control select2" required readonly>
                                                <option value="">Pilih Lokasi</option>
                                                @foreach ($lokasi as $lok)
                                                <option value="{{ $lok->id }}" {{ $lok->id == $data->lokasi->id  ? 'selected' : ''}} disabled>{{ $lok->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="supplier">Supplier</label>
                                                <input type="text" class="form-control" id="supplier" name="supplier" value="{{ $data->supplier->nama }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="penerima">Lokasi</label>
                                            <input type="text" class="form-control" id="lokasi" name="lokasi" value="{{ $data->lokasi->nama }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tgl_terima">Tanggal Diterima</label>
                                            <input type="date" class="form-control" id="tgl_diterima" name="tgl_diterima"  value="{{ $data->tgl_diterima }}" readonly>
                                         </div>
                                        <div class="form-group">
                                            <div class="custom-file-container" data-upload-id="myFirstImage">
                                                <label>Bukti<a href="javascript:void" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"> clear</a>
                                                </label>
                                                <label class="custom-file-container__custom-file">
                                                    <input type="file" id="bukti" class="custom-file-container__custom-file__custom-file-input" name="bukti" accept="image/*">
                                                    <span class="custom-file-container__custom-file__custom-file-control"></span>
                                                </label>
                                                <span class="text-danger">max 2mb</span>
                                                <img id="preview" src="{{ old('bukti', ($data->bukti ? '/storage/' . $data->bukti : '')) }}" alt="your image" />
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
                                                    <th>Bulan Inden</th>
                                                    <th>Kode Inden</th>
                                                    <th>Kategori</th>
                                                    <th>QTY Kirim</th>
                                                    <th>QTY Terima</th>
                                                    <th>Kondisi</th>
                                                    <th>Biaya Perawatan</th>
                                                    <th>Total Biaya Perawatan</th>
                                                    {{-- <th></th> --}}
                                                </tr>
                                            </thead>
                                            <tbody id="dynamic_field">
                                                @foreach ($data->produkmutasi as $index => $produk)
                                                    <tr>
                                                        <td>
                                                            <input type="hidden" class="form-control" name="id[]" id="id_{{ $index }}" value="{{ $produk->id }}" readonly>
                                                            <input type="text" class="form-control" name="bulan_inden[]" id="bulan_inden_{{ $index }}" value="{{ $produk->produk->bulan_inden }}" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="kode_inden[]" id="kode_inden_{{ $index }}" value="{{ $produk->produk->kode_produk_inden }}" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="hidden" class="form-control" name="idinven[]" id="idinven_{{ $index }}" value="{{ $produk->inventoryinden_id }}" readonly>
                                                            <input type="text" class="form-control" name="kategori1[]" id="kategori1_{{ $index }}" value="{{ $produk->produk->produk->nama }}" readonly>
                                                            <input type="hidden" class="form-control" name="kode[]" id="kode_{{ $index }}" value="{{ $produk->produk->produk->kode }}" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="qtykrm[]" id="qtykrm_{{ $index }}" class="form-control" value="{{ $produk->jml_dikirim ?? 0 }}" oninput="validateQty({{ $index }})" data-jumlah="{{ $produk->produk->jumlah }}" required readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="qtytrm[]" id="qtytrm_{{ $index }}" class="form-control" value="{{ $produk->jml_diterima ?? 0 }}" onchange="calculateTotal({{ $index }})" readonly>
                                                        </td>
                                                        <td>
                                                            <select id="kondisi_{{ $index }}" name="kondisi[]" class="form-control" onchange="showInputType(0)" readonly>
                                                                <option value="">Pilih Kondisi</option>
                                                                @foreach ($kondisis as $kondisi)
                                                                    <option value="{{ $kondisi->id }}" {{ $kondisi->id == $produk->kondisi_id ? 'selected' : '' }} disabled>{{ $kondisi->nama }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <span class="input-group-text">Rp. </span> 
                                                                <input type="text" name="rawat2[]" id="rawat2_{{ $index }}" value="{{ $produk->biaya_rawat ? formatRupiah2($produk->biaya_rawat) : 0 }}" class="form-control-banyak" oninput="calculateTotal({{ $index }})">
                                                                <input type="hidden" name="rawat[]" id="rawat_{{ $index }}" value="{{ $produk->biaya_rawat ?? 0 }}" class="form-control" oninput="calculateTotal({{ $index }})">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <span class="input-group-text">Rp. </span> 
                                                                <input type="text" name="jumlah_display[]" id="jumlah_{{ $index }}" value="{{ $produk->totalharga ? formatRupiah2($produk->totalharga) : 0 }}" class="form-control-banyak" oninput="calculateTotal({{ $index }})" readonly>
                                                                <input type="hidden" name="jumlah[]" id="jumlahint_{{ $index }}" value="{{ $produk->totalharga ?? 0 }}" class="form-control" oninput="calculateTotal({{ $index }})">
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
                            <div class="col-12 border rounded pt-3 me-1 mt-2">
                                <div class="row">
                                    <div class="col-lg-7 col-md-6 col-12 mt-4">
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
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {{-- Data pembayaran --}}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-6 col-12 mt-4">
                                        <div class="total-order">
                                            <ul>
                                                <li>
                                                    <h4>Sub Total</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="sub_total" name="sub_total_dis" class="form-control" value="{{ $data->subtotal ? formatRupiah2($data->subtotal) : 0 }}" onchange="calculateTotal(0)" readonly>
                                                            <input type="hidden" id="sub_total_int" name="sub_total" class="form-control" value="{{ $data->subtotal ?? 0 }}" onchange="calculateTotal(0)" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Biaya Perawatan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="biaya_rwt2" name="biaya_rwt_dis" class="form-control" value="{{ $data->biaya_perawatan ? formatrupiah2($data->biaya_perawatan): 0 }}" oninput="calculateTotal(0)">
                                                            <input type="hidden" id="biaya_rwt" name="biaya_rwt" class="form-control" value="{{ $data->biaya_perawatan ?? 0 }}" oninput="calculateTotal(0)">
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Biaya Pengiriman</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="biaya_ongkir2" name="biaya_ongkir_dis" class="form-control" value="{{ $data->biaya_pengiriman ? formatRupiah2($data->biaya_pengiriman) : 0 }}" oninput="calculateTotal(0)" required>
                                                            <input type="hidden" id="biaya_ongkir" name="biaya_ongkir" class="form-control" value="{{ $data->biaya_pengiriman ?? 0 }}" oninput="calculateTotal(0)" required>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li class="total">
                                                    <h4>Total Tagihan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="total_tagihan" name="total_tagihan_dis" value="{{ $data->total_biaya ? formatRupiah2($data->total_biaya): 0 }}" class="form-control" readonly>
                                                            <input type="hidden" id="total_tagihan_int" name="total_tagihan" value="{{ $data->total_biaya ?? 0 }}" class="form-control" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                {{-- <li>
                                                    <h4>Sisa Tagihan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="sisa_bayar" name="sisa_bayar" class="form-control" readonly>
                                                        </div>
                                                    </h5>
                                                </li> --}}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row justify-content-start">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2"> 
                                <div class="table-responsive">
                                    <table class="table border rounded">
                                        <thead>
                                            <tr>
                                                <th>Dibuat</th>                                              
                                                <th>Diterima</th>                                              
                                                <th>Dibukukan</th>
                                                <th>Diperiksa</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td id="pembuat">
                                                    {{-- <input type="hidden" name="pembuat" value="{{ Auth::user()->id ?? '' }}"> --}}
                                                    <input type="text" class="form-control" value="{{ $pembuat ?? '' }} ({{ $jabatanbuat ?? '' }})" readonly>
                                                </td>
                                                <td id="penerima">
                                                    <input type="hidden" name="penerima" value="{{ Auth::user()->id ?? '' }}">
                                                    @if($penerima)
                                                    <input type="text" class="form-control" value="{{ $penerima ?? '' }} ({{ $jabatanterima ?? '' }})" readonly>
                                                    @else
                                                    <input type="text" class="form-control" value="Nama (Admin Gallery)" readonly>
                                                    @endif
                                                </td>
                                                <td id="pemeriksa">
                                                    <input type="hidden" name="pemeriksa" value="{{ Auth::user()->id ?? '' }}">
                                                    @if($pemeriksa)
                                                    <input type="text" class="form-control" value="{{ $pemeriksa ?? '' }} ({{ $jabatanperiksa ?? '' }})" readonly>
                                                    @else
                                                    <input type="text" class="form-control" value="Nama (Auditor)" readonly>
                                                    @endif
                                                </td>
                                                <td id="pembuku">
                                                    <input type="hidden" name="pembuku" value="{{ Auth::user()->id ?? '' }}">
                                                    <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" placeholder="{{ Auth::user()->karyawans->nama ?? '' }}" required readonly>
                                                </td>
                                                
                                            </tr>
                                            <tr>
                                                <td id="status_dibuat">
                                                    <input type="text" class="form-control" value="{{ $data->status_dibuat }}" readonly>
                                                </td>
                                                <td id="status_diterima">
                                                    <input type="text" class="form-control" value="{{ $data->status_diterima ?? '-'}}" readonly>
                                                </td>
                                                <td id="status_diperiksa">
                                                    <input type="text" class="form-control" value="{{ $data->status_diperiksa ?? '-'}}" readonly>
                                                </td>
                                                <td id="status_dibukukan">
                                                    <select id="status_dibukukan" name="status_dibukukan" class="form-control" required>
                                                        <option disabled>Pilih Status</option>
                                                        <option value="TUNDA" {{ $data->status_dibukukan == 'TUNDA' ? 'selected' : '' }}>TUNDA</option>
                                                        <option value="MENUNGGU PEMBAYARAN" {{ $data->status_dibukukan == 'MENUNGGU PEMBAYARAN' || $data->status_dibukukan == null ? 'selected' : '' }}>MENUNGGU PEMBAYARAN</option>
                                                        {{-- <option value="BATAL" {{ $data->status_dibuat == 'BATAL' ? 'selected' : '' }}>BATAL</option> --}}
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td id="tgl_dibuat">
                                                    <input type="text" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{ tanggalindo($data->tgl_dibuat) }}"readonly >
                                                </td>
                                                <td id="tgl_diterima">
                                                    <input type="text" class="form-control" id="tgl_diterima" name="tgl_diterima" value="{{ $data->tgl_diterima_ttd ? tanggalindo($data->tgl_diterima_ttd) : '-' }}" readonly>
                                                </td>
                                                <td id="tgl_diperiksa">
                                                    <input type="text" class="form-control" id="tgl_diperiksa" name="tgl_diperiksa" value="{{ $data->tgl_diperiksa ? tanggalindo($data->tgl_diperiksa) : '-' }}"  readonly>
                                                </td>
                                                <td id="tgl_dibukukan">
                                                    <input type="date" class="form-control" id="tgl_dibukukan" name="tgl_dibukukan" value="{{ now()->format('Y-m-d') }}" required>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>  
                                <br>                                 
                            </div>
                        </div>
                        

                        <div class="text-end mt-3">
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <a href="{{ route('mutasiindengh.index') }}" class="btn btn-secondary" type="button">Back</a>
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
              <input type="text" class="form-control" id="nobay" name="nobay" value="" readonly>
            </div>
            <div class="mb-3">
              <label for="tgl" class="form-label">Tanggal</label>
              <input type="date" class="form-control" id="tgl" name="tgl" value="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="mb-3">
              <label for="metode" class="form-label">Metode</label>
              {{-- <select class="form-control select2" id="metode" name="metode">
                <option value="cash">cash</option>
                @foreach ($rekenings as $item)
                <option value="transfer-{{ $item->id }}">transfer - {{ $item->bank }} | {{ $item->nomor_rekening }}</option>
                @endforeach
            </select> --}}
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

@endsection

@section('scripts')
 <script>

    // Fungsi untuk mengubah format input menjadi format Rupiah
function formatRupiah(angka) {
    var reverse = angka.toString().split('').reverse().join('');
    var ribuan = reverse.match(/\d{1,3}/g);
    ribuan = ribuan.join('.').split('').reverse().join('');
    return ribuan;
}

function unformatRupiah(formattedValue) {
    return formattedValue.replace(/\./g, '');
}

function inputToRupiah(inputId) {
    var inputValue = document.getElementById(inputId).value;
    var unformattedValue = unformatRupiah(inputValue);
    var formattedValue = formatRupiah(unformattedValue);
    document.getElementById(inputId).value = formattedValue;
}

function rupiahToInput(inputId) {
    var inputValue = document.getElementById(inputId).value;
    var unformattedValue = unformatRupiah(inputValue);
    document.getElementById(inputId).value = unformattedValue;
}

document.querySelectorAll('input[id^="rawat2_"], input[id^="biaya_rwt2"], input[id^="biaya_ongkir2"]').forEach(function(input) {
    input.addEventListener('focus', function() {
        rupiahToInput(this.id); // Ketika fokus, ubah ke format input biasa
    });
    input.addEventListener('blur', function() {
        inputToRupiah(this.id); // Ketika kehilangan fokus, ubah kembali ke format Rupiah
        calculateTotal(0); // Hitung kembali total setelah perubahan
    });
});

function calculateTotal(index) {
    
    var qtyKirimElem = document.getElementById('qtykrm_' + index);
    var rawatElem = document.getElementById('rawat2_' + index);

    if (qtyKirimElem && rawatElem) {
        var qtyTerima = parseFloat(qtyKirimElem.value) || 0;
        var rawat = parseFloat(unformatRupiah(rawatElem.value)) || 0;
        var totalPerBaris = qtyTerima * rawat;

        document.getElementById('jumlah_' + index).value = formatRupiah(totalPerBaris);
        document.getElementById('jumlahint_' + index).value = totalPerBaris;
        document.getElementById('rawat_' + index).value = rawat;

        calculateTotalAll();
    }
}

function calculateTotalAll() { 
    var subTotal = 0;
    var biaya_ongkir = parseFloat(unformatRupiah(document.getElementById('biaya_ongkir2').value)) || 0;
    var biaya_perawatan = parseFloat(unformatRupiah(document.getElementById('biaya_rwt2').value)) || 0;
    // console.log($('#biaya_rwt2').val());

       
    document.querySelectorAll('input[id^="jumlahint_"]').forEach(function(input) {
            subTotal += parseFloat(input.value) || 0;
    });

    var totalTagihan = subTotal + biaya_ongkir + biaya_perawatan;

        document.getElementById('sub_total').value = formatRupiah(subTotal);
        document.getElementById('total_tagihan').value = formatRupiah(totalTagihan.toString());
        document.getElementById('sub_total_int').value = subTotal;
        document.getElementById('biaya_rwt').value = biaya_perawatan;
        document.getElementById('biaya_ongkir').value = biaya_ongkir;
        document.getElementById('total_tagihan_int').value = totalTagihan;


}

function validateQty(index) {
            let qtyKrm = parseFloat($(`#qtykrm_${index}`).val());
            let jumlah = parseFloat($(`#qtykrm_${index}`).data('jumlah')); // Ambil jumlah dari atribut data

            console.log('Validating quantity:', qtyKrm, 'against:', jumlah); // Debug log

            if (qtyKrm < 0) {
                $(`#qtykrm_${index}`).val(0);
            } else if (qtyKrm > jumlah) {
                $(`#qtykrm_${index}`).val(jumlah);
            }
            calculateTotal(index);
    } 

    $(document).ready(function() {

        $('.select2').select2();

        $(document).on('input change', 'input[name="qtykrm[]"]', function () {
        let index = $(this).attr('id').split('_')[1];
        validateQty(index);
        });

        var t = $('#supplier').val()
        populateBulan(t)
        for (let index = 0; index < $('[id^=bulan_inden_]').length; index++) {
                  
            bindSelectEvents(index);
        }


            if ($('#preview').attr('src') === '') {
                $('#preview').attr('src', defaultImg);
            }

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

           
         // Tambahkan baris baru
    var i = $('[id^=bulan_inden_]').length;
    var bulanIndenData = [];

    $('#add').click(function() {
        var newRow = `
            <tr id="row${i}">
                <td>
                    <select class="form-control" id="bulan_inden_${i}" name="bulan_inden[]">
                        <option value="">Pilih Bulan Inden</option>
                        ${bulanIndenData.map(bulan => `<option value="${bulan}">${bulan}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <select class="form-control" id="kode_inden_${i}" name="kode_inden[]">
                        <option value="">Pilih Kode Inden</option>
                    </select>
                </td>
                <td><input type="text" class="form-control" name="kategori1[]" id="kategori1_${i}" readonly></td>
                <td><input type="number" name="qtykrm[]" id="qtykrm_${i}" class="form-control" onchange="calculateTotal(${i})"></td>
                <td><input type="number" name="qtytrm[]" id="qtytrm_${i}" class="form-control" onchange="calculateTotal(${i})" readonly></td>
                <td>
                    <select id="kondisi_${i}" name="kondisi[]" class="form-control" readonly>
                        <option value="">Pilih Kondisi</option>
                        @foreach ($kondisis as $kondisi)
                         <option value="{{ $kondisi->id }}">{{ $kondisi->nama }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">Rp. </span>
                        <input type="text" name="rawat2[]" id="rawat2_${i}" class="form-control" oninput="calculateTotal(${i})">
                        <input type="hidden" name="rawat[]" id="rawat_${i}" class="form-control">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">Rp. </span>
                        <input type="text" name="jumlah_display[]" id="jumlah_${i}" class="form-control" readonly>
                        <input type="hidden" name="jumlah[]" id="jumlahint_${i}" class="form-control" readonly>
                    </div>
                </td>
                <td><button type="button" name="remove" id="${i}" class="btn btn-danger btn_remove">X</button></td>
            </tr>
        `;

        $('#dynamic_field').append(newRow);
        // bindSelectEvents(i);
        populateBulan(t);

        // Bind event untuk input yang baru ditambahkan
        document.getElementById(`rawat2_${i}`).addEventListener('focus', function() {
            rupiahToInput(this.id);
        });
        document.getElementById(`rawat2_${i}`).addEventListener('blur', function() {
            inputToRupiah(this.id);
            calculateTotal(i);
        });
       
        document.getElementById(`qtytrm_${i}`).addEventListener('input', function() {
            calculateTotal(i);
        });
        i++;
    });

    $(document).on('click', '.btn_remove', function() {
        var button_id = $(this).attr("id");
        $('#row' + button_id).remove();
        calculateTotal(); // Panggil fungsi calculateTotal tanpa parameter setelah penghapusan baris
    });

    function calculateTotal() {
        var totalKeseluruhan = 0;
        var biaya_ongkir = parseFloat(unformatRupiah(document.getElementById('biaya_ongkir2').value)) || 0;
        var biaya_perawatan = parseFloat(unformatRupiah(document.getElementById('biaya_rwt2').value)) || 0;

        document.querySelectorAll('input[id^="jumlahint_"]').forEach(function(input) {
            totalKeseluruhan += parseFloat(input.value) || 0;
        });

        document.getElementById('sub_total').value = formatRupiah(totalKeseluruhan); 
        document.getElementById('sub_total_int').value = totalKeseluruhan; 

        var totalTagihan = totalKeseluruhan + biaya_ongkir + biaya_perawatan;

        document.getElementById('total_tagihan').value = formatRupiah(totalTagihan.toString());
        document.getElementById('total_tagihan_int').value = totalTagihan;

        
    }

});
        // Fungsi untuk mengikat event pada elemen dropdown baru
function bindSelectEvents(index) {
    $('#bulan_inden_' + index).change(function() {
        const supplierId = $('#supplier').val();
        const bulanInden = $(this).val();
        const kodeIndenDropdown = $('#kode_inden_' + index);
        kodeIndenDropdown.empty();
        kodeIndenDropdown.append('<option value="">Pilih Kode Inden</option>');

        $('#kategori1_' + index).val('');
        $('#idinven_' + index).val('');
        $('#qtykrm_'+ index ).val('');

        $('#rawat_'+ index ).val('');
        $('#rawat2_'+ index ).val('');
        $('#jumlah_'+ index ).val('');
        $('#jumlahint_'+ index ).val('');

        if (bulanInden) {
            $.ajax({
                url: `/get-kode-inden/${bulanInden}/${supplierId}`,
                type: 'GET',
                success: function(data) {
                    if (Array.isArray(data)) {
                        data.forEach(function(item) {
                            kodeIndenDropdown.append('<option value="' + item + '">' + item + '</option>');
                        });
                    } else {
                        console.error("Diharapkan array tetapi mendapat", data);
                    }
                },
                error: function() {
                    alert('Gagal mengambil data kode inden');
                }
            });
        }
    });

    $('#kode_inden_' + index).change(function() {
        const supplierId = $('#supplier').val();
        const bulanInden = $('#bulan_inden_' + index).val();
        const kodeInden = $(this).val();
        const kategoriInput = $('#kategori1_' + index);
        const jumlahkirimInput = $('#qtykrm_' + index); 
        const inventoryinden_id = $('#idinven_' + index); 
          
        $('#rawat_'+ index ).val('');
        $('#rawat2_'+ index ).val('');
        $('#jumlah_'+ index ).val('');
        $('#jumlahint_'+ index ).val('');

        if (kodeInden) {
            $.ajax({
                url: `/get-kategori-inden/${kodeInden}/${bulanInden}/${supplierId}`,
                type: 'GET',
                success: function(response) {
                    kategoriInput.val(response.kategori);
                    jumlahkirimInput.val(response.jumlah);
                    inventoryinden_id.val(response.idinven);
                    jumlahkirimInput.data('jumlah', response.jumlah); // Simpan jumlah dalam atribut data
                    console.log('Jumlah saved in data attribute:', response.jumlah); 
                },
              
                error: function() {
                    alert('Gagal mengambil data kategori');
                }
            });
        }
    });
}


$('#supplier').change(function() {
    const supplierId = $(this).val();

    // Kosongkan opsi untuk setiap dropdown bulan_inden
    $('select[id^="bulan_inden_"]').each(function() {
        $(this).empty();
        $(this).append('<option value="">Pilih Bulan Inden</option>');
    });
    $('select[id^="kode_inden_"]').each(function() {
        $(this).empty();
        $(this).append('<option value="">Pilih Kode Inden</option>');
    });
    $('input[name="idinven[]"]').val('');
    $('input[name="kategori1[]"]').val('');
    $('input[name="qtykrm[]"]').val('');
    $('input[name="rawat[]"]').val('');
    $('input[name="rawat2[]"]').val('');
    $('input[name="jumlah[]"]').val('');
    $('input[name="jumlah_display[]"]').val('');
    

    if (supplierId) {
        // Ambil data bulan inden dari server
        $.ajax({
            url: `/get-bulan-inden/${supplierId}`,
            type: 'GET',
            success: function(data) {
                if (Array.isArray(data)) {
                    bulanIndenData = data; // Simpan data bulan inden sebagai array
                    $('select[id^="bulan_inden_"]').each(function() {
                        var bulanIndenDropdown = $(this);
                        data.forEach(function(bulanInden) {
                            bulanIndenDropdown.append('<option value="' + bulanInden + '">' + bulanInden + '</option>');
                        });
                    });
                } else {
                    console.error("Diharapkan array tetapi mendapat", data);
                }
            },
            error: function() {
                alert('Gagal mengambil data bulan inden');
            }
        });
    }
});

$(document).on('change', 'select[id^="bulan_inden_"]', function(){
    var bulanInden = $(this).val();
    var supplier_id = $('#supplier').val();
    var id_row = $(this).attr('id').split('_')[2];
   

    console.log(bulanInden, supplier_id, id_row)
    populateKodeInden(bulanInden, supplier_id, id_row);

});

$(document).on('change', 'select[id^="kode_inden_"]', function() {
        var id_row = $(this).attr('id').split('_')[2];
        var supplierId = $('#supplier').val();
        var bulanInden = $('#bulan_inden_' + id_row).val();
        var kodeInden = $(this).val();
        var kategoriInput = $('#kategori1_' + id_row);
        var jumlahkirimInput = $('#qtykrm_' + id_row);  
        var inventoryinden_id = $('#idinven_' + index);

        $('#rawat_'+ index ).val('');
        $('#rawat2_'+ index ).val('');
        $('#jumlah_'+ index ).val('');
        $('#jumlahint_'+ index ).val('');

        if (kodeInden) {
            $.ajax({
                url: `/get-kategori-inden/${kodeInden}/${bulanInden}/${supplierId}`,
                type: 'GET',
                success: function(response) {
                    kategoriInput.val(response.kategori);
                    jumlahkirimInput.val(response.jumlah);
                    inventoryinden_id.val(response.idinven);
                    jumlahkirimInput.data('jumlah', response.jumlah); // Simpan jumlah dalam atribut data
                    console.log('Jumlah saved in data attribute:', response.jumlah); 
                },
                error: function() {
                    alert('Gagal mengambil data kategori');
                }
            });
        }
    });

    function clearFile(){
        $('#bukti').val('');
        $('#preview').attr('src', defaultImg);
    }

        
    var dataproduk = @json($data->produkmutasi);
    function populateBulan(supplier_id)
    {
        $.ajax({
            url: `/get-bulan-inden/${supplier_id}`,
            type: 'GET',
            success: function(data) {
                if (Array.isArray(data)) {
                    $('select[id^="bulan_inden_"]').each(function() {
                        $(this).empty();
                        $(this).append('<option value="">Pilih Bulan Inden</option>');
                        var id_row = $(this).attr('id').split('_')[2];
                        var bulanIndenDropdown = $(this);
                        data.forEach(function(bulanInden) {
                            var isSelected = dataproduk[id_row]?.produk?.bulan_inden == bulanInden ? 'selected' : '';
                            var supplier_id = dataproduk[id_row] ? dataproduk[id_row]['produk']['supplier_id'] : $('#supplier').val();
                            bulanIndenDropdown.append('<option value="' + bulanInden + '" '+isSelected+'>' + bulanInden + '</option>');
                            if(isSelected){
                                populateKodeInden(bulanInden, supplier_id, id_row);
                            }
                        });
                    });
                } else {
                    console.error("Diharapkan array tetapi mendapat", data);
                }
            },
            error: function() {
                alert('Gagal mengambil data bulan inden');
            }
        });
    }

    function populateKodeInden(bulanInden, supplier_id, id)
    {
        $.ajax({
                url: `/get-kode-inden/${bulanInden}/${supplier_id}`,
                type: 'GET',
                success: function(data) {
                    if (Array.isArray(data)) {
                        var kodeIndenDropdown = $('#kode_inden_' + id);
                        kodeIndenDropdown.empty();
                        kodeIndenDropdown.append('<option value="">Pilih Kode Inden</option>');
                        data.forEach(function(item) {
                            console.log(item)
                            var isSelected = dataproduk[id]?.produk?.kode_produk_inden == item ? 'selected' : '';
                            kodeIndenDropdown.append('<option value="' + item + '" '+isSelected+'>' + item + '</option>');
                        });
                    } else {
                        console.error("Diharapkan array tetapi mendapat", data);
                    }
                },
                error: function() {
                    alert('Gagal mengambil data kode inden');
                }
            });
    }

    
</script>
@endsection