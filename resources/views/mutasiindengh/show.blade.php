
@extends('layouts.app-von')

@section('content')
<style>
   
    .form-control {
        min-width: 200px; /* Adjust as necessary */
    }
    .form-control-banyak{
        min-width: 200px; /* Adjust as necessary */
    }
</style>
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Edit Mutasi Inden ke GreenHouse</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{route('mutasiindengh.index')}}">Mutasi</a>
                </li>
                <li class="breadcrumb-item active">
                    Inden Ke GreenHouse
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="card">
       
        <div class="card-body">
            {{-- <form action="{{ route('mutasiindengh.update') }}" method="POST" enctype="multipart/form-data"> --}}
                @csrf
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
                                            <input type="text" class="form-control" id="tgl_kirim" name="tgl_kirim" value="{{ formatTanggal($data->tgl_dikirim) }}" readonly>
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
                                            <input type="date" class="form-control" id="tgl_diterima" name="tgl_diterima" value="{{ formatTanggal($data->tgl_diterima) }}" readonly>
                                         </div>
                                        <div class="form-group">
                                            <label for="tgl_terima">Bukti</label>

                                                <img id="previewdo" src="{{ $data->bukti ? '/storage/' . $data->bukti : '' }}" alt="your image" />                                            
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
                                                @foreach ($barangmutasi as $item)        
                                                <tr>   
                                                    <td>
                                                        <input type="text" class="form-control" name="bulan_inden[]" id="bulan_inden_0" value="{{ $item->produk->bulan_inden }}" readonly>

                                                        {{-- <select class="form-control" id="bulan_inden_0" name="bulan_inden[]">
                                                            <option value="">Pilih Bulan Inden</option>
                                                        </select> --}}
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" name="kode_inden[]" id="kode_inden_0" value="{{ $item->produk->kode_produk_inden }}" readonly>

                                                        {{-- <select class="form-control" id="kode_inden_0" name="kode_inden[]">
                                                            <option value="">Pilih Kode Inden</option>
                                                        </select> --}}
                                                    </td>
                                                    <td><input type="text" class="form-control" name="kategori[]" id="kategori_0" value="{{ $item->produk->produk->nama }}" readonly></td>
                                                    <td><input type="number" name="qtykrm[]" id="qtykrm_0" class="form-control" onchange="calculateTotal(0)" value="{{ $item->jml_dikirim }}" readonly></td>
                                                    <td><input type="number" name="qtytrm[]" id="qtytrm_0" class="form-control" onchange="calculateTotal(0)" value="{{ $item->jml_diterima }}" readonly></td>
                                                    <td>
                                                        <input type="text" name="kondisi[]" id="kondisi_0" class="form-control" onchange="calculateTotal(0)" value="{{ $item->kondisi->nama }}" readonly>

                                                        {{-- <select id="kondisi_0" name="kondisi[]" class="form-control" onchange="showInputType(0)">
                                                            <option value="">Pilih Kondisi</option>
                                                            @foreach ($kondisis as $kondisi)
                                                                <option value="{{ $kondisi->id }}">{{ $kondisi->nama }}</option>
                                                            @endforeach
                                                        </select> --}}
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" name="rawat2[]" id="rawat2_0" class="form-control-banyak" oninput="calculateTotal(0)" value="{{ $item->biaya_rawat }}" readonly>
                                                            <input type="hidden" name="rawat[]" id="rawat_0" class="form-control" oninput="calculateTotal(0)">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" name="jumlah_display[]" id="jumlah_0" class="form-control-banyak" oninput="calculateTotal(0)" value="{{ $item->totalharga }}" readonly>
                                                            <input type="hidden" name="jumlah[]" id="jumlahint_0" class="form-control" oninput="calculateTotal(0)">
                                                        </div>
                                                    </td>
                                                    {{-- <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td> --}}
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
                                                            
                                                            <input type="text" id="sub_total" name="sub_total_dis" class="form-control" onchange="calculateTotal(0)" value="{{ $data->subtotal }}" readonly>
                                                            <input type="hidden" id="sub_total_int" name="sub_total" class="form-control" onchange="calculateTotal(0)"   readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Biaya Perawatan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="biaya_rwt2" name="biaya_rwt_dis"  class="form-control" oninput="calculateTotal(0)" value="{{ $data->biaya_perawatan }}" readonly>
                                                            <input type="hidden" id="biaya_rwt" name="biaya_rwt" class="form-control" oninput="calculateTotal(0)">
                                                        </div>
                                                    </h5>

                                                </li>
                                                
                                                <li>
                                                    <h4>Biaya Pengiriman</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="biaya_ongkir2" name="biaya_ongkir_dis"  class="form-control" oninput="calculateTotal(0)" value="{{ $data->biaya_pengiriman }}" readonly>
                                                            <input type="hidden" id="biaya_ongkir" name="biaya_ongkir" class="form-control" oninput="calculateTotal(0)">
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li class="total">
                                                    <h4>Total Tagihan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="total_tagihan" name="total_tagihan_dis" class="form-control" value="{{ $data->total_biaya }}" readonly>
                                                            <input type="hidden" id="total_tagihan_int" name="total_tagihan" class="form-control" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Sisa Tagihan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="sisa_bayar" name="sisa_bayar" class="form-control" value="{{ $data->sisa_bayar }}" readonly>
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
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2"> 
                             
                                        <table class="table table-responsive border rounded">
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
                                                        <input type="hidden" name="pembuat" value="{{ Auth::user()->id ?? '' }}">
                                                        <input type="text" class="form-control" value="{{ $pembuat ?? '' }} ({{ $jabatanbuat ?? '' }})" readonly>
                                                    </td>
                                                    <td id=penerima">
                                                        <input type="hidden" name=penerima" value="{{ Auth::user()->id ?? '' }}">
                                                        <input type="text" class="form-control" value="{{ $penerima ?? '' }} ({{ $jabatanterima ?? '' }})" readonly>
                                                    </td>
                                                    <td id="pembuku">
                                                        <input type="hidden" name="pembuku" value="{{ Auth::user()->id ?? '' }}">
                                                        <input type="text" class="form-control" value="{{ $pembuku ?? '' }} ({{ $jabatanbuku ?? '' }})" readonly>
                                                    </td>
                                                    <td id="pemeriksa">
                                                        <input type="hidden" name="pemeriksa" value="{{ Auth::user()->id ?? '' }}">
                                                        <input type="text" class="form-control" value="{{ $pemeriksa ?? '' }} ({{ $jabatanperiksa ?? '' }})" readonly>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td id="status_dibuat">
                                                        <select id="status_dibuat" name="status_dibuat" class="form-control" readonly>
                                                            <option selected>Pilih Status</option>
                                                            <option value="draft" {{ $data->status_dibuat == 'draft' ? 'selected' : '' }} disabled>Draft</option>
                                                            <option value="publish" {{ $data->status_dibuat == 'publish' ? 'selected' : '' }} disabled>Publish</option>
                                                        </select>
                                                    </td>
                                                    <td id="status_diterima">
                                                        <select id="status_diterima" name="status_diterima" class="form-control" readonly>
                                                            <option selected>Pilih Status</option>
                                                            <option value="pending" {{ $data->status_diterima  == 'pending' ? 'selected' : '' }} disabled>Pending</option>
                                                            <option value="acc" {{  $data->status_diterima == 'acc' ? 'selected' : '' }} disabled>Accept</option>
                                                        </select>
                                                    </td>
                                                    <td id="status_dibuku">
                                                        <select id="status_dibukukan" name="status_dibuku" class="form-control" readonly>
                                                            <option selected>Pilih Status</option>
                                                            <option value="pending" {{  $data->status_dibukukan == 'pending' ? 'selected' : '' }} disabled>Pending</option>
                                                            <option value="acc" {{  $data->status_dibukukan == 'acc' ? 'selected' : '' }} disabled>Accept</option>
                                                        </select>
                                                    </td>
                                                    <td id="status_diperiksa">
                                                        <select id="status_diperiksa" name="status_diperiksa" class="form-control" readonly>
                                                            <option disabled selected>Pilih Status</option>
                                                            <option value="pending" {{  $data->status_diperiksa  == 'pending' ? 'selected' : '' }} disabled>Pending</option>
                                                            <option value="acc" {{  $data->status_diperiksa  == 'acc' ? 'selected' : '' }} disabled>Accept</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td id="tgl_dibuat">
                                                        <input type="text" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{ formatTanggal($data->tgl_dibuat) }}"readonly >
                                                    </td>
                                                    <td id="tgl_diterima">
                                                        <input type="date" class="form-control" id="tgl_diterima" name="tgl_diterima" value="{{ formatTanggal($data->tgl_diterima_ttd) }}" readonly>
                                                    </td>
                                                    <td id="tgl_dibuku">
                                                        <input type="date" class="form-control" id="tgl_dibukukan" name="tgl_dibukukan" value="{{ formatTanggal($data->tgl_dibukukan) }}"  readonly>
                                                    </td>
                                                    <td id="tgl_diperiksa">
                                                        <input type="date" class="form-control" id="tgl_diperiksa" name="tgl_diperiksa" value="{{ formatTanggal($data->tgl_diperiksa) }}"  readonly>
                                                    </td>
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
    var qtyTerimaElem = document.getElementById('qtytrm_' + index);
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


    $(document).ready(function() {

        
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
                         <option value="{{ $kondisi->id }}" disabled>{{ $kondisi->nama }}</option>
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
        //             '<option value="" disabled>Pilih Kondisi</option>'+
        //                         '@foreach ($kondisis as $kondisi)'+
        //                             '<option value="{{ $kondisi->id }}" disabled>{{ $kondisi->nama }}</option>'+
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
                            <option value="" disabled>Pilih Kondisi</option>
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