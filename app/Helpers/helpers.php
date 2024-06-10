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


if (!function_exists('formatTanggalInd')) {
    function formatTanggalInd($date = null) {
        \Carbon\Carbon::setLocale('id');
        $carbonDate = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::now();
        return $carbonDate->translatedFormat('d F Y');
    }
}

if (! function_exists('base64_image')) {
    function base64_image($path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
}

if (!function_exists('stringToCamelCase')) {
    function stringToCamelCase($string) {
        $string = strtolower($string);
        $string = str_replace('_', ' ', $string);
        $string = ucwords($string);
        return $string;
    }
}
