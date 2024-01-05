<?php
/**
 * 客户私海控制器
 * @author Dracowyn
 * @since 2024-01-05 14:40
 */

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\ShopController;
use App\Models\Business\Business as BusinessModel;
use App\Models\Business\Receive as ReceiveModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PrivateseaController extends ShopController
{
    // 客户列表
    public function index(): JsonResponse
    {
        $admin = request()->get('admin');
        $business = BusinessModel::with(['source'])->where('adminid', $admin->id)->orderBy('id', 'desc')->get();

        if (count($business) > 0) {
            return $this->success('获取成功', $business);
        } else {
            return $this->error('暂无数据', null);
        }
    }

    // 客户详情
    public function info(): JsonResponse
    {
        $admin = request()->get('admin');
        $id = request('id', 0);

        $business = BusinessModel::with(['source'])->where('adminid', $admin->id)->find($id);

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
        $admin = request()->get('admin');

        $business = BusinessModel::where('adminid', $admin->id)->find($id);

        if (!$business) {
            return $this->error('该客户不存在', null);
        }

        $business->delete();

        return $this->success('删除成功', null);
    }

    // 回收客户
    public function recovery(): JsonResponse
    {
        $id = request('id', 0);
        $admin = request()->get('admin');

        $business = BusinessModel::where('adminid', $admin->id)->find($id);

        if (!$business) {
            return $this->error('该客户不存在', null);
        }

        // 开启事务
        DB::beginTransaction();

        $receiveData = [
            'applyid' => $business->adminid,
            'status' => 'recovery',
            'busid' => $business->id,
        ];

        $receiveStatus = ReceiveModel::create($receiveData);

        if (!$receiveStatus) {
            DB::rollBack();
            return $this->error('回收失败', null);
        }

        $businessData = [
            'adminid' => null,
        ];

        $businessStatus = $business->update($businessData);

        if (!$businessStatus) {
            DB::rollBack();
            return $this->error('回收失败', null);
        }

        DB::commit();
        return $this->success('回收成功', null);
    }

}
