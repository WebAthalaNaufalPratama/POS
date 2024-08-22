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


if (!function_exists('tanggallengkap')) {
    function tanggallengkap($tanggal, $format = 'd F Y H:i:s')
    {
        return $tanggal ? Carbon::parse($tanggal)->translatedFormat($format) : '-';
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

if (!function_exists('NumberToWords')) {
    function NumberToWords($number) {
        $number = (int) $number;
        $words = '';
    
        $units = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan'];
        $teens = ['Sepuluh', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas', 'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];
        $tens = ['', 'Sepuluh', 'Dua Puluh', 'Tiga Puluh', 'Empat Puluh', 'Lima Puluh', 'Enam Puluh', 'Tujuh Puluh', 'Delapan Puluh', 'Sembilan Puluh'];
        $hundreds = ['', 'Seratus', 'Dua Ratus', 'Tiga Ratus', 'Empat Ratus', 'Lima Ratus', 'Enam Ratus', 'Tujuh Ratus', 'Delapan Ratus', 'Sembilan Ratus'];
    
        if ($number == 0) {
            return 'Nol';
        }
    
        if ($number < 10) {
            $words = $units[$number];
        } elseif ($number < 20) {
            $words = $teens[$number - 10];
        } elseif ($number < 100) {
            $words = $tens[intdiv($number, 10)] . ($number % 10 != 0 ? ' ' . $units[$number % 10] : '');
        } elseif ($number < 1000) {
            $words = $hundreds[intdiv($number, 100)] . ($number % 100 != 0 ? ' ' . NumberToWords($number % 100) : '');
        } elseif ($number < 1000000) {
            $words = NumberToWords(intdiv($number, 1000)) . ' Ribu' . ($number % 1000 != 0 ? ' ' . NumberToWords($number % 1000) : '');
        } elseif ($number < 1000000000) {
            $words = NumberToWords(intdiv($number, 1000000)) . ' Juta' . ($number % 1000000 != 0 ? ' ' . NumberToWords($number % 1000000) : '');
        }
    
        return $words;
    }
    
}
