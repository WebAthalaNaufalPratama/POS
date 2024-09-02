<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class FormPerangkai extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];
    protected static $logAttributes = [
        'no_form',
        'jenis_rangkaian',
        'tanggal',
        'perangkai_id'
    ];

    public function perangkai(){
        return $this->belongsTo(Karyawan::class, 'perangkai_id', 'id');
    }

    public function produk_terjual(){
        return $this->belongsTo(Produk_Terjual::class, 'no_form', 'no_form');
    }
}
