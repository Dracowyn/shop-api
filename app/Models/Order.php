<?php
/**
 * 订单模型
 * @author Dracowyn
 * @since 2023-12-21 15:44
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'order';

    // 自动写入时间戳
    public $timestamps = true;

    // 时间类型
    protected $dateFormat = 'U';

    // 自定义创建时间字段
    const CREATED_AT = 'createtime';

    // 自定义更新时间字段
    const UPDATED_AT = null;

    // 自定义软删除字段
    const DELETED_AT = 'deletetime';

    // 订单状态
    const STATUS = [
        '0' => '待付款',
        '1' => '待发货',
        '2' => '待收货',
        '3' => '已完成',
        '4' => '已取消',
    ];

    protected $guarded = [];

}
