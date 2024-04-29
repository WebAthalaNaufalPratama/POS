@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Detail Pembayaran</h5>
            </div>
            <div class="card-body">
                <form id="form_perangkai" action="{{ route('pembayaran.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="route" value="{{ request()->route()->getName() }},pembayaran,{{ request()->route()->parameter('pembayaran') }}">
                    <div class="mb-3">
                        <label for="no_invoice_bayar" class="col-form-label">No Invoice Perangkai</label>
                        <input type="text" class="form-control" name="no_invoice_bayar" id="no_invoice_bayar" value="{{ $data->no_invoice_bayar}}" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="cara_bayar">Cara Bayar</label>
                        <select class="form-control" id="cara_bayar" name="cara_bayar" required>
                            <option value="">Pilih Cara Bayar</option>
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nominal">Nominal</label>
                        <input type="number" class="form-control" id="nominal" name="nominal" value="{{ $data->nominal }}" placeholder="Nominal Bayar" required>
                    </div>
                    <div class="mb-3">
                        <label for="bankpenerima">Rekening Vonflorist</label>
                        <select class="form-control" id="rekening_id" name="rekening_id" required>
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
                                <input type="file" id="bukti" class="custom-file-container__custom-file__custom-file-input" name="file" accept="image/*" required>
                                <span class="custom-file-container__custom-file__custom-file-control"></span>
                            </label>
                            <span class="text-danger">max 2mb</span>
                            <img id="preview" src="{{ $data->bukti ? '/storage/' . $data->bukti : '' }}" alt="your image" />
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a href="{{ route('formpenjualan.index') }}" class="btn btn-secondary" type="button">Back</a>
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

    $('#bukti').on('change', function() {
        const file = $(this)[0].files[0];
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

    function clearFile() {
        $('#bukti').val('');
        $('#preview').attr('src', defaultImg);
    };

    $('#cara_bayar').on('change', function(){
        var caraBayar = $(this).find(':selected');

        if( caraBayar == 'cash')
        {
            $('#rekening_id').hide();
        }elseif( caraBayar == 'transfer')
        {
            $('#rekening_id').show();
        }
    });
</script>
@endsection