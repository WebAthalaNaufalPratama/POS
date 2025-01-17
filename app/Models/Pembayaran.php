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
        'mutasiinden_id',
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

    public function pembelian()
    {
        return $this->belongsTo(Invoicepo::class, 'invoice_purchase_id');
    }

    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'rekening_id');
    }
    public function sewa()
    {
        return $this->belongsTo(InvoiceSewa::class, 'invoice_sewa_id');
    }
    public function mutasiinden()
    {
        return $this->belongsTo(Mutasiindens::class, 'mutasiinden_id');
    }
    public function po()
    {
        return $this->belongsTo(Invoicepo::class, 'invoice_purchase_id');
    }
    public function retur()
    {
        return $this->belongsTo(Returpembelian::class, 'retur_pembelian_id');
    }
    public function returinden()
    {
        return $this->belongsTo(Returinden::class, 'returinden_id');
    }
}

