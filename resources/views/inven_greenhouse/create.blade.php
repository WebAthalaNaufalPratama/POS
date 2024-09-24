@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Buat Data Inventory Greenhouse</h5>
            </div>
            <div class="card-body">
                <form id="form_create" action="{{ route('inven_greenhouse.store') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Kondisi</th>
                                    <th>Greenhouse</th>
                                    <th>Jumlah</th>
                                    <th>Minimal Stok</th>
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
                                        <select id="kondisi_id_{{ $key }}" name="kondisi_id[]" class="form-control" required>
                                            <option value="">Pilih Kondisi</option>
                                            @foreach ($kondisi as $item)
                                                <option value="{{ $item->id }}" {{ $item->id == old('kondisi_id.'.$key) ? 'selected' : '' }}>{{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select id="lokasi_id_{{ $key }}" name="lokasi_id[]" class="form-control" required>
                                            @if(count($greenhouses) == 1)
                                                @foreach ($greenhouses as $item)
                                                    <option value="{{ $item->id }}" {{ $item->id == old('lokasi_id.'.$key) ? 'selected' : '' }}>{{ $item->nama }}</option>
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
                                        <input type="number" class="form-control" name="jumlah[]" id="jumlah_{{ $key }}" oninput="validateMinZero(this, 10000)" value="{{ old('jumlah.'.$key) }}" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="min_stok[]" id="min_stok_{{ $key }}" oninput="validateMinZero(this, 100)" value="{{ old('min_stok.'.$key) }}" required>
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
                                        <select id="kondisi_id_0" name="kondisi_id[]" class="form-control" required>
                                            <option value="">Pilih Kondisi</option>
                                            @foreach ($kondisi as $item)
                                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
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
                        <a href="{{ route('inven_galeri.index') }}" class="btn btn-secondary" type="button">Back</a>
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
                                    <option value="{{ $item->kode }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select id="kondisi_id_${rowCount}" name="kondisi_id[]" class="form-control" required>
                                <option value="">Pilih Kondisi</option>
                                @foreach ($kondisi as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select id="lokasi_id_${rowCount}" name="lokasi_id[]" class="form-control" required>
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
                            <input type="number" class="form-control" name="jumlah[]" id="jumlah_${rowCount}" oninput="validateMinZero(this, 10000)" required>
                        </td>
                        <td>
                            <input type="number" class="form-control" name="min_stok[]" id="min_stok_${rowCount}" oninput="validateMinZero(this, 100)" required>
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