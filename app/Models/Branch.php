<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

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

    /**
     * Get the branch name (alias for branch_name).
     */
    public function getNameAttribute(): string
    {
        return $this->branch_name;
    }

    /**
     * Get the branch code (alias for ti_agent_code).
     */
    public function getCodeAttribute(): string
    {
        return $this->ti_agent_code;
    }

    /**
     * Get the users that belong to this branch.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
