<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kontrak extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    public function lokasi(){
        return $this->belongsTo(Lokasi::class);
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function rekening(){
        return $this->belongsTo(Rekening::class);
    }
    
    public function promo(){
        return $this->belongsTo(Promo::class);
    }
    
    public function sales(){
        return $this->belongsTo(Karyawan::class, 'sales', 'id');
    }
        
    public function pembuat(){
        return $this->belongsTo(Karyawan::class, 'pembuat', 'id');
    }
        
    public function penyetuju(){
        return $this->belongsTo(Karyawan::class, 'penyetuju', 'id');
    }
        
    public function pemeriksa(){
        return $this->belongsTo(Karyawan::class, 'pemeriksa', 'id');
    }
}
