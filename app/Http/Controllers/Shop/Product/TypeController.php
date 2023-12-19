<?php
/**
 * 商品分类控制器
 * @author Dracowyn
 * @since 2023-12-18 17:02
 */

namespace App\Http\Controllers\Shop\Product;

use App\Http\Controllers\ShopController;
use App\Models\Product\Type as TypeModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TypeController extends ShopController
{
    public function index(): JsonResponse
    {
        $typeData = TypeModel::orderBy('weigh','desc')->get();
        return $this->success('获取成功', $typeData);
    }
}
