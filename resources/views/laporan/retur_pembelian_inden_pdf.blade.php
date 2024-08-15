<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Retur Pembelian Inden</title>
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
        {{-- <p>Alamat : {{ Auth::user()->karyawans->lokasi->alamat }}</p> --}}
        <h2>Laporan Retur Inden</h2>
    </div>
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Komplain</th>
                    <th>No Retur</th>
                    <th>No Mutasi</th>
                    <th>Tipe Komplain</th>
                    <th>Alasan</th>
                    <th>Kode Inden</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>QTY</th>
                    <th>Total</th>
                    <th>Supplier</th>
                    <th>Tujuan</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($returs as $index1 => $item)
                    @php
                        $rowCount = 0;
                        $produkreturinden = [];
            
                        $produkreturinden = $item->produkreturinden;
                        $rowCount = count($produkreturinden);
                    @endphp
                    
                    @foreach ($produkreturinden as $index => $produkreturItem)
                        <tr>
                            @if ($index === 0)
                                <td rowspan="{{ $rowCount }}">{{ $index1 + 1 }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $item->tgl_dibuat }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $item->no_retur }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $item->mutasiinden->no_mutasi }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $item->tipe_komplain }}</td>
                            @endif
                            <td>{{ $produkreturItem->alasan }}</td>
                            <td>{{ $produkreturItem->produk->produk->kode_produk_inden }}</td>
                            <td>{{ $produkreturItem->produk->produk->produk->nama}}</td>
                            <td>{{ formatRupiah($produkreturItem->harga_satuan)}}</td>
                            <td>{{ $produkreturItem->jml_diretur }}</td>
                            @if ($index === 0)
                                <td rowspan="{{ $rowCount }}">{{ formatRupiah($item->refund) }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $item->mutasiinden->supplier->nama }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $item->mutasiinden->lokasi->nama }}</td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>