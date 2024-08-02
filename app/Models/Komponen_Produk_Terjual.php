<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

class Komponen_Produk_Terjual extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $table = 'komponen_produk_terjuals';
    protected $guarded = ['id'];

    protected static $logAttributes =[
        'produk_terjual_id',
        'kode_produk',
        'nama_produk',
        'tipe_produk',
        'kondisi',
        'deskripsi',
        'jumlah',
        'harga_satuan',
        'harga_total',
    ];

    public function produk_terjual(){
        return $this->belongsTo(Produk_Terjual::class, 'produk_terjual_id', 'id');
    }

    public function produk(){
        return $this->belongsTo(Produk::class, 'kode_produk', 'kode');
    }

    public function kondisi(){
        return $this->belongsTo(Kondisi::class, 'kondisi', 'id');
    }

    public function activityLogs()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function data_kondisi()
    {
        return $this->belongsTo(Kondisi::class, 'kondisi', 'id');
    }

    public function kondisi_dit()
    {
        return $this->belongsTo(Kondisi::class, 'kondisi_diterima', 'id');
    }
}
