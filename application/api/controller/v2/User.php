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
use app\api\service\UserToken;
use app\api\validate\Login;
use app\api\validate\UserNew;
use app\api\model\User as UserModel;
use app\lib\enum\ScopeEnum;
use app\lib\exception\BaseException;
use app\lib\exception\ParameterException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\VerifyException;
use think\Db;
use app\api\service\Token;
use app\index\model\Send;
use think\exception\ErrorException;
use app\api\service\User as UserService;
use app\lib\exception\LoginException;

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
        $result = UserService::getVerifyCode($tel);
        return $result;
//        if($result !== true){
//            return new SuccessMessage();
//        }
//        throw new ErrorException();
    }

    public function login($tel,$password){
        //登录，验证数据
        (new Login())->goCheck();
        $user = UserModel::where('tel',$tel)->find();
        if($user['password'] == md5($password)){
            //登录成功，去生成token
            $key = UserToken::generateToken();
            $value = $user;
            $value['uid'] = $value['id'];
            $value['scope'] = ScopeEnum::User;
            $value = json_encode($value);
            $expire_in = config('setting.token_expire_in');
            $result = cache($key, $value, $expire_in);
            if (!$result){
                throw new TokenException([
                    'msg' => '服务器缓存异常',
                    'errorCode' => 10005
                ]);
            }
            return array('token'=>$key);
        }else{
            //登录失败，返回账号或密码错误
            throw new LoginException();
        }
    }
}