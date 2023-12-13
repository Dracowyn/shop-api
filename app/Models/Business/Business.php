<?php
/**
 * 客户模型
 * @author Dracowyn
 * @since 2023-12-12 15:01
 */

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    // 指定的数据表
    protected $table = 'business';

    // 自动写入时间戳
    public $timestamps = true;

    // 时间类型
    protected $dateFormat = 'U';

    // 自定义创建时间字段
    const CREATED_AT = 'create_time';

    // 自定义更新时间字段
    const UPDATED_AT = 'update_time';

    protected $guarded = [];
}
