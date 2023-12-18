<?php
/**
 * @author Dracowyn
 * @since 2023-12-18 15:15
 */

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\ShopController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Models\Product\Product as ProductModel;
use App\Models\Product\Type as TypeModel;

class HomeController extends ShopController
{
    public function index()
    {
        $recommendData = ProductModel::where(['flag' => "3"])->orderBy('create_time', 'desc')->limit(6)->get();
        $typeData = TypeModel::orderBy('weigh','desc')->limit(8)->get();

        $data = [
            'recommendData' => $recommendData,
            'typeData' => $typeData
        ];

        return $this->success('获取成功', $data);

    }
}
