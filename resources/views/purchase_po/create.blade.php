@extends('layouts.app-von')

@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Purchase Order</h3>
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
<form action="{{ route('pembelianpo.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
<div class="row">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">
                Transaksi Pembelian
            </h4>
        </hr>
        <label style="color: black; font-size: 16px; z-index: 1; position: relative;">
            <input type="checkbox" id="returCheckbox"> Pembelian Retur
        </label>
        
            <div id="returDropdown" style="display:none; margin-top: 10px;">
                <label for="nomerRetur">Nomor Retur:</label>
                <input type="text" class="form-control" id="nomerRetur" name="no_retur" style="width: 30%;" value="{{ old('no_retur') }}" placeholder="Nomor Retur">
            </div>
            
        </div>
         <div class="card-body">
                <div class="row">
                    <div class="col-sm">
                        <div class="row justify-content-start">
                        <div class="col-md-12 border rounded pt-3 me-1">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="nopo">No. PO</label>
                                            <input type="text" class="form-control" id="nopo" name="nopo" placeholder="Nomor Purchase Order" value="{{ old('nopo', $nomor_po) }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="supplier">Supplier</label>
                                            <div class="input-group">
                                                <select id="id_supplier" name="id_supplier" class="form-control" required>
                                                    <option value="">Pilih Nama Supplier</option>
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}" {{ old('id_supplier') == $supplier->id ? 'selected' : '' }}>
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
                                                <select id="id_lokasi" name="id_lokasi" class="form-control select2" required>
                                                    <option value="">Pilih Lokasi</option>
                                                    @foreach ($lokasis as $lokasi)
                                                    <option value="{{ $lokasi->id }}" {{ old('id_lokasi') == $lokasi->id ? 'selected' : '' }}>{{ $lokasi->nama }}</option>
                                                    @endforeach
                                                </select>
                                       </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select id="status" name="status" class="form-control select2" required>
                                                <option disabled>Pilih Status</option>
                                                <option value="TUNDA" {{ old('status') == 'TUNDA'  ? 'selected' : '' }}>TUNDA</option>
                                                <option value="DIKONFIRMASI" {{ old('status') == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                {{-- <option value="BATAL" {{ old('status') == 'BATAL' ? 'selected' : '' }}>BATAL</option> --}}
                                            </select>
                                                {{-- <input type="text" class="form-control" id="status" name="status" value="Draft" readonly> --}}
                                        </div>
                                    </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="tgl_kirim">Tanggal Kirim</label>
                                                <input type="date" class="form-control" id="tgl_kirim" name="tgl_kirim" value="{{ old('tgl_kirim', now()->format('Y-m-d')) }}" >
                                            </div>
                                            {{-- <div class="form-group">
                                                <label for="tgl_terima">Tanggal Terima</label>
                                                    <input type="date" class="form-control" id="tgl_diterima" name="tgl_diterima" readonly>
                                            </div> --}}
                                            <div class="form-group">
                                                <label for="no_do">Nomor DO supplier</label>
                                                <input type="text" class="form-control" id="no_do" name="no_do" value="{{ old('no_do')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            
                                            <div class="form-group">

                                                <div class="custom-file-container" data-upload-id="myFirstImage">
                                                    <label>Delivery Order supplier<a href="javascript:void" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"> clear</a>
                                                    </label>
                                                    <label class="custom-file-container__custom-file">
                                                        <input type="file" id="bukti" class="custom-file-container__custom-file__custom-file-input" name="filedo" accept="image/*">
                                                        <span class="custom-file-container__custom-file__custom-file-control"></span>
                                                    </label>
                                                    <span class="text-danger">max 2mb</span>
                                                    <img id="preview" src="" alt="your image" />
                                                </div>
                                                {{-- <img id="previewdo" src="{{ $beli->file_do_suplier ? '/storage/' . $beli->file_do_suplier : '' }}" alt="your image" /> --}}
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
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="dynamic_field">
                                                <tr>
                                                    <td>
                                                        <input type="text" name="kode[]" id="kode_0" class="form-control" value="{{ old('kode.0') }}" readonly>
                                                    </td>
                                                    <td>
                                                        <select id="produk_0" name="produk[]" class="form-control" onchange="showInputType(0)">
                                                            <option value="">----- Pilih Produk ----</option>
                                                            @foreach ($produks as $produk)
                                                                <option value="{{ $produk->id }}" data-kode="{{ $produk->kode }}" {{ old('produk.0') == $produk->id ? 'selected' : '' }}>
                                                                    {{ $produk->nama }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="qtykrm[]" id="qtykrm_0" class="form-control" onchange="calculateTotal(0)" value="{{ old('qtykrm.0') }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="qtytrm[]" id="qtytrm_0" class="form-control" onchange="calculateTotal(0)" value="{{ old('qtytrm.0') }}" readonly>
                                                    </td>
                                                    <td>
                                                        <select id="kondisi_0" name="kondisi[]" class="form-control" onchange="showInputType(0)" readonly>
                                                            <option value="" disabled>Pilih Kondisi</option>
                                                            @foreach ($kondisis as $kondisi)
                                                                <option value="{{ $kondisi->id }}" {{ old('kondisi.0') == $kondisi->id ? 'selected' : '' }} disabled>
                                                                    {{ $kondisi->nama }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    
                                                        <td><a href="javascript:void(0);" id="add"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></a></td>
                                                    
                                                </tr>
                                            </tbody>
                                            
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>

                        <div class="row justify-content-end">
                            <div class="col-md-3 col-12 border rounded pt-3 me-1 mt-2">
                                <div class="table-responsive">
                                    <table class="table border rounded">
                                        <thead>
                                            <tr>
                                                <th>Dibuat Oleh :</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td id="pembuat">
                                                    <input type="hidden" name="pembuat" value="{{ Auth::user()->id ?? '' }}">
                                                    <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" disabled>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td id="tgl_pembuat">
                                                    <input type="datetime-local" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{ now() }}">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <br>
                            </div>
                        </div>
                        
                        <div class="text-end mt-3">
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <a href="{{ route('pembelian.index') }}" class="btn btn-secondary" type="button">Back</a>
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
              <input type="date" class="form-control" id="tanggal_bergabung" name="tanggal_bergabung" value="{{ now()->format('Y-m-d') }}">
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

    document.getElementById('returCheckbox').addEventListener('change', function() {
        var returDropdown = document.getElementById('returDropdown');
        if (this.checked) {
            returDropdown.style.display = 'block';
        } else {
            returDropdown.style.display = 'none';
        }
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
                                '<option value="">---- Pilih Produk ----</option>'+
                                '@foreach ($produks as $produk)'+
                                    '<option value="{{ $produk->id }}" data-kode="{{ $produk->kode }}">{{ $produk->nama }}</option>'+
                                '@endforeach'+
                            '</select>'+
                        '</td>'+
                        '<td><input type="number" name="qtykrm[]" id="qtykrm_'+i+'" oninput="multiply($(this))" class="form-control" onchange="calculateTotal('+i+')"></td>'+
                        '<td><input type="number" name="qtytrm[]" id="qtytrm_'+i+'" oninput="multiply($(this))" class="form-control" onchange="calculateTotal('+i+')" readonly></td>'+
                        '<td>'+
                            '<select id="kondisi_'+i+'" name="kondisi[]" class="form-control" onchange="showInputType('+i+') required" readonly>'+
                                '<option value="" disabled>Pilih Kondisi</option>'+
                                '@foreach ($kondisis as $kondisi)'+
                                    '<option value="{{ $kondisi->id }}" disabled>{{ $kondisi->nama }}</option>'+
                                '@endforeach'+
                            '</select>'+
                        '</td>'+
                        '<td><a href="javascript:void(0);" name="remove" class="btn_remove" id="'+ i +'"><img src="/assets/img/icons/delete.svg" alt="svg"></a></td>';
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


</script>
@endsection