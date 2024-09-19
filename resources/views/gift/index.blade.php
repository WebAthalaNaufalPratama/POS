@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Produk Gift</h4>
                </div>
                <div class="page-btn">
                    <a href="{{ route('gift.create') }}" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Produk</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <div class="row mb-2">
                    <div class="col-12 d-flex justify-content-between align-items-center">
                        <!-- Tombol Filter di Kiri -->
                        <div class="col-auto pe-0">
                        {{-- <a href="javascript:void(0);" class="btn btn-primary p-1 d-flex justify-content-center align-items-center" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <img src="{{ asset('assets/img/icons/filter.svg') }}" alt="filter">
                        </a> --}}
                        </div>
                    
                        <!-- Tombol PDF & Excel di Kanan -->
                        <div class="col-auto">
                        @if(in_array('gift.pdf', $thisUserPermissions))
                        <button class="btn btn-outline-danger" style="height: 2.5rem; padding: 0.5rem 1rem; font-size: 1rem;" onclick="pdf()">
                            <img src="/assets/img/icons/pdf.svg" alt="PDF" style="height: 1rem;" /> PDF
                        </button>
                        @endif
                        @if(in_array('gift.excel', $thisUserPermissions))
                        <button class="btn btn-outline-success" style="height: 2.5rem; padding: 0.5rem 1rem; font-size: 1rem;" onclick="excel()">
                            <img src="/assets/img/icons/excel.svg" alt="EXCEL" style="height: 1rem;" /> EXCEL
                        </button>
                        @endif
                        </div>
                    </div>
                </div>
                <table class="table datanew">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Tipe Produk</th>
                        <th>Harga</th>
                        <th>Harga Jual</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Komponen</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($gifts as $gift)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $gift->nama }}</td>
                                <td>{{ $gift->tipe->nama }}</td>
                                <td>{{ formatRupiah($gift->harga) }}</td>
                                <td>{{ formatRupiah($gift->harga_jual) }}</td>
                                <td>{{ $gift->deskripsi }}</td>
                                <td>
                                    <table class="table table-bordered">
                                        @foreach ($gift->komponen as $komponen)
                                        <tr>
                                            <td>{{ $komponen->kode_produk }}</td>
                                            <td>{{ $komponen->nama_produk }}</td>
                                            <td>{{ $komponen->jumlah }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td class="text-center">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('gift.edit', ['gift' => $gift->id]) }}" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                        </li>
                                        <li>
                                            <a href="#" class="dropdown-item" onclick="deleteData({{ $gift->id }})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>

    function deleteData(id){
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "GET",
                    url: "/gift/"+id+"/delete",
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        toastr.success(response.msg, 'Success', {
                            closeButton: true,
                            tapToDismiss: false,
                            rtl: false,
                            progressBar: true
                        });
        
                        setTimeout(() => {
                            location.reload()
                        }, 2000);
                    },
                    error: function(error) {
                        toastr.error(JSON.parse(error.responseText).msg, 'Error', {
                            closeButton: true,
                            tapToDismiss: false,
                            rtl: false,
                            progressBar: true
                        });
                    }
                });
            }
        });
    }
    function pdf() {
        // var filterNamaProduk = [];
        // $('#namaProdukChecklist input:checked').each(function() {
        //     filterNamaProduk.push($(this).val());
        // });
        
        // var filterTipeProduk = $('#filterTipeProduk').val();
        // var filterSatuan = $('#filterSatuan').val();

        var desc = 'Cetak laporan tanpa filter';
        // if (filterNamaProduk.length > 0 || filterTipeProduk || filterSatuan) {
        //     desc = 'Cetak laporan dengan filter';
        // }

        Swal.fire({
            title: 'Cetak PDF?',
            text: desc,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Cetak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                var url = "{{ route('gift.pdf') }}" + '?' + $.param({
                    // produk: filterNamaProduk,
                    // tipe_produk: filterTipeProduk,
                    // satuan: filterSatuan,
                });

                window.open(url);
            }
        });
    }
    function excel() {
        // var filterNamaProduk = [];
        // $('#namaProdukChecklist input:checked').each(function() {
        //     filterNamaProduk.push($(this).val());
        // });
        
        // var filterTipeProduk = $('#filterTipeProduk').val();
        // var filterSatuan = $('#filterSatuan').val();

        var desc = 'Cetak laporan tanpa filter';
        // if (filterNamaProduk.length > 0 || filterTipeProduk || filterSatuan) {
        //     desc = 'Cetak laporan dengan filter';
        // }

        Swal.fire({
            title: 'Cetak Excel?',
            text: desc,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Cetak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                var url = "{{ route('gift.excel') }}" + '?' + $.param({
                    // produk: filterNamaProduk,
                    // tipe_produk: filterTipeProduk,
                    // satuan: filterSatuan,
                });

                window.location.href = url;
            }
        });
    }
    </script>
@endsection