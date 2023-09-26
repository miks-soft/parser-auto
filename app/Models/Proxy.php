<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proxy extends Model
{
    protected $fillable = [
        'address', 'last_used',
    ];

    protected $dates = [
        'last_used',
    ];

    public $timestamps = false;
}
