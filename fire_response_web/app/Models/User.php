<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
// use Illuminate\Auth\Passwords\CanResetPassword;

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
        'userRole',
        'rejection_reason',
        'reapply_allowed',
        'reapplication_count',
        'id_image',
        'profile_image',
        'last_rejection_at'
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
            'reapply_allowed' => 'boolean',
            'reapplication_count' => 'integer',
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

    public function markedAsFalseAlarm() {
    return $this->hasMany(FireReport::class, 'marked_as_false_alarm_by');
    }

    public function isRejected(): bool
    {
        return $this->userStatus === 'Rejected';
    }

    public function canReapply(): bool
    {
        if (!$this->reapply_allowed || $this->userStatus !== 'Rejected') {
            return false;
        }

        if (!$this->last_rejection_at) {
            return true;
        }

        return now()->diffInDays($this->last_rejection_at) >= 7;
    }

}
