<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

class TransaksiKas extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];
    protected static $logAttributes = [
        'lokasi_penerima',
        'lokasi_pengirim',
        'rekening_penerima',
        'rekening_pengirim',
        'nominal',
        'tanggal',
        'file',
        'status',
        'keterangan',
    ];

    public function lok_penerima()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_penerima');
    }

    public function lok_pengirim()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_pengirim');
    }

    public function rek_penerima()
    {
        return $this->belongsTo(Rekening::class, 'rekening_penerima');
    }

    public function rek_pengirim()
    {
        return $this->belongsTo(Rekening::class, 'rekening_pengirim');
    }

    public static function getSaldo($rekening_id = null, $metode = null, $status = 'DIKONFIRMASI', $maxDate = null, $lokasi = null)
    {
        $incomeQuery = self::query();
        $outcomeQuery = self::query();
        $saldoAwalQuery = Rekening::query();

        $incomeQuery->where('status', $status);
        $outcomeQuery->where('status', $status);

        if ($metode) {
            $incomeQuery->where('metode', $metode);
            $outcomeQuery->where('metode', $metode);
        }

        if ($rekening_id) {
            $incomeQuery->where('rekening_penerima', $rekening_id);
            $outcomeQuery->where('rekening_pengirim', $rekening_id);
            $saldoAwalQuery->where('id', $rekening_id);
        }

        if ($lokasi) {
            $incomeQuery->where('lokasi_penerima', $lokasi);
            $outcomeQuery->where('lokasi_pengirim', $lokasi);
        }

        if ($maxDate) {
            $incomeQuery->where('tanggal', '<=', $maxDate);
            $outcomeQuery->where('tanggal', '<=', $maxDate);
        }

        $incomeBalance = $incomeQuery->sum('nominal');
        $outcomeBalance = $outcomeQuery->sum(DB::raw('nominal + biaya_lain'));
        if($metode == 'Transfer'){
            $saldoAwal = $saldoAwalQuery->sum('saldo_awal');
        } else {
            $saldoAwal = 0;
        }
        $balance = $saldoAwal + $incomeBalance - $outcomeBalance;

        return $balance;
    }

    public static function getSaldoLokasi($lokasi_id, $maxDate = null)
    {
        if (!is_array($lokasi_id)) {
            $lokasi_id = [$lokasi_id];
        }
        $incomeQuery = self::whereIn('lokasi_penerima', $lokasi_id);
        $outcomeQuery = self::whereIn('lokasi_pengirim', $lokasi_id);

        if (isset($maxDate)) {
            $incomeQuery->where('tanggal', '<', $maxDate);
            $outcomeQuery->where('tanggal', '<', $maxDate);
        }

        $incomeBalance = $incomeQuery->sum('nominal');

        $outcomeBalance = $outcomeQuery->sum(DB::raw('nominal + biaya_lain'));

        $balance = $incomeBalance - $outcomeBalance;

        return $balance;
    }
}
