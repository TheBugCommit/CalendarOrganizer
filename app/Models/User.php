<?php

namespace App\Models;

use App\Casts\HashCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
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
        'role_id',
        'google_access_token_json'
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

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['full_name'];

    public function nation()
    {
        return $this->belongsTo(Nation::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
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

    public function hasHelperCalendar($calendar_id)
    {
        return $this->helperCalendars()->where('calendar_id', $calendar_id)->exists();
    }

    public function hasEvent($event_id)
    {
        return $this->events()->where('id', $event_id)->exists();
    }

    public function hasCategory($category_id)
    {
        return $this->categories()->where('id', $category_id)->exists();
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->surname1 . ' ' . $this->surname2;
    }

    public static function getAll(array $fields = null)
    {
        if ($fields == null)
            return User::all();
        else
            return User::select($fields)->get();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
