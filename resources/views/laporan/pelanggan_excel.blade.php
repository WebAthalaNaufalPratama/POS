<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Pelanggan</title>
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
            @foreach($penjualan as $item)
            <table class="table datanew">
                <thead>
                    <tr>
                        <th style="border: 1px solid #000;">No</th>
                        <th style="border: 1px solid #000;">No. Invoice</th>
                        <th style="border: 1px solid #000;">Lokasi</th>
                        <th style="border: 1px solid #000;">Tanggal Invoice</th>
                        <th style="border: 1px solid #000;">Jatuh Tempo</th>
                        <th style="border: 1px solid #000;">ID Customer</th>
                        <th style="border: 1px solid #000;">Total Tagihan</th>
                        <th style="border: 1px solid #000;">DP</th>
                        <th style="border: 1px solid #000;">Sisa Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach(explode(', ', $item->no_invoice) as $index => $invoice)
                    <tr>
                        <td style="border: 1px solid #000;">{{ $no++ }}</td>
                        <td style="border: 1px solid #000;">{{ $invoice }}</td>
                        <td style="border: 1px solid #000;">{{ isset(explode(', ', $item->lokasi->nama)[$index]) ? explode(', ', $item->lokasi->nama)[$index] : '-' }}</td>
                        <td style="border: 1px solid #000;">{{ isset(explode(', ', $item->tanggal_invoice)[$index]) ? explode(', ', $item->tanggal_invoice)[$index] : '-' }}</td>
                        <td style="border: 1px solid #000;">{{ isset(explode(', ', $item->jatuh_tempo)[$index]) ? explode(', ', $item->jatuh_tempo)[$index] : '-' }}</td>
                        <td style="border: 1px solid #000;">{{ $item->customer->nama }}</td>
                        <td style="border: 1px solid #000;">{{ isset(explode(', ', $item->total_tagihan)[$index]) ? 'Rp ' . number_format(explode(', ', $item->total_tagihan)[$index], 2, ',', '.') : 'Rp 0,00' }}</td>
                        <td style="border: 1px solid #000;">{{ isset(explode(', ', $item->dp)[$index]) ? 'Rp ' . number_format(explode(', ', $item->dp)[$index], 2, ',', '.') : 'Rp 0,00' }}</td>
                        <td style="border: 1px solid #000;">{{ isset(explode(', ', $item->sisa_bayar)[$index]) ? 'Rp ' . number_format(explode(', ', $item->sisa_bayar)[$index], 2, ',', '.') : 'Rp 0,00' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" style="text-align: right; border: 1px solid #000;">Total:</td>
                        <td style="border: 1px solid #000;">{{ 'Rp ' . number_format(array_sum(explode(', ', $item->total_tagihan)), 2, ',', '.') }}</td>
                        <td style="border: 1px solid #000;">{{ 'Rp ' . number_format(array_sum(explode(', ', $item->dp)), 2, ',', '.') }}</td>
                        <td style="border: 1px solid #000;">{{ 'Rp ' . number_format(array_sum(explode(', ', $item->sisa_bayar)), 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
            @endforeach
        </div>
    </div>
</body>
</html>
