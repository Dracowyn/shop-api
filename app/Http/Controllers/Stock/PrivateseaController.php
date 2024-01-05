<?php
/**
 * 客户私海控制器
 * @author Dracowyn
 * @since 2024-01-05 14:40
 */

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\ShopController;
use App\Models\Admin\Admin as AdminModel;
use App\Models\Business\Business as BusinessModel;
use App\Models\Business\Receive as ReceiveModel;
use App\Models\Config as ConfigModel;
use CURLFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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

    // 新增客户
    public function add(): JsonResponse
    {
        $params = request()->input();
        $admin = request()->get('admin');

        $password = trim($params['password']);

        if ($password) {
            $salt = build_randStr(6);
            $password = md5(md5($password) . $salt);
        }

        $mobile = trim($params['mobile']);

        if (!$mobile) {
            return $this->error('手机号不能为空', null);
        }

        $money = trim($params['money']);
        // money字段必须是数字且大于等于0
        if (!is_numeric($money) || $money < 0) {
            return $this->error('金额必须是数字且大于等于0', null);
        }

        $data = [
            'mobile' => $mobile,
            'nickname' => $params['nickname'],
            'password' => $password,
            'salt' => $salt ?? null,
            'adminid' => $admin->id,
            'gender' => $params['gender'] ?? 0,
            'sourceid' => $params['sourceid'] ?? null,
            'auth' => $params['auth'],
            'money' => $money,
            'email' => $params['email'] ?? null,
            'deal' => $params['deal'] ?? null,
            'avatar' => $params['avatar'] ?? null,
        ];

        $validate = [
            [
                'mobile' => 'required|unique:business,mobile', //必填
            ],
            [
                'mobile.required' => '请输入手机号码',
                'mobile.unique' => '手机号码已存在',
            ]
        ];

        $validator = Validator::make($data, ...$validate);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), null);
        }

        $result = BusinessModel::create($data);

        if ($result) {
            return $this->success('新增客户成功', null);
        } else {
            return $this->error('新增客户失败', null);
        }

    }

    /**
     * 上传头像
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function avatar(): JsonResponse
    {
        $admin = request()->get('admin');
        $url = ConfigModel::where('name', 'url')->value('value');

        $url = $url . '/stock/business/upload';

        $file = new CURLFile($_FILES['avatar']['tmp_name'], $_FILES['avatar']['type'], $_FILES['avatar']['name']);

        $result = httpRequest($url, ['adminid' => $admin->id, 'avatar' => $file]);

        $result = json_decode($result, true);

        if ($result['code'] === 0) {
            return $this->success($result['msg'], $result['data']);
        } else {
            return $this->error($result['msg'], null);
        }
    }

}
