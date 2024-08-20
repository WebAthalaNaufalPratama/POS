
@extends('layouts.app-von')

@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Edit Purchase Order</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('pembelian.index') }}">Purchase Order</a>
                </li>
                <li class="breadcrumb-item active">
                    PO
                </li>
            </ul>
        </div>
    </div>
</div>
<form action="{{ route('pembelian.updatepurchase',['datapo' => $beli->id ]) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    Transaksi Pembelian
                </h4>
                <label style="color: black; font-size: 16px; z-index: 1; position: relative;">
                    <input type="checkbox" id="returCheckbox" {{ $beli->no_retur ? 'checked' : '' }}> Pembelian Retur
                </label>
                <div id="returDropdown" style="display: {{ $beli->no_retur ? 'block' : 'none' }}; margin-top: 10px;">
                    <label for="nomerRetur">Nomor Retur:</label>
                    <input type="text" class="form-control" id="nomerRetur" name="no_retur" style="width: 20%;" value="{{ old('no_retur', $beli->no_retur) }}"  placeholder="Nomor Retur">
                </div>
            </div>

            <div class="card-body">
                {{-- @method('PUT') --}}
                <div class="row">
                    <div class="col-sm">
                        <div class="row justify-content-start">
                            <div class="col-md-12 border rounded pt-3 me-1">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="nopo">No. PO</label>
                                            <input type="text" class="form-control" id="nopo" name="nopo" placeholder="Nomor Purchase Order" value="{{ old('nopo', $beli->no_po) }}" readonly>
                                            <input type="hidden" class="form-control" id="type" name="type" value="pembelian" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="supplier">Supplier</label>
                                            <div class="input-group">
                                                <select id="id_supplier" name="id_supplier" class="form-control">
                                                    <option value="" disabled>Pilih Nama Supplier</option>
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}" {{ $supplier->id == old('id_supplier', $beli->supplier->id) ? 'selected' : '' }}>
                                                            {{ $supplier->nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                                        <img src="/assets/img/icons/plus1.svg" alt="img" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="lokasi">Lokasi</label>
                                            <select id="id_lokasi" name="id_lokasi" class="form-control select2">
                                                <option value="" disabled>Pilih Lokasi</option>
                                                @foreach ($lokasis as $lokasi)
                                                    <option value="{{ $lokasi->id }}" {{ $lokasi->id == old('id_lokasi', $beli->lokasi->id) ? 'selected' : '' }}>
                                                        {{ $lokasi->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select id="status" name="status" class="form-control select2" required>
                                                <option disabled>Pilih Status</option>
                                                <option value="TUNDA" {{ old('status', $beli->status_dibuat) == 'TUNDA' || old('status', $beli->status_dibuat) == '' ? 'selected' : '' }}>TUNDA</option>
                                                <option value="DIKONFIRMASI" {{ old('status', $beli->status_dibuat) == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                <option value="BATAL" {{ old('status', $beli->status_dibuat) == 'BATAL' ? 'selected' : '' }}>BATAL</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="tgl_kirim">Tanggal Kirim</label>
                                            <input type="date" class="form-control" id="tgl_kirim" name="tgl_kirim" value="{{ old('tgl_kirim', $beli->tgl_kirim) }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="no_do">Nomor DO supplier</label>
                                            <input type="text" class="form-control" id="no_do" name="no_do" value="{{ old('no_do', $beli->no_do_suplier) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="custom-file-container" data-upload-id="myFirstImage">
                                            <label>Delivery Order supplier<a href="javascript:void" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"> clear</a></label>
                                            <label class="custom-file-container__custom-file">
                                                <input type="file" id="bukti" class="custom-file-container__custom-file__custom-file-input" name="filedo" accept="image/*">
                                                <span class="custom-file-container__custom-file__custom-file-control"></span>
                                            </label>
                                            <span class="text-danger">max 2mb</span>
                                            <img id="preview" src="{{ old('filedo', ($beli->file_do_suplier ? '/storage/' . $beli->file_do_suplier : '')) }}" alt="your image" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-around">
                            <div class="col-md-12 border rounded pt-3 me-1 mt-2">
                                <div class="form-row row">
                                    <div class="mb-4">
                                        {{-- <h5>List Produk</h5> <button type="button" name="add" id="add" class="btn btn-success">+</button> --}}
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th hidden></th>
                                                        <th>Kode Produk</th>
                                                        <th>Nama Produk</th>
                                                        <th>Jumlah Dikirim</th>
                                                        <th>Jumlah Diterima</th>
                                                        <th>Kondisi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="dynamic_field">
                                                    @foreach ($produkbelis as $index => $item)
                                                        <tr id="row{{ $index }}">
                                                            <td hidden>
                                                                <input type="text" name="id[{{ $index }}]" id="id_{{ $index }}" class="form-control" value="{{ $item->id }}" readonly hidden>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="kode[{{ $index }}]" id="kode_{{ $index }}" class="form-control" value="{{ old('kode.' . $index, $item->produk->kode) }}" readonly>
                                                            </td>
                                                            <td>
                                                                <select id="produk_{{ $index }}" name="produk[{{ $index }}]" class="form-control select2">
                                                                    @foreach ($produks as $produk)
                                                                        <option value="{{ $produk->id }}" data-kode="{{ $produk->kode }}" {{ old('produk.' . $index, $item->produk->id) == $produk->id ? 'selected' : '' }}>
                                                                            {{ $produk->nama }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="number" name="qtykrm[{{ $index }}]" id="qtykrm_{{ $index }}" class="form-control" value="{{ old('qtykrm.' . $index, $item->jml_dikirim) }}">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="qtytrm[{{ $index }}]" id="qtytrm_{{ $index }}" class="form-control" value="{{ old('qtytrm.' . $index, $item->jml_diterima) }}" disabled>
                                                            </td>
                                                            <td>
                                                                <select id="kondisi_{{ $index }}" name="kondisi[{{ $index }}]" class="form-control" disabled>
                                                                    <option value="">Pilih Kondisi</option>
                                                                    @foreach ($kondisis as $kondisi)
                                                                        <option value="{{ $kondisi->id }}" {{ old('kondisi.' . $index, $item->kondisi_id) == $kondisi->id ? 'selected' : '' }}>{{ $kondisi->nama }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            @if($index == 0)
                                                            <td><button type="button" name="add" id="add" class="btn"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></button></td>

                                                            {{-- <td><a href="javascript:void(0);" id="add"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></a></td> --}}
                                                            @else
                                                            {{-- <td><a href="javascript:void(0);"  id="{{ $index }}"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td> --}}

                                                            <td><button type="button" name="remove" id="{{ $index }}" class="btn btn_remove"><img src="/assets/img/icons/delete.svg" alt="svg"></button></td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-start">
                            <div class="col-md-7 border rounded pt-3 me-1 mt-2">
                                <table class="table table-responsive border rounded">
                                    <thead>
                                        <tr>
                                            <th>Dibuat :</th>
                                            <th>Diterima :</th>
                                            <th>Diperiksa :</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="pembuat">
                                                <input type="hidden" name="pembuat" value="{{ Auth::user()->id ?? '' }}">
                                                <input type="text" class="form-control" value="{{ $pembuat }} ({{ $pembuatjbt }})"  disabled>
                                            </td>
                                            <td id="penerima">
                                                @if (!$penerima)
                                                    <input type="text" class="form-control" value="Nama (Admin Galery)" disabled>
                                                @else
                                                    <input type="text" class="form-control" value="{{ $penerima }} ({{ $penerimajbt }})" disabled>
                                                @endif
                                            </td>
                                            <td id="pemeriksa">
                                                @if (!$pemeriksa)
                                                    <input type="text" class="form-control" value="Nama (Auditor)"  disabled>
                                                @else
                                                    <input type="text" class="form-control" value="{{ $pemeriksa }} ({{ $pemeriksajbt }})"  disabled>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td id="status_dibuat">
                                                <input type="text" class="form-control" id="status_buat" value="{{ old('status_dibuat', $beli->status_dibuat) }}" readonly>
                                            </td>
                                            <td id="status_diterima">
                                                <input type="text" class="form-control" id="status_diterima" value="{{ old('status_diterima', $beli->status_diterima) ?? '-' }}" readonly>
                                            </td>
                                            <td id="status_diperiksa">
                                                <input type="text" class="form-control" id="status_diperiksa" value="{{ old('status_diperiksa', $beli->status_diperiksa) ?? '-' }}" readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td id="tgl_pembuat">
                                                <input type="datetime-local" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{ now() }}" readonly>
                                            </td>
                                            <td id="tgl_diterima">
                                                <input type="text" class="form-control" id="tgl_diterima" name="tgl_diterima" value="{{ old('tgl_diterima', $beli->tgl_diterima_ttd) ?? '-' }}" disabled>
                                            </td>
                                            <td id="tgl_pemeriksa">
                                                <input type="text" class="form-control" id="tgl_pemeriksa" name="tgl_diperiksa" value="{{ old('tgl_diperiksa', $beli->tgl_diperiksa) ?? '-' }}" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <a href="{{ route('pembelian.index') }}" class="btn btn-secondary" type="button">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

        </div>

    </div>
</div>
</div>
</div>
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Tambah Supplier</h5>
          <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="supplierForm" action="{{ route('supplier.store') }}" method="POST">
                @csrf
            <div class="mb-3">
              <label for="nama" class="form-label">Nama Supplier</label>
              <input type="text" class="form-control" id="nama" name="nama" required>
            </div>
            <div class="mb-3">
              <label for="pic" class="form-label">PIC</label>
              <input type="text" class="form-control" id="pic" name="pic">
            </div>
            <div class="mb-3">
              <label for="handphone" class="form-label">Handphone</label>
              <input type="text" class="form-control" id="handphone" name="handphone">
            </div>
            <div class="mb-3">
              <label for="alamat" class="form-label">Alamat</label>
              <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label for="tanggal_bergabung" class="form-label">Tanggal bergabung</label>
              <input type="date" class="form-control" id="tanggal_bergabung" name="tanggal_bergabung">
            </div>
            <div class="mb-3">
              <label for="tipe_supplier" class="form-label">Tipe Supplier</label>
              <select class="form-control" id="tipe_supplier" name="tipe_supplier">
                <option value="tradisional">Tradisional</option>
                {{-- <option value="inden">Inden</option> --}}
              </select>
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
@php
    $initialIndex = count($produkbelis);
@endphp

@endsection
  
@section('scripts')
<script>
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    document.addEventListener('DOMContentLoaded', function() {
        const checkbox = document.getElementById('returCheckbox');
        const dropdown = document.getElementById('returDropdown');
        
        checkbox.addEventListener('change', function() {
            if (checkbox.checked) {
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
            }
        });

        // Set initial state based on the checkbox
        if (checkbox.checked) {
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
    });

    function clearFile(){
            $('#bukti').val('');
            $('#preview').attr('src', defaultImg);
        }

    function initSelect2(index) {
        $('#produk_' + index).select2({
            placeholder: "----- Pilih Produk ----",
        });

        // Ketika terjadi perubahan pada dropdown produk
        $('#produk_' + index).on('change', function() {
            // Ambil nilai kode dari atribut data
            var kode_produk = $(this).find(':selected').data('kode');

            // Masukkan nilai kode ke input kode
            $('#kode_' + index).val(kode_produk);

            // Tutup dropdown Select2
            $('#produk_' + index).select2('close');
        });
    }       

    $(document).ready(function() {
    
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

    $('.select2').select2();
    // Inisialisasi Select2 dengan konfigurasi tambahan
    @foreach ($produkbelis as $index => $item)
            initSelect2({{ $index }});
    @endforeach
    
        
    var i = {{ count($produkbelis) }};
        
        $('#add').off('click').on('click', function() {
            var newRow = '<tr id="row'+i+'">'+
                            '<td hidden><input type="text" name="id['+i+']" id="id_'+i+'" class="form-control" readonly hidden></td>'+
                            '<td><input type="text" name="kode['+i+']" id="kode_'+i+'" class="form-control" readonly></td>'+
                            '<td>'+
                                '<select id="produk_'+i+'" name="produk['+i+']" class="form-control select2">'+
                                    '<option value="">Pilih Produk</option>'+
                                    '@foreach ($produks as $produk)'+
                                        '<option value="{{ $produk->id }}" data-kode="{{ $produk->kode }}">{{ $produk->nama }}</option>'+
                                    '@endforeach'+
                                '</select>'+
                            '</td>'+
                            '<td><input type="number" name="qtykrm['+i+']" id="qtykrm_'+i+'" class="form-control"></td>'+
                            '<td><input type="number" name="qtytrm['+i+']" id="qtytrm_'+i+'" class="form-control" disabled></td>'+
                            '<td>'+
                                '<select id="kondisi_'+i+'" name="kondisi['+i+']" class="form-control" disabled required>'+
                                    '<option value="">Pilih Kondisi</option>'+
                                    '@foreach ($kondisis as $kondisi)'+
                                        '<option value="{{ $kondisi->id }}" disabled>{{ $kondisi->nama }}</option>'+
                                    '@endforeach'+
                                '</select>'+
                            '</td>'+
                            '<td><button type="button" name="remove" id="' + i + '" class="btn btn_remove"><img src="/assets/img/icons/delete.svg" alt="svg"></button></td>'+
                        '</tr>';
            $('#dynamic_field').append(newRow);

            // Mengaktifkan select2 untuk dropdown produk
            $('#produk_' + i).select2();

            // Ketika terjadi perubahan pada dropdown produk
            $('#produk_' + i).change(function() {
                // Ambil nilai kode dari atribut data
                var kode_produk = $(this).find(':selected').data('kode');
                
                // Masukkan nilai kode ke input kode yang sesuai
                var id = $(this).attr('id').split('_')[1];
                $('#kode_' + id).val(kode_produk);
            });

            i++;
        });

    $(document).on('click', '.btn_remove', function(){
        console.log('fsa')
        var button_id = $(this).attr("id");
        $('#row'+button_id+'').remove();
    });
    });


</script>
@endsection