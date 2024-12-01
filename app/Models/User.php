<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
     * Boot method to assign default role during user creation.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if ($user->role_id) {
                return;
            }

            // search user role
            $defaultRole = Role::where('name', 'User')->first();

            // if user role not exist create it
            if (!$defaultRole) {
                $defaultRole = Role::create(['name' => 'User']);
            }

            // set user role to user instance
            $user->role_id = $defaultRole->id;
        });
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'users_exams', 'user_id', 'exam_id');
    }

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
        ];
    }
}
