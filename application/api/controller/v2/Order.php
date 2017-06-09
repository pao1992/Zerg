<?php
/**
 * Created by 七月.
 * Author: 七月
 * 微信公号：小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/2/22
 * Time: 21:52
 */

namespace app\api\controller\v2;

use app\api\controller\BaseController;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\api\service\Token;
use app\api\validate\BaseValidate;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\validate\OrderCreate;
use app\api\validate\PagingParameter;
use app\lib\exception\OrderException;
use app\lib\exception\SuccessMessage;
//use think\Validate;
use app\lib\exception\ParameterException;
use think\Controller;

class Order extends BaseController
{
    protected $beforeActionList = [
//        'checkExclusiveScope' => ['only' => 'placeOrder'],
//        'checkPrimaryScope' => ['only' => 'getDetail,getSummaryByUser'],
//        'checkSuperScope' => ['only' => 'delivery,getSummary']
    ];

    /**
     * 创建订单
     * @url /order
     * @HTTP POST
     */
    public function createOrder()
    {
        $data = input('post.');
        //return $data;
//        //检查数据是否符合规范
        (new OrderCreate())->check($data);
        $order = new OrderService();
        $uid = Token::getCurrentUid();
        $status = $order->create($uid, $data);
        //返回订单结果
        return $status;
//        $products = input('post.products/a');
//        //获取uid
//        $uid = Token::getCurrentUid();
//        //service层
//        $order = new OrderService();
//        //获取订单状态，订单状态OK则创建订单
//        $status = $order->place($uid, $products);
//        return $status;
    }

    /**
     * 获取订单详情
     * @param $id
     * @return static
     * @throws OrderException
     * @throws \app\lib\exception\ParameterException
     */
    public function getDetail($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $orderDetail = OrderModel::get($id);
        if (!$orderDetail) {
            throw new OrderException();
        }
        return $orderDetail
            ->hidden(['prepay_id']);
    }

    /**
     * 根据用户订单状态获取订单列表（简要信息）
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function getSummaryByOrderStatus($order_status, $limit = 10, $offset = 20)
    {

        $validate = new BaseValidate();
        $validate->rule([
            'order_status' => 'require|egt:0',
            'limit' => 'require|isPositiveInteger',
            'offset' => 'require|natureNumber'
        ]);
        $params = input('get.');
        $validate->goCheck($params);

        $uid = Token::getCurrentUid();
        $orders = OrderModel::getSummaryByOrderStatus($uid, $order_status,$limit,$offset);
        $data = $orders->hidden(['snap_items', 'snap_address','user_id','prepay_id'])
            ->toArray();
        return $data;

    }
    /**
     * 根据用户订单支付状态获取订单列表（简要信息）
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function getSummaryByPayStatus($pay_status, $limit = 10, $offset = 20)
    {
        $validate = new BaseValidate();
        $validate->rule([
            'pay_status' => 'require|egt:0',
            'limit' => 'require|isPositiveInteger',
            'offset' => 'require|natureNumber'
        ]);
        $params = input('get.');
        $validate->goCheck($params);

        $uid = Token::getCurrentUid();
        $orders = OrderModel::getSummaryByPayStatus($uid, $pay_status,$limit,$offset);
        $data = $orders->hidden(['snap_items', 'snap_address','user_id','prepay_id'])
            ->toArray();
        return $data;

    }
    /**
     * 获取全部订单简要信息（分页）
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function getSummary($limit = 10, $offset = 20)
    {
        $validate = new BaseValidate();
        $validate->rule([
            'limit' => 'require|isPositiveInteger',
            'offset' => 'require|natureNumber'
        ]);
        $validate->goCheck();
        $uid = Token::getCurrentUid();
        $orders = OrderModel::getSummary($uid, $limit, $offset);
        $orders = $orders->hidden(['snap_items', 'snap_address','user_id','prepay_id'])
            ->toArray();
        return $orders;
    }

    public function delivery($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $order = new OrderService();
        $success = $order->delivery($id);
        if ($success) {
            return new SuccessMessage();
        }
    }
}






















