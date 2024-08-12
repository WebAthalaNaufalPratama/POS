<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan DO Penjualan</title>
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
        <p>Alamat Perusahaan</p>
    </div>
    <div class="content">
        <div class="table-responsive">
        <table>
        <thead>
            <tr>
                <th rowspan="2" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">No DO</th>
                <th rowspan="2" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Lokasi Pengirim</th>
                <th rowspan="2" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Customer</th>
                <th rowspan="2"style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Penerima</th>
                <th rowspan="2" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Tanggal Kirim</th>
                <th rowspan="2" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Tanggal Invoice</th>
                <th colspan="4" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Produk Jual</th>
                <th colspan="5" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Komponen</th>
            </tr>
            <tr class="group-header">
                <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Nama Produk Jual</th>
                <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Jumlah Produk Jual</th>
                <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Unit Satuan</th>
                <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Keterangan</th>
                <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Nama Produk</th>
                <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Jumlah</th>
                <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Baik</th>
                <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Afkir</th>
                <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Bonggol</th>
            </tr>
            
        </thead>
        <tbody>
        @php
            $previousNoDo = '';
        @endphp
        @foreach($combinedData as $data)
            @php
                $produkCount = $data['produk_jual']->count();
            @endphp
            @foreach($data['produk_jual'] as $produk)
                @php
                    $komponenCount = $produk['komponen']->count();
                @endphp
                @foreach($produk['komponen'] as $komponen)
                    @if($loop->first)
                        <tr>
                            @if($data['no_do']!= $previousNoDo)
                                <td rowspan="{{ $produkCount * $komponenCount }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: none;">{{ $data['no_do'] }}</td>
                                <td rowspan="{{ $produkCount * $komponenCount }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: none;">{{ $data['lokasi_pengirim'] }}</td>
                                <td rowspan="{{ $produkCount * $komponenCount }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: none;">{{ $data['customer']->nama }}</td>
                                <td rowspan="{{ $produkCount * $komponenCount }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: none;">{{ $data['penerima'] }}</td>
                                <td rowspan="{{ $produkCount * $komponenCount }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: none;">{{ $data['tanggal_kirim'] }}</td>
                                <td rowspan="{{ $produkCount * $komponenCount }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: none;">{{ $data['tanggal_invoice']?? '-' }}</td>
                            @endif
                            <td rowspan="{{ $komponenCount }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ $produk['nama_produkjual'] }}</td>
                            <td rowspan="{{ $komponenCount }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ $produk['jumlahprodukjual'] }}</td>
                            <td rowspan="{{ $komponenCount }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ $produk['unitsatuan'] }}</td>
                            <td rowspan="{{ $komponenCount }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ $produk['keterangan'] }}</td>
                            <td style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ $komponen['nama_produk'] }}</td>
                            <td style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ $komponen['jumlah'] }}</td>
                            <td style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ $komponen['kondisibaik'] }}</td>
                            <td style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ $komponen['kondisiafkir'] }}</td>
                            <td style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ $komponen['kondisibonggol'] }}</td>
                        </tr>
                    @else
                        <tr>
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
    </div>
</body>
</html>
