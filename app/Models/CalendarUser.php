<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'calendar_id'
    ];
}
