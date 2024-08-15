<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Stok Gallery</title>
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
        .header h1, .header p {
            margin: 0;
            text-align: center;
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
        <h2>Laporan Bunga Datang</h2>
    </div>
    <div class="content">
        <table class="table" id="datanew">
            <thead>
                <tr>
                    <th class="text-center" >Bulan</th>
                    <th class="text-center" >Lokasi</th>
                    <th class="text-center" >Supplier</th>
                    <th class="text-center" >Total</th>
                </tr>
            </thead>
            <tbody>
            <!-- @php
                $previousNoDo = '';
            @endphp -->
                @foreach ($groupedData->groupBy('lokasi_id') as $lokasiId => $items)
                    @foreach ($items as $item)
                    
                        <tr>
                        @if($loop->first)
                            {{-- @if( \Carbon\Carbon::parse($listDate[0])->locale('id')->translatedFormat('F') != $previousNoDo) --}}
                                <td class="text-center" rowspan="{{ $items->count() }}">{{ \Carbon\Carbon::parse($listDate[0])->locale('id')->translatedFormat('F') }}</td>
                            @endif
                            <td class="text-center" rowspan="{{  $items->count() }}">{{ $item['lokasi_name'] }}</td>
                            <td>{{ $item['supplier_name'] }}</td>
                            <td>{{ $item['total_masuk'] }}</td>
                        </tr>
                    @php
                        $previousNoDo = \Carbon\Carbon::parse($listDate[0])->locale('id')->translatedFormat('F');
                    @endphp
                    @endforeach
                @endforeach
            </tbody>
            <tbody style="border-top: 2px solid #000;">
                @foreach ($groupedData->groupBy('lokasi_id') as $lokasiId => $items)
                @foreach ($items as $item)
                @if ($loop->first)
                    <tr>
                    @if( 1 != $previousNoDo)
                            <td rowspan="{{ count($item) }}" colspan="2">Total Kedatangan</td>
                    @endif
                        <td>{{ $item['supplier_name'] }}</td>
                        <td>{{ $items->sum('total_masuk') }}</td>
                    </tr>
                @else
                    <tr>
                        <td>{{ $item['supplier_name'] }}</td>
                        <td>{{ $items->sum('total_masuk') }}</td>
                    </tr>
                @endif
                @php
                    $previousNoDo = 1;
                @endphp
                @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>