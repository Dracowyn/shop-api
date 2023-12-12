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
