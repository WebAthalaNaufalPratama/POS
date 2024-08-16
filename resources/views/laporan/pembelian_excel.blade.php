<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Pembelian</title>
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
        <h2>Laporan Pembelian</h2>
    </div>
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th class="align-middle">No</th>
                    <th class="align-middle">No Invoice</th>
                    <th class="align-middle">Tanggal</th>
                    <th class="align-middle">Gallery</th>
                    <th class="align-middle">Supplier</th>
                    <th class="align-middle">List Barang</th>
                    <th class="align-middle">Harga</th>
                    <th class="align-middle">Diskon</th>
                    <th class="align-middle">QTY</th>
                    <th class="align-middle">Harga Total</th>
                    <th class="align-middle">PPN</th>
                    <th class="align-middle">Ongkir</th>
                    <th class="align-middle">Komplain</th>
                    <th class="align-middle">Nominal Komplain</th>
                    <th class="align-middle">Ongkir Komplain</th>
                    <th class="align-middle">Total Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index1 => $item)
                    @php
                        $rowCount = count($item->pembelian->produkbeli);
                    @endphp
                    
                    @foreach ($item->pembelian->produkbeli as $index => $produkItem)
                        <tr>
                            @if ($index === 0)
                                <td rowspan="{{ $rowCount }}">{{ $index1 + 1 }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $item->no_inv }}</td>
                                <td rowspan="{{ $rowCount }}">{{ tanggalindo($item->tgl_inv) }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $item->pembelian->lokasi->nama }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $item->pembelian->supplier->nama }}</td>
                            @endif
                            <td>{{ $produkItem->produk->nama }}</td>
                            <td>{{ formatRupiah($produkItem->harga) }}</td>
                            <td>{{ formatRupiah($produkItem->diskon) }}</td>
                            <td>{{ $produkItem->jml_diterima }}</td>
                            <td>{{ formatRupiah(($produkItem->harga - $produkItem->diskon) * $produkItem->jml_diterima )}}</td>
                            @if ($index === 0)
                                <td rowspan="{{ $rowCount }}">{{ formatRupiah($item->ppn) }}</td>
                                <td rowspan="{{ $rowCount }}">{{ formatRupiah($item->biaya_kirim) }}</td>
                                @if ($item->retur && $item->retur->status_dibuat == "DIKONFIRMASI")
                                    <td rowspan="{{ $rowCount }}">{{ $item->retur->komplain }}</td>
                                    <td rowspan="{{ $rowCount }}">{{ formatRupiah($item->retur->subtotal) }}</td>
                                    <td rowspan="{{ $rowCount }}">{{ formatRupiah($item->retur->ongkir) }}</td>
                                @else
                                    <td rowspan="{{ $rowCount }}">-</td>
                                    <td rowspan="{{ $rowCount }}">-</td>
                                    <td rowspan="{{ $rowCount }}">-</td>
                                @endif

                                <td rowspan="{{ $rowCount }}">{{ formatRupiah($item->total_tagihan) }}</td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>