<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Mutasi Inden</title>
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
        <h2>Laporan Mutasi Inden</h2>
    </div>
    <div class="content">
        <div class="table-responsive">
        <table class="table datanew">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Mutasi</th>
                    <th>Nama Pengirim</th>
                    <th>Nama Penerima</th>
                    <th>Tanggal Pengiriman</th>
                    <th>Tanggal Diterima</th>
                    <th>Bulan</th>
                    <th>Kategori</th>
                    <th>Jumlah Pengiriman</th>
                    <th>Biaya Perawatan</th>
                    <th>Sub Total Biaya Perawatan</th>
                    <th>Jumlah Diterima</th>
                    <th>Kondisi Diterima</th>
                    <th>Biaya Pengiriman</th>
                    <th>Rekening Bank</th>
                    <th>Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($mutasiindenRecords as $mutasi)
                    @php
                        $produkMutasi = $produkterjual->where('mutasiinden_id', $mutasi->id);
                        $produkCount = $produkMutasi->count();
                    @endphp
                    @if ($produkMutasi->isNotEmpty())
                        @foreach ($produkMutasi as $produk)
                            <tr>
                                @if ($loop->first)
                                    <td rowspan="{{ $produkCount }}">{{ $no++ }}</td>
                                    <td rowspan="{{ $produkCount }}">{{ $mutasi->no_mutasi }}</td>
                                    <td rowspan="{{ $produkCount }}">{{ $mutasi->supplier->nama }}</td>
                                    <td rowspan="{{ $produkCount }}">{{ $mutasi->lokasi->nama }}</td>
                                    <td rowspan="{{ $produkCount }}">{{ $mutasi->tgl_dikirim }}</td>
                                    <td rowspan="{{ $produkCount }}">{{ $mutasi->tgl_diterima?? '-' }}</td>
                                @endif
                                <td>{{ $produk->produk->bulan_inden }}</td>
                                <td>{{ $produk->produk->kode_produk }}</td>
                                <td>{{ $produk->jml_dikirim }}</td>
                                <td>{{ $produk->biaya_rawat }}</td>
                                <td>{{ $produk->totalharga }}</td>
                                <td>{{ $produk->jml_diterima }}</td>
                                <td>{{ $produk->kondisi->nama?? '' }}</td>
                                @if ($loop->first)
                                    <td rowspan="{{ $produkCount }}">{{ $mutasi->biaya_pengiriman }}</td>
                                    <td rowspan="{{ $produkCount }}">{{ $mutasi->rekening->nama ?? '' }}</td>
                                    <td rowspan="{{ $produkCount }}">{{ $mutasi->total_biaya }}</td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</body>
</html>
