<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size:12px;
        }

        /* Container styles */
        .container {
            width: 90%;
            margin: 0 auto;
        }

        header .logo{
            float: left;
            width: 10%;
            max-width: 100px;
        }

        hr{
            margin-top: 80px;
        }

        header .nota-details {
            float: right;
            width: 55%;
            text-align: left;
        }
        header .nota-details h4{
            width: 45%;
        }
        header .nota {
            float: right;
            width: 40%;
        }
        header .note {
            float: right;
            width: 20%;
            text-align: left;
        }
        
        header .nota-details p {
            margin: 10px 0 -100px 0; 
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }
        
        header .nota-details span {
            padding-left: 10px;
        }

        .pelanggan{
            float: left;
            width: 25%;
            max-width: 100px;
        }

        .jawabpelanggan{
            float: left;
            width: 50%;
            text-align: left;
        }

        .invoice{
            float: right;
            width: 50%;
            text-align: left;
        }

        .jawabinvoice{
            float: right;
            width: 25%;
            text-align: left;
        }

        .jawabinvoice .auditor{
            margin-top: 120px;
            float: right;
            width: 25%;
            text-align: right;
        }

        .auditor .name-title {
            float: right;
            width: 20%;
            text-align: center;
        }

        .pengiriman{
            float: left;
            width: 25%;
            max-width: 100px;
        }

        .tagihan{
            float: right;
            width: 15%;
            text-align: left;
        }

        .pembuat{
            margin-top: 120px;
            float: left;
            text-align: center;
        }

        .dibukukan{
            margin-top: 120px;
            float: left;
            width: 60%;
            text-align: center;
        }


        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            align-items: center;
        }
        
        table, th, td {
            border: 1px solid #ddd;
        }
        
        th, td {
            padding: 8px;
            text-align: left;
        }
        
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .rekening{
            width: 50%;
        }

        .tabletagihan{
            width : 45%;
            float: right;
        }


        /* Footer styles */
        footer {
            clear: both;
            text-align: center;
            margin-top: 20px;
        }
        .garis_bawah{
            margin-top:140px;
        }

        @media print {
            @page {
                margin: 20mm;
            }

            body {
                margin: 0;
            }

            .container {
                margin-top: 140px; /* Adjust based on header height */
            }
        }

        .repeat-header {
            display: none;
        }

        @page {
            margin: 20mm;
            @top-center {
                content: element(header);
            }
        }

        .repeat-header {
            display: block;
            position: running(header);
        }
    </style>
</head>
<body>

    <div class="container">
        <header>
            <div class="logo">
                <img src="{{ base64_image(public_path('assets/img/von.png')) }}" alt="image">
            </div>
            <div class="nota-details"> <!-- Perbaiki penulisan nama kelas -->
                <h1>VONFLORIST</h1>
                <h4><center>{{ $kontrak['lokasi']['alamat'] }}</center></h4>
            </div>
            <hr>
        </header>

        <div class="judul">
            <h1><center>INVOICE SEWA</center></h1>
        </div>

        <div class="pelanggan">
            <p>Customer</p>
            <p>No Handphone</p>
            <p>Lokasi Beli</p>
            <p>Jumlah Point</p>
            <p>Nama Sales</p>
        </div>

        <div class="jawabpelanggan">
            <p>: {{ $kontrak['customer']['nama'] }}</p>
            <p>: {{ $kontrak['customer']['handphone'] }}</p>
            <p>: {{ $kontrak['lokasi']['nama'] }}</p>
            <p>: -</p>
            <p>: {{ $data_sales['nama'] }}</p>
        </div>

        <div class="invoice">
            <p>No Invoice</p>
            <p>Tanggal Invoice</p>
            <p>Jatuh Tempo Bayar</p>
        </div>

        <div class="jawabinvoice">
            <p>: {{ $no_invoice }}</p>
            <p>: {{ formatTanggal($tanggal_invoice) }}</p>
            <p>: {{ formatTanggal($jatuh_tempo) }}</p>
        </div>

        <hr class="garis_bawah">

        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                    <th>Harga Total</th>
                </tr>
            </thead>
            <tbody>
                @if(count($produk) < 1)
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @else
                    @php
                        $i = 0;
                    @endphp
                    @foreach ($produk as $komponen)
                        <tr>
                            <td>{{ $komponen['produk']['nama'] }}</td>
                            <td> {{ formatRupiah($komponen['harga']) }} </td>
                            <td> {{ formatRupiah($komponen['jumlah']) }} </td>
                            <td>{{ formatRupiah($komponen['harga_jual']) }}
                            </td>
                        </tr>
                        @php
                        $i++;
                        @endphp
                    @endforeach
                @endif
            </tbody>
        </table>

        <table class="tabletagihan">
                <tr>
                    <th style="line-height: 0.8;">Sub Total</th>
                    <td style="line-height: 0.8;">{{ formatRupiah($kontrak['subtotal']) }}</td>
                </tr>
                <tr>
                    <th style="line-height: 0.8;">Promo</th>
                    <td style="line-height: 0.8;">{{ formatRupiah($kontrak['total_promo']) }}</td>
                </tr>
                <tr>
                    <th style="line-height: 0.8;">PPN</th>
                    <td style="line-height: 0.8;">{{ formatRupiah($kontrak['ppn_nominal']) }}</td>
                </tr>
                <tr>
                    <th style="line-height: 0.8;">PPH</th>
                    <td style="line-height: 0.8;">{{ formatRupiah($kontrak['pph_nominal']) }}</td>
                </tr>
                <tr>
                    <th style="line-height: 0.8;">Biaya Pengiriman</th>
                    <td style="line-height: 0.8;">{{ formatRupiah($kontrak['ongkir_nominal']) }}</td>
                </tr>
                <tr>
                    <th style="line-height: 0.8;">Down Payment</th>
                    <td style="line-height: 0.8;">{{ formatRupiah($dp) }}</td>
                </tr>
                <tr>
                    <th style="line-height: 0.8;">Total Tagihan</th>
                    <td style="line-height: 0.8;">{{ formatRupiah($total_tagihan) }}</td>
                </tr>
                <tr>
                    <th style="line-height: 0.8;">Sisa Bayar</th>
                    <td style="line-height: 0.8;">{{ formatRupiah($sisa_bayar) }}</td>
                </tr>
        </table>

        <h4>Pembayaran Via Rekening</h4>
        <table class="rekening">
            <thead>
                <tr>
                    <th>Nama Bank</th>
                    <th>No Rekening</th>
                    <th>Atas Nama</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $rekening['bank'] }}</td>
                    <td>{{ $rekening['nomor_rekening'] }}</td>
                    <td>{{ $rekening['nama_akun'] }}</td>
                </tr>
            </tbody>
        </table> 

        <div class="pembuat">
                <p>Pembuat</p>
                <br>
                <br>
                <br>
                <p>{{ $data_pembuat['nama'] }}</p>
                <p>{{ formatTanggal($tanggal_pembuat) }}</p>
        </div>

        

        <div class="dibukukan">
                <p>Dibukukan</p>
                <br>
                <br>
                <br>
                <p>{{ $data_penyetuju['nama'] ?? '-' }}</p>
                <p>{{ formatTanggal($tanggal_penyetuju) ?? '-' }}</p>
        </div>
        <center>
        <div class="auditor">
                <p>Auditor</p>
                <br>
                <br>
                <br>
                <p>{{ $data_pemeriksa['nama'] ?? '-' }}</p>
                <div class="name-title">
                <p>{{ formatTanggal($tanggal_penyetuju) ?? '-'  }}</p>
                </div>
        </div>
        </center>

        <footer>
            <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        </footer>
    </div>

</body>
</html>
