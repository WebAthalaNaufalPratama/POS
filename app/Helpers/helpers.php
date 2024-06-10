<?php

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

if (! function_exists('base64_image')) {
    function base64_image($path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
}

