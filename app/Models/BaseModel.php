<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';
}