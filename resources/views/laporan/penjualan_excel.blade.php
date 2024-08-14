<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Penjualan</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm;
            margin-top: 140px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
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
        .header h1, .header p {
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
            table-layout: auto; /* Adjust table layout to auto-fit cell content */
        }
        th, td {
            border: 1px solid #000; /* Define border color and style */
            padding: 8px;
            text-align: left;
            word-wrap: break-word; /* Ensure long words break and do not overflow */
            white-space: nowrap; /* Prevent text wrapping within cells */
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tfoot tr {
            background-color: #e6e6e6;
            font-weight: bold;
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
    <div class="header">
        <h1>VONFLORIST</h1>
        <h2>Laporan Penjualan</h2>
    </div>
    <div class="content">
        <div class="table-responsive">
        <table>
        <thead>
            <tr>
                <th >No</th>
                <th >No Invoice</th>
                <th >Galery</th>
                <th >Nama Customer</th>
                <th >Tanggal Invoice</th>
                <th >Jatuh Tempo</th>
                <th >Nama Sales</th>
                <th >Nama Produk Jual</th>
                <th >Jumlah Produk Jual</th>
                <th >Nama Produk</th>
                <th >Jumlah</th>
                <th >Kondisi (Baik)</th>
                <th >Kondisi (Afkir)</th>
                <th >Kondisi (Bonggol)</th>
                <th >Harga</th>
                <th >Diskon</th>
                <th >Jumlah Harga</th>
                <th >Sub Total</th>
                <th >PPN</th>
                <th >Biaya Pengiriman</th>
                <th >Total Tagihan</th>
                <th >DP</th>
                <th >Sisa Bayar</th>
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
        </div>
    </div>
</body>
</html>
