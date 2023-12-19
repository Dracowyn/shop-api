<?php
/**
 * 用户收藏模型
 * @author Dracowyn
 * @since 2023-12-19 15:32
 */

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    protected $table = 'business_collection';

    // 时间类型
    protected $dateFormat = 'U';

    // 自定义创建时间字段
    const CREATED_AT = 'createtime';

    // 自定义更新时间字段
    const UPDATED_AT = null;
}
