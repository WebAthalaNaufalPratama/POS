@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Form Perangkai Penjualan</h4>
                </div>
            </div>
        </div>
        <div class="card-body">
        <div class="row ps-2 pe-2">
                <input type="hidden" class="form-control" name="filterJenisRangkaian" id="filterJenisRangkaian" value="{{ request()->input('jenis_rangkaian') }}">
                <div class="col-sm-2 ps-0 pe-0">
                    <select id="filterPerangkai" name="filterPerangkai" class="form-control" title="Perangkai">
                        <option value="">Pilih Perangkai</option>
                        @foreach ($perangkai as $item)
                            <option value="{{ $item->id }}" {{ $item->id == request()->input('perangkai') ? 'selected' : '' }}>{{ $item->nama }}</option>
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
                    @if(request()->query('jenis_rangkaian') == 'Penjualan')
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('formpenjualan.index', ['jenis_rangkaian' => 'Penjualan']) }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('formpenjualan.index', ['jenis_rangkaian' => 'Penjualan']) }}" class="btn btn-warning">Clear</a>
                    @elseif(request()->query('jenis_rangkaian') == 'MUTASIGO')
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('formpenjualan.index', ['jenis_rangkaian' => 'MUTASIGO']) }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('formpenjualan.index', ['jenis_rangkaian' => 'MUTASIGO']) }}" class="btn btn-warning">Clear</a>
                    @endif
                </div>
            </div>
            <div class="table-responsive">
            <table class="table pb-5" id="formPerangkaiTable">
                <thead>
                <tr>
                    <th>No</th>
                    <th>No From</th>
                    <th>No Invoice</th>
                    <th>Jenis Rangkaian</th>
                    <th>Perangkai</th>
                    <th>Tanggal Dirangkai</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    {{-- @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->no_form ?? '-' }}</td>
                            <td>
                            @if(!empty($item->produk_terjual->no_invoice))
                            {{ $item->produk_terjual->no_invoice ?? '-' }}
                            @elseif(!empty($item->produk_terjual->no_mutasigo))
                            {{ $item->produk_terjual->no_mutasigo ?? '-' }}
                            @endif
                            </td>
                            <td>{{ $item->produk_terjual->produk->nama ?? '-' }}</td>
                            <td>{{ $item->perangkai->nama ?? '-' }}</td>
                            <td>{{ $item->tanggal ?? '-' }}</td>
                            <td>
                                <div class="dropdown">
                                    <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('formpenjualan.show', ['formpenjualan' => $item->id]) }}"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                        @if($jenis == 'Penjualan')
                                            <a href="{{ route('formpenjualan.cetak', ['formpenjualan' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/printer.svg" class="me-2" alt="img">Cetak</a>
                                        @elseif($jenis == 'MUTASIGO')
                                            <a href="{{ route('formmutasigalery.cetak', ['mutasiGO' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/printer.svg" class="me-2" alt="img">Cetak</a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach --}}
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
        $.ajax({
            type: "GET",
            url: "/do_sewa/"+id+"/delete",
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
    </script>

<script>
    $(document).ready(function(){
        $('#filterPerangkai').select2();

        window.routes = {
            formMutasiCetak: "{{ route('formmutasigalery.cetak', ['mutasiGO' => '__ID__']) }}",
            formPenjualanShow: "{{ route('formpenjualan.show', ['formpenjualan' => '__ID__']) }}",
            formPenjualanCetak: "{{ route('formpenjualan.cetak', ['formpenjualan' => '__ID__']) }}"
        };

        $('#formPerangkaiTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("formpenjualan.index") }}',
                type: 'GET',
                data: function (d) {
                    d.jenis_rangkaian = $('#filterJenisRangkaian').val();
                    d.perangkai = $('#filterPerangkai').val();
                    d.dateStart = $('#filterDateStart').val();
                    d.dateEnd = $('#filterDateEnd').val();
                },
                dataSrc: function (json) {
                    console.log("Received Data:", json); // Log received data
                    return json.data;
                }
            },
            columns: [
                { data: null, name: null, searchable: false, orderable: false, render: function (data, type, row, meta) {
                    return meta.row + 1;
                }},
                { data: 'no_form', name: 'no_form' },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'jenis_rangkaian', name: 'jenis_rangkaian' },
                { 
                    data: 'perangkai.nama', 
                    name: 'perangkai.nama', 
                    orderable: false, 
                    render: function(data, type, row) {
                        if (Array.isArray(data)) {
                            return data.join('<br>');
                        }
                        return '';
                    }
                },
                { data: 'tanggal', name: 'tanggal' },
                {
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const userRoles = @json(Auth::user()->roles->pluck('name')->toArray()); // Get user roles
                        const jenisRangkaian = row.jenis_rangkaian; // Get jenis_rangkaian from the row
                        let dropdownHtml = `
                            <div class="dropdown">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <div class="dropdown-menu">`;

                        if (jenisRangkaian === 'Penjualan') {
                            dropdownHtml += `
                                <a class="dropdown-item" href="${window.routes.formPenjualanShow.replace('__ID__', row.id)}">
                                    <img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail
                                </a>
                                <a href="${window.routes.formPenjualanCetak.replace('__ID__', row.id)}" class="dropdown-item">
                                    <img src="assets/img/icons/printer.svg" class="me-2" alt="img">Cetak
                                </a>`;
                        } else if (jenisRangkaian === 'MUTASIGO') {
                            dropdownHtml += `
                                <a class="dropdown-item" href="${window.routes.formMutasiCetak.replace('__ID__', row.id)}">
                                    <img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail
                                </a>
                                <a href="${window.routes.formMutasiCetak.replace('__ID__', row.id)}" class="dropdown-item">
                                    <img src="assets/img/icons/printer.svg" class="me-2" alt="img">Cetak
                                </a>`;
                        }

                        dropdownHtml += `</div></div>`;
                        return dropdownHtml;
                    }
                }
            ]
        });
    });

    $('#filterBtn').click(function(){
        var baseUrl = $(this).data('base-url');
        var urlString = baseUrl;
        var first = true;
        var symbol = '&';

        var perangkai = $('#filterPerangkai').val();
        if (perangkai) {
            var filterPerangkai = 'perangkai=' + perangkai;
            urlString += symbol;
            urlString += filterPerangkai;
        }

        var dateStart = $('#filterDateStart').val();
        if (dateStart) {
            var filterDateStart = 'dateStart=' + dateStart;
            urlString += symbol;
            urlString += filterDateStart;
        }

        var dateEnd = $('#filterDateEnd').val();
        if (dateEnd) {
            var filterDateEnd = 'dateEnd=' + dateEnd;
            urlString += symbol;
            urlString += filterDateEnd;
        }
        window.location.href = urlString;
    });
    $('#clearBtn').click(function(){
        var baseUrl = $(this).data('base-url');
        var url = window.location.href;
        if(url.indexOf('?') !== 0){
            window.location.href = baseUrl;
        }
        return 0;
    });
    function deleteData(id){
        $.ajax({
            type: "GET",
            url: "/do_sewa/"+id+"/delete",
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
    </script>
@endsection