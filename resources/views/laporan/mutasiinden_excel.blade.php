<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Mutasi Inden</title>
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
    <h1>Laporan Mutasi Inden</h1>
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
                                <td rowspan="{{ $produkCount }}">{{ $mutasi->rekening_id }}</td>
                                <td rowspan="{{ $produkCount }}">{{ $mutasi->total_biaya }}</td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>
