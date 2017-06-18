<?php
/**
 * Created by 七月.
 * Author: 七月
 * 微信公号：小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/3/5
 * Time: 13:32
 */

namespace app\api\service;
use app\api\model\Send;

class User
{
    public static function getVerifyCode($tel)
    {
        //生成4位数验证码
        $arr = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $code = '';
        for ($i = 4; $i > 0; $i--) {
            $code .= $arr[rand(0, 9)];
        }
        session('verifyCode', $code);
        //发送阿里大于
//        $Send = new Send();
//        $result = $Send->sms([
//            'param'  => ['code'=>$code],
//            'mobile'  => $tel,
//            'template'  => config('alidayu.regTemplate')
//        ]);
        //测试用
        return $code;
        return $result;
    }
}