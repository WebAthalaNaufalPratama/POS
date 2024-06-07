<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kontrak</title>
    <style>
        @page { margin-top: 160px; }
        body {
            font-family: 'Times New Roman', Times, serif;
            padding: auto 2rem;
        }

        .header {
            position: fixed; /* Tetap di atas halaman */
            top: -140px;
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
            color: #bf8f00;
            margin: 0;
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
                <td style="width: 20%; vertical-align: bottom;">
                    <img src="{{ base64_image(public_path('assets/img/logo_von.jpg')) }}" alt="logo von">
                </td>
                <td style="vertical-align: bottom;color: #bf8f00;">
                    <h1 style="margin-bottom: 0;">PT VON MUSTIKA SEJAHTERA</h1>
                </td>
            </tr>
        </table>
        <table class="full-width">
            <tr>
                <td class="text-start"><small>www.vonflorist.com</small></td>
                <td class="text-end"><small>IG; @von.florist</small></td>
                <td class="text-end"><small>Email : vonflorist@gmail.com</small></td>
            </tr>
        </table>
    </div>

    <div class="content">
        <h3 class="text-center">FORM SEWA BUNGA ANGGREK</h3>
        <p class="text-end">{{ stringToCamelCase($lokasi['operasional']['nama']) }}, {{ formatTanggalInd() }} </p>
        <br>
        <br>
        <table class="full-width">
            <tr>
                <td style="width: 30%">Pihak Pertama (Customer)</td>
                <td style="width: 30%" class="text-start">: {{ stringToCamelCase($customer['nama']) }}</td>
            </tr>
            <tr>
                <td style="width: 30%">Nama Pic</td>
                <td style="width: 30%" class="text-start">: {{ stringToCamelCase($pic) }}</td>
            </tr>
            <tr>
                <td style="width: 30%">Jabatan</td>
                <td style="width: 30%" class="text-start">: -</td>
            </tr>
            <tr>
                <td style="width: 30%">Contact Person</td>
                <td style="width: 30%" class="text-start">: {{ $handphone }}</td>
            </tr>
            <tr>
                <td style="width: 30%">Pihak Kedua (Vendor)</td>
                <td style="width: 30%" class="text-start">: {{ stringToCamelCase($data_sales['nama']) }}</td>
            </tr>
            <tr>
                <td style="width: 30%">Masa Sewa</td>
                <td style="width: 30%" class="text-start">: {{ $masa_sewa }} bulan</td>
            </tr>
            <tr>
                <td style="width: 30%">Alamat Pengiriman Tanaman</td>
                <td style="width: 30%" class="text-start">: {{ $alamat }}</td>
            </tr>
            <tr>
                <td style="width: 30%">Deskripsi Sewa</td>
                <td style="width: 30%" class="text-start">: -</td>
            </tr>
        </table>
        <br>
        <table class="full-width border">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach ($produk as $item)
                    <tr>
                        <td>{{ $item['produk']['nama'] }}</td>
                        <td>{{ $item['jumlah'] }} tanaman</td>
                        <td>{{ formatRupiah($item['harga']) }}</td>
                        <td>{{ formatRupiah($item['harga_jual']) }}</td>
                    </tr>
                    @endforeach
                </tr>
                <tr>
                    <td colspan="3" class="text-end">Jumlah Harga Nett</td>
                    <td>{{ formatRupiah($subtotal) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-end">PPN {{ $ppn_persen }} %</td>
                    <td>{{ formatRupiah($ppn_nominal) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-end">Total Pembayaran</td>
                    <td>{{ formatRupiah($total_harga) }}</td>
                </tr>
            </tbody>
        </table>
        <p>
            <strong>Catatan</strong>
            <br>
            <strong>Harga diatas sudah termasuk pajak PPN {{ $ppn_persen }} %</strong>
        </p>
        <br>
        <p><strong>Ketentuan Sewa</strong></p>
        <ol>
            <li>{{-- 1 --}} 
                <p><strong>PIHAK KEDUA</strong> sanggup untuk mengadakan Tanaman Bunga Anggrek lengkap dengan pot.</p>
            </li>
            <li>{{-- 2 --}}
                <p><strong>PIHAK KEDUA</strong> akan mengganti Tanaman Bunga Anggrek setiap 1 bulan sekali, pengganitan kurang dari 1 bulan wajib menyertakan foto kondisi bunga dengan kerusakan bunga 70%.</p>
            </li>
            <li>{{-- 3 --}}
                <p><strong>PIHAK KEDUA</strong> akan meminjamkan pot kepada <strong>PIHAK PERTAMA</strong> sesuai dengan perjanjian.</p>
            </li>
            <li>{{-- 4 --}}
                <p><strong>PIHAK PERTAMA</strong> akan melakukan penggantian biaya kepada <strong>PIHAK KEDUA</strong> jika ada kerusakan pada pot akibat kesalahan <strong>PIHAK PERTAMA</strong>.</p>
            </li>
            <li>{{-- 5 --}}
                <p><strong>PIHAK PERTAMA</strong> akan mengembalikan pot beserta bonggol kepada <strong>PIHAK KEDUA</strong> bersamaan dengan pergantian Tanaman Bunga Anggrek.</p>
            </li>
            <li>{{-- 6 --}}
                <p>Harga pot yang disepakai bersama adalah :</p>
                <table>
                    @foreach ($produk as $item)
                    <tr>
                        <td>{{ $item['produk']['nama'] }}</td>
                        <td class="text-start" style="padding-left: 1rem">{{ formatRupiah($item['harga']) }},-per pot.</td>
                    </tr>
                @endforeach
                </table>
            </li>
            <li>{{-- 7 --}}
                <p>Perpanjangan perjanian dapat dilakukan dengan pemberitahuan terlebih dahulu antara <strong>PIHAK PERTAMA</strong> kepada <strong>PIHAK KEDUA</strong>, selambat-lambatnya 14 (empat belas) hari sebelum berakhir jangka waktu perjanjian ini.</p>
            </li>
            <li>{{-- 8 --}}
                <p><strong>PIHAK PERTAMA</strong> akan memproses pembayaran yang diajukan <strong>PIHAK KEDUA</strong> setiap bulan setelah <strong>PIHAK PERTAMA</strong> menerima kwitansi asli.</p>
            </li>
            <li>{{-- 9 --}}
                <p><strong>PIHAK PERTAMA</strong> akan melakukan pembayaran tagihan <strong>PIHAK KEDUA</strong>, maksimal 30 hari kalender setelah tanggal penerimaan kwitansi asli dengan mentransfer ke bank.</p>
            </li>
            <li>{{-- 10 --}}
                <p><strong>{{ $rekening['bank'] }}</strong></p>
                <p><strong>No. Rekening {{ $rekening['nomor_rekening'] }}</strong></p>
                <p><strong>{{ $rekening['nama_akun'] }}</strong></p>
                <p><small><strong>*Pembayaran cash dan diluar rekening tersebut dianggap TIDAK SAH</strong></small></p>
            </li>
            <li>{{-- 11 --}}
                <p>Apabila <strong>PIHAK PERTAMA</strong> terlambat melakukan pembayaran selama tiga bulan berturut-turut / tiga puluh hari, maka pada bulan berikutnya <strong>PIHAK KEDUA</strong> tidak dapat mengirimkan tanaman tersebut kepada <strong>PIHAK PERTAMA</strong> melakukan pelunasan pembayaran.</p>
            </li>
        </ol>
        <br><br><br>
        <div class="ttd">
            <div class="pihak-pertama">
                <p><strong>Pihak Pertama</strong></p>
                <p><strong>{{ stringToCamelCase($customer['nama']) }}</strong></p>
                <br>
                <br>
                <br>
                <br>
                <p><strong>{{ stringToCamelCase($pic) }}</strong></p>
                <p><strong>Customer</strong></p>
            </div>
            <div class="pihak-kedua">
                <p><strong>Pihak Kedua</strong></p>
                <p><strong>Von Florist</strong></p>
                <br>
                <br>
                <br>
                <br>
                <p><strong>{{ stringToCamelCase($data_sales['nama']) }}</strong></p>
                <p><strong>{{ stringToCamelCase($data_sales['jabatan']) }}</strong></p>
            </div>
        </div>
    </div>
</body>
</html>