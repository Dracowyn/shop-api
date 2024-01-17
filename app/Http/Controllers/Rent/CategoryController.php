<?php
/**
 * @author Dracowyn
 * @since 2024-01-15 11:39
 */

namespace App\Http\Controllers\Rent;

use App\Http\Controllers\ApiController;
use App\Models\Category as CategoryModel;
use App\Models\Business\Collection as CollectionModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

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

    /**
     * 学术详情
     * @return JsonResponse
     */
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

    public function collection(): JsonResponse
    {
        $busId = request('busid', 0);

        $id = request('id', 0);

        $category = CategoryModel::find($id);

        if (!$category) {
            return $this->error('该文章不存在', null);
        }

        $collection = CollectionModel::where([
            ['busid', '=', $busId],
            ['cateid', '=', $id],
        ])->first();

        if ($collection) {
            $collection->delete();
            return $this->success('取消收藏成功', null);
        } else {
            $data = [
                'busid' => $busId,
                'cateid' => $id,
            ];

            $validator = Validator::make($data, [
                'busid' => 'required|integer',
                'cateid' => 'required|integer',
            ], [
                'busid.required' => '用户id不能为空',
                'busid.integer' => '用户id必须为整数',
                'cateid.required' => '文章id不能为空',
                'cateid.integer' => '文章id必须为整数',
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors()->first(), null);
            }

            $result = CollectionModel::create($data);

            if ($result) {
                return $this->success('收藏成功', null);
            } else {
                return $this->error('收藏失败', null);
            }
        }
    }
}
