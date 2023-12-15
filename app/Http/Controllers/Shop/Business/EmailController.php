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
use Illuminate\Support\Facades\Mail;

class EmailController extends ShopController
{
    // 发送验证码
    public function send(): JsonResponse
    {
        $params = request()->input();
        $busId = $params['busid'];
        $email = $params['email'];
        // 生成验证码
        $code = build_randStr(6);
        // 获取IP地址
        $ip = request()->getClientIp();
        // 插入数据库
        $data = [
            'event' => $busId,
            'email' => $email,
            'code' => $code,
            'ip' => $ip,
        ];
        $result = EmailModel::insert($data);

        // 发送邮件
        $messageContent = "您的验证码为：{$code}，请勿泄露给他人，如非本人操作，请忽略此邮件。";

        Mail::raw($messageContent, function ($message) use ($email) {
            $message->to($email)
                ->subject('邮箱验证码');
        });

        if ($result === false) {
            return $this->error('发送验证码失败', null);
        } else {
            return $this->success('发送验证码成功', $code);
        }
    }

    // 验证验证码
    public function check(): JsonResponse
    {
        $params = request()->input();
        $busId = $params['busid'];
        $email = $params['email'];
        $code = $params['code'];
        $data = [
            'event' => $busId,
            'email' => $email,
            'code' => $code,
        ];
        $result = EmailModel::where($data)->first();
        if ($result) {
            $result = BusinessModel::where(['id' => $busId])->update(['auth' => '1']);
            // 验证成功后删除验证码
            EmailModel::where($data)->delete();
            return $this->success('验证成功', null);
        } else {
            return $this->error('验证失败', null);
        }
    }
}
