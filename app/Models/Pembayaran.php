<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Pembayaran extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected static $logAttributes = [
        'invoice_penjualan_id',
        'mutasi_id',
        'invoice_purchase_id',
        'retur_pembelian_id',
        'no_invoice_bayar',
        'nominal',
        'rekening_id',
        'tanggal_bayar',
        'bukti',
        'status_bayar',
        'invoice_sewa_id',
        'cara_bayar',
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'invoice_penjualan_id');
    }

    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'rekening_id');
    }
    public function sewa()
    {
        return $this->belongsTo(InvoiceSewa::class, 'invoice_sewa_id');
    }
}
