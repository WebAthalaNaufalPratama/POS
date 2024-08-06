<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Stok Inden</title>
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
        .d-none {
            display: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>VONFLORIST</h1>
        <p>Alamat Perusahaan</p>
    </div>
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th class="d-none"></th>
                    <th>BULAN</th>
                    @foreach ($produk as $key1 => $value1)
                        <th class="text-center">{{ $value1 }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $value)
                <tr>
                    <td class="d-none">{{ $loop->iteration }}</td>
                    <td>{{ $key }}</td>
                    @foreach ($value as $key1 => $item1)
                        <td class="text-center">{{ $item1 }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="d-none"></th>
                    <th>TOTAL</th>
                    @foreach ($produk as $key1 => $value1)
                        <th class="text-center">{{ $total[$value1] ?? 0 }}</th>
                    @endforeach
                </tr>
                <tr>
                    <th class="d-none"></th>
                    <th>TOTAL SISA BUNGA</th>
                    <th class="text-center" colspan="{{ count($produk) }}">{{ $totalSisaBunga }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>