<?php

namespace App\Http\Controllers\Shop\Business;

use App\Http\Controllers\ShopController;
use App\Models\Business\Business as BusinessModel;
use App\Models\Business\Source as SourceModel;
use App\Models\Region as RegionModel;
use App\Models\Config as ConfigModel;
use CURLFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use function PHPUnit\Framework\exactly;

class BaseController extends ShopController
{
    // 注册
    public function register(Request $request): JsonResponse
    {
        $params = $request->only(['mobile', 'password']);
        $password = trim($params['password']);

        if (!$password) {
            return $this->error('密码不能为空', null);
        }

        $salt = build_randStr(6);

        $password = md5(md5($password) . $salt);

        $source = SourceModel::where([['name', 'LIKE', '%商城%']])->first();

        $data = [
            'mobile' => $params['mobile'],
            'password' => $password,
            'salt' => $salt,
        ];

        if ($source) {
            $data['source_id'] = $source->id;
        }

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

        $result = BusinessModel::create($data);

        if ($result) {
            return $this->success('注册成功', null);
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

    // 登录验证
    public function check(Request $request): JsonResponse
    {
        $id = $request->input('id', 0);
        $mobile = $request->input('mobile', '');

        $where = [
            'id' => $id,
            'mobile' => $mobile
        ];

        $business = BusinessModel::where($where)->first();

        if (!$business) {
            return $this->error('非法登录', null);
        }

        $data = $this->getUserData($business);

        return $this->success('验证成功', $data);
    }

    /**
     * 获取头像
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
            'auth' => $business->auth,
        ];
        $avatar = httpRequest('http://127.0.0.1:8173/shop/business/avatar', ['id' => $business['id']]);
        $avatarData = json_decode($avatar);
        if ($avatarData) {
            $data['avatar'] = $avatarData->data->avatar;
        }
        return $data;
    }

    /**
     * 修改资料
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function profile(): JsonResponse
    {
        $params = \request()->input();

        $business = \request()->get('business');

        $data = [
            'nickname' => trim($params['nickname']),
            'gender' => $params['gender']
        ];

        if ($params['email'] != $business['email']) {
            $data['email'] = $params['email'];
            $data['auth'] = '0';
        }

        $password = $params['password'] ?? '';

        if (!empty($password)) {
            $rePassword = md5(md5($password) . $business['salt']);
            if ($rePassword === $business['password']) {
                return $this->error('新密码不能与旧密码相同', null);
            }
            $salt = build_randStr(6);
            $password = md5(md5($password) . $salt);
            $data['password'] = $password;
            $data['salt'] = $salt;
        }

        $path = RegionModel::where('code', $params['code'])->value('parentpath');

        if (!$path) {
            return $this->error('所选地区不存在',null);
        }

        [$province, $city, $district] = explode(',', $path);

        $data['province'] = $province;
        $data['city'] = $city;
        $data['district'] = $district;

        if (isset($_FILES['avatar']) && $_FILES['avatar']['size'] > 0) {
            $cdn = ConfigModel::where('name', 'url')->value('value');
            $url = $cdn . '/shop/business/upload';
            $file = new CURLFile($_FILES['avatar']['tmp_name'], $_FILES['avatar']['type'], $_FILES['avatar']['name']);
            $result = httpRequest($url, ['avatar' => $file, 'id' => $business['id']]);
            $avatar = json_decode($result, true);
            if ($avatar['code'] == 0) {
                return $this->error($avatar['msg'], null);
            }
            $data['avatar'] = $avatar['data'];
        }

        $result = BusinessModel::where('id', $business['id'])->update($data);

        if ($result) {
            if (isset($_FILES['avatar']) && $_FILES['avatar']['size'] > 0) {
                $cdn = ConfigModel::where('name', 'url')->value('value');
                $url = $cdn . '/shop/business/del';
                httpRequest($url, ['avatar' => $business['avatar']]);
            }
            $business = BusinessModel::find($business['id']);
            $data = $this->getUserData($business);
            return $this->success('修改成功', $data);
        } else {
            if (isset($_FILES['avatar']) && $_FILES['avatar']['size'] > 0) {
                $cdn = ConfigModel::where('name', 'url')->value('value');
                $url = $cdn . '/shop/business/del';
                httpRequest($url, ['id' => $business['id'], 'avatar' => $data['avatar']]);
            }
            return $this->error('修改失败', null);
        }
    }
}
