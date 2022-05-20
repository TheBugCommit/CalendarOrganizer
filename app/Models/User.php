<?php

namespace App\Models;

use App\Casts\HashCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const GENDERS = [
        'M' => 'Male',
        'F' => 'Female',
        'O' => 'Other'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'surname1',
        'surname2',
        'locked',
        'birth_date',
        'gender',
        'nation_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'password' => HashCast::class,
    ];

    public function nation()
    {
        return $this->belongsTo(Nation::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function calendars()
    {
        return $this->hasMany(Calendar::class);
    }

    public function helperCalendars()
    {
        return $this->belongsToMany(Calendar::class);
    }

    public function hasCalendar($calendar_id)
    {
        return $this->calendars()->where('id', $calendar_id)->exists();
    }
}
