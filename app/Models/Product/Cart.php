<?php
/**
 * 购物车模型
 * @author Dracowyn
 * @since 2023-12-20 14:45
 */

namespace App\Models\Product;

use App\Models\Product\Product as ProductModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart';

    protected $guarded = [];

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'proid', 'id', ['status' => 1]);
    }
}
