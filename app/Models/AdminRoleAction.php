<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class AdminRoleAction extends BaseModel
{
    use SoftDeletes;
    protected $table = 'admin_role_actions';
}
