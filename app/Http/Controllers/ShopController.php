<?php
/**
 * @author Dracowyn
 * @since 2023-12-12 14:49
 */


namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ShopController extends Controller
{
    /**
     * 返回数据
     * @param $msg string 提示信息
     * @param $data array|string|null 返回数据
     * @param $code int 状态码
     * @return JsonResponse 返回json数据
     */
    public function result(string $msg, array|string|null $data, $code): JsonResponse
    {
        return response()->json([
            'msg' => $msg,
            'data' => $data,
            'code' => $code
        ]);
    }

    /**
     * 成功返回
     * @param $msg string 提示信息
     * @param $data array|string|null 返回数据
     * @param $code int 状态码
     * @return JsonResponse 返回json数据
     */
    public function success(string $msg, array|string|null $data, int $code = 1): JsonResponse
    {
        return response()->json([
            'msg' => $msg,
            'data' => $data,
            'code' => $code
        ]);
    }

    /**
     * 失败返回
     * @param $msg string 提示信息
     * @param $data array|string|null 返回数据
     * @param $code int 状态码
     * @return JsonResponse 返回json数据
     */
    public function error(string $msg, array|string|null $data, int $code = 0): JsonResponse
    {
        return response()->json([
            'msg' => $msg,
            'data' => $data,
            'code' => $code
        ]);
    }
}
