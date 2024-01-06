<?php
/**
 * 客户回访记录控制器
 * @author Dracowyn
 * @since 2024-01-06 12:21
 */

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\ShopController;
use App\Models\Admin\Admin as AdminModel;
use App\Models\Business\Business as BusinessModel;
use App\Models\Business\Receive as ReceiveModel;
use App\Models\Business\Visit as VisitModel;
use Illuminate\Http\JsonResponse;

class VisitController extends ShopController
{
    // 回访记录列表
    public function index(): JsonResponse
    {
        $admin = request()->get('admin');
        $visit = VisitModel::with(['business'])->where('adminid', $admin->id)->orderBy('id', 'desc')->get();

        if (count($visit) > 0) {
            return $this->success('获取成功', $visit);
        } else {
            return $this->error('暂无数据', null);
        }
    }

}
