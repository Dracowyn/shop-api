<?php
/**
 * @author Dracowyn
 * @since 2024-01-09 15:41
 */

namespace App\Http\Controllers\Rent\Business;

use App\Http\Controllers\ShopController;
use App\Models\Business\Business as BusinessModel;
use App\Models\Business\Source as SourceModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class BaseController extends ShopController
{
    public function register(): JsonResponse
    {
        $mobile = request('mobile', '');
        $password = request('password', '');

        $salt = build_randStr(6);
        $password = md5(md5($password) . $salt);

        $data = [
            'mobile' => $mobile,
            'password' => $password,
            'salt' => $salt,
        ];

        $source = SourceModel::where('name', 'LIKE', '%租赁%')->first();

        if ($source) {
            $data['sourceid'] = $source->id;
        }

        $validator = Validator::make($data, [
            'mobile' => ['required', 'regex:/^1[3456789]\d{9}$/', 'unique:business'],
            'password' => 'required',
            'salt' => 'required',
        ], [
            'mobile.required' => '手机号不能为空',
            'mobile.regex' => '手机号格式不正确',
            'mobile.unique' => '手机号已存在',
            'password.required' => '密码不能为空',
            'salt.required' => '密码盐不能为空',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), null);
        }

        $business = BusinessModel::create($data);

        if ($business) {
            return $this->success('注册成功', $business);
        } else {
            return $this->error('注册失败', null);
        }
    }
}
