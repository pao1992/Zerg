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


use think\Exception;
use app\api\controller\BaseController;
use app\api\validate\BaseValidate;
use app\api\validate\UserNew;
use app\api\model\User as UserModel;
use app\lib\exception\BaseException;
use app\lib\exception\ParameterException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\VerifyException;
use think\Db;
use app\api\service\Token;
use app\api\model\Send;
use think\exception\ErrorException;

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
        echo session('verifyCode');
        //发送阿里大于
//        $Send = new Send();
//        $result = $Send->sms([
//            'param'  => ['code'=>$code],
//            'mobile'  => $tel,
//            'template'  => config('alidayu.regTemplate')
//        ]);
//        return $result;
    }
}