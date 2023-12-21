<?php
/**
 * 购物车控制器
 * @author Dracowyn
 * @since 2023-12-20 14:43
 */

namespace App\Http\Controllers\Shop\Product;

use App\Http\Controllers\ShopController;
use App\Models\Business\Business as BusinessModel;
use App\Models\Product\Product as ProductModel;
use App\Models\Product\Cart as CartModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CartController extends ShopController
{
    // 购物车列表
    public function index(): JsonResponse
    {
        $busId = request('busid', 0);
        $page = request('page', 1);
        $limit = request('limit', 10);

        $where = [
            'busid' => $busId,
        ];

        $start = ($page - 1) * $limit;

        $cartData = CartModel::where($where)->with('product')->offset($start)->limit($limit)->get();

        $cartCount = CartModel::where($where)->count();

        $data = [
            'count' => $cartCount,
            'list' => $cartData,
        ];

        return $this->success('获取成功', $data);

    }

    // 加入购物车
    public function add(): JsonResponse
    {
        $busId = request('busid', 0);
        $proId = request('proid', 0);

        $product = ProductModel::find($proId);

        if (!$product) {
            return $this->error('商品不存在', null);
        }

        if ($product->stock <= 0) {
            return $this->error('商品库存不足', null);
        }

        $cart = CartModel::where(['busid' => $busId, 'proid' => $proId])->first();

        if ($cart) {
            // 购物车已存在该商品，数量+1
            $productNumber = $cart->nums + 1;

            $cart->nums = $productNumber;
            $cart->total = $productNumber * $product->price;
            $result = $cart->save();
        } else {
            // 购物车不存在该商品，新增
            $data = [
                'busid' => $busId,
                'proid' => $proId,
                'nums' => 1,
                'price' => $product->price,
                'total' => bcmul(1, $product->price, 2),
            ];

            $validate = [
                [
                    'busid' => 'required',
                    'proid' => 'required',
                    'nums' => 'required',
                    'price' => 'required',
                    'total' => 'required',
                ],
                [
                    'busid.required' => '用户未知',
                    'proid.required' => '商品未知',
                    'nums.required' => '请选择商品数量',
                    'price.required' => '请输入商品的单价',
                    'total.required' => '请输入商品总价',
                ]
            ];

            $validator = Validator::make($data, ...$validate);

            if ($validator->fails()) {
                return $this->error($validator->errors()->first(), null);
            }

            $result = CartModel::create($data);
        }

        if ($result === false) {
            return $this->error('加入购物车失败', null);
        } else {
            return $this->success('加入购物车成功', null);
        }
    }

    // 更新购物车商品数量
    public function update(): JsonResponse
    {
        $busId = request('busid', 0);
        $proId = request('proid', 0);
        $nums = request('nums', 0);

        $product = ProductModel::find($proId);

        if (!$product) {
            return $this->error('商品不存在', null);
        }

        if ($product->stock <= 0) {
            return $this->error('商品库存不足', null);
        }

        $cart = CartModel::where(['busid' => $busId, 'proid' => $proId])->first();

        if (!$cart) {
            return $this->error('购物车不存在该商品', null);
        }

        if ($nums <= 0) {
            return $this->error('商品数量不能小于1', null);
        }

        $cart->nums = $nums;
        $cart->total = bcmul($nums, $product->price, 2);
        $result = $cart->save();

        if ($result === false) {
            return $this->error('更新失败', null);
        } else {
            return $this->success('更新成功', null);
        }
    }

    // 删除购物车商品
    public function del(): JsonResponse
    {
        $busId = request('busid', 0);
        $proId = request('proid', 0);

        $cart = CartModel::where(['busid' => $busId, 'proid' => $proId])->first();

        if (!$cart) {
            return $this->error('购物车不存在该商品', null);
        }

        $result = $cart->delete();

        if ($result === false) {
            return $this->error('删除失败', null);
        } else {
            return $this->success('删除成功', null);
        }
    }

    // 获取购物车信息
    public function info(): JsonResponse
    {
        $cartIdStr = request('cartids', 0);
        $cardIds = explode(',', $cartIdStr);
        $cardIds = array_filter($cardIds);

        $cartData = CartModel::with('product')->whereIn('id', $cardIds)->get();

        if ($cartData->toArray()) {
            return $this->success('获取成功', $cartData);
        } else {
            return $this->error('购物车不存在', null);
        }
    }
}
