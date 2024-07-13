
@extends('layouts.app-von')

@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Show Purchase Order : {{ $beli->no_po }}</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('pembelian.index')}}">Purchase Order Inden</a>
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
                                            <label for="nopo">No. PO Inden</label>
                                            <input type="text" class="form-control" id="nopo" name="nopo" value="{{ $beli->no_po }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="supplier">Supplier</label>
                                            <input type="text" class="form-control" id="supplier" name="supplier" value="{{ $beli->supplier->nama}}" readonly>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="bulan_inden">Bulan Inden</label>
                                            <input type="text" class="form-control" id="bulan_inden" name="bulan_inden"  value="{{ $beli->bulan_inden }}" readonly>
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
                                                    <th>Kode Inden</th>
                                                    <th>Kategori Produk</th>
                                                    <th>Kode Produk</th>
                                                    <th>Jumlah</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody id="dynamic_field">
                                                @foreach ($produkbelis as $item)
                                                <tr>
                                                    <td><input type="text" name="kodeinden[]" id="kodeinden_0" class="form-control" value="{{ $item->kode_produk_inden }}" readonly></td>
                                                    <td><input type="text" name="nama[]" id="nama_0" class="form-control" value="{{ $item->produk->nama }}" readonly></td>
                                                    <td><input type="text" name="kode[]" id="kode_0" class="form-control" value="{{ $item->produk->kode }}" readonly></td>

                                                    {{-- <select id="produk_0" name="produk[]" class="form-control" onchange="showInputType(0)">
                                                        <option value="">----- Pilih Produk ----</option>
                                                        @foreach ($produks as $produk)
                                                        <option value="{{ $produk->id }}" data-kode="{{ $produk->kode }}">{{ $produk->nama }}</option>
                                                        @endforeach
                                                    </select> --}}
                                                    <td><input type="number" name="qtykrm[]" id="qtykrm_0"  class="form-control" value="{{ $item->jumlahInden }}" readonly></td>
                                                    <td><input type="text" name="ket[]" id="ket_0"  class="form-control" value="{{ $item->keterangan }}" readonly></td>

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
                            <div class="col-md-7 border rounded pt-3 me-1 mt-2">
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
                                                <input type="text" class="form-control" value="{{ $pembuat  }} ({{ $pembuatjbt  }})"  disabled>
                                            </td>

                                            <td id="pemeriksa">
                                                <input type="text" class="form-control" value="{{ $pemeriksa }} ({{ $pemeriksajbt }})"  disabled>
                                            </td>
                                        </tr>
                                        
                                        <tr>
                                            <td id="status_dibuat">
                                                <select id="status_dibuat" name="status_dibuat" class="form-control" required disabled>
                                                    <option disabled selected>Pilih Status</option>
                                                    <option value="TUNDA" {{ $beli->status_dibuat == 'TUNDA' ? 'selected' : '' }}>TUNDA</option>
                                                    <option value="DIKONFIRMASI" {{ $beli->status_dibuat == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                </select>
                                            </td>
                                          
                                            <td id="status_diperiksa">
                                                <select id="status_diperiksa" name="status_diperiksa" class="form-control" required disabled>
                                                    <option disabled selected>Pilih Status</option>
                                                    <option value="TUNDA" {{ $beli->status_diperiksa == 'TUNDA' ? 'selected' : '' }}>TUNDA</option>
                                                    <option value="DIKONFIRMASI" {{ $beli->status_diperiksa == 'DIKONFIRMASI' ? 'selected' : '' }}>DIKONFIRMASI</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td id="tgl_pembuat">
                                                <input type="text" class="form-control" id="tgl_dibuat" name="tgl_dibuat" value="{{ $beli->tgl_dibuat }}" disabled>
                                            </td>
                                           
                                            <td id="tgl_pemeriksa">
                                                <input type="text" class="form-control" id="tgl_pemeriksa" name="tgl_diperiksa" value="{{ $beli->tgl_diperiksa ?? '' }}" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br>
                            </div>
                        </div>
                        {{-- <div class="text-end mt-3">
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <a href="" class="btn btn-secondary" type="button">Back</a>
                        </div> --}}
            
        </div>

    </div>
</div>
</div>
</div>
@endsection
<!-- Modal -->


@section('scripts')
    <script>
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

