<?php
/**
 * Created by 七月.
 * Author: 七月
 * 微信公号：小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/2/26
 * Time: 14:15
 */

namespace app\api\controller\v2;

use app\api\controller\BaseController;
use app\api\service\Pay as PayService;
use app\api\service\WxNotify;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ParameterException;
use app\api\model\Order as OrderModel;

class Pay extends BaseController
{
    protected $beforeActionList = [
//        'checkExclusiveScope' => ['only' => 'getPreOrder']
    ];
    
    public function getPreOrder()
    {
        $data = input('post.');
        $order_id = $data['order_id'];
        $payment = $data['payment'];
        (new IDMustBePositiveInt()) -> check($order_id);
        if($payment == 'wxpay'){
            //微信支付
            $pay= new PayService($order_id);

        }else if($payment == 'alipay'){
            //阿里支付

        }else{
            throw new ParameterException(['msg'=>'支付参数错误！']);
        }
        $pre_order = $pay->pay();
        $pre_order['appId'] = config('wx.app_id');
        return $pre_order;
    }

    public function redirectNotify()
    {

        $notify = new WxNotify();
        $notify->handle();
    }

    public function notifyConcurrency()
    {
        $notify = new WxNotify();
        $notify->handle();
    }
    
    public function receiveNotify()
    {
//        $xmlData = file_get_contents('php://input');
//        Log::error($xmlData);
        $notify = new WxNotify();
        $notify->handle();
//        $xmlData = file_get_contents('php://input');
//        $result = curl_post_raw('http:/zerg.cn/api/v1/pay/re_notify?XDEBUG_SESSION_START=13133',
//            $xmlData);
//        return $result;
//        Log::error($xmlData);
    }
}