
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
    .form-group-retur {
    display: flex;
    align-items: center;
    }

    .label-retur {
        margin-right: 10px; /* Atur jarak antara label dan input */
        min-width: 100px; /* Atur lebar minimum label sesuai kebutuhan */
    }

</style>

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Retur Mutasi Inden</h3>
            {{-- <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{route('mutasiindengh.index')}}">Mutasi</a>
                </li>
                <li class="breadcrumb-item active">
                    Retur Inden
                </li>
            </ul> --}}
        {{-- <form action="{{ route('retur.store') }}" method="POST" enctype="multipart/form-data"> --}}
                {{-- @csrf --}}
            <div class="form-group-retur">
                <label for="no_retur" class="label-retur">Nomor Retur :</label>
                <input type="text" class="form-control" id="no_retur" name="no_retur" value="{{ $dataretur->no_retur }}" readonly>
            </div>
            <br>
            <div class="form-group-retur">
                <label for="no_retur" class="label-retur">Tanggal Retur :</label>
                <input type="text" class="form-control" id="no_retur" name="tgl_retur" value="{{ tanggalindo($dataretur->tgl_dibuat) }}" readonly>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="card">
       
        <div class="card-body">
            
                <div class="row">
                    <div class="col-sm">
                        <div class="row justify-content-start">
                            <div class="col-md-12 border rounded pt-3 me-1">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="no_mutasi">No Mutasi</label>
                                            <input type="hidden" id="id_mutasi" name="mutasiinden_id" class="form-control" value="{{ $data->id }}" readonly>
                                            <input type="text" id="no_mutasi" name="no_mutasi" class="form-control" value="{{ $data->no_mutasi }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="tgl_kirim">Tanggal Kirim</label>
                                            <input type="text" class="form-control" id="tgl_kirim" name="tgl_kirim" value="{{ tanggalindo($data->tgl_dikirim) }}" readonly>
                                         </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="supplier">Supplier</label>
                                                <input type="text" class="form-control" id="supplier" name="supplier" value="{{ $data->supplier->nama }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="penerima">Lokasi</label>
                                            <input type="text" class="form-control" id="lokasi" name="lokasi" value="{{ $data->lokasi->nama }}" readonly>

                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="tgl_terima">Tanggal Diterima</label>
                                            <input type="text" class="form-control" id="tgl_diterima" name="tgl_diterima" value="{{ tanggalindo($data->tgl_diterima) }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="tgl_terima">Bukti</label>
                                            <img id="preview" src="{{ $data->bukti ? '/storage/' . $data->bukti : '' }}" alt="your image"  class="img-thumbnail" data-bs-toggle="modal" data-bs-target="#imageModal" onclick="showImageInModal(this)" />                                            
                                        </div>
                                        </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="tgl_terima">Bukti Retur</label>
                                                <img id="preview" src="{{ $dataretur->foto ? '/storage/' . $dataretur->foto : '' }}" alt="your image"  class="img-thumbnail" data-bs-toggle="modal" data-bs-target="#imageModal" onclick="showImageInModal(this)" />                                            
                                        </div>
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
                                                    <input type="hidden" class="form-control" name="kategori1[]" id="kategori1_{{ $index }}" value="{{ $item->produk->kode_produk}}" readonly>
                                                    </td>
                                                    <td><input type="number" name="qtykrm[]" id="qtykrm_{{ $index }}" class="form-control" onchange="calculateTotal({{ $index }})" value="{{ $item->jml_dikirim }}" readonly></td>
                                                    <td><input type="number" name="qtytrm[]" id="qtytrm_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" data-produk-id="{{ $item->id }}" value="{{ $item->jml_diterima }}" readonly></td>
                                                    <td>
                                                        <input type="text" name="kondisi[]" id="kondisi_{{ $index }}" class="form-control" oninput="calculateTotal({{ $index }})" value="{{ $item->kondisi->nama }}" readonly>
                                                        {{-- <select id="kondisi_{{ $index }}" name="kondisi[]" class="form-control">
                                                            <option value="">Pilih Kondisi</option>
                                                            @foreach ($kondisis as $kondisi)
                                                                <option value="{{ $kondisi->id }}">{{ $kondisi->nama }}</option>
                                                            @endforeach
                                                        </select> --}}
                                                    </td>
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
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2">
                                <div class="form-row row">
                                    <div class="mb-4">
                                        <h5>List Produk Komplain</h5>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Kode Inden</th>
                                                    <th>Kategori</th>
                                                    <th>Alasan</th>
                                                    <th>QTY</th>
                                                    <th>Harga Satuan</th>
                                                    <th>Total Harga</th>
                                                </tr>
                                            </thead>
                                            <tbody id="dynamic_field2">
                                                @foreach ($barangretur as $index => $retur)
                                                <tr>   
                                                    <td>
                                                        <select class="form-control" id="kode_inden_retur_0" name="kode_inden_retur[]" onchange="updateKategori(this, 0)" readonly>
                                                            <option value="" disabled selected>Pilih Kode Inden</option>
                                                            @foreach ($barangmutasi as $index => $item)
                                                            <option value="{{ $item->produk->kode_produk_inden }}" 
                                                                @if ($item->produk->kode_produk_inden == $retur->produk->produk->kode_produk_inden)
                                                                    selected
                                                                @endif
                                                                data-kategori="{{ $item->produk->produk->nama }}" data-diterima="{{ $item->jml_diterima }}" data-produk-id="{{ $item->id }}" disabled>
                                                                {{ $item->produk->kode_produk_inden }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <input type="hidden" class="form-control" name="produk_mutasi_inden_id[]" id="produk_mutasi_inden_id_0" readonly>
                                                    <td><input type="text" class="form-control" name="kategori_retur[]" id="kategori_retur_0" value="{{ $retur->produk->produk->produk->nama }}" readonly></td>
                                                    <td><textarea name="alasan[]" id="alasan_0" class="form-control" cols="30" readonly>{{ $retur->alasan }}</textarea></td>
                                                    <td><input type="number" class="form-control qty_retur" name="jml_diretur[]" id="qty_retur_0"  oninput="calculateJumlahRetur(0)" value="{{ $retur->jml_diretur }}" readonly></td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" name="rawat_retur_dis[]" id="rawat_retur_dis_0" class="form-control-banyak"  oninput="calculateJumlahRetur(0)" value="{{ formatRupiah2($retur->harga_satuan) }}" readonly>
                                                            <input type="hidden" name="harga_satuan[]" id="rawat_retur_0" class="form-control">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" name="jumlah_retur_dis[]" id="jumlah_retur_dis_0" class="form-control-banyak" value="{{ formatRupiah($retur->totalharga) }}" readonly>
                                                            <input type="hidden" name="totalharga[]" id="jumlah_retur_0" class="form-control">
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
                                            @if (Auth::user()->hasRole('Finance') && ($dataretur->sisa_refund != 0 || $data->sisa_bayar != 0 ) )   
                                            {{-- <a href="" data-toggle="modal" data-target="#myModalbayar" class="btn btn-added"><img src="/assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Pembayaran</a> --}}
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModalbayar">
                                                Tambah Pembayaran
                                           </button>
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
                                                                    <a href="javascript:void(0);" onclick="editbayar({{ $databayar->id }}, '{{ $databayar->mutasiinden ? 'MutasiInden' : 'ReturInden' }}')" class="dropdown-item"><img src="/assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                        @endif
                                                       
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        {{-- <div class="card">
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
                                        </div> --}}
                                </div>
                                    <div class="col-lg-5 float-md-right">
                                        <div class="total-order">
                                            <ul>
                                                <li>
                                                    <h4>Sub Total</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            
                                                            <input type="text" id="sub_total" name="sub_total_dis" class="form-control" value="{{ formatRupiah2($data->subtotal) }}" readonly>
                                                            <input type="hidden" id="sub_total_int" name="sub_total" class="form-control" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Biaya Perawatan</h4>
                                                        <h5>
                                                            <div class="input-group">
                                                                <span class="input-group-text">Rp. </span>
                                                                <input type="text" id="biaya-rawat" name="biaya_rwt_dis" class="form-control" value="{{ formatRupiah2($data->biaya_perawatan) }}" readonly>
                                                                <input type="hidden" id="biaya_rwt" name="biaya_rwt" class="form-control">
                                                            </div>
                                                        </h5>

                                                </li>
                                                
                                                <li>
                                                    <h4>Biaya Pengiriman</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="biaya_ong" name="biaya_ongkir_dis"  class="form-control" value="{{ formatRupiah2($data->biaya_pengiriman) }}" readonly>
                                                            <input type="hidden" id="biaya_ongkir" name="biaya_ongkir" class="form-control">
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Total Tagihan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="total_tagihan_dis" name="total_tagihan_dis" class="form-control" value="{{ formatRupiah2($data->total_biaya) }}" readonly>
                                                            <input type="hidden" id="total_tagihan_int" name="total_tagihan_int" class="form-control" value="{{ $data->total_biaya }}" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                @if ($dataretur->tipe_komplain == "Diskon")
                                                <li class="total">
                                                    <h4>Diskon</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="total_refund_dis" name="total_refund_dis" class="form-control" value="{{ formatRupiah2($dataretur->refund) }}" readonly>
                                                            <input type="hidden" id="total_refund" name="refund" class="form-control" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                {{-- @if($data->sisa_bayar == 0) --}}
                                                <li class="total">
                                                    <h4>Total Tagihan Akhir</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="total_tagihan_akhir_dis" name="total_tagihan_akhir_dis" class="form-control" value="{{ formatRupiah2($dataretur->total_akhir) }}" readonly>
                                                            <input type="hidden" id="total_tagihan_akhir" name="total_akhir" class="form-control" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                {{-- @endif --}}
                                                @elseif($dataretur->tipe_komplain == "Refund")
                                                <li class="total">
                                                    <h4>Refund</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="total_refund_dis" name="total_refund_dis" class="form-control" value="{{ formatRupiah2($dataretur->refund) }}" readonly>
                                                            <input type="hidden" id="total_refund" name="refund" class="form-control" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                <li class="total">
                                                    <h4>Sisa Refund</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="sisa_refund_dis" name="sisa_refund_dis" class="form-control" value="{{ formatRupiah2($dataretur->sisa_refund) }}" readonly>
                                                            <input type="hidden" id="sisa_refund" name="sisa_refund" class="form-control" value="{{ $dataretur->sisa_refund }}" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                                @endif
                                                <li>
                                                    @if($dataretur->status_dibukukan == "TUNDA" ||$dataretur->status_dibukukan == null )
                                                    <h4><span class="badges bg-lightred">Komplain Belum Dikonfirmasi Finance</span></h4>
                                                    @endif
                                                </li>
                                                <li>
                                                    <h4>Sisa Tagihan</h4>
                                                    <h5>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp. </span> 
                                                            <input type="text" id="sisa_bayar_dis" name="sisa_bayar_dis" class="form-control" value="{{ formatRupiah2($data->sisa_bayar) }}" readonly>
                                                            <input type="hidden" id="sisa_bayar" name="sisa_bayar" class="form-control" value="{{ $data->sisa_bayar }}" readonly>
                                                        </div>
                                                    </h5>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                {{-- </div> --}}
                            </div>
                        </div>
                        <div class="row justify-content-start">
                            <div class="col-md-6 border rounded pt-3 me-1 mt-2"> 
                                <form action="{{ route('returinden.updatePembuku', $dataretur->id ) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('patch')
                                        <table class="table table-responsive border rounded">
                                            <thead>
                                                <tr>
                                                    <th>Dibuat</th>                                              
                                                    {{-- <th>Diterima</th>                                               --}}
                                                    <th>Dibukukan</th>
                                                    {{-- <th>Diperiksa</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td id="pembuat">
                                                        <input type="hidden" name="pembuat" value="{{ Auth::user()->id ?? '' }}">
                                                        <input type="text" class="form-control" value="{{ $pembuat ?? '' }} ({{ $jabatanbuat ?? '' }})" readonly>
                                                    </td>
                                                    {{-- <td id=penerima">
                                                        <input type="hidden" name=penerima_id" value="{{ Auth::user()->id ?? '' }}">
                                                        <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" placeholder="{{ Auth::user()->karyawans->nama ?? '' }}" disabled>
                                                    </td> --}}
                                                    <td id="pembuku">
                                                        @if(Auth::user()->hasRole('Purchasing'))
                                                            @if (!$pembuku )
                                                            <input type="text" class="form-control" value="Nama (Finance)"  disabled>
                                                            @else
                                                            <input type="text" class="form-control" value="{{ $pembuku }} ({{ $jabatanbuku }})"  disabled>
                                                            @endif
                                                        @endif
        
                                                        @if(Auth::user()->hasRole('Finance'))
                                                            @if($dataretur->status_dibukukan == "DIKONFIRMASI")
    
                                                            <input type="text" class="form-control" value="{{ $pembuku  }} ({{ $jabatanbuku   }})"  disabled>
    
                                                            @else
                                                            <input type="hidden" name="pembuku" value="{{ Auth::user()->id ?? '' }}">
                                                            <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" placeholder="{{ Auth::user()->karyawans->nama ?? '' }}" disabled>
    
                                                            @endif
                                                        @endif
                                                    </td>                                                    {{-- <td id="pemeriksa">
                                                        <input type="hidden" name="pemeriksa_id" value="{{ Auth::user()->id ?? '' }}">
                                                        <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" placeholder="{{ Auth::user()->karyawans->nama ?? '' }}" disabled>
                                                    </td> --}}
                                                </tr>
                                                <tr>
                                                    <td id="status_dibuat">
                                                        <input type="text" class="form-control" value="{{ $dataretur->status_dibuat }}" readonly>

                                                    </td>
                                                    {{-- <td id="status_diterima">
                                                        <select id="status_diterima" name="status_diterima" class="form-control" readonly>
                                                            <option disabled selected>Pilih Status</option>
                                                            <option value="pending" {{ old('status_diterima') == 'pending' ? 'selected' : '' }} disabled>Pending</option>
                                                            <option value="acc" {{ old('status_diterima') == 'acc' ? 'selected' : '' }} disabled>Accept</option>
                                                        </select>
                                                    </td> --}}
                                                    <td id="status_dibukukan">
                                                        @if(Auth::user()->hasRole('Purchasing'))
                                                        <input type="text" class="form-control" id="status_dibukukan" value="{{ $dataretur->status_dibukukan ?? '-' }}" readonly>
                                                        @endif
                                                        @if(Auth::user()->hasRole('Finance'))
    
                                                            @if($dataretur->status_dibukukan == "DIKONFIRMASI")
                                                                <input type="text" class="form-control" id="status_dibukukan" value="{{ $dataretur->status_dibukukan }}" readonly>
    
                                                            @else
                                                            <select id="status_dibukukan" name="status_dibukukan" class="form-control">
                                                                <option disabled>Pilih Status</option>
                                                                <option value="MENUNGGU PEMBAYARAN" {{ $dataretur->status_dibukukan == 'MENUNGGU PEMBAYARAN' ? 'selected' : '' }}>MENUNGGU PEMBAYARAN</option>
                                                                @if ($dataretur->sisa_refund == 0 && $data->sisa_bayar == 0)
                                                                <option value="DIKONFIRMASI" {{ $dataretur->status_dibukukan == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                                @endif
                                                            </select>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    {{-- <td id="status_dibuku">
                                                        <select id="status_diperiksa" name="status_diperiksa" class="form-control" readonly>
                                                            <option disabled selected>Pilih Status</option>
                                                            <option value="pending" {{ old('status_diperiksa') == 'pending' ? 'selected' : '' }} disabled>Pending</option>
                                                            <option value="acc" {{ old('status_diperiksa') == 'acc' ? 'selected' : '' }} disabled>Accept</option>
                                                        </select>
                                                    </td> --}}
                                                </tr>
                                                <tr>
                                                    <td id="tgl_dibuat">
                                                        <input type="text" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{isset($dataretur->tgl_dibuat) ? tanggalindo($dataretur->tgl_dibuat) : '-'}}" readonly>
                                                    </td>
                                                    {{-- <td id="tgl_diterima">
                                                        <input type="date" class="form-control" id="tgl_diterima" name="tgl_diterima_ttd" value="{{ old('tgl_diterima', now()->format('Y-m-d')) }}" readonly>
                                                    </td> --}}
                                                    <td id="tgl_dibukukan">
                                                        @if(Auth::user()->hasRole('Purchasing'))
                                                        <input type="text" class="form-control" id="tgl_dibukukan" name="tgl_dibukukan" value="{{isset($dataretur->tgl_dibukukan) ? tanggalindo($dataretur->tgl_dibukukan) : '-'}}" readonly>
                                                        @endif
                                                        @if(Auth::user()->hasRole('Finance'))
                                                            @if($dataretur->status_dibukukan == "DIKONFIRMASI")
                                                            <input type="text" class="form-control" name="tgl_dibukukan" value="{{tanggalindo($dataretur->tgl_dibukukan) }}" readonly>
                                                            @else
                                                            <input type="date" class="form-control" name="tgl_dibukukan" value="{{ now()->format('Y-m-d') }}" >
                                                            @endif
                                                        @endif                                                   
                                                    </td>
                                                    {{-- <td id="tgl_diperiksa">
                                                        <input type="date" class="form-control" id="tgl_diperiksa" name="tgl_diperiksa" value="{{ old('tgl_diperiksa', now()->format('Y-m-d')) }}" readonly >
                                                    </td> --}}
                                                </tr>
                                            </tbody>
                                        </table>  
                                        <br>                                 
                               </div>
                         </div>

                        <div class="text-end mt-3">
                            {{-- @if($dataretur->sisa_refund == 0 && $data->sisa_bayar == 0) --}}
                            <button class="btn btn-primary" type="submit">Submit</button>
                            {{-- @endif --}}
                            <a href="{{ route('mutasiindengh.index') }}" class="btn btn-secondary" type="button">Back</a>
                        </div>
            </form>
        </div>

    </div>
</div>
</div>



</div>

 <!-- Modal -->
 <div class="modal fade" id="myModalbayar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Pembayaran</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tab Navigation inside Modal -->
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="bayar-tab" data-toggle="tab" href="#bayarContent" role="tab" aria-controls="bayarContent" aria-selected="true">Input Refund</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="refund-tab" data-toggle="tab" href="#refundContent" role="tab" aria-controls="refundContent" aria-selected="false">Bayar Tagihan</a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="myTabContent">
                    <!-- Bayar Tagihan Tab -->
                    <div class="tab-pane fade show active" id="bayarContent" role="tabpanel" aria-labelledby="bayar-tab">
                        <form id="bayarForm" action="{{ route('refundinden.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="nobay" class="form-label">No Bayar</label>
                                <input type="text" class="form-control" id="returinden_id" name="returinden_id" value="{{ $dataretur->id }}" hidden>
                                <input type="text" class="form-control" id="mutasiinden_id" name="mutasiinden_id" value="{{ $dataretur->mutasiinden_id }}" hidden>
                                <input type="text" class="form-control" id="no_invoice_bayar" name="no_invoice_bayar" value="{{ $no_bayar }}" readonly>
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
                                    <input type="text" class="form-control" id="nominal_refund" value="">
                                    <input type="hidden" class="form-control" id="nominal_refund2" name="nominal" value="">
                                </div>
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

                    <!-- Input Refund Tab -->
                    <div class="tab-pane fade" id="refundContent" role="tabpanel" aria-labelledby="refund-tab">
                        <form id="refundForm" action="{{ route('pembayaranmutasi.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="nobay" class="form-label">No bayar</label>
                                {{-- <input type="text" class="form-control" id="returinden_id" name="returinden_id" value="{{ $dataretur->id }}" hidden> --}}
                                <input type="text" class="form-control" id="mutasiinden_id" name="mutasiinden_id" value="{{ $dataretur->mutasiinden_id }}" hidden>
                                <input type="text" class="form-control" id="no_invoice_bayar" name="no_invoice_bayar" value="{{ $no_bypo }}" readonly>
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
                                    <input type="text" class="form-control" id="nominal_bayar" value="">
                                    <input type="hidden" class="form-control" id="nominal_bayar2" name="nominal" value="">
                                </div>
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
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
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
              <input type="hidden" class="form-control" id="edit_type" name="type" value="ReturInden">
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
</div>
@endsection

@section('scripts')
<script>

    function showImageInModal(element) {
        var imgSrc = element.src;
        document.getElementById('modalImage').src = imgSrc;
    }

    $(document).ready(function() {
        if ($('#previewtf').attr('src') === '') {
            $('#previewtf').attr('src', defaultImg);
        }
        if ($('#previewByr').attr('src') === '') {
            $('#previewByr').attr('src', defaultImg);
        }
        if ($('#edit_preview').attr('src') === '') {
            $('#edit_preview').attr('src', defaultImg);
        }

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
        $('#edit_bukti').on('change', function() {
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
    });

    document.getElementById('dynamic_field2').addEventListener('input', function(event) {
    if (event.target.matches('[id^="rawat_retur_dis_"]')) {
        var index = event.target.id.split('_').pop(); // Ambil indeks dari id input
        var rupiah = event.target.value.replace(/[^\d]/g, ''); // Hanya ambil angka

        if (rupiah === "") {
            event.target.value = "";
            document.getElementById(`rawat_retur_${index}`).value = "";
        } else {
            event.target.value = formatRupiah(rupiah);
            // Set nilai ke input hidden
            document.getElementById(`rawat_retur_${index}`).value = unformatRupiah(event.target.value);
        }
        calculateTotal(index); // Hitung ulang total setelah perubahan
    }
    });


    document.addEventListener('DOMContentLoaded', function() {
         // Initialize input field with formatted value
         var nominalInput = document.getElementById('nominal_refund');
         var nominalInput2 = document.getElementById('nominal_refund2');
            var initialNominalValue = '{{ $dataretur->sisa_refund }}';
            nominalInput.value = formatRupiah(initialNominalValue);
            nominalInput2.value = unformatRupiah(initialNominalValue);

            

    document.getElementById('nominal_refund').addEventListener('keyup', function(e) {
        var rupiah = this.value.replace(/[^\d]/g, ''); // hanya ambil angka
        this.value = formatRupiah(rupiah);

        // Set nilai ke input hidden
        document.getElementById('nominal_refund2').value = unformatRupiah(this.value);
    });
    });

    document.addEventListener('DOMContentLoaded', function() {
         // Initialize input field with formatted value
         var nominalInput = document.getElementById('nominal_bayar');
         var nominalInput2 = document.getElementById('nominal_bayar2');
            var initialNominalValue = '{{ $data->sisa_bayar }}';
            nominalInput.value = formatRupiah(initialNominalValue);
            nominalInput2.value = unformatRupiah(initialNominalValue);

            

    document.getElementById('nominal_bayar').addEventListener('keyup', function(e) {
        var rupiah = this.value.replace(/[^\d]/g, ''); // hanya ambil angka
        this.value = formatRupiah(rupiah);

        // Set nilai ke input hidden
        document.getElementById('nominal_bayar2').value = unformatRupiah(this.value);
    });
    });

    function formatRupiah(angka) {
        var reverse = angka.toString().split('').reverse().join('');
        var ribuan = reverse.match(/\d{1,3}/g);
        ribuan = ribuan.join('.').split('').reverse().join('');
        return ribuan;
        console.log()
    }

    function unformatRupiah(formattedValue) {
        return formattedValue.replace(/\./g, '');
        console.log()
    }

    function updateKategori(selectElement, index) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const kategori = selectedOption.getAttribute('data-kategori');
    const qtyDiterima = selectedOption.getAttribute('data-diterima');
    const produkId = selectedOption.getAttribute('data-produk-id');

    document.getElementById(`produk_mutasi_inden_id_${index}`).value = produkId;
    document.getElementById(`kategori_retur_${index}`).value = kategori;
    document.getElementById(`qty_retur_${index}`).value = qtyDiterima;
    document.getElementById(`qty_retur_${index}`).setAttribute('data-produk-id', produkId);
    }

  

    // Hitung Jumlah Retur berdasarkan input rawat_retur_dis
    function calculateJumlahRetur(index) {
        const qty = parseFloat(document.getElementById(`qty_retur_${index}`).value) || 0;
        const rawatRetur = parseFloat(unformatRupiah(document.getElementById(`rawat_retur_dis_${index}`).value)) || 0;
        const jumlahRetur = qty * rawatRetur;

        // Update nilai jumlah retur
        document.getElementById(`jumlah_retur_${index}`).value = jumlahRetur;
        document.getElementById(`jumlah_retur_dis_${index}`).value = formatRupiah(jumlahRetur);

        calculateTotal(); // Hitung total refund dan total tagihan akhir
    }

    // Hitung Total Refund dan Total Tagihan Akhir
    function calculateTotal() {
        let totalRefund = 0;
        document.querySelectorAll('input[name="totalharga[]"]').forEach(function(input) {
            const value = parseFloat(input.value) || 0;
            totalRefund += value;
        });

        const totalTagihan = parseFloat(document.getElementById('total_tagihan_int').value) || 0;
        const totalTagihanAkhir = totalTagihan - totalRefund;

        // Update nilai total refund dan total tagihan akhir
        document.getElementById('total_refund').value = totalRefund;
        document.getElementById('total_refund_dis').value = formatRupiah(totalRefund);
        document.getElementById('total_tagihan_akhir').value = totalTagihanAkhir;
        document.getElementById('total_tagihan_akhir_dis').value = formatRupiah(totalTagihanAkhir); 
    }



  // Tambah baris untuk input kode inden retur
    // document.getElementById('add').addEventListener('click', function() {
    // const counter = document.querySelectorAll('#dynamic_field2 tr').length;
    // const maxOptions = {{ count($barangmutasi) }};

    // if (counter < maxOptions) {
    //     const newRow = document.createElement('tr');
    //     newRow.innerHTML = `
    //         <td>
    //             <select class="form-control" id="kode_inden_retur_${counter}" name="kode_inden_retur[]" onchange="updateKategori(this, ${counter})">
    //                 <option value="" disabled selected>Pilih Kode Inden</option>
    //                 @foreach ($barangmutasi as $item)
    //                 <option value="{{ $item->produk->kode_produk_inden }}" data-kategori="{{ $item->produk->produk->nama }}" data-diterima="{{ $item->jml_diterima }}" data-produk-id="{{ $item->id }}">
    //                     {{ $item->produk->kode_produk_inden }}
    //                 </option>
    //                 @endforeach
    //             </select>
    //         </td>
    //         <input type="hidden" class="form-control" name="produk_mutasi_inden_id[]" id="produk_mutasi_inden_id_${counter}" readonly>
    //         <td><input type="text" class="form-control" name="kategori_retur[]" id="kategori_retur_${counter}" readonly></td>
    //         <td><textarea name="alasan[]" id="alasan_${counter}" class="form-control" cols="30"></textarea></td>
    //         <td><input type="number" class="form-control qty_retur" name="jml_diretur[]" id="qty_retur_${counter}" oninput="calculateJumlahRetur(${counter})"></td>
    //         <td>
    //             <div class="input-group">
    //                 <span class="input-group-text">Rp. </span> 
    //                 <input type="text" name="rawat_retur_dis[]" id="rawat_retur_dis_${counter}" class="form-control-banyak" oninput="calculateJumlahRetur(${counter})">
    //                 <input type="hidden" name="harga_satuan[]" id="rawat_retur_${counter}" class="form-control">
    //             </div>
    //         </td>
    //         <td>
    //             <div class="input-group">
    //                 <span class="input-group-text">Rp. </span> 
    //                 <input type="text" name="jumlah_retur_dis[]" id="jumlah_retur_dis_${counter}" class="form-control-banyak" readonly>
    //                 <input type="hidden" name="totalharga[]" id="jumlah_retur_${counter}" class="form-control">
    //             </div>
    //         </td>
    //         <td><button type="button" name="remove" class="btn btn-danger remove">-</button></td>
    //     `;
    //     document.getElementById('dynamic_field2').appendChild(newRow);
    //     counter++;
    // } else {
    //     alert('Tidak dapat menambah lebih banyak baris.');
    // }
    // });

    // Remove baris untuk input kode inden retur
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove')) {
            const rowsCount = document.querySelectorAll('#dynamic_field2 tr').length;
            if (rowsCount > 1) {
                event.target.closest('tr').remove();
                calculateTotal();
            }
        }
    });

    $(document).ready(function() {
        $(document).on('input', '.qty_retur', function() {
            var qtyRetur = parseInt($(this).val(), 10); // Ambil nilai qty_retur dari input
            var produkId = $(this).data('produk-id'); // Ambil nilai produk-id dari atribut data

            console.log('Produk ID:', produkId);
            console.log('qtyRetur:', qtyRetur);

            // Ambil nilai qtytrm berdasarkan produk-id
            var qtyTrm = parseInt($(`select option[data-produk-id='${produkId}']`).attr('data-diterima'), 10);

            console.log('qtyTrm:', qtyTrm);

            // Validasi jika qty_retur melebihi qtytrm
            if (qtyRetur > qtyTrm) {
                toastr.warning('Jumlah retur tidak boleh lebih besar dari jumlah diterima.', {
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: false,
                    progressBar: true
                });

                // Reset nilai qty_retur ke nilai qtytrm
                $(this).val(qtyTrm);
            }
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
    });
    function editbayar(id, type){
        $.ajax({
            type: "GET",
            url: "/purchase/pembayaran/"+id+"/edit",
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: {
                'jenis': type,
            },
            beforeSend: function() {
                $('#global-loader-transparent').show();
            },
            success: function(response) {
                console.log(response)
                $('#editBayarForm').attr('action', `{{ route("pembayaran_pembelian.update", ":id") }}`.replace(':id', id));
                $('#edit_nobay').val(response.no_invoice_bayar);
                $('#edit_type').val(type);
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