<?php

namespace app\api\model;

use think\Model;

class Order extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time'];
    protected $autoWriteTimestamp = true;
    public function setBookTimeAttr($value){
        return strtotime($value);
    }
    public function getBookTimeAttr($value){
        return date('Y/m/d',$value);
    }
    public function getSnapItemsAttr($value)
    {
        if (empty($value)) {
            return null;
        }
        return json_decode($value);
    }


    public static function getSummaryByOrderStatus($uid, $order_status, $limit, $offset)
    {
        $data = self::where(['user_id' => $uid, 'order_status' => $order_status])
            ->with('products')->order('create_time desc')
            ->limit($offset, $limit)->select();
        return $data;
    }

    public static function getSummaryByPayStatus($uid, $pay_status, $limit, $offset)
    {
        $data = self::where(['user_id' => $uid, 'pay_status' => $pay_status])
            ->with('products')->order('create_time desc')
            ->limit($offset, $limit)->select();
        return $data;
    }

    public static function getSummary($uid, $limit, $offset)
    {
        $orders = self::with('products')->order('create_time desc')
            ->where(['user_id' => $uid])
            ->limit($offset, $limit)->select();
        return $orders;
    }

    public function products()
    {
        return $this->HasMany('order_product', 'order_id', 'order_id');
    }


}
