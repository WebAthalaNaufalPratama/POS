@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Buat Data Inventory Inden</h5>
            </div>
            <div class="card-body">
                {{-- <form id="form_perangkai" action="{{ route('inven_inden.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="kode_produk" class="col-form-label">Produk</label>
                            <div id="div_produk" class="form-group">
                                <select id="kode_produk" name="kode_produk" class="form-control" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach ($produks as $item)
                                        <option value="{{ $item->kode }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="bulan_inden">Bulan Inden</label>
                                <select class="form-control" id="bulanTahun" name="bulan_inden">
                                    <!-- Opsi akan diisi oleh JavaScript -->
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label for="lokasi_id" class="col-form-label">Supplier</label>
                            <div id="div_lokasi" class="form-group">
                                <select id="supplier_id" name="supplier_id" class="form-control" required>
                                    <option value="">Pilih Supplier</option>
                                    @foreach ($suppliers as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="kode_produk_inden" class="col-form-label">Kode Produk Inden</label>
                            <input type="text" class="form-control" name="kode_produk_inden" id="kode_produk_inden" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="jumlah" class="col-form-label">Jumlah</label>
                            <input type="number" class="form-control" name="jumlah" id="jumlah" required>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a href="{{ route('inven_inden.index') }}" class="btn btn-secondary" type="button">Back</a>
                    </div>
                </form> --}}
                <form id="form_create" action="{{ route('inven_inden.store') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Periode</th>
                                    <th>Supplier</th>
                                    <th>Kode Produk inden</th>
                                    <th>Jumlah</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="dynamic_field">
                                @forelse (old('kode_produk', ['']) as $key => $kodeProdukOld)
                                <tr id="row_{{ $key }}">
                                    <td>
                                        <select id="kode_produk_{{ $key }}" name="kode_produk[]" class="form-control" required>
                                            <option value="">Pilih Produk</option>
                                            @foreach ($produks as $item)
                                                <option value="{{ $item->kode }}" {{ $item->kode == old('kode_produk.'.$key) ? 'selected' : '' }}>{{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select id="bulan_inden_{{ $key }}" name="bulan_inden[]" class="form-control" required>
                                            <option value="">Pilih Periode</option>
                                            @foreach ($periodes as $item)
                                                <option value="{{ $item }}" {{ $item == old('bulan_inden.'.$key) ? 'selected' : '' }}>{{ $item }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select id="supplier_id_{{ $key }}" name="supplier_id[]" class="form-control" required>
                                            @if(count($suppliers) == 1)
                                                @foreach ($suppliers as $item)
                                                    <option value="{{ $item->id }}" {{ $item->id == old('supplier_id.'.$key) ? 'selected' : '' }}>{{ $item->nama }}</option>
                                                @endforeach
                                            @else
                                            <option value="">Pilih Greenhouse</option>
                                                @foreach ($suppliers as $item)
                                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="kode_produk_inden[]" id="kode_produk_inden_{{ $key }}"  value="{{ old('kode_produk_inden.'.$key) }}" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="jumlah[]" id="jumlah_{{ $key }}" value="{{ old('jumlah.'.$key) }}" required>
                                    </td>
                                    <td>
                                        @if($key == 0)
                                        <a href="javascript:void(0);" id="add_row"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></a>
                                        @else
                                        <a href="javascript:void(0);" id="{{ $key }}" class="remove_row"><img src="/assets/img/icons/delete.svg" style="color: #ff6666" alt="svg"></a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr id="row_0">
                                    <td>
                                        <select id="kode_produk_0" name="kode_produk[]" class="form-control" required>
                                            <option value="">Pilih Produk</option>
                                            @foreach ($produks as $item)
                                                <option value="{{ $item->kode }}">{{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select id="bulan_inden_0" name="bulan_inden[]" class="form-control" required>
                                            <option value="">Pilih Periode</option>
                                            @foreach ($periodes as $item)
                                                <option value="{{ $item }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select id="lokasi_id_0" name="lokasi_id[]" class="form-control" required>
                                            @if(count($greenhouses) == 1)
                                                @foreach ($greenhouses as $item)
                                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                                @endforeach
                                            @else
                                            <option value="">Pilih Greenhouse</option>
                                                @foreach ($greenhouses as $item)
                                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="jumlah[]" id="jumlah_0" oninput="validateMinZero(this, 10000)" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="min_stok[]" id="min_stok_0" oninput="validateMinZero(this, 100)" required>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" id="add_row"><img src="/assets/img/icons/plus.svg" style="color: #90ee90" alt="svg"></a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-3">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a href="{{ route('inven_inden.index') }}" class="btn btn-secondary" type="button">Back</a>
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
        $(document).ready(function() {
            $('select').select2();
            let rowCount = 1;

            // Function to add a new row
            $('#dynamic_field').on('click', '#add_row', function() {
                let newRow = `
                    <tr id="row_${rowCount}">
                        <td>
                            <select id="kode_produk_${rowCount}" name="kode_produk[]" class="form-control" required>
                                <option value="">Pilih Produk</option>
                                @foreach ($produks as $item)
                                    <option value="{{ $item->kode }}" {{ $item->kode == old('kode_produk.'.$key) ? 'selected' : '' }}>{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select id="bulan_inden_${rowCount}" name="bulan_inden[]" class="form-control" required>
                                <option value="">Pilih Periode</option>
                                @foreach ($periodes as $item)
                                    <option value="{{ $item }}" {{ $item == old('bulan_inden.'.$key) ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select id="supplier_id_${rowCount}" name="supplier_id[]" class="form-control" required>
                                @if(count($suppliers) == 1)
                                    @foreach ($suppliers as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == old('supplier_id.'.$key) ? 'selected' : '' }}>{{ $item->nama }}</option>
                                    @endforeach
                                @else
                                <option value="">Pilih Greenhouse</option>
                                    @foreach ($suppliers as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="kode_produk_inden[]" id="kode_produk_inden_${rowCount}"  value="{{ old('kode_produk_inden.'.$key) }}" required>
                        </td>
                        <td>
                            <input type="number" class="form-control" name="jumlah[]" id="jumlah_${rowCount}" value="{{ old('jumlah.'.$key) }}" required>
                        </td>
                        <td>
                            <a href="javascript:void(0);" id="${rowCount}" class="remove_row"><img src="/assets/img/icons/delete.svg" style="color: #ff6666" alt="svg"></a>
                        </td>
                    </tr>
                `;

                $('#dynamic_field').append(newRow);
                $('select').select2();
                rowCount++;
            });

            // Function to remove a row
            $(document).on('click', '.remove_row', function() {
                let rowId = $(this).attr('id');
                $(`#row_${rowId}`).remove();
            });
        });
    </script>
@endsection