<?php
/**
 * @author Dracowyn
 * @since 2024-01-06 15:39
 */

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\ApiController;
use App\Models\Admin\Admin as AdminModel;
use App\Models\Business\Business as BusinessModel;
use App\Models\Business\Receive as ReceiveModel;
use App\Models\Business\Visit as VisitModel;
use Illuminate\Http\JsonResponse;

class RecycleseaController extends ApiController
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

    // 客户回收站详情
    public function info(): JsonResponse
    {
        $id = request('id', 0);

        $business = BusinessModel::onlyTrashed()->with(['source'])->find($id);

        if ($business) {
            return $this->success('获取成功', $business);
        } else {
            return $this->error('该客户不存在', null);
        }
    }

    // 恢复客户
    public function recover(): JsonResponse
    {
        $id = request('id', 0);

        $business = BusinessModel::onlyTrashed()->find($id);

        if (!$business) {
            return $this->error('该客户不存在', null);
        }

        $business->restore();

        return $this->success('恢复成功', null);
    }

    // 删除客户
    public function del(): JsonResponse
    {
        $id = request('id', 0);

        $business = BusinessModel::onlyTrashed()->find($id);

        if (!$business) {
            return $this->error('该客户不存在', null);
        }

        $business->forceDelete();

        return $this->success('删除成功', null);
    }
}
