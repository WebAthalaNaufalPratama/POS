<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Pelanggan</title>
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
        <h2>Laporan Tagihan Pelanggan</h2>
    </div>
    <div class="content">
    <div class="table-responsive">
    @foreach($penjualan as $item)
        <table class="table datanew" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="width: 5%;">No</th>
                    <th style="width: 10%;">No. Invoice</th>
                    <th style="width: 10%;">Lokasi</th>
                    <th style="width: 10%;">Tanggal Invoice</th>
                    <th style="width: 10%;">Jatuh Tempo</th>
                    <th style="width: 10%;">ID Customer</th>
                    <th style="width: 15%;">Total Tagihan</th>
                    <th style="width: 15%;">DP</th>
                    <th style="width: 15%;">Sisa Bayar</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach(explode(', ', $item->no_invoice) as $index => $invoice)
                <tr style="background-color: {{ $index % 2 == 0 ? '#ffffff' : '#f9f9f9' }};">
                    <td style="padding: 10px; text-align: center;">{{ $no++ }}</td>
                    <td style="padding: 10px; text-align: center;">{{ $invoice }}</td>
                    <td style="padding: 10px; text-align: center;">{{ $item->lokasi->nama }}</td>
                    <td style="padding: 10px; text-align: center;">
                        {{ isset(explode(', ', $item->tanggal_invoice)[$index]) ? explode(', ', $item->tanggal_invoice)[$index] : '-' }}
                    </td>
                    <td style="padding: 10px; text-align: center;">
                        {{ isset(explode(', ', $item->jatuh_tempo)[$index]) ? explode(', ', $item->jatuh_tempo)[$index] : '-' }}
                    </td>
                    <td style="padding: 10px; text-align: center;">{{ $item->customer->nama }}</td>
                    <td style="padding: 10px; text-align: center;">
                        {{ isset(explode(', ', $item->total_tagihan)[$index]) ? 'Rp ' . number_format(explode(', ', $item->total_tagihan)[$index], 2, ',', '.') : 'Rp 0,00' }}
                    </td>
                    <td style="padding: 10px; text-align: center;">
                        {{ isset(explode(', ', $item->dp)[$index]) ? 'Rp ' . number_format(explode(', ', $item->dp)[$index], 2, ',', '.') : 'Rp 0,00' }}
                    </td>
                    <td style="padding: 10px; text-align: center;">
                        {{ isset(explode(', ', $item->sisa_bayar)[$index]) ? 'Rp ' . number_format(explode(', ', $item->sisa_bayar)[$index], 2, ',', '.') : 'Rp 0,00' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #e6e6e6;">
                    <td colspan="6" style="text-align: right; padding: 10px; font-weight: bold;">Total:</td>
                    <td style="padding: 10px; text-align: center;">
                        {{ 'Rp ' . number_format(array_sum(explode(', ', $item->total_tagihan)), 2, ',', '.') }}
                    </td>
                    <td style="padding: 10px; text-align: center;">
                        {{ 'Rp ' . number_format(array_sum(explode(', ', $item->dp)), 2, ',', '.') }}
                    </td>
                    <td style="padding: 10px; text-align: center;">
                        {{ 'Rp ' . number_format(array_sum(explode(', ', $item->sisa_bayar)), 2, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    @endforeach

    </div>
</body>
</html>