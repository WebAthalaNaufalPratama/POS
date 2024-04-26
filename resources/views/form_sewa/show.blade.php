@extends('layouts.app-von')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Detail Form Perangkai</h5>
            </div>
            <div class="card-body">
                <form id="form_perangkai" action="{{ route('form.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="route" value="{{ request()->route()->getName() }},form,{{ request()->route()->parameter('form') }}">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-sm-8">
                            <label for="prdTerjual" class="col-form-label">Produk</label>
                            <input type="text" class="form-control" name="produk_id" id="prdTerjual" readonly required>
                        </div>
                        <input type="hidden" name="prdTerjual_id" id="prdTerjual_id" value="">
                        <div class="col-sm-4">
                            <label for="jml_produk" class="col-form-label">Jumlah</label>
                            <input type="number" class="form-control" name="jml_produk" id="jml_produk" readonly required>
                        </div>
                      </div>
                    </div>
                    <div class="mb-3">
                      <label for="no_form" class="col-form-label">No Form Perangkai</label>
                      <input type="text" class="form-control" name="no_form" id="no_form" readonly required>
                    </div>
                    <div class="mb-3">
                      <label for="jenis_rangkaian" class="col-form-label">Jenis Rangkaian</label>
                      <input type="text" class="form-control" name="jenis_rangkaian" id="jenis_rangkaian" value="Sewa" readonly required>
                      </div>
                    <div class="mb-3">
                      <label for="tanggal" class="col-form-label">Tanggal</label>
                      <input type="date" class="form-control" name="tanggal" id="tanggal" value="" required>
                    </div>
                    <div class="mb-3">
                      <label for="jml_perangkai" class="col-form-label">Jumlah Perangkai</label>
                      <input type="number" class="form-control" name="jml_perangkai" id="jml_perangkai" required>
                    </div>
                    <div class="mb-3">
                        <label for="perangkai_id" class="col-form-label">Perangkai</label>
                        <div id="div_perangkai" class="form-group">
                          <select id="perangkai_id_0" name="perangkai_id[]" class="form-control" required>
                              <option value="">Pilih Perangkai</option>
                              @foreach ($perangkai as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                              @endforeach
                          </select>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a href="{{ route('form.index') }}" class="btn btn-secondary" type="button">Back</a>
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
            $('[id^=produk], #customer_id, #sales, #rekening_id, #status, #ongkir_id, #promo_id, #add_tipe').select2();
            var produk_id = {{ $data->produk_terjual->id }};
            getDataPerangkai(produk_id);
        });
        $('#jml_perangkai').on('input', function(e) {
            e.preventDefault();
            var jumlah = $(this).val();
            jumlah = parseInt(jumlah) > 10 ? 10 : parseInt(jumlah);
            console.log(jumlah)
            $('[id^="perangkai_id_"]').each(function() {
                $(this).select2('destroy');
                $(this).remove();
            });
            if(jumlah < 1) return 0;
            for(var i = 0; i < jumlah; i++){
                var rowPerangkai = 
                    '<select id="perangkai_id_' + i + '" name="perangkai_id[]" class="form-control">' +
                    '<option value="">Pilih Perangkai</option>' +
                    '@foreach ($perangkai as $item)' +
                    '<option value="{{ $item->id }}">{{ $item->nama }}</option>' +
                    '@endforeach' +
                    '</select>';
                $('#div_perangkai').append(rowPerangkai);
                $('#perangkai_id_' + i).select2();
            }
        })
        function getDataPerangkai(produk_id){
            var data = {
                produk_id: produk_id,
            };
            $.ajax({
                url: '/getProdukTerjual',
                type: 'GET',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    console.log(response.perangkai)
                    $('#prdTerjual').val(response.produk.nama);
                    $('#prdTerjual_id').val(response.id);
                    $('#jml_produk').val(response.jumlah);
                    $('#no_form').val(response.perangkai[0].no_form);
                    $('#tanggal').val(response.perangkai[0].tanggal);
                    $('#jml_perangkai').val(response.perangkai.length);
                    $('[id^="perangkai_id"]').select2()
                    $('[id^="perangkai_id_"]').each(function() {
                        $(this).select2('destroy');
                        $(this).remove();
                    });
                    if(response.perangkai.length > 0){
                        for(var i = 0; i < response.perangkai.length; i++){
                            var rowPerangkai = 
                                '<select id="perangkai_id_' + i + '" name="perangkai_id[]" class="form-control">' +
                                '<option value="">Pilih Perangkai</option>' +
                                '@foreach ($perangkai as $item)' +
                                '<option value="{{ $item->id }}">{{ $item->nama }}</option>' +
                                '@endforeach' +
                                '</select>';
                            $('#div_perangkai').append(rowPerangkai);
                            $('#div_perangkai select').each(function(index) {
                                $(this).val(response.perangkai[index].perangkai_id);
                            });
                            $('#perangkai_id_' + i).select2();
                        }
                    }
                    $('#modalPerangkai').modal('show');
                },
                error: function(xhr, status, error) {
                    console.log(error)
                }
            });
        }
    </script>
@endsection