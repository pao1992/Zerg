<?php
/**
 * Created by 七月.
 * Author: 七月
 * 微信公号：小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/2/25
 * Time: 16:18
 */

namespace app\lib\exception;


class VerifyException extends BaseException
{
    public $code = 200;
    public $msg = '验证码错误！';
    public $errorCode = 10006;
}