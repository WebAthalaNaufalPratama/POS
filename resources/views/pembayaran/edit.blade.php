@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Detail Pembayaran</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('pembayaran.update', ['pembayaran' => $data->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('patch')
                    <!-- <input type="hidden" name="route" value="{{ request()->route()->getName() }},pembayaran,{{ request()->route()->parameter('pembayaran') }}"> -->
                    <div class="mb-3">
                        <label for="no_invoice_bayar" class="col-form-label">No Invoice Perangkai</label>
                        <input type="text" class="form-control" name="no_invoice_bayar" id="no_invoice_bayar" value="{{ $data->no_invoice_bayar}}" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="cara_bayar">Cara Bayar</label>
                        <select class="form-control" id="cara_bayar" name="cara_bayar" required>
                            <option value="">Pilih Cara Bayar</option>
                            <option value="cash" {{ $data->cara_bayar == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ $data->cara_bayar == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nominal">Nominal</label>
                        <input type="text" class="form-control" id="nominal" name="nominal" value="{{ 'Rp ' . number_format($data->nominal, 0, ',', '.') }}" placeholder="Nominal Bayar" required>
                    </div>
                    <div class="mb-3" id="rekening">
                        <label for="rekening_id">Rekening Vonflorist</label>
                        <select class="form-control" id="rekening_id" name="rekening_id">
                            <option value="">Pilih Rekening Von</option>
                            @foreach ($bankpens as $bankpen)
                            <option value="{{ $bankpen->id }}" {{ $bankpen->id == $data->rekening_id ? 'selected' : '' }}>{{ $bankpen->bank }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tanggalbayar">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal_bayar" name="tanggal_bayar" value="{{ $data->tanggal_bayar}}" required>
                    </div>
                    <div class="mb-3">
                            <div class="custom-file-container" data-upload-id="myFirstImage">
                            <label>Bukti Bayar <a href="javascript:void(0)" id="clearFile" class="custom-file-container__image-clear" onclick="clearFile()" title="Clear Image"></a>
                            </label>
                            <label class="custom-file-container__custom-file">
                                <input type="file" id="bukti_file" class="custom-file-container__custom-file__custom-file-input" name="bukti" accept="image/*" >
                                <span class="custom-file-container__custom-file__custom-file-control"></span>
                            </label>
                            <span class="text-danger">max 2mb</span>
                            <div class="image-preview">
                                <img id="imagePreview" src="{{ $data->bukti ? '/storage/' . $data->bukti : '' }}" />
                            </div>
                            
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a href="{{ route('pembayaran.index') }}" class="btn btn-secondary" type="button">Back</a>
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

    $('#bukti_file').on('change', function() {
        const file = $(this)[0].files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#imagePreview').attr('src', e.target.result);
        }
        reader.readAsDataURL(event.target.files[0]);
        if (file.size > 2 * 1024 * 1024) {
            toastr.warning('Ukuran file tidak boleh lebih dari 2mb', {
                closeButton: true,
                tapToDismiss: false,
                rtl: false,
                progressBar: true
            });
            $(this).val('');
            return;
        }
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });

    function formatRupiah(angka, prefix) {
        var numberString = angka.toString().replace(/[^,\d]/g, ''),
            split = numberString.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            var separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix === undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
    }

    var sisabayar = "{{ $penjualan->sisa_bayar }}";
    $('#nominal').on('change', function() {
        var bayar = $(this).val();

        if (parseFloat(bayar) > sisabayar) {
            alert('Nominal Tidak Boleh Lebih dari Total Tagihan');
            $(this).val(formatRupiah(sisabayar)); 
        }else{
            $(this).val( formatRupiah(bayar)); 
        }
    });

    function validateNumericInput() {
        $('#nominal').on('input', function() {
            var value = $(this).val();
            var numericValue = value.replace(/[^0-9.]/g, '');

            if (numericValue !== value) {
                $(this).val(numericValue);
            }
        });
    }

    validateNumericInput();

    function clearFile() {
        $('#bukti').val('');
        $('#preview').attr('src', defaultImg);
    };
    $('#cara_bayar').on('change', function() {
        var caraBayar = $(this).find(':selected').val();
        if (caraBayar == 'cash') {
            $('#rekening').hide();
        } else if (caraBayar == 'transfer') {
            $('#rekening').show();
        }
    });
    function parseRupiahToNumber(rupiah) {
        return parseInt(rupiah.replace(/[^\d]/g, ''));
    }
    
    $('form').on('submit', function(e) {
        // Parse semua nilai input yang diformat Rupiah ke angka numerik
        $('#nominal').val(parseRupiahToNumber($('#nominal').val()));
    });
</script>
@endsection