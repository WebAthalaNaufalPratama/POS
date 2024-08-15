@extends('layouts.app-von')

@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Mutasi Outlet ke Galery</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.html">Mutasi</a>
                </li>
                <li class="breadcrumb-item active">
                    Outlet Ke Galery
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">
                Transaksi Penjualan
            </h4>
        </div>
        <div class="card-body">
                <div class="row">
                    <div class="col-sm">
                    <form action="{{ route('auditmutasioutlet.update', ['mutasiOG' => $mutasis->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('patch')
                        <div class="row justify-content-around">
                            <div class="col-md-6 border rounded pt-3">
                                <!-- <h5>Informasi Mutasi</h5> -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="pengirim">Nama Pengirim</label>
                                            <select id="pengirim" name="pengirim" class="form-control" required >
                                                <option value="">Pilih Nama Pengirim</option>
                                                @foreach ($lokasis as $lokasi)
                                                <option value="{{ $lokasi->id }}" {{ $lokasi->id == $mutasis->pengirim ? 'selected' : ''}}>{{ $lokasi->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="penerima">Nama Penerima</label>
                                            <select id="penerima" name="penerima" class="form-control" required >
                                                <option value="">Pilih Nama Penerima</option>
                                                @foreach ($lokasis as $lokasi)
                                                <option value="{{ $lokasi->id }}" {{ $lokasi->id == $mutasis->penerima ? 'selected' : ''}}>{{ $lokasi->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="no_mutasi">No Mutasi</label>
                                            <input type="text" id="no_mutasi" name="no_mutasi" class="form-control" value="{{ $mutasis->no_mutasi}}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="rekening_id">Rekening</label>
                                            <select id="rekening_id" name="rekening_id" class="form-control" required>
                                                <option value="">Pilih Rekening</option>
                                                @foreach ($bankpens as $rekening)
                                                <option value="{{ $rekening->id }}" {{ $mutasis->rekening_id == $rekening->id ? 'selected':''}}>{{ $rekening->bank }} -{{ $rekening->nama_akun}}({{$rekening->nomor_rekening}})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6 border rounded pt-3">
                                <!-- <h5>Informasi Invoice</h5> -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="nama">Tanggal Pengiriman</label>
                                            <input type="date" class="form-control" id="tanggal_kirim" name="tanggal_kirim" placeholder="Tanggal_Invoice" value="{{ $mutasis->tanggal_kirim}}" required >
                                        </div>
                                        <div class="form-group">
                                            <label for="nama">Tanggal Diterima</label>
                                            <input type="date" class="form-control" id="tanggal_diterima" name="tanggal_diterima" placeholder="Tanggal_Invoice" value="{{ $mutasis->tanggal_diterima}}" required >
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select id="status" name="status" class="form-control" required >
                                                <option value="">Pilih Status</option>
                                                @php
                                                    $user = Auth::user();
                                                @endphp
                                                @if($user->hasRole(['KasirOutlet']) && $mutasis->status != 'DIKONFIRMASI')
                                                <option value="TUNDA" {{ $mutasis->status == 'TUNDA' ? 'selected' : ''}}>TUNDA</option>
                                                @endif
                                                <option value="DIKONFIRMASI" {{ $mutasis->status == 'DIKONFIRMASI' ? 'selected' : ''}}>DIKONFIRMASI</option>
                                                @if($user->hasRole(['KasirOutlet']) && $mutasis->status != 'DIKONFIRMASI')
                                                <option value="DIBATALKAN" {{ $mutasis->status == 'DIBATALKAN' ? 'selected' : ''}}>DIBATALKAN</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div id="alasan" style="display: none;">
                                                <label for="alasan">Alasan</label>
                                                <textarea name="alasan" id="alasan"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-file-container" data-upload-id="myFirstImage">
                                                <label>Bukti <a href="javascript:void(0)" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"></a>
                                                </label>
                                                <label class="custom-file-container__custom-file">
                                                    <input type="file" id="bukti_file" class="custom-file-container__custom-file__custom-file-input" name="bukti" accept="image/*" >
                                                    <span class="custom-file-container__custom-file__custom-file-control"></span>
                                                </label>
                                                <span class="text-danger">max 2mb</span>
                                                <div class="image-preview">
                                                    <img id="imagePreview" src="{{ $mutasis->bukti ? '/storage/' . $mutasis->bukti : '' }}" />
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
                                                        <th>Nama</th>
                                                        <th>Nama Komponen</th>
                                                        <th>Kondisi Komponen</th>
                                                        <th>Jumlah Komponen</th>
                                                        <th>Jumlah Dikirim</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="dynamic_field">
                                                    @if(count($mutasis->produkMutasiOutlet) > 0)
                                                    @php
                                                    $i = 0;
                                                    @endphp
                                                    @foreach ($mutasis->produkMutasiOutlet as $produk)
                                                    <tr id="row{{ $i }}">
                                                        <td>
                                                        @php
                                                            $isTRDSelected = false;
                                                        @endphp
                                                            <input type="hidden" name="nama_produk[]" id="nama_produk_{{$i}}" class="form-control" value="{{ $produk->id}}">
                                                            <select id="kode_produk_{{ $i }}" name="kode_produk[]" class="form-control" readonly>
                                                                @php
                                                                    $isTRDSelected = false; 
                                                                    $selectedTRDKode = ''; 
                                                                    $selectedGFTKode = ''; 
                                                                @endphp
                                                                @foreach ($produkjuals as $index => $pj)
                                                                @php
                                                                if($pj->produk && $produk->produk->kode){
                                                                    $isSelectedTRD = ($pj->produk->kode == $produk->produk->kode && substr($pj->produk->kode, 0, 3) === 'TRD' && $pj->no_retur ==  $produk->no_retur && $pj->jenis != 'GANTI');
                                                                    $isSelectedGFT = ($pj->produk->kode == $produk->produk->kode && substr($pj->produk->kode, 0, 3) === 'GFT' && $pj->no_retur ==  $produk->no_retur && $pj->jenis != 'GANTI');
                                                                    if($isSelectedTRD) {
                                                                        $isTRDSelected = true;
                                                                        // Reset selected TRD code
                                                                        $selectedTRDKode = '';
                                                                        foreach ($pj->komponen as $komponen) {
                                                                            if ($komponen->kondisi) {
                                                                                foreach($kondisis as $kondisi) {
                                                                                    if($kondisi->id == $komponen->kondisi) {
                                                                                        // Set selected TRD code based on condition
                                                                                        $selectedTRDKode = $kondisi->nama;
                                                                                        $selectedTRDJumlah = $komponen->jumlah;
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                                @endphp
                                                                <!-- @if($pj->produk) -->
                                                                <option value="{{ $produk->id }}" {{ $isSelectedTRD || $isSelectedGFT ? 'selected' : '' }}>
                                                                    @if (isset($pj->produk->kode) && substr($pj->produk->kode, 0, 3) === 'TRD' && $isSelectedTRD)
                                                                        {{ $pj->produk->nama }}
                                                                    @elseif (isset($pj->produk->kode) && substr($pj->produk->kode, 0, 3) === 'GFT' && $isSelectedGFT)
                                                                        {{ $pj->produk->nama }}
                                                                    @endif
                                                                </option>
                                                                <!-- @endif -->
                                                            @endforeach
                                                            </select>
                                                            @if($isTRDSelected)
                                                            <td>Tidak Ada Komponen</td>
                                                            <td>
                                                                <select name="kondisitradproduk_{{ $i }}[]" id="kondisitradproduk_{{ $i }}" data-produk="{{ $selectedTRDKode }}" class="form-control kondisitrad-{{ $i }}" readonly>
                                                                    <option value=""> Pilih Kondisi </option>
                                                                    @foreach ($kondisis as $kondisi)
                                                                    <option value="{{ $kondisi->nama }}" {{ $kondisi->nama == $selectedTRDKode ? 'selected' : ''}}>{{ $kondisi->nama }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="jumlahtradproduk_{{ $i }}[]" id="jumlahtradproduk_{{ $i }}" class="form-control jumlahtrad-{{ $i }}" placeholder="Kondisi Produk" data-produk="{{ $selectedTRDKode }}" value="{{ $selectedTRDJumlah }}" readonly>
                                                            </td>
                                                            @elseif($perPendapatan)
                                                                @foreach ($perPendapatan as $noMOG => $items)
                                                                    @if($noMOG == $produk->id)
                                                                    <td>
                                                                    @foreach ($items as $komponen)
                                                                            <div class="row mt-2">
                                                                                <div class="col">
                                                                                    <input type="hidden" name="kodegiftproduk_{{ $i }}[]" id="kodegiftproduk_{{ $i }}" class="form-control" value="{{ $komponen['kode'] }}" readonly>
                                                                                    <input type="text" name="komponengiftproduk_{{ $i }}[]" id="komponengiftproduk_{{ $i }}" class="form-control komponengift-{{ $i }}" value="{{ $komponen['nama'] }}" readonly>
                                                                                </div>
                                                                            </div>
                                                                    @endforeach
                                                                    </td>
                                                                    <td>
                                                                    @foreach ($items as $komponen)
                                                                            <div class="row mt-2">
                                                                                <div class="col">
                                                                                    <select name="kondisigiftproduk_{{ $i }}[]" id="kondisigiftproduk_{{ $i }}" class="form-control kondisigift-{{ $i }}" readonly>
                                                                                        <option value=""> Pilih Kondisi </option>
                                                                                        @foreach ($kondisis as $kondisi)
                                                                                        <option value="{{ $kondisi->nama }}" {{ $kondisi->nama == $komponen['kondisi'] ? 'selected' : ''}}>{{ $kondisi->nama }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                    @endforeach
                                                                    </td>
                                                                    <td>
                                                                    @foreach ($items as $komponen)
                                                                            <div class="row mt-2">
                                                                                <div class="col">
                                                                                    <input type="number" name="jumlahgiftproduk_{{ $i }}[]" id="jumlahgiftproduk_{{ $i }}" class="form-control jumlahgift-{{ $i }}" data-index="{{ $i }}" value="{{ $komponen['jumlah'] }}" required readonly>
                                                                                </div>
                                                                            </div>
                                                                    @endforeach
                                                                    </td>
                                                                    @endif
                                                                @endforeach
                                                            @endif

                                                        <td><input type="number" name="jumlah_dikirim[]" id="jumlah_dikirim_{{ $i }}" class="form-control" value="{{ $produk->jumlah }}" ></td>
                                                    </tr>
                                                    @php
                                                    $i++;
                                                    @endphp
                                                    @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-around">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2">
                                <div class="row">
                                <div class="col-lg-8 col-sm-12 col-12 border radius mt-1">
                                        <div class="row mt-4">
                                            <div class="col-lg-12">
                                                <table class="table table-responsive border rounded">
                                                    @php
                                                        $user = Auth::user();
                                                    @endphp
                                                    <thead>
                                                        <tr>
                                                            <th>Pembuat</th>
                                                            <th>Penerima</th>
                                                            <th>Penyetuju</th>
                                                            <th>Pemeriksa</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            
                                                            @if($mutasis->status == 'DIKONFIRMASI' && $user->hasRole(['Finance']))
                                                                <td id="pembuat">{{ $mutasis->dibuat->name }}</td>
                                                                <td id="penerima" >{{ $mutasis->diterima->name ?? '-'}}</td>
                                                                <td id="penyetuju" >{{ Auth::user()->name}}</td>
                                                                <td id="pemeriksa" >{{ $mutasis->dibuku->name ?? '-'}}</td>
                                                            @elseif($mutasis->status == 'DIKONFIRMASI' && $user->hasRole(['Auditor']))
                                                                <td id="pembuat">{{ $mutasis->dibuat->name }}</td>
                                                                <td id="penerima" >{{ $mutasis->diterima->name ?? '-'}}</td>
                                                                <td id="penyetuju" >{{ $mutasis->diperiksa->name ?? '-'}}</td>
                                                                <td id="pemeriksa">{{ Auth::user()->name}}</td>
                                                            @elseif($user->hasRole(['KasirOutlet']))
                                                                <td id="pembuat">{{ $mutasis->dibuat ? $mutasis->dibuat->name : '-' }}</td>
                                                                <td id="penerima" >-</td>
                                                                <td id="penyetuju" >-</td>
                                                                <td id="pemeriksa">-</td>
                                                            @endif
                                                        </tr>
                                                        <tr>
                                                            @if($user->hasRole(['KasirOutlet']))
                                                                <td><input type="date" class="form-control" name="tanggal_pembuat"  value="{{ $mutasis->tanggal_pembuat ? $mutasis->tanggal_pembuat : '-' }}"></td>
                                                                <td id="tgl_penerima">-</td>
                                                                <td id="tgl_penyetuju">-</td>
                                                                <td id="tgl_pemeriksa">-</td>
                                                            @elseif($mutasis->status == 'DIKONFIRMASI' && $user->hasRole(['Finance']))
                                                                <td><input type="date" class="form-control" value="{{ $mutasis->tanggal_pembuat }}" readonly></td>
                                                                <td><input type="date" class="form-control" value="{{ $mutasis->tanggal_penerima ?? '-' }}" readonly></td>
                                                                <td><input type="date" class="form-control" name="tanggal_diperiksa" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" ></td>
                                                                <td><input type="date" class="form-control"  value="{{ $mutasis->tanggal_dibukukan ?? '-' }}" readonly></td></td>
                                                            @elseif($mutasis->status == 'DIKONFIRMASI' && $user->hasRole(['Auditor']))
                                                                <td><input type="date" class="form-control"  value="{{ $mutasis->tanggal_pembuat }}" readonly></td>
                                                                <td><input type="date" class="form-control"  value="{{ $mutasis->tanggal_penerima ?? '-' }}" readonly></td>
                                                                <td><input type="date" class="form-control"  value="{{ $mutasis->tanggal_diperiksa ?? '-'}}" readonly></td>
                                                                <td><input type="date" class="form-control" name="tanggal_dibukukan" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"></td>
                                                            @endif
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 float-md-right border radius">
                                        <div class="total-order">
                                            <ul>
                                                <li>
                                                    <h4>Pengiriman
                                                    <select id="pilih_pengiriman" name="pilih_pengiriman" class="form-control" >
                                                        <option value="">Pilih Jenis Pengiriman</option>
                                                        <option value="exspedisi" {{ $mutasis->pilih_pengiriman == 'exspedisi' ? 'selected' : ''}}>Ekspedisi</option>
                                                        <option value="sameday" {{ $mutasis->pilih_pengiriman == 'sameday' ? 'selected' : ''}}>SameDay</option>
                                                    </select>
                                                    </h4>
                                                    <h5>
                                                    <div id="inputOngkir" style="display: none;">
                                                        <!-- <label for="alamat_tujuan">Alamat Tujuan </label> -->
                                                        <input type="text" id="alamat_tujuan" name="alamat_tujuan" class="form-control" >
                                                    </div>
                                                    <div id="inputExspedisi" style="display: none;">
                                                        <!-- <label>Alamat Pengiriman</label> -->
                                                        <select id="ongkir_id" name="ongkir_id" class="form-control" >
                                                            <option value="">Pilih Alamat Tujuan</option>
                                                            @foreach($ongkirs as $ongkir)
                                                            <option value="{{ $ongkir->id }}" data-biaya_pengiriman="{{ $ongkir->biaya}}" {{ $mutasis->ongkir_id == $ongkir->id ? 'selected' : ''}}>{{ $ongkir->nama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div> 
                                                    </h5>
                                                </li>
                                                <li>
                                                    <h4>Biaya Ongkir</h4>
                                                    <h5><input type="text" id="biaya_pengiriman" name="biaya_pengiriman" class="form-control" value="{{ 'Rp '. number_format($mutasis->biaya_pengiriman, 0, ',', '.') }}"  required></h5>
                                                </li>
                                                <li class="total">
                                                    <h4>Total Biaya</h4>
                                                    <h5><input type="text" id="total_biaya" name="total_biaya" class="form-control" value="{{ 'Rp '. number_format($mutasis->total_biaya, 0, ',', '.') }}"  required readonly>
                                                </h5>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <a href="{{ route('mutasioutlet.index') }}" class="btn btn-secondary" type="button">Back</a>
                        </div>
            </form>
        </div>

    </div>
</div>



<div class="modal fade" id="modalGiftCoba" tabindex="-1" aria-labelledby="modalGiftLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalGiftLabel">Atur Komponen Gift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="form_gift" action="{{ route('komponenpenjulan.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="route" value="{{ request()->route()->getName() }},penjualan,{{ request()->route()->parameter('penjualan') }}">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-sm-8">
                                <label for="prdTerjualGift" class="col-form-label">Produk</label>
                                <input type="text" class="form-control" name="produk_id" id="prdTerjualGift"  required>
                            </div>
                            <input type="hidden" name="prdTerjual_id" id="prdTerjualGift_id" value="">
                            <div class="col-sm-4">
                                <label for="jmlGift_produk" class="col-form-label">Jumlah</label>
                                <input type="number" class="form-control" name="jml_produk" id="jmlGift_produk"  required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="jml_komponen" class="col-form-label">Jumlah Bunga/POT</label>
                        <input type="number" class="form-control" name="jml_komponen" id="jml_komponen" required>
                    </div>
                    <div class="mb-3">
                        <label for="komponen_id" class="col-form-label">Bunga/POT</label>
                        <div id="div_komponen" class="form-group">
                            <div id="div_produk_jumlah_0" class="row">
                                <div class="col-sm-4">
                                    <select id="komponen_id_0" name="komponen_id[]" class="form-control" required>
                                        <option value="">Pilih Bunga/POT</option>
                                        @foreach ($produkKomponens as $itemkomponen)
                                        <option value="{{ $itemkomponen->id }}">{{ $itemkomponen->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <select id="kondisi_id_0" name="kondisi_id[]" class="form-control" required>
                                        <option value="">kondisi</option>
                                        @foreach ($kondisis as $kondisi)
                                        <option value="{{ $kondisi->id }}">{{ $kondisi->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <input type="number" class="form-control" name="jumlahproduk_id[]" id="jumlahproduk_id_0">
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
    // Function to update date to today's date
    function updateDate(element) {
        var today = new Date().toISOString().split('T')[0];
        element.value = today;
    }

    // Call the function to set the date to today's date initially
    updateDate(document.getElementById('tanggal_kirim'));
    updateDate(document.getElementById('tanggal_diterima'));
    @foreach($produks as $index => $produk)
    updateDate(document.getElementById('tglrangkai_{{ $index }}'), '{{ $index }}');
    @endforeach
    updateDate(document.getElementById('tanggalbayar'));
</script>
<script>
    function showInputType(index) {
        var selectElement = document.getElementById("jenis_diskon_" + index);
        var selectedValue = selectElement.value;
        // console.log(selectedValue);
        var diskonInput = document.getElementById("diskon_" + index);
        var nominalInput = document.getElementById("nominalInput_" + index);
        var persenInput = document.getElementById("persenInput_" + index);

        if (selectedValue === "Nominal") {
            diskonInput.style.display = "block";
            nominalInput.style.display = "block";
            persenInput.style.display = "none";
        } else if (selectedValue === "persen") {
            diskonInput.style.display = "block";
            nominalInput.style.display = "none";
            persenInput.style.display = "block";
        } else {
            diskonInput.style.display = "none";
            nominalInput.style.display = "none";
            persenInput.style.display = "none";
            diskonInput.value = 0;
        }

        calculateTotal(index);
    }

    function calculateTotal(index) {
        var diskonType = $('#jenis_diskon_' + index).val();
        // console.log(diskonType);

        var diskonValue = parseFloat($('#diskon_' + index).val());
        var jumlah = parseFloat($('#jumlah_' + index).val());
        var hargaSatuan = parseFloat($('#harga_satuan_' + index).val());
        var hargaTotal = 0;
        // console.log(diskonValue);

        if (!isNaN(jumlah) && !isNaN(hargaSatuan)) {
            hargaTotal = jumlah * hargaSatuan;
        }

        if (!isNaN(hargaTotal)) {
            if (diskonType === "Nominal" && !isNaN(diskonValue)) {
                hargaTotal -= diskonValue;
            } else if (diskonType === "persen" && !isNaN(diskonValue)) {
                hargaTotal -= (hargaTotal * diskonValue / 100);
            }
        }

        // Set nilai input harga total
        $('#harga_total_' + index).val(hargaTotal.toFixed(2));

        // Hitung ulang subtotal
        var subtotal = 0;
        $('input[name="harga_total[]"]').each(function() {
            subtotal += parseFloat($(this).val()) || 0;
        });

        // Set nilai input subtotal
        $('#sub_total').val(subtotal.toFixed(2));
    }

    function copyDataToModal(index) {
        var namaProdukValue = $('#nama_produk_' + index).val();
        var jumlahValue = $('#jumlah_' + index).val();
        // console.log(namaProdukValue);

        $('#nama_produk_modal_' + index).val(namaProdukValue);
        $('#jumlah_produk_modal_' + index).val(jumlahValue);
    }
</script>

<script>
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $(document).ready(function() {
        var i = 1;
        $('#add').click(function() {
            var newRow = `<tr class="tr_clone" id="row${i}">
                            <td>
                                <select id="nama_produk_${i}" name="nama_produk[]" class="form-control select2">
                                    <option value="">Pilih Produk</option>
                                    @foreach ($produks as $index => $produk)
                                        <option value="{{ $produk->kode }}" data-harga="{{ $produk->harga_jual }}" data-kode="{{ $produk->kode }}" data-tipe="{{ $produk->tipe }}" data-deskripsi="{{ $produk->deskripsi }}" data-tipe_produk="{{ $produk->tipe_produk }}">
                                            @if (substr($produk->kode, 0, 3) === 'TRD') 
                                                {{ $produk->nama }}
                                                @foreach ($produk->komponen as $komponen)
                                                    @if ($komponen->kondisi)
                                                        @foreach($kondisis as $kondisi)
                                                            @if($kondisi->id == $komponen->kondisi)
                                                                - {{ $kondisi->nama }}
                                                                @php
                                                                    $found = true;
                                                                    break;
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                    @if ($found) @break @endif
                                                @endforeach
                                            @elseif (substr($produk->kode, 0, 3) === 'GFT')
                                                {{ $produk->nama }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="jumlah_dikirim[]" id="jumlah_dikirim_${i}" oninput="multiply($(this))" class="form-control" onchange="calculateTotal(0)"></td>
                            <td><input type="number" name="jumlah_diterima[]" id="jumlah_diterima_${i}" oninput="multiply($(this))" class="form-control" onchange="calculateTotal(0)" readonly></td>
                            <td><button type="button" name="remove" id="${i}" class="btn btn_remove"><img src="/assets/img/icons/delete.svg" alt="svg"></button></td>
                        </tr>`;

            $('#dynamic_field').append(newRow);

            // var picModal = `<div class="modal fade" id="picModal_${i}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            //                     <div class="modal-dialog" role="document">
            //                         <div class="modal-content">
            //                             <div class="modal-header">
            //                                 <h5 class="modal-title" id="exampleModalLabel">Form PIC Perangkai ${i}</h5>
            //                                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            //                                     <span aria-hidden="true">&times;</span>
            //                                 </button>
            //                             </div>
            //                             <div class="modal-body">
            //                                 <div class="form-group">
            //                                     <label for="tglrangkai_${i}">Tanggal Rangkaian</label>
            //                                     <input type="date" class="form-control" id="tglrangkai_${i}" name="tglrangkai_${i}">
            //                                 </div>
            //                                 <div class="form-group">
            //                                     <label for="jnsrangkai_${i}">Jenis Rangkaian</label>
            //                                     <input type="text" class="form-control" id="jnsrangkai_${i}" name="jnsrangkai_${i}" value="penjualan" readonly>
            //                                 </div>
            //                                 <div class="form-group">
            //                                     <label for="no_invoice_rangkai_${i}">Nomor Invoice</label>
            //                                     <input type="text" class="form-control" id="no_invoice_rangkai_${i}" name="no_invoice_rangkai_${i}" placeholder="Nomor Invoice" onchange="generateInvoice(this)" required>
            //                                 </div>
            //                                 <div class="form-group">
            //                                     <label for="jumlahStaff_${i}">Jumlah Staff Perangkai</label>
            //                                     <input type="text" class="form-control" id="jumlahStaff_${i}" name="jumlahStaff_${i}" placeholder="Jumlah Staff Perangkai" onchange="generateStaffInput(this)" required>
            //                                 </div>
            //                                 <div class="form-group">
            //                                     <label for="staffPerangkaiContainer_${i}">Pilih PIC Perangkai</label>
            //                                     <div id="staffPerangkaiContainer_${i}"></div>
            //                                 </div>
            //                                 <div class="table-responsive">
            //                                     <table class="table">
            //                                         <thead>
            //                                             <tr>
            //                                                 <th>Nama</th>
            //                                                 <th>Jumlah</th>
            //                                                 <th></th>
            //                                             </tr>
            //                                         </thead>
            //                                         <tbody id="dynamic_field">
            //                                             <tr>
            //                                                 <td>
            //                                                     <select id="nama_produk" name="nama_produk[]" class="form-control">
            //                                                         <option value="">Pilih Produk</option>`;

            // @foreach($produks as $produk)
            // picModal += `<option value="{{ $produk->id }}" data-harga="{{ $produk->harga_jual }}">{{ $produk->nama }}</option>`;
            // @endforeach

            // picModal += `                    </select>
            //                                                     <input type="hidden" name="kode_produk[]" style="display: none;">
            //                                                     <input type="hidden" name="tipe_produk[]" style="display: none;">
            //                                                     <input type="hidden" name="deskripsi_komponen[]" style="display: none;">
            //                                                 </td>
            //                                                 <td><input type="number" name="jumlah[]" id="jumlah_0" oninput="multiply($(this))" class="form-control"></td>
            //                                             </tr>
            //                                         </tbody>
            //                                     </table>
            //                                 </div>
            //                             </div>
            //                             <div class="modal-footer justify-content-center">
            //                                 <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            //                             </div>
            //                         </div>
            //                     </div>
            //                 </div>`;


            // $('body').append(picModal);


            $('#nama_produk_' + i + ', #jenis_diskon_' + i).select2();
            i++
        });

        $(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
            $('#row' + button_id + '').remove();
            calculateTotal(0);
        });

        $('[id^=kode_produk], [id^=kondisitradproduk], [id^=kondisigiftproduk]').on('mousedown click focus', function(e) {
            e.preventDefault();
        });

        function addModal() {
            let i = $('.modal').length;
        }
        $('#pic_0').on('click', function() {
            addModal();
        });

        var jumlahDO = [];

        
        @foreach ($mutasis->produkMutasiOutlet as $produk)
                jumlahDO.push({{ $produk->jumlah }});
        @endforeach

        // console.log(jumlahDO);

        $('[id^=jumlah_dikirim_]').on('input', function() {
            var jumlahInput = $(this);
            var index = jumlahInput.attr('id').split('_')[2];
            // console.log(jumlahInput.attr('id').split('_')[2]);
            var inputJumlah = $(this).val();
            // console.log(jumlahDO[index]);
            if (parseInt(inputJumlah) > jumlahDO[index]) {

                alert('Jumlah Komplain harus sesuai dengan jumlah DO!');
                $(this).val(jumlahDO[index]);
            } else if (parseInt(inputJumlah) < 0) {
                alert('Jumlah Komplain tidak boleh kurang dari 0');
                $(this).val(0);
            }
        });

        $('#bukti_file').on('change', function() {
            const file = $(this)[0].files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(event.target.files[0]);
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

        function clearFile() {
            $('#bukti_file').val('');
            $('#preview').attr('src', defaultImg);
        };
        
        $('[id^=btnGift]').click(function(e) {
            console.log('coba');
            e.preventDefault();
            var produk_id = $(this).data('produk_gift');
            console.log(produk_id);
            getDataGift(produk_id);
        });

        function getDataGift(produk_id) {
            var data = {
                produk_id: produk_id,
            };
            // console.log(data);
            $.ajax({
                url: '/getProdukTerjual',
                type: 'GET',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    // console.log(response.produk.nama)
                    $('#prdTerjualGift').val(response.produk.nama);
                    $('#prdTerjualGift_id').val(response.id);
                    // console.log(response.id);
                    $('#jmlGift_produk').val(response.jumlah);
                    $('[id^="komponen_id"]').select2()
                    $('[id^="div_produk_jumlah_"]').each(function() {
                        $(this).remove();
                    });
                    
                    $('[id^="jumlahproduk_id_"]').remove();
                    // console.log(response);
                    var pot_bunga = 0
                    if(response.komponen.length > 0){
                        for(var i = 0; i < response.komponen.length; i++){
                            if(response.komponen[i].tipe_produk == 1 || response.komponen[i].tipe_produk == 2){
                                pot_bunga++;
                                    var rowPerangkai =
                                    '<div id="div_produk_jumlah_'+i+'" class="row">' +
                                    '<div class="col-sm-4">' +
                                    '<select id="komponen_id_' + i + '" name="komponen_id[]" class="form-control">' +
                                    '<option value="">Pilih Bunga/POT</option>' +
                                    '@foreach ($produkKomponens as $itemkomponen)' +
                                    '<option value="{{ $itemkomponen->id }}">{{ $itemkomponen->nama }}</option>' +
                                    '@endforeach' +
                                    '</select>' +
                                    '</div>' +
                                    '<div class="col-sm-4">' +
                                    '<select id="kondisi_id_' + i + '" name="kondisi_id[]" class="form-control" required>' +
                                    '<option value="">kondi i</option>' +
                                    '@foreach ($kondisis as $kondisi)' +
                                    '<option value="{{ $kondisi->id }}">{{ $kondisi->nama }}</option>' +
                                    '@endforeach' +
                                    '</select>' +
                                    '</div>' +
                                    '<div class="col-sm-4">' +
                                    '<input type="number" class="form-control" id="jumlahproduk_id_'+ i +'" name="jumlahproduk[]">' +
                                    '</div>' +
                                    '</div>';
                                    $('#div_komponen').append(rowPerangkai);
                                    $('#kondisi_id_' + i).val(response.komponen[i].kondisi);
                                    $('#jumlahproduk_id_' + i).val(response.komponen[i].jumlah);
                                    $('#komponen_id_' + i).val(response.komponen[i].produk.id);
                                    $('#komponen_id_' + i).select2();
                                    $('#kondisi_id_' + i).select2();
                                }
                            }
                    }
                    $('#jml_komponen').val(pot_bunga);
                    $('#modalGiftCoba').modal('show');
                },
                error: function(xhr, status, error) {
                    console.log(error)
                }
            });
        }

        function parseRupiahToNumber(rupiah) {
            return parseInt(rupiah.replace(/[^\d]/g, ''));
        }

        $('form').on('submit', function(e) {
            // Parse semua nilai input yang diformat Rupiah ke angka numerik
            $('#biaya_pengiriman').val(parseRupiahToNumber($('#biaya_pengiriman').val()));
            $('#total_biaya').val(parseRupiahToNumber($('#total_biaya').val()));

        });

        var produkData = [];

        @foreach ($produks as $produk)
            produkData.push({
                id: {{ $produk->id }},
                jumlah: {{ $produk->jumlah }}
            });
        @endforeach
        // console.log('Produk Data:', produkData);


        $(document).on('input', '.jumlah_diterima', function() {
            var inputId = $(this).attr('id');
            var jumlah = parseInt($(this).val(), 10); // Ensure jumlah is parsed as an integer
            var produkId = $(this).data('produk-id'); // Extract the product ID from the data attribute

            var produk = produkData.find(function(item) {
                return item.id == produkId;
            });

            if (jumlah > produk.jumlah) {
                alert('jumlah diterima tidak boleh lebih dari jumlah dikirim');
                $(this).val(produk.jumlah);
            } else if (jumlah < 0) {
                alert('jumlah diterima tidak boleh kurang dari 0');
                $(this).val(0);
            }
        });

        $('#jml_komponen').on('input', function(e) {
            e.preventDefault();
            var jumlah = $(this).val();
            jumlah = parseInt(jumlah) > 10 ? 10 : parseInt(jumlah);
            console.log(jumlah)
            $('[id^="komponen_id_"]').each(function() {
                $(this).select2('destroy');
                $(this).remove();
            });
            $('[id^="kondisi_id_"]').each(function() {
                $(this).select2('destroy');
                $(this).remove();
            });
            $('[id^="jumlahproduk_id_"]').remove();
            if (jumlah < 1) return 0;
            for (var i = 0; i < jumlah; i++) {
                var rowPerangkai =
                    '<div class="row">' +
                    '<div class="col-sm-4">' +
                    '<select id="komponen_id_' + i + '" name="komponen_id[]" class="form-control">' +
                    '<option value="">Pilih Bunga/POT</option>' +
                    '@foreach ($produkKomponens as $itemkomponen)' +
                    '<option value="{{ $itemkomponen->id }}">{{ $itemkomponen->nama }}</option>' +
                    '@endforeach' +
                    '</select>' +
                    '</div>' +
                    '<div class="col-sm-4">' +
                    '<select id="kondisi_id_' + i + '" name="kondisi_id[]" class="form-control" required>' +
                    '<option value="">kondisi</option>' +
                    '@foreach ($kondisis as $kondisi)' +
                    '<option value="{{ $kondisi->id }}">{{ $kondisi->nama }}</option>' +
                    '@endforeach' +
                    '</select>' +
                    '</div>' +
                    '<div class="col-sm-4">' +
                    '<input type="number" class="form-control" id="jumlahproduk_id_'+ i +'" name="jumlahproduk[]">' +
                    '</div>' +
                    '</div>';
                $('#div_komponen').append(rowPerangkai);
                $('#komponen_id_' + i).select2();
                $('#kondisi_id_' + i).select2();
            }
        });

        $('#status').change(function(){
            var status = $(this).val();
            if(status == 'DIBATALKAN')
            {
                $('#alasan').show();
            }else{
                $('#alasan').hide();
            }
        });

        $(document).on('change', '[id^=nama_produk]', function() {
            var id = $(this).attr('id').split('_')[2];
            var selectedOption = $(this).find(':selected');

            var kodeProduk = selectedOption.data('kode');
            var tipeProduk = selectedOption.data('tipe');
            var deskripsiProduk = selectedOption.data('deskripsi');
            // console.log(kodeProduk);
            $('#kode_produk_' + id).val(kodeProduk);
            $('#tipe_produk_' + id).val(tipeProduk);
            $('#deskripsi_komponen_' + id).val(deskripsiProduk);

            // Panggil fungsi updateHargaSatuan
            updateHargaSatuan(this);
        });

        function formatRupiah(angka, prefix) {
            var numberString = angka.toString().replace(/[^,\d]/g, ''),
                split = numberString.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                var separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix === undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
        }

        $('#pilih_pengiriman').change(function() {
            var pengiriman = $(this).val();
            var biayaOngkir = parseFloat($('#biaya_pengiriman').val()) || 0;

            $('#inputOngkir').hide();
            $('#inputExspedisi').hide();

            if (pengiriman === "sameday") {
                $('#inputOngkir').show();
                $('#biaya_pengiriman').prop('readonly', false);
            } else if (pengiriman === "exspedisi") {
                $('#inputExspedisi').show();
                $('#biaya_pengiriman').prop('readonly', true);
                $('#ongkir_id').trigger('change');
            }
        });

         $('#pilih_pengiriman').trigger('change');

         $('#ongkir_id').on('change',function() {
            var selectedOption = $(this).find('option:selected');
            var ongkirValue = parseFloat(selectedOption.data('biaya_pengiriman')) || 0;
            $('#biaya_pengiriman').val(formatRupiah(ongkirValue));
            Totaltagihan();
        });

        if ($('#pilih_pengiriman').val() === "exspedisi") {
            $('#ongkir_id').trigger('change');
        }

        Totaltagihan();

        function Totaltagihan() {
            var biayaOngkir = parseFloat(parseRupiahToNumber($('#biaya_pengiriman').val())) || 0;
            
            var totalTagihan = biayaOngkir;

            $('#total_biaya').val(formatRupiah(totalTagihan));
            $('#biaya_pengiriman').val(formatRupiah(biayaOngkir));
        }

        $('#biaya_pengiriman').on('input', Totaltagihan);

        $('input[id^="jumlah_diterima_"]').on('input', function() {
            var inputDiterima = $(this).val();
            var jumlahkirim = parseFloat($(this).closest('tr').find('input[name^="jumlah_dikirim"]').val());

            if (parseFloat(inputDiterima) > jumlahkirim || inputDiterima < 0) {
                alert('Jumlah Diterima tidak boleh lebih dari Jumlah yang dikirim atau kurang dari 0!');
                $(this).val(jumlahkirim);
            }
        });

        $('#biaya_pengiriman').on('input', Totaltagihan);

    });
</script>

@endsection