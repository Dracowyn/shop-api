<?php
/**
 * @author Dracowyn
 * @since 2024-01-03 15:07
 */

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\ShopController;
use App\Models\Product\Order as OrderModel;
use App\Models\Business\Business as BusinessModel;
use Illuminate\Http\JsonResponse;

class Controller extends ShopController
{
    public function total(): JsonResponse
    {
        $orderCount = OrderModel::count();
        $orderMoney = OrderModel::where(['status' => '4'])->sum('amount');

        $businessCount = BusinessModel::count();

        $data = [
            'orderCount' => $orderCount,
            'orderMoney' => $orderMoney,
            'businessCount' => $businessCount,
        ];

        return $this->success('获取成功', $data);
    }
}
