@extends('layouts.app-von')

@section('content')
<div id="form" class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Data Retur Pembelian</h5>
            </div>
            <div class="card-body">
                {{-- <form action="{{ route('returbeli.store') }}" method="POST" enctype="multipart/form-data" id="addForm"> --}}
                <div class="row">
                    <div class="col-sm">
                            @csrf
                            <div class="row justify-content-around">
                                <div class="col-md-6 border rounded pt-3">
                                    <h5 class="card-title">Informasi Supplier</h5>
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Supplier</label>
                                            <select id="supplier_id" name="supplier_id" class="form-control" required readonly>
                                                <option value="{{ $data->invoice->pembelian->supplier_id }}">{{ $data->invoice->pembelian->supplier->nama }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Lokasi</label>
                                            <select id="lokasi_id" name="lokasi_id" class="form-control" required readonly>
                                                <option value="{{ $data->invoice->pembelian->lokasi_id }}">{{ $data->invoice->pembelian->lokasi->nama }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Catatan</label>
                                            <textarea type="text" id="catatan" name="catatan" class="form-control" readonly>{{ $data->catatan }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 border rounded pt-3">
                                    <h5 class="card-title">Detail Retur</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Tanggal PO</label>
                                                <input type="text" id="tanggal_po" name="tanggal_po" value="{{ tanggalindo($data->invoice->pembelian->tgl_dibuat) }}" class="form-control" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Invoice</label>
                                                <input type="text" id="tanggal_invoice" name="tanggal_invoice" value="{{ tanggalindo($data->invoice->tgl_inv) }}" class="form-control" required readonly>
                                            </div>
                                            <input type="hidden" name="invoicepo_id" value="{{ $data->invoice->id }}">
                                            <div class="form-group">
                                                <label>Tanggal Retur</label>
                                                <input type="text" id="tgl_retur" name="tgl_retur" value="{{ tanggalindo($data->tgl_retur) }}" class="form-control" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Komplain</label>
                                                <select id="komplain" name="komplain" class="form-control" required disabled>
                                                    <option value="">Pilih Komplain</option>
                                                   
                                                    <option value="Refund" {{ $data->komplain == 'Refund' ? 'selected' : '' }}>Refund</option>
                                                    <option value="Diskon" {{ $data->komplain == 'Diskon' ? 'selected' : '' }}>Diskon</option>
                                                    <option value="Retur" {{ $data->komplain == 'Retur' ? 'selected' : '' }}>Retur</option>
                                                
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>No PO</label>
                                                <input type="text" id="no_po" name="no_po" value="{{ $data->invoice->pembelian->no_po }}" class="form-control" required readonly>
                                            </div>
                                            <!-- <div class="form-group">
                                                <label>No PO Retur</label>
                                                <input type="text" id="no_po_retur" name="no_po_retur" class="form-control" required readonly>
                                            </div> -->
                                            <div class="form-group">
                                                <label>No Invoice</label>
                                                <input type="text" id="no_invoice" name="no_invoice" value="{{ $data->invoice->no_inv }}" class="form-control" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>No Retur</label>
                                                <input type="text" id="no_retur" name="no_retur" value="{{ $data->no_retur }}" value="" class="form-control" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>File</label>
                                                {{-- <div class="input-group">
                                                    <input type="file" id="file" name="file" value="" class="form-control" accept=".pdf,image/*">
                                                </div> --}}
                                                <img id="preview" src="{{ $data->foto ? '/storage/' . $data->foto : '' }}" alt="your image" />
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
                                        <th>No</th>
                                        {{-- <th>Kode Produk</th> --}}
                                        <th>Nama Produk</th>
                                        <th>Alasan</th>
                                        <th>Jumlah</th>
                                        <th id="thDiskon">Diskon</th>
                                        <th>Harga satuan</th>
                                        <th>Harga Total</th>
                                        {{-- <th></th> --}}
                                    </tr>
                                </thead>
                                <tbody id="dynamic_field">
                                    @foreach ($data->produkretur as $item)
                                        <tr>
                                            <td>1</td>
                                            <input type="hidden" name="kode_produk[]" id="kode_produk_0" class="form-control" required readonly>
                                            <td style="width: 20%">
                                                <select id="produk_0" name="nama_produk[]" class="form-control" required disabled>
                                                    <option value="">Pilih Produk</option>
                                                    @foreach ($data->produkretur as $produk)
                                                        <option value="{{ $produk->id }}"{{ $produk->id == $item->id ? 'selected' : '' }}  data-jumlah="{{ $produk->jml_diterima }}" data-harga="{{ $produk->harga }}" data-diskon="{{ $produk->diskon }}" data-harga_total="{{ $produk->totalharga }}">{{ $produk->produkbeli->produk->nama }} ({{ $produk->produkbeli->kondisi->nama }})</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><textarea name="alasan[]" id="alasan_0" class="form-control" cols="30" readonly>{{ $item->alasan }}</textarea></td>
                                            <td><input type="number" name="jumlah[]" id="jumlah_0" class="form-control jumlah_diterima" required value="{{ $item->jumlah }}" readonly></td>
                                            <td id="tdDiskon_0"><input type="text" name="diskon[]" id="diskon_0" class="form-control" required value="{{ formatRupiah($item->diskon) }}" readonly></td>
                                            <td><input type="text" name="harga_satuan[]" id="harga_satuan_0" class="form-control" required readonly value="{{ formatRupiah($item->harga) }}"></td>
                                            <td><input type="text" name="harga_total[]" id="harga_total_0" class="form-control" required readonly value="{{ formatRupiah($item->totharga) }}"></td>
                                            {{-- <td><button type="button" name="add" id="add" class="btn btn-success">+</button></td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm">
                        @if ($data->komplain == 'Refund')
                        <div class="row justify-content-around">
                            <div class="col-lg-8 col-md-8 col-sm-6 col-6 border rounded mt-3 pt-3">
                                <div class="page-btn">
                                    <center><h5>Riwayat uang masuk (Refund) </h5></center><br>
                                    <a href="" data-toggle="modal" data-target="#myModalbayar" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Pembayaran</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table datanew">
                                        <thead>
                                            <tr>
                                                <th>No Bayar</th>
                                                <th>Tanggal</th>
                                                <th>Metode</th>
                                                <th>Rekening</th>
                                                <th>Nominal</th>
                                                <th>Bukti</th>
                                                <th>Status</th>
                                                {{-- <th>Aksi</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($databayars as $bayar)
                                            <tr>
                                                <td>{{ $bayar->no_invoice_bayar ?? "" }}</td>
                                                <td>{{ tanggalindo($bayar->tanggal_bayar) ?? ""}}</td>
                                                <td>{{ $bayar->cara_bayar ?? "" }}</td>
                                                <td>{{ $bayar->rekening->bank ?? '-'}}</td>
                                                <td>{{ formatRupiah($bayar->nominal) ?? ""}}</td>
                                                <td>
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#buktiModal{{ $bayar->id }}">
                                                    Lihat Bukti
                                                </button>
                                        
                                                <!-- Modal -->
                                                <div class="modal fade" id="buktiModal{{ $bayar->id }}" tabindex="-1" role="dialog" aria-labelledby="buktiModalLabel{{ $bayar->id }}" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="buktiModalLabel{{ $bayar->id }}">Bukti Pembayaran</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="{{ asset('storage/'.$bayar->bukti) }}" class="img-fluid" alt="Bukti Pembayaran">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                    
                                            </td>
                                                <td>{{ $bayar->status_bayar}}</td>
                                               
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="total-order">
                                    <ul>
                                        <li>
                                            <h4>Sub Total</h4>
                                            <h5>
                                                    <input type="text" id="sub_total" name="sub_total" class="form-control" value="{{ formatRupiah($data->subtotal) }}" readonly>
                                            </h5>
                                        </li>
                                        <li>
                                            <h4>Biaya Pengiriman</h4>
                                            <h5>
                                                    <input type="text" id="biaya_ongkir" name="biaya_ongkir" class="form-control" value="{{ formatRupiah($data->ongkir ?? 0) }}" readonly required>
                                            </h5>
                                        </li>
                                
                                    </ul>
                                </div>
                            </div>

                            
                        </div>
                        <div class="row justify-content-start">
                            <div class="col-md-6 border rounded pt-3 me-1 mt-2">
                                <table class="table table-responsive border rounded">
                                    <thead>
                                        <tr>
                                            <th>Dibuat</th>                                              
                                            <th>Dibukukan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="pembuat">
                                                <input type="hidden" name="pembuat" value="{{ Auth::user()->id ?? '' }}">
                                                <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" placeholder="{{ Auth::user()->karyawans->nama ?? '' }}" disabled>
                                            </td>
                                            <td id="pembuku">
                                                <input type="hidden" name="pembuku" value="{{ Auth::user()->id ?? '' }}">
                                                <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" placeholder="{{ Auth::user()->karyawans->nama ?? '' }}" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td id="status_dibuat">
                                                <select id="status_dibuat" name="status_dibuat" class="form-control" required disabled>
                                                    <option value="">Pilih Status</option>
                                                    <option value="draft" {{$data->status_dibuat == 'draft' ? 'selected' : '' }}>Draft</option>
                                                    <option value="publish" {{ ($data->status_dibuat == 'publish') || ($data->status_dibuat == null)  ? 'selected' : '' }}>Publish</option>
                                                </select>
                                            </td>
                                            <td id="status_dibuku">
                                                <select id="status_dibukukan" name="status_dibuku" class="form-control" disabled>
                                                    <option value="">Pilih Status</option>
                                                    <option value="pending" {{$data->status_dibukukan == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="acc" {{ ($data->status_dibukukan == 'acc') || ($data->status_dibukukan == null) ? 'selected' : '' }}>Accept</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td id="tgl_dibuat">
                                                <input type="date" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{$data->tgl_dibuat }}" readonly>
                                            </td>
                                            <td id="tgl_dibuku">
                                                <input type="date" class="form-control" id="tgl_dibuku" name="tgl_dibuku" value="{{$data->tgl_dibuku }}" readonly>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>  
                                <br>                                 
                            </div>
                        </div>
                        @else
                        <div class="row justify-content-around">
                            <div class="col-md-8 border rounded pt-3 mt-3">
                                <table class="table table-responsive border rounded">
                                    <thead>
                                        <tr>
                                            <th>Dibuat</th>                                              
                                            <th>Dibukukan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="pembuat">
                                                <input type="hidden" name="pembuat" value="{{ Auth::user()->id ?? '' }}">
                                                <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" placeholder="{{ Auth::user()->karyawans->nama ?? '' }}" disabled>
                                            </td>
                                            <td id="pembuku">
                                                <input type="hidden" name="pembuku" value="{{ Auth::user()->id ?? '' }}">
                                                <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" placeholder="{{ Auth::user()->karyawans->nama ?? '' }}" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td id="status_dibuat">
                                                <select id="status_dibuat" name="status_dibuat" class="form-control" required disabled>
                                                    <option value="">Pilih Status</option>
                                                    <option value="draft" {{$data->status_dibuat == 'draft' ? 'selected' : '' }}>Draft</option>
                                                    <option value="publish" {{ ($data->status_dibuat == 'publish') || ($data->status_dibuat == null)  ? 'selected' : '' }}>Publish</option>
                                                </select>
                                            </td>
                                            <td id="status_dibuku">
                                                <select id="status_dibukukan" name="status_dibuku" class="form-control" disabled>
                                                    <option value="">Pilih Status</option>
                                                    <option value="pending" {{$data->status_dibukukan == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="acc" {{ ($data->status_dibukukan == 'acc') || ($data->status_dibukukan == null) ? 'selected' : '' }}>Accept</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td id="tgl_dibuat">
                                                <input type="date" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{$data->tgl_dibuat }}" readonly>
                                            </td>
                                            <td id="tgl_dibuku">
                                                <input type="date" class="form-control" id="tgl_dibuku" name="tgl_dibuku" value="{{$data->tgl_dibuku }}" readonly>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>  
                                <br>                                 
                            </div>
                            <div class="col-sm">
                                <div class="total-order">
                                    <ul>
                                        <li>
                                            <h4>Sub Total</h4>
                                            <h5>
                                                    <input type="text" id="sub_total" name="sub_total" class="form-control" value="{{ formatRupiah($data->subtotal) }}" readonly>
                                            </h5>
                                        </li>
                                        <li>
                                            <h4>Biaya Pengiriman</h4>
                                            <h5>
                                                    <input type="text" id="biaya_ongkir" name="biaya_ongkir" class="form-control" value="{{ formatRupiah($data->ongkir ?? 0) }}" readonly required>
                                            </h5>
                                        </li>
                                
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="text-end mt-3">
                    {{-- <button class="btn btn-primary" type="submit">Submit</button> --}}
                    <a href="{{ route('returbeli.index') }}" class="btn btn-secondary" type="button">Back</a>
                </div>
                {{-- </form> --}}
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
            <form id="supplierForm" action="{{ route('bayarrefund.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
            <div class="mb-3">
              <label for="nobay" class="form-label">No Bayar</label>
              <input type="hidden" class="form-control" id="type" name="type" value="pembelian">
              <input type="hidden" class="form-control" id="idpo" name="retur_pembelian_id" value="{{ $data->id }}">
              {{-- <input type="hidden" class="form-control" id="invoice_purchase_id" name="invoice_purchase_id" value="{{ $data->invoicepo_id }}"> --}}
              <input type="text" class="form-control" id="nobay" name="no_invoice_bayar" value="{{ $no_byre }}" readonly>
            </div>
            <div class="mb-3">
              <label for="tgl" class="form-label">Tanggal</label>
              <input type="date" class="form-control" id="tgl" name="tanggal_bayar" value="{{ now()->format('Y-m-d') }}">
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
              <div class="input-group">
                <span class="input-group-text">Rp. </span>
                <input type="text" class="form-control"  id="nominal" value="{{ $data->subtotal}}">
              </div>
              <input type="text" class="form-control"  id="nominal2" name="nominal" hidden>
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
@endsection

@section('scripts')
<script>

    document.addEventListener('DOMContentLoaded', function() {
         // Initialize input field with formatted value
         var nominalInput = document.getElementById('nominal');
         var nominalInput2 = document.getElementById('nominal2');
            var initialNominalValue = '{{ $data->sisa }}';
            nominalInput.value = formatRupiah(initialNominalValue);
            nominalInput2.value = unformatRupiah(initialNominalValue);

            

    document.getElementById('nominal').addEventListener('keyup', function(e) {
        var rupiah = this.value.replace(/[^\d]/g, ''); // hanya ambil angka
        this.value = formatRupiah(rupiah);

        // Set nilai ke input hidden
        document.getElementById('nominal2').value = unformatRupiah(this.value);
    });
    });
</script>
@endsection