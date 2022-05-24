<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Event extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'calendar_id',
        'category_id',
        'user_id',
        'title',
        'description',
        'location',
        'published',
        'color',
        'start',
        'end'
    ];

    protected static function boot()
    {
        parent::boot();

        Event::creating(function ($model) {
            $model->user_id = Auth::user()->id;
        });
    }

    public function calendar(){
        return $this->belongsTo(Calendar::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
