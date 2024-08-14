<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Pembayaran</title>
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
        td {
            vertical-align: top;
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
        <h2>Laporan Pembayaran</h2>
    </div>
    <div class="content">
        <div class="table-responsive">
            <table class="table datanew">
                <thead>
                    <tr>
                        <th style="border: 1px solid #000;">No</th>
                        <th style="border: 1px solid #000;">Cara Pembayaran</th>
                        <th style="border: 1px solid #000;">Nama Akun</th>
                        <th style="border: 1px solid #000;">Jumlah Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach ($duit as $rekening_id => $total_nominal)
                    @php
                        $penj = $pembayaran->firstWhere('rekening_id', $rekening_id);
                        $lokasi = \App\Models\Penjualan::firstWhere('id', $penj->invoice_penjualan_id);
                    @endphp
                    <tr>
                        <td style="border: 1px solid #000;">{{ $no++ }}</td>
                        <td style="border: 1px solid #000;">{{ $lokasi->lokasi->nama}}</td>
                        <td style="border: 1px solid #000;">
                            {{ $pembayaran->firstWhere('rekening_id', $rekening_id)->cara_bayar ?? 'Cash' }} 
                            @if(!empty($rekening_id)) 
                                ({{ $pembayaran->firstWhere('rekening_id', $rekening_id)->rekening->bank }})
                            @endif
                        </td>
                        <td style="border: 1px solid #000;">
                            @if(!empty($rekening_id))
                                {{ $pembayaran->firstWhere('rekening_id', $rekening_id)->rekening->nama_akun ?? 'Unknown' }}
                            @else
                                Pembayaran Cash
                            @endif
                        </td>
                        <td style="border: 1px solid #000;">{{ 'Rp ' . number_format($total_nominal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
