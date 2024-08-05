<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Mutasi</title>
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
            padding: 2px;
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
    <table class="table datanew">
    <thead>
        <tr>
            <th>No</th>
            <th>No Mutasi</th>
            <th>Pengirim</th>
            <th>Penerima</th>
            <th>Tanggal Pengiriman</th>
            <th>Tanggal Diterima</th>
            <th>Nama Produk</th>
            <th>Jumlah Pengiriman</th>
            <th>Kondisi Pengiriman</th>
            <th>Jumlah Diterima</th>
            <th>Kondisi Diterima</th>
            <th>Biaya Pengiriman</th>
            <th>Rekening Bank</th>
            <th>Total Biaya</th>
        </tr>
    </thead>
    <tbody>
        @php
            $previousNoDo = '';
            $no = 1;
        @endphp
        @foreach ($combinedData as $data)
            @php
                $produkCount = count($data['produk_jual']);
            @endphp
            @foreach ($data['produk_jual'] as $produkJual)
                @php
                    $komponenCount = count($produkJual['komponen']);
                @endphp
                @foreach ($produkJual['komponen'] as $index => $komponen)
                    @if($loop->first)
                        <tr>
                            @if($data['no_mutasi'] != $previousNoDo)
                                <td rowspan="{{ $produkCount * $komponenCount }}">{{ $no++ }}</td>
                                <td rowspan="{{ $produkCount * $komponenCount }}">{{ $data['no_mutasi'] }}</td>
                                <td rowspan="{{ $produkCount * $komponenCount }}">{{ $data['lokasi_pengirim'] }}</td>
                                <td rowspan="{{ $produkCount * $komponenCount }}">{{ $data['lokasi_penerima'] }}</td>
                                <td rowspan="{{ $produkCount * $komponenCount }}">{{ $data['tanggal_pengiriman'] }}</td>
                                <td rowspan="{{ $produkCount * $komponenCount }}">{{ $data['tanggal_diterima'] }}</td>
                            @endif
                            <td>
                                @if(substr($data['no_mutasi'], 0, 3) != 'MGO')
                                    {{ $komponen['nama_produk'] }}
                                @else
                                    {{ $produkJual['nama_produkjual'] }}
                                @endif
                            </td>
                            <td>
                                {{ $produkJual['jumlahprodukjual'] }}
                            </td>
                            <td>
                                @if(substr($data['no_mutasi'], 0, 3) != 'MGO')
                                    {{ $komponen['kondisi'] }}
                                @else
                                    Tidak Ada Kondisi
                                @endif
                            </td>
                            <td>
                                {{ $produkJual['jumlah_diterima'] }}
                            </td>
                            <td>
                                @if(substr($data['no_mutasi'], 0, 3) != 'MGO')
                                    {{ $komponen['kondisi_diterima']->nama }}
                                @else
                                    Tidak Ada Kondisi
                                @endif
                            </td>
                            @if($data['no_mutasi'] != $previousNoDo)
                                <td rowspan="{{ $produkCount * $komponenCount }}">{{ number_format($data['biaya_pengiriman'], 0, ',', '.') }}</td>
                                <td rowspan="{{ $produkCount * $komponenCount }}">{{ $data['rekening'] }}</td>
                                <td rowspan="{{ $produkCount * $komponenCount }}">{{ number_format($data['total_biaya'], 0, ',', '.') }}</td>
                            @endif
                            @php
                                $previousNoDo = $data['no_mutasi'];
                            @endphp
                        </tr>
                    @endif
                @endforeach
            @endforeach
        @endforeach
    </tbody>
    </table>
    </div>
</body>
</html>