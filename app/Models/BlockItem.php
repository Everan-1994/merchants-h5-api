<?php
/**
 * User: everan
 * Date: 2019/3/27
 * Time: 11:52 AM.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class BlockItem extends BaseModel
{
    use SoftDeletes;

    protected $table = 'block_items';

    protected $fillable = ['blockId', 'title', 'description', 'url', 'imagePath', 'sort'];
}
