<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Retur Penjualan</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>VONFLORIST</h1>
        <h2>Laporan Retur Penjualan</h2>
    </div>
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">No</th>
                    <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">No Retur</th>
                    <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">No Invoice</th>
                    <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Tanggal Invoice</th>
                    <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Tanggal Retur</th>
                    <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Nama Customer</th>
                    <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Penanganan Komplain</th>
                    <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Supplier</th>
                    <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Galery</th>
                    <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Nama Sales</th>
                    <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Nama Produk</th>
                    <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Komplain Kerusakan</th>
                    <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">QTY</th>
                    <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Harga Jual</th>
                    <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Jumlah Harga</th>
                    <th style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">Jumlah Diskon</th>
                </tr>
            </thead>
            <tbody>
                @foreach($combinedData as $data)
                    @php
                        $retur = $data['retur'];
                        $produkterjual = $data['produkterjual'];
                        $penjualan = $data['penjualan'];
                    @endphp

                    @foreach($produkterjual as $produk)
                    @if ($loop->first)
                        <tr>
                            <td rowspan="{{ $produkterjual->count() }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{  $loop->parent->iteration  }}</td>
                            <td rowspan="{{ $produkterjual->count() }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{  $retur->no_retur  }}</td>
                            <td rowspan="{{ $produkterjual->count() }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{  $retur->no_invoice  }}</td>
                            <td rowspan="{{ $produkterjual->count() }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{  $retur->tanggal_invoice  }}</td>
                            <td rowspan="{{ $produkterjual->count() }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{  $retur->tanggal_retur  }}</td>
                            <td rowspan="{{ $produkterjual->count() }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{  $retur->customer->nama  }}</td>
                            <td rowspan="{{ $produkterjual->count() }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{  $retur->komplain  }}</td>
                            <td rowspan="{{ $produkterjual->count() }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{  $retur->supplier->nama  }}</td>
                            <td rowspan="{{ $produkterjual->count() }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{  $retur->lokasi->nama  }}</td>
                            <td rowspan="{{ $produkterjual->count() }}" style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{  $penjualan->firstWhere('no_invoice', $retur->no_invoice)->karyawan->nama  }}</td>
                            <td style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ $produk->produk->nama }}</td>
                            <td style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ $produk->alasan }}</td>
                            <td style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ $produk->jumlah }}</td>
                            <td style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ 'Rp ' . number_format($produk->harga_jual, 2, ',', '.') }}</td>
                            <td style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ 'Rp ' . number_format($produk->jumlah * $produk->harga_jual, 2, ',', '.') }}</td>
                            <td style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ 'Rp ' . number_format($produk->diskon, 2, ',', '.') }}</td>
                        </tr>
                    @else
                    <tr>
                    <td style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ $produk->produk->nama }}</td>
                            <td style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ $produk->alasan }}</td>
                            <td style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ $produk->jumlah }}</td>
                            <td style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ 'Rp ' . number_format($produk->harga_jual, 2, ',', '.') }}</td>
                            <td style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ 'Rp ' . number_format($produk->jumlah * $produk->harga_jual, 2, ',', '.') }}</td>
                            <td style="border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ 'Rp ' . number_format($produk->diskon, 2, ',', '.') }}</td>
                    </tr>
                    @endif
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
