<?php
/**
 * 商品订单模型
 * @author Dracowyn
 * @since 2023-12-21 16:02
 */

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product\Product as ProductModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $table = 'product_order';

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class, 'proid', 'id');
    }
}
