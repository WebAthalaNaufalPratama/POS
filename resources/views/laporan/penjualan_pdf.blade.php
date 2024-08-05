<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Penjualan</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20mm;
            margin-top: 140px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 0;
        }
        .header {
            position: fixed;
            top: -120px;
            left: 0;
            right: 0;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fff;
        }
        .header {
            color: #000000;
            margin: 0;
            text-align: center;
        }
        .page-break {
            page-break-before: always;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 2px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        th[rowspan="2"] {
            vertical-align: middle;
        }
        th[colspan="2"] {
            text-align: center;
        }
        td {
            vertical-align: top;
        }
        table td table {
            width: 90%;
            border-collapse: collapse;
        }
        table td table th, 
        table td table td {
            border: none;
            padding: 4px;
        }
        thead {
            display: table-header-group;
        }
        tfoot {
            display: table-row-group;
        }
        tr {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
<table class="table datanew" border="1" border-collapse="collapse">
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 5%;">No Invoice</th>
            <th style="width: 5%;">Galery</th>
            <th style="width: 5%;">Nama Customer</th>
            <th style="width: 5%;">Tanggal Invoice</th>
            <th style="width: 5%;">Jatuh Tempo</th>
            <th style="width: 5%;">Nama Sales</th>
            <th style="width: 5%;">Nama Produk Jual</th>
            <th style="width: 5%;">Jumlah Produk Jual</th>
            <th style="width: 5%;">Nama Produk</th>
            <th style="width: 5%;">Jumlah</th>
            <th style="width: 5%;">Kondisi (Baik)</th>
            <th style="width: 5%;">Kondisi (Afkir)</th>
            <th style="width: 5%;">Kondisi (Bonggol)</th>
            <th style="width: 5%;">Harga</th>
            <th style="width: 5%;">Diskon</th>
            <th style="width: 5%;">Jumlah Harga</th>
            <th style="width: 5%;">Sub Total</th>
            <th style="width: 5%;">PPN</th>
            <th style="width: 5%;">Biaya Pengiriman</th>
            <th style="width: 5%;">Total Tagihan</th>
            <th style="width: 5%;">DP</th>
            <th style="width: 5%;">Sisa Bayar</th>
        </tr>
    </thead>
    <tbody>
    @php
        $previousNoDo = '';
        $isGFTInFirstProdukJual = false; // Track if GFT is in the first produk_jual
    @endphp
    @php $no = 1; @endphp
    @foreach ($combinedData as $data)
        @php
            $produkCount = count($data['produk_jual']);
            $firstProdukJual = reset($data['produk_jual']);
            $isGFTInFirstProdukJual = substr($firstProdukJual['kode_produkjual'], 0, 3) === 'GFT';
        @endphp
        @foreach ($data['produk_jual'] as $produkJual)
            @php
                $komponenCount = count($produkJual['komponen']);
            @endphp
            @foreach ($produkJual['komponen'] as $index => $komponen)
                @if($loop->first)
                    <tr>
                    @if($data['no_invoice'] != $previousNoDo)
                        <td rowspan="{{ $produkCount * $komponenCount }}" style="border-bottom: none;">{{ $no++ }}</td>
                        <td rowspan="{{ $produkCount * $komponenCount }}" style="border-bottom: none;">{{ $data['no_invoice'] }}</td>
                        <td rowspan="{{ $produkCount * $komponenCount }}" style="border-bottom: none;">{{ $data['lokasi_pengirim'] }}</td>
                        <td rowspan="{{ $produkCount * $komponenCount }}" style="border-bottom: none;">{{ $data['customer'] }}</td>
                        <td rowspan="{{ $produkCount * $komponenCount }}" style="border-bottom: none;">{{ $data['tanggal_invoice'] }}</td>
                        <td rowspan="{{ $produkCount * $komponenCount }}" style="border-bottom: none;">{{ $data['jatuh_tempo'] }}</td>
                        <td rowspan="{{ $produkCount * $komponenCount }}" style="border-bottom: none;">{{ $data['sales'] }}</td>
                    @endif
                        <td rowspan="{{ $komponenCount }}">{{ $produkJual['nama_produkjual'] }}</td>
                        <td rowspan="{{ $komponenCount }}">{{ $produkJual['jumlahprodukjual'] }}</td>
                        <td>{{ $komponen['nama_produk'] }}</td>
                        <td>{{ $komponen['jumlah'] }}</td>
                        <td>{{ $komponen['kondisibaik'] }}</td>
                        <td>{{ $komponen['kondisiafkir'] }}</td>
                        <td>{{ $komponen['kondisibonggol'] }}</td>
                        <td rowspan="{{ $komponenCount }}">{{ number_format($produkJual['harga'], 0, ',', '.') }}</td>
                        <td rowspan="{{ $komponenCount }}">{{ number_format($produkJual['diskon'], 0, ',', '.') }}</td>
                        <td rowspan="{{ $komponenCount }}">{{ number_format($produkJual['jumlah_harga'], 0, ',', '.') }}</td>
                    @if($data['no_invoice'] != $previousNoDo)
                        <td style="border-bottom: none;">{{ number_format($data['sub_total'], 0, ',', '.') }}</td>
                        <td style="border-bottom: none;">{{ number_format($data['jumlah_ppn'], 0, ',', '.') }}</td>
                        <td style="border-bottom: none;">{{ number_format($data['biaya_pengiriman'], 0, ',', '.') }}</td>
                        <td style="border-bottom: none;">{{ number_format($data['total_tagihan'], 0, ',', '.') }}</td>
                        <td style="border-bottom: none;">{{ number_format($data['dp'], 0, ',', '.') }}</td>
                        <td style="border-bottom: none;">{{ number_format($data['sisa_bayar'], 0, ',', '.') }}</td>
                    @endif
                    </tr>
                @else
                    <tr>
                        @if($isGFTInFirstProdukJual)
                            <td>{{ $komponen['nama_produk'] }}</td>
                            <td>{{ $komponen['jumlah'] }}</td>
                            <td>{{ $komponen['kondisibaik'] }}</td>
                            <td>{{ $komponen['kondisiafkir'] }}</td>
                            <td>{{ $komponen['kondisibonggol'] }}</td>
                            <td style="border-right: 1px solid #000; border-top: none;"></td>
                            <td style="border-right: 1px solid #000; border-top: none;"></td>
                            <td style="border-right: 1px solid #000; border-top: none;"></td>
                            <td style="border-right: 1px solid #000; border-top: none;"></td>
                            <td style="border-right: 1px solid #000; border-top: none;"></td>
                            <td style="border-right: 1px solid #000; border-top: none;"></td>
                        @else
                            <td style="border-right: 1px solid #000; border-top: none;"></td>
                            <td style="border-right: 1px solid #000; border-top: none;"></td>
                            <td style="border-right: 1px solid #000; border-top: none;"></td>
                            <td style="border-right: 1px solid #000; border-top: none;"></td>
                            <td style="border-right: 1px solid #000; border-top: none;"></td>
                            <td style="border-right: 1px solid #000; border-top: none;"></td>
                            <td style="border-right: 1px solid #000; border-top: none;"></td>
                            <td>{{ $komponen['nama_produk'] }}</td>
                            <td>{{ $komponen['jumlah'] }}</td>
                            <td>{{ $komponen['kondisibaik'] }}</td>
                            <td>{{ $komponen['kondisiafkir'] }}</td>
                            <td>{{ $komponen['kondisibonggol'] }}</td>
                            <td style="border-right: 1px solid #000; border-top: none;"></td>
                            <td style="border-right: 1px solid #000; border-top: none;"></td>
                            <td style="border-right: 1px solid #000; border-top: none;"></td>
                            <td style="border-right: 1px solid #000; border-top: none;"></td>
                            <td style="border-right: 1px solid #000; border-top: none;"></td>
                            <td style="border-right: 1px solid #000; border-top: none;"></td>
                        @endif
                    </tr>
                @endif
                @php
                    $previousNoDo = $data['no_invoice'];
                @endphp
            @endforeach
        @endforeach
    @endforeach
    </tbody>
</table>

</body>
</html>
