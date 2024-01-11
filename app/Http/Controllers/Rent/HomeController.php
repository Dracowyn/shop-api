<?php
/**
 * @author Dracowyn
 * @since 2024-01-11 17:02
 */

namespace App\Http\Controllers\Rent;

use App\Http\Controllers\ApiController;
use App\Models\Category as CategoryModel;
use App\Models\Product\Product as ProductModel;
use Illuminate\Http\JsonResponse;

class HomeController extends ApiController
{
    public function index(): JsonResponse
    {
        $newList = ProductModel::where(['flag', '=', '1'], ['rent_status', '<>', '1'])->limit(6)->OrderBy('create_time', 'desc')->get();
        $recommendList = ProductModel::where(['flag', '=', '3'], ['rent_status', '<>', '1'])->limit(6)->OrderBy('create_time', 'desc')->get();
        $categoryList = CategoryModel::where('flag', 'LIKE', 'index')->OrderBy('create_time', 'desc')->limit(6)->get();

        $data = [
            'newList' => $newList,
            'recommendList' => $recommendList,
            'categoryList' => $categoryList
        ];

        return $this->success('获取成功', $data);
    }
}
