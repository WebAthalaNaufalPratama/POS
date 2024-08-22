
@extends('layouts.app-von')

@section('content')
@php
$user = Auth::user();
@endphp
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
            <h3 class="page-title">Show Mutasi Inden ke Galery/GreenHouse</h3>
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
                                            <input type="text" class="form-control" id="tgl_diterima" name="tgl_diterima" value="{{$data->tgl_diterima ? tanggalindo($data->tgl_diterima) : "-" }}" readonly>
                                         </div>
                                        <div class="form-group">
                                            <label for="tgl_terima">Bukti</label>
                                                <img id="preview" src="{{ $data->bukti ? '/storage/' . $data->bukti : '' }}" alt="your image" class="img-thumbnail" data-bs-toggle="modal" data-bs-target="#imageModal" onclick="showImageInModal(this)" />                                            
                                        </div>
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
                                                    @if($user->hasRole(['Purchasing','Finance']) && (!$data->returinden || ($data->returinden && $data->returinden->status_dibuat == "BATAL")) )  
                                                    <th>Biaya Perawatan</th>
                                                    <th>Total Biaya Perawatan</th>
                                                 
                                                    @endif
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
                                                    <input type="hidden" class="form-control" name="kategori1[]" id="kategori1_{{ $index }}" value="{{ $item->produk->kode_produk}}" readonly>
                                                    </td>
                                                    <td><input type="number" name="qtykrm[]" id="qtykrm_{{ $index }}" class="form-control" onchange="calculateTotal({{ $index }})" value="{{ $item->jml_dikirim }}" readonly></td>
                                                    <td><input type="number" name="qtytrm[]" id="qtytrm_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{ $item->jml_diterima }}" readonly></td>
                                                    <td>
                                                        <input type="text" name="kondisi[]" id="kondisi_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{ $item->kondisi->nama ?? '' }}" readonly>
                                                        {{-- <select id="kondisi_{{ $index }}" name="kondisi[]" class="form-control">
                                                            <option value="">Pilih Kondisi</option>
                                                            @foreach ($kondisis as $kondisi)
                                                                <option value="{{ $kondisi->id }}">{{ $kondisi->nama }}</option>
                                                            @endforeach
                                                        </select> --}}
                                                    </td>
                                                    @if($user->hasRole(['Purchasing','Finance']) && (!$data->returinden || ($data->returinden && $data->returinden->status_dibuat == "BATAL")))   
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" name="rawat2[]" id="rawat2_{{ $index }}" class="form-control-banyak" value="{{ formatRupiah2($item->biaya_rawat) }}" readonly>
                                                            <input type="hidden" name="rawat[]" id="rawat_{{ $index }}" class="form-control">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" name="jumlah_display[]" id="jumlah_{{ $index }}" class="form-control-banyak" value="{{ formatRupiah2($item->totalharga)}}" readonly>
                                                            <input type="hidden" name="jumlah[]" id="jumlahint_{{ $index }}" class="form-control">
                                                        </div>
                                                    </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($user->hasRole(['Purchasing','Finance']) && !$data->returinden || ($data->returinden && $data->returinden->status_dibuat == "BATAL"))   
                        <div class="row justify-content-around">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2">
                                <div class="row">
                                    <div class="col-lg-7 col-sm-6 col-6 mt-4 ">
                                        <div class="page-btn">
                                            @if (Auth::user()->hasRole('Finance') && $data->sisa_bayar !== 0)    
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModalbayar">
                                                Tambah Pembayaran
                                           </button>
                                            {{-- <a href="" data-toggle="modal" data-target="#myModalbayar" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Pembayaran</a> --}}
                                            @else
                                            Riwayat Pembayaran
                                            @endif
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table datanew">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>No Bayar</th>
                                                        <th>Tanggal</th>
                                                        <th>Metode</th>
                                                        <th>Nominal</th>
                                                        <th>Bukti</th>
                                                        <th>Status</th>
                                                        @if(in_array('pembayaran_pembelian.edit', $thisUserPermissions))
                                                        <th>Aksi</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($databayars as $databayar)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $databayar->no_invoice_bayar }}</td>
                                                        <td>{{ tanggalindo($databayar->tanggal_bayar) }}</td>
                                                        <td>{{ $databayar->cara_bayar }}</td>
                                                        <td>{{ formatRupiah($databayar->nominal)}}</td>
                                                        <td>
                                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#buktiModal{{ $databayar->id }}">
                                                                Lihat Bukti
                                                            </button>
                                                    
                                                            <!-- Modal -->
                                                            <div class="modal fade" id="buktiModal{{ $databayar->id }}" tabindex="-1" role="dialog" aria-labelledby="buktiModalLabel{{ $databayar->id }}" aria-hidden="true">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="buktiModalLabel{{ $databayar->id }}">Bukti Pembayaran</h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <img src="{{ asset('storage/'.$databayar->bukti) }}" class="img-fluid" alt="Bukti Pembayaran">
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                
                                                        </td>
                                                        <td>{{ $databayar->status_bayar}}</td>
                                                        @if(in_array('pembayaran_pembelian.edit', $thisUserPermissions))
                                                        <td class="text-center">
                                                            <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                            </a>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a href="javascript:void(0);" onclick="editbayar({{ $databayar->id }})" class="dropdown-item"><img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                        @endif
                                                       
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
                                                            
                                                            <input type="text" id="sub_total" name="sub_total_dis" class="form-control" onchange="calculateTotal(0)" value="{{ formatRupiah2($data->subtotal) }}" readonly>
                                                            <input type="hidden" id="sub_total_int" name="sub_total" class="form-control" onchange="calculateTotal(0)" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Biaya Perawatan</h4>
                                                        <h5>
                                                            <div class="input-group">
                                                                <span class="input-group-text">Rp. </span>
                                                                <input type="text" id="biaya-rawat" name="biaya_rwt_dis" class="form-control" oninput="calculateTotal(0)" value="{{ formatRupiah2($data->biaya_perawatan) }}" readonly>
                                                                <input type="hidden" id="biaya_rwt" name="biaya_rwt" class="form-control" oninput="calculateTotal(0)">
                                                            </div>
                                                        </h5>

                                                </li>
                                                
                                                <li>
                                                    <h4>Biaya Pengiriman</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="biaya_ong" name="biaya_ongkir_dis"  class="form-control" oninput="calculateTotal(0)" value="{{ formatRupiah2($data->biaya_pengiriman) }}" readonly>
                                                            <input type="hidden" id="biaya_ongkir" name="biaya_ongkir" class="form-control" oninput="calculateTotal(0)">
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li class="total">
                                                    <h4>Total Tagihan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="total_tag" name="total_tagihan_dis" class="form-control" value="{{ formatRupiah2($data->total_biaya) }}" readonly>
                                                            <input type="hidden" id="total_tagihan_int" name="total_tagihan" class="form-control" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Sisa Tagihan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="sisa" name="sisa_bayar" class="form-control" value="{{ formatRupiah2($data->sisa_bayar) }}" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <form action="{{ route('mutasiindengh.updatePembuku', $data->id ) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('patch')
                         <div class="row justify-content-start">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2"> 
                             
                                        <table class="table table-responsive border rounded">
                                            <thead>
                                                <tr>
                                                    <th>Dibuat</th>                                              
                                                    <th>Diterima</th>                                              
                                                    <th>Diperiksa</th>
                                                    <th>Dibukukan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td id="pembuat">
                                                        <input type="hidden" name="pembuat" value="{{ Auth::user()->id ?? '' }}">
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
                                                    @if(Auth::user()->hasRole('Finance') && ($data->status_dibukukan == null || $data->status_dibukukan == "MENUNGGU PEMBAYARAN"))

                                                        <input type="hidden" name="pembuku" value="{{ Auth::user()->id ?? '' }}">
                                                        <input type="text" class="form-control" value="{{  Auth::user()->karyawans->nama }} ({{  Auth::user()->karyawans->jabatan }})" readonly>
                                                    
                                                    @elseif(Auth::user()->hasRole('Finance') && $data->status_dibukukan == "DIKONFIRMASI")
                                                        <input type="text" class="form-control" value="{{ $pembuku ?? '' }} ({{ $jabatanbuku ?? '' }})" readonly>
                                                    @else
                                                        <input type="hidden" name="pembuku" value="{{ Auth::user()->id ?? '' }}">
                                                        @if($pembuku)
                                                        <input type="text" class="form-control" value="{{ $pembuku ?? '' }} ({{ $jabatanbuku ?? '' }})" readonly>
                                                        @else
                                                        <input type="text" class="form-control" value="Nama (Finance)" readonly>
                                                        @endif
                                                    @endif
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td id="status_dibuat">
                                                        <input type="text" class="form-control" value="{{ $data->status_dibuat }}" readonly>

                                                        {{-- <select id="status_dibuat" name="status_dibuat" class="form-control" readonly>
                                                            <option selected disabled>Pilih Status</option>
                                                            <option value="TUNDA" disabled {{ $data->status_dibuat == 'TUNDA' ? 'selected' : '' }}>TUNDA</option>
                                                            <option value="DIKONFIRMASI" disabled {{ $data->status_dibuat == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                            <option value="BATAL" disabled {{ $data->status_dibuat == 'BATAL' ? 'selected' : '' }}>BATAL</option>
                                                        </select> --}}
                                                    </td>
                                                    <td id="status_diterima">
                                                        <input type="text" class="form-control" value="{{ $data->status_diterima ?? '-'}}" readonly>

                                                        {{-- <select id="status_diterima" name="status_diterima" class="form-control" readonly>
                                                            <option selected disabled>Pilih Status</option>
                                                            <option value="TUNDA" disabled {{ $data->status_diterima == 'TUNDA' ? 'selected' : '' }}>TUNDA</option>
                                                            <option value="DIKONFIRMASI" disabled {{ $data->status_diterima == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                            <option value="BATAL" disabled {{ $data->status_diterima == 'BATAL' ? 'selected' : '' }}>BATAL</option>
                                                        </select> --}}
                                                    </td>
                                                    <td id="status_diperiksa">
                                                        <input type="text" class="form-control" value="{{ $data->status_diperiksa ?? '-'}}" readonly>
                                                        
                                                        {{-- <select id="status_diperiksa" name="status_diperiksa" class="form-control" readonly>
                                                            <option disabled selected>Pilih Status</option>
                                                            <option value="TUNDA" disabled {{ $data->status_diperiksa == 'TUNDA' ? 'selected' : '' }}>TUNDA</option>
                                                            <option value="DIKONFIRMASI" disabled {{ $data->status_diperiksa == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                            <option value="BATAL" disabled {{ $data->status_diperiksa == 'BATAL' ? 'selected' : '' }}>BATAL</option>
                                                        </select> --}}
                                                    </td>
                                                    <td id="status_dibukukan">
                                                    @if(Auth::user()->hasRole('Finance') && $data->status_dibukukan == "MENUNGGU PEMBAYARAN")
                                                    <select id="status_dibukukan" name="status_dibukukan" class="form-control">
                                                        <option disabled>Pilih Status</option>
                                                        <option value="MENUNGGU PEMBAYARAN" {{ $data->status_dibukukan == 'MENUNGGU PEMBAYARAN' ? 'selected' : '' }}>MENUNGGU PEMBAYARAN</option>
                                                        @if( ($data->returinden ==null && $data->sisa_bayar == 0) || ($data->returinden && $data->returinden->status_dibuat == "BATAL" && $data->sisa_bayar == 0) || ($data->returinden !== null && $data->sisa_bayar == 0 && $data->returinden->sisa_refund == 0 && $data->returinden->status_dibukukan == "DIKONFIRMASI" &&  $data->returinden->status_dibuat == "DIKONFIRMASI"))
                                                        <option value="DIKONFIRMASI" {{ $data->status_dibukukan == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                        @endif
                                                    </select>
                                                    @elseif(Auth::user()->hasRole('Finance') && $data->status_dibukukan == "DIKONFIRMASI")
                                                        <input type="text" class="form-control" value="{{ $data->status_dibukukan ?? '-'}}" readonly>
                                                    @else
                                                        <input type="text" class="form-control" value="{{ $data->status_dibukukan ?? '-'}}" readonly>
                                                    @endif
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
                                                    @if(Auth::user()->hasRole('Finance') && ($data->status_dibukukan == null || $data->status_dibukukan == "MENUNGGU PEMBAYARAN"))
                                                    <input type="date" class="form-control" id="tgl_dibukukan" name="tgl_dibukukan" value="{{ now()->format('Y-m-d')  }}">
                                                    @elseif(Auth::user()->hasRole('Finance') && $data->status_dibukukan == "DIKONFIRMASI")
                                                    <input type="text" class="form-control" id="tgl_dibukukan" name="tgl_dibukukan" value="{{ $data->tgl_dibukukan ? tanggalindo($data->tgl_dibukukan) : '-' }}"  readonly>
                                                    @else
                                                    <input type="text" class="form-control" id="tgl_dibukukan" name="tgl_dibukukan" value="{{ $data->tgl_dibukukan ? tanggalindo($data->tgl_dibukukan) : '-' }}"  readonly>
                                                    @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>  
                                        <br>                                 
                               </div>
                         </div>

                        <div class="text-end mt-3">
                            @if(Auth::user()->hasRole('Finance') && $data->status_dibukukan == "MENUNGGU PEMBAYARAN") 
                            <button class="btn btn-primary" type="submit">Submit</button>
                            @endif
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
            <form id="supplierForm" action="{{ route('pembayaranmutasi.store')}}" method="POST" enctype="multipart/form-data">
                @csrf 
            <div class="mb-3">
              <label for="nobay" class="form-label">No Bayar</label>
              <input type="hidden" class="form-control" id="mutasiinden_id" name="mutasiinden_id" value="{{ $data->id }}">
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
                  <input type="text" class="form-control"  id="nominal" value="{{ formatRupiah2($data->sisa_bayar) }}">
                </div>
                <input type="text" class="form-control"  id="nominal2" name="nominal" value="{{ $data->sisa_bayar }}" hidden>
            </div>
            <div class="mb-3">
                <div class="row mx-auto">
                    <label for="bukti" class="form-label ps-0">Bukti</label>
                    <input type="file" class="form-control" id="buktitf" name="buktitf" accept="image/*">
                </div>
                <div style="text-align: center;">
                    <img id="previewtf" src="" alt="Bukti tf" style="max-width: 100%; max-height: 300px; object-fit: contain;">
                </div>
            </div>            
            <div class="modal-footer justify-content-center">
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
              <input type="hidden" class="form-control" id="edit_type" name="type" value="MutasiInden">
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

{{-- <input type="text" name="rupiah" id="rupiah"> --}}

@endsection

@section('scripts')
 <script>
    function showImageInModal(element) {
        var imgSrc = element.src;
        document.getElementById('modalImage').src = imgSrc;
    }

    document.getElementById('nominal').addEventListener('keyup', function(e) {
        var rupiah = this.value.replace(/[^\d]/g, ''); // hanya ambil angka
        this.value = formatRupiah(rupiah);

        // Set nilai ke input hidden
        document.getElementById('nominal2').value = unformatRupiah(this.value);
    });

           
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
            if ($('#previewtf').attr('src') === '') {
                $('#previewtf').attr('src', defaultImg);
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
            $('#buktitf').on('change', function() {
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
                        $('#previewtf').attr('src', e.target.result);
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
    });
    function editbayar(id){
        $.ajax({
            type: "GET",
            url: "/purchase/pembayaran/"+id+"/edit",
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: {
                'jenis': 'MutasiInden',
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