<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

    protected static function boot()
    {
        parent::boot();

        Calendar::creating(function($model) {
            $model->user_id = Auth::user()->id;
        });
    }

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
