<?php
/**
 * 商品分类控制器
 * @author Dracowyn
 * @since 2023-12-18 17:02
 */

namespace App\Http\Controllers\Shop\Product;

use App\Http\Controllers\ApiController;
use App\Models\Product\Type as TypeModel;
use App\Models\Product\Product as ProductModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TypeController extends ApiController
{
    public function index(): JsonResponse
    {
        $typeData = TypeModel::orderBy('weigh', 'desc')->get();
        return $this->success('获取成功', $typeData);
    }

    public function product(): JsonResponse
    {
        $typeId = request('typeid', 0);
        $page = request('page', 1);
        $limit = request('limit', 10);

        if (!$typeId) {
            return $this->error('参数错误', null);
        }

        // 封装条件数组
        $where = [
            'status' => '1',
            'typeid' => $typeId,
        ];

        $start = ($page - 1) * $limit;

        $productData = ProductModel::where($where)->offset($start)->limit($limit)->get();
        $productCount = ProductModel::where($where)->count();

        $data = [
            'count' => $productCount,
            'list' => $productData,
        ];

        if ($productData) {
            return $this->success('获取成功', $data);
        } else {
            return $this->error('暂无商品', []);
        }
    }
}
