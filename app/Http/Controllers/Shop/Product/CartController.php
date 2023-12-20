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
    public function index()
    {

    }

    // 加入购物车
    public function add()
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
}
