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
            font-size: 10px;
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
            width: 90%;
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
        <p>Alamat Perusahaan</p>
    </div>
    <div class="content">
        <div class="table-responsive">
            @foreach($combinedData as $data)
                @php
                    $retur = $data['retur'];
                    $produkterjual = $data['produkterjual'];
                    $penjualan = $data['penjualan'];
                @endphp

                <table class="table datanew">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 5%;">No Retur</th>
                            <th style="width: 5%;">No Invoice</th>
                            <th style="width: 5%;">Tanggal Invoice</th>
                            <th style="width: 5%;">Tanggal Retur</th>
                            <th style="width: 5%;">Nama Customer</th>
                            <th style="width: 5%;">Penanganan Komplain</th>
                            <th style="width: 5%;">Supplier</th>
                            <th style="width: 5%;">Galery</th>
                            <th style="width: 5%;">Nama Sales</th>
                            <th style="width: 5%;">Nama Produk</th>
                            <th style="width: 5%;">Komplain Kerusakan</th>
                            <th style="width: 5%;">QTY</th>
                            <th style="width: 5%;">Harga Jual</th>
                            <th style="width: 5%;">Jumlah Harga</th>
                            <th style="width: 5%;">Jumlah Diskon</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        <tr>
                            <td rowspan="{{ $produkterjual->count() }}">{{ $no++ }}</td>
                            <td rowspan="{{ $produkterjual->count() }}">{{ $retur->no_retur }}</td>
                            <td rowspan="{{ $produkterjual->count() }}">{{ $retur->no_invoice }}</td>
                            <td rowspan="{{ $produkterjual->count() }}">{{ $retur->tanggal_invoice }}</td>
                            <td rowspan="{{ $produkterjual->count() }}">{{ $retur->tanggal_retur }}</td>
                            <td rowspan="{{ $produkterjual->count() }}">{{ $retur->customer->nama }}</td>
                            <td rowspan="{{ $produkterjual->count() }}">{{ $retur->komplain }}</td>
                            <td rowspan="{{ $produkterjual->count() }}">{{ $retur->supplier->nama }}</td>
                            <td rowspan="{{ $produkterjual->count() }}">{{ $retur->lokasi->nama }}</td>
                            <td rowspan="{{ $produkterjual->count() }}">
                                @foreach ($penjualan as $pen)
                                    @if ($pen->no_invoice == $retur->no_invoice)
                                        {{ $pen->karyawan->nama }}
                                    @endif
                                @endforeach
                            </td>
                            @foreach ($produkterjual as $produk)
                                @if ($loop->first)
        
                                        <td>{{ $produk->produk->nama }}</td>
                                        <td>{{ $produk->alasan }}</td>
                                        <td>{{ $produk->jumlah }}</td>
                                        <td>{{ 'Rp ' . number_format($produk->harga_jual, 2, ',', '.') }}</td>
                                        <td>{{ 'Rp ' . number_format($produk->jumlah * $produk->harga_jual, 2, ',', '.') }}</td>
                                        <td>{{ 'Rp ' . number_format($produk->diskon, 2, ',', '.') }}</td>
                    
                                @else
                                    <tr>
                                        <td>{{ $produk->produk->nama }}</td>
                                        <td>{{ $produk->alasan }}</td>
                                        <td>{{ $produk->jumlah }}</td>
                                        <td>{{ 'Rp ' . number_format($produk->harga_jual, 2, ',', '.') }}</td>
                                        <td>{{ 'Rp ' . number_format($produk->jumlah * $produk->harga_jual, 2, ',', '.') }}</td>
                                        <td>{{ 'Rp ' . number_format($produk->diskon, 2, ',', '.') }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tr>
                    </tbody>
                </table>

                <div class="row-separator"></div>
            @endforeach
        </div>
    </div>
</body>
</html>
