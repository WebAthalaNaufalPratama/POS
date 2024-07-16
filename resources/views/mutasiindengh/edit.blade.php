
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
            <h3 class="page-title">Edit Mutasi Inden ke Galery/GreenHouse</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{route('mutasiindengh.index')}}">Mutasi</a>
                </li>
                <li class="breadcrumb-item active">
                    Inden Ke {{ $data->lokasi->nama}}
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
                                            <input type="text" class="form-control" id="tgl_kirim" name="tgl_kirim" value="{{ tanggalindo($data->tgl_dikirim) }}" readonly>
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
                                            <label for="supplier">Supplier</label>
                                                <input type="hidden" name="supplier_id" value="{{ $data->supplier_id }}">
                                                <input type="text" class="form-control" id="supplier" name="supplier" value="{{ $data->supplier->nama }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="lokasi">Lokasi</label>
                                            <input type="hidden" name="lokasi_id" value="{{ $data->lokasi_id }}">
                                            <input type="text" class="form-control" id="lokasi" name="lokasi" value="{{ $data->lokasi->nama }}" readonly>

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tgl_terima">Tanggal Diterima</label>
                                            <input type="date" class="form-control" id="tgl_diterima" name="tgl_diterima" 
                                            value="{{ $data->tgl_diterima ?? now()->format('Y-m-d') }}"
                                            min="{{ now()->format('Y-m-d') }}" 
                                            max="{{ now()->addYear()->format('Y-m-d') }}">
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
                                                <img id="preview" />
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
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="dynamic_field">
                                                @foreach ($barangmutasi as $index => $item)
                                                <tr>
                                                    <td>
                                                        <input type="hidden" class="form-control" name="id[]" id="id_{{ $index }}" value="{{ $item->id }}" readonly>
                                                        <input type="text" class="form-control" name="bulan_inden[]" id="bulan_inden_{{ $index }}" value="{{ $item->produk->bulan_inden }}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" name="kode_inden[]" id="kode_inden_{{ $index }}" value="{{ $item->produk->kode_produk_inden }}" readonly>
                                                    </td>
                                                    <td>
                                                    <input type="text" class="form-control" name="kategori[]" id="kategori_{{ $index }}" value="{{ $item->produk->produk->nama }}" readonly>
                                                    <input type="hidden" class="form-control" name="kategori1[]" id="kategori1_{{ $index }}" value="{{ $item->produk->produk->nama}}" readonly>
                                                    </td>
                                                    <td><input type="number" name="qtykrm[]" id="qtykrm_{{ $index }}" class="form-control" onchange="calculateTotal({{ $index }})" value="{{ $item->jml_dikirim }}" readonly></td>
                                                    <td><input type="number" name="qtytrm[]" id="qtytrm_{{ $index }}" class="form-control" value="{{ $item->jml_diterima ?? '' }}" oninput="calculateTotal({{ $index }})"></td>
                                                    <td>
                                                        <select id="kondisi_{{ $index }}" name="kondisi[]" class="form-control">
                                                            <option value="">Pilih Kondisi</option>
                                                            @foreach ($kondisis as $kondisi)
                                                                <option value="{{ $kondisi->id }}" {{ $item->kondisi_id == $kondisi->id ? 'selected' : '' }}>{{ $kondisi->nama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" name="rawat2[]" id="rawat2_{{ $index }}" value="{{ $item->biaya_rawat }}" class="form-control-banyak" oninput="calculateTotal({{ $index }})" readonly>
                                                            <input type="hidden" name="rawat[]" id="rawat_{{ $index }}" value="{{ $item->biaya_rawat }}" class="form-control" oninput="calculateTotal({{ $index }})" readonly>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" name="jumlah_display[]" id="jumlah_{{ $index }}" value="{{ $item->totalharga }}" class="form-control-banyak" readonly>
                                                            <input type="hidden" name="jumlah[]" id="jumlahint_{{ $index }}" value="{{ $item->totalharga }}" class="form-control">
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
                                                        <th>Aksi</th>
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
                                                            
                                                            <input type="text" id="sub_total" name="sub_total_dis" class="form-control" onchange="calculateTotal(0)" readonly value="{{ $data->subtotal ?? 0 }}">
                                                            <input type="hidden" id="sub_total_int" name="sub_total" class="form-control" onchange="calculateTotal(0)" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Biaya Perawatan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="biaya_rwt2" name="biaya_rwt_dis"  class="form-control" oninput="calculateTotal(0)"  value="{{ $data->biaya_perawatan ?? 0 }}" readonly>
                                                            <input type="hidden" id="biaya_rwt" name="biaya_rwt" class="form-control" oninput="calculateTotal(0)"  value="{{ $data->biaya_perawatan ?? 0 }}" readonly>
                                                        </div>
                                                    </h5>

                                                </li>
                                                
                                                <li>
                                                    <h4>Biaya Pengiriman</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="biaya_ongkir2" name="biaya_ongkir_dis"  class="form-control" value="{{ $data->biaya_pengiriman ?? 0 }}" oninput="calculateTotal(0)" readonly>
                                                            <input type="hidden" id="biaya_ongkir" name="biaya_ongkir" class="form-control" value="{{ $data->biaya_pengiriman ?? 0 }}" oninput="calculateTotal(0)" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li class="total">
                                                    <h4>Total Tagihan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="total_tagihan" name="total_tagihan_dis" class="form-control" value="{{ $data->total_biaya ?? 0 }}" readonly>
                                                            <input type="hidden" id="total_tagihan_int" name="total_tagihan" class="form-control" value="{{ $data->total_biaya ?? 0 }}" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Sisa Tagihan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" value="{{ $data->sisa_bayar ?? 0 }}" id="sisa_bayar" name="sisa_bayar" class="form-control" readonly>
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
                            @if(Auth::user()->hasRole('Auditor'))
                            <div class="col-md-8 border rounded pt-3 me-1 mt-2"> 
                            @else
                            <div class="col-md-6 border rounded pt-3 me-1 mt-2"> 
                            @endif
                             
                                        <table class="table table-responsive border rounded">
                                            <thead>
                                                <tr>
                                                    <th>Dibuat</th>                                              
                                                    <th>Diterima</th>    
                                                    @if(Auth::user()->hasRole('Finance'))                                          
                                                    <th>Dibukukan</th>
                                                    @endif
                                                    @if(Auth::user()->hasRole('Auditor'))
                                                    <th>Diperiksa</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td id="pembuat">
                                                        <input type="hidden" name="pembuat" value="{{ Auth::user()->id ?? '' }}">
                                                        <input type="text" class="form-control" value="{{ $pembuat ?? '' }} ({{ $jabatan ?? '' }})" readonly>
                                                    </td>
                                                    <td id="penerima_id">
                                                        <input type="hidden" name="penerima" value="{{ Auth::user()->id ?? '' }}">

                                                        {{-- <input type="hidden" name=penerima_id" value="{{ Auth::user()->id ?? '' }}"> --}}
                                                        <input type="text" class="form-control" value="{{ $penerima ?? Auth::user()->karyawans->nama }} ({{ $jabatan_penerima ?? Auth::user()->karyawans->jabatan }})" readonly>
                                                    </td>
                                                    @if(Auth::user()->hasRole('Finance'))
                                                    <td id="pembuku">
                                                        <input type="hidden" name="pembuku" value="{{ Auth::user()->id ?? '' }}">
                                                        <input type="text" class="form-control" value="Nama (Finance)" readonly>
                                                    </td>
                                                    @endif
                                                    @if(Auth::user()->hasRole('Auditor'))
                                                    <td id="pemeriksa">
                                                        <input type="hidden" name="pemeriksa" value="{{ $pembuku ?? Auth::user()->id }}">
                                                        <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }}" readonly>
                                                    </td>
                                                    @endif
                                                </tr>
                                                <tr>
                                                    <td id="status_dibuat">
                                                        <select id="status_dibuat" name="status_dibuat" class="form-control" readonly>
                                                            <option selected>Pilih Status</option>
                                                            <option value="TUNDA" {{ $data->status_dibuat == 'TUNDA' ? 'selected' : '' }}>TUNDA</option>
                                                            <option value="DIKONFIRMASI" {{ $data->status_dibuat == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                            <option value="BATAL" {{ $data->status_dibuat == 'BATAL' ? 'selected' : '' }}>BATAL</option>
                                                        </select>
                                                    </td>
                                                    <td id="status_diterima">
                                                        <select id="status_diterima" name="status_diterima" class="form-control" {{ Auth::user()->hasRole('AdminGallery') ? 'required' : 'readonly' }}>
                                                            <option selected>Pilih Status</option>
                                                            {{-- <option value="TUNDA" {{ $data->status_diterima == 'TUNDA' ? 'selected' : '' }}>TUNDA</option> --}}
                                                            <option value="DIKONFIRMASI" {{ $data->status_diterima == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                            {{-- <option value="BATAL" {{ $data->status_diterima == 'BATAL' ? 'selected' : '' }}>BATAL</option> --}}
                                                        </select>
                                                    </td>
                                                    @if(Auth::user()->hasRole('Finance'))
                                                    <td id="status_dibuku">
                                                        <select id="status_dibukukan" name="status_dibukukan" class="form-control" required>
                                                            <option selected>Pilih Status</option>
                                                            <option value="DIKONFIRMASI" {{ $data->status_dibukukan == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                        </select>
                                                    </td>
                                                    @endif
                                                    @if(Auth::user()->hasRole('Auditor'))
                                                    <td id="status_dibuku">
                                                        <select id="status_diperiksa" name="status_diperiksa" class="form-control" required>
                                                            <option selected>Pilih Status</option>
                                                            <option value="DIKONFIRMASI" {{ $data->status_diperiksa == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                        </select>
                                                    </td>
                                                    @endif
                                                </tr>
                                                <tr>
                                                    <td id="tgl_dibuat">
                                                        <input type="hidden" name="tgl_dibuat" value="{{ $data->tgl_dibuat }}">
                                                        <input type="text" class="form-control" id="tgl_dibuat" value="{{ formatTanggal($data->tgl_dibuat) }}" readonly>
                                                    </td>
                                                    <td id="tgl_diterima">
                                                        <input type="date" class="form-control" id="tgl_diterima" name="tgl_diterima_ttd" value="{{ $data->tgl_diterima ?? date('Y-m-d') }}" {{ Auth::user()->hasRole('AdminGallery') ? 'required' : 'readonly' }}>
                                                    </td>
                                                    @if(Auth::user()->hasRole('Finance'))
                                                    <td id="tgl_dibuku">
                                                        <input type="date" class="form-control" id="tgl_dibukukan" name="tgl_dibukukan" value="{{ $data->tgl_dibukukan ?? date('Y-m-d') }}" readonly>
                                                    </td>
                                                    @endif
                                                    @if(Auth::user()->hasRole('Auditor'))
                                                    <td id="tgl_diperiksa">
                                                        <input type="date" class="form-control" id="tgl_diperiksa" name="tgl_diperiksa" value="{{ $data->tgl_diperiksa ?? date('Y-m-d') }}" required>
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
              <input type="file" class="form-control" id="bukti" name="bukti" accept="image/*">
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
    var qtyTerimaElem = document.getElementById('qtykrm_' + index);
    var rawatElem = document.getElementById('rawat2_' + index);

    if (qtyTerimaElem && rawatElem) {
        var qtyTerima = parseFloat(qtyTerimaElem.value) || 0;
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



    $(document).ready(function() {
        $(document).on('input change', 'input[name="qtytrm[]"]', function () {
            let index = $(this).attr('id').split('_')[1];
            validateQty(index);
        });
        
        $('.select2').select2();

        bindSelectEvents(0);

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
    var i = 0;
    var bulanIndenData = [];

    $('#add').click(function() {
        i++;

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
                <td><input type="text" class="form-control" name="kategori[]" id="kategori_${i}" readonly></td>
                <td><input type="number" name="qtykrm[]" id="qtykrm_${i}" class="form-control" onchange="calculateTotal(${i})"></td>
                <td><input type="number" name="qtytrm[]" id="qtytrm_${i}" class="form-control" onchange="calculateTotal(${i})" readonly></td>
                <td>
                    <select id="kondisi_${i}" name="kondisi[]" class="form-control" readonly>
                        <option value="">Pilih Kondisi</option>
                        @foreach ($kondisis as $kondisi)
                         <option value="{{ $kondisi->id }}" readonly>{{ $kondisi->nama }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">Rp. </span>
                        <input type="text" name="rawat2[]" id="rawat2_${i}" class="form-control" oninput="calculateTotal(${i})" readonly>
                        <input type="hidden" name="rawat[]" id="rawat_${i}" class="form-control" readonly>
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

        // var newRow = '<tr id="row'+i+'">'+
        //         '<td><select class="form-control" id="bulan_inden_'+i+'" name="bulan_inden[]">'+
        //                 '<option value="">Pilih Bulan Inden</option>'+
        //            '</select>'+
        //         '</td>'+
        //         '<td><select class="form-control" id="kode_inden_'+i+'" name="kode_inden[]">'+
        //                 '<option value="">Pilih Kode Inden</option>'+
        //             '</select>'+
        //         '</td>'+
        //         '<td><input type="text" class="form-control" name="kategori[]" id="kategori_'+i+'" readonly></td>
        //         '<td><input type="number" name="qtykrm[]" id="qtykrm_'+i+'" class="form-control" onchange="calculateTotal('+i+')"></td>'+
        //         '<td><input type="number" name="qtytrm[]" id="qtytrm_'+i+'" class="form-control" onchange="calculateTotal('+i+')"></td>'+
        //         '<td><select id="kondisi_'+i+'" name="kondisi[]" class="form-control">'+
        //             '<option value="" readonly>Pilih Kondisi</option>'+
        //                         '@foreach ($kondisis as $kondisi)'+
        //                             '<option value="{{ $kondisi->id }}" readonly>{{ $kondisi->nama }}</option>'+
        //                         '@endforeach'+
        //             '</select>'+
        //         '</td>'+
        //         '<td><div class="input-group">'+
        //                 '<span class="input-group-text">Rp. </span>'+
        //                 '<input type="text" name="rawat2[]" id="rawat2_'+i+'" class="form-control" oninput="calculateTotal('+i+')" required>'+
        //                 '<input type="hidden" name="rawat[]" id="rawat_'+i+'" class="form-control" required>'+
        //         '</div>'+
        //         '</td>'+
        //         '<td><div class="input-group">'+
        //                 '<span class="input-group-text">Rp. </span>'+
        //                 '<input type="text" name="jumlah_display[]" id="jumlah_'+i+'" class="form-control" readonly>'+
        //                 '<input type="hidden" name="jumlah[]" id="jumlahint_'+i+'" class="form-control" readonly>'+
        //             '</div>'+
        //         '</td>'+
        //         '<td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td>'+
        //     '</tr>';

        $('#dynamic_field').append(newRow);
        bindSelectEvents(i);

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


        function bindSelectEvents(index) {
            $('#bulan_inden_' + index).change(function() {
                const supplierId = $('#supplier').val();
                const bulanInden = $(this).val();
                const kodeIndenDropdown = $('#kode_inden_' + index);

                kodeIndenDropdown.empty();
                kodeIndenDropdown.append('<option value="">Pilih Kode Inden</option>');

                if (bulanInden) {
                    $.ajax({
                        url: `/get-kode-inden/${bulanInden}/${supplierId}`,
                        type: 'GET',
                        success: function(data) {
                            data.forEach(function(kodeInden) {
                                kodeIndenDropdown.append('<option value="' + kodeInden + '">' + kodeInden + '</option>');
                            });
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
                const kategoriInput = $('#kategori_' + index); 

                if (kodeInden) {
                    $.ajax({
                        url: `/get-kategori-inden/${kodeInden}/${bulanInden}/${supplierId}`,
                        type: 'GET',
                        success: function(kategori) {
                            kategoriInput.val(kategori);
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

            // Kosongkan opsi bulan inden pada setiap dropdown bulan_inden
            $('select[id^="bulan_inden_"]').each(function() {
                $(this).empty();
                $(this).append('<option value="">Pilih Bulan Inden</option>');
            });

            if (supplierId) {
                // Ambil data bulan inden dari server
                $.ajax({
                    url: `/get-bulan-inden/${supplierId}`,
                    type: 'GET',
                    success: function(data) {
                        bulanIndenData = data; // Simpan data bulan inden
                        $('select[id^="bulan_inden_"]').each(function() {
                            var bulanIndenDropdown = $(this);
                            data.forEach(function(bulanInden) {
                                bulanIndenDropdown.append('<option value="' + bulanInden + '">' + bulanInden + '</option>');
                            });
                        });
                    },
                    error: function() {
                        alert('Gagal mengambil data bulan inden');
                    }
                });
            }
        });

       
    });
   



    function clearFile(){
        $('#bukti').val('');
        $('#preview').attr('src', defaultImg);
    }
    function validateQty(index) {
        let qtyKrm = parseFloat($(`#qtykrm_${index}`).val());
        let qtyTrm = parseFloat($(`#qtytrm_${index}`).val());

        if (qtyTrm < 0) {
            $(`#qtytrm_${index}`).val(0);
        } else if (qtyTrm > qtyKrm) {
            $(`#qtytrm_${index}`).val(qtyKrm);
        }
    }
        
</script>
@endsection


{{-- <script>


    $(document).ready(function() {
        $('.select2').select2();

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

           
        var i = 0;
        var bulanIndenData = [];

        $('#add').click(function() {
            i++;
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
                    <td><input type="text" class="form-control" name="kategori[]" id="kategori_${i}" readonly></td>
                    <td><input type="number" name="qtykrm[]" id="qtykrm_${i}" oninput="multiply($(this))" class="form-control" onchange="calculateTotal(${i})"></td>
                    <td><input type="number" name="qtytrm[]" id="qtytrm_${i}" oninput="multiply($(this))" class="form-control" onchange="calculateTotal(${i})"></td>
                    <td>
                        <select id="kondisi_${i}" name="kondisi[]" class="form-control" onchange="showInputType(${i})">
                            <option value="" readonly>Pilih Kondisi</option>
                        </select>
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-text">Rp. </span> 
                            <input type="text" name="rawat2[]" id="rawat2_${i}" class="form-control-banyak" oninput="calculateTotal(${i})" value="" required>
                            <input type="hidden" name="rawat[]" id="rawat_${i}" class="form-control" oninput="calculateTotal(${i})" value="" required>
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-text">Rp. </span> 
                            <input type="text" name="jumlah_display[]" id="jumlah_${i}" class="form-control-banyak" value="" readonly>
                            <input type="hidden" name="jumlah[]" id="jumlahint_${i}" class="form-control" value="" readonly>
                        </div>
                    </td>
                    <td><button type="button" name="remove" id="${i}" class="btn btn-danger btn_remove">X</button></td>
                </tr>
            `;
            $('#dynamic_field').append(newRow);
            bindSelectEvents(i);
        });

        $(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id"); 
            $('#row' + button_id + '').remove();
        });

        function bindSelectEvents(index) {
            $('#bulan_inden_' + index).change(function() {
                const supplierId = $('#supplier').val();
                const bulanInden = $(this).val();
                const kodeIndenDropdown = $('#kode_inden_' + index);

                kodeIndenDropdown.empty();
                kodeIndenDropdown.append('<option value="">Pilih Kode Inden</option>');

                if (bulanInden) {
                    $.ajax({
                        url: `/get-kode-inden/${bulanInden}/${supplierId}`,
                        type: 'GET',
                        success: function(data) {
                            data.forEach(function(kodeInden) {
                                kodeIndenDropdown.append('<option value="' + kodeInden + '">' + kodeInden + '</option>');
                            });
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
                const kategoriInput = $('#kategori_' + index); 

                if (kodeInden) {
                    $.ajax({
                        url: `/get-kategori-inden/${kodeInden}/${bulanInden}/${supplierId}`,
                        type: 'GET',
                        success: function(kategori) {
                            kategoriInput.val(kategori);
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

            // Kosongkan opsi bulan inden pada setiap dropdown bulan_inden
            $('select[id^="bulan_inden_"]').each(function() {
                $(this).empty();
                $(this).append('<option value="">Pilih Bulan Inden</option>');
            });

            if (supplierId) {
                // Ambil data bulan inden dari server
                $.ajax({
                    url: `/get-bulan-inden/${supplierId}`,
                    type: 'GET',
                    success: function(data) {
                        bulanIndenData = data; // Simpan data bulan inden
                        $('select[id^="bulan_inden_"]').each(function() {
                            var bulanIndenDropdown = $(this);
                            data.forEach(function(bulanInden) {
                                bulanIndenDropdown.append('<option value="' + bulanInden + '">' + bulanInden + '</option>');
                            });
                        });
                    },
                    error: function() {
                        alert('Gagal mengambil data bulan inden');
                    }
                });
            }
        });

        bindSelectEvents(0); // Initial binding for the first row
    });
   



        function clearFile(){
            $('#bukti').val('');
            $('#preview').attr('src', defaultImg);
        }

        
</script> --}}