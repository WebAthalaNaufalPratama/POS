@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
          <div class="card-header">
              <div class="page-header">
                  <div class="page-title">
                      <h4>Ongkir</h4>
                  </div>
                  <div class="page-btn">
                      <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addongkir" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Ongkir</a>
                  </div>
              </div>
          </div>
          <div class="card-body">
              <div class="table-responsive">
                <div class="row mb-2">
                  <div class="col-12 d-flex justify-content-between align-items-center">
                    <!-- Tombol Filter di Kiri -->
                    <div class="col-auto pe-0">
                      <a href="javascript:void(0);" class="btn btn-primary p-1 d-flex justify-content-center align-items-center" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <img src="{{ asset('assets/img/icons/filter.svg') }}" alt="filter">
                      </a>
                    </div>
                
                    <!-- Tombol PDF & Excel di Kanan -->
                    <div class="col-auto">
                      @if(in_array('ongkir.pdf', $thisUserPermissions))
                      <button class="btn btn-outline-danger" style="height: 2.5rem; padding: 0.5rem 1rem; font-size: 1rem;" onclick="pdf()">
                        <img src="/assets/img/icons/pdf.svg" alt="PDF" style="height: 1rem;" /> PDF
                      </button>
                      @endif
                      @if(in_array('ongkir.excel', $thisUserPermissions))
                      <button class="btn btn-outline-success" style="height: 2.5rem; padding: 0.5rem 1rem; font-size: 1rem;" onclick="excel()">
                        <img src="/assets/img/icons/excel.svg" alt="EXCEL" style="height: 1rem;" /> EXCEL
                      </button>
                      @endif
                    </div>
                  </div>
                </div>
                <table class="table w-100" id="datatable">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Lokasi</th>
                        <th>Biaya</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                        {{-- @foreach ($ongkirs as $ongkir)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $ongkir->nama ??'-' }}</td>
                                <td>{{ $ongkir->lokasi->nama ?? '-' }}</td>
                                <td>{{ $ongkir->biaya ? formatRupiah($ongkir->biaya) : 0 }}</td>
                                <td class="text-center">
                                  <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                      <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                  </a>
                                  <ul class="dropdown-menu">
                                      <li>
                                          <a href="javascript:void(0);" onclick="getData({{ $ongkir->id }})" data-bs-toggle="modal" data-bs-target="#editongkir" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                      </li>
                                      <li>
                                          <a href="#" class="dropdown-item" href="javascript:void(0);" onclick="deleteData({{ $ongkir->id }})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
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

{{-- modal start --}}
<div class="modal fade" id="addongkir" tabindex="-1" aria-labelledby="addongkirlabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addongkirlabel">Tambah Ongkir</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form action="{{ route('ongkir.store') }}" method="POST" id="addForm">
            @csrf
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="add_nama" required>
            </div>
            <div class="mb-3">
              <label for="lokasi_id" class="col-form-label">Lokasi</label>
              <div class="form-group">
                <select class="select2" name="lokasi_id" id="add_lokasi" required>
                  <option value="">Pilih Lokasi</option>
                  @foreach ($lokasis as $lokasi)
                    <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3">
                <label for="biaya" class="col-form-label">Biaya</label>
                <input type="text" class="form-control" name="biaya" id="add_biaya" required>
              </div>
        </div>
        <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
      </div>
    </div>
</div>
<div class="modal fade" id="editongkir" tabindex="-1" aria-labelledby="editongkirlabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editongkirlabel">Edit ongkir</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="editForm" action="ongkir/0/update" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="edit_nama" value="" required>
            </div>
            <div class="mb-3">
                <label for="lokasi_id" class="col-form-label">Lokasi</label>
                <div class="form-group">
                  <select class="select2" name="lokasi_id" id="edit_lokasi" required>
                    <option value="">Pilih Lokasi</option>
                    @foreach ($lokasis as $lokasi)
                      <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            <div class="mb-3">
                <label for="biaya" class="col-form-label">Biaya</label>
                <input type="text" class="form-control" name="biaya" id="edit_biaya" value="" required>
              </div>
        </div>
        <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
      </div>
    </div>
</div>
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModallabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="filterModallabel">Filter Ongkir</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                <img src="assets/img/icons/closes.svg" alt="">
              </button>
          </div>
          <div class="modal-body">
              <!-- Checklist Lokasi -->
              <div class="mb-3" >
                <label for="ongkirChecklist" class="form-label me-3">Pilih Ongkir</label>
                <a href="javascript:void(0);" id="checkAll">
                  <span class="text-primary">Select All</span>
                </a>
                <a href="javascript:void(0);" class="d-none" id="uncheckAll">
                  <span class="text-danger">Deselect All</span>
                </a>
                <div id="ongkirChecklist" class="row" style="max-height: 300px; overflow-y: auto;border: 1px solid #ddd;">
                  @foreach ($ongkirs as $ongkir)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="{{ $ongkir->id }}" id="{{ $ongkir->id }}">
                            <label class="form-check-label" for="{{ $ongkir->id }}">
                                {{ $ongkir->nama }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
              </div>
              
              <!-- Select Lokasi -->
              <div class="mb-3">
                  <label for="filterLokasi" class="form-label">Pilih Lokasi</label>
                  <select class="form-select" id="filterLokasi">
                      <option value="">Semua Lokasi</option>
                      @foreach ($lokasis as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                      @endforeach
                  </select>
              </div>

          </div>
          <div class="modal-footer justify-content-center">
              <button type="button" class="btn btn-secondary" id="clearBtn">Clear</button>
              <button type="button" class="btn btn-primary" id="filterBtn">Filter</button>
          </div>
      </div>
  </div>
</div>
{{-- modal end --}}
@endsection

@section('scripts')
    <script>
    
    $(document).ready(function() {
      $('select').select2()
      $(document).on('input', '#add_biaya, #edit_biaya', function() {
        let input = $(this);
        let value = input.val();
        let cursorPosition = this.selectionStart;
        
        if (!isNumeric(cleanNumber(value))) {
          value = value.replace(/[^\d]/g, "");
        }

        value = cleanNumber(value);
        let formattedValue = formatNumber(value);
        
        input.val(formattedValue);
        this.setSelectionRange(cursorPosition, cursorPosition);
      });

      $('#addForm').on('submit', function(e) {
        let input = $('#add_biaya');
        let value = input.val();
        let cleanedValue = cleanNumber(value);
        input.val(cleanedValue);

        return true;
      });

      $('#editForm').on('submit', function(e) {
        let input = $('#edit_biaya');
        let value = input.val();
        let cleanedValue = cleanNumber(value);
        input.val(cleanedValue);

        return true;
      });

      const columns = [
            { data: 'no', name: 'no', orderable: false },
            { data: 'nama', name: 'nama' },
            { 
                data: 'lokasi_id', 
                name: 'lokasi_id', 
                render: function(data, type, row) {
                    return row.lokasi_value;
                } 
            },
            { 
              data: 'biaya', 
              name: 'biaya', 
              render: function(data, type, row) {
                  return row.biaya_format;
              } 
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    let actionsHtml = `
                        <div class="text-center">
                            <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu">
                    `;

                    if (row.canEdit) {
                        actionsHtml += `
                            <li>
                                <a href="javascript:void(0);" onclick="getData(${row.id})" data-bs-toggle="modal" data-bs-target="#editongkir" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                            </li>
                        `;
                    }

                    if (row.canDelete) {
                        actionsHtml += `
                            <li>
                                <a href="#" class="dropdown-item" href="javascript:void(0);" onclick="deleteData(${row.id})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
                            </li>
                        `;
                    }

                    actionsHtml += `
                            </ul>
                        </div>
                    `;

                    return actionsHtml;
                }
            }
        ]

        let table = initDataTable('#datatable', {
            ajaxUrl: "{{ route('ongkir.index') }}",
            columns: columns,
            order: [[1, 'asc']],
            searching: true,
            lengthChange: true,
            pageLength: 10,
        }, {
            ongkir: '#ongkirChecklist',
            lokasi: '#filterLokasi',
        });

        $('#filterBtn').on('click', function() {
            table.ajax.reload();
            $('#filterModal').modal('hide');
        });

        $('#clearBtn').on('click', function() {
            $('#filterModal input[type="checkbox"]').prop('checked', false);
            table.ajax.reload();
            $('#filterLokasi').val('').trigger('change');
            $('#uncheckAll').addClass('d-none');
            $('#checkAll').removeClass('d-none');
        });

        $('#checkAll').on('click', function() {
            $('#ongkirChecklist input').prop('checked', true);
            $(this).addClass('d-none');
            $('#uncheckAll').removeClass('d-none');
        });
        
        $('#uncheckAll').on('click', function() {
            $('#ongkirChecklist input').prop('checked', false);
            $(this).addClass('d-none');
            $('#checkAll').removeClass('d-none');
        });
    });

    function getData(id){
        $.ajax({
            type: "GET",
            url: "/ongkir/"+id+"/edit",
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            beforeSend: function() {
                $('#global-loader-transparent').show();
            },
            success: function(response) {
                $('#editForm').attr('action', 'ongkir/'+id+'/update');
                $('#edit_nama').val(response.nama)
                $('#edit_lokasi').val(response.lokasi_id).trigger('change')
                $('#edit_biaya').val(formatNumber(response.biaya))
                $('#global-loader-transparent').hide();
            },
            error: function(error) {
                $('#global-loader-transparent').hide();
                toastr.error('Ambil data error', 'Error', {
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: false,
                    progressBar: true
                });
            }
        });
    }

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
              url: "/ongkir/"+id+"/delete",
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
        var filterOngkir = [];
        $('#ongkirChecklist input:checked').each(function() {
            filterOngkir.push($(this).val());
        });
        
        var filterLokasi = $('#filterLokasi').val();

        var desc = 'Cetak laporan tanpa filter';
        if (filterOngkir.length > 0 || filterLokasi) {
            desc = 'Cetak laporan dengan filter';
        }

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
                var url = "{{ route('ongkir.pdf') }}" + '?' + $.param({
                    ongkir: filterOngkir,
                    lokasi: filterLokasi,
                });

                window.open(url);
            }
        });
    }

    function excel() {
        var filterOngkir = [];
        $('#ongkirChecklist input:checked').each(function() {
            filterOngkir.push($(this).val());
        });
        
        var filterLokasi = $('#filterLokasi').val();

        var desc = 'Cetak laporan tanpa filter';
        if (filterOngkir.length > 0 || filterLokasi) {
            desc = 'Cetak laporan dengan filter';
        }

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
                var url = "{{ route('ongkir.excel') }}" + '?' + $.param({
                    ongkir: filterOngkir,
                    lokasi: filterLokasi,
                });

                window.open(url);
            }
        });
    }
    </script>
@endsection