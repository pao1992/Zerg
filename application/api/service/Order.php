<?php
/**
 * Created by 七月.
 * Author: 七月
 * 微信公号：小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/2/23
 * Time: 1:48
 */

namespace app\api\service;


use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\Order as OrderModel;
use app\api\model\UserAddress;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use think\Db;
use think\Exception;

/**
 * 订单类
 * 订单做了以下简化：
 * 创建订单时会检测库存量，但并不会预扣除库存量，因为这需要队列支持
 * 未支付的订单再次支付时可能会出现库存不足的情况
 * 所以，项目采用3次检测
 * 1. 创建订单时检测库存
 * 2. 支付前检测库存
 * 3. 支付成功后检测库存
 */
class Order
{
    //这是前端发过来的订单商品数据，其中包含了选中的规格
    protected $oProducts;
    //这是商品原始数据
    protected $products;
    //这是即将要保存到order_product表中的数据
    protected $orderProds;
    protected $uid;

    function __construct()
    {
    }

    /**
     * @param int $uid 用户id
     * @param array $oProducts 订单商品列表
     * @return array 订单商品状态
     * @throws Exception
     */
    public function place($uid, $oProducts)
    {
        $this->oProducts = $oProducts;
        $this->products = $this->getProductsByOrder($oProducts);
        $this->uid = $uid;
        //获取订单状态
        $status = $this->getOrderStatus();
        if (!$status['pass']) {
            $status['order_id'] = -1;
            return $status;
        }
        //生成订单预览
        $orderSnap = $this->snapOrder();
        //创建订单
        $status = self::createOrderByTrans($orderSnap);
        $status['pass'] = true;
        //返回订单状态
        return $status;
    }

    /**
     * @param int $uid 用户id
     * @param array $oProducts 订单商品列表
     * @return array 订单商品状态
     * @throws Exception
     */
    public function create($uid, $data)
    {
        $this->uid = $uid;
        $this->data = $data;
        $this->oProducts = $data['products'];
        //获取数据库中的商品信息
        $this->products = $this->getProductsByOrder($data['products']);
        //生成订单预览
        $orderSnap = $this->snapOrder();
        //创建订单
        $status = self::createOrderByTrans($orderSnap);
        //返回订单状态
        return $status;
    }
    public function getOrderById($id){
        $res = OrderModel::find($id);
        return $res;
    }

//处理，并微信通知
    public function delivery($orderID, $jumpPage = '')
    {
        $order = OrderModel::where('id', '=', $orderID)
            ->find();
        if (!$order) {
            throw new OrderException();
        }
        if ($order->status != OrderStatusEnum::PAID) {
            throw new OrderException([
                'msg' => '还没付款呢，想干嘛？或者你已经更新过订单了，不要再刷了',
                'errorCode' => 80002,
                'code' => 403
            ]);
        }
        $order->status = OrderStatusEnum::DELIVERED;
        $order->save();
//            ->update(['status' => OrderStatusEnum::DELIVERED]);
        $message = new DeliveryMessage();
        return $message->sendDeliveryMessage($order, $jumpPage);
    }



    // 根据订单查找真实商品
    private function getProductsByOrder($oProducts)
    {
        $oPIDs = [];
        foreach ($oProducts as $item) {
            array_push($oPIDs, $item['product_id']);
        }
        // 为了避免循环查询数据库
        $products = Product::all($oPIDs)
            ->visible(['product_id', 'shop_price', 'product_name', 'pic_thumb', 'spec'])
            ->toArray();
        return $products;
    }

    // 创建订单时没有预扣除库存量，简化处理
    // 如果预扣除了库存量需要队列支持，且需要使用锁机制
    //这里是服务，不做库存检查处理
    private function createOrderByTrans($snap)
    {
        try {
            $orderNo = $this->makeOrderNo();
            $order = new OrderModel();
            $order->user_id = $this->uid;
            $order->order_sn = $orderNo;
            $order->book_time = $this->data['book_time'];
            $order->linkman = $this->data['userInfo']['linkman'];
            $order->tel = $this->data['userInfo']['tel'];
            $order->birthday = $this->data['userInfo']['birthday'];
            $order->products_price = $snap['products_price'];
            //订单总价，暂时不考虑优惠券
            $order->total_price = $snap['products_price']-$snap['discount'];
            $order->discount = $snap['discount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->save();

            $orderID = $order->order_id;
            foreach ($this->orderProds as &$v) {
                $v['order_id'] = $orderID;

            }
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->orderProds);
            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'create_time' => $order->create_time
            ];
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // 预检测并生成订单快照
    private function snapOrder()
    {
        // status可以单独定义一个类
        $snap = [
            'products_price' => 0,
            'snapName' => $this->products[0]['product_name'],
            'snapImg' => $this->products[0]['pic_thumb'],
            'discount' => 0,
        ];
        $count = count($this->products);
        //计算组合优惠价格
        if ($count > 1) {
            $snap['discount'] = (int)(count($this->products)-1)*config('setting.combination_discount');
            $snap['snapName'] .= '等';
        }


        for ($i = 0; $i < count($this->products); $i++) {
            $product = $this->products[$i];
            $oProduct = $this->oProducts[$i];
            $this->orderProds[$i]['product_name'] = $this->products[$i]['product_name'];
            $this->orderProds[$i]['product_id'] = $this->products[$i]['product_id'];
            $this->orderProds[$i]['count'] = $this->oProducts[$i]['count'];
            $total_price = 0;
            //记录选中的spec
            $selectedSpec = [];
            //统计订单价格，这里需要重点处理
            foreach ($product['spec'] as $k => $v) {
                //判断4个规格中有没有包含与数量相关的键num_related,没有就主动加上
                if(!array_key_exists('num_related', $v)){
                    $v['num_related'] = false;
                }
                switch ($k) {
                    case 'required_single':
                        $selectedSpec[$k] = [];
                        $data = $oProduct['spec'][$k]['data'];
                        $num_related = $v['num_related'];
                        foreach ($v['items_price'] as $items_price) {
                            if ($data == $items_price['key_name']) {
                                $selectedSpec[$k]['selected'] = $data;
                                if ($num_related) {
                                    $total_price += $oProduct['count'] * $items_price['price'];

                                } else {
                                    $total_price += $items_price['price'];
                                }
                            }
                        }
                        break;
                    case 'unrequired_single':
                        $selectedSpec[$k] = [];
                        $data = $oProduct['spec'][$k]['data'];
                        $num_related = $v['num_related'];
                        $list = [];
                        foreach ($v['items'] as $items) {
                            foreach ($items['options'] as $option) {
                                $list[$items['name'] . '@' . $option['item']] = $option['price'];
                            }
                        }
                        foreach ($data as $item) {
                            $selectedSpec[$k]['selected'][] = $item;
                            if ($num_related) {
                                $total_price += $oProduct['count'] * $list[$item];
                            } else {
                                $total_price += $list[$item];
                            }
                        }
                        break;
                    case 'required_multiple':
                        $selectedSpec[$k] = [];
                        $data = $oProduct['spec'][$k]['data'];
                        $num_related = $v['num_related'];
                        $list = [];
                        foreach ($v['items'] as $items) {
                            foreach ($items['options'] as $option) {
                                $list[$items['name'] . '@' . $option['item']] = $option['price'];
                            }
                        }
                        foreach ($data as $item) {
                            $selectedSpec[$k]['selected'][] = $item;
                            if ($num_related) {
                                $total_price += $oProduct['count'] * $list[$item];
                            } else {
                                $total_price += $list[$item];
                            }
                        }
                        break;
                    case 'unrequired_multiple':
                        $selectedSpec[$k] = [];
                        $data = $oProduct['spec'][$k]['data'];
                        $num_related = $v['num_related'];
                        $list = [];
                        foreach ($v['items'] as $items) {
                            foreach ($items['options'] as $option) {
                                $list[$items['name'] . '@' . $option['item']] = $option['price'];
                            }
                        }
                        foreach ($data as $item) {
                            $selectedSpec[$k]['selected'][] = $item;
                            if ($num_related) {
                                $total_price += $oProduct['count'] * $list[$item];
                            } else {
                                $total_price += $list[$item];
                            }
                        }
                        break;

                }
            }
            $snap['products_price'] += $total_price;
            $this->orderProds[$i]['total_price'] = $total_price;
            $this->orderProds[$i]['spec'] = json_encode($selectedSpec);
        }
        return $snap;
    }

    //生成订单编号
    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }
}