
@extends('layouts.app-von')

@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Edit Purchase Order (Auditor)</h3>
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

<div class="row">
    <div class="card">
        <div class="card-header">
                <h4 class="card-title mb-0">
                    Transaksi Pembelian
                </h4>
            </hr>
            @if($beli->no_retur !== null)
            <label>
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
            <form action="{{ route('pembelian.updateaudit',['datapo' => $beli->id ]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                {{-- @method('PUT') --}}
                <div class="row">
                    <div class="col-sm">
                        @csrf
                        <div class="row justify-content-start">
                        <div class="col-md-12 border rounded pt-3 me-1">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="hidden" class="form-control" id="type" name="type" value="pembelian" readonly>
                                        <div class="form-group">
                                            <label for="nopo">No. PO</label>
                                            <input type="text" class="form-control" id="nopo" name="nopo" placeholder="Nomor Purchase Order" value="{{ $beli->no_po}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="supplier">Supplier</label>
                                            <div class="input-group">
                                                <select id="id_supplier" name="id_supplier" class="form-control" readonly>
                                                    <option value="" disabled>Pilih Nama Supplier</option>
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}" {{ $supplier->id == $beli->supplier->id ? 'selected' : '' }} disabled>
                                                            {{ $supplier->nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                
                                                {{-- <div class="input-group-append">
                                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                                        <img src="/assets/img/icons/plus1.svg" alt="img" />
                                                    </button>
                                                </div> --}}
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="lokasi">Lokasi</label>
                                                <select id="id_lokasi" name="id_lokasi" class="form-control" readonly>
                                                    <option value="" disabled>Pilih Lokasi</option>
                                                    @foreach ($lokasis as $lokasi)
                                                    <option value="{{ $lokasi->id }}" {{ $lokasi->id == $beli->lokasi->id ? 'selected' : '' }} disabled>{{ $lokasi->nama }}</option>
                                                    @endforeach
                                                </select>
                                       </div>
                                       <div class="form-group">
                                        <label for="status">Status</label>
                                        @role('Purchasing')
                                        <select id="status" name="status" class="form-control select2" required>
                                            <option value="">Pilih Status</option>
                                            <option value="TUNDA" {{ old('status') == 'TUNDA' || old('status') == '' ? 'selected' : '' }}>TUNDA</option>
                                            <option value="DIKONFIRMASI" {{ old('status') == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                            <option value="BATAL" {{ old('status') == 'BATAL' ? 'selected' : '' }}>BATAL</option>
                                        </select>
                                        @endrole
                                        @role('AdminGallery')
                                        <select id="status" name="status" class="form-control select2" required>
                                            {{-- <option value="">Pilih Status</option> --}}
                                            <option value="DIKONFIRMASI" selected>DIKONFIRMASI</option>
                                        </select>
                                        @endrole
                                        @role('Auditor')
                                        <select id="status" name="status" class="form-control" required>
                                            {{-- <option value="">Pilih Status</option> --}}
                                            <option value="DIKONFIRMASI" selected>DIKONFIRMASI</option>
                                        </select>
                                        @endrole
                                       </div>   
                                    </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="tgl_kirim">Tanggal Kirim</label>
                                                <input type="text" class="form-control" id="tgl_kirim" name="tgl_kirim" value="{{ tanggalindo($beli->tgl_kirim ?? '') }}" disabled>
                                            </div>
                                            <div class="form-group">
                                                <label for="tgl_terima">Tanggal Terima</label>
                                                @if($beli->tgl_diterima !==null)
                                                <input type="text" class="form-control" id="tgl_diterima" value="{{ tanggalindo($beli->tgl_diterima) }}" disabled>
                                                @else
                                                <input type="date" class="form-control" id="tgl_diterima" name="tgl_diterima" value="{{ old('tgl_diterima', now()->format('Y-m-d')) }}">
                                                @endif
                                                </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="no_do">Nomor DO supplier</label>
                                                <input type="text" class="form-control" id="no_do" name="no_do" value="{{ $beli->no_do_suplier ?? ''}}" disabled>
                                            </div>
                                            <div class="form-group">

                                                    <div class="custom-file-container" data-upload-id="myFirstImage">
                                                        <label>Delivery Order supplier
                                                            {{-- <a href="javascript:void" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"> clear</a> --}}
                                                        </label>
                                                        {{-- <label class="custom-file-container__custom-file">
                                                            <input type="file" id="bukti" class="custom-file-container__custom-file__custom-file-input" name="bukti" accept="image/*">
                                                            <span class="custom-file-container__custom-file__custom-file-control"></span>
                                                        </label> --}}
                                                        {{-- <span class="text-danger">max 2mb</span> --}}
                                                        <img id="preview" src="{{ $beli->file_do_suplier ? '/storage/' . $beli->file_do_suplier : '' }}" alt="your image" />
                                                    </div>
                                                    {{-- <img id="previewdo" src="{{ $beli->file_do_suplier ? '/storage/' . $beli->file_do_suplier : '' }}" alt="your image" /> --}}
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
                                        {{-- <button id="ubahButton" class="btn btn-primary">Ubah</button>                                    </div> --}}
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr><th hidden></th>
                                                    <th>Kode Produk</th>
                                                    <th>Nama Produk</th>
                                                    <th>Jumlah Dikirim</th>
                                                    <th>Jumlah Diterima</th>
                                                    <th>Kondisi</th>
                                                    
                                                </tr>
                                            </thead>
                                            
                                            <tbody id="dynamic_field_1">
                                                @php
                                                    $i = 0;
                                                @endphp
                                                @foreach ($produkbelis as $index  => $item)
                                                <tr>
                                                    <td hidden><input type="text" name="id[]" id="id_{{ $i }}" class="form-control" value="{{ $item->id }}" readonly hidden></td>
                                                    <td><input type="text" name="kode[]" id="kode_{{ $i }}" class="form-control" value="{{ $item->produk->kode }}" readonly></td>
                                                    <td><input type="text" name="nama[]" id="nama_{{ $i }}" class="form-control" value="{{ $item->produk->nama }}" readonly></td>
                                                    <td><input type="number" name="qtykrm[]" id="qtykrm_{{ $i }}" class="form-control" value="{{ $item->jml_dikirim }}" readonly></td>
                                                    <td><input type="number" name="qtytrm[]" id="qtytrm_{{ $i }}" class="form-control" value="{{ old('qtytrm.' . $i, $item->jml_diterima ?? '') }}" ></td>
                                                    <td>
                                                        <select id="kondisi_{{ $i }}" name="kondisi[]" class="form-control" onchange="showInputType({{ $i }})">
                                                            <option value="">Pilih Kondisi</option>
                                                            @foreach ($kondisis as $kondisi)
                                                                <option value="{{ $kondisi->id }}" {{ $kondisi->id == $item->kondisi_id ? 'selected' : '' }}>{{ $kondisi->nama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                 
                                                </tr>
                                                @php
                                                    $i++;
                                                @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
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
                                            @if($beli->lokasi->tipe_lokasi == 1)
                                            <th>Diterima :</th>
                                            @endif
                                            <th>Diperiksa :</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="pembuat">
                                                {{-- <input type="hidden" name="pembuat" value="{{ Auth::user()->id ?? '' }}"> --}}
                                                {{-- <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" disabled> --}}
                                                <input type="text" class="form-control" value="{{ $pembuat  }} ({{ $pembuatjbt  }})"  disabled>
                                            </td>
                                            @if($beli->lokasi->tipe_lokasi == 1)
                                            <td id="penerima">
                                                <input type="text" class="form-control" value="{{ $penerima  }} ({{ $penerimajbt  }})"  disabled>

                                                {{-- <input type="hidden" name="penerima" value="{{ Auth::user()->id ?? '' }}">
                                                <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" disabled> --}}
                                            </td>
                                            @endif
                                            {{-- <td id="pemeriksa">
                                                <input type="hidden" name="pemeriksa" value="{{ Auth::user()->id ?? '' }}">
                                                <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" disabled>
                                            </td> --}}
                                            <td id="pemeriksa">
                                                <input type="hidden" name="pemeriksa" value="{{ Auth::user()->id ?? '' }}">
                                                <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" disabled>
{{-- 
                                                @if (!$pemeriksa )
                                                <input type="text" class="form-control" value="Nama (Auditor)"  disabled>
                                                @else
                                                <input type="text" class="form-control" value="{{ $pemeriksa }} ({{ $pemeriksajbt }})"  disabled>
                                                @endif --}}
                                            </td>
                                        </tr>
                                        
                                        <tr>
                                            <td id="status_dibuat">
                                                <input type="text" class="form-control" id="status_buat" value="{{ $beli->status_dibuat }}" readonly>

                                                {{-- <select id="status_dibuat" name="status_dibuat" class="form-control" required readonly>
                                                    <option disabled selected>Pilih Status</option>
                                                    <option value="draft" {{ $beli->status_dibuat == 'draft' ? 'selected' : ''}}>Draft</option>
                                                    <option value="publish" {{ $beli->status_dibuat == 'publish' ? 'selected' : ''}}>Publish</option>
                                                </select> --}}
                                            </td>
                                            @if($beli->lokasi->tipe_lokasi == 1)
                                            <td id="status_diterima">
                                                <input type="text" class="form-control" id="status_diterima" value="{{ $beli->status_diterima }}" readonly>

                                                {{-- <select id="status_diterima" name="status_diterima" class="form-control" required disabled>
                                                    <option disabled selected>Pilih Status</option>
                                                    <option value="pending" {{ $beli->status_diterima == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="acc" {{ $beli->status_diterima == 'acc' ? 'selected' : '' }}>Accept</option>
                                                </select> --}}
                                            </td>
                                            @endif
                                            <td id="status_diperiksa">
                                                <input type="text" class="form-control" id="status_diperiksa" value="TUNDA" readonly>

                                                {{-- <select id="status_diperiksa" name="status_diperiksa" class="form-control" required>
                                                    <option disabled selected>Pilih Status</option>
                                                    <option value="pending" {{ $beli->status_diperiksa == 'pending' ? 'selected' : ''}}>Pending</option>
                                                    <option value="acc" {{ ($beli->status_diperiksa == 'acc') || ($beli->status_diperiksa == null) ? 'selected' : ''}}>Accept</option>
                                                </select> --}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td id="tgl_pembuat">
                                                <input type="datetime-local" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{ $beli->tgl_dibuat ?? '-'}}" readonly >
                                            </td>
                                            @if($beli->lokasi->tipe_lokasi == 1)
                                            <td id="tgl_diterima">
                                                <input type="datetime-local" class="form-control" id="tgl_diterima" name="tgl_diterima_ttd" value="{{ $beli->tgl_diterima_ttd ?? '-'}}" readonly>
                                            </td>
                                            @endif
                                            <td id="tgl_pemeriksa">
                                                <input type="datetime-local" class="form-control" id="tgl_pemeriksa" name="tgl_diperiksa" value="{{ now() }}">
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
@endsection
  
@section('scripts')
<script>
var csrfToken = $('meta[name="csrf-token"]').attr('content');

function clearFile(){
            $('#bukti').val('');
            $('#preview').attr('src', defaultImg);
        }

        
$(document).ready(function() {
    $(document).on('input change', 'input[name="qtytrm[]"]', function () {
        let index = $(this).attr('id').split('_')[1];
        validateQty(index);
    });
    
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
    $('#produk_0').select2({
            placeholder: "----- Pilih Produk ----",
          
        });

        // Ketika terjadi perubahan pada dropdown produk
        $('#produk_0').on('change', function() {
            // Ambil nilai kode dari atribut data
            var kode_produk = $(this).find(':selected').data('kode');

            // Masukkan nilai kode ke input kode
            $('#kode_0').val(kode_produk);

            // Tutup dropdown Select2
            $('#produk_0').select2('close');
        });

    var i = 1;
    $('#add').click(function(){
        var newRow = '<tr id="row'+i+'">'+
                        '<td><input type="text" name="kode[]" id="kode_'+i+'" class="form-control" readonly></td>'+
                        '<td>'+
                            '<select id="produk_'+i+'" name="produk[]" class="form-control">'+
                                '<option value="">Pilih Produk</option>'+
                                '@foreach ($produks as $produk)'+
                                    '<option value="{{ $produk->id }}" data-kode="{{ $produk->kode }}">{{ $produk->nama }}</option>'+
                                '@endforeach'+
                            '</select>'+
                        '</td>'+
                        '<td><input type="number" name="qtykrm[]" id="qtykrm_'+i+'" class="form-control"></td>'+
                        '<td><input type="number" name="qtytrm[]" id="qtytrm_'+i+'" class="form-control"></td>'+
                        '<td>'+
                            '<select id="kondisi_'+i+'" name="kondisi[]" class="form-control" onchange="showInputType('+i+') required">'+
                                '<option value="">Pilih Kondisi</option>'+
                                '@foreach ($kondisis as $kondisi)'+
                                    '<option value="{{ $kondisi->id }}">{{ $kondisi->nama }}</option>'+
                                '@endforeach'+
                            '</select>'+
                        '</td>'+
                        '<td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">x</button></td>'+
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
        var button_id = $(this).attr("id");
        $('#row'+button_id+'').remove();
    });
});

    function validateQty(index) {
        let qtyKrm = parseFloat($(`#qtykrm_${index}`).val());
        let qtyTrm = parseFloat($(`#qtytrm_${index}`).val());

        if (qtyTrm < 0) {
            $(`#qtytrm_${index}`).val(0);
        } else if (qtyTrm > qtyKrm) {
            $(`#qtytrm_${index}`).val(qtyKrm);
        }
    }

</script>
@endsection