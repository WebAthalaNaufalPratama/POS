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
            float: right;
            width: 25%;
            text-align: right;
        }
        
        .auditor{
            margin-top: 80px;
        }

        .auditor .name-title {
            margin-top : -10px;
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
            margin-top: 80px;
            float: left;
            text-align: center;
        }

        .dibukukan{
            margin-top: 80px;
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


        /* Footer styles */
        footer {
            clear: both;
            text-align: center;
            margin-top: 20px;
        }
        .garis_bawah{
            margin-top:120px;
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
                <h4><center>alamat : {{$lokasi}}</center></h4>
            </div>
            <hr>
        </header>

        <div class="judul">
            <h1><center>DELIVERY ORDER</center></h1>
        </div>

        <div class="pelanggan">
            <p>Penerima</p>
            <p>No Handphone</p>
            <p>Alamat</p>
            <p>Driver</p>
        </div>

        <div class="jawabpelanggan">
            <p>: {{$penerima}}</p>
            <p>: {{$handphone}}</p>
            <p>: {{$alamat}}</p>
            <p>: {{$driver}}</p>
        </div>

        <div class="invoice">
            <p>Pengirim</p>
            <p>No Invoice</p>
            <p>Tanggal Invoice</p>
            <p>Tanggal Pengiriman</p>
        </div>

        <div class="jawabinvoice">
            <p>: {{$customer}}</p>
            <p>: {{$no_referensi}}</p>
            <p>: {{date('d F Y', strtotime($tanggal_invoice))}}</p>
            <p>: {{date('d F Y', strtotime($tanggal_driver))}}</p>
        </div>

        <hr class="garis_bawah">

        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Unit Satuan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
            @if(count($produks) < 1) <tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endif
                @php
                $i = 0;
                @endphp
                @foreach ($produks as $komponen)
                <tr>
                    <td>
                        {{$komponen->produk->nama}}
                    </td>
                    <td>{{ $komponen->jumlah }}</td>
                    <td>{{ $komponen->satuan ? $komponen->satuan : '-'}}
                    </td>
                    <td>{{ $komponen->keterangan ? $komponen->keterangan : '-'}}</td>
                    @php
                    $i++;
                    @endphp
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="tabletagihan">
            <tr>
                <th>Catatan</th>
                <td>: {{ $catatan }}</td>
            </tr>
        </table>

        <div class="pembuat">
                <p>Pembuat</p>
                <br>
                <br>
                <br>
                <p>{{$dibuat ? $dibuat : '-'}}</p>
                <p>{{$tanggal_pembuat ? date('d F Y', strtotime($tanggal_pembuat)) : '-'}}</p>
        </div>

        

        <div class="dibukukan">
                <p>Dibukukan</p>
                <br>
                <br>
                <br>
                <p>{{$disetujui ? $disetujui : '-'}}</p>
                <p>{{$tanggal_penyetuju ? date('d F Y', strtotime($tanggal_penyetuju)) : '-'}}</p>
        </div>
        <center>
        <div class="auditor">
                <p>Auditor</p>
                <br>
                <br>
                <br>
                <p>{{$diperiksa ? $diperiksa : '-'}}</p>
                <div class="name-title">
                <p>{{$tanggal_pemeriksa ? date('d F Y', strtotime($tanggal_pemeriksa)) : '-'}}</p>
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
