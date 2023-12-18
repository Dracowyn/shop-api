<?php
/**
 * @author Dracowyn
 * @since 2023-12-18 15:16
 */

namespace App\Models\Product;

use App\Models\Config as ConfigModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'product';
    protected $appends = [
        'thumb_cdn'
    ];

    // 时间类型
    protected $dateFormat = 'U';

    // 自定义创建时间字段
    const CREATED_AT = 'create_time';

    // 自定义更新时间字段
    const UPDATED_AT = 'update_time';

    // 自定义软删除时间字段
    const DELETED_AT = 'delete_time';

    public function getThumbCdnAttribute()
    {
        $cdn = ConfigModel::where('name', 'url')->value('value');
        $url = $cdn . '/shop/product/thumb';
        return httpRequest($url, ['proid' => $this->id]);
    }
}
