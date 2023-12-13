<?php
/**
 * @author Dracowyn
 * @since 2023-12-12 14:41
 */

if (!function_exists('build_randStr')) {
    /**
     * 生成随机字符串
     * @param int $length 长度
     * @param bool $special 是否包含特殊字符
     * @return string 生成的随机字符串
     */
    function build_randStr(int $length = 8, bool $special = false): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $special = $special ? '!@#$%^&*()_+-=,.<>/?;:[]{}|' : '';
        $chars .= $special;
        $randStr = '';
        // 打乱
        $chars = str_shuffle($chars);
        // 随机取
        for ($i = 0; $i < $length; $i++) {
            $randStr .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $randStr;
    }
}

// 生成唯一订单号
if (!function_exists('build_order')) {
    /**
     * 生成唯一订单号
     * @param String $prefix 前缀
     * @return string 生成的订单号
     */
    function build_order(string $prefix = ''): string
    {
        @date_default_timezone_set('PRC');
        $orderIdMain = date('YmdHis') . rand(10000000, 99999999);
        $orderIdLen = strlen($orderIdMain);
        $orderIdSum = 0;
        for ($i = 0; $i < $orderIdLen; $i++) {
            $orderIdSum += (int)(substr($orderIdMain, $i, 1));
        }
        // 唯一订单号码（YYYYMMDDHHIISSNNNNNNNNCC）
        return $prefix . $orderIdMain . str_pad((100 - $orderIdSum % 100) % 100, 2, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('httpRequest')) {
    function httpRequest($url, $data = null)
    {
        if (function_exists('curl_init')) {
            // 初始化
            $curl = curl_init();
            // 设置请求地址
            curl_setopt($curl, CURLOPT_URL, $url);
            // 设置http某些配置
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);

            // 判断传进来的参数是否不为空
            if (!empty($data)) {
                // 设置该请求为POST
                curl_setopt($curl, CURLOPT_POST, 1);
                // 把参数带入请求
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($curl);
            curl_close($curl);
            return $output;
        } else {
            return false;
        }
    }
}
