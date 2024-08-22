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
            <h3 class="page-title">Show Purchase Order : {{ $beli->no_po }}</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('pembelian.index')}}">Purchase Order</a>
                </li>
                <li class="breadcrumb-item active">
                    PO
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">
                Transaksi Pembelian
            </h4>
        </hr>
        @if($beli->no_retur !== null)
        <label style="color: black; font-size: 16px; z-index: 1; position: relative;">

            <input type="checkbox" id="returCheckbox" @if($beli->no_retur !== null)  checked @endif disabled> Pembelian Retur
        </label>
    </br>
        <div>
            <label for="nomerRetur">Nomor Retur:</label>
            <input type="text" class="form-control" id="nomerRetur" name="no_retur" value="{{ $beli->no_retur}}" style="width: 20%;" disabled>
        </div>
        @endif
    </div>
        <div class="card-body">
           
                <div class="row">
                    <div class="col-sm">
                        @csrf
                        <div class="row justify-content-start">
                        <div class="col-md-12 border rounded pt-3 me-1">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="nopo">No. PO</label>
                                            <input type="text" class="form-control" id="nopo" name="nopo" value="{{ $beli->no_po }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="supplier">Supplier</label>
                                            <input type="text" class="form-control" id="supplier" name="supplier" value="{{ $beli->supplier->nama}}" readonly>
                                            {{-- <div class="input-group">
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
                                            </div> --}}
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="lokasi">Lokasi</label>
                                            <input type="text" class="form-control" id="lokasi" name="lokasi"  value="{{ $beli->lokasi->nama }}" readonly>
                                                {{-- <select id="id_lokasi" name="id_lokasi" class="form-control" required>
                                                    <option value="">Pilih Lokasi</option>
                                                    @foreach ($lokasis as $lokasi)
                                                    <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                                                    @endforeach
                                                </select> --}}
                                       </div>
                                       <div class="form-group">

                                        <label for="harga_jual">Status</label>
                                        @role('Purchasing')
                                        <input type="text" class="form-control" id="status" name="status" value="{{$beli->status_dibuat}}" readonly>
                                        @endrole
                                        @role('AdminGallery')
                                        <input type="text" class="form-control" id="status" name="status" value="{{$beli->status_diterima}}" readonly>
                                        @endrole
                                        @role('Auditor')
                                        <input type="text" class="form-control" id="status" name="status" value="{{$beli->status_diperiksa}}" readonly>
                                        @endrole
                                        @role('Finance')
                                        <input type="text" class="form-control" id="status" name="status" value="@php
                                            $latestDate = max($beli->tgl_dibuat, $beli->tgl_diterima, $beli->tgl_diperiksa);
                                            if ($latestDate == $beli->tgl_dibuat) {
                                                echo $beli->status_dibuat. ' oleh ' . $pembuat;
                                            } elseif ($latestDate == $beli->tgl_diterima) {
                                                $beli->status_diterima . ' oleh ' . $penerima; 
                                            } else {
                                                echo $beli->status_diperiksa. ' oleh ' . $pemeriksa;
                                            }
                                        @endphp" readonly>
                                        @endrole
                                    </div>
                                    
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="tgl_kirim">Tanggal Kirim</label>
                                                <input type="text" class="form-control" id="tgl_kirim" name="tgl_kirim" value="{{ $beli->tgl_kirim ? tanggalindo($beli->tgl_kirim) : '' }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="tgl_terima">Tanggal Terima</label>
                                                    <input type="text" class="form-control" id="tgl_terima" name="tgl_terima" value="{{ $beli->tgl_diterima ? tanggalindo($beli->tgl_diterima) : '-' }}" readonly>
                                                </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="no_do">Nomor DO supplier</label>
                                                <input type="text" class="form-control" id="no_do" name="no_do" value="{{ $beli->no_do_suplier ?? '-' }}" readonly>
                                            </div>
                                            <div class="form-group">
                                             
                                                <form action="{{ route('gambarpo.update', ['datapo' => $beli->id]) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('patch')
                                                    <div class="custom-file-container" data-upload-id="myFirstImage">
                                                        <label>Delivery Order supplier <a href="javascript:void(0)" id="clearFileDO" class="custom-file-container__image-clear" onclick="clearFileDO()" title="Clear Image">clear</a></label>
                                                        <label class="custom-file-container__custom-file">
                                                            <input type="file" id="bukti_do" class="custom-file-container__custom-file__custom-file-input" name="file" accept="image/*" required>
                                                            <span class="custom-file-container__custom-file__custom-file-control"></span>
                                                        </label>
                                                        <span class="text-danger">max 2mb</span>
                                                        <img id="previewdo" src="{{ $beli->file_do_suplier ? '/storage/' . $beli->file_do_suplier : '' }}" alt="your image" class="img-thumbnail" data-bs-toggle="modal" data-bs-target="#imageModal" onclick="showImageInModal(this)" />
                                                    </div>
                                                    <div class="text-end mt-3">
                                                        <button class="btn btn-primary" type="submit">Upload File</button>
                                                        {{-- <a href="{{ route('pembelian.index') }}" class="btn btn-secondary" type="button">Back</a> --}}
                                                    </div>
                                                </form>
                                                {{-- <input type="file" class="form-control" id="filedo" name="filedo"> --}}
                                                
                                               
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
                                                    <th>Kode Produk</th>
                                                    <th>Nama Produk</th>
                                                    <th>Jumlah Dikirim</th>
                                                    <th>Jumlah Diterima</th>
                                                    <th>Kondisi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="dynamic_field">
                                                @foreach ($produkbelis as $item)
                                                <tr>
                                                    <td><input type="text" name="kode[]" id="kode_0" class="form-control" value="{{ $item->produk->kode }}" readonly></td>
                                                    <td><input type="text" name="nama[]" id="nama_0" class="form-control" value="{{ $item->produk->nama }}" readonly></td>

                                                    {{-- <select id="produk_0" name="produk[]" class="form-control" onchange="showInputType(0)">
                                                        <option value="">----- Pilih Produk ----</option>
                                                        @foreach ($produks as $produk)
                                                        <option value="{{ $produk->id }}" data-kode="{{ $produk->kode }}">{{ $produk->nama }}</option>
                                                        @endforeach
                                                    </select> --}}
                                                    <td><input type="number" name="qtykrm[]" id="qtykrm_0" oninput="multiply($(this))" class="form-control" onchange="calculateTotal(0)" value="{{ $item->jml_dikirim }}" readonly></td>
                                                    <td><input type="number" name="qtytrm[]" id="qtytrm_0" oninput="multiply($(this))" class="form-control" onchange="calculateTotal(0)" value="{{ $item->jml_diterima ?? '' }}" readonly></td>
                                                    <td><input type="text" name="kondisi[]" id="kondisi_0" class="form-control" value="{{ $item->kondisi->nama ?? ''}}" readonly></td>

                                                        {{-- <select id="kondisi_0" name="kondisi[]" class="form-control" onchange="showInputType(0)">
                                                            <option value="">Pilih Kondisi</option>
                                                            @foreach ($kondisis as $kondisi)
                                                            <option value="{{ $kondisi->id }}">{{ $kondisi->nama }}</option>
                                                            @endforeach
                                                        </select> --}}
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-start">
                            <div class="col-md-8 border rounded pt-3 me-1 mt-2">
                                <table class="table table-responsive border rounded">
                                    <thead>
                                        <tr>
                                            <th>Dibuat</th>
                                            {{-- @if(in_array($beli->lokasi->tipe->id, [1, 2])) --}}
                                            <th>Diterima</th>
                                            {{-- @endif --}}
                                            <th>Diperiksa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="pembuat">
                                                <input type="text" class="form-control" value="{{ $pembuat  }} ({{ $pembuatjbt  }})"  disabled>
                                            </td>
                                            {{-- @if(in_array($beli->lokasi->tipe->id, [1, 2])) --}}
                                            <td id="penerima">
                                                @if (!$penerima )
                                                <input type="text" class="form-control" value="Nama (Admin Galery)" disabled>
                                                @else
                                                <input type="text" class="form-control" value="{{ $penerima }} ({{ $penerimajbt }})" disabled>
                                                @endif
                                            </td>
                                            {{-- @endif --}}
                                            <td id="pemeriksa">
                                                @if (!$pemeriksa )
                                                <input type="text" class="form-control" value="Nama (Auditor)"  disabled>
                                                @else
                                                <input type="text" class="form-control" value="{{ $pemeriksa }} ({{ $pemeriksajbt }})"  disabled>
                                                @endif
                                            </td>
                                        </tr>
                                        
                                        <tr>
                                            <td id="status_dibuat">
                                                <input type="text" class="form-control" id="status_buat" value="{{ $beli->status_dibuat }}" readonly>
                                                {{-- <select id="status_dibuat" name="status_dibuat" class="form-control" required disabled>
                                                    <option disabled selected>Pilih Status</option>
                                                    <option value="draft" {{ $beli->status_dibuat == 'draft' ? 'selected' : '' }}>Draft</option>
                                                    <option value="publish" {{ $beli->status_dibuat == 'publish' ? 'selected' : '' }}>Publish</option>
                                                </select> --}}
                                            </td>
                                            {{-- @if(in_array($beli->lokasi->tipe->id, [1, 2])) --}}

                                            <td id="status_diterima">
                                                <input type="text" class="form-control" id="status_diterima" value="{{ $beli->status_diterima ?? '-' }}" readonly>

                                                {{-- <select id="status_diterima" name="status_diterima" class="form-control" required disabled>
                                                    <option disabled selected>Pilih Status</option>
                                                    <option value="pending" {{ $beli->status_diterima == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="acc" {{ $beli->status_diterima == 'acc' ? 'selected' : '' }}>Accept</option>
                                                </select> --}}
                                            </td>
                                            {{-- @endif --}}
                                            <td id="status_diperiksa">
                                                <input type="text" class="form-control" id="status_diperiksa" value="{{ $beli->status_diperiksa ?? '-' }}" readonly>

                                                {{-- <select id="status_diperiksa" name="status_diperiksa" class="form-control" required disabled>
                                                    <option disabled selected>Pilih Status</option>
                                                    <option value="pending" {{ $beli->status_diperiksa == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="acc" {{ $beli->status_diperiksa == 'acc' ? 'selected' : '' }}>Accept</option>
                                                </select> --}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td id="tgl_pembuat">
                                                <input type="text" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{ tanggallengkap($beli->tgl_dibuat) }}" disabled>
                                            </td>
                                            {{-- @if(in_array($beli->lokasi->tipe->id, [1, 2])) --}}
                                            <td id="tgl_diterima">
                                                <input type="text" class="form-control" id="tgl_diterima" name="tgl_diterima" value="{{ $beli->tgl_diterima_ttd ? tanggallengkap($beli->tgl_diterima_ttd) :  '-'}}" disabled>
                                            </td>
                                            {{-- @endif --}}
                                            <td id="tgl_pemeriksa">
                                                <input type="text" class="form-control" id="tgl_pemeriksa" name="tgl_diperiksa" value="{{ $beli->tgl_diperiksa ? tanggallengkap($beli->tgl_diperiksa ): '-' }}" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            {{-- <button class="btn btn-primary" type="submit">Submit</button> --}}
                            <a href="{{ route('pembelian.index') }}" class="btn btn-secondary" type="button">Back</a>
                        </div>
            
        </div>

    </div>
</div>
</div>
</div>
@endsection
<!-- Modal -->


@section('scripts')
    <script>


    function showImageInModal(element) {
        var imgSrc = element.src;
        document.getElementById('modalImage').src = imgSrc;
    }


        // var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function(){
            if ($('#previewdo').attr('src') === '') {
                $('#previewdo').attr('src', defaultImg);
            }

            $('#bukti_do').on('change', function() {
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
                        $('#previewdo').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Definisikan fungsi clearFile di sini
        });

        function clearFileDO(){
            $('#bukti_do').val('');
            $('#previewdo').attr('src', defaultImg);
        }
    </script>
@endsection

