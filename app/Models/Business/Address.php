<?php
/**
 * 客户收货地址模型
 * @author Dracowyn
 * @since 2023-12-15 11:34
 */

namespace App\Models\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $table = 'business_address';
    public $timestamps = true;

    // 时间类型
    protected $dateFormat = 'U';

    // 自定义创建时间字段
    const CREATED_AT = null;

    // 自定义更新时间字段
    const UPDATED_AT = null;

    protected $guarded = [];
}
