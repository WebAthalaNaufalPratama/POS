<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Kontrak</title>
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
            color: #000000;
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
            font-weight: bold;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>VONFLORIST</h1>
        <p>Alamat Perusahaan</p>
    </div>
    <div class="content">
        <div class="table-responsive">
            <table class="table datanew">
                <thead>
                    <tr>
                        <th style="border: 1px solid #000;">No</th>
                        <th style="border: 1px solid #000;">Nama Produk</th>
                        <th style="border: 1px solid #000;">Group</th>
                        <th style="border: 1px solid #000;">Jumlah</th>
                        <th style="border: 1px solid #000;">Sub Total (Sebelum promo)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produkterjual as $index => $pj)
                        @php
                            $pojuCollection = collect($pojuList);
                            $matchingPoju = $pojuCollection->firstWhere('id', $pj->produk_jual_id);
                        @endphp
                        <tr>
                            <td style="border: 1px solid #000;">{{ $loop->iteration }}</td>
                            <td style="border: 1px solid #000;">{{ $matchingPoju ? $matchingPoju->nama : 'N/A' }}</td>
                            <td style="border: 1px solid #000;">{{ $matchingPoju ? $matchingPoju->tipe->nama : 'N/A' }}</td>
                            <td style="border: 1px solid #000;">{{ $pj->jumlah }}</td>
                            <td style="border: 1px solid #000;">{{ number_format($pj->harga_jual, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
