@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Kontrak</h4>
                </div>
                <div class="page-btn">
                    <a href="{{ route('kontrak.create') }}" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Kontrak</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row ps-2 pe-2">
                <div class="col-sm-2 ps-0 pe-0">
                    <select id="filterCustomer" name="filterCustomer" class="form-control" title="Customer">
                        <option value="">Pilih Customer</option>
                        @foreach ($customer as $item)
                            <option value="{{ $item->customer->id }}" {{ $item->customer->id == request()->input('customer') ? 'selected' : '' }}>{{ $item->customer->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 ps-0 pe-0">
                    <select id="filterSales" name="filterSales" class="form-control" title="Sales">
                        <option value="">Pilih Sales</option>
                        @foreach ($sales as $item)
                            <option value="{{ $item->data_sales->id }}" {{ $item->data_sales->id == request()->input('sales') ? 'selected' : '' }}>{{ $item->data_sales->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 ps-0 pe-0">
                    <input type="date" class="form-control" name="filterDateStart" id="filterDateStart" value="{{ request()->input('dateStart') }}" title="Tanggal Awal">
                </div>
                <div class="col-sm-2 ps-0 pe-0">
                    <input type="date" class="form-control" name="filterDateEnd" id="filterDateEnd" value="{{ request()->input('dateEnd') }}" title="Tanggal Akhir">
                </div>
                <div class="col-sm-2">
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('kontrak.index') }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('kontrak.index') }}" class="btn btn-warning">Clear</a>
                </div>
            </div>
            <div class="table-responsive">
            <table class="table datanew" id="dataTable">
                <thead>
                <tr>
                    <th>No</th>
                    <th>No Kontrak</th>
                    <th>Customer</th>
                    <th>PIC</th>
                    <th>Sales</th>
                    <th>Handphone</th>
                    <th>Masa Kontrak</th>
                    <th>Rentang Tanggal</th>
                    <th>Total Biaya</th>
                    <th>Tanggal Dibuat</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($kontraks as $kontrak)
                        <tr>
                            <td>{{ $loop->iteration ?? '-' }}</td>
                            <td>{{ $kontrak->no_kontrak ?? '-' }}</td>
                            <td>{{ $kontrak->customer->nama ?? '-' }}</td>
                            <td>{{ $kontrak->pic ?? '-'  }}</td>
                            <td>{{ $kontrak->data_sales->nama ?? '-'  }}</td>
                            <td>{{ $kontrak->handphone ?? '-' }}</td>
                            <td>{{ $kontrak->masa_sewa ?? '-' }} bulan</td>
                            <td>{{ formatTanggal($kontrak->tanggal_mulai)}} - {{ formatTanggal($kontrak->tanggal_selesai) ?? '-' }}</td>
                            <td>{{ formatRupiah($kontrak->total_harga) ?? '-' }}</td>
                            <td>{{ formatTanggal($kontrak->tanggal_kontrak) ?? '-'  }}</td>
                            <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('kontrak.pdfKontrak', ['kontrak' => $kontrak->id]) }}" target="_blank" class="dropdown-item"><img src="assets/img/icons/pdf.svg" class="me-2" alt="img">Kontrak</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('kontrak.excelPergantian', ['kontrak' => $kontrak->id]) }}" target="_blank" class="dropdown-item"><img src="assets/img/icons/reverse-alt.svg" class="me-2" alt="img">Pergantian</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('do_sewa.create', ['kontrak' => $kontrak->id]) }}" class="dropdown-item"><img src="assets/img/icons/truck.svg" class="me-2" alt="img">Delivery Order</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('kembali_sewa.create', ['kontrak' => $kontrak->id]) }}" class="dropdown-item"><img src="assets/img/icons/return1.svg" class="me-2" alt="img">Kembali Sewa</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('invoice_sewa.create', ['kontrak' => $kontrak->id]) }}" class="dropdown-item"><img src="assets/img/icons/dollar-square.svg" class="me-2" alt="img">Invoice Sewa</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('kontrak.show', ['kontrak' => $kontrak->id]) }}" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('kontrak.edit', ['kontrak' => $kontrak->id]) }}" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item" onclick="deleteData({{ $kontrak->id }})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
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
    $(document).ready(function(){
        $('#filterCustomer, #filterSales').select2();
    });
    $('#filterBtn').click(function(){
        var baseUrl = $(this).data('base-url');
        var urlString = baseUrl;
        var first = true;
        var symbol = '';

        var customer = $('#filterCustomer').val();
        if (customer) {
            var filterCustomer = 'customer=' + customer;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterCustomer;
        }

        var sales = $('#filterSales').val();
        if (sales) {
            var filterSales = 'sales=' + sales;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterSales;
        }

        var dateStart = $('#filterDateStart').val();
        if (dateStart) {
            var filterDateStart = 'dateStart=' + dateStart;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterDateStart;
        }

        var dateEnd = $('#filterDateEnd').val();
        if (dateEnd) {
            var filterDateEnd = 'dateEnd=' + dateEnd;
            if (first == true) {
                symbol = '?';
                first = false;
            } else {
                symbol = '&';
            }
            urlString += symbol;
            urlString += filterDateEnd;
        }
        window.location.href = urlString;
    });
    $('#clearBtn').click(function(){
        var baseUrl = $(this).data('base-url');
        var url = window.location.href;
        if(url.indexOf('?') !== -1){
            window.location.href = baseUrl;
        }
        return 0;
    });
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
                    url: "/kontrak/"+id+"/delete",
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
    </script>
@endsection