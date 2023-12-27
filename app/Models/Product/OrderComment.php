<?php
/**
 * 订单评论模型
 * @author Dracowyn
 * @since 2023-12-27 11:55
 */

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderComment extends Model
{
    use HasFactory;

    protected $table = 'order_comment';

    public $timestamps = true;

    protected $dateFormat = 'U';

    const CREATED_AT = 'createtime';

    const UPDATED_AT = null;

    protected $guarded = [];

    protected $appends = [
        'createtime_text',
    ];

    public function getCreatetimeTextAttribute()
    {
        return date('Y-m-d H:i:s', strtotime($this->createtime));
    }

}
