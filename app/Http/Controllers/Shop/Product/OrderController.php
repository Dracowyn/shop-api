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
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

        return isset($params['proid'])
            ? $this->createSingleProductOrder($params, $business, $address)
            : $this->createCartOrder($params, $business, $address);
    }

    /**
     * 创建单个商品订单
     * @param $params
     * @param $business
     * @param $address
     * @return JsonResponse
     */
    private function createSingleProductOrder($params, $business, $address): JsonResponse
    {
        $productData = ProductModel::find($params['proid']);
        if (!$productData) {
            return $this->error('商品不存在', null);
        }

        $stock = bcsub($productData->stock, $params['nums']);
        if ($stock < 0) {
            return $this->error('商品库存不足', null);
        }

        DB::beginTransaction();
        try {
            $total = bcmul($params['nums'], $productData->price, 2);
            $orderData = $this->prepareOrderData($business, $address, $total, $params['content'] ?? null);
            $this->validateOrderData($orderData);

            $order = OrderModel::create($orderData);
            $this->createOrderProduct($order->id, $productData->id, $params['nums'], $productData->price);

            ProductModel::where('id', $productData->id)->update(['stock' => $stock]);

            DB::commit();
            return $this->success('订单创建成功', null);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error('订单创建失败: ' . $e->getMessage(), null);
        }
    }

    /**
     * 创建购物车订单
     * @param $params
     * @param $business
     * @param $address
     * @return JsonResponse
     */
    private function createCartOrder($params, $business, $address): JsonResponse
    {
        $cartIds = explode(',', $params['cartids'] ?? '');
        if (empty($cartIds)) {
            return $this->error('购物车数据不存在', null);
        }

        $cartData = CartModel::with('product')->whereIn('id', $cartIds)->get();
        if ($cartData->isEmpty()) {
            return $this->error('购物车数据不存在', null);
        }

        DB::beginTransaction();
        try {
            $total = $cartData->sum(fn($item) => $item->total);
            $orderData = $this->prepareOrderData($business, $address, $total, $params['content'] ?? null);
            $this->validateOrderData($orderData);

            $order = OrderModel::create($orderData);
            foreach ($cartData as $item) {
                $this->createOrderProduct($order->id, $item->product->id, $item->nums, $item->product->price);
                ProductModel::where('id', $item->product->id)->decrement('stock', $item->nums);
            }

            CartModel::whereIn('id', $cartIds)->delete();

            DB::commit();
            return $this->success('订单创建成功', null);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error('订单创建失败: ' . $e->getMessage(), null);
        }
    }

    /**
     * 封装订单数据
     * @param $business
     * @param $address
     * @param $total
     * @param $remark
     * @return array
     */
    private function prepareOrderData($business, $address, $total, $remark): array
    {
        return [
            'code' => build_order('BU'),
            'busid' => $business->id,
            'businessaddrid' => $address->id,
            'amount' => $total,
            'remark' => $remark,
            'status' => "0"
        ];
    }

    /**
     * 验证订单数据
     * @param $orderData
     * @return void
     * @throws Exception
     */
    private function validateOrderData($orderData)
    {
        $validator = Validator::make($orderData, [
            'busid' => 'required',
            'businessaddrid' => 'required',
            'code' => 'required|unique:order',
            'status' => 'in:0,1,2,3,4',
            'amount' => 'required'
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
    }

    /**
     * 创建订单商品
     * @param $orderId
     * @param $productId
     * @param $quantity
     * @param $price
     * @return void
     * @throws Exception
     */
    private function createOrderProduct($orderId, $productId, $quantity, $price)
    {
        $orderProductData = [
            'orderid' => $orderId,
            'proid' => $productId,
            'nums' => $quantity,
            'price' => $price,
            'total' => bcmul($quantity, $price, 2),
        ];

        $validator = Validator::make($orderProductData, [
            'orderid' => 'required',
            'proid' => 'required',
            'nums' => 'required|gt:0',
            'price' => 'required|gt:0',
            'total' => 'required|gt:0',
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        ProductOrderModel::insert($orderProductData);
    }

    /**
     * 订单列表
     * @return JsonResponse
     */
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

    // 支付订单
    public function pay(): JsonResponse
    {
        $orderId = request('orderid', 0);
        $busId = request('busid', 0);

        try {
            $orderData = OrderModel::with('orderProduct.product')->where('code', $orderId)->where('busid', $busId)->firstOrFail();
            $businessData = BusinessModel::findOrFail($busId);

            if ($businessData->money < $orderData->amount) {
                return $this->error('余额不足', null);
            }

            DB::beginTransaction();

            $orderData->status = '1';
            $orderData->save();

            $businessData->money = bcsub($businessData->money, $orderData->amount, 2);
            $businessData->save();

            DB::commit();
            return $this->success('支付成功', null);
        } catch (ModelNotFoundException $e) {
            return $this->error('订单或用户不存在', null);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error('支付失败: ' . $e->getMessage(), null);
        }
    }

    // 取消订单
    public function cancel(): JsonResponse
    {
        $orderId = request('orderid', 0);
        $busId = request('busid', 0);

        $where = [
            'code' => $orderId,
            'busid' => $busId,
        ];

        $orderData = OrderModel::where($where)->first();

        if (!$orderData) {
            return $this->error('订单不存在', null);
        }

        // 开启事务
        DB::beginTransaction();

        // 删除订单
        $orderStatus = OrderModel::where($where)->delete();

        // 更新商品库存
        $productData = [];

        foreach ($orderData->orderProduct as $item) {
            $productData[] = [
                'id' => $item->product->id,
                'stock' => bcadd($item->product->stock, $item->nums),
            ];
        }

        $productStatus = ProductModel::upsert($productData, ['id'], ['stock']);

        if ($orderStatus === false || $productStatus === false) {
            DB::rollBack();
            return $this->error('取消失败', null);
        } else {
            DB::commit();
            return $this->success('取消成功', null);
        }
    }

    // 申请退款
    public function rejected(): JsonResponse
    {
        $orderId = request('orderid', 0);
        $busId = request('busid', 0);
        // 退款选项，1：仅退款，2：退货退款
        $type = request('type', 0);
        // 退款原因
        $reason = request('reason', '');

        $where = [
            'code' => $orderId,
            'busid' => $busId,
        ];

        $orderData = OrderModel::where($where)->first();

        if (!$orderData) {
            return $this->error('订单不存在', null);
        }

        // 开启事务
        DB::beginTransaction();

        $data = [
            'refundreason' => $reason,
        ];

        if ($type == 1) {
            $data = array_merge($data, ['status' => '-1']);
        } else {
            $data = array_merge($data, ['status' => '-2']);
        }

        // 退货选项
        if ($type == 1) {
            // 更新订单状态
            $orderStatus = OrderModel::where($where)->update($data);
        } elseif ($type == 2) {
            // 更新订单状态
            $orderStatus = OrderModel::where($where)->update($data);
        } else {
            return $this->error('退货选项错误', null);
        }

        if ($orderStatus === false) {
            DB::rollBack();
            return $this->error('系统错误', null);
        } else {
            DB::commit();
            return $this->success('申请成功', null);
        }
    }

    // 确认收货
    public function confirm(): JsonResponse
    {
        $orderId = request('orderid', 0);
        $busId = request('busid', 0);

        $where = [
            'code' => $orderId,
            'busid' => $busId,
        ];

        $orderData = OrderModel::where($where)->first();

        if (!$orderData) {
            return $this->error('订单不存在', null);
        }

        // 开启事务
        DB::beginTransaction();

        // 更新订单状态
        $orderStatus = OrderModel::where($where)->update(['status' => '3']);

        if ($orderStatus === false) {
            DB::rollBack();
            return $this->error('系统错误', null);
        } else {
            DB::commit();
            return $this->success('确认成功', null);
        }
    }
}

