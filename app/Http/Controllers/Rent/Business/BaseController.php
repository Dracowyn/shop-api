<?php
/**
 * @author Dracowyn
 * @since 2024-01-09 15:41
 */

namespace App\Http\Controllers\Rent\Business;

use App\Http\Controllers\ShopController;
use App\Models\Business\Business as BusinessModel;
use App\Models\Business\Source as SourceModel;
use App\Models\Config as ConfigModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    // 登录
    public function login(Request $request): JsonResponse
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

        $business = BusinessModel::where('mobile', $mobile)->first();

        if (!$business) {
            return $this->error('账号不存在', null);
        }

        // 判断密码是否正确
        $BusinessPassword = $business->password;
        $BusinessSalt = $business->salt;

        $password = md5(md5($password) . $BusinessSalt);
        if ($password != $BusinessPassword) {
            return $this->error('密码错误', null);
        }

        $data = $this->getUserData($business);

        return $this->success('登录成功', $data);
    }

    /**
     * 获取用户信息
     * @param $business
     * @return array
     */
    public function getUserData($business): array
    {
        $data = [
            'id' => $business->id,
            'mobile' => $business->mobile,
            'nickname' => $business->nickname,
            'email' => $business->email,
            'gender' => $business->gender,
            'province' => $business->province,
            'city' => $business->city,
            'district' => $business->district,
            'sourceid' => $business->sourceid,
            'region_text' => $business->region_text,
            'money' => $business->money,
            'auth' => $business->auth,
        ];
        $url = ConfigModel::where(['name' => 'url'])->value('value');
        $avatar = httpRequest($url . '/shop/business/avatar',['id' => $business['id']]);
        $avatarData = json_decode($avatar);
        if ($avatarData) {
            $data['avatar'] = $avatarData->data->avatar;
        }
        return $data;
    }
}
