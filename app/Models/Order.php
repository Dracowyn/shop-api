<?php
/**
 * 订单模型
 * @author Dracowyn
 * @since 2023-12-21 15:44
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product\Order as OrderProductModel;

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

    protected $guarded = [];

    protected $appends = [
        'createtime_text',
        'status_text',
    ];


    public function getStatusList(): array
    {
        return [
            '0' => __('未支付'),
            '1' => __('已支付'),
            '2' => __('已发货'),
            '3' => __('已收货'),
            '4' => __('已完成'),
            '-1' => __('仅退款'),
            '-2' => __('退款退货'),
            '-3' => __('售后中'),
            '-4' => __('退货审核成功'),
            '-5' => __('退货审核失败')
        ];
    }

    public function getStatusTextAttribute()
    {
        $statusList = $this->getStatusList();

        return $statusList[$this->status];
    }

    public function orderProduct(): HasMany
    {
        return $this->hasMany(OrderProductModel::class, 'orderid', 'id');
    }

    public function getCreatetimeTextAttribute()
    {
        return date('Y-m-d H:i:s', strtotime($this->createtime));
    }

}
