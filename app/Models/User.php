<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'role',
        'status',
        'avatar',
        'bio',
        'last_login_at',
        'last_login_ip',
        'branch_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is Branch Manager.
     */
    public function isBM(): bool
    {
        return $this->role === 'branch_manager';
    }

    /**
     * Check if user is Assistant Branch Manager.
     */
    public function isABM(): bool
    {
        return $this->role === 'assistant_branch_manager';
    }

    /**
     * Check if user is Operations Officer.
     */
    public function isOO(): bool
    {
        return $this->role === 'operation_officer';
    }

    /**
     * Check if user is Headquarters.
     */
    public function isHQ(): bool
    {
        return $this->role === 'headquarters';
    }

    /**
     * Get role display name.
     */
    public function getRoleDisplayAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'branch_manager' => 'Branch Manager',
            'assistant_branch_manager' => 'Assistant Branch Manager',
            'operation_officer' => 'Operations Officer',
            'headquarters' => 'Headquarters',
            default => ucfirst(str_replace('_', ' ', $this->role)),
        };
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user has admin panel access (admin and staff roles).
     */
    public function hasAdminAccess(): bool
    {
        return in_array($this->role, ['admin', 'headquarters', 'branch_manager', 'assistant_branch_manager', 'operation_officer']);
    }

    /**
     * Check if user can manage users (admin only).
     */
    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if user can manage settings (all staff roles).
     */
    public function canManageSettings(): bool
    {
        return $this->hasAdminAccess();
    }

    /**
     * Check if user can view audit trails (admin only).
     */
    public function canViewAuditTrails(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if user can manage forms (admin only).
     */
    public function canManageForms(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if user can manage branches (admin only).
     */
    public function canManageBranches(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if user can manage QR codes (admin only).
     */
    public function canManageQrCodes(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Get the audit trails for the user.
     */
    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class);
    }

    /**
     * Get the branch that the user belongs to.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
