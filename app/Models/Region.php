<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = [
        'name',
        'links',
    ];

    /**
     * Get the branches in this region.
     */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
