<?php
/**
 * 课程控制器
 * @author Dracowyn
 * @since 2024-01-06 16:48
 */

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\ApiController;
use App\Models\Subject\Order as OrderModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class SubjectController extends ApiController
{
    // 课程订单列表
    public function index(): JsonResponse
    {
        $subject = OrderModel::with(['business','subject'])->orderBy('id', 'desc')->get();

        if (count($subject) > 0) {
            return $this->success('获取成功', $subject);
        } else {
            return $this->error('暂无数据', null);
        }
    }

}
