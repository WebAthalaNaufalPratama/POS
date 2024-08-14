<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Stok Gallery</title>
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
        .header h1, .header p {
            margin: 0;
            text-align: center;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>VONFLORIST</h1>
        <h2>Laporan Stok Gallery</h2>
    </div>
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th colspan="2" class="text-center">{{ ucfirst($lokasi->nama) }}</th>
                    <th colspan="1" class="text-center">{{ \Carbon\Carbon::parse($listDate[0])->locale('id')->translatedFormat('F') }}</th>
                </tr>
                <tr>
                    <th rowspan="2" class="align-middle">No</th>
                    <th rowspan="2" class="align-middle">Produk</th>
                    <th rowspan="2" class="align-middle">Saldo Awal</th>
                    @foreach ($listDate as $date)
                        <th colspan="4" class="text-center">{{ \Carbon\Carbon::parse($date)->day }}</th>
                    @endforeach
                    <th colspan="3" class="text-center">Total AKhir</th>
                </tr>
                <tr>

                    @foreach ($listDate as $date)
                        <th class="text-center">M</th>
                        <th class="text-center">K</th>
                        <th class="text-center">R</th>
                        <th class="text-center">S</th>
                    @endforeach
                    <th class="text-center">M</th>
                    <th class="text-center">K</th>
                    <th class="text-center">R</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->nama }}</td>
                        <td>0</td>
                        @foreach ($listDate as $date)
                            <td class="text-success">{{ $item->dates[$date]['stok_masuk'] == 0 ? '' :  $item->dates[$date]['stok_masuk'] }}</td>
                            <td class="text-danger">{{ $item->dates[$date]['stok_keluar'] == 0 ? '' :  $item->dates[$date]['stok_keluar'] }}</td>
                            <td class="text-warning">{{ $item->dates[$date]['stok_retur'] == 0 ? '' :  $item->dates[$date]['stok_retur'] }}</td>
                            <td class="text-primary">{{ $item->dates[$date]['saldo'] }}</td>
                        @endforeach
                        <td>{{ $item->totalMasuk }}</td>
                        <td>{{ $item->totalKeluar }}</td>
                        <td>{{ $item->totalRetur }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>