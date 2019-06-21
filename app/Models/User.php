<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends BaseModel implements JWTSubject, Authenticatable
{
    use SoftDeletes;

    const ACTIVE = 1; // 激活
    const FREEZE = 0; // 冻结

    protected $fillable = [
        'name', 'sex', 'status', 'avatar', 'openid',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

//    protected $hidden = [
//        'password',
//    ];

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     */
    public function setRememberToken($value)
    {
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
    }

    public function activities()
    {
        return $this->hasMany(ActivitySignUp::class, 'user_id', 'id')
            ->where('status', '=', 1);
    }

    public function try_uses()
    {
        return $this->hasMany(UseSignUp::class, 'user_id', 'id')
            ->where('status', '=', 1);
    }

    public function reports()
    {
        return $this->hasMany(ExperienceReport::class, 'user_id', 'id');
    }

    public function user_logs()
    {
        return $this->hasMany(UserLog::class, 'user_id', 'id');
    }
}
