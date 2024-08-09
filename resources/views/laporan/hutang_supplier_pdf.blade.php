<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Hutang Supplier</title>
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
        <p>Alamat Perusahaan</p>
    </div>
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Invoice</th>
                    <th>Supplier</th>
                    <th>Tanggal Invoice</th>
                    <th>List Barang</th>
                    <th>QTY</th>
                    <th>Tagihan</th>
                    <th>Terbayar</th>
                    <th>Sisa Tagihan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    @php
                        $rowCount = 0;
                        $produkbeli = [];
            
                        if ($item->poinden && $item->poinden->produkbeli) {
                            $produkbeli = $item->poinden->produkbeli;
                            $rowCount = count($produkbeli);
                        } elseif ($item->pembelian && $item->pembelian->produkbeli) {
                            $produkbeli = $item->pembelian->produkbeli;
                            $rowCount = count($produkbeli);
                        }
                    @endphp
            
                    @foreach ($produkbeli as $index => $produkbeliItem)
                        <tr>
                            @if ($index === 0)
                                <td rowspan="{{ $rowCount }}">{{ $loop->iteration }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $item->no_inv }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $item->supplier_nama }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $item->tgl_inv }}</td>
                            @endif
                            <td>{{ $produkbeliItem->produk->nama }}</td>
                            <td>{{ $produkbeliItem->jml_dikirim }}</td>
                            @if ($index === 0)
                                <td rowspan="{{ $rowCount }}">{{ formatRupiah($item->total_tagihan) }}</td>
                                <td rowspan="{{ $rowCount }}">{{ formatRupiah($item->terbayar) }}</td>
                                <td rowspan="{{ $rowCount }}">{{ formatRupiah($item->sisa) }}</td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
            
            <tfoot>
                <tr>
                    <th colspan="8" class="text-center">TOTAL TAGIHAN</th>
                    <th>{{ formatRupiah($totalTagihan) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>