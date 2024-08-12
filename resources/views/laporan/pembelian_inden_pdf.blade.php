<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Pembelian Inden</title>
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
        <p>Alamat Perusahaan</p>
    </div>
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th class="align-middle">No</th>
                    <th class="align-middle">No Invoice</th>
                    <th class="align-middle">Tanggal</th>
                    <th class="align-middle">List Barang</th>
                    <th class="align-middle">Harga</th>
                    <th class="align-middle">Gallery</th>
                    <th class="align-middle">Supplier</th>
                    <th class="align-middle">QTY</th>
                    <th class="align-middle">Harga Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    @php
                        $rowCount = count($item->poinden->produkbeli);
                    @endphp
                    <tr>
                        <td rowspan="{{ $rowCount }}">{{ $loop->iteration }}</td>
                        <td rowspan="{{ $rowCount }}">{{ $item->no_inv }}</td>
                        <td rowspan="{{ $rowCount }}">{{ tanggalindo($item->tgl_inv) }}</td>
                        <td>{{ $item->poinden->produkbeli[0]->produk->nama }}</td>
                        <td>{{ formatRupiah($item->poinden->produkbeli[0]->harga) }}</td>
                        <td rowspan="{{ $rowCount }}">{{ $item->poinden->lokasi->nama }}</td>
                        <td rowspan="{{ $rowCount }}">{{ $item->poinden->supplier->nama }}</td>
                        <td>{{ $item->poinden->produkbeli[0]->jml_dikirim }}</td>
                        <td>{{ formatRupiah($item->poinden->produkbeli[0]->totalharga) }}</td>
                    </tr>
                    @for ($i = 1; $i < $rowCount; $i++)
                        <tr>
                            <td>{{ $item->poinden->produkbeli[$i]->produk->nama }}</td>
                            <td>{{ formatRupiah($item->poinden->produkbeli[$i]->harga) }}</td>
                            <td>{{ $item->poinden->produkbeli[$i]->jml_dikirim }}</td>
                            <td>{{ formatRupiah($item->poinden->produkbeli[$i]->totalharga) }}</td>
                        </tr>
                    @endfor
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>