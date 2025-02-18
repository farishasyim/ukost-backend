<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'identity_number',
        'identity_card',
        'profile_picture',
        'gender',
        'phone',
        'date_of_birth',
        'deleted_at',
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

    protected $appends = ["profile_link", "identity_card_link", "is_default"];

    public function getProfileLinkAttribute()
    {
        if (isset($this->profile_picture)) {
            return env('APP_URL') . '/profile_picture/' . $this->profile_picture;
        }
        return env('APP_URL') . '/default.jpg';
    }

    public function getIsDefaultAttribute()
    {
        if ($this->role == "customer") {
            return $this->password == '$2y$12$R21X/OxAZ1gdLXf5uX.bwOlWtmN5kM8eiMChlSgyPfHR9Yj6ctjdm';
        }
        return false;
    }

    public function getIdentityCardLinkAttribute()
    {
        if (isset($this->identity_card)) {
            return env('APP_URL') . '/identity_card/' . $this->identity_card;
        }
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function pivot()
    {
        return $this->hasOne(PivotRoom::class, "customer_id")->whereNull("left_at")->ofMany("created_at", "max");;
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, "user_id");
    }
}
