<?php
/**
 * @author Dracowyn
 * @since 2023-12-18 15:17
 */

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Config as ConfigModel;

class Type extends Model
{
    use HasFactory;

    protected $table = 'product_type';

    protected $appends = [
        'thumb_cdn'
    ];

    public function getThumbCdnAttribute()
    {
        $cdn = ConfigModel::where('name', 'url')->value('value');
        $url = $cdn . '/shop/product/type/thumb';
        return httpRequest($url, ['typeid' => $this->id]);
    }
}
