<?php
/**
 * @author Dracowyn
 * @since 2024-01-06 11:56
 */

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\ShopController;
use App\Models\Admin\Admin as AdminModel;
use App\Models\Business\Business as BusinessModel;
use App\Models\Business\Receive as ReceiveModel;
use Illuminate\Http\JsonResponse;

class ReceiveController extends ShopController
{
    // 客户申领列表
    public function index(): JsonResponse
    {
        $admin = request()->get('admin');
        $receive = ReceiveModel::with(['business'])->where('applyid', $admin->id)->orderBy('id', 'desc')->get();

        if (count($receive) > 0) {
            return $this->success('获取成功', $receive);
        } else {
            return $this->error('暂无数据', null);
        }
    }

}
