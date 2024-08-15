<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Omset</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20mm;
            margin-top: 140px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
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
        ul {
            list-style-type: none; 
            padding: 0;
            margin: 0;
        }
        li {
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>VONFLORIST</h1>
        <h2>Laporan Omset</h2>
    </div>
    <div class="content">
        <table class="table datanew" id="dataTable">
            <thead>
                <tr>
                    <th class="align-middle">No</th>
                    <th class="align-middle">No Invoice</th>
                    <th class="align-middle">Marketing</th>
                    <th class="align-middle">Tipe</th>
                    <th class="align-middle">Metode</th>
                    <th class="align-middle">Tanggal Invoice</th>
                    <th class="align-middle">Customer</th>
                    <th class="align-middle">Tanggal Pembayaran</th>
                    <th class="align-middle">Barang</th>
                    <th class="align-middle">Harga</th>
                    <th class="align-middle">QTY</th>
                    <th class="align-middle">Jumlah</th>
                    <th class="align-middle">PPN</th>
                    <th class="align-middle">PPH</th>
                    <th class="align-middle">Ongkir</th>
                    <th class="align-middle">Diskon</th>
                    <th class="align-middle">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $outerIndex => $item)
                @php
                    $rowCount = 0;
                    $produk = [];
                    $produk = $item->produk;
                    $rowCount = count($produk);
                @endphp
                    @foreach ($produk as $index => $produkItem)
                    <tr>
                        @if ($index === 0)
                            <td rowspan="{{ $rowCount }}">{{ $outerIndex + 1 }}</td>
                            <td rowspan="{{ $rowCount }}">{{ $item->no_invoice }}</td>
                            <td rowspan="{{ $rowCount }}">{{ $item->nama_sales }}</td>
                            <td rowspan="{{ $rowCount }}">{{ 'Tradisional / Gift' }}</td>
                            <td rowspan="{{ $rowCount }}">{{ $item->metode }}</td>
                            <td rowspan="{{ $rowCount }}">{{ formatTanggal($item->tanggal_invoice) }}</td>
                            <td rowspan="{{ $rowCount }}">{{ $item->nama_customer }}</td>
                            @if(!$item->pembayaran->isEmpty())
                            <td rowspan="{{ $rowCount }}">
                                <ul>
                                    @foreach ($item->pembayaran as $pembayaran)
                                            <li>{{ formatTanggal($pembayaran->tanggal_bayar) }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            @else
                            <td rowspan="{{ $rowCount }}">Belum ada Pembayaran</td>
                            @endif
                        @endif
                        <td>{{ $produkItem->produk->nama }}</td>
                        <td>{{ formatRupiah($produkItem->harga_jual) }}</td>
                        <td>{{ $produkItem->jumlah }}</td>
                        @if ($index === 0)
                            <td rowspan="{{ $rowCount }}">{{ $item->jumlah }}</td>
                            <td rowspan="{{ $rowCount }}">{{ $item->ppn_nominal }}</td>
                            <td rowspan="{{ $rowCount }}">{{ $item->pph_nominal }}</td>
                            <td rowspan="{{ $rowCount }}">{{ $item->ongkir_nominal }}</td>
                            <td rowspan="{{ $rowCount }}">{{ $item->total_promo }}</td>
                            <td rowspan="{{ $rowCount }}">{{ $item->total_tagihan }}</td>
                        @endif
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>