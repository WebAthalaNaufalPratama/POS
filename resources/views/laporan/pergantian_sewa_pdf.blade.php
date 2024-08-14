<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Pergantian Sewa</title>
    <style>
        @page {
            size: A4 landscape;
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
            width: 100%;
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
        .align-middle {
            vertical-align: middle;
        }
        .text-center {
            text-align: center;
        }
        ul {
            list-style-type: none; 
            padding: 0;
            margin: 0;
        }

        li {
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>VONFLORIST</h1>
        <h2>Laporan Pergantian Sewa</h2>
    </div>
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th rowspan="2" class="align-middle">No</th>
                    <th rowspan="2" class="align-middle">Gallery</th>
                    <th rowspan="2" class="align-middle">Customer</th>
                    <th rowspan="1" colspan="2" class="align-middle text-center">Produk Sewa</th>
                    <th rowspan="2" class="align-middle text-center">Jumlah Pengiriman</th>
                    <th rowspan="2" class="align-middle text-center">Jumlah Kembali</th>
                </tr>
                <tr>
                    <th rowspan="1" class="text-center">Jumlah</th>
                    <th rowspan="1" class="text-center">Nama</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $outerIndex => $item)
                @php
                    $produkListCount = count($item['produk_list']);
                @endphp
                @foreach ($item['produk_list'] as $index => $detail)
                    <tr>
                        @if ($index === 0)
                            <td rowspan="{{ $produkListCount }}">{{ $loop->iteration }}</td>
                            <td rowspan="{{ $produkListCount }}">{{ $item['gallery'] }}</td>
                            <td rowspan="{{ $produkListCount }}">{{ $item['nama_customer'] }}</td>
                        @endif
                        <td class="text-center">{{ $detail['jumlah_sewa'] }}</td>
                        <td class="text-center">{{ $detail['nama_produk'] }}</td>
                        <td class="text-center">{{ $detail['jumlah_dikirim'] }}</td>
                        <td class="text-center">{{ $detail['jumlah_kembali'] }}</td>
                    </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>