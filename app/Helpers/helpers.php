<?php

if (!function_exists('formatRupiah')) {
    function formatRupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('formatTanggal')) {
    function formatTanggal($date)
    {
        return \Carbon\Carbon::parse($date)->format('d/m/Y');
    }
}