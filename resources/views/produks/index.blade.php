@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Produk</h4>
                </div>
                <div class="page-btn">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addproduk" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Produk</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
              <div class="row mb-2">
                <div class="col-auto m-0 pe-0">
                  <a href="javascript:void(0);" class="btn btn-primary p-1 d-flex justify-content-center items-align-center" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <img src="{{ asset('assets/img/icons/filter.svg') }}" alt="filter">
                  </a>
                </div>
                <div class="col-auto m-0">
                  <a href="javascript:void(0);" id="clearBtn" class="btn btn-warning">Clear</a>
                </div>
              </div>
              <table class="table w-100" id="datatable">
                  <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>Satuan</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                      {{-- @foreach ($produks as $produk)
                          <tr>
                              <td>{{ $loop->iteration }}</td>
                              <td>{{ $produk->kode }}</td>
                              <td>{{ $produk->nama }}</td>
                              <td>{{ $produk->tipe->nama ?? '-' }}</td>
                              <td>{{ $produk->satuan }}</td>
                              <td>{{ $produk->deskripsi }}</td>
                              <td class="text-center">
                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="javascript:void(0);" onclick="getData({{ $produk->id }})" data-bs-toggle="modal" data-bs-target="#editproduk" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item" href="javascript:void(0);" onclick="deleteData({{ $produk->id }})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
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
<div class="modal fade" id="addproduk" tabindex="-1" aria-labelledby="addproduklabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addproduklabel">Tambah Produk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            <img src="assets/img/icons/closes.svg" alt="">
          </button>
        </div>
        <div class="modal-body">
          <form action="{{ route('produks.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="add_nama" required>
            </div>
            <div class="mb-3">
              <label for="tipe_produk" class="col-form-label">Tipe Produk</label>
              <div class="form-group">
                <select class="select2" name="tipe_produk" id="add_tipe_produk" required>
                  <option value="">Pilih Tipe</option>
                  @foreach ($tipe_produks as $tipe_produk)
                    <option value="{{ $tipe_produk->id }}">{{ $tipe_produk->nama }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="satuan" class="col-form-label">Satuan</label>
              <input type="text" class="form-control" name="satuan" id="add_satuan" required>
            </div>
            <div class="mb-3">
              <label for="deskripsi" class="col-form-label">Deskripsi</label>
              <textarea class="form-control" name="deskripsi" id="add_deskripsi" required></textarea>
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
<div class="modal fade" id="editproduk" tabindex="-1" aria-labelledby="editproduklabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editproduklabel">Edit Produk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            <img src="assets/img/icons/closes.svg" alt="">
          </button>
        </div>
        <div class="modal-body">
          <form id="editForm" action="produks/0/update" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="edit_nama" value="" required>
            </div>
            <div class="mb-3">
              <label for="tipe_produk" class="col-form-label">Tipe Produk</label>
              <div class="form-group">
                <select class="select2" name="tipe_produk" id="edit_tipe_produk" value="" required>
                  <option value="">Pilih Tipe</option>
                  @foreach ($tipe_produks as $tipe_produk)
                    <option value="{{ $tipe_produk->id }}">{{ $tipe_produk->nama }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="satuan" class="col-form-label">Satuan</label>
              <input type="text" class="form-control" name="satuan" id="edit_satuan" required>
            </div>
            <div class="mb-3">
              <label for="deskripsi" class="col-form-label">Deskripsi</label>
              <textarea class="form-control" name="deskripsi" id="edit_deskripsi" value="" required></textarea>
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
              <h5 class="modal-title" id="filterModallabel">Filter Produk</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                <img src="assets/img/icons/closes.svg" alt="">
              </button>
          </div>
          <div class="modal-body">
              <!-- Checklist Nama Produk -->
              <div class="mb-3" >
                <label for="namaProdukChecklist" class="form-label me-3">Pilih Nama Produk</label>
                <a href="javascript:void(0);" id="checkAll">
                  <span class="text-primary">Select All</span>
                </a>
                <a href="javascript:void(0);" class="d-none" id="uncheckAll">
                  <span class="text-danger">Deselect All</span>
                </a>
                <div id="namaProdukChecklist" class="row" style="max-height: 300px; overflow-y: auto;border: 1px solid #ddd;">
                    @foreach ($produks as $produk)
                      <div class="col-lg-3 col-md-4 col-sm-6">
                          <div class="form-check">
                              <input class="form-check-input" type="checkbox" value="{{ $produk->id }}" id="{{ $produk->id }}">
                              <label class="form-check-label" for="{{ $produk->id }}">
                                  {{ $produk->nama }}
                              </label>
                          </div>
                      </div>
                      @endforeach
                  </div>
              </div>
              
              <!-- Select Tipe Produk -->
              <div class="mb-3">
                  <label for="filterTipeProduk" class="form-label">Pilih Tipe Produk</label>
                  <select class="form-select" id="filterTipeProduk">
                      <option value="">Semua Tipe</option>
                      @foreach ($tipe_produks as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                      @endforeach
                  </select>
              </div>

              <!-- Select Satuan -->
              <div class="mb-3">
                  <label for="filterSatuan" class="form-label">Pilih Tipe Produk</label>
                  <select class="form-select" id="filterSatuan">
                      <option value="">Semua Satuan</option>
                      @foreach ($satuans as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                      @endforeach
                  </select>
              </div>
          </div>
          <div class="modal-footer justify-content-center">
              <button type="button" class="btn btn-secondary" id="clearBtn2">Clear</button>
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

        const columns = [
            { data: 'no', name: 'no', orderable: false },
            { data: 'kode', name: 'kode' },
            { data: 'nama', name: 'nama' },
            { 
                data: 'tipe_value', 
                name: 'tipe_value', 
                render: function(data, type, row) {
                    return row.tipe_value;
                } 
            },
            { data: 'satuan', name: 'satuan' },
            { data: 'deskripsi', name: 'deskripsi', orderable: false },
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
                                <a href="javascript:void(0);" onclick="getData(${row.id})" data-bs-toggle="modal" data-bs-target="#editproduk" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
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
            ajaxUrl: "{{ route('produks.index') }}",
            columns: columns,
            order: [[1, 'asc']],
            searching: true,
            lengthChange: true,
            pageLength: 10,
        }, {
            produk: '#namaProdukChecklist',
            tipe_produk: '#filterTipeProduk',
            satuan: '#filterSatuan',
        });

        $('#filterBtn').on('click', function() {
            table.ajax.reload();
            $('#filterModal').modal('hide');
        });

        $('#clearBtn, #clearBtn2').on('click', function() {
            $('#filterModal input[type="checkbox"]').prop('checked', false);
            $('#filterTipeProduk').val('').trigger('change');
            $('#filterSatuan').val('').trigger('change');
            table.ajax.reload();
            $('#uncheckAll').addClass('d-none');
            $('#checkAll').removeClass('d-none');
        });

        $('#checkAll').on('click', function() {
            $('#namaProdukChecklist input').prop('checked', true);
            $(this).addClass('d-none');
            $('#uncheckAll').removeClass('d-none');
        });
        $('#uncheckAll').on('click', function() {
            $('#namaProdukChecklist input').prop('checked', false);
            $(this).addClass('d-none');
            $('#checkAll').removeClass('d-none');
        });
    });

    function getData(id){
        $.ajax({
            type: "GET",
            url: "/produks/"+id+"/edit",
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            beforeSend: function() {
                $('#global-loader-transparent').show();
            },
            success: function(response) {
                $('#editForm').attr('action', 'produks/'+id+'/update');
                $('#edit_nama').val(response.nama)
                $('#edit_tipe_produk').val(response.tipe_produk).trigger('change')
                $('#edit_satuan').val(response.satuan)
                $('#edit_deskripsi').val(response.deskripsi)
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
                url: "/produks/"+id+"/delete",
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