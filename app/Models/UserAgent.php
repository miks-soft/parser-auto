<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAgent extends Model
{
    protected $fillable = [
        'agent', 'last_used',
    ];

    protected $dates = [
        'last_used',
    ];

    public $timestamps = false;
}
