<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Kontrak</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20mm;
            margin-top: 100px; /* Adjusted to ensure space for header */
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .header {
            position: fixed;
            top: 10px;
            left: 0;
            right: 0;
            height: 60px;
            text-align: center;
            line-height: 50px;
            background-color: #f2f2f2;
            padding: 10px 0;
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
        td.nested-table {
            white-space: pre-wrap; /* Ensures long text wraps correctly */
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
        <table>
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Customer</th>
                    <th rowspan="2">Masa Sewa</th>
                    <th colspan="2">Tanggal Kontrak</th>
                    <th rowspan="2">Produk Sewa</th>
                    <th rowspan="2">Jumlah</th>
                    <th rowspan="2">Harga Satuan</th>
                    <th rowspan="2">Total Harga</th>
                    <th rowspan="2">PPN</th>
                    <th rowspan="2">PPH</th>
                    <th rowspan="2">Total Yang Diterima</th>
                    <th rowspan="2">Status</th>
                </tr>
                <tr>
                    <th>Awal Sewa</th>
                    <th>Akhir Sewa</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->customer->nama }}</td>
                    <td>{{ $item->masa_sewa }} bulan</td>
                    <td>{{ tanggalindo($item->tanggal_mulai) }}</td>
                    <td>{{ tanggalindo($item->tanggal_selesai) }}</td>
                    <td class="nested-table">
                        @foreach ($item->produk as $produk)
                            {{ $produk->produk->nama }}<br>
                        @endforeach
                    </td>
                    <td class="nested-table">
                        @foreach ($item->produk as $produk)
                            {{ $produk->jumlah }}<br>
                        @endforeach
                    </td>
                    <td class="nested-table">
                        @foreach ($item->produk as $produk)
                            {{ formatRupiah($produk->harga) }}<br>
                        @endforeach
                    </td>
                    <td>{{ formatRupiah($item->subtotal) }}</td>
                    <td>{{ formatRupiah($item->ppn_nominal) }} ({{ $item->ppn_persen }}%)</td>
                    <td>{{ formatRupiah($item->pph_nominal) }} ({{ $item->pph_persen }}%)</td>
                    <td>{{ formatRupiah($item->total_harga) }}</td>
                    <td>{{ $item->status_kontrak }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>