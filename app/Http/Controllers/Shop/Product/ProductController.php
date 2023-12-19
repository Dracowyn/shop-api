<?php
/**
 * 商品控制器
 * @author Dracowyn
 * @since 2023-12-18 15:25
 */

namespace App\Http\Controllers\Shop\Product;

use App\Http\Controllers\ShopController;
use App\Models\Product\Product as ProductModel;
use Illuminate\Http\JsonResponse;

class ProductController extends ShopController
{
    // 商品详情
    public function index(): JsonResponse
    {
        $page = request('page', 1);
        $limit = request('limit', 10);
        $typeId = request('typeid', 10);
        $keyword = request('keyword', '');
        $flag = request('flag', 0);
        $orderBy = request('orderBy', 'create_time');

        // 封装条件数组
        $where = [
            'status' => '1',
        ];

        if ($typeId) {
            $where['typeid'] = $typeId;
        }

        if ($keyword) {
            $where[] = ['title', 'like', '%' . $keyword . '%'];
        }

        if ($flag) {
            $where['flag'] = $flag;
        }

        $start = ($page - 1) * $limit;

        $productData = ProductModel::where($where)->orderBy($orderBy, 'desc')->offset($start)->limit($limit)->get();
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
