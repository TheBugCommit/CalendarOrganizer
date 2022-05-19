<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'title',
        'start_date',
        'end_date'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function helpers()
    {
        return $this->belongsToMany(User::class);
    }

    public function events(){
        return $this->hasMany(Event::class);
    }

    public function targets()
    {
        return $this->hasMany(Target::class);
    }
}
