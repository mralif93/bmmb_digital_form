<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use HasFactory, SoftDeletes;

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
