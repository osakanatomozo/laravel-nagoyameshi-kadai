<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $casts = [
        'lowest_price' => 'integer',
        'highest_price' => 'integer',
        'seating_capacity' => 'integer',
    ];
}
