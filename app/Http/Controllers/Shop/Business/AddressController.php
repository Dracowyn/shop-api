<?php
/**
 * @author Dracowyn
 * @since 2023-12-15 11:36
 */

namespace App\Http\Controllers\Shop\Business;

use App\Http\Controllers\ShopController;
use App\Models\Business\Address as AddressModel;
use App\Models\Business\Business as BusinessModel;
use App\Models\Region as RegionModel;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AddressController extends ShopController
{
    // 收货地址列表
    public function index(): JsonResponse
    {
        $busId = request('busid', 0);
        $list = AddressModel::where(['busid' => $busId])->orderBy('id', 'desc')->get();

        if ($list) {
            return $this->success('获取收货地址成功', $list);
        } else {
            return $this->error('获取收货地址失败', null);
        }
    }


    // 添加收货地址
    public function add(): JsonResponse
    {
        $params = request()->input();

        $data = [
            'busid' => $params['busid'],
            'consignee' => $params['consignee'],
            'mobile' => $params['mobile'],
            'address' => $params['address'],
            'status' => (string)$params['status'],
        ];

        if ($params['status'] == 1) {
            $result = AddressModel::where(['busid' => $params['busid']])->update(['status' => '0']);
            if ($result === false) {
                return $this->error('更新默认地址失败', null);
            }
        }

        $path = RegionModel::where('code', $params['code'])->value('parentpath');

        if (!$path) {
            return $this->error('地址编码错误', null);
        }

        [$province, $city, $district] = explode(',', $path);

        $data['province'] = $province;
        $data['city'] = $city;
        $data['district'] = $district;

        $validate = [
            [
                'consignee' => 'required', //必填
                'mobile' => 'required', //必填
                'address' => 'required', //必填
                'status' => 'in:0,1',  //给字段设置范围
                'busid' => 'required', //必填
            ],
            [
                'consignee.required' => '请输入收货人名称',
                'mobile.required' => '请输入手机号码',
                'address.required' => '请输入详细地址',
                'busid.required' => '用户信息未知',
                'status.in' => '默认收货地址未知'
            ]
        ];

        $validator = Validator::make($data, ...$validate);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), null);
        }

        $result = AddressModel::create($data);

        if ($result) {
            return $this->success('添加收货地址成功', null);
        } else {
            return $this->error('添加收货地址失败', null);
        }
    }

    // 选择收货地址
    public function selected(): JsonResponse
    {
        $busId = request()->input('busid', 0);
        $id = request()->input('id', 0);

        try {
            $address = AddressModel::where(['busid' => $busId, 'id' => $id])->first();
            if (!$address) {
                throw new Exception('收货地址不存在');
            }

            $result = AddressModel::where(['busid' => $busId])->update(['status' => '0']);

            if ($result === false) {
                throw new Exception('更新默认地址失败');
            }

            $address->status = 1;
            $result = $address->save();
            if ($result === false) {
                throw new Exception('更新默认收货地址失败');
            }
        } catch (Exception $e) {
            return $this->error($e->getMessage(), null);
        }
        return $this->success('更新默认收货地址成功', null);
    }

    // 删除收货地址
    public function del(): JsonResponse
    {
        $busId = request()->input('busid', 0);
        $id = request()->input('id', 0);

        try {
            $address = AddressModel::where(['busid' => $busId, 'id' => $id])->first();
            if (!$address) {
                throw new Exception('收货地址不存在');
            }

            $result = AddressModel::destroy($id);

            if (!$result) {
                throw new Exception('删除收货地址失败');
            }
        } catch (Exception $e) {
            return $this->error($e->getMessage(), null);
        }
        return $this->success('删除收货地址成功', null);
    }

    // 编辑收货地址
    public function edit(): JsonResponse
    {
        $params = request()->input();

        $data = [
            'id' => $params['id'],
            'busid' => $params['busid'],
            'consignee' => $params['consignee'],
            'mobile' => $params['mobile'],
            'address' => $params['address'],
            'status' => (string)$params['status'],
        ];

        if ($params['status'] === '1') {
            $result = AddressModel::where(['busid' => $params['busid']])->update(['status' => '0']);
            if ($result === false) {
                return $this->error('更新默认地址失败', null);
            }
        }

        $path = RegionModel::where('code', $params['code'])->value('parentpath');

        if (!$path) {
            return $this->error('地址编码错误', null);
        }

        [$province, $city, $district] = explode(',', $path);

        $data['province'] = $province;
        $data['city'] = $city;
        $data['district'] = $district;

        $validate = [
            [
                'id' => 'required', //必填
                'consignee' => 'required', //必填
                'mobile' => 'required', //必填
                'address' => 'required', //必填
                'status' => 'in:0,1',  //给字段设置范围
                'busid' => 'required', //必填
            ],
            [
                'id.required' => '收货地址ID未知',
                'consignee.required' => '请输入收货人名称',
                'mobile.required' => '请输入手机号码',
                'address.required' => '请输入详细地址',
                'busid.required' => '用户信息未知',
                'status.in' => '默认收货地址未知'
            ]
        ];

        $validator = Validator::make($data, ...$validate);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), null);
        }

        $result = AddressModel::where(['id' => $params['id']])->update($data);

        if ($result) {
            return $this->success('编辑收货地址成功', null);
        } else {
            return $this->error('编辑收货地址失败', null);
        }
    }

    public function info(): JsonResponse
    {
        $busId = request('busid', 0);
        $id = request('id', 0);

        $address = AddressModel::where(['id' => $id, 'busid' => $busId])->first();

        if ($address) {
            return $this->success('查询成功', $address);
        } else {
            return $this->error('该收货地址不存在', null);
        }
    }

    // 获取用户默认收货地址
    public function default(): JsonResponse
    {
        $busId = request('busid', 0);

        $data = [
            'busid' => $busId,
            'status' => '1',
        ];
        $address = AddressModel::where($data)->first();

        if ($address) {
            return $this->success('获取成功', $address);
        } else {
            return $this->error('找不到默认的收货地址', null);
        }
    }
}
