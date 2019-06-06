<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class AdminRole extends BaseModel
{
    use SoftDeletes;

    protected $table = 'admin_roles';

    protected $fillable = [
        'name', 'isSuper',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function actions()
    {
        return $this->hasManyThrough(
            Action::class,
            AdminRoleAction::class,
            'adminRoleId',
            'id',
            '',
            'actionId'
        );
    }

    /**
     * 获取拥有角色的所有成员.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function adminUsers()
    {
        return $this->belongsToMany(
            AdminUser::class,
            'admin_role_users',
            'adminRoleId',
            'adminUserId'
        );
    }

    /**
     * 获取角色下所有权限.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adminRoleActions()
    {
        return $this->hasMany(
            AdminRoleAction::class,
            'adminRoleId'
        );
    }
}
