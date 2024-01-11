<?php
/**
 * 收藏控制器
 * @author Dracowyn
 * @since 2023-12-19 16:57
 */

namespace App\Http\Controllers\Shop\Business;

use App\Http\Controllers\ApiController;
use App\Models\Business\Business as BusinessModel;
use App\Models\Product\Product as ProductModel;
use App\Models\Business\Collection as CollectionModel;
use Illuminate\Http\JsonResponse;

class CollectionController extends ApiController
{
    // 收藏列表
    public function index(): JsonResponse
    {
        $busId = request('busid', 0);
        $page = request('page', 1);
        $limit = request('limit', 10);

        $where = [
            'busid' => $busId,
        ];

        $start = ($page - 1) * $limit;

//        $collectionData = CollectionModel::where($where)->offset($start)->limit($limit)->get();
        // 通过proid关联查询ProductModel
        $collectionData = CollectionModel::where($where)->with('product')->offset($start)->limit($limit)->get();

        $collectionCount = CollectionModel::where($where)->count();

        $data = [
            'count' => $collectionCount,
            'list' => $collectionData,
        ];

        return $this->success('获取成功', $data);
    }
}
