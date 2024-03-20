<?php

namespace App\Models;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory, LogsActivity;
    
    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
                ->logOnly(['title', 'slug','content'])
                ->setDescriptionForEvent(function(string $eventName) {
                    return "This model has been $eventName";
                })
                ->useLogName('Post');
    }


    public function user(){
        return $this->belongsTo(User::class);
    }
}