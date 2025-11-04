<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_name',
        'weekend_start_day',
        'ti_agent_code',
        'address',
        'email',
        'state',
        'region',
    ];

    protected $casts = [
        'weekend_start_day' => 'string',
    ];
}
