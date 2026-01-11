<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable 
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'is_active' => 'boolean',
        ];
    }

    /**
     * تحديد من يمكنه الدخول للوحة التحكم
     */
    // public function canAccessPanel(Panel $panel): bool
    // {
    //     return $this->is_active && in_array($this->role, ['manager', 'admin']);
    // }

    /**
     * التحقق من كون المستخدم أدمن
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * التحقق من كون المستخدم نشط
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Scope للحصول على الأدمنز فقط
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope للحصول على المستخدمين النشطين
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isManager();
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isManager();
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->isManager();
    }

    public static function canDelete($record): bool
    {
        // منع حذف نفسه لو المانجر مستخدم هنا
        return auth()->user()?->isManager() && $record->id !== auth()->id();
    }

}