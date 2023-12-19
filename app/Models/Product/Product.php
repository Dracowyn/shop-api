<?php
/**
 * @author Dracowyn
 * @since 2023-12-18 15:16
 */

namespace App\Models\Product;

use App\Models\Config as ConfigModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'product';
    protected $appends = [
        'thumb_cdn',
        'thumbs_cdn',
        'content_text',
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

    public function getThumbsCdnAttribute()
    {
        $cdn = ConfigModel::where('name', 'url')->value('value');
        $url = $cdn . '/shop/product/thumbs';
        $thumbs = httpRequest($url, ['proid' => $this->id]);
        return json_decode($thumbs, true);
    }

    // 过滤content字段的html标签
    public function getContentTextAttribute()
    {
        return strip_tags($this->content);
    }
}
