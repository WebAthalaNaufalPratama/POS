<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Pemakaian Sendiri</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>VONFLORIST</h1>
        <p>Alamat Perusahaan</p>
    </div>
    <div class="content">
    <div class="table-responsive">
        <table class="table datanew" id="dataTable">
            <thead>
                <tr>
                    <th class="align-middle" rowspan="2">No</th>
                    <th class="align-middle" rowspan="2">Gallery</th>
                    <th class="align-middle" rowspan="2">Produk</th>
                    <th class="align-middle" rowspan="2">Tanggal</th>
                    <th class="align-middle text-center" rowspan="2">Jumlah</th>
                    <th class="align-middle text-center" colspan="3">Kondisi</th>
                    <th class="align-middle" rowspan="2">Keterangan</th>
                </tr>
                <tr>
                    <th class="text-center">Baik</th>
                    <th class="text-center">Afkir</th>
                    <th class="text-center">Bonggol</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->gallery_nama }}</td>
                    <td>{{ $item->produk->nama }}</td>
                    <td>{{ tanggalindo($item->tanggal) }}</td>
                    <td class="text-center">{{ $item->jumlah }}</td>
                    <td class="text-center">{{ $item->jumlah_baik }}</td>
                    <td class="text-center">{{ $item->jumlah_afkir }}</td>
                    <td class="text-center">{{ $item->jumlah_bonggol }}</td>
                    <td>{{ $item->alasan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>