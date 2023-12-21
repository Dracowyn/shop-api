<?php
/**
 * 商品订单模型
 * @author Dracowyn
 * @since 2023-12-21 16:02
 */

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'product_order';
}
