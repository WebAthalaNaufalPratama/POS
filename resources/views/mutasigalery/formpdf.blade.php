<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Form Perangkai</title>
    <style>
        @page { margin-top: 160px; }
        body {
            font-family: 'Times New Roman', Times, serif;
            padding: auto 2rem;
        }

        .header {
            position: fixed; /* Tetap di atas halaman */
            top: -160px;
            left: 0;
            right: 0;
            height: 100px; /* Sesuaikan dengan tinggi header */
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fff; /* Warna latar belakang header */
        }

        .header img {
            width: 100px; /* Ukuran gambar logo */
            height: auto;
            margin-right: 20px; /* Jarak antara logo dan teks */
        }

        .header h1 {
            margin-top: 30px;
        }

        .text-start{
            text-align: left !important;
        }
        .text-center{
            text-align: center !important;
        }
        .text-end{
            text-align: right !important;
        }

        .full-width {
            width: 100%
        }

        .border, .border th, .border td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 8px;
            text-align: center;
        }
        .border thead th {
            font-weight: bold;
        }

        ol {
            padding: 0 0 0 1.5em; 
            margin: 0;
        }
        ol li {
            padding: 0; 
            margin: 0;
            text-align: justify
        }
        
        li {
            page-break-inside: avoid;
        }

        .ttd {
            width: 100%;
            display: flex;
            justify-content: space-between; /
        }

        .pihak-pertama {
            float: left;
            margin-left: 5em;
            text-align: center; 
            box-sizing: border-box; 
        }
        .pihak-kedua {
            float: right;
            margin-right: 5em;
            text-align: center; 
            box-sizing: border-box; 
        }

        p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="full-width">
            <tr>
                <td style="width: 30%; vertical-align: bottom;">
                    <img src="{{ base64_image(public_path('assets/img/von.png')) }}" alt="image">
                </td>
                <td style="vertical-align: bottom;">
                    <h1 style="margin-bottom: 0;" class="text-start">FORM RANGKAIAN</h1>
                </td>
            </tr>
        </table>
        <table class="full-width">
            <tr>
                <td class="text-center"><small>{{ $produk_terjual['mutasi']['pengirim']['alamat'] }}</small></td>
            </tr>
            <tr>
                <td class="text-center"><small>No. {{ $no_form }}</small></td>
            </tr>
        </table>
    </div>

    <div class="content">
        <table class="full-width">
            <tr>
                <td style="width: 20%">Jenis Rangkaian</td>
                <td style="width: 20%" class="text-start">: {{ $jenis_rangkaian }}</td>
            </tr>
            <tr>
                <td style="width: 20%">Tanggal Rangkaian</td>
                <td style="width: 20%" class="text-start">: {{ formatTanggal($tanggal) }}</td>
            </tr>
            <tr>
                <td style="width: 20%">Nomor</td>
                <td style="width: 20%" class="text-start">: {{ $produk_terjual['mutasi']['no_mutasi'] }}</td>
            </tr>
            <tr>
                <td style="width: 20%">Staff Perangkai</td>
                <td style="width: 20%" class="text-start">: {{ $perangkai['nama'] }}</td>
            </tr>
        </table>
        <br>
        <table class="full-width border">
            <thead style="background-color: rgb(197, 197, 197)">
                <tr>
                    <th>No</th>
                    <th>Produk Rangkaian</th>
                    <th>Quantity</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                {{-- @foreach ($produk_terjual as $item) --}}
                    <tr>
                        {{-- <td>{{ $loop->iteration }}</td> --}}
                        <td>1</td>
                        <td>{{ $produk_terjual['produk']['nama'] }}</td>
                        <td>{{ $produk_terjual['jumlah'] }}</td>
                        <td>-</td>
                    </tr>
                {{-- @endforeach --}}
            </tbody>
        </table>
        <br><br><br>
        <div class="ttd">
            <div class="pihak-pertama">
                <p><strong>Dibuat</strong></p>
                <br>
                <br>
                <br>
                <br>
                <p><strong>{{ $produk_terjual['mutasi']['dibuat']['name'] }}</strong></p>
                <p>{{ formatTanggal($tanggal) }}</p>
            </div>
            <div class="pihak-kedua">
                <p><strong>Diterima</strong></p>
                <br>
                <br>
                <br>
                <br>
                <p><strong>-</strong></p>
                <p>{{ formatTanggal(now()) }}</p>
            </div>
        </div>
    </div>
</body>
</html>