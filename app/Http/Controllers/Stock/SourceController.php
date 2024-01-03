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
use Illuminate\Support\Facades\Validator;

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

    // 添加客户来源
    public function add(): JsonResponse
    {
        $name = request('name', '');

        // 定义验证器
        $validator = Validator::make(
            [
                'name' => ['required', 'unique:business_source'],
            ],
            [
                'name.required' => '客户来源不能为空',
                'name.unique' => '客户来源已存在',
            ]
        );

        // 验证失败
        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), null);
        }

        $result = SourceModel::create([
            'name' => $name,
        ]);

        if ($result === false) {
            return $this->error('添加失败', null);
        } else {
            return $this->success('添加成功', null);
        }
    }

    // 删除客户来源
    public function del(): JsonResponse
    {
        $id = request('id', '');

        if (empty($id)) {
            return $this->error('参数错误', null);
        }

        $result = SourceModel::where(['id' => $id])->delete();

        if ($result === false) {
            return $this->error('删除失败', null);
        } else {
            return $this->success('删除成功', null);
        }
    }

    // 获取客户来源信息
    public function info(): JsonResponse
    {
        $id = request('id', '');

        if (empty($id)) {
            return $this->error('参数错误', null);
        }

        $result = SourceModel::where(['id' => $id])->first();

        if ($result) {
            return $this->success('获取成功', $result);
        } else {
            return $this->error('获取失败', null);
        }
    }
}
