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
use app\api\validate\UserNew;
use app\api\model\User as UserModel;
use app\lib\exception\BaseException;
use app\lib\exception\ParameterException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\VerifyException;
use think\Db;
use app\api\service\Token;
use app\index\model\Send;
use think\exception\ErrorException;
use app\api\service\User as UserSevice;

class User extends BaseController
{
    public function getUser()
    {
        $uid = Token::getCurrentUid();
        $user = Db::table('user')->find($uid);
        return $user;
    }

    public function createOne()
    {
        //从session取出验证码,储存在哪
        $verifyCode = session('verifyCode');
        //验证user是否
        $validate = new UserNew();
        $validate->goCheck();
        $data = $validate->getDataByRule(input('post.'));
        //验证验证码是否一致
        if ($data['verifyCode'] != $verifyCode) throw new VerifyException();
        unset($data['verifyCode']);
        $user = UserModel::createOne($data);
        if (!$user) {
            throw New BaseException();
        }
        throw new SuccessMessage([
            'msg'=>'用户创建成功！',
        ]);
    }

    public function getVerifyCode($tel)
    {
        //验证手机号
        if(!preg_match('/^1[34578]\d{9}$/',$tel)){
            throw new ParameterException(['msg'=>'电话号码错误！']);
        }
        $user = UserModel::where('tel',$tel)->find();
        if($user){
            throw new BaseException([
                'code'=>400,
                'msg'=>'手机号已被注册！',
                'errorCode'=>10007,
            ]);
        }
        $result = UserSevice::getVerifyCode($tel);
        return $result;
//        if($result !== true){
//            return new SuccessMessage();
//        }
//        throw new ErrorException();
    }
}