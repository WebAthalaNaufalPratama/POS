<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Kontrak extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];
    protected static $logAttributes = [
        'no_kontrak',
        'lokasi_id',
        'masa_sewa',
        'tanggal_kontrak',
        'tanggal_mulai',
        'tanggal_selesai',
        'customer_id',
        'pic',
        'handphone',
        'alamat',
        'no_npwp',
        'nama_npwp',
        'rekening_id',
        'catatan',
        'ppn_persen',
        'ppn_nominal',
        'pph_persen',
        'pph_nominal',
        'ongkir_id',
        'ongkir_nominal',
        'promo_id',
        'total_promo',
        'subtotal',
        'total_harga',
        'status',
        'file',
        'sales',
        'pembuat',
        'penyetuju',
        'pemeriksa',
        'tanggal_sales',
        'tanggal_pembuat',
        'tanggal_penyetuju',
        'tanggal_pemeriksa',
    ];

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
