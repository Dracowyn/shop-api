<?php
/**
 * 商品订单控制器
 * @author Dracowyn
 * @since 2024-01-06 17:09
 */

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\ShopController;
use App\Models\Order as OrderModel;
use Illuminate\Http\JsonResponse;

class ProductController extends ShopController
{
    // 商品订单列表
    public function index(): JsonResponse
    {
        $product = OrderModel::with(['orderProduct.product','business'])->orderBy('id', 'desc')->get();

        if (count($product) > 0) {
            return $this->success('获取成功', $product);
        } else {
            return $this->error('暂无数据', null);
        }
    }

}
