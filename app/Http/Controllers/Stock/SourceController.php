<?php
/**
 * 客户来源控制器
 * @author Dracowyn
 * @since 2024-01-02 16:58
 */

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\ShopController;
use App\Models\Business\Source as SourceModel;
use Illuminate\Http\JsonResponse;

class SourceController extends ShopController
{
    public function index(): JsonResponse
    {
        $source = SourceModel::orderBy('id', 'desc')->get();

        if (count($source) > 0) {
            return $this->success('获取成功', $source);
        } else {
            return $this->error('暂无数据', null);
        }
    }
}
