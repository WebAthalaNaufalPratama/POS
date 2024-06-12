@extends('layouts.app-von')

@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Edit Purchase Order Inden</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('pembelian.index') }}">Purchase Order</a>
                </li>
                <li class="breadcrumb-item active">
                    Inden
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
        </div>
        <div class="card-body">
            <form action="{{ route('pembelian.update',['datapo' => $beli->id ]) }}" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm">
                        @csrf
                        <div class="row justify-content-start">
                            <div class="col-md-6 border rounded pt-3 me-1">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nopo">No. PO Inden</label>
                                            <input type="hidden" class="form-control" id="type" name="type" value="poinden" readonly>
                                            <input type="text" class="form-control" id="nopo" name="nopo" placeholder="Nomor Purchase Order" value="{{$beli->no_po }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="supplier">Supplier</label>
                                            {{-- <div class="input-group"> --}}
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
                                            {{-- </div> --}}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bulan_inden">Bulan Inden</label>
                                            <input type="text" class="form-control" id="bulan" name="bulan_inden" value="{{ $beli->bulan_inden}}" readonly>

                                            {{-- <select class="form-control" id="bulanTahun" name="bulan_inden">
                                                <!-- Opsi akan diisi oleh JavaScript -->
                                            </select> --}}
                                        </div>
                                        <div class="form-group">
                                            <label for="harga_jual">Status</label>
                                                <input type="text" class="form-control" id="status" name="status" value="Draft" readonly>
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
                                <h5>List Produk  
                                    {{-- <button type="button" name="add" id="add" class="btn btn-success">+</button> --}}
                                </h5>

                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr><th hidden></th>
                                            <th>Kode Inden</th>
                                            <th>Kategori Produk</th>
                                            <th>Kode Produk</th>
                                            <th>Jumlah</th>
                                            <th>Keterangan</th>
                                            <th></th>
                                        
                                        </tr>
                                    </thead>
                                    <tbody id="dynamic_field">
                                        @foreach ($produkbelis as $index => $item)
                                            <tr id="row{{ $index }}">
                                                <td hidden><input type="text" name="id[]" id="id_{{ $index }}" class="form-control" value="{{ $item->id }}" readonly hidden></td>
                                                <td><input type="text" name="kode_inden[]" id="kode_inden_{{ $index }}" class="form-control" value="{{ $item->kode_produk_inden }}"></td>
                                                <td>
                                                    <select id="kategori_{{ $index }}" name="kategori[]" class="form-control kategori-select" style="width: 100%;">
                                                        <option value="">----- Pilih Kategori ----</option>
                                                        @foreach ($produks as $produk)
                                                        <option value="{{ $produk->id }}" data-kode="{{ $produk->kode }}" {{ $item->produk_id == $produk->id ? 'selected' : '' }}>{{ $produk->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="text" name="kode[]" id="kode_{{ $index }}" class="form-control" value="{{ $item->produk->kode }}" readonly></td>
                                                <td><input type="number" name="qty[]" id="qty_{{ $index }}" class="form-control" value="{{ $item->jumlahInden }}"></td>
                                                <td><input type="text" name="ket[]" id="ket_{{ $index }}" class="form-control" value="{{ $item->keterangan }}"></td>
                                                {{-- <td><button type="button" name="remove" id="{{ $index }}" class="btn btn-danger btn_remove">x</button></td> --}}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
                                            <th>Diperiksa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="pembuat">
                                                {{-- <input type="hidden" name="pembuat" value="{{ Auth::user()->id ?? '' }}"> --}}
                                                {{-- <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" disabled> --}}
                                                <input type="text" class="form-control" value="{{ $pembuat  }} ({{ $pembuatjbt  }})"  disabled>
                                            </td>
                                            <td id="pemeriksa">
                                                <input type="hidden" name="pemeriksa" value="{{ Auth::user()->id ?? '' }}">
                                                <input type="text" class="form-control" value="{{ Auth::user()->karyawans->nama ?? '' }} ({{ Auth::user()->karyawans->jabatan ?? '' }})" disabled>
                                            </td>
                                        </tr>
                                        
                                        <tr>
                                            <td id="status_dibuat">
                                                <select id="status_dibuat" name="status_dibuat" class="form-control" readonly>
                                                    <option disabled selected>Pilih Status</option>
                                                    <option value="draft" {{ $beli->status_dibuat == 'draft' ? 'selected' : ''}} disabled>Draft</option>
                                                    <option value="publish" {{ $beli->status_dibuat == 'publish' ? 'selected' : ''}} disabled>Publish</option>
                                                </select>
                                            </td>
                                            <td id="status_diperiksa">
                                                <select id="status_diperiksa" name="status_diperiksa" class="form-control" required>
                                                    <option disabled selected>Pilih Status</option>
                                                    <option value="pending">Pending</option>
                                                    <option value="acc">Accept</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td id="tgl_pembuat">
                                                <input type="datetime-local" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{ $beli->tgl_dibuat ?? ''}}" disabled>
                                            </td>
                                            <td id="tgl_pemeriksa">
                                                <input type="datetime-local" class="form-control" id="tgl_pemeriksa" name="tgl_diperiksa" value="" required>
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
<!-- Modal -->
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
                {{-- <option value="tradisional">Tradisional</option> --}}
                <option value="inden">Inden</option>
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


// $(document).ready(function() {

//     $('.kategori-select').select2({
//         placeholder: "----- Pilih Kategori ----",
//     });

//     // Attach change event to update the corresponding kode input field
//     $('.kategori-select').on('change', function() {
//         var selectedElement = $(this);
//         var kode_produk = selectedElement.find(':selected').data('kode');
//         var index = selectedElement.attr('id').split('_')[1]; // Extract the index from the id

//         // Set the corresponding kode input value
//         $('#kode_' + index).val(kode_produk);

//         // Close the dropdown
//         selectedElement.select2('close');
//     });

//     var i = 1;
//     $('#add').click(function(){
//         var newRow = '<tr id="row'+i+'">'+
//                         '<td><input type="text" name="kode_inden[]" id="kode_inden_'+i+'" class="form-control"></td>'+
//                         '<td>'+
//                             '<select id="kategori_'+i+'" name="kategori[]" class="form-control" onchange="">'+
//                                 '<option value="">Pilih Produk</option>'+
//                                 '@foreach ($produks as $produk)'+
//                                     '<option value="{{ $produk->id }}" data-kode="{{ $produk->kode }}">{{ $produk->nama }}</option>'+
//                                 '@endforeach'+
//                             '</select>'+
//                         '</td>'+
//                         '<td><input type="text" name="kode[]" id="kode_'+i+'" oninput="multiply($(this))" class="form-control" onchange="calculateTotal('+i+')" readonly></td>'+
//                         '<td><input type="number" name="qty[]" id="qty_'+i+'" oninput="multiply($(this))" class="form-control" onchange="calculateTotal('+i+')"></td>'+
//                         '<td><input type="text" name="ket[]" id="ket_'+i+'" class="form-control"></td>'+
//                         '<td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">x</button></td>'+
//                     '</tr>';
//         $('#dynamic_field').append(newRow);

//         $('#kategori_' + i).select2(); // Jika Anda menggunakan plugin Select2
        
//         $('#kategori_' + i).change(function() {
//             // Ambil nilai kode dari atribut data
//             var kode_produk = $(this).find(':selected').data('kode');
            
//             // Masukkan nilai kode ke input kode yang sesuai
//             var id = $(this).attr('id').split('_')[1];
//             $('#kode_' + id).val(kode_produk);
            
//             // Tutup dropdown Select2 secara manual
//             // $(this).trigger('select2:close');
//             $('#kategori_0').select2('close');
//         });

        
//         i++;
//     });

//     $(document).on('click', '.btn_remove', function(){
//         var button_id = $(this).attr("id");
//         $('#row'+button_id+'').remove();
//     });


//     const selectBulanTahun = $('#bulanTahun');
//             const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
//             const currentYear = new Date().getFullYear();
//             const numberOfYears = 5; // Mengisi untuk 5 tahun ke depan

//             for (let year = currentYear; year < currentYear + numberOfYears; year++) {
//                 for (let i = 0; i < bulan.length; i++) {
//                     const option = new Option(`${bulan[i]}-${year}`, `${bulan[i]}-${year}`);
//                     selectBulanTahun.append(option);
//                 }
//             }

//         // Inisialisasi Select2
//         selectBulanTahun.select2({
//             placeholder: 'Pilih Bulan dan Tahun',
//             tags: true // Aktifkan opsi tags untuk mengizinkan penambahan opsi baru
//         });

//         // Set the selected option based on $data->bulan_inden
//         var bulanInden = '{{ $beli->bulan_inden }}';
//         if (bulanInden) {
//             // Check if the option exists
//             if (selectBulanTahun.find(`option[value="${bulanInden}"]`).length) {
//                 selectBulanTahun.val(bulanInden).trigger('change');
//             } else {
//                 // Add new option if it doesn't exist
//                 const newOption = new Option(bulanInden, bulanInden, true, true);
//                 selectBulanTahun.append(newOption).trigger('change');
//             }
//         }
// });

// $(document).ready(function() {
//     // Fungsi untuk menginisialisasi Select2 dan mengatur event handler
//     function initializeSelect2AndEvents(index) {
//         $('#kategori_' + index).select2({
//             placeholder: "----- Pilih Kategori ----",
//         });

//         $('#kategori_' + index).change(function() {
//             // Ambil nilai kode dari atribut data
//             var kode_produk = $(this).find(':selected').data('kode');
//             // Masukkan nilai kode ke input kode yang sesuai
//             $('#kode_' + index).val(kode_produk);
//             // Tutup dropdown Select2 secara manual
//             $('#kategori_' + index).select2('close');
//         });
//     }

//     // Inisialisasi Select2 dan event handler untuk baris yang sudah ada
//     @foreach ($produkbelis as $index => $item)
//         initializeSelect2AndEvents({{ $index }});
//     @endforeach

//     var i = {{ count($produkbelis) }}; // Mulai dari indeks yang sesuai dengan jumlah produkbelis
//     $('#add').click(function() {
//         var newRow = '<tr id="row' + i + '">' +
//                         '<td><input type="text" name="kode_inden[]" id="kode_inden_' + i + '" class="form-control"></td>' +
//                         '<td>' +
//                             '<select id="kategori_' + i + '" name="kategori[]" class="form-control kategori-select" style="width: 100%;">' +
//                                 '<option value="">----- Pilih Kategori ----</option>' +
//                                 '@foreach ($produks as $produk)' +
//                                     '<option value="{{ $produk->id }}" data-kode="{{ $produk->kode }}">{{ $produk->nama }}</option>' +
//                                 '@endforeach' +
//                             '</select>' +
//                         '</td>' +
//                         '<td><input type="text" name="kode[]" id="kode_' + i + '" class="form-control" readonly></td>' +
//                         '<td><input type="number" name="qty[]" id="qty_' + i + '" class="form-control"></td>' +
//                         '<td><input type="text" name="ket[]" id="ket_' + i + '" class="form-control"></td>' +
//                         '<td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">x</button></td>' +
//                     '</tr>';
//         $('#dynamic_field').append(newRow);

//         // Inisialisasi Select2 dan event handler untuk baris yang baru ditambahkan
//         initializeSelect2AndEvents(i);
//         i++;
//     });

//     $(document).on('click', '.btn_remove', function() {
//         var button_id = $(this).attr("id");
//         $('#row' + button_id).remove();
//     });

//     // Inisialisasi Select2 untuk bulanTahun
//     const selectBulanTahun = $('#bulanTahun');
//     const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
//     const currentYear = new Date().getFullYear();
//     const numberOfYears = 5; // Mengisi untuk 5 tahun ke depan

//     for (let year = currentYear; year < currentYear + numberOfYears; year++) {
//         for (let i = 0; i < bulan.length; i++) {
//             const option = new Option(`${bulan[i]}-${year}`, `${bulan[i]}-${year}`);
//             selectBulanTahun.append(option);
//         }
//     }

//     selectBulanTahun.select2({
//         placeholder: 'Pilih Bulan dan Tahun',
//         tags: true // Aktifkan opsi tags untuk mengizinkan penambahan opsi baru
//     });

//     // Set the selected option based on $data->bulan_inden
//     var bulanInden = '{{ $beli->bulan_inden }}';
//     if (bulanInden) {
//         // Check if the option exists
//         if (selectBulanTahun.find(`option[value="${bulanInden}"]`).length) {
//             selectBulanTahun.val(bulanInden).trigger('change');
//         } else {
//             // Add new option if it doesn't exist
//             const newOption = new Option(bulanInden, bulanInden, true, true);
//             selectBulanTahun.append(newOption).trigger('change');
//         }
//     }
// });

$(document).ready(function() {
    $('.select2').select2();
    function initializeSelect2AndEvents(index) {
        $('#kategori_' + index).select2({
            placeholder: "----- Pilih Kategori ----",
        });

        $('#kategori_' + index).change(function() {
            var kode_produk = $(this).find(':selected').data('kode');
            $('#kode_' + index).val(kode_produk);
            $('#kategori_' + index).select2('close');
        });
    }

    @foreach ($produkbelis as $index => $item)
        initializeSelect2AndEvents({{ $index }});
    @endforeach

    var i = {{ count($produkbelis) }};
    $('#add').click(function() {
        var newRow = `<tr id="row${i}">
                       
                        <td><input type="text" name="kode_inden[]" id="kode_inden_${i}" class="form-control">
                             <input type="hidden" name="id[]" id="id_${i}" class="form-control"></td>
                        <td>
                            <select id="kategori_${i}" name="kategori[]" class="form-control kategori-select" style="width: 100%;">
                                <option value="">----- Pilih Kategori ----</option>
                                @foreach ($produks as $produk)
                                <option value="{{ $produk->id }}" data-kode="{{ $produk->kode }}">{{ $produk->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" name="kode[]" id="kode_${i}" class="form-control" readonly></td>
                        <td><input type="number" name="qty[]" id="qty_${i}" class="form-control"></td>
                        <td><input type="text" name="ket[]" id="ket_${i}" class="form-control"></td>
                        <td><button type="button" name="remove" id="${i}" class="btn btn-danger btn_remove">x</button></td>
                    </tr>`;
        $('#dynamic_field').append(newRow);
        initializeSelect2AndEvents(i);
        i++;
    });

    $(document).on('click', '.btn_remove', function() {
        var button_id = $(this).attr("id");
        $('#row' + button_id).remove();
    });

    const selectBulanTahun = $('#bulanTahun');
    const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    const currentYear = new Date().getFullYear();
    const numberOfYears = 5;

    for (let year = currentYear; year < currentYear + numberOfYears; year++) {
        for (let i = 0; i < bulan.length; i++) {
            const option = new Option(`${bulan[i]}-${year}`, `${bulan[i]}-${year}`);
            selectBulanTahun.append(option);
        }
    }

    selectBulanTahun.select2({
        placeholder: 'Pilih Bulan dan Tahun',
        tags: true
    });

    var bulanInden = '{{ $beli->bulan_inden }}';
    if (bulanInden) {
        if (selectBulanTahun.find(`option[value="${bulanInden}"]`).length) {
            selectBulanTahun.val(bulanInden).trigger('change');
        } else {
            const newOption = new Option(bulanInden, bulanInden, true, true);
            selectBulanTahun.append(newOption).trigger('change');
        }
    }
});



</script>
@endsection