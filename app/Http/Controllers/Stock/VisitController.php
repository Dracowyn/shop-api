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
use Illuminate\Support\Facades\Validator;

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

    // 客户列表
    public function business(): JsonResponse
    {
        $admin = request()->get('admin');
        $business = BusinessModel::where('adminid', $admin->id)->orderBy('id', 'desc')->get();

        if (count($business) > 0) {
            return $this->success('获取成功', $business);
        } else {
            return $this->error('您当前暂无客户', null);
        }
    }

    // 添加回访记录
    public function add(): JsonResponse
    {
        $admin = request()->get('admin');
        $params = request()->input();

        $business = BusinessModel::find($params['busid']);

        if (!$business) {
            return $this->error('该客户不存在', null);
        }

        if ($business->adminid != $admin->id) {
            return $this->error('该客户不属于您', null);
        }

        $validator = Validator::make($params, [
            'busid' => 'required',
            'content' => 'required',
        ], [
            'busid.required' => '客户ID不能为空',
            'content.required' => '回访内容不能为空',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), null);
        }

        $data = [
            'adminid' => $admin->id,
            'busid' => $params['busid'],
            'content' => $params['content'],
        ];

        $result = VisitModel::create($data);

        if ($result) {
            return $this->success('添加成功', null);
        } else {
            return $this->error('添加失败', null);
        }
    }
}
