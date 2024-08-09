<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pergantian Sewa</title>
</head>
<body>
    {{-- <table>
        <tr>
            <td style="text-align: right">Nama Customer: </td>
            <td>{{ $data->customer->nama }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="text-align: right">Nama Sales: </td>
            <td>{{ $data->data_sales->nama }}</td>
        </tr>
        <tr>
            <td style="text-align: right">Tanggal Kontrak: </td>
            <td>{{ formatTanggal($data->tanggal_kontrak) }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="text-align: right">Masa Sewa: </td>
            <td>{{ $data->masa_sewa }} bulan</td>
        </tr>
    </table> --}}
    {{-- <table>
        <thead>
            <tr>
                <th rowspan="3" valign="center" style="item-align: center">Nomor Referensi</th>
                <th rowspan="3" valign="center" style="item-align: center">Tanggal Pergantian</th>
                <th colspan="5" style="text-align: center">Pengiriman</th>
                <th colspan="5" style="text-align: center">Kembali Ke Gallery</th>
            </tr>
            <tr>
                <th rowspan="2" valign="center" style="text-align: center">Produk Dikirim</th>
                <th rowspan="2" valign="center" colspan="2" style="text-align: center">Jumlah Pot</th>
                <th rowspan="2" valign="center" colspan="2" style="text-align: center">Jumlah Tanaman</th>
                <th colspan="3" style="text-align: center">Tanaman</th>
                <th colspan="2" style="text-align: center">Pot</th>
            </tr>
            <tr>
                <th style="text-align: center">Baik</th>
                <th style="text-align: center">Afkir</th>
                <th style="text-align: center">Bonggol</th>
                <th style="text-align: center">Qty</th>
                <th style="text-align: center">Jenis</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalKirimPot = 0;
                $totalKirimTanaman = 0;
                $totalKembaliPot = 0;
                $totalKembaliTanaman = 0;
            @endphp
            @foreach ($data->do_sewa as $item)
                @foreach ($item->produk as $produk)
                    @if ($produk->jenis == null)
                    @php // get jumlah kirim pot dan tanaman
                        $baik = 0;
                        $afkir = 0;
                        $bonggol = 0;
                        $kembaliPot = 0;
                        $pot = 0;
                        $tanaman = 0;
                        $isDO = true;
                        for ($i=0; $i < count($produk->komponen); $i++) { 
                            if($produk->komponen[$i]->tipe_produk == 2){
                                $pot += $produk->komponen[$i]->jumlah * $produk->jumlah;
                            } else if($produk->komponen[$i]->tipe_produk == 1){
                                $tanaman += $produk->komponen[$i]->jumlah * $produk->jumlah;
                            }
                        }
                        $totalKirimPot += $pot;
                        $totalKirimTanaman += $tanaman;
                    @endphp
                    @elseif ($produk->jenis == 'KEMBALI_SEWA')
                    @php // get jumlah kembali pot dan tanaman
                        $baik = 0;
                        $afkir = 0;
                        $bonggol = 0;
                        $kembaliPot = 0;
                        $pot = 0;
                        $tanaman = 0;
                        $isDO = false;
                        for ($i=0; $i < count($produk->komponen); $i++) { 
                            if($produk->komponen[$i]->tipe_produk == 1){
                                if($produk->komponen[$i]->kondisi == 1){
                                    $baik += $produk->komponen[$i]->jumlah * $produk->jumlah;
                                } elseif($produk->komponen[$i]->kondisi == 2){
                                    $afkir += $produk->komponen[$i]->jumlah * $produk->jumlah;
                                } elseif($produk->komponen[$i]->kondisi == 3){
                                    $bonggol += $produk->komponen[$i]->jumlah * $produk->jumlah;
                                }
                            } elseif($produk->komponen[$i]->tipe_produk == 2){
                                if($produk->komponen[$i]->kondisi == 1){
                                    $kembaliPot += $produk->komponen[$i]->jumlah * $produk->jumlah;
                                }
                            }
                        }
                        $totalKembaliPot += $kembaliPot;
                        $totalKembaliTanaman += ($baik + $afkir + $bonggol);
                    @endphp
                    @endif
                        <tr>
                            <td>{{ $isDO ? $item->no_do : $produk->no_kembali_sewa }}</td>
                            <td>{{ $isDO ? formatTanggal($item->tanggal_kirim) : formatTanggal($produk->kembali_sewa->tanggal_kembali) }}</td>
                            <td>{{ $produk->produk->nama }}</td>
                            <td style="text-align: right">{{ $pot }}</td>
                            <td>pot</td>
                            <td style="text-align: right">{{ $tanaman }}</td>
                            <td>tanaman</td>
                            <td style="text-align: center">{{ $baik }}</td>
                            <td style="text-align: center">{{ $afkir }}</td>
                            <td style="text-align: center">{{ $bonggol }}</td>
                            <td style="text-align: right">{{ $kembaliPot }}</td>
                            <td>B1</td>
                        </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">1</td>
                <td>Total</td>
                <td style="text-align: right">{{ $totalKirimPot }}</td>
                <td>Pot</td>
                <td>{{ $totalKirimTanaman }}</td>
                <td colspan="2">Tanaman</td>
                <td style="text-align: right">{{ $totalKembaliTanaman }}</td>
                <td>Tanaman</td>
                <td style="text-align: right">{{ $totalKembaliPot }}</td>
                <td>Pot</td>
            </tr>
            <tr>
                <td rowspan="2" colspan="1" valign="center" style="text-align: center">Keterangan</td>
                <td rowspan="2" colspan="2" valign="center" style="text-align: right">Jumlah Pergantian sesuai dengan kontrak</td>
                <td style="text-align: right" valign="center" rowspan="2">0</td>
                <td style="text-align: center" valign="center" rowspan="2" colspan="3">Tanaman</td>
                <td style="text-align: center" colspan="3">Jumlah Kekurangan Tanaman</td>
                <td style="text-align: right">{{ $totalKirimTanaman - $totalKembaliTanaman }}</td>
                <td style="text-align: center">Tanaman</td>
            </tr>
            <tr>
                <td style="text-align: center" colspan="3">Jumlah Kekurangan Pot</td>
                <td style="text-align: right">{{ $totalKirimPot - $totalKembaliPot }}</td>
                <td style="text-align: center">Pot</td>
            </tr>
        </tfoot>
    </table> --}}
    <table>
        <tr>
            <td style="text-align: right">Nama Customer: </td>
            <td>{{ $data['data']->customer->nama }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="text-align: right">Nama Sales: </td>
            <td>{{ $data['data']->data_sales->nama }}</td>
        </tr>
        <tr>
            <td style="text-align: right">Tanggal Kontrak: </td>
            <td>{{ formatTanggal($data['data']->tanggal_kontrak) }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="text-align: right">Masa Sewa: </td>
            <td>{{ $data['data']->masa_sewa }} bulan</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th rowspan="3" valign="center" style="text-align: center">Nomor Referensi</th>
                <th rowspan="3" valign="center" style="text-align: center">Tanggal Pergantian</th>
                <th colspan="5" style="text-align: center">Pengiriman</th>
                <th colspan="5" style="text-align: center">Kembali Ke Gallery</th>
            </tr>
            <tr>
                <th rowspan="2" valign="center" style="text-align: center">Produk Dikirim</th>
                <th rowspan="2" valign="center" colspan="2" style="text-align: center">Jumlah Pot</th>
                <th rowspan="2" valign="center" colspan="2" style="text-align: center">Jumlah Tanaman</th>
                <th colspan="3" style="text-align: center">Tanaman</th>
                <th colspan="2" style="text-align: center">Pot</th>
            </tr>
            <tr>
                <th style="text-align: center">Baik</th>
                <th style="text-align: center">Afkir</th>
                <th style="text-align: center">Bonggol</th>
                <th style="text-align: center">Qty</th>
                <th style="text-align: center">Jenis</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalKirimPot = $data['totalKirimPot'];
                $totalKirimTanaman = $data['totalKirimTanaman'];
                $totalKembaliPot = $data['totalKembaliPot'];
                $totalKembaliTanaman = $data['totalKembaliTanaman'];
            @endphp
            @foreach ($data['tableData'] as $item)
                <tr>
                    <td>{{ $item['no_referensi'] }}</td>
                    <td>{{ $item['tanggal'] }}</td>
                    <td>{{ $item['produk'] }}</td>
                    <td style="text-align: right">{{ $item['pot'] }}</td>
                    <td>pot</td>
                    <td style="text-align: right">{{ $item['tanaman'] }}</td>
                    <td>tanaman</td>
                    <td style="text-align: center">{{ $item['baik'] }}</td>
                    <td style="text-align: center">{{ $item['afkir'] }}</td>
                    <td style="text-align: center">{{ $item['bonggol'] }}</td>
                    <td style="text-align: right">{{ $item['pot'] }}</td>
                    <td>{{ $item['isDO'] ? 'B1' : 'B1' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">1</td>
                <td>Total</td>
                <td style="text-align: right">{{ $totalKirimPot }}</td>
                <td>Pot</td>
                <td>{{ $totalKirimTanaman }}</td>
                <td colspan="2">Tanaman</td>
                <td style="text-align: right">{{ $totalKembaliTanaman }}</td>
                <td>Tanaman</td>
                <td style="text-align: right">{{ $totalKembaliPot }}</td>
                <td>Pot</td>
            </tr>
            <tr>
                <td rowspan="2" colspan="1" valign="center" style="text-align: center">Keterangan</td>
                <td rowspan="2" colspan="2" valign="center" style="text-align: right">Jumlah Pergantian sesuai dengan kontrak</td>
                <td style="text-align: right" valign="center" rowspan="2">0</td>
                <td style="text-align: center" valign="center" rowspan="2" colspan="3">Tanaman</td>
                <td style="text-align: center" colspan="3">Jumlah Kekurangan Tanaman</td>
                <td style="text-align: right">{{ $totalKirimTanaman - $totalKembaliTanaman }}</td>
                <td style="text-align: center">Tanaman</td>
            </tr>
            <tr>
                <td style="text-align: center" colspan="3">Jumlah Kekurangan Pot</td>
                <td style="text-align: right">{{ $totalKirimPot - $totalKembaliPot }}</td>
                <td style="text-align: center">Pot</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>