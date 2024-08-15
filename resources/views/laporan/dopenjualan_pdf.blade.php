<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Delivery Order Penjualan</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20mm;
            margin-top: 140px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
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
            padding: 8px;
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
    <div class="header">
        <h1>VONFLORIST</h1>
        <h2>Laporan Delivery Order Penjualan</h2>
    </div>
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th rowspan="2">No DO</th>
                    <th rowspan="2">Lokasi Pengirim</th>
                    <th rowspan="2">Customer</th>
                    <th rowspan="2">Penerima</th>
                    <th rowspan="2">Tanggal Kirim</th>
                    <th rowspan="2">Tanggal Invoice</th>
                    <th colspan="4">Produk Jual</th>
                    <th colspan="5">Komponen</th>
                </tr>
                <tr class="group-header">
                    <th>Nama Produk Jual</th>
                    <th>Jumlah Produk Jual</th>
                    <th>Unit Satuan</th>
                    <th>Keterangan</th>
                    <th>Nama Produk</th>
                    <th>Jumlah</th>
                    <th>Baik</th>
                    <th>Afkir</th>
                    <th>Bonggol</th>
                </tr>
                
            </thead>
            <tbody>
                @php
                    $previousNoDo = '';
                @endphp
                @foreach($combinedData as $data)
                    @php
                        $produkCount = $data['produk_jual']->count();
                        $firstProdukJual = $data['produk_jual']->first();
                        $isGFTInFirstProdukJual = substr($firstProdukJual['kodeprod'], 0, 3) === 'GFT';
                    @endphp
                    @foreach($data['produk_jual'] as $produk)
                        @php
                            $komponenCount = $produk['komponen']->count();
                        @endphp
                        @foreach($produk['komponen'] as $komponen)
                            @if($loop->first)
                                <tr>
                                    @if($data['no_do']!= $previousNoDo)
                                        <td rowspan="{{ $produkCount * $komponenCount }}" style="border-bottom: none;">{{ $data['no_do'] }}</td>
                                        <td rowspan="{{ $produkCount * $komponenCount }}" style="border-bottom: none;">{{ $data['lokasi_pengirim'] }}</td>
                                        <td rowspan="{{ $produkCount * $komponenCount }}" style="border-bottom: none;">{{ $data['customer']->nama }}</td>
                                        <td rowspan="{{ $produkCount * $komponenCount }}" style="border-bottom: none;">{{ $data['penerima'] }}</td>
                                        <td rowspan="{{ $produkCount * $komponenCount }}" style="border-bottom: none;">{{ $data['tanggal_kirim'] }}</td>
                                        <td rowspan="{{ $produkCount * $komponenCount }}" style="border-bottom: none;">{{ $data['tanggal_invoice']?? '-' }}</td>
                                    @endif
                                    <td rowspan="{{ $komponenCount }}">{{ $produk['nama_produkjual'] }}</td>
                                    <td rowspan="{{ $komponenCount }}">{{ $produk['jumlahprodukjual'] }}</td>
                                    <td rowspan="{{ $komponenCount }}">{{ $produk['unitsatuan'] }}</td>
                                    <td rowspan="{{ $komponenCount }}">{{ $produk['keterangan'] }}</td>
                                    <td>{{ $komponen['nama_produk'] }}</td>
                                    <td>{{ $komponen['jumlah'] }}</td>
                                    <td>{{ $komponen['kondisibaik'] }}</td>
                                    <td>{{ $komponen['kondisiafkir'] }}</td>
                                    <td>{{ $komponen['kondisibonggol'] }}</td>
                                </tr>
                            @else
                                <tr>
                                @if($isGFTInFirstProdukJual)
                                    <td>{{ $komponen['nama_produk'] }}</td>
                                    <td>{{ $komponen['jumlah'] }}</td>
                                    <td>{{ $komponen['kondisibaik'] }}</td>
                                    <td>{{ $komponen['kondisiafkir'] }}</td>
                                    <td>{{ $komponen['kondisibonggol'] }}</td>
                                @else
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
                                @endif
                                </tr>
                            @endif
                            @php
                                $previousNoDo = $data['no_do'];
                            @endphp
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
