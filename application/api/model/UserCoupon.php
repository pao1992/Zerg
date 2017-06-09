<?php

namespace app\api\model;
use think\Db;
use think\Model;
use app\lib\exception\CouponException;

class UserCoupon extends BaseModel
{
    protected $hidden = [ 'create_time','delete_time','update_time'];
    protected $autoWriteTimestamp = true;
    public function coupon(){
        return $this->belongsTo('Coupon');
    }
    public static function receiveCoupon($uid, $id)
    {
        Db::transaction(function () use ($uid, $id) {
            //判断是否已经领取过了
            $res = self::where(['user_id'=>$uid,'coupon_id'=>$id])->find();
            if($res) {
                throw new CouponException([
                    'code'=>200,
                    'errorCode'=>90001,
                    'msg'=>'该优惠券已领取'
                ]);
            }
            //判断是不是被领取完了
            $coupon = Db::table('coupon')->where('coupon_id',$id)->find();
            if($coupon['num'] <= 0) throw new CouponException([
                'code'=>200,
                'errorCode'=>90003,
                'msg'=>'很抱歉，已经被抢完了！'
            ]);
            $user = Db::table('user')->where('id' , $uid)->find();
            Db::table('user_coupon')->insert(['user_id' => $uid, 'coupon_id' => $id,'nickname'=>$user['nickname'],'tel'=>$user['tel']]);
            Db::table('coupon')->where('coupon_id', $id)->setDec('num');
        });
    }

}
