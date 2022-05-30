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
        'description',
        'google_calendar_id',
        'start_date',
        'end_date',
    ];

    protected static function boot()
    {
        parent::boot();

        Calendar::creating(function ($model) {
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

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function targets()
    {
        return $this->hasMany(Target::class);
    }

    public function getEventsByRange($start = null, $end = null)
    {
        if($start == null && $end == null)
            return $this->events()->where('published', true)->get();

        if($start != null && $end != null){
            return $this->events()
                ->where('start', '>=', $start)
                ->where('start', '<', $end)
                ->where('end', '<=', $end)
                ->where('end', '>', $start)
                ->get();
        }

        if($start != null)
            return $this->events()->where('start', '>=' ,$start)->get();

        if($end != null)
            return $this->events()->where('end', '<=' ,$end)->get();
    }

    public static function getById($id, $user_id)
    {
        $user = User::find($user_id);
        $calendars = $user->helperCalendars->merge($user->calendars);
        return $calendars->where('id', $id)->first();
    }

}
