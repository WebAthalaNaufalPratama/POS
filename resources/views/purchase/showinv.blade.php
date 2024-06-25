<?php
use Carbon\Carbon;

setlocale(LC_TIME, 'id_ID');
Carbon::setLocale('id');
?>

@extends('layouts.app-von')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Detail Invoice</h3>
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
            {{-- <form action="{{ route('invoice.update', $inv_po->id )}} " method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') --}}
                <div class="row">
                    <div class="col-sm">
                        <div class="row justify-content-start">
                            <div class="col-md-12 border rounded pt-3 me-1">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="no_inv">No. Invoice</label>
                                            <input type="text" class="form-control" id="no_inv" name="no_inv"  value="{{ $inv_po->no_inv }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="tg_inv">Tanggal Invoice</label>
                                            <input type="text" class="form-control" id="tgl_inv" name="tgl_inv" value="{{ \Carbon\Carbon::parse($inv_po->tgl_inv)->translatedFormat('d F Y') }}" readonly>
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
                                            <label for="no_po">No. Purchase Order</label>
                                            <input type="text" class="form-control" id="no_po" name="id_po" value="{{ $inv_po->pembelian_id }}" readonly hidden>
                                            <input type="text" class="form-control" id="no_po" name="no_po" value="{{ $inv_po->pembelian->no_po }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="tgl_po">Tanggal PO</label>
                                                <input type="text" class="form-control" id="tgl_po" name="tgl_po" value="{{ \Carbon\Carbon::parse($inv_po->pembelian->created_at)->translatedFormat('d F Y') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="suplier">Supplier</label>
                                            <input type="text" class="form-control" id="suplier" name="suplier" value="{{ $inv_po->pembelian->supplier->nama }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="lokasi">Lokasi</label>
                                                <input type="text" class="form-control" id="lokasi" name="lokasi" value="{{ $inv_po->pembelian->lokasi->nama }}" readonly>
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
                                                    <td><input type="number" name="qtytrm[]" id="qtytrm_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{ ($item->jml_diterima - $item->qty_komplain )}}" readonly></td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" name="harga[]" id="harga_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{formatRupiah2($item->harga) }}" readonly>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" name="diskon[]" id="diskon_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{ formatRupiah2($item->diskon) }}" readonly>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" name="jumlah[]" id="jumlah_{{ $index }}" class="form-control" value="{{ formatRupiah2($item->totalharga) }}" readonly>
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
                        @if ($retur)
                        <div class="row justify-content-around">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2">
                                <div class="form-row row">
                                    <div class="mb-4">
                                        <h5>List Produk {{ $retur->komplain }}</h5> 
                                        <h6>Nomor: 
                                            <span id="returNumber">{{ $retur->no_retur }}</span>
                                            <i class="fa fa-copy" id="copyIcon" style="cursor: pointer; margin-left: 10px;"></i>
                                        </h6>
                                    </br>
                                        @if($retur->komplain == "Retur")
                                        <a href="{{ route('pembelian.create') }}" class="btn btn-primary" target="_blank">Buat Pembelian Baru</a>
                                        @endif

                                        
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
                                                    <th>Total Harga</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="dynamic_field">
                                                @foreach ($produkkomplains as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td hidden><input type="text" name="id[]" id="id{{ $index }}" class="form-control" value="{{ $item->id }}" readonly hidden></td>
                                                    <td><input type="text" name="kode[]" id="kode_{{ $index }}" class="form-control" value="{{ $item->produkbeli->produk->kode }}" readonly></td>
                                                    <td><input type="text" name="nama[]" id="nama_{{ $index }}" class="form-control" value="{{ $item->produkbeli->produk->nama }}" readonly></td>
                                                    <td><input type="number" name="qtytrm[]" id="qtytrm_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{ $item->jumlah }}" readonly></td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" name="harga[]" id="harga_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{formatRupiah2($item->harga) }}" readonly>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" name="diskon[]" id="diskon_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{ formatRupiah2($item->diskon) }}" readonly>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" name="jumlah[]" id="jumlah_{{ $index }}" class="form-control" value="{{ formatRupiah2($item->totharga) }}" readonly>
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
                        @endif
                        
                        <div class="row justify-content-around">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2">
                                <div class="row">
                                    <div class="col-lg-7 col-sm-6 col-6 mt-4 ">
                                        {{-- <div class="page-btn">
                                            <a href="" data-toggle="modal" data-target="#myModalbayar" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Pembayaran</a>
                                        </div> --}}
                                        Riwayat Pembayaran
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
                                                        <td>{{ $bayar->no_invoice_bayar }}</td>
                                                        <td>{{ tanggalindo($bayar->tanggal_bayar) }}</td>
                                                        <td>{{ $bayar->cara_bayar }}</td>
                                                        <td>{{ $bayar->rekening->bank ?? '-'}}</td>
                                                        <td>{{ formatRupiah($bayar->nominal) }}</td>
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
                                                        {{-- <td></td> --}}
                                                       
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title">Riwayat</h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                <table class="table datanew">
                                                    <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Tanggal Perubahan</th>
                                                        <th>Pengubah</th>
                                                        <th>Log</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach ($riwayat as $item)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $item->created_at ?? '-' }}</td>
                                                            <td>{{ $item->causer->name ?? '-' }}</td>
                                                            <td>
                                                                @php
                                                                    $properties = json_decode($item->properties, true);
                                                                    $changes = $item->changes();

                                                                    if (isset($changes['old'])) {
                                                                        $diff = array_keys(array_diff_assoc($changes['attributes'], $changes['old']));
                                                                        foreach ($diff as $key => $value) {
                                                                            echo "$value: <span class='text-danger'>{$changes['old'][$value]}</span> => <span class='text-success'>{$changes['attributes'][$value]}</span><br>";
                                                                        }
                                                                    } else {
                                                                        if ($item->subject_type == 'App\Models\Invoicepo') {
                                                                            echo 'Data Invoice PO Terbuat';
                                                                        } elseif ($item->subject_type == 'App\Models\Pembayaran') {
                                                                            echo 'Data Pembayaran terbuat';
                                                                        }
                                                                    }
                                                                @endphp
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                                </div>
                                            </div>
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
                                                            <input type="text" id="sub_total" name="sub_total" class="form-control" onchange="calculateTotal(0)" value="{{ formatRupiah2($inv_po->subtotal) }}" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                               
                                                <li>
                                                    <h4>PPN</h4>
                                                    <h5 class="col-lg-5">
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="persen_ppn" name="persen_ppn" class="form-control" value="{{formatRupiah2($inv_po->ppn) }}" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Biaya Pengiriman</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="biaya_ongkir" name="biaya_ongkir" class="form-control" oninput="calculateTotal(0)" value="{{ formatRupiah2($inv_po->biaya_kirim) }}" readonly required>
                                                        </div>    
                                                    </h5>
                                                </li>
                                                @if ($retur && $retur->komplain == 'Refund')
                                                <li>
                                                    <h4>Total Tagihan barang</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="total_tagihan" name="total_tagihan" class="form-control" value="{{ formatRupiah2($inv_po->subtotal + $inv_po->ppn + $inv_po->biaya_kirim ) }}" readonly required>
                                                        </div>    
                                                    </h5>
                                                </li>
                                                @endif
                                                @if ($retur)
                                                <li>
                                                    <h4>Biaya Pengiriman {{ $retur->komplain }}</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="biaya_ongkir" name="biaya_ongkir" class="form-control" oninput="calculateTotal(0)" value="{{ formatRupiah2($inv_po->retur->ongkir) }}" readonly required>
                                                        </div>    
                                                    </h5>
                                                </li>
                                                @endif
                                                <li class="total">
                                                    <h4>Total Tagihan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="total_tagihan" name="total_tagihan" class="form-control" value="{{ formatRupiah2($inv_po->total_tagihan) }}" readonly required>
                                                        </div>    
                                                    </h5>
                                                </li>
                                                @if (!$retur)
                                                <li>
                                                    <h4>Total Diskon</h4>
                                                    <h5 class="col-lg-5">
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" class="form-control" required name="diskon_total" id="diskon_total" oninput="calculateTotal(0)" placeholder="contoh : 2000" value="{{ $totalDis }}" readonly>
                                                        </div>
                                                    </h5>
                                                  
                                                </li>
                                                <li>
                                                    <h4>DP</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="dp" name="dp" class="form-control" 
                                                            value="{{ formatRupiah2($inv_po->dp) ?? '' }}" 
                                                            readonly required>
                                                        </div>
                                                    </h5>
                                                </li>
                                                @endif
                                                <li>
                                                    <h4>Sisa Tagihan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="sisa_bayar" name="sisa_bayar" class="form-control" 
                                                        value="{{ formatRupiah2($inv_po->sisa) ?? '' }}" 
                                                        readonly required>
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
                                                        <input type="text" class="form-control" value="{{ $pembuat }} ({{ $pembuatjbt }})"  disabled>
                                                    </td>
                                                    <td id="pembuku">
                                                        <input type="text" class="form-control" value="{{ $pembuku }} ({{ $pembukujbt }})"  disabled>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td id="status_dibuat">
                                                        <select id="status_dibuat" name="status_dibuat" class="form-control" required readonly>
                                                           <option value="draft" {{ $inv_po->status_dibuat == 'draft' ? 'selected' : '' }}>Draft</option>
                                                           <option value="publish" {{ $inv_po->status_dibuat == 'publish' ? 'selected' : '' }}>Publish</option>
                                                       </select>
                                                       </td>
                                                       <td id="status_diterima">
                                                           <select id="status_dibukukan" name="status_dibukukan" class="form-control" required readonly>
                                                               <option value="pending" {{ $inv_po->status_dibuku == 'pending' ? 'selected' : '' }}>Pending</option>
                                                               <option value="acc" {{ $inv_po->status_dibuku == 'acc' ? 'selected' : '' }}>Accept</option>
                                                           </select>
                                                       </td>
                                                </tr>
                                                <tr>
                                                    <td id="tgl_dibuat">
                                                        <input type="text" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{ tanggalindo($inv_po->tgl_dibuat)}}" disabled>
                                                    </td>
                                                    <td id="tgl_dibuku">
                                                        <input type="text" class="form-control" id="tgl_dibuku" name="tgl_dibukukan" value="{{ tanggalindo($inv_po->tgl_dibukukan) }}" disabled>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>  
                                        <br>                                 
                               </div>
                         </div>

                        <div class="text-end mt-3">
                            {{-- <button class="btn btn-primary" type="submit">Submit</button> --}}
                            <a href="{{ route('invoicebeli.index') }}" class="btn btn-secondary" type="button">Back</a>
                        </div>
            {{-- </form> --}}
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
            {{-- <form id="supplierForm" action="{{ route('bayarpo.store')}}" method="POST" enctype="multipart/form-data">
                @csrf --}}
            <div class="mb-3">
              <label for="nobay" class="form-label">No Bayar</label>
              <input type="hidden" class="form-control" id="type" name="type" value="pembelian">
              <input type="hidden" class="form-control" id="idpo" name="id_po" value="{{ $inv_po->pembelian_id }}">
              <input type="hidden" class="form-control" id="invoice_purchase_id" name="invoice_purchase_id" value="{{ $inv_po->id }}">
              <input type="text" class="form-control" id="nobay" name="no_invoice_bayar" value="{{ $no_bypo }}" readonly>
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
                <input type="text" class="form-control"  id="nominal">
              </div>
              <input type="text" class="form-control"  id="nominal2" name="nominal" hidden>
            </div>
            <div class="mb-3">
              <label for="bukti" class="form-label">Bukti</label>
              <input type="file" class="form-control" id="bukti" name="bukti">
            </div>
            
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              {{-- <button type="submit" class="btn btn-primary">Simpan</button> --}}
            </div>
          {{-- </form> --}}
        </div>
      </div>
    </div>
</div>

@endsection

@section('scripts')
<script>

    document.getElementById('copyIcon').addEventListener('click', function() {
        var returNumber = document.getElementById('returNumber').innerText;
        var tempInput = document.createElement('input');
        tempInput.style.position = 'absolute';
        tempInput.style.left = '-9999px';
        tempInput.value = returNumber;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        alert('Nomor retur berhasil disalin: ' + returNumber);
    });

    function formatRupiah(angka) {
            var reverse = angka.toString().split('').reverse().join('');
            var ribuan = reverse.match(/\d{1,3}/g);
            ribuan = ribuan.join('.').split('').reverse().join('');
            return ribuan;
        }
    
    function unformatRupiah(formattedValue) {
        return formattedValue.replace(/\./g, '');
    }

    document.getElementById('nominal').addEventListener('keyup', function(e) {
        var rupiah = this.value.replace(/[^\d]/g, ''); // hanya ambil angka
        this.value = formatRupiah(rupiah);

        // Set nilai ke input hidden
        document.getElementById('nominal2').value = unformatRupiah(this.value);
    });
</script>


@endsection