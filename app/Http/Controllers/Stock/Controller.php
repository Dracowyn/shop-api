<?php
/**
 * @author Dracowyn
 * @since 2024-01-03 15:07
 */

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\ApiController;
use App\Models\Order as OrderModel;
use App\Models\Business\Business as BusinessModel;
use Illuminate\Http\JsonResponse;

class Controller extends ApiController
{
    protected $timeList = [];

    public function __construct()
    {
        // 获取当前年份
        for ($i = 1; $i <= 12; $i++) {
            $start = strtotime(date('Y-m-01', strtotime('2023' . '-' . $i)));
            $end = strtotime(date('Y-m-t', strtotime('2023' . '-' . $i)));
            $this->timeList[] = [$start, $end];
        }
    }

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

    public function business(): JsonResponse
    {
        // 未认证用户
        $noCertification = [];
        // 已认证用户
        $certification = [];

        foreach ($this->timeList as $time) {
            $noCertification[] = BusinessModel::where(['auth' => '0'])->whereBetween('create_time', $time)->count();
            $certification[] = BusinessModel::where(['auth' => '1'])->whereBetween('create_time', $time)->count();
        }

        $data = [
            'noCertification' => $noCertification,
            'certification' => $certification,
        ];

        return $this->success('获取成功', $data);
    }
}
