<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Data Produk Gift</title>
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
        <h2>Data Produk Gift</h2>
    </div>
    <div class="content">
        <table class="table datanew" id="dataTable">
            <thead>
                <tr>
                    <th rowspan="2" class="align-middle">No</th>
                    <th rowspan="2" class="align-middle">Nama</th>
                    <th rowspan="2" class="align-middle">Tipe Produk</th>
                    <th rowspan="2" class="align-middle">Deskripsi</th>
                    <th class="text-center align-middle" colspan="8">Komponen</th>
                    <th rowspan="2" class="align-middle">Harga Pokok</th>
                    <th rowspan="2" class="align-middle">Harga Jual</th>
                </tr>
                <tr>
                    <th class="text-center align-middle">Kode</th>
                    <th class="text-center align-middle">Nama</th>
                    <th class="text-center align-middle">Tipe</th>
                    <th class="text-center align-middle">Kondisi</th>
                    <th class="text-center align-middle">Deskripsi</th>
                    <th class="text-center align-middle">Jumlah</th>
                    <th class="text-center align-middle">Harga Satuan</th>
                    <th class="text-center align-middle">Harga Harga Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $outerIndex => $item)
                    @php
                        $rowCount = 0;
                        $produk = [];
                        $produk = $item->komponen;
                        $rowCount = count($produk);
                    @endphp
                    @foreach ($produk as $index => $produkItem)
                        <tr>
                        @if ($index === 0)
                            <td rowspan="{{ $rowCount }}">{{ $outerIndex + 1 }}</td>
                            <td rowspan="{{ $rowCount }}">{{ $item->nama }}</td>
                            <td rowspan="{{ $rowCount }}">{{ $item->tipe_value }}</td>
                            <td rowspan="{{ $rowCount }}">{{ $item->deskripsi }}</td>
                        @endif
                            <td>{{ $produkItem->kode_produk }}</td>
                            <td>{{ $produkItem->nama_produk }}</td>
                            <td>{{ $produkItem->tipe->nama }}</td>
                            <td>{{ $produkItem->dataKondisi->nama }}</td>
                            <td>{{ $produkItem->deskripsi }}</td>
                            <td>{{ $produkItem->jumlah }}</td>
                            <td>{{ formatRupiah($produkItem->harga_satuan) }}</td>
                            <td>{{ formatRupiah($produkItem->harga_total) }}</td>
                        @if ($index === 0)
                            <td rowspan="{{ $rowCount }}">{{ formatRupiah($item->harga) }}</td>
                            <td rowspan="{{ $rowCount }}">{{ formatRupiah($item->harga_jual) }}</td>
                        @endif
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>