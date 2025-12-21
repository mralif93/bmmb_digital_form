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
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'branch_id',
        // MAP integration fields
        'map_user_id',
        'map_staff_id',
        'username',
        'map_position',
        'map_last_sync',
        'is_map_synced',
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
     * Check if user is IAM (Identity and Access Management).
     */
    public function isIAM(): bool
    {
        return $this->role === 'iam';
    }

    /**
     * Check if user is CFE (Customer Finance Executive).
     * Also checks MAP position 3 for SSO users.
     */
    public function isCFE(): bool
    {
        return $this->role === 'cfe' || $this->map_position === '3';
    }

    /**
     * Get role display name.
     */
    public function getRoleDisplayAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Administrator',
            'branch_manager' => 'Branch Manager',
            'assistant_branch_manager' => 'Assistant Branch Manager',
            'operation_officer' => 'Operations Officer',
            'headquarters' => 'Headquarters',
            'iam' => 'Identity & Access Management',
            'cfe' => 'Customer Finance Executive',
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
     * Check if user has admin panel access (HQ, BM, CFE roles).
     */
    public function hasAdminAccess(): bool
    {
        return $this->isAdmin() || $this->isHQ() || $this->isBM() || $this->isCFE();
    }

    /**
     * Check if user can manage users (admin and IAM).
     */
    public function canManageUsers(): bool
    {
        return $this->isAdmin() || $this->isIAM();
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
     * Check if user can manage forms (admin and HQ).
     */
    public function canManageForms(): bool
    {
        return $this->isAdmin() || $this->isHQ();
    }

    /**
     * Check if user can manage branches (admin and HQ).
     */
    public function canManageBranches(): bool
    {
        return $this->isAdmin() || $this->isHQ();
    }

    /**
     * Check if user can manage QR codes (admin and HQ).
     */
    public function canManageQrCodes(): bool
    {
        return $this->isAdmin() || $this->isHQ();
    }

    /**
     * Check if user is synced from MAP system
     */
    public function isMapUser(): bool
    {
        return (bool) $this->is_map_synced;
    }

    /**
     * Get MAP position display name
     */
    public function getMapPositionDisplayAttribute(): ?string
    {
        if (!$this->map_position) {
            return null;
        }

        return match ($this->map_position) {
            '1' => 'HQ',
            '2' => 'Branch Manager',
            '3' => 'CFE',
            '4' => 'COD',
            '5' => 'CRR',
            '6' => 'CSO',
            '7' => 'CFE-HQ',
            '8' => 'CCQ',
            '9' => 'Operation Officer/ABM',
            '10' => 'OO',
            default => 'Unknown',
        };
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
