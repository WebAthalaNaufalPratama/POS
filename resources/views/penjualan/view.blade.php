<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size:12px;
        }

        /* Container styles */
        .container {
            width: 90%;
            margin: 0 auto;
        }

        header .logo{
            float: left;
            width: 10%;
            max-width: 100px;
        }

        hr{
            margin-top: 80px;
        }

        header .nota-details {
            float: right;
            width: 55%;
            text-align: left;
        }
        header .nota-details h4{
            width: 100%;
        }
        header .nota {
            float: right;
            width: 40%;
        }
        header .note {
            float: right;
            width: 20%;
            text-align: left;
        }
        
        header .nota-details p {
            margin: 10px 0 -100px 0; 
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }
        
        header .nota-details span {
            padding-left: 10px;
        }

        .pelanggan{
            float: left;
            width: 25%;
            max-width: 100px;
        }

        .jawabpelanggan{
            float: left;
            width: 50%;
            text-align: left;
        }

        .invoice{
            float: right;
            width: 50%;
            text-align: left;
        }

        .jawabinvoice{
            float: right;
            width: 25%;
            text-align: left;
        }

        .jawabinvoice .auditor{
            margin-top: 100px;
            float: right;
            width: 25%;
            text-align: right;
        }

        .auditor .name-title {
            margin-top : -10px;
            float: right;
            width: 20%;
            text-align: center;
        }

        .pengiriman{
            float: left;
            width: 25%;
            max-width: 100px;
        }

        .tagihan{
            float: right;
            width: 15%;
            text-align: left;
        }

        .pembuat{
            margin-top: 120px;
            float: left;
            text-align: center;
        }

        .dibukukan{
            margin-top: 120px;
            float: left;
            width: 60%;
            text-align: center;
        }


        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            align-items: center;
        }
        
        table, th, td {
            border: 1px solid #ddd;
        }
        
        th, td {
            padding: 8px;
            text-align: left;
        }
        
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .rekening{
            width: 50%;
        }

        .tabletagihan{
            width : 45%;
            float: right;
        }


        /* Footer styles */
        footer {
            clear: both;
            text-align: center;
            margin-top: 20px;
        }
        .garis_bawah{
            margin-top:140px;
        }

        @media print {
            @page {
                margin: 20mm;
            }

            body {
                margin: 0;
            }

            .container {
                margin-top: 140px; /* Adjust based on header height */
            }
        }

        .repeat-header {
            display: none;
        }

        @page {
            margin: 20mm;
            @top-center {
                content: element(header);
            }
        }

        .repeat-header {
            display: block;
            position: running(header);
        }
    </style>
</head>
<body>

    <div class="container">
        <header>
            <div class="logo">
                <img src="{{ base64_image(public_path('assets/img/von.png')) }}" alt="image">
            </div>
            <div class="nota-details">
                <h1>VONFLORIST</h1>
                <h4>alamat : {{$lokasi}}</h4>
            </div>
            
        </header>
        <hr>

        <div class="judul">
            <h1><center>INVOICE PENJUALAN</center></h1>
        </div>

        <div class="pelanggan">
            <p>Customer</p>
            <p>No Handphone</p>
            <p>Lokasi Beli</p>
            <p>Jumlah Point</p>
            <p>Nama Sales</p>
        </div>

        <div class="jawabpelanggan">
            <p>: {{$customer}}</p>
            <p>: {{$no_handphone}}</p>
            <p>: {{$lokasi}}</p>
            <p>: {{$point_dipakai}}</p>
            <p>: {{$sales}}</p>
        </div>

        <div class="invoice">
            <p>No Invoice</p>
            <p>Tanggal Invoice</p>
            <p>Jatuh Tempo Bayar</p>
        </div>

        <div class="jawabinvoice">
            <p>: {{$no_invoice}}</p>
            <p>: {{date('d F Y', strtotime($tanggal_invoice))}}</p>
            <p>: {{ date('d F Y', strtotime($jatuh_tempo))}}</p>
        </div>

        <hr class="garis_bawah">

        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                    <th>Diskon</th>
                    <th>Harga Total</th>
                </tr>
            </thead>
            <tbody>
            @if(count($produks) < 1) <tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endif
                @php
                $i = 0;
                @endphp
                @foreach ($produks as $komponen)
                <tr>
                    <td>
                        {{$komponen->produk->nama}}
                    </td>
                    <td>{{ 'Rp '. number_format($komponen->harga, 0, ',', '.') }}</td>
                    <td>{{ $komponen->jumlah }}</td>
                    <td>{{ $komponen->diskon ? $komponen->diskon : '-'}}
                    </td>
                    <td>{{ 'Rp '. number_format($komponen->harga_jual, 0, ',', '.')}}</td>
                    @php
                    $i++;
                    @endphp
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="tabletagihan">
                <tr>
                    <th>Sub Total</th>
                    <td>: {{ 'Rp '. number_format($sub_total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Promo</th>
                    <td>: {{ 'Rp '. number_format($total_promo, 0, ',', '.') }}</t>
                </tr>
                <tr>
                    <th>PPN</th>
                    <td>: {{ 'Rp '. number_format($jumlah_ppn, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Biaya Ongkir</th>
                    <td>: {{ 'Rp '. number_format($biaya_ongkir, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Down Payment</th>
                    <td>: {{ 'Rp '. number_format($dp, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Total Tagihan</th>
                    <td>: {{ 'Rp '. number_format($total_tagihan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Sisa Bayar</th>
                    <td>: {{ 'Rp '. number_format($sisa_bayar, 0, ',', '.') }}</td>
                </tr>
        </table>

        <h4>Pembayaran Via Rekening</h4>
        <table class="rekening">
            <thead>
                <tr>
                    <th>Nama Bank</th>
                    <th>No Rekening</th>
                    <th>Atas Nama</th>
                </tr>
            </thead>
            <tbody>
                <tr>@if($cara_bayar == 'transfer')
                        <td>{{ $bank ? $bank : '-' }}</td>
                        <td>{{ $norek ? $norek : '-' }}</td>
                        <td>{{ $akun ? $norek : '-'}}</td>
                    @else
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    @endif
                </tr>
            </tbody>
        </table> 

        <div class="pembuat">
                <p>Pembuat</p>
                <br>
                <br>
                <br>
                <p>{{$dibuat ? $dibuat : '-'}}</p>
                <p>{{$tanggal_dibuat ? date('d F Y', strtotime($tanggal_dibuat)) : '-'}}</p>
        </div>

        

        <div class="dibukukan">
                <p>Dibukukan</p>
                <br>
                <br>
                <br>
                <p>{{$dibukukan ? $dibukukan : '-'}}</p>
                <p>{{$tanggal_dibukukan ? date('d F Y', strtotime($tanggal_dibukukan)) : '-'}}</p>
        </div>
        <center>
        <div class="auditor">
                <p>Auditor</p>
                <br>
                <br>
                <br>
                <p>{{$auditor ? $auditor : '-'}}</p>
                <div class="name-title">
                <p>{{$tanggal_audit ? date('d F Y', strtotime($tanggal_audit)) : '-'}}</p>
                </div>
        </div>
        </center>

        <footer>
            <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        </footer>
    </div>

</body>
</html>
