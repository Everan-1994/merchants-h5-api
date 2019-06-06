<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class AdminUser extends BaseModel implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, SoftDeletes;

    protected $table = 'admin_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'realname',
        'email',
        'password',
        'isEnable',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

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
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function roles()
    {
        return $this->hasManyThrough(
            AdminRole::class,
            AdminRoleUser::class,
            'adminUserId',
            'id',
            '',
            'adminRoleId'
        );
    }

    /**
     * 获取所有用户拥有的角色.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function adminRoles()
    {
        return $this->belongsToMany(
            AdminRole::class,
            'admin_role_users',
            'adminUserId',
            'adminRoleId'
        );
    }

    /**
     * 获取用户拥有的角色.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adminRoleUsers()
    {
        return $this->hasMany(
            AdminRoleUser::class,
            'adminUserId'
        );
    }
}
