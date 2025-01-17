@extends('layouts.app-von')

@section('content')

<div class="row">
    @if(Auth::user()->hasRole(['SuperAdmin', 'Auditor', 'Finance', 'Pimpinan']))
        <div class="col-lg-6 col-sm-12 col-12 d-flex justify-content-start align-items-center">
            <div class="dash-widget">
                <div class="dash-widgetimg">
                <i data-feather="map-pin"></i>
                </div>
                <div class="dash-widgetcontent">
                    <div class="row">
                        <div class="col-6">
                            <select id="locationSelect" class="custom-select">
                                @foreach($lokasis as $lokasi)
                                    <option value="{{ $lokasi->id }}" {{ request('lokasi_id') == $lokasi->id ? 'selected' : '' }}>{{ $lokasi->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            @if(Auth::user()->hasRole(['Finance', 'Pimpinan']))
                            <select id="rekeningSelect" class="custom-select">
                                <option value="">--Rekening--</option>
                                @foreach($rekenings as $rekening)
                                    <option value="{{ $rekening->id }}" {{ request('rekening_id') == $rekening->id ? 'selected' : '' }}>{{ $rekening->nama_akun }}</option>
                                @endforeach
                            </select>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-sm-12 col-12 d-flex justify-content-end align-items-center">
    @else
        <div class="col-lg-12 col-sm-12 col-12 d-flex justify-content-end align-items-center">
    @endif
        <div class="dash-widget dash3">
            <div class="dash-widget-content text-right">
                <h5 id="currentDate"></h5>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-sm-6 col-12 d-flex justify-content-start align-items-center mb-4">
        <a href="{{ in_array('pembelian.index', $thisUserPermissions) ? route('pembelian.index') : '#' }}">
            <div class="dash-count dash-penjualan-sukses">
                <div class="dash-counts">
                    <h4>{{ $jumlahpenjualan }}</h4>
                    <h5>Pembelian (SUKSES)</h5>
                </div>
                <div class="dash-imgs">
                    <i data-feather="shopping-cart"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-lg-2 col-sm-6 col-12 d-flex justify-content-start align-items-center mb-4">
        <a href="{{ in_array('pembelian.index', $thisUserPermissions) ? route('pembelian.index') : '#' }}">
            <div class="dash-count dash-penjualan-batal">
                <div class="dash-counts">
                    <h4>{{ $batalpenjualan }}</h4>
                    <h5>Pembelian (BATAL)</h5>
                </div>
                <div class="dash-imgs">
                    <i data-feather="shopping-cart"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-lg-2 col-sm-6 col-12 d-flex justify-content-start align-items-center mb-4">
        <a href="{{ in_array('pembelian.index', $thisUserPermissions) ? route('pembelian.index') : '#' }}">
            <div class="dash-count dash-penjualan-retur">
                <div class="dash-counts">
                    <h4>{{ $returpenjualan }}</h4>
                    <h5>Pembelian (RETUR)</h5>
                </div>
                <div class="dash-imgs">
                    <i data-feather="shopping-cart"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-sm-6 col-12 d-flex justify-content-start align-items-center mb-4">
        <a href="{{ in_array('inven_galeri.index', $thisUserPermissions) ? route('inven_galeri.index') : '#' }}">
            <div class="dash-count dash-customerlama">
                <div class="dash-counts">
                    <h4>{{ $penjualanlama }}</h4>
                    <h5>Jumlah Barang Masuk</h5>
                </div>
                <div class="dash-imgs">
                    <i data-feather="user"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-sm-6 col-12 d-flex justify-content-start align-items-center mb-4">
        <a href="{{ in_array('inven_galeri.index', $thisUserPermissions) ? route('inven_galeri.index') : '#' }}">
            <div class="dash-count dash-customerbaru">
                <div class="dash-counts">
                    <h4>{{ $penjualanbaru }}</h4>
                    <h5>Jumlah Barang Keluar</h5>
                </div>
                <div class="dash-imgs">
                    <i data-feather="user"></i>
                </div>
            </div>
        </a>
    </div>
    @if(Auth::user()->hasRole(['Purchasing', 'Pimpinan']))
    <div class="col-lg-6 col-sm-6 col-12 d-flex justify-content-start align-items-center mb-4">
        <a href="{{ in_array('pembelian.index', $thisUserPermissions) ? route('pembelian.index') : '#' }}" style="width: 100%">
            <div class="dash-count dash-pemasukan">
                <div class="dash-counts">
                    <h4>{{ 'Rp ' . number_format($pengeluaran, 0, ',', '.') }}</h4>
                    <h5>Pengeluaran</h5>
                </div>
                <div class="dash-imgs">
                    <i data-feather="dollar-sign"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-6 col-sm-6 col-12 d-flex justify-content-start align-items-center mb-4">
        <div class="dash-count dash-pemasukan">
            <div class="dash-counts">
                <h4>{{ 'Rp ' . number_format($pemasukan, 0, ',', '.') }}</h4>
                <h5>Pemasukan</h5>
            </div>
            <div class="dash-imgs">
                <i data-feather="dollar-sign"></i>
            </div>
        </div>
    </div>
    @endif
    @if(Auth::user()->hasRole(['Finance', 'Pimpinan']))
    <div class="col-lg-6 col-sm-6 col-12 d-flex justify-content-start align-items-center mb-4">
        <a href="{{ in_array('penjualan.index', $thisUserPermissions) ? route('kas_pusat.index') : '#' }}" style="width: 100%">
            <div class="dash-count dash-penjualan-retur">
                <div class="dash-counts">
                    <h4>{{ 'Rp ' . number_format($balance, 0, ',', '.') }}</h4>
                    <h5>Saldo Rekening</h5>
                </div>
                <div class="dash-imgs">
                    <i data-feather="credit-card"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-6 col-sm-6 col-12 d-flex justify-content-start align-items-center mb-4">
        <div class="dash-count dash-pemasukan">
            <div class="dash-counts">
                <h4>{{ 'Rp ' . number_format($pemasukan, 0, ',', '.') }}</h4>
                <h5>Pemasukan</h5>
            </div>
            <div class="dash-imgs">
                <i data-feather="dollar-sign"></i>
            </div>
        </div>
    </div>
    @endif
    <div class="col-lg-6 col-sm-12 col-12 d-flex">
        <div class="card col-lg-12 col-sm-12 col-12 d-flex">
            <div class="card-header">
                <h5 class="card-title">Top Minus Produk</h5>
                <div class="row">
                    <div class="col-8">
                        <select id="InvenSelect" class="custom-select">
                            <option value="">Pilih Inventory</option>
                            <option value="Greenhouse" {{ request('inven') == 'Greenhouse' ? 'selected':''}}>Greenhouse</option>
                            <option value="Inden" {{ request('inven') == 'Inden' ? 'selected':''}}>Inden</option>
                            <option value="Gudang" {{ request('inven') == 'Gudang' ? 'selected':''}}>Gudang</option>
                        </select>
                    </div>
                    <div class="col-4 bulan">
                        <input type="month" class="form-control" name="filterDate" id="filterDate" value="{{ request()->input('dateInden') }}">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="top_minus_produk" class="chart-set"></div>
            </div>
        </div>
    </div>

    <!-- Top Produk Terjual -->
    <div class="col-lg-6 col-sm-12 col-12 d-flex">
        <div class="card col-lg-12 col-sm-12 col-12 d-flex">
            <div class="card-header">
                <h5 class="card-title">Produk Paling Banyak Dibeli</h5>
            </div>
            <div class="card-body">
                <div id="top_produk_chart" class="chart-set"></div>
            </div>
        </div>
    </div>
    @if(Auth::user()->hasRole(['Finance', 'Pimpinan']))
    {{-- Uang Keluar --}}
    <div class="col-lg-6 col-sm-12 col-12 d-flex">
        <div class="card col-lg-12 col-sm-12 col-12 d-flex">
            <div class="card-header">
                <h5 class="card-title">Uang Keluar</h5>
            </div>
            <div class="card-body">
                <div id="uang_keluar_chart" class="chart-set"></div>
            </div>
        </div>
    </div>
    
    {{-- Tagihan SUpplier --}}
    <div class="col-lg-6 col-sm-12 col-12 d-flex">
        <div class="card col-lg-12 col-sm-12 col-12 d-flex">
            <div class="card-header">
                <h5 class="card-title">Tagihan Supplier</h5>
            </div>
            <div class="card-body">
                <div id="tagihan_supplier_chart" class="chart-set"></div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    @if(Auth::user()->hasRole(['SuperAdmin', 'Auditor', 'Finance', 'Pimpinan']))
        $(document).ready(function() {
            // Handle change event for locationSelect
            $('#locationSelect').on('change', function() {
                var locationId = $(this).val();
                var currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('lokasi_id', locationId);
                window.location.href = currentUrl.toString();
            });

            // Handle change event for rekeningSelect
            $('#rekeningSelect').on('change', function() {
                var rekeningId = $(this).val();
                var currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('rekening_id', rekeningId);
                window.location.href = currentUrl.toString();
            });

            // Handle change event for invenSelect
            $('#InvenSelect').on('change', function() {
                var invenId = $(this).val();
                var currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('inven', invenId);
                window.location.href = currentUrl.toString();
            });

            invenSelect();

            function invenSelect() {
                var InvenId = $('#InvenSelect').val();
                if (InvenId === "Inden") {
                    $('.bulan').show();
                } else {
                    $('.bulan').hide();
                }
            }

            // Handle change event for invenSelect
            $('#filterDate').on('change', function() {
                var invenId = $(this).val();
                var currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('dateInden', invenId);
                window.location.href = currentUrl.toString();
            });
        });
    @else
        var first = true;
        var urlString = '';

        $(document).ready(function() {
            var urlString = '';

            invenSelect();

            function invenSelect() {
                var InvenId = $('#InvenSelect').val();
                if (InvenId === "Inden") {
                    $('.bulan').show();
                } else {
                    $('.bulan').hide();
                }
            }

            $('#InvenSelect').on('change', function() {
                var InvenId = $(this).val();
                if(InvenId == 'Inden'){
                    $('.bulan').show();
                    updateUrl(InvenId, $('#filterDate').val());
                }else{
                    updateUrl(InvenId, null);
                }
            });

            $('#filterDate').on('change', function() {
                var dateInden = $(this).val();
                updateUrl($('#InvenSelect').val(), dateInden);
            });

            function updateUrl(InvenId, dateInden) {
                urlString = '';

                if (InvenId) {
                    urlString += 'inven=' + InvenId;
                }

                if (dateInden) {
                    if (urlString.length > 0) {
                        urlString += '&';
                    }
                    urlString += 'dateInden=' + dateInden;
                }

                window.location.href = '{{ url("dashboard") }}' + (urlString ? '?' + urlString : '');
            }
        });
    @endif

    
    function formatTanggal(date) {
        var options = {
            year: 'numeric',
            month: 'long',
        };
        return date.toLocaleDateString('id-ID', options);
    }

    function updateDate() {
        var now = new Date();
        $('#currentDate').text(formatTanggal(now));
    }
    setInterval(updateDate, 1000);
    updateDate();

    function getData(id) {
        $.ajax({
            type: "GET",
            url: "/aset/" + id + "/edit",
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                // console.log(response)
                $('#editForm').attr('action', 'aset/' + id + '/update');
                $('#edit_nama').val(response.nama)
                $('#edit_deskripsi').val(response.deskripsi)
                $('#edit_lokasi_id').val(response.lokasi_id).trigger('change')
                $('#edit_jumlah').val(response.jumlah)
                $('#edit_tahun_beli').val(response.tahun_beli)
            },
            error: function(error) {
                toastr.error('Ambil data error', 'Error', {
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: false,
                    progressBar: true
                });
            }
        });
    }
    if ($('#top_sales').length > 0) {
        var topSalesBar = {
            chart: {
                height: 350,
                type: 'bar',
                toolbar: {
                    show: false,
                }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                }
            },
            dataLabels: {
                enabled: false
            },
            series: [{
                data: [400, 430, 448, 470, 540, 580, 690, 1100, 1200, 1380]
            }],
            xaxis: {
                categories: ['South Korea', 'Canada', 'United Kingdom', 'Netherlands', 'Italy', 'France', 'Japan', 'United States', 'China', 'Germany']
            }
        };

        var topSalesChart = new ApexCharts($('#top_sales')[0], topSalesBar);
        topSalesChart.render();

        var locationId = $('#locationSelect').val();

        $.ajax({
            @if(Auth::user()->hasRole(['SuperAdmin', 'Auditor', 'Finance', 'Pimpinan']))
            url: '{{ route('getTopSales') }}' + (locationId ? '?lokasi_id=' + locationId : ''),
            @else
            url: '{{ route('getTopSales') }}',
            @endif
            method: 'GET',
            success: function(response) {
                if (response.labels && response.data) {
                    topSalesChart.updateOptions({
                        xaxis: {
                            categories: response.labels
                        }
                    });
                    topSalesChart.updateSeries([{
                        data: response.data
                    }]);
                } else {
                    console.error('Invalid data format received from server');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching top sales data:", error);
            }
        });
    }

    if ($('#top_minus_produk').length > 0) {
        var topMinusProductsOptions = {
            chart: {
                height: 350,
                type: 'bar',
                stacked: true,
                toolbar: {
                    show: false,
                }
            },
            series: [],
            xaxis: {
                categories: []
            },
            yaxis: {
                title: {
                    text: 'Jumlah Stok',
                },
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value.toLocaleString('id-ID');
                    }
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                },
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                offsetX: 40
            },
            colors: ['#FF4560', '#008FFB']
        };

        var topMinusProductsChart = new ApexCharts($('#top_minus_produk')[0], topMinusProductsOptions);
        topMinusProductsChart.render();

        var locationId = $('#locationSelect').val();
        var InvenId = $('#InvenSelect').val();
        var dateInden = $('#filterDate').val();
        // console.log(dateInden);

        $.ajax({
            @if(Auth::user()->hasRole(['SuperAdmin', 'Auditor', 'Finance', 'Pimpinan']))
            url: '{{ route('getTopMinusProduk') }}' + (locationId ? '?lokasi_id=' + locationId : '') +'&'+ (InvenId ? 'inven=' + InvenId : '') +'&'+ (dateInden ? 'dateInden=' + dateInden : ''),
            @else
            url: '{{ route('getTopMinusProduk') }}' + (InvenId ? '?inven=' + InvenId : '') +'&'+ (dateInden ? 'dateInden=' + dateInden : ''),
            @endif
            method: 'GET',
            success: function(response) {
                if (response.labels && response.series && response.series.length === 2) {
                    topMinusProductsChart.updateOptions({
                        xaxis: {
                            categories: response.labels
                        }
                    });
                    topMinusProductsChart.updateSeries([{
                            name: 'Melewati Minimal Stok',
                            data: response.series[1].data
                        },
                        {

                            name: 'Stok Saat Ini',
                            data: response.series[0].data
                        }
                    ]);
                } else {
                    console.error('Invalid data format received from server');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching top minus products data:", error);
            }
        });
    }

    if ($('#top_produk_chart').length > 0) {
        var topProductsOptions = {
            chart: {
                height: 350,
                type: 'bar',
                toolbar: {
                    show: false,
                }
            },
            series: [{
                name: 'Jumlah Terjual',
                data: []
            }],
            xaxis: {
                categories: []
            },
            yaxis: {
                title: {
                    text: 'Jumlah Terjual',
                },
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value.toLocaleString('id-ID');
                    }
                }
            }
        };

        var topProductsChart = new ApexCharts($('#top_produk_chart')[0], topProductsOptions);
        topProductsChart.render();

        var locationId = $('#locationSelect').val();

        // Fetch data for Top Products Chart
        $.ajax({
            @if(Auth::user()->hasRole(['SuperAdmin', 'Auditor', 'Finance', 'Pimpinan']))
            url: '{{ route('getTopProduk') }}' + (locationId ? '?lokasi_id=' + locationId : ''),
            @else
            url: '{{ route('getTopProduk') }}',
            @endif
            method: 'GET',
            success: function(response) {
                topProductsChart.updateOptions({
                    xaxis: {
                        categories: response.labels
                    }
                });
                topProductsChart.updateSeries([{
                    data: response.data
                }]);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching top products data:", error);
            }
        });
    }

    if ($('#loyalty').length > 0) {
        var loyaltyChart = {
            chart: {
                height: 350,
                type: 'donut',
                toolbar: {
                    show: false,
                }
            },
            series: [], 
            labels: [], 
            dataLabels: {
                enabled: true,
                formatter: function (val, opts) {
                    return opts.w.config.series[opts.seriesIndex]; // Display the series value
                },
                style: {
                    fontSize: '14px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 'bold',
                    colors: undefined
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val;
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            plotOptions: {
                pie: {
                    donut: {
                        labels: {
                            show: true,
                            name: {
                                show: true
                            },
                            value: {
                                show: true,
                                formatter: function(val) {
                                    return val;
                                }
                            }
                        }
                    }
                }
            },
            legend: {
                show: true,
                position: 'right', 
                offsetX: 0,
                offsetY: 0,
                labels: {
                    colors: ['#000'], 
                    useSeriesColors: false 
                },
                itemMargin: {
                    horizontal: 10, // Space between items
                    vertical: 5 // Space between rows
                }
            }
        };

        var donut = new ApexCharts($('#loyalty')[0], loyaltyChart);
        donut.render();

        $.ajax({
            @if(Auth::user()->hasRole(['SuperAdmin', 'Auditor', 'Finance', 'Pimpinan']))
            url: '{{ route('getLoyalty') }}' + (locationId ? '?lokasi_id=' + locationId : ''),
            @else
            url: '{{ route('getLoyalty') }}',
            @endif
            method: 'GET',
            success: function(response) {
                if (response.labels && response.data) {
                    donut.updateOptions({
                        labels: response.labels
                    });
                    donut.updateSeries(response.data);
                } else {
                    console.error('Invalid data format received from server');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching top products data:", error);
            }
        });
    }
    @if(Auth::user()->hasRole(['SuperAdmin', 'Auditor', 'Finance', 'Pimpinan']))
        if ($('#uang_keluar_chart').length > 0) {
            var uang_keluarChart = {
                chart: {
                    height: 350,
                    type: 'donut',
                    toolbar: {
                        show: false,
                    }
                },
                series: [], 
                labels: [], 
                dataLabels: {
                    enabled: true,
                    formatter: function (val, opts) {
                        return opts.w.config.series[opts.seriesIndex]; // Display the series value
                    },
                    style: {
                        fontSize: '14px',
                        fontFamily: 'Helvetica, Arial, sans-serif',
                        fontWeight: 'bold',
                        colors: undefined
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val;
                        }
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                name: {
                                    show: true
                                },
                                value: {
                                    show: true,
                                    formatter: function(val) {
                                        return val;
                                    }
                                }
                            }
                        }
                    }
                },
                legend: {
                    show: true,
                    position: 'right', 
                    offsetX: 0,
                    offsetY: 0,
                    labels: {
                        colors: ['#000'], 
                        useSeriesColors: false 
                    },
                    itemMargin: {
                        horizontal: 10, // Space between items
                        vertical: 5 // Space between rows
                    }
                }
            };

            var donut2 = new ApexCharts($('#uang_keluar_chart')[0], uang_keluarChart);
            donut2.render();

            $.ajax({
                url: '{{ route('uang_keluar') }}',
                method: 'GET',
                success: function(response) {
                    console.log(response)
                    if (response.labels && response.data) {
                        donut2.updateOptions({
                            labels: response.labels
                        });
                        donut2.updateSeries(response.data);
                    } else {
                        console.error('Invalid data format received from server');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching uang keluar data:", error);
                }
            });
        }

        if ($('#tagihan_supplier_chart').length > 0) {
        var tagihanSupplierOptions = {
            chart: {
                height: 350,
                type: 'bar',
                toolbar: {
                    show: false,
                }
            },
            series: [{
                name: 'Tagihan Supplier',
                data: []
            }],
            xaxis: {
                categories: []
            },
            yaxis: {
                title: {
                    text: 'Nominal',
                },
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value.toLocaleString('id-ID');
                    }
                }
            }
        };

        var tagihanSupplierChart = new ApexCharts($('#tagihan_supplier_chart')[0], tagihanSupplierOptions);
        tagihanSupplierChart.render();

        var locationId = $('#locationSelect').val();

        $.ajax({
            url: '{{ route('tagihan_supplier') }}',
            method: 'GET',
            success: function(response) {
                console.log(response)
                tagihanSupplierChart.updateOptions({
                    xaxis: {
                        categories: response.labels
                    }
                });
                tagihanSupplierChart.updateSeries([{
                    data: response.data
                }]);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching tagihan supplier data:", error);
            }
        });
    }
    @endif

    function deleteData(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "GET",
                    url: "/aset/" + id + "/delete",
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        toastr.success(response.msg, 'Success', {
                            closeButton: true,
                            tapToDismiss: false,
                            rtl: false,
                            progressBar: true
                        });

                        setTimeout(() => {
                            location.reload()
                        }, 2000);
                    },
                    error: function(error) {
                        toastr.error(JSON.parse(error.responseText).msg, 'Error', {
                            closeButton: true,
                            tapToDismiss: false,
                            rtl: false,
                            progressBar: true
                        });
                    }
                });
            }
        });
    }
</script>
@endsection