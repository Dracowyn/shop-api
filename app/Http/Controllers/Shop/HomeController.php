<?php
/**
 * 首页控制器
 * @author Dracowyn
 * @since 2023-12-18 15:15
 */

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Models\Product\Product as ProductModel;
use App\Models\Product\Type as TypeModel;

class HomeController extends ApiController
{
    public function index(): JsonResponse
    {
        $recommendData = ProductModel::where(['flag' => "3", 'status' => "1"])->orderBy('create_time', 'desc')->limit(6)->get();
        $typeData = TypeModel::orderBy('weigh', 'desc')->limit(8)->get();

        $data = [
            'recommendData' => $recommendData,
            'typeData' => $typeData
        ];

        return $this->success('获取成功', $data);

    }

    // 新品
    public function new(): JsonResponse
    {
        // 封装条件数组
        $where = [
            'status' => '1',
            'flag' => '1',
        ];

        $data = ProductModel::where($where)->orderBy('create_time', 'desc')->limit(6)->get();

        if ($data->count() > 0) {
            return $this->success('获取成功', $data);
        } else {
            return $this->error('暂无商品', []);
        }
    }

    // 热销
    public function hot(): JsonResponse
    {
        // 封装条件数组
        $where = [
            'status' => '1',
            'flag' => '2',
        ];

        $data = ProductModel::where($where)->orderBy('create_time', 'desc')->limit(6)->get();

        if ($data->count() > 0) {
            return $this->success('获取成功', $data);
        } else {
            return $this->error('暂无商品', []);
        }
    }
}
