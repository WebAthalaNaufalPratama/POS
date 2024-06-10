<?php
use Carbon\Carbon;

if (!function_exists('formatRupiah')) {
    function formatRupiah($amount)
    {
        return 'Rp. ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('formatRupiah2')) {
    function formatRupiah2($amount2)
    {
        return number_format($amount2, 0, ',', '.');
    }
}

if (!function_exists('formatTanggal')) {
    function formatTanggal($date)
    {
        return \Carbon\Carbon::parse($date)->format('d/m/Y');
    }

}

if (!function_exists('tanggalindo')) {
    function tanggalindo($date)
    {
        $bulan = [
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];
        $tanggal = Carbon::parse($date)->format('d');
        $bulanIndex = Carbon::parse($date)->format('n');
        $tahun = Carbon::parse($date)->format('Y');

        return $tanggal . ' ' . $bulan[$bulanIndex] . ' ' . $tahun;
    }
}