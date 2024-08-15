<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Kas Pusat</title>
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
        .text-end {
            text-align: right;
        }
        .d-none {
            display: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>VONFLORIST</h1>
        <h2>Laporan Kas Gallery</h2>
    </div>
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th class="d-none"></th>
                    <th class="align-middle">Periode</th>
                    <th class="align-middle text-center">Tanggal</th>
                    <th class="align-middle">Keterangan</th>
                    <th class="align-middle">Operasional</th>
                    <th class="align-middle">Debit</th>
                    <th class="align-middle">Kredit</th>
                    <th class="align-middle">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="d-none">0</td>
                    <td>{{ $thisMonth }} {{ $thisYear }}</td>
                    <td class="text-center">01</td>
                    <td>Saldo</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ formatRupiah($saldo) }}</td>
                </tr>
                @foreach ($data as $index => $item)
                <tr>
                    <td class="d-none">{{ $index + 1 }}</td>
                    <td></td>
                    <td class="text-center">{{ $item->dateNumber }}</td>
                    <td>{{ $item->keterangan }}</td>
                    @if(in_array($item->lokasi_penerima, $id_galleries))
                        <td>{{$item->lok_penerima->operasional->nama }}</td>
                        <td>{{ formatRupiah($item->nominal) }}</td>
                        <td></td>
                    @elseif(in_array($item->lokasi_pengirim, $id_galleries))
                        <td>{{$item->lok_pengirim->operasional->nama }}</td>
                        <td></td>
                        <td>{{ formatRupiah($item->nominal) }}</td>
                    @else
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif
                    <td>{{ formatRupiah($item->saldo) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="text-end">Saldo Rekening</td>
                    <td>{{ formatRupiah($saldoRekening) }}</td>
                </tr>
                <tr>
                    <td colspan="6" class="text-end">Saldo Cash</td>
                    <td>{{ formatRupiah($saldoCash) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>