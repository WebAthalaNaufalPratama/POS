<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Delivery Order Sewa</title>
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
                    <th rowspan="2" class="align-middle">No</th>
                    <th rowspan="2" class="align-middle">No DO</th>
                    <th rowspan="2" class="align-middle">Gallery</th>
                    <th rowspan="2" class="align-middle">No Sewa</th>
                    <th rowspan="2" class="align-middle">Masa Sewa</th>
                    <th rowspan="2" class="align-middle">Driver</th>
                    <th rowspan="2" class="align-middle">Nama Produk Jual</th>
                    <th rowspan="2" class="align-middle">Jumlah Produk Jual</th>
                    <th rowspan="2" class="align-middle">Nama Produk</th>
                    <th rowspan="1" colspan="3" class="text-center">Kondisi</th>
                    <th rowspan="2" class="align-middle">Unit Satuan</th>
                    <th rowspan="2" class="align-middle">Unit Detail Lokasi</th>
                </tr>
                <tr>
                    <th>Bagus</th>
                    <th>Afkir</th>
                    <th>Bonggol</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->no_do }}</td>
                    <td>{{ $item->kontrak->lokasi->nama }}</td>
                    <td>{{ $item->no_referensi }}</td>
                    <td>{{ $item->kontrak->masa_sewa }} bulan</td>
                    <td>{{ $item->data_driver->nama }}</td>
                    <td>
                        <table>
                            @foreach ($item->produk as $produk_terjual)
                                <tr>
                                    <td>{{ $produk_terjual->produk->nama }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                    <td>
                        <table>
                            @foreach ($item->produk as $produk_terjual)
                                <tr>
                                    <td>{{ $produk_terjual->jumlah }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                    <td>
                        <table>
                            @foreach ($item->produk as $produk_terjual)
                                @foreach ($produk_terjual->komponen as $komponen)
                                    <tr>
                                        <td>{{ $komponen->produk->nama }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </table>
                    </td>
                    <td>
                        <table>
                            @foreach ($item->produk as $produk_terjual)
                                @foreach ($produk_terjual->komponen as $komponen)
                                    <tr>
                                        @if($komponen->data_kondisi->nama == 'Baik')
                                            <td>{{ $komponen->jumlah }}</td>
                                        @else
                                            <td>0</td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endforeach
                        </table>
                    </td>
                    <td>
                        <table>
                            @foreach ($item->produk as $produk_terjual)
                                @foreach ($produk_terjual->komponen as $komponen)
                                    <tr>
                                        @if($komponen->data_kondisi->nama == 'Afkir')
                                            <td>{{ $komponen->jumlah }}</td>
                                        @else
                                            <td>0</td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endforeach
                        </table>
                    </td>
                    <td>
                        <table>
                            @foreach ($item->produk as $produk_terjual)
                                @foreach ($produk_terjual->komponen as $komponen)
                                    <tr>
                                        @if($komponen->data_kondisi->nama == 'Bonggol')
                                            <td>{{ $komponen->jumlah }}</td>
                                        @else
                                            <td>0</td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endforeach
                        </table>
                    </td>
                    <td>
                        <table>
                            @foreach ($item->produk as $produk_terjual)
                                <tr>
                                    <td>{{ $produk_terjual->satuan }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                    <td>
                        <table>
                            @foreach ($item->produk as $produk_terjual)
                                <tr>
                                    <td>{{ $produk_terjual->detail_lokasi }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>