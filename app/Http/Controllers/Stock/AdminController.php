<?php
/**
 * @author Dracowyn
 * @since 2023-12-29 14:09
 */

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\ShopController;
use App\Models\Admin\Admin as AdminModel;
use App\Models\Config as ConfigModel;
use Illuminate\Http\JsonResponse;

class AdminController extends ShopController
{
    public function login()
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

        // TODO: 登录成功，返回token
    }

    /**
     * 通过code获取openid
     * @param $code
     * @return false|mixed|string
     */
    protected function code2Session($code)
    {
        $appId = ConfigModel::where(['name' => 'AppId'])->value('value');
        $appSecret = ConfigModel::where(['name' => 'AppSecret'])->value('value');
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

}
