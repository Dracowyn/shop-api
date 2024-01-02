<?php
/**
 * 商品评论模型
 * @author Dracowyn
 * @since 2024-01-02 10:23
 */

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'product_comment';

    public $timestamps = true;

    protected $dateFormat = 'U';

    const CREATED_AT = 'createtime';

    const UPDATED_AT = 'updatetime';

    const DELETED_AT = 'deletetime';



}
