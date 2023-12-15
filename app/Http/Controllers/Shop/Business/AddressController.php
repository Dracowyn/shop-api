<?php
/**
 * @author Dracowyn
 * @since 2023-12-15 11:36
 */

namespace App\Http\Controllers\Shop\Business;

use App\Http\Controllers\ShopController;
use App\Models\Business\Address as AddressModel;
use App\Models\Region as RegionModel;

class AddressController extends ShopController
{
    public function add()
    {
        $params = request()->input();

        $data = [
            'busid' => $params['busid'],
            'consignee' => $params['consignee'],
            'mobile' => $params['mobile'],
            'address' => $params['address'],
            'status' => (string)$params['status'],
        ];

        if ($params['status'] === 1) {
            $result = AddressModel::where('busid', $params['busid'])->update(['status' => 0]);
            if (!$result) {
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
}
