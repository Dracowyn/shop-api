<?php
/**
 * @author Dracowyn
 * @since 2023-12-15 18:56
 */


namespace App\Http\Controllers\Shop\Business;

use App\Http\Controllers\ShopController;
use Illuminate\Http\JsonResponse;
use App\Models\Business\Email as EmailModel;
use App\Models\Business\Business as BusinessModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailController extends ShopController
{
    // 发送验证码
    public function send(): JsonResponse
    {
        $params = request()->input();
        $email = $params['email'];
        // 生成验证码
        if (!$email) {
            $this->error('邮箱不能为空', null);
        }

        $code = build_randStr(6);
        // 获取IP地址
        $ip = request()->getClientIp();

        $where = [
            'event' => 'email',
            'email' => $email,
            'times' => 0,
        ];

        DB::beginTransaction();

        $result = EmailModel::where($where)->first();
        // 判断是否存在
        if ($result) {
            $currentTime = time();
            $resultTime = $result->createtime->timestamp;
            if (abs($currentTime - $resultTime) < 60) {
                return $this->error('发送验证码过于频繁，请稍后再试', null);
            } else {
                // 更新验证码
                $result = EmailModel::where($where)->update(['code' => $code, 'createtime' => time(), 'ip' => $ip]);
                if ($result === false) {
                    return $this->error('发送验证码失败', null);
                }
            }
        }

        // 插入数据库
        $data = [
            'event' => 'email',
            'email' => $email,
            'times' => 0,
            'code' => $code,
            'ip' => $ip,
        ];

        $validate = [
            [
                'event' => 'required',
                'email' => 'required',
                'code' => 'required',
                'ip' => 'required',
            ],
            [
                'event.required' => '事件必填',
                'email.required' => '邮箱必须填写',
                'code.required' => '验证码未知',
                'ip.required' => 'ip地址未知',
            ]
        ];

        $validator = Validator::make($data, ...$validate);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), null);
        }

        $result = EmailModel::create($data);

        // 发送邮件
        $messageContent = "您的验证码为：{$code}，请勿泄露给他人，如非本人操作，请忽略此邮件。";
        Mail::raw($messageContent, function ($message) use ($email) {
            $message
                ->to($email)
                ->subject('邮箱验证码');
        });

        if ($result === false) {
            DB::rollBack();
            return $this->error('发送验证码失败', null);
        } else {
            DB::commit();
            return $this->success('发送验证码成功', $code);
        }
    }

    // 验证验证码
    public function check(): JsonResponse
    {
        $params = request()->input();
        $business = request()->get('business');

        if (!isset($params['email'])) {
            return $this->error('邮箱不能为空', null);
        }
        if (!isset($params['code'])) {
            return $this->error('验证码不能为空', null);
        }

        $email = $params['email'];
        $code = $params['code'];

        $data = [
            'event' => 'email',
            'email' => $email,
            'code' => $code,
        ];


        $result = EmailModel::where($data)->first();
        if (!$result) {
            return $this->error('验证码错误', null);
        }

        DB::beginTransaction();

        // 更新用户认证字段
        $business->auth = 1;
        $authStatus = $business->save();
        $emailStatus = EmailModel::where($data)->delete();

        if ($authStatus === false && $emailStatus === false) {
            DB::rollBack();
            return $this->error('验证失败', null);
        } else {
            DB::commit();
            return $this->success('验证成功', null);
        }
    }
}
