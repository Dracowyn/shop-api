<?php
/**
 * @author Dracowyn
 * @since 2023-12-29 14:09
 */

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\ShopController;
use App\Models\Admin\Admin as AdminModel;
use App\Models\Config as ConfigModel;
use CURLFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class AdminController extends ShopController
{
    /**
     * 微信登录
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        $code = request('code', '');

        if (empty($code)) {
            return $this->error('获取临时登录凭证失败', null);
        }

        $openid = $this->code2Session($code);

        if (!$openid) {
            return $this->error('获取openid失败', null);
        }

        $admin = AdminModel::where(['openid' => $openid])->first();

        if (!$admin) {
            return $this->error('请先绑定账号', ['openid' => $openid]);
        }

        if ($admin->status !== 'normal') {
            return $this->error('账号已被禁用', null);
        }

        $data = [
            'id' => $admin->id,
            'username' => $admin->username,
            'nickname' => $admin->nickname,
            'avatar_cdn' => $admin->avatar_cdn,
            'avatar' => $admin->avatar,
            'email' => $admin->email,
            'mobile' => $admin->mobile,
            'group_text' => $admin->group_text,
            'createtime' => strtotime($admin->createtime),
        ];

        return $this->success('登录成功', $data);
    }

    /**
     * 通过code获取openid
     * @param $code
     * @return false|mixed|string
     */
    protected function code2Session($code)
    {
        $appId = ConfigModel::where(['name' => 'wxm_AppID'])->value('value');
        $appSecret = ConfigModel::where(['name' => 'wxm_AppSecret'])->value('value');
        $apiUrl = "https://api.weixin.qq.com/sns/jscode2session?appid=$appId&secret=$appSecret&js_code=$code&grant_type=authorization_code";

        $result = httpRequest($apiUrl);

        $data = json_decode($result, true);

        $openid = $data['openid'] ?? '';

        if (empty($openid)) {
            return false;
        } else {
            return $openid;
        }
    }

    /**
     * 绑定账号
     * @return JsonResponse
     */
    protected function bind(): JsonResponse
    {
        $username = request('username', '');
        $password = request('password', '');
        $openid = request('openid', '');

        $admin = AdminModel::where(['username' => $username])->first();

        if (!$admin) {
            return $this->error('账号不存在', null);
        }

        if ($admin->password != md5(md5($password) . $admin->salt)) {
            return $this->error('密码错误', null);
        }

        if ($admin->status !== 'normal') {
            return $this->error('账号已被禁用', null);
        }

        $admin->openid = $openid;

        $result = $admin->save();

        if ($result === false) {
            return $this->error('绑定失败', null);
        } else {
            return $this->success('绑定成功', null);
        }
    }

    /**
     * 解绑账号
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function unbind(): JsonResponse
    {
        $admin = request()->get('admin');

        $admin->openid = null;

        $result = $admin->save();

        if ($result === false) {
            return $this->error('解绑失败', null);
        } else {
            return $this->success('解绑成功', null);
        }
    }

    /**
     * 普通登录
     * @return JsonResponse
     */
    public function login2(): JsonResponse
    {
        $username = request('username', '');
        $password = request('password', '');

        $admin = AdminModel::where(['username' => $username])->first();

        if (!$admin) {
            return $this->error('账号不存在', null);
        }

        if ($admin->password != md5(md5($password) . $admin->salt)) {
            return $this->error('密码错误', null);
        }

        if ($admin->status !== 'normal') {
            return $this->error('账号已被禁用', null);
        }

        $data = [
            'id' => $admin->id,
            'username' => $admin->username,
            'nickname' => $admin->nickname,
            'avatar_cdn' => $admin->avatar_cdn,
            'avatar' => $admin->avatar,
            'email' => $admin->email,
            'mobile' => $admin->mobile,
            'group_text' => $admin->group_text,
            'createtime' => strtotime($admin->createtime),
        ];

        return $this->success('登录成功', $data);
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

        $url = $url . '/stock/admin/upload';

        $file = new CURLFile($_FILES['avatar']['tmp_name'], $_FILES['avatar']['type'], $_FILES['avatar']['name']);

        $result = httpRequest($url, ['adminid' => $admin->id, 'avatar' => $file]);

        $result = json_decode($result, true);

        if ($result['code'] === 0) {
            $admin = AdminModel::find($admin->id);
            $data = [
                'id' => $admin->id,
                'username' => $admin->username,
                'nickname' => $admin->nickname,
                'avatar_cdn' => $admin->avatar_cdn,
                'avatar' => $admin->avatar,
                'email' => $admin->email,
                'mobile' => $admin->mobile,
                'group_text' => $admin->group_text,
                'createtime' => strtotime($admin->createtime),
            ];
            return $this->success($result['msg'], $data);
        } else {
            return $this->error($result['msg'], null);
        }
    }

    public function profile(): JsonResponse
    {
        $admin = request()->get('admin');

        $nickname = request('nickname', '');
        $email = request('email', '');
        $mobile = request('mobile', '');
        $password = request('password', '');

        $data = [
            'nickname' => $nickname,
            'email' => $email,
            'mobile' => $mobile,
        ];

        if ($password) {
            $repass = md5(md5($password) . $admin->salt);
            if ($admin->password === $repass) {
                return $this->error('新密码不能与旧密码相同', null);
            }
            $salt = build_randStr(6);
            $password = md5(md5($password) . $salt);

            $data['password'] = $password;
            $data['salt'] = $salt;
        }

        // 验证数据
        $validate = [
            [
                'nickname' => 'required',
                'email' => 'required',
                'mobile' => ['required', 'unique:admin', 'regex:/^1[356789]\d{9}$/'],
            ],
            [
                'nickname.required' => '昵称不能为空',
                'email.required' => '邮箱不能为空',
                'mobile.required' => '手机号不能为空',
                'mobile.unique' => '手机号已存在',
                'mobile.regex' => '手机号格式不正确',
            ],
        ];

        $validator = Validator::make($data, ...$validate);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), null);
        }

        $result = AdminModel::where(['id' => $admin->id])->update($data);

        if ($result === false) {
            return $this->error('修改失败', null);
        } else {
            $admin = AdminModel::find($admin->id);
            $data = [
                'id' => $admin->id,
                'username' => $admin->username,
                'nickname' => $admin->nickname,
                'avatar_cdn' => $admin->avatar_cdn,
                'avatar' => $admin->avatar,
                'email' => $admin->email,
                'mobile' => $admin->mobile,
                'group_text' => $admin->group_text,
                'createtime' => strtotime($admin->createtime),
            ];
            return $this->success('修改成功', $data);
        }
    }
}
