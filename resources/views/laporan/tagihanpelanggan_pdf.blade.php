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
            margin: 0px 0 -100px 0; 
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            font-style: bold;
            text-align: right;
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

        .auditor{
            margin-top: -40px;
            float: right;
            width: 25%;
            text-align: left;
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
            border: none;
        }
        
        th, td {
            padding: 8px;
            text-align: left;
        }
        
        tr:nth-child(even) {
            background-color: none;
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
                <p>VON FLORIST</p><br>
                <p>Jl. Taman Ade Irma Suryani Nasution No.16, Sekayu.</p><br>
                <p>Semarang Tengah, 50135</p><br>
                <p>Telp : 0811676758</p><br>
                <p><a href="www.vonflorist, IG : @von.florist">www.vonflorist, IG : @von.florist</a></p><br>
            </div>
        </header>
        <hr>

        <div class="judul">
            <h1><center>INVOICE</center></h1>
        </div>
        <table style="border-collapse: collapse; width: 100%;">
            <tbody>
                <tr>
                    <td style="border: none; padding: 8px; text-align: left; background-color: none; font-style: bold; text-decoration: underline;">Customer</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $penjualan->first()->customer->nama }}</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 8px; text-align: left; background-color: none; font-style: bold; text-decoration: underline;">Jumlah Tagihan</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ NumberToWords($penjualan->sum('sisa_bayar')) }}</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 8px; text-align: left; background-color: none; font-style: bold; text-decoration: underline;">Rincian Penjualan</td>
                    <td style="border: none; padding: 8px;">
                        <table style="border-collapse: collapse; width: 100%;">
                            <thead>
                                <tr>
                                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Tanggal</th>
                                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">No Invoice</th>
                                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Produk</th>
                                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Qty</th>
                                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($penjualan as $jual)
                                    @php
                                        $produkCount = $produkterjual->where('no_invoice', $jual->no_invoice)->count();
                                    @endphp
                                    @foreach ($produkterjual->where('no_invoice', $jual->no_invoice) as $item)
                                        <tr>
                                            @if($loop->first)
                                                <td style="border: 1px solid #ddd; padding: 8px;" rowspan="{{ $produkCount }}">{{ $jual->tanggal_invoice }}</td>
                                                <td style="border: 1px solid #ddd; padding: 8px;" rowspan="{{ $produkCount }}">{{ $jual->no_invoice }}</td>
                                            @endif
                                            <td style="border: 1px solid #ddd; padding: 8px;">{{ $item->produk->nama }}</td>
                                            <td style="border: 1px solid #ddd; padding: 8px;">{{ $item->jumlah }}</td>
                                            @if($loop->first)
                                                <td style="border: 1px solid #ddd; padding: 8px;" rowspan="{{ $produkCount }}">{{'Rp '. number_format($jual->sisa_bayar, 0, ',', '.') }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;">Total</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">{{'Rp '. number_format($penjualan->sum('sisa_bayar'), 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="border: none; padding: 8px; text-align: left; background-color: none; font-style: bold; text-decoration: underline;">Masa Termin</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ \Carbon\Carbon::parse($penjualan->first()->tanggal_invoice)->format('F Y') }}</td>
                </tr>
                <tr>
                    <th style="border: none; padding: 8px; text-align: left; background-color:none; font-style: bold; text-decoration: underline;">Sejumlah</th>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{'Rp '. number_format($penjualan->sum('sisa_bayar'), 0, ',', '.') }}</td>
                </tr>  
            </tbody>
        </table>
        <br>
        <p>Pembayaran Via Bank :</p>
        <p>{{$penjualan->first()->rekening->bank}} {{$penjualan->first()->rekening->nomor_rekening}} {{$penjualan->first()->rekening->nama_akun}}</p>
        <p>Mohon Perincian Tagihan Diatas Dapat Segera dilunas</p>

        <div class="auditor">
            <p>{{$penjualan->first()->lokasi->operasional->nama}}, {{\Carbon\Carbon::parse($penjualan->first()->tanggal_invoice)->format('d F Y')}}</p>
            <br>
            <br>
            <br>
            <p style="text-decoration:underline;">{{$penjualan->first()->karyawan->nama}}</p>
            <p>Staff Administrasi</p>
        </div>

        <footer>
            <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        </footer>
    </div>

</body>
</html>
