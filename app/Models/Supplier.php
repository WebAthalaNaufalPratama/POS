<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    public function pembelian (){
        return $this->hasMany(Pembelian::class);
    }
    public function poinden (){
        return $this->hasMany(Poinden::class);
    }
}
