<?php
/**
 * 客户公海控制器
 * @author Dracowyn
 * @since 2024-01-04 16:50
 */

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\ShopController;
use App\Models\Business\Source as SourceModel;
use App\Models\Business\Business as BusinessModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class HighseaController extends ShopController
{
    // 公海客户列表
    public function index(): JsonResponse
    {
        $business = BusinessModel::with(['source'])->where('adminid', null)->orderBy('id', 'desc')->get();

        if (count($business) > 0) {
            return $this->success('获取成功', $business);
        } else {
            return $this->error('暂无数据', null);
        }
    }

    // 客户详情
    public function info(): JsonResponse
    {
        $id = request('id', 0);

        $business = BusinessModel::with(['source'])->find($id);

        if ($business) {
            return $this->success('获取成功', $business);
        } else {
            return $this->error('暂无数据', null);
        }
    }

    // 删除客户
    public function del(): JsonResponse
    {
        $id = request('id', 0);

        $business = BusinessModel::find($id);

        if (!$business) {
            return $this->error('该客户不存在', null);
        }

        $business->delete();

        return $this->success('删除成功', null);
    }

    // 分配客户
    public function allot(): JsonResponse
    {
        $id = request('id', 0);
        $adminid = request('adminid', 0);

        $business = BusinessModel::find($id);

        if (!$business) {
            return $this->error('该客户不存在', null);
        }

        if ($business->adminid) {
            return $this->error('该客户已被分配', null);
        }

        $business->adminid = $adminid;
        $business->save();

        return $this->success('分配成功', null);
    }
}
