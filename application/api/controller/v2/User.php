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
        //从session取出验证码
        $verify = session('verify');
        //这时候session应该怎么取出，需要标识
        $verify = 1102;
        //验证user
        $validate = new UserNew();
        $validate->goCheck();
        $data = $validate->getDataByRule(input('post.'));
        //验证验证码是否一致
        if ($data['verify'] != $verify) throw new VerifyException();
        unset($data['verify']);
        $user = UserModel::createOne($data);
        if (!$user) {
            throw New BaseException();
        }
        return $user;
    }

    public function getVerifyCode($tel)
    {
        //验证手机号
        if(!preg_match('/^1[34578]\d{9}$/',$tel)){
            throw new ParameterException(['msg'=>'电话号码错误！']);
        }
        $result = UserSevice::getVerifyCode($tel);
        if($result !== true){
            return new SuccessMessage();
        }
        throw new ErrorException();
    }
}