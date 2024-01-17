<?php
/**
 * @author Dracowyn
 * @since 2024-01-15 11:39
 */

namespace App\Http\Controllers\Rent;

use App\Http\Controllers\ApiController;
use App\Models\Category as CategoryModel;
use Illuminate\Http\JsonResponse;

class CategoryController extends ApiController
{
    public function hot(): JsonResponse
    {
        $hotList = CategoryModel::where('flag', 'LIKE', '%hot%')->OrderBy('createtime', 'desc')->limit(6)->get();
        return $this->success('获取成功', $hotList);
    }

    public function index(): JsonResponse
    {
        $page = request()->input('page', 1);
        $pageSize = request()->input('limit', 10);

        $start = ($page - 1) * $pageSize;

        $count = CategoryModel::where('status', 'normal')->count();

        $list = CategoryModel::where('status', 'normal')->OrderBy('createtime', 'desc')->offset($start)->limit($pageSize)->get();

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

    public function info(): JsonResponse
    {
        $id = request('id');

        $info = CategoryModel::find($id);

        if (!$info) {
            return $this->error('暂无数据', null);
        }

        $prev = CategoryModel::where('id', '<', $id)->OrderBy('id', 'desc')->first();

        $next = CategoryModel::where('id', '>', $id)->OrderBy('id', 'asc')->first();

        $data = [
            'info' => $info,
            'prev' => $prev,
            'next' => $next
        ];

        return $this->success('获取成功', $data);
    }
}
