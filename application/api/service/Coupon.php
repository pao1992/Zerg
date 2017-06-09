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
use app\api\model\Coupon as CouponModel;

class Coupon
{
    public static function getMonthDays($month, $format = "Y-m-d")
    {
        $start = strtotime("first day of $month");
        $end = strtotime("last day of $month");
        $days = array();
//        for ($i = $start; $i <= $end; $i += 24 * 3600) $days[] = date($format, $i);
        for ($i = $start; $i <= $end; $i += 24 * 3600) $days[] = $i;

        return $days;
    }

    public static function getCouponsMark($month = "this month", $format = 'd')
    {
        //获得当月所有日期数组
        $currentDays = $monthDays = self::getMonthDays($month, $format);
        $monthStart = strtotime("first day of $month");
        $monthEnd = strtotime("last day of $month");
        //获取在选中月份中的所有优惠
        $coupons = CouponModel::getCouponsByMouth($month);
        //获取这些优惠下的所有时间
        $markDays = [];
        foreach ($coupons as $v) {
            //如果有超出本月的时间，将其裁剪掉
            $start = max($v['start'], $monthStart);
            $end = min($v['end'], $monthEnd);
            $days = array();
            for ($i = $start; $i <= $end; $i += 24 * 3600) $days[] = $i;
            $markDays = array_merge($days, $markDays);
        }
        $markDays = array_unique($markDays);
        sort($markDays);
        foreach ($markDays as $k=>$v) $markDays[$k] = date('Y/n/j',$v);
        return $markDays;
    }

}