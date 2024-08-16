<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bhavcopy extends Model
{
    use HasFactory;

    protected $fillable = [
        'symbol', 'series', 'open', 'high', 'low', 'close', 'last', 'prev_close', 'tot_trd_qt', 'tot_trd_val', 'timestamp',
    ];
}