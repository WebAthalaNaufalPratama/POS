<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Tagihan Sewa</title>
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
                    <th class="align-middle">No</th>
                    <th class="align-middle">No Kontrak</th>
                    <th class="align-middle">No Invoice</th>
                    <th class="align-middle">Customer</th>
                    <th class="align-middle">Tanggal Invoice</th>
                    <th class="align-middle">Total Nilai</th>
                    <th class="align-middle">Terbayar</th>
                    <th class="align-middle">Sisa Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->no_sewa }}</td>
                    <td>{{ $item->no_invoice }}</td>
                    <td>{{ $item->kontrak->customer->nama }}</td>
                    <td>{{ tanggalindo($item->tanggal_invoice) }}</td>
                    <td>{{ formatRupiah($item->total_tagihan) }}</td>
                    <td>{{ formatRupiah($item->terbayar) }}</td>
                    <td>{{ formatRupiah($item->sisa_bayar) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>