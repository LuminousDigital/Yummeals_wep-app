<?php

namespace App\Models;


use App\Enums\Activity;
use App\Enums\Ask;
use App\Enums\Status;
use App\Models\Address;
use App\Models\ReferralBonus;
use App\Models\Scopes\BranchScope;
use App\Traits\MultiTenantModelTrait;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class User extends Authenticatable implements HasMedia
{
    use InteractsWithMedia;
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use MultiTenantModelTrait;
    use Notifiable;
    use SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = "users";
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'phone',
        'branch_id',
        'country_code',
        'is_guest',
        'status',
        'email_verified_at',

        'referral_code',
        'referred_by',
        'referral_balance',
        'total_referrals'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id'                => 'integer',
        'name'              => 'string',
        'email'             => 'string',
        'password'          => 'string',
        'username'          => 'string',
        'phone'             => 'string',
        'branch_id'         => 'integer',
        'country_code'      => 'string',
        'is_guest'          => 'integer',
        'status'            => 'integer',
        'email_verified_at' => 'datetime',
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


    protected $appends = ['myrole'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new BranchScope());

        static::updating(function ($user) {
            if ($user->id === 1) {
                $user->status = Status::ACTIVE;
            }
        });
    }

    public function getMyRoleAttribute()
    {
        return $this->roles->pluck('id', 'id')->first();
    }

    public function getrole(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Role::class, 'id', 'myrole');
    }

    public function addresses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function getFirstNameAttribute(): string
    {
        $name = explode(' ', $this->name, 2);
        return $name[0];
    }

    public function getLastNameAttribute(): string
    {
        $name = explode(' ', $this->name, 2);
        return !empty($name[1]) ? $name[1] : '';
    }

    public function getImageAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('profile'))) {
            return asset($this->getFirstMediaUrl('profile'));
        }
        return asset('images/default/profile.png');
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MessageHistory::class, 'user_id', 'id')->where('is_read', Ask::NO);
    }


    public static function generateUniqueReferralCode($username)
    {
        $baseCode = Str::upper(Str::substr(preg_replace('/[^A-Za-z]/', '', Str::slug($username)), 0, 6));
        $code = $baseCode . rand(100, 999);
        
        $counter = 1;
        while (self::where('referral_code', $code)->exists()) {
            $code = $baseCode . rand(100, 999);
            if ($counter++ > 10) {
                $code = Str::upper(Str::random(8));
                break;
            }
        }
        
        return $code;
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function givenBonuses()
    {
        return $this->hasMany(ReferralBonus::class, 'referrer_id');
    }

    public function receivedBonuses()
    {
        return $this->hasMany(ReferralBonus::class, 'referee_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}
