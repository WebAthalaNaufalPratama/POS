<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }

    public function kontrak()
    {
        return $this->hasMany(Kontrak::class);
    }
}
