@extends('layouts.app-von')

@section('content')
<div id="form" class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Data Retur Pembelian</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('returfinance.update', $data->id) }}" method="POST" enctype="multipart/form-data" id="addForm">
                    @csrf
                    @method('PUT')
                <div class="row">
                    <div class="col-sm">
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
                                                <img id="preview" src="{{ $data->foto ? '/storage/' . $data->foto : '' }}" alt="your image" class="img-thumbnail" data-bs-toggle="modal" data-bs-target="#imageModal" onclick="showImageInModal(this)" />
                                            </div>
                                            <!-- Modal -->
                                            <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Tambahkan kelas modal-lg -->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="imageModalLabel">Preview Image</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <img id="modalImage" src="" alt="Preview Image" class="img-fluid w-100"> <!-- Tambahkan kelas w-100 -->
                                                        </div>
                                                    </div>
                                                </div>
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
                                        {{-- <th>No</th> --}}
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
                                            {{-- <td>1</td> --}}
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
                               
                                    <center><h5>Riwayat uang masuk (Refund) </h5></center><br>
                                    {{-- @if(Auth::user()->hasRole('Finance') && $data->status_dibuat == "DIKONFIRMASI") --}}
                                    @if (Auth::user()->hasRole('Finance') && $data->sisa != 0 && $data->status_dibuku == "DIKONFIRMASI")   
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModalbayar">
                                         Tambah Pembayaran
                                    </button>
                                    @else
                                        Riwayat Pembayaran
                                    @endif
                                    {{-- <a href="" data-toggle="modal" data-target="#myModalbayar" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Pembayaran</a> --}}
                               
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
                                                @if(in_array('pembayaran_pembelian.edit', $thisUserPermissions) && $data->status_dibuku !== "DIKONFIRMASI")
                                                <th>Aksi</th>
                                                @endif
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
                                                @if(in_array('pembayaran_pembelian.edit', $thisUserPermissions) && $data->status_dibuku !== "DIKONFIRMASI")
                                                <td class="text-center">
                                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="javascript:void(0);" onclick="editbayar({{ $bayar->id }})" class="dropdown-item"><img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                                        </li>
                                                    </ul>
                                                </td>
                                                @endif
                                               
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
                                            <h4>Sub Total  {{ $data->komplain }} </h4>
                                            <h5>
                                                    <input type="text" id="sub_total" name="sub_total" class="form-control" value="{{ formatRupiah($data->subtotal) }}" readonly>
                                            </h5>
                                        </li>
                                        <li>
                                            <h4>Sisa Piutang {{ $data->komplain }} </h4>
                                            <h5>
                                                    <input type="text" id="sisa" name="sisa" class="form-control" value="{{ formatRupiah($data->sisa) }}" readonly>
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
                                                    <input type="text" class="form-control" value="{{ $pembuat  }} ({{ $pembuatjbt  }})"  disabled>
                                                </td>
                                                <td id="pembuku">
                                                    @if(Auth::user()->hasRole('Purchasing'))
                                                        @if (!$pembuku )
                                                        <input type="text" class="form-control" value="Nama (Finance)"  disabled>
                                                        @else
                                                        <input type="text" class="form-control" value="{{ $pembuku }} ({{ $pembukujbt }})"  disabled>
                                                        @endif
                                                    @endif
    
                                                    @if(Auth::user()->hasRole('Finance'))
                                                        @if($data->status_dibuku == "DIKONFIRMASI")

                                                        <input type="text" class="form-control" value="{{ $pembuku  }} ({{ $pembukujbt  }})"  disabled>

                                                        @else
                                                        <input type="hidden" name="pembuku" value="{{ Auth::user()->id ?? '' }}">
                                                        <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" placeholder="{{ Auth::user()->karyawans->nama ?? '' }}" disabled>

                                                        @endif
                                                    @endif
                                                </td>
    
                                                
                                            </tr>
                                            <tr>
                                                <td id="status_dibuat">
                                                    <input type="text" class="form-control" id="status_buat" value="{{ $data->status_dibuat }}" readonly>
                                                </td>
                                                <td id="status_dibuku">
                                                    @if(Auth::user()->hasRole('Purchasing'))
                                                    <input type="text" class="form-control" id="status_dibuku" value="{{ $data->status_dibuku ?? '-' }}" readonly>
                                                    @endif
                                                    @if(Auth::user()->hasRole('Finance'))

                                                        @if($data->status_dibuku == "TUNDA" && $data->sisa = 0)
                                                        <select id="status" name="status_dibuku" class="form-control select2" required>
                                                            <option value="TUNDA" {{ old('status_dibuku', $data->status_dibuku) == 'TUNDA' ? 'selected' : '' }}>TUNDA</option>
                                                            <option value="DIKONFIRMASI" {{ old('status_dibuku', $data->status_dibuku)  == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                        </select>
                                                        @else
                                                        <input type="text" class="form-control" id="status_buku" value="{{ $data->status_dibuku }}" readonly>
                                                        @endif
                                                    @endif
    
                                                </td>
                                            </tr>
                                            <tr>
                                                <td id="tgl_dibuat">
                                                    <input type="text" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{tanggalindo($data->tgl_dibuat) }}" readonly>
                                                </td>
                                                <td id="tgl_dibuku">
                                                    @if(Auth::user()->hasRole('Purchasing'))
                                                    <input type="text" class="form-control" id="tgl_dibuku" name="tgl_dibuku" value="{{isset($data->tgl_dibuku) ? tanggalindo($data->tgl_dibuku) : '-'}}" readonly>
                                                    @endif
                                                    @if(Auth::user()->hasRole('Finance'))
                                                        @if($data->status_dibuku == "TUNDA" && $data->sisa = 0)
                                                        <input type="date" class="form-control" id="tgl_dibuat" name="tgl_dibuku" value="{{ now()->format('Y-m-d') }}" >
                                                        @else
                                                        <input type="text" class="form-control" id="tgl_dibuat" name="tgl_dibuku" value="{{tanggalindo($data->tgl_dibuku) }}" readonly>
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
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
                                                <input type="text" class="form-control" value="{{ $pembuat  }} ({{ $pembuatjbt  }})"  disabled>
                                            </td>
                                            <td id="pembuku">
                                                {{-- @if(Auth::user()->hasRole('Purchasing')) --}}
                                                    @if (!$pembuku )
                                                    <input type="text" class="form-control" value="Nama (Finance)"  disabled>
                                                    @else
                                                    <input type="text" class="form-control" value="{{ $pembuku }} ({{ $pembukujbt }})"  disabled>
                                                    @endif
                                                {{-- @endif --}}

                                                {{-- @if(Auth::user()->hasRole('Finance'))
                                                    @if($data->status_dibuku == "DIKONFIRMASI")

                                                    <input type="text" class="form-control" value="{{ $pembuku  }} ({{ $pembukujbt  }})"  disabled>

                                                    @else
                                                    <input type="hidden" name="pembuku" value="{{ Auth::user()->id ?? '' }}">
                                                    <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" placeholder="{{ Auth::user()->karyawans->nama ?? '' }}" disabled>

                                                    @endif
                                                @endif --}}
                                            </td>                                         
                                        </tr>
                                        <tr>
                                            <td id="status_dibuat">
                                                <input type="text" class="form-control" id="status_buat" value="{{ $data->status_dibuat }}" readonly>
                                            </td>
                                            <td id="status_dibuku">
                                                {{-- @if(Auth::user()->hasRole('Purchasing')) --}}
                                                <input type="text" class="form-control" id="status_dibuku" value="{{ $data->status_dibuku ?? '-' }}" readonly>
                                                {{-- @endif --}}
                                                {{-- @if(Auth::user()->hasRole('Finance'))

                                                    @if($data->status_dibuku == "DIKONFIRMASI")
                                                    <input type="text" class="form-control" id="status_buku" value="{{ $data->status_dibuku }}" readonly>

                                                    @else
                                                    <select id="status" name="status_dibuku" class="form-control select2" required>
                                                        <option value="TUNDA" {{ old('status_dibuku', $data->status_dibuku) == 'TUNDA' ? 'selected' : '' }}>TUNDA</option>
                                                        <option value="DIKONFIRMASI" {{ old('status_dibuku', $data->status_dibuku)  == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                    </select>
                                                    @endif
                                                @endif --}}

                                            </td>
                                        </tr>
                                        <tr>
                                            <td id="tgl_dibuat">
                                                <input type="text" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{tanggalindo($data->tgl_dibuat) }}" readonly>
                                            </td>
                                            <td id="tgl_dibuku">
                                                {{-- @if(Auth::user()->hasRole('Purchasing')) --}}
                                                <input type="text" class="form-control" id="tgl_dibuku" name="tgl_dibuku" value="{{isset($data->tgl_dibuku) ? tanggalindo($data->tgl_dibuku) : '-'}}" readonly>
                                                {{-- @endif
                                                @if(Auth::user()->hasRole('Finance'))
                                                    @if($data->status_dibuku == "DIKONFIRMASI")
                                                    <input type="text" class="form-control" id="tgl_dibuat" name="tgl_dibuku" value="{{tanggalindo($data->tgl_dibuku) }}" readonly>
                                                    @else
                                                    <input type="date" class="form-control" id="tgl_dibuat" name="tgl_dibuku" value="{{ now()->format('Y-m-d') }}" >
                                                    @endif
                                                @endif --}}
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
                                            <h4>Sub Total {{ $data->komplain }}</h4>
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
                    {{-- @if(Auth::user()->hasRole('Finance') && ($data->status_dibuku == "TUNDA" || $data->status_dibuku == null))
                    <button class="btn btn-primary" type="submit">Submit</button>
                    @endif --}}
                    <a href="{{ route('returbeli.index') }}" class="btn btn-secondary" type="button">Back</a>
                </div>
            </form>
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
                <input type="text" class="form-control"  id="nominal">
              </div>
              <input type="text" class="form-control"  id="nominal2" name="nominal" hidden>
            </div>
            <div class="mb-3">
                <div class="row mx-auto">
                    <label for="bukti" class="form-label ps-0">Bukti</label>
                    <input type="file" class="form-control" id="buktiByr" name="bukti" accept="image/*">
                </div>
                <div style="text-align: center;">
                    <img id="previewByr" src="" alt="Bukti tf" style="max-width: 100%; max-height: 300px; object-fit: contain;">
                </div>
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
<div class="modal fade" id="editModalbayar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Edit Pembayaran</h5>
          <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="editBayarForm" action="" method="POST" enctype="multipart/form-data">
            @csrf
            @method('patch')
            <div class="mb-3">
              <label for="nobay" class="form-label">No Bayar</label>
              <input type="hidden" class="form-control" id="edit_type" name="type" value="ReturPO">
              <input type="hidden" class="form-control" id="edit_invoice_id" name="invoice_id" value="">
              <input type="text" class="form-control" id="edit_nobay" name="no_invoice_bayar" value="" readonly>
            </div>
            <div class="mb-3">
              <label for="tgl" class="form-label">Tanggal</label>
              <input type="date" class="form-control" id="edit_tgl" name="tanggal_bayar" value="">
            </div>
            <div class="mb-3">
                <label for="metode" class="form-label">Metode</label>
                <select class="form-control select2" id="edit_metode" name="metode">
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
                <input type="text" class="form-control"  id="edit_nominal" name="nominal" value="">
              </div>
            </div>
            <div class="mb-3">
                <div class="row mx-auto">
                    <label for="bukti" class="form-label ps-0">Bukti</label>
                    <input type="file" class="form-control" id="edit_bukti" name="bukti" accept="image/*">
                </div>
                <div style="text-align: center;">
                    <img id="edit_preview" src="" alt="Bukti tf" style="max-width: 100%; max-height: 300px; object-fit: contain;">
                </div>
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
    function showImageInModal(element) {
        var imgSrc = element.src;
        document.getElementById('modalImage').src = imgSrc;
    }

    $(document).ready(function(){
        if ($('#previewByr').attr('src') === '') {
            $('#previewByr').attr('src', defaultImg);
        }
        if ($('#edit_preview').attr('src') === '') {
                $('#edit_preview').attr('src', defaultImg);
            }
        $('#buktiByr').on('change', function() {
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
                    $('#previewByr').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
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

    $(document).on('input', '[id^=edit_nominal]', function() {
        let input = $(this);
        let value = input.val();
        
        if (!isNumeric(cleanNumber(value))) {
        value = value.replace(/[^\d]/g, "");
        }

        value = cleanNumber(value);
        let formattedValue = formatNumber(value);
        
        input.val(formattedValue);
    });
    $('#editBayarForm').on('submit', function(e) {
        // Add input number cleaning for specific inputs
        let inputs = $('#editBayarForm').find('[id^=edit_nominal]');
        inputs.each(function() {
            let input = $(this);
            let value = input.val();
            let cleanedValue = cleanNumber(value);

            // Set the cleaned value back to the input
            input.val(cleanedValue);
        });

        return true;
    });$('#edit_bukti').on('change', function() {
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
                    $('#edit_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
    function editbayar(id){
        $.ajax({
            type: "GET",
            url: "/purchase/pembayaran/"+id+"/edit",
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: {
                'jenis': 'ReturPO',
            },
            beforeSend: function() {
                $('#global-loader-transparent').show();
            },
            success: function(response) {
                $('#editBayarForm').attr('action', `{{ route("pembayaran_pembelian.update", ":id") }}`.replace(':id', id));
                $('#edit_nobay').val(response.no_invoice_bayar);
                $('#edit_invoice_id').val(response.invoice_id);
                $('#edit_metode').val(response.metode).trigger('change');
                $('#edit_nominal').val(formatNumber(response.nominal));
                $('#edit_tgl').val(response.tanggal_bayar);
                if(response.bukti){
                    $('#edit_preview').attr('src', '/storage/'+response.bukti);
                } else {
                    $('#edit_preview').attr('src', defaultImg);
                }
                $('#edit_metode').select2({
                    dropdownParent: $("#editModalbayar")
                });
                $('#global-loader-transparent').hide();
                $('#editModalbayar').modal('show');
            },
            error: function(error) {
                $('#global-loader-transparent').hide();
                toastr.error(error.responseJSON, 'Error', {
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: false,
                    progressBar: true
                });
            }
        });
    }
</script>
@endsection