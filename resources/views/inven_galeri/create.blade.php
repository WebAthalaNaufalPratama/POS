@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Buat Data Inventory Gallery</h5>
            </div>
            <div class="card-body">
                <form id="form_create" action="{{ route('inven_galeri.store') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Kondisi</th>
                                    <th>Gallery</th>
                                    <th>Jumlah</th>
                                    <th>Minimal Stok</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="dynamic_field">
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
                                            @if(count($gallery) == 1)
                                                @foreach ($gallery as $item)
                                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                                @endforeach
                                            @else
                                            <option value="">Pilih Gallery</option>
                                                @foreach ($gallery as $item)
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
                                @if(count($gallery) == 1)
                                    @foreach ($gallery as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                @else
                                <option value="">Pilih Gallery</option>
                                    @foreach ($gallery as $item)
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