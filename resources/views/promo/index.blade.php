@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
        <div class="card-header">
            <div class="page-header">
                <div class="page-title">
                    <h4>Promo</h4>
                </div>
                <div class="page-btn">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addpromo" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Tambah Promo</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table" id="datatable">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Berakhir</th>
                    <th>Ketentuan</th>
                    <th>Diskon</th>
                    <th>Lokasi</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    {{-- @foreach ($promos as $promo)
                        <tr>
                            <td>{{ $loop->iteration ?? '-' }}</td>
                            <td>{{ $promo->nama ?? '-' }}</td>
                            <td>{{ $promo->tanggal_mulai ? formatTanggal($promo->tanggal_mulai) : '-'}}</td>
                            <td>{{ $promo->tanggal_berakhir ? formatTanggal($promo->tanggal_berakhir) : '-'}}</td>
                            <td>{{ $promo->ketentuan ?? '-' }}</td>
                            <td>{{ $promo->diskon ?? '-' }}</td>
                            <td>{{ $promo->lokasi->nama?? '-' }}</td>
                            <td class="text-center">
                              <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                  <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                              </a>
                              <ul class="dropdown-menu">
                                  <li>
                                      <a href="javascript:void(0);" onclick="getData({{ $promo->id }})" data-bs-toggle="modal" data-bs-target="#editpromo" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
                                  </li>
                                  <li>
                                      <a href="#" class="dropdown-item" href="javascript:void(0);" onclick="deleteData({{ $promo->id }})"><img src="assets/img/icons/delete1.svg" class="me-2" alt="img">Delete</a>
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
<div class="modal fade" id="addpromo" tabindex="-1" aria-labelledby="addpromolabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addpromolabel">Tambah Promo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="addForm" action="{{ route('promo.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="add_nama" required>
            </div>
            <div class="mb-3">
              <label for="tanggal_mulai" class="col-form-label">Tanggal Mulai</label>
              <input type="date" class="form-control" name="tanggal_mulai" id="add_tanggal_mulai" required>
            </div>
            <div class="mb-3">
              <label for="tanggal_berakhir" class="col-form-label">Tanggal Berakhir</label>
              <input type="date" class="form-control" name="tanggal_berakhir" id="add_tanggal_berakhir" required>
            </div>
            <div class="mb-3">
              <label for="ketentuan" class="col-form-label">Ketentuan</label>
              <div class="form-group">
                <select class="select2" name="ketentuan" id="add_ketentuan" required>
                  <option value="">Pilih Ketentuan</option>
                    <option value="produk">Produk</option>
                    <option value="tipe_produk">Tipe Produk</option>
                    <option value="min_transaksi">Minimal Transaksi</option>
                </select>
              </div>
            </div>
            <div class="mb-3" id="div_add_ketentuan_produk" style="display: none">
              <label for="ketentuan_produk" class="col-form-label">Produk</label>
              <div class="form-group">
                <select class="select2" name="ketentuan_produk" id="add_ketentuan_produk" required>
                  <option value="">Pilih Produk</option>
                  @foreach ($produk_juals as $produk_jual)
                    <option value="{{ $produk_jual->kode }}">{{ $produk_jual->nama }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3" id="div_add_ketentuan_tipe_produk" style="display: none">
              <label for="ketentuan_tipe_produk" class="col-form-label">Tipe Produk</label>
              <div class="form-group">
                <select class="select2" name="ketentuan_tipe_produk" id="add_ketentuan_tipe_produk" required>
                  <option value="">Pilih Tipe Produk</option>
                  @foreach ($tipe_produks as $tipe_produk)
                    <option value="{{ $tipe_produk->id }}">{{ $tipe_produk->nama }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3" id="div_add_ketentuan_min_transaksi" style="display: none">
              <label for="ketentuan_min_transaksi" class="col-form-label">Minimal Transaksi</label>
              <input type="text" class="form-control" name="ketentuan_min_transaksi" id="add_ketentuan_min_transaksi" required>
            </div>
            <div class="mb-3">
              <label for="diskon" class="col-form-label">Diskon</label>
              <div class="form-group">
                <select class="select2" name="diskon" id="add_diskon" required>
                  <option value="">Pilih Diskon</option>
                    <option value="produk">Produk</option>
                    <option value="nominal">Nominal</option>
                    <option value="persen">Persen</option>
                    <option value="poin">Poin</option>
                </select>
              </div>
            </div>
            <div class="mb-3" id="div_add_diskon_free_produk" style="display: none">
              <label for="diskon_free_produk" class="col-form-label">Free Produk</label>
              <div class="form-group">
                <select class="select2" name="diskon_free_produk" id="add_diskon_free_produk" required>
                  <option value="">Pilih Free Produk</option>
                  @foreach ($produk_juals as $free_produk)
                    <option value="{{ $free_produk->kode }}">{{ $free_produk->nama }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3" id="div_add_diskon_nominal" style="display: none">
                <label for="diskon_nominal" class="col-form-label">Nominal Diskon</label>
                <input type="text" class="form-control" name="diskon_nominal" id="add_diskon_nominal" required>
            </div>
            <div class="mb-3" id="div_add_diskon_persen" style="display: none">
                <label for="diskon_persen" class="col-form-label">Persen Diskon</label>
                <div class="input-group">
                  <input type="number" class="form-control" name="diskon_persen" id="add_diskon_persen" aria-describedby="basic-addon3" min="0" max="100" oninput="checkPersen('add_diskon_persen', 'persen_error', 'add_submit')" required>
                  <span class="input-group-text" id="basic-addon3">%</span>
                </div>
                <span id="persen_error" class="text-danger">Nilai harus berada direntang 0 - 100</span>
            </div>
            <div class="mb-3" id="div_add_diskon_poin" style="display: none">
                <label for="diskon_poin" class="col-form-label">Poin</label>
                <input type="text" class="form-control" name="diskon_poin" id="add_diskon_poin" required>
            </div>
            <div class="mb-3" >
              <label for="addLokasiChecklist" class="form-label me-3">Pilih Lokasi</label>
              <a href="javascript:void(0);" id="addCheckAll">
                <span class="text-primary">Select All</span>
              </a>
              <a href="javascript:void(0);" class="d-none" id="addUncheckAll">
                <span class="text-danger">Deselect All</span>
              </a>
              <div id="addLokasiChecklist" class="row" style="max-height: 300px; overflow-y: auto;border: 1px solid #ddd;">
                @foreach ($lokasis as $lokasi)
                  <div class="col-6">
                      <div class="form-check">
                          <input class="form-check-input" name="lokasi_id[]" type="checkbox" value="{{ $lokasi->id }}" id="add_{{ $lokasi->id }}">
                          <label class="form-check-label" for="{{ $lokasi->id }}">
                              {{ $lokasi->nama }}
                          </label>
                      </div>
                  </div>
                  @endforeach
              </div>
            </div>
        </div>
        <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button id="add_submit" type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
      </div>
    </div>
</div>
<div class="modal fade" id="editpromo" tabindex="-1" aria-labelledby="editpromolabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editpromolabel">Edit Promo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
          <form id="editForm" action="promo/0/update" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label for="nama" class="col-form-label">Nama</label>
              <input type="text" class="form-control" name="nama" id="edit_nama" required>
            </div>
            <div class="mb-3">
              <label for="tanggal_mulai" class="col-form-label">Tanggal Mulai</label>
              <input type="date" class="form-control" name="tanggal_mulai" id="edit_tanggal_mulai" required>
            </div>
            <div class="mb-3">
              <label for="tanggal_berakhir" class="col-form-label">Tanggal Berakhir</label>
              <input type="date" class="form-control" name="tanggal_berakhir" id="edit_tanggal_berakhir" required>
            </div>
            <div class="mb-3">
              <label for="ketentuan" class="col-form-label">Ketentuan</label>
              <div class="form-group">
                <select class="select2" name="ketentuan" id="edit_ketentuan" required>
                  <option value="">Pilih Ketentuan</option>
                    <option value="produk">Produk</option>
                    <option value="tipe_produk">Tipe Produk</option>
                    <option value="min_transaksi">Minimal Transaksi</option>
                </select>
              </div>
            </div>
            <div class="mb-3" id="div_edit_ketentuan_produk" style="display: none">
              <label for="ketentuan_produk" class="col-form-label">Produk</label>
              <div class="form-group">
                <select class="select2" name="ketentuan_produk" id="edit_ketentuan_produk" required>
                  <option value="">Pilih Produk</option>
                  @foreach ($produk_juals as $produk_jual)
                    <option value="{{ $produk_jual->kode }}">{{ $produk_jual->nama }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3" id="div_edit_ketentuan_tipe_produk" style="display: none">
              <label for="ketentuan_tipe_produk" class="col-form-label">Tipe Produk</label>
              <div class="form-group">
                <select class="select2" name="ketentuan_tipe_produk" id="edit_ketentuan_tipe_produk" required>
                  <option value="">Pilih Tipe Produk</option>
                  @foreach ($tipe_produks as $tipe_produk)
                    <option value="{{ $tipe_produk->id }}">{{ $tipe_produk->nama }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3" id="div_edit_ketentuan_min_transaksi" style="display: none">
              <label for="ketentuan_min_transaksi" class="col-form-label">Minimal Transaksi</label>
              <input type="text" class="form-control" name="ketentuan_min_transaksi" id="edit_ketentuan_min_transaksi" required>
            </div>
            <div class="mb-3">
              <label for="diskon" class="col-form-label">Diskon</label>
              <div class="form-group">
                <select class="select2" name="diskon" id="edit_diskon" required>
                  <option value="">Pilih Diskon</option>
                    <option value="produk">Produk</option>
                    <option value="nominal">Nominal</option>
                    <option value="persen">Persen</option>
                    <option value="poin">Poin</option>
                </select>
              </div>
            </div>
            <div class="mb-3" id="div_edit_diskon_free_produk" style="display: none">
              <label for="diskon_free_produk" class="col-form-label">Free Produk</label>
              <div class="form-group">
                <select class="select2" name="diskon_free_produk" id="edit_diskon_free_produk" required>
                  <option value="">Pilih Free Produk</option>
                  @foreach ($produk_juals as $free_produk)
                    <option value="{{ $free_produk->kode }}">{{ $free_produk->nama }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3" id="div_edit_diskon_nominal" style="display: none">
                <label for="diskon_nominal" class="col-form-label">Nominal Diskon</label>
                <input type="text" class="form-control" name="diskon_nominal" id="edit_diskon_nominal" required>
            </div>
            <div class="mb-3" id="div_edit_diskon_persen" style="display: none">
                <label for="diskon_persen" class="col-form-label">Persen Diskon</label>
                <div class="input-group">
                  <input type="number" class="form-control" name="diskon_persen" id="edit_diskon_persen" aria-describedby="basic-addon3" min="0" max="100" oninput="checkPersen('edit_diskon_persen', 'persen_error', 'edit_submit')" required>
                  <span class="input-group-text" id="basic-addon3">%</span>
                </div>
                <span id="persen_error" class="text-danger">Nilai harus berada direntang 0 - 100</span>
            </div>
            <div class="mb-3" id="div_edit_diskon_poin" style="display: none">
                <label for="diskon_poin" class="col-form-label">Poin</label>
                <input type="text" class="form-control" name="diskon_poin" id="edit_diskon_poin" required>
            </div>
            <div class="mb-3" >
              <label for="editLokasiChecklist" class="form-label me-3">Pilih Lokasi</label>
              <a href="javascript:void(0);" id="editCheckAll">
                <span class="text-primary">Select All</span>
              </a>
              <a href="javascript:void(0);" class="d-none" id="editUncheckAll">
                <span class="text-danger">Deselect All</span>
              </a>
              <div id="editLokasiChecklist" class="row" style="max-height: 300px; overflow-y: auto;border: 1px solid #ddd;">
                @foreach ($lokasis as $lokasi)
                  <div class="col-6">
                      <div class="form-check">
                          <input class="form-check-input" name="lokasi_id[]" type="checkbox" value="{{ $lokasi->id }}" id="edit_{{ $lokasi->id }}">
                          <label class="form-check-label" for="{{ $lokasi->id }}">
                              {{ $lokasi->nama }}
                          </label>
                      </div>
                  </div>
                  @endforeach
              </div>
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
{{-- modal end --}}
@endsection

@section('scripts')
    <script>
    $(document).ready(function() {
        $('#add_ketentuan, #add_diskon, #add_lokasi_id, #add_diskon_free_produk, #add_ketentuan_produk, #add_ketentuan_tipe_produk, #edit_ketentuan, #edit_diskon, #edit_lokasi_id, #edit_diskon_free_produk, #edit_ketentuan_produk, #edit_ketentuan_tipe_produk').select2()
    });
    $('#add_ketentuan').change(function(){
      var ketentuan = $(this).val();
      if(!ketentuan){
        $('#div_add_ketentuan_produk').css('display', 'none');
        $('#div_add_ketentuan_tipe_produk').css('display', 'none');
        $('#div_add_ketentuan_min_transaksi').css('display', 'none');
        $('#add_ketentuan_produk').attr('required', false);
        $('#add_ketentuan_tipe_produk').attr('required', false);
        $('#add_ketentuan_min_transaksi').attr('required', false);
      }
      if(ketentuan == 'produk'){
        $('#div_add_ketentuan_produk').css('display', 'block');
        $('#div_add_ketentuan_tipe_produk').css('display', 'none');
        $('#div_add_ketentuan_min_transaksi').css('display', 'none');
        $('#add_ketentuan_produk').attr('required', true);
        $('#add_ketentuan_tipe_produk').attr('required', false);
        $('#add_ketentuan_min_transaksi').attr('required', false);
      }
      if(ketentuan == 'min_transaksi'){
        $('#div_add_ketentuan_produk').css('display', 'none');
        $('#div_add_ketentuan_tipe_produk').css('display', 'none');
        $('#div_add_ketentuan_min_transaksi').css('display', 'block');
        $('#add_ketentuan_produk').attr('required', false);
        $('#add_ketentuan_tipe_produk').attr('required', false);
        $('#add_ketentuan_min_transaksi').attr('required', true);
      }
      if(ketentuan == 'tipe_produk'){
        $('#div_add_ketentuan_produk').css('display', 'none');
        $('#div_add_ketentuan_tipe_produk').css('display', 'block');
        $('#div_add_ketentuan_min_transaksi').css('display', 'none');
        $('#add_ketentuan_produk').attr('required', false);
        $('#add_ketentuan_tipe_produk').attr('required', true);
        $('#add_ketentuan_min_transaksi').attr('required', false);
      }
    });
    $('#add_diskon').change(function(){
      var diskon = $(this).val();
      if(!diskon){
        $('#div_add_diskon_free_produk').css('display', 'none');
        $('#div_add_diskon_nominal').css('display', 'none');
        $('#div_add_diskon_persen').css('display', 'none');
        $('#div_add_diskon_poin').css('display', 'none');
        $('#add_diskon_free_produk').attr('required', false);
        $('#add_diskon_nominal').attr('required', false);
        $('#add_diskon_persen').attr('required', false);
        $('#add_diskon_poin').attr('required', false);
      }
      if(diskon == 'produk'){
        $('#div_add_diskon_free_produk').css('display', 'block');
        $('#div_add_diskon_nominal').css('display', 'none');
        $('#div_add_diskon_persen').css('display', 'none');
        $('#div_add_diskon_poin').css('display', 'none');
        $('#add_diskon_free_produk').attr('required', true);
        $('#add_diskon_nominal').attr('required', false);
        $('#add_diskon_persen').attr('required', false);
        $('#add_diskon_poin').attr('required', false);
      }
      if(diskon == 'nominal'){
        $('#div_add_diskon_free_produk').css('display', 'none');
        $('#div_add_diskon_nominal').css('display', 'block');
        $('#div_add_diskon_persen').css('display', 'none');
        $('#div_add_diskon_poin').css('display', 'none');
        $('#add_diskon_free_produk').attr('required', false);
        $('#add_diskon_nominal').attr('required', true);
        $('#add_diskon_persen').attr('required', false);
        $('#add_diskon_poin').attr('required', false);
      }
      if(diskon == 'persen'){
        $('#div_add_diskon_free_produk').css('display', 'none');
        $('#div_add_diskon_nominal').css('display', 'none');
        $('#div_add_diskon_persen').css('display', 'block');
        $('#div_add_diskon_poin').css('display', 'none');
        $('#add_diskon_free_produk').attr('required', false);
        $('#add_diskon_nominal').attr('required', false);
        $('#add_diskon_persen').attr('required', true);
        $('#add_diskon_poin').attr('required', false);
      }
      if(diskon == 'poin'){
        $('#div_add_diskon_free_produk').css('display', 'none');
        $('#div_add_diskon_nominal').css('display', 'none');
        $('#div_add_diskon_persen').css('display', 'none');
        $('#div_add_diskon_poin').css('display', 'block');
        $('#add_diskon_free_produk').attr('required', false);
        $('#add_diskon_nominal').attr('required', false);
        $('#add_diskon_persen').attr('required', false);
        $('#add_diskon_poin').attr('required', true);
      }
    });
    $('#edit_ketentuan').change(function(){
      var ketentuan = $(this).val();
      if(!ketentuan){
        $('#div_edit_ketentuan_produk').css('display', 'none');
        $('#div_edit_ketentuan_tipe_produk').css('display', 'none');
        $('#div_edit_ketentuan_min_transaksi').css('display', 'none');
        $('#edit_ketentuan_produk').attr('required', false);
        $('#edit_ketentuan_tipe_produk').attr('required', false);
        $('#edit_ketentuan_min_transaksi').attr('required', false);
      }
      if(ketentuan == 'produk'){
        $('#div_edit_ketentuan_produk').css('display', 'block');
        $('#div_edit_ketentuan_tipe_produk').css('display', 'none');
        $('#div_edit_ketentuan_min_transaksi').css('display', 'none');
        $('#edit_ketentuan_produk').attr('required', true);
        $('#edit_ketentuan_tipe_produk').attr('required', false);
        $('#edit_ketentuan_min_transaksi').attr('required', false);
      }
      if(ketentuan == 'min_transaksi'){
        $('#div_edit_ketentuan_produk').css('display', 'none');
        $('#div_edit_ketentuan_tipe_produk').css('display', 'none');
        $('#div_edit_ketentuan_min_transaksi').css('display', 'block');
        $('#edit_ketentuan_produk').attr('required', false);
        $('#edit_ketentuan_tipe_produk').attr('required', false);
        $('#edit_ketentuan_min_transaksi').attr('required', true);
      }
      if(ketentuan == 'tipe_produk'){
        $('#div_edit_ketentuan_produk').css('display', 'none');
        $('#div_edit_ketentuan_tipe_produk').css('display', 'block');
        $('#div_edit_ketentuan_min_transaksi').css('display', 'none');
        $('#edit_ketentuan_produk').attr('required', false);
        $('#edit_ketentuan_tipe_produk').attr('required', true);
        $('#edit_ketentuan_min_transaksi').attr('required', false);
      }
    });
    $('#edit_diskon').change(function(){
      var diskon = $(this).val();
      if(!diskon){
        $('#div_edit_diskon_free_produk').css('display', 'none');
        $('#div_edit_diskon_nominal').css('display', 'none');
        $('#div_edit_diskon_persen').css('display', 'none');
        $('#div_edit_diskon_poin').css('display', 'none');
        $('#edit_diskon_free_produk').attr('required', false);
        $('#edit_diskon_nominal').attr('required', false);
        $('#edit_diskon_persen').attr('required', false);
        $('#edit_diskon_poin').attr('required', false);
      }
      if(diskon == 'produk'){
        $('#div_edit_diskon_free_produk').css('display', 'block');
        $('#div_edit_diskon_nominal').css('display', 'none');
        $('#div_edit_diskon_persen').css('display', 'none');
        $('#div_edit_diskon_poin').css('display', 'none');
        $('#edit_diskon_free_produk').attr('required', true);
        $('#edit_diskon_nominal').attr('required', false);
        $('#edit_diskon_persen').attr('required', false);
        $('#edit_diskon_poin').attr('required', false);
      }
      if(diskon == 'nominal'){
        $('#div_edit_diskon_free_produk').css('display', 'none');
        $('#div_edit_diskon_nominal').css('display', 'block');
        $('#div_edit_diskon_persen').css('display', 'none');
        $('#div_edit_diskon_poin').css('display', 'none');
        $('#edit_diskon_free_produk').attr('required', false);
        $('#edit_diskon_nominal').attr('required', true);
        $('#edit_diskon_persen').attr('required', false);
        $('#edit_diskon_poin').attr('required', false);
      }
      if(diskon == 'persen'){
        $('#div_edit_diskon_free_produk').css('display', 'none');
        $('#div_edit_diskon_nominal').css('display', 'none');
        $('#div_edit_diskon_persen').css('display', 'block');
        $('#div_edit_diskon_poin').css('display', 'none');
        $('#edit_diskon_free_produk').attr('required', false);
        $('#edit_diskon_nominal').attr('required', false);
        $('#edit_diskon_persen').attr('required', true);
        $('#edit_diskon_poin').attr('required', false);
      }
      if(diskon == 'poin'){
        $('#div_edit_diskon_free_produk').css('display', 'none');
        $('#div_edit_diskon_nominal').css('display', 'none');
        $('#div_edit_diskon_persen').css('display', 'none');
        $('#div_edit_diskon_poin').css('display', 'block');
        $('#edit_diskon_free_produk').attr('required', false);
        $('#edit_diskon_nominal').attr('required', false);
        $('#edit_diskon_persen').attr('required', false);
        $('#edit_diskon_poin').attr('required', true);
      }
    });

    // add form
    $('#addCheckAll').on('click', function() {
        $('#addLokasiChecklist input').prop('checked', true);
        $(this).addClass('d-none');
        $('#addUncheckAll').removeClass('d-none');
    });
    $('#addUncheckAll').on('click', function() {
        $('#addLokasiChecklist input').prop('checked', false);
        $(this).addClass('d-none');
        $('#addCheckAll').removeClass('d-none');
    });

    // edit form
    $('#editCheckAll').on('click', function() {
        $('#editLokasiChecklist input').prop('checked', true);
        $(this).addClass('d-none');
        $('#editUncheckAll').removeClass('d-none');
    });
    $('#editUncheckAll').on('click', function() {
        $('#editLokasiChecklist input').prop('checked', false);
        $(this).addClass('d-none');
        $('#editCheckAll').removeClass('d-none');
    });

    const columns = [
        { data: 'no', name: 'no', orderable: false },
        { data: 'nama', name: 'nama' },
        { 
            data: 'tanggal_mulai', 
            name: 'tanggal_mulai', 
            render: function(data, type, row) {
                return row.tanggal_mulai_format;
            } 
        },
        { 
            data: 'tanggal_berakhir', 
            name: 'tanggal_berakhir', 
            render: function(data, type, row) {
                return row.tanggal_berakhir_format;
            } 
        },
        { data: 'ketentuan', name: 'ketentuan' },
        { data: 'diskon', name: 'diskon' },
        { 
          data: 'lokasi', 
          name: 'lokasi',
          orderable: false,
          render: function(data, type, row) {
            if (Array.isArray(data)) {
                // Convert array to <ul> <li> list
                return '<ul>' + data.map(function(item) {
                    return '<li>- ' + item + '</li>';
                }).join('') + '</ul>';
            }
            return '<ul><li>' + data + '</li></ul>';
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
                            <a href="javascript:void(0);" onclick="getData(${row.id})" data-bs-toggle="modal" data-bs-target="#editpromo" class="dropdown-item"><img src="assets/img/icons/edit.svg" class="me-2" alt="img">Edit</a>
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
        ajaxUrl: "{{ route('promo.index') }}",
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

    // $('#filterBtn').on('click', function() {
    //     table.ajax.reload();
    //     $('#filterModal').modal('hide');
    // });

    // $('#clearBtn').on('click', function() {
    //     $('#filterModal input[type="checkbox"]').prop('checked', false);
    //     $('#filterTipeProduk').val('').trigger('change');
    //     $('#filterSatuan').val('').trigger('change');
    //     table.ajax.reload();
    //     $('#uncheckAll').addClass('d-none');
    //     $('#checkAll').removeClass('d-none');
    // });

    // $('#checkAll').on('click', function() {
    //     $('#namaProdukChecklist input').prop('checked', true);
    //     $(this).addClass('d-none');
    //     $('#uncheckAll').removeClass('d-none');
    // });
    
    // $('#uncheckAll').on('click', function() {
    //     $('#namaProdukChecklist input').prop('checked', false);
    //     $(this).addClass('d-none');
    //     $('#checkAll').removeClass('d-none');
    // });

    $(document).on('input', '#add_diskon_poin, #edit_diskon_poin, #add_ketentuan_min_transaksi, #edit_ketentuan_min_transaksi, #add_diskon_nominal, #edit_diskon_nominal', function() {
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
          // Add input number cleaning for specific inputs
          let inputs = $('#addForm').find('#add_diskon_poin, #add_ketentuan_min_transaksi, #add_diskon_nominal');
          inputs.each(function() {
              let input = $(this);
              let value = input.val();
              let cleanedValue = cleanNumber(value);

              // Set the cleaned value back to the input
              input.val(cleanedValue);
          });

          return true;
      });

      $('#editForm').on('submit', function(e) {
          // Add input number cleaning for specific inputs
          let inputs = $('#editForm').find('#edit_diskon_poin, #edit_ketentuan_min_transaksi, #edit_diskon_nominal');
          inputs.each(function() {
              let input = $(this);
              let value = input.val();
              let cleanedValue = cleanNumber(value);

              // Set the cleaned value back to the input
              input.val(cleanedValue);
          });

          return true;
      });

    function checkPersen(input_id, validation_id, submit_id){
      var persen = $('#'+input_id).val();
      var int_persen = parseInt(persen);
      $('#'+validation_id).css('display', 'none');
      $('#'+submit_id).attr('disabled', false);
      console.log('changed')
      if(int_persen > 100 || int_persen < 0){
        $('#'+validation_id).css('display', 'block');
        $('#'+submit_id).attr('disabled', true);
      } else {
        
      }
    }

    function getData(id){
        $.ajax({
            type: "GET",
            url: "/promo/"+id+"/edit",
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                $('#editForm').attr('action', 'promo/'+id+'/update');
                $('#edit_nama').val(response.nama)
                $('#edit_tanggal_mulai').val(response.tanggal_mulai)
                $('#edit_tanggal_berakhir').val(response.tanggal_berakhir)
                $('#edit_ketentuan').val(response.ketentuan).trigger('change')
                if(response.ketentuan == 'tipe_produk'){
                 $('#edit_ketentuan_tipe_produk').val(response.ketentuan_tipe_produk).trigger('change')
                }
                if(response.ketentuan == 'min_transaksi'){
                 $('#edit_ketentuan_min_transaksi').val(formatNumber(response.ketentuan_min_transaksi))
                }
                if(response.ketentuan == 'produk'){
                 $('#edit_ketentuan_produk').val(response.ketentuan_produk).trigger('change')
                }

                $('#edit_diskon').val(response.diskon).trigger('change')
                if(response.diskon == 'persen'){
                 $('#edit_diskon_persen').val(response.diskon_persen)
                }
                if(response.diskon == 'nominal'){
                 $('#edit_diskon_nominal').val(formatNumber(response.diskon_nominal))
                }
                if(response.diskon == 'poin'){
                 $('#edit_diskon_poin').val(formatNumber(response.diskon_poin))
                }
                if(response.diskon == 'produk'){
                 $('#edit_diskon_free_produk').val(response.diskon_free_produk).trigger('change')
                }

                const lokasiIdJson = response.lokasi_id;
                lokasiIds = JSON.parse(lokasiIdJson);
    
                if (!Array.isArray(lokasiIds)) {
                  lokasiIds = [response.lokasi_id.toString()];
                }
                $('#editLokasiChecklist input').each(function() {
                    const checkbox = $(this);
                    const id = checkbox.val();
                    checkbox.prop('checked', lokasiIds.includes(id));
                });

                const allChecked = $('#editLokasiChecklist input:checkbox').length === $('#editLokasiChecklist input:checkbox:checked').length;
                if (allChecked) {
                    $('#editCheckAll').addClass('d-none');
                    $('#editUncheckAll').removeClass('d-none');
                } else {
                    $('#editCheckAll').removeClass('d-none');
                    $('#editUncheckAll').addClass('d-none');
                }
                
            },
            error: function(error) {
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
                url: "/promo/"+id+"/delete",
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