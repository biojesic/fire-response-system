<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
     * @var list<string>
     */
    protected $fillable = [
        'userFirstName',
        'userLastName',
        'email',
        'userContactNumber',
        'userAddress',
        'latitude',
        'longitude',
        'password',
        'userBirthDate',
        'userStatus',
        'userRole'
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
        ];
    }

    public function FireReports()
    {
        return $this->hasMany(FireReports::class, 'reported_by');
    }

    public function fireStation()
{
    return $this->belongsTo(FireStation::class);
}

public function firefighter()
{
    return $this->hasOne(Firefighter::class, 'userId');
}

}
