<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompositePrimaryKey;

class Target extends Model
{
    use HasFactory, HasCompositePrimaryKey;

    protected $primaryKey = ['email', 'calendar_id'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'email',
        'calendar_id',
        'notifyed'
    ];

    public function calendar(){
        return $this->belongsTo(Calendar::class);
    }
}
