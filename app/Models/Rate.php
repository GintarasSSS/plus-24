<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    protected $table = 'rates';

    protected $fillable = [
        'base',
        'date',
        'rates'
    ];

    protected $casts = [
        'rates' => 'json',
        'date' => 'datetime:Y-m-d'
    ];
}
