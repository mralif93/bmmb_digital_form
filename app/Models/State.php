<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get the branches in this state.
     */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
