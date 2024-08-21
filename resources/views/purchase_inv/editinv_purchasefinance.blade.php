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
            @if(Auth::user()->hasRole('Purchasing'))
            <h3 class="page-title">Edit Invoice (Purchasing)</h3>
            @endif
            @if(Auth::user()->hasRole('Finance'))
            <h3 class="page-title">Edit Invoice (Finance)</h3>
            @endif
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
            <form action="{{ route('invoice_purchase.update', $inv_po->id )}} " method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-sm">
                        <div class="row justify-content-start">
                            <div class="col-md-12 border rounded pt-3 me-1">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="no_inv">No. Invoice</label>
                                            <input type="text" class="form-control" id="no_inv" name="no_inv"  value="{{ $inv_po->no_inv }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="tg_inv">Tanggal Invoice</label>
                                            <input type="date" class="form-control" id="tgl_inv" name="tgl_inv" value="{{ $inv_po->tgl_inv }}">
                                        </div>
                                    </div>
                                   {{-- info po --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="no_po">No. Purchase Order</label>
                                            <input type="hidden" class="form-control" id="no_po" name="id_po" value="{{ $inv_po->pembelian_id }}" readonly>
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
                                                    <td><input type="number" name="qtytrm[]" id="qtytrm_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{ $item->jml_diterima }}" readonly></td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" name="harga_display[]" id="harga2_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{formatRupiah2($item->harga) }}" required>
                                                            <input type="hidden" name="harga[]" id="harga_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{$item->harga}}" readonly>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text"  name="diskon_display[]" id="diskon2_{{ $index }}" class="form-control" oninput="limitDiskon({{ $index }}), calculateTotal({{ $index }})" value="{{ formatRupiah2($item->diskon) }}">
                                                            <input type="hidden" name="diskon[]" id="diskon_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{ $item->diskon }}" readonly>
                                                            <input type="hidden" name="distot[]" id="distot_int_{{ $index }}" class="form-control" value="{{ $item->jml_diterima * $item->diskon }}" readonly></td>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" name="jumlah_display[]" id="jumlah_{{ $index }}" class="form-control" value="{{ formatRupiah2($item->totalharga) }}" readonly></td>
                                                            <input type="hidden" name="jumlah[]" id="jumlahint_{{ $index }}" class="form-control" value="{{ $item->totalharga}}" readonly>
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
                                            {{-- <a href="" data-toggle="modal" data-target="#myModalbayar" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Pembayaran</a> --}}
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
                                                            <div class="modal-dialog modal-lg" role="document">
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
                                                            <input type="text" id="sub_total" name="sub_total_dis" class="form-control" onchange="calculateTotal(0)" value="{{ formatRupiah2($inv_po->subtotal) }}" readonly required>
                                                            <input type="hidden" id="sub_total_int" name="sub_total" class="form-control" onchange="calculateTotal(0)" value="{{$inv_po->subtotal}}" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                             
                                                {{-- <li>
                                                    <h4>PPN</h4>
                                                    <h5 class="col-lg-5">
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="persen_ppn" name="persen_ppn" class="form-control" value="{{formatRupiah2($inv_po->ppn) }}" readonly>
                                                        </div>
                                                    </h5>
                                                </li> --}}
                                                <li>
                                                    <h4>PPN
                                                        <select id="jenis_ppn" name="jenis_ppn" class="form-control" required>
                                                            <option value="">Pilih Jenis PPN</option>
                                                            <option value="exclude" @if($inv_po->ppn !== 0) selected @endif>EXCLUDE</option>
                                                            <option value="include" @if($inv_po->ppn == 0) selected @endif>INCLUDE</option>
                                                        </select>
                                                        
                                                    </h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <input type="text" id="persen_ppn" name="persen_ppn" class="form-control" value="{{ $inv_po->persen_ppn ?? 0 }}" oninput="calculatePPN(this), validatePersen(this)" readonly>
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="nominal_ppn" name="nominal_ppn" class="form-control" value="{{ formatRupiah($inv_po->ppn  ?? 0 )}}" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Biaya Pengiriman</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="biaya_ongkir2" name="biaya_ongkir_dis" class="form-control" oninput="calculateTotal(0)" value="{{ formatRupiah2($inv_po->biaya_kirim) }}" required>
                                                            <input type="hidden" id="biaya_ongkir" name="biaya_ongkir" class="form-control" oninput="calculateTotal(0)" value="{{ $inv_po->biaya_kirim }}" required>
                                                        </div>    
                                                    </h5>
                                                </li>
                                            
                                                <li class="total">
                                                    <h4>Total Tagihan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="total_tagihan" name="total_tagihan_dis" class="form-control" readonly value="{{ formatRupiah2($inv_po->total_tagihan) }}" required>
                                                            <input type="hidden" id="total_tagihan_int" name="total_tagihan" class="form-control" value="{{ $inv_po->total_tagihan }}" readonly required>
                                                        </div>    
                                                    </h5>
                                                </li>
                                             
                                                <li>
                                                   
                                                    <h4>Total Diskon</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" class="form-control" required name="total_diskon_display" id="total_diskon_display" oninput="calculateTotal(0), calculateTotal({{ $index }})" value="{{ $totalDis }}" readonly>
                                                        </div>
                                                    </h5>
                                                  
                                                </li>
                                                {{-- <li>
                                                    <h4>DP</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="dp" name="dp" class="form-control" 
                                                            value="{{ formatRupiah2($inv_po->dp) ?? '' }}" 
                                                            readonly required>
                                                        </div>
                                                    </h5>
                                                </li> --}}
                                               
                                                {{-- <li>
                                                    <h4>Sisa Tagihan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span>
                                                            <input type="text" id="sisa_bayar" name="sisa_bayar" class="form-control" 
                                                        value="{{ formatRupiah2($inv_po->sisa) ?? '' }}" 
                                                        readonly required>
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
                            <div class="col-md-6 border rounded pt-3 me-1 mt-2">
                                <table class="table table-responsive border rounded">
                                    <thead>
                                        <tr>
                                            <th>Dibuat</th>                                              
                                            <th>Dibukukan</th>
                                        </tr>
                                    </thead>
                                    @if(Auth::user()->hasRole('Purchasing'))
                                    <tbody>
                                                <tr>
                                                    <td id="pembuat">
                                                        <input type="hidden" name="pembuat" value="{{ Auth::user()->id ?? '' }}">
                                                        <input type="text" class="form-control" value="{{ $pembuat }} ({{ $pembuatjbt }})"  disabled>

                                                    </td>
                                                    <td id="pembuku"> 
                                                        @if (!$pembuku )
                                                        <input type="text" class="form-control" value="Nama (Finance)"  disabled>
                                                        @else
                                                        <input type="text" class="form-control" value="{{ $pembuku }} ({{ $pembukujbt }})"  disabled>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td id="status_dibuat">
                                                        {{-- <input type="text" class="form-control" id="status_buat" value="{{ $inv_po->status_dibuat }}"> --}}
                                                        <select id="status" name="status_dibuat" class="form-control select2" required>
                                                            <option disabled>Pilih Status</option>
                                                            <option value="TUNDA" {{ $inv_po->status_dibuat == 'TUNDA' || $inv_po->status_dibuat == '' ? 'selected' : '' }}>TUNDA</option>
                                                            <option value="DIKONFIRMASI" {{ $inv_po->status_dibuat == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                            {{-- <option value="BATAL" {{ $inv_po->status_dibuat == 'BATAL' ? 'selected' : '' }}>BATAL</option> --}}
                                                        </select>
                                                    </td>
                                                    <td id="status_dibuku">
                                                        <input type="text" class="form-control" id="status_dibuku" value="{{ $inv_po->status_dibuku ?? '-' }}" readonly>

                                                           {{-- <select id="status_dibukukan" name="status_dibukukan" class="form-control" required>
                                                               <option value="pending" {{ $inv_po->status_dibuku == 'pending' ? 'selected' : '' }}>Pending</option>
                                                               <option value="acc" {{ $inv_po->status_dibuku == 'acc' ? 'selected' : '' }}>Accept</option>
                                                           </select> --}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td id="tgl_dibuat">
                                                        <input type="date" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{ now()->format('Y-m-d') }}">
                                                    </td>
                                                    <td id="tgl_dibuku">
                                                        <input type="text" class="form-control" id="tgl_dibuku" name="tgl_dibukukan" value="{{ $inv_po->tgl_dibukukan ?? '-'}}" disabled>
                                                    </td>
                                                </tr>
                                    </tbody>
                                    @endif
                                    @if(Auth::user()->hasRole('Finance'))
                                    <tbody>
                                                <tr>
                                                    <td id="pembuat">
                                                        <input type="text" class="form-control" value="{{ $pembuat }} ({{ $pembuatjbt }})"  disabled>
                                                    </td>
                                                    <td id="pembuku"> 
                                                        <input type="hidden" name="pembuku" value="{{ Auth::user()->id ?? '' }}">
                                                        <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" placeholder="{{ Auth::user()->karyawans->nama ?? '' }}" disabled>
                                                    
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td id="status_dibuat">
                                                        {{-- <input type="text" class="form-control" id="status_buat" value="{{ $inv_po->status_dibuat }}"> --}}
                                                        <select id="status" name="status_dibuat" class="form-control select2" disabled>
                                                            <option disabled>Pilih Status</option>
                                                            <option value="TUNDA" {{ $inv_po->status_dibuat == 'TUNDA' || $inv_po->status_dibuat == '' ? 'selected' : '' }}>TUNDA</option>
                                                            <option value="DIKONFIRMASI" {{ $inv_po->status_dibuat == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                            {{-- <option value="BATAL" {{ $inv_po->status_dibuat == 'BATAL' ? 'selected' : '' }}>BATAL</option> --}}
                                                        </select>
                                                    </td>
                                                    <td id="status_dibuku">
                                                        <select id="status" name="status_dibuku" class="form-control select2">
                                                            <option disabled>Pilih Status</option>
                                                            <option value="TUNDA" {{ $inv_po->status_dibuku == 'TUNDA' ? 'selected' : '' }}>TUNDA</option>
                                                            <option value="MENUNGGU PEMBAYARAN" {{ $inv_po->status_dibuku == 'MENUNGGU PEMBAYARAN' || $inv_po->status_dibuku == null ? 'selected' : '' }}>MENUNGGU PEMBAYARAN</option>
                                                            @if( $inv_po->sisa == 0)
                                                            <option value="DIKONFIRMASI" {{ $inv_po->status_dibuku == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                            @endif
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td id="tgl_dibuat">
                                                        <input type="date" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{ $inv_po->tgl_dibuat }}" disabled>
                                                    </td>
                                                    <td id="tgl_dibuku">
                                                        <input type="date" class="form-control" id="tgl_dibuku" name="tgl_dibukukan" value="{{ now()->format('Y-m-d')  }}">
                                                    </td>
                                                </tr>
                                    </tbody>
                                    @endif
                                </table>    
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
            <form id="supplierForm" action="{{ route('bayarpo.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
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
                <input type="text" class="form-control"  id="nominal" value="">
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
        console.log(input.value)
    });

     // Menghitung tot_disk
     document.querySelectorAll('input[id^="distot_int_"]').forEach(function(input) {
        Totaldis += parseFloat(input.value) || 0;
        console.log(input.value)
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