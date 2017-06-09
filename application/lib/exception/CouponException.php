<?php
/**
 * Created by 七月
 * Author: 七月
 * Date: 2017/2/18
 * Time: 13:47
 */

namespace app\lib\exception;


class CouponException extends BaseException
{
    public $code = 404;
    public $msg = '指定优惠券不存在，请检查优惠券ID';
    public $errorCode = 90000;
}