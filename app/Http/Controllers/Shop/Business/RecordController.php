<?php
/**
 * 消费记录控制器
 * @author Dracowyn
 * @since 2023-12-22 18:28
 */


namespace App\Http\Controllers\Shop\Business;

use App\Http\Controllers\ShopController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Models\Business\Business as BusinessModel;
use App\Models\Business\Record as RecordModel;

class RecordController extends ShopController
{
    // 记录列表
    public function index(): JsonResponse
    {
        $busId = request('busid', 0);
        $page = request('page', 1);
        $limit = request('limit', 10);

        $where = [
            'busid' => $busId,
        ];

        $start = ($page - 1) * $limit;

        $recordData = RecordModel::where($where)->offset($start)->limit($limit)->get();

        $recordCount = RecordModel::where($where)->count();

        $data = [
            'count' => $recordCount,
            'list' => $recordData,
        ];

        return $this->success('获取成功', $data);
    }
}


