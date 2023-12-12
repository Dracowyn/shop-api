<?php

namespace App\Http\Controllers\Shop\Business;

use App\Http\Controllers\ShopController;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BaseController extends ShopController
{
    public function register(Request $request)
    {
        $params = $request->only(['mobile', 'password']);
        $password = $params['password'];

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

        $validator = Validator::make($data, $validate[0], $validate[1]);

        $result = Business::create($data);

        if ($result) {
            return $this->success('注册成功', null);
        } else {
            return $this->error('注册失败', null);
        }
    }
}
