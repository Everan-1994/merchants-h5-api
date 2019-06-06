<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class AdminRoleUser extends BaseModel
{
    use SoftDeletes;

    protected $table = 'admin_role_users';
}
