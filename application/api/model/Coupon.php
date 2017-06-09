<?php

namespace app\api\model;

use app\lib\exception\BaseException;
use app\lib\exception\SuccessMessage;
use think\Db;
use app\api\model\User as UserModel;
use think\Model;
use app\lib\exception\CouponException;

class Coupon extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time', 'pivot'];
    protected $autoWriteTimestamp = true;

    public function setStartAttr($value)
    {
        return strtotime($value);
    }

    public function setEndAttr($value)
    {
        return strtotime($value);
    }

    public function getStartAttr($value)
    {
        return date('Y/m/d', $value);
    }

    public function getEndAttr($value)
    {
        return date('Y/m/d', $value);
    }

    public static function getCouponsByDate($date)
    {
//        return $date;
        $res = self::where('start', '<=', $date)
            ->where('end', '>=', $date)->where('num','>',0)->select();
        return $res;
    }

    /**
     * @return array
     */
    public static function getCouponsByMouth($month)
    {
        $start = strtotime("first day of $month");
        $end = strtotime("last day of $month");
        $sql = "`delete_time` is null AND `num`>0 AND ((`start` BETWEEN  {$start} AND {$end}) OR (`end` BETWEEN {$start} AND {$end}))";
        $res = Db::table('coupon')->where($sql)->select();
        return $res->toArray();
    }

}
