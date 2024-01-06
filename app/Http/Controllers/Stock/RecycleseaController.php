<?php
/**
 * @author Dracowyn
 * @since 2024-01-06 15:39
 */

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\ShopController;
use App\Models\Admin\Admin as AdminModel;
use App\Models\Business\Business as BusinessModel;
use App\Models\Business\Receive as ReceiveModel;
use App\Models\Business\Visit as VisitModel;
use Illuminate\Http\JsonResponse;

class RecycleseaController extends ShopController
{
    // 客户回收站列表
    public function index(): JsonResponse
    {
        $business = BusinessModel::onlyTrashed()->with(['source'])->orderBy('id', 'desc')->get();

        if (count($business) > 0) {
            return $this->success('获取成功', $business);
        } else {
            return $this->error('暂无数据', null);
        }
    }

}
