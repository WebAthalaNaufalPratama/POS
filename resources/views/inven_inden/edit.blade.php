@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Edit Data Inventory Gallery</h5>
            </div>
            <div class="card-body">
                <form id="form_perangkai" action="{{ route('inven_inden.update', ['inven_inden' => $data->id]) }}" method="POST">
                    @csrf
                    @method('patch')
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="kode_produk" class="col-form-label">Produk</label>
                            <div id="div_produk" class="form-group">
                                <select id="kode_produk" name="kode_produk" class="form-control" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach ($produks as $item)
                                        <option value="{{ $item->kode }}" {{ $data->kode_produk == $item->kode ? 'selected' : '' }}>{{ $item->nama }}</option>
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
                                        <option value="{{ $item->id }}"  {{ $data->supplier_id == $item->id ? 'selected' : ''}}>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="kode_produk_inden" class="col-form-label">Kode Produk Inden</label>
                            <input type="text" class="form-control" name="kode_produk_inden" id="kode_produk_inden"  value="{{ $data->kode_produk_inden }}" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="jumlah" class="col-form-label">Jumlah</label>
                            <input type="number" class="form-control" name="jumlah" id="jumlah" value="{{ $data->jumlah }}" required>
                        </div>
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
            $('#kode_produk, #supplier_id').select2();

            const selectBulanTahun = $('#bulanTahun');
            const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            const currentYear = new Date().getFullYear();
            const numberOfYears = 5; // Mengisi untuk 5 tahun ke depan

            for (let year = currentYear; year < currentYear + numberOfYears; year++) {
                for (let i = 0; i < bulan.length; i++) {
                    const option = new Option(`${bulan[i]}-${year}`, `${bulan[i]}-${year}`);
                    selectBulanTahun.append(option);
                }
            }

        // Inisialisasi Select2
        selectBulanTahun.select2({
            placeholder: 'Pilih Bulan dan Tahun',
            tags: true // Aktifkan opsi tags untuk mengizinkan penambahan opsi baru
        });

        // Set the selected option based on $data->bulan_inden
        var bulanInden = '{{ $data->bulan_inden }}';
        if (bulanInden) {
            // Check if the option exists
            if (selectBulanTahun.find(`option[value="${bulanInden}"]`).length) {
                selectBulanTahun.val(bulanInden).trigger('change');
            } else {
                // Add new option if it doesn't exist
                const newOption = new Option(bulanInden, bulanInden, true, true);
                selectBulanTahun.append(newOption).trigger('change');
            }
        }
    });

    </script>
@endsection