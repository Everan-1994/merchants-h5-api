<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Block extends BaseModel
{
    use SoftDeletes;

    protected $fillable = ['name', 'front_cover', 'watch_times', 'status', 'sort'];
}
