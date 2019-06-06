<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Block extends BaseModel
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'flag'];

    public function items()
    {
        return $this->hasMany(BlockItem::class, 'blockId', 'id');
    }

    public function sortedItems()
    {
        return $this->items()->orderBy('sort', 'desc');
    }
}
