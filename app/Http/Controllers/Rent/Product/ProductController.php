<?php
/**
 * @author Dracowyn
 * @since 2024-01-17 14:36
 */

namespace App\Http\Controllers\Rent\Product;

use App\Http\Controllers\ApiController;
use App\Models\Product\Product as ProductModel;
use Illuminate\Http\JsonResponse;

class ProductController extends ApiController
{
    public function index(): JsonResponse
    {
        $page = request('page', 1);
        $limit = request('limit', 10);

        $start = ($page - 1) * $limit;

        $count = ProductModel::where('rent_status', '<>', '1')->where('status', '1')->count();

        $list = ProductModel::where('rent_status', '<>', '1')->where('status', '1')->offset($start)->limit($limit)->get();

        $data = [
            'count' => $count,
            'list' => $list
        ];

        if ($count > 0) {
            return $this->success('获取成功', $data);
        } else {
            return $this->error('暂无数据', null);
        }
    }
}
