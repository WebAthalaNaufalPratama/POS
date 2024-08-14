<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Promo</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>VONFLORIST</h1>
        <h2>Laporan Promo</h2>
    </div>
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th class="align-middle">No</th>
                    <th class="align-middle">No Invoice</th>
                    <th class="align-middle">Tanggal invoice</th>
                    <th class="align-middle">Customer</th>
                    <th class="align-middle">Sales</th>
                    <th class="align-middle">Nominal Invoice</th>
                    <th class="align-middle">Jenis Diskon</th>
                    <th class="align-middle">Total Diskon</th>
                    <th class="align-middle">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->no_invoice }}</td>
                    <td>{{ formatTanggal($item->tanggal_invoice) }}</td>
                    <td>{{ $item->customer->nama }}</td>
                    <td>{{ $item->karyawan->nama }}</td>
                    <td>{{ formatRupiah($item->sub_total) }}</td>
                    <td>{{ $item->promo->nama }}</td>
                    <td>{{ formatRupiah($item->total_promo) }}</td>
                    <td>{{ formatRupiah($item->total_with_diskon) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>