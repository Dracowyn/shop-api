<?php
/**
 * 订单控制器
 * @author Dracowyn
 * @since 2023-12-21 15:43
 */

namespace App\Http\Controllers\Shop\Product;

use App\Http\Controllers\ShopController;
use App\Models\Business\Address as AddressModel;
use App\Models\Product\Order as ProductOrderModel;
use App\Models\Order as OrderModel;
use App\Models\Product\Cart as CartModel;
use App\Models\Product\Product as ProductModel;
use App\Models\Business\Business as BusinessModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class OrderController extends ShopController
{
    /**
     * 创建订单
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function create(): JsonResponse
    {
        $params = request()->input();
        $business = request()->get('business');

        $address = AddressModel::find($params['addressid']);

        if (!$address) {
            return $this->error('收货地址不存在', null);
        }

        // 订单总额
        $total = 0;

        if (isset($params['proid'])) {
            $params = request()->input();
            $business = request()->get('business');

            $proId = $params['proid'] ?? 0;

            $productData = ProductModel::find($proId);

            if (!$productData) {
                return $this->error('商品不存在', null);
            }

            // 判断商品库存
            $stock = bcsub($productData->stock, $params['nums']);
            if ($stock <= 0) {
                return $this->error('商品库存不足', null);
            }

            $address = AddressModel::find($params['addressid']);

            if (!$address) {
                return $this->error('收货地址不存在', null);
            }

            // 开启事务
            DB::beginTransaction();

            $total = bcmul($params['nums'], $productData->price, 2);

            // 创建订单
            $orderData = [
                'code' => build_order('BU'),
                'busid' => $business->id,
                'businessaddrid' => $address->id,
                'amount' => $total,
                'remark' => $params['content'] ?? null,
                'status' => "0"
            ];

            $validate = [
                [
                    'busid' => 'required',
                    'businessaddrid' => 'required',
                    'code' => 'required|unique:order',
                    'status' => 'in:0,1,2,3,4',
                    'amount' => 'required'
                ],
                [
                    'busid.required' => '用户信息未知',
                    'businessaddrid.required' => '收货地址未知',
                    'code.required' => '订单号必填',
                    'code.unique' => '订单号已存在，请重新输入',
                    'amount.required' => '订单号金额未知',
                ]
            ];

            $validator = Validator::make($orderData, ...$validate);

            if ($validator->fails()) {
                return $this->error($validator->errors()->first(), null);
            }

            $orderStatus = OrderModel::create($orderData);

            if ($orderStatus === false) {
                DB::rollBack();
                return $this->error('订单创建失败', null);
            }

            $orderProductData = [
                'orderid' => $orderStatus->id,
                'proid' => $productData->id,
                'nums' => $params['nums'],
                'price' => $productData->price,
                'total' => bcmul($params['nums'], $productData->price, 2),
            ];

            $orderProductValidate = [
                [
                    'orderid' => 'required', //必填
                    'proid' => 'required', //必填
                    'nums' => 'required|gt:0', //必填
                    'price' => 'required|gt:0', //必填
                    'total' => 'required|gt:0', //必填
                ],
                [
                    'orderid.required' => '订单ID未知',
                    'proid.required' => '商品ID未知',
                    'nums.required' => '请填写商品数量',
                    'price.required' => '请填写商品的单价',
                    'total.required' => '请填写商品的总价',
                    'nums.gt' => '商品数量大于0',
                    'price.gt' => '商品的单价大于0',
                    'total.gt' => '商品的总价大于0',
                ]
            ];


            $orderProductValidator = Validator::make($orderProductData, ...$orderProductValidate);
            if ($orderProductValidator->fails()) {
                return $this->error($orderProductValidator->errors()->first(), null);
            }

            $orderProductStatus = ProductOrderModel::insert($orderProductData);

            // 更新商品库存
            $productData = [
                'id' => $productData->id,
                'stock' => $stock,
            ];

            $productStatus = ProductModel::upsert($productData, ['id'], ['stock']);


            if ($orderProductStatus === false || $productStatus === false) {
                DB::rollBack();
                return $this->error('订单创建失败', null);
            } else {
                DB::commit();
                return $this->success('订单创建成功', null);
            }
        } else {
            $cartIds = isset($params['cartids']) ? explode(',', $params['cartids']) : [];
            if ($cartIds == [] && !$cartIds && !is_array($cartIds)) {
                return $this->error('购物车数据不存在', null);
            }
            $cartData = CartModel::with('product')->whereIn('id', $cartIds)->get();

            if (!$cartData->toArray()) {
                return $this->error('购物车数据不存在', null);
            }

            foreach ($cartData as $item) {
                $stock = bcsub($item->product->stock, $item->nums);

                if ($stock < 0) {
                    return $this->error('商品库存不足', null);
                }

                $total += $item->total;
            }

            // 开启事务
            DB::beginTransaction();

            // 创建订单
            $orderData = [
                'code' => build_order('BU'),
                'busid' => $business->id,
                'businessaddrid' => $address->id,
                'amount' => $total,
                'remark' => $params['content'] ?? null,
                'status' => "0"
            ];

            $validate = [
                [
                    'busid' => 'required',
                    'businessaddrid' => 'required',
                    'code' => 'required|unique:order',
                    'status' => 'in:0,1,2,3,4',
                    'amount' => 'required'
                ],
                [
                    'busid.required' => '用户信息未知',
                    'businessaddrid.required' => '收货地址未知',
                    'code.required' => '订单号必填',
                    'code.unique' => '订单号已存在，请重新输入',
                    'amount.required' => '订单号金额未知',
                ]
            ];

            $validator = Validator::make($orderData, ...$validate);

            if ($validator->fails()) {
                return $this->error($validator->errors()->first(), null);
            }

            $orderStatus = OrderModel::create($orderData);

            if ($orderStatus === false) {
                DB::rollBack();
                return $this->error('订单创建失败', null);
            }

            $orderProductData = [];

            foreach ($cartData as $item) {
                $orderProductData[] = [
                    'orderid' => $orderStatus->id,
                    'proid' => $item->product->id,
                    'nums' => $item->nums,
                    'price' => $item->product->price,
                    'total' => $item->total,
                ];
            }

            $orderProductValidate = [
                [
                    'orderid' => 'required', //必填
                    'proid' => 'required', //必填
                    'nums' => 'required|gt:0', //必填
                    'price' => 'required|gt:0', //必填
                    'total' => 'required|gt:0', //必填
                ],
                [
                    'orderid.required' => '订单ID未知',
                    'proid.required' => '商品ID未知',
                    'nums.required' => '请填写商品数量',
                    'price.required' => '请填写商品的单价',
                    'total.required' => '请填写商品的总价',
                    'nums.gt' => '商品数量大于0',
                    'price.gt' => '商品的单价大于0',
                    'total.gt' => '商品的总价大于0',
                ]
            ];

            foreach ($orderProductData as $item) {
                $orderProductValidator = Validator::make($item, ...$orderProductValidate);
                if ($orderProductValidator->fails()) {
                    return $this->error($orderProductValidator->errors()->first(), null);
                }
            }

            $orderProductStatus = ProductOrderModel::insert($orderProductData);

            // 更新商品库存
            $productData = [];
            foreach ($cartData as $item) {
                $productData[] = [
                    'id' => $item->product->id,
                    'stock' => bcsub($item->product->stock, $item->nums),
                ];
            }

            $productStatus = ProductModel::upsert($productData, ['id'], ['stock']);

            // 删除购物车数据
            $cartStatus = CartModel::whereIn('id', $cartIds)->delete();

            if ($orderProductStatus === false || $productStatus === false || $cartStatus === false) {
                DB::rollBack();
                return $this->error('订单创建失败', null);
            } else {
                DB::commit();
                return $this->success('订单创建成功', null);
            }
        }
    }

    // 订单列表
    public function index(): JsonResponse
    {
        $busId = request('busid', 0);
        $page = request('page', 1);
        $limit = request('limit', 10);
        $status = request('status', '');

        $start = ($page - 1) * $limit;

        $where = [
            'busid' => $busId,
        ];

        if ($status !== '' && $status !== -1) {
            $where['status'] = $status;
        } elseif ($status === -1) {
            $where[] = ['status', '<', '0'];
        }

        $orderData = OrderModel::with('orderProduct.product')->where($where)->offset($start)->limit($limit)->get();

        $orderCount = OrderModel::with('orderProduct')->where($where)->count();

        $data = [
            'count' => $orderCount,
            'list' => $orderData,
        ];

        if (count($orderData) > 0) {
            return $this->success('获取成功', $data);
        } else {
            return $this->error('暂无订单', []);
        }
    }

    // 订单详情
    public function info(): JsonResponse
    {
        $orderId = request('orderid', 0);
        $busId = request('busid', 0);

        $where = [
            'code' => $orderId,
            'busid' => $busId,
        ];

        $orderData = OrderModel::with('orderProduct.product')->where($where)->first();

        if (!$orderData) {
            return $this->error('订单不存在', null);
        }

        $addressData = AddressModel::find($orderData->businessaddrid);
        if ($addressData) {
            $orderData->address = $addressData;
        }

        return $this->success('获取成功', $orderData);
    }
}

