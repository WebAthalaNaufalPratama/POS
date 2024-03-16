<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tipe_Lokasi extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'tipe_lokasis';
    protected $guarded = ['id'];
}
