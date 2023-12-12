<?php

namespace App\Http\Controllers\Shop\Business;

use App\Http\Controllers\ShopController;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BaseController extends ShopController
{
    // 注册
    public function register(Request $request)
    {
        $params = $request->only(['mobile', 'password']);
        $password = trim($params['password']);

        if (!$password) {
            return $this->error('密码不能为空', null);
        }

        $salt = build_randStr(6);

        $password = md5(md5($password) . $salt);

        $data = [
            'mobile' => $params['mobile'],
            'password' => $password,
            'salt' => $salt,
        ];

        $validate = [
            [
                'mobile' => ['required', 'unique:business', 'regex:/^1[356789]\d{9}$/'],
                'password' => ['required'],
                'salt' => ['required']
            ],
            [
                'mobile.required' => '手机号不能为空',
                'mobile.unique' => '手机号已存在',
                'mobile.regex' => '手机号格式不正确',
                'password.required' => '密码不能为空',
                'salt.required' => '盐值不能为空'
            ]
        ];

        $validator = Validator::make($data, ...$validate);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), null);
        }

        $result = Business::create($data);

        if ($result) {
            return $this->success('注册成功', null);
        } else {
            return $this->error('注册失败', null);
        }
    }

    // 登录
    public function login(Request $request)
    {
        $params = $request->only(['mobile', 'password']);
        $password = trim($params['password']);
        $mobile = trim($params['mobile']);

        if (!$mobile) {
            return $this->error('手机号不能为空', null);
        }
        if (!$password) {
            return $this->error('密码不能为空', null);
        }

        $business = Business::where('mobile', $mobile)->first();

        if (!$business) {
            return $this->error('账号不存在', null);
        }

        // 判断密码是否正确
        $BusinessPassword = $business->password;
        $BusinessSalt = $business->salt;

        $password = md5(md5($password) . $BusinessSalt);
        if ($password != $BusinessPassword) {
            return $this->error('密码错误', null);
        } else {
            return $this->success('登录成功', null);
        }
    }
}
