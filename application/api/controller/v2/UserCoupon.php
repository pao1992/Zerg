<?php
/**
 * Created by 七月.
 * Author: 七月
 * 微信公号：小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/2/19
 * Time: 11:28
 */

namespace app\api\controller\v2;


use app\api\controller\BaseController;
use app\api\model\UserCoupon as UserCouponModel;
use app\api\model\Coupon as CouponModel;
use app\api\validate\CouponNew;
use app\lib\exception\SuccessMessage;
use app\lib\exception\BaseException;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\Token;

class UserCoupon extends BaseController
{
    public function getByUser(){
        $uid = Token::getCurrentUid();
        $res = UserCouponModel::where('user_id',$uid)->order('id DESC')->select()->toArray();
        foreach ($res as $k=>$v){
            $res[$k]['coupon'] = CouponModel::withTrashed()->find($v['coupon_id']);
        }
        return $res;
    }
    public function receiveCoupon($id)
    {
        (new IDMustBePositiveInt())->check($id);
        $uid = Token::getCurrentUid();
        UserCouponModel::receiveCoupon($uid,$id);
        return new SuccessMessage();


    }
    public function check()
    {
        $data = input('param.');
        (new IDMustBePositiveInt())->check($data['id']);
        //检查核销密码
        if(config('setting.coupon_pwd') !== $data['coupon_pwd']){
            throw new BaseException([
                'errorCode'=>90002,
                'msg'=>'优惠券核销密码错误！',
            ]);
        }
        //开始核销
        $res = UserCouponModel::destroy($data['id']);
        if($res){
            return new SuccessMessage();
        }else{
            throw new BaseException();
        }
    }


}