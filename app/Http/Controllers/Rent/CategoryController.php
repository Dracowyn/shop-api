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
}
