@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Form Perangkai</h4>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row ps-2 pe-2">
                <div class="col-sm-2 ps-0 pe-0">
                    <select id="filterPerangkai" name="filterPerangkai" class="form-control" title="Perangkai">
                        <option value="">Pilih Perangkai</option>
                        @foreach ($perangkai as $item)
                            <option value="{{ $item->perangkai->id }}" {{ $item->perangkai->id == request()->input('perangkai') ? 'selected' : '' }}>{{ $item->perangkai->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 ps-0 pe-0">
                    <input type="text" class="form-control" name="filterDateStart" id="filterDateStart" value="{{ request()->input('dateStart') }}" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Tanggal Awal" title="Tanggal Awal">
                </div>
                <div class="col-sm-2 ps-0 pe-0">
                    <input type="text" class="form-control" name="filterDateEnd" id="filterDateEnd" value="{{ request()->input('dateEnd') }}" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Tanggal Akhir" title="Tanggal Akhir">
                </div>
                <div class="col-sm-2">
                    <a href="javascript:void(0);" id="filterBtn" data-base-url="{{ route('form.index', ['jenis_rangkaian' => 'Sewa']) }}" class="btn btn-info">Filter</a>
                    <a href="javascript:void(0);" id="clearBtn" data-base-url="{{ route('form.index', ['jenis_rangkaian' => 'Sewa']) }}" class="btn btn-warning">Clear</a>
                </div>
            </div>
            <div class="table-responsive">
            <table class="table" id="dataTable" style="width: 100%">
                <thead>
                <tr>
                    <th>No</th>
                    <th>No From</th>
                    <th>No Kontrak</th>
                    <th>Produk</th>
                    <th>Perangkai</th>
                    <th>Tanggal Dirangkai</th>
                    <th class="text-center">Aksi</th>
                </tr>
                </thead>
                <tbody>
                    {{-- @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->no_form ?? '-' }}</td>
                            <td>{{ $item->produk_terjual->no_sewa ?? '-' }}</td>
                            <td>{{ $item->produk_terjual->produk->nama ?? '-' }}</td>
                            <td>{{ $item->perangkai->nama ?? '-' }}</td>
                            <td>{{ formatTanggal($item->tanggal) ?? '-' }}</td>
                            <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('form.cetak', ['form' => $item->id]) }}" target="_blank" class="dropdown-item"><img src="assets/img/icons/download.svg" class="me-2" alt="img">Cetak</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('form.show', ['form' => $item->id]) }}" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                                    </li>
                                </ul>
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
    $(document).ready(function(){
        $('#filterPerangkai').select2();

        // Start Datatable
        const columns = [
            { data: 'no', name: 'no', orderable: false },
            { data: 'no_form', name: 'no_form' },
            { data: 'no_kontrak', name: 'no_kontrak', orderable: false },
            { data: 'nama_produk', name: 'nama_produk', orderable: false },
            { data: 'nama_perangkai', name: 'nama_perangkai', orderable: false },
            { 
                data: 'tanggal', 
                name: 'tanggal', 
                render: function(data, type, row) {
                    return row.tanggal_format;
                } 
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `
                    <div class="text-center">
                        <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="form/${row.id}/cetak" target="_blank" class="dropdown-item"><img src="assets/img/icons/download.svg" class="me-2" alt="img">Cetak</a>
                            </li>
                            <li>
                                <a href="form/${row.id}/show" class="dropdown-item"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img">Detail</a>
                            </li>
                        </ul>
                    </div>
                    `;
                }
            }
        ];

        let table = initDataTable('#dataTable', {
            ajaxUrl: "{{ route('form.index') }}",
            columns: columns,
            order: [[1, 'asc']],
            searching: true,
            lengthChange: true,
            pageLength: 10
        }, {
            perangkai: '#filterPerangkai',
            dateStart: '#filterDateStart',
            dateEnd: '#filterDateEnd'
        });

        $('#filterBtn').on('click', function() {
            table.ajax.reload();
        });

        $('#clearBtn').on('click', function() {
            $('#filterPerangkai').val('').trigger('change');
            $('#filterDateStart').val('');
            $('#filterDateEnd').val('');
            table.ajax.reload();
        });
        // End Datatable
    });
    function deleteData(id){
        $.ajax({
            type: "GET",
            url: "/do_sewa/"+id+"/delete",
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
    </script>
@endsection