<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Prevent privilege escalation via mass assignment.
     *
     * @var list<string>
     */
    protected $guarded = [
        'is_admin',
    ];

    /**
     * Seeder-only escape hatch for controlled admin promotion.
     */
    protected static bool $allowAdminPromotion = false;

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
            'is_admin' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Run a callback while allowing admin promotion (used by trusted seeders).
     */
    public static function allowAdminPromotion(callable $callback): mixed
    {
        $previous = static::$allowAdminPromotion;
        static::$allowAdminPromotion = true;

        try {
            return $callback();
        } finally {
            static::$allowAdminPromotion = $previous;
        }
    }

    protected static function booted(): void
    {
        static::saving(function (self $user): void {
            if (! $user->isDirty('is_admin')) {
                return;
            }

            if ((bool) $user->is_admin === true && ! static::$allowAdminPromotion) {
                $user->is_admin = (bool) ($user->getOriginal('is_admin') ?? false);
            }
        });
    }
}
