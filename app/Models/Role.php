<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name'];

    public const VALID_ROLES = ['admin', 'supervisor'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($role) {
            if (! in_array($role->name, self::VALID_ROLES)) {
                throw new \InvalidArgumentException("Invalid role name: {$role->name}");
            }
        });
    }
}
