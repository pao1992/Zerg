<?php

/**
 * Created by 七月
 * Author: 七月
 * 微信公号: 小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/2/21
 * Time: 12:23
 */

namespace app\api\controller\v2;
use app\api\service\AppToken;
use app\api\service\UserToken;
use app\api\service\Token as TokenService;
use app\api\validate\AppTokenGet;
use app\api\validate\TokenGet;
use think\Controller;
/**
 * 获取令牌，相当于登录
 */
class Token extends Controller
{
    public function getCode(){
        $version = input('param.version');
        //用cookie来储存需要获取微信鉴权的前端页面
        $redirect_uri = url('/api/'.$version.'/receiveCode','','',true);
        //这里需要通过state记录来时候的地址
        $code_url = sprintf(
            config('wx.code_url'), config('wx.app_id'),$redirect_uri,trim($_SERVER['HTTP_REFERER'],'/'));
        $this->redirect($code_url);
    }
    public function receiveCode($code,$state){
        $token = $this->getToken($code);
//        $url = config('wx.author_url');
        //微信获取code跳转后的回调地址
        $url = $state.'/#/author?token='.$token;
        $this->redirect($url);
    }
    /**
     * 用户获取令牌（登陆）
     * @url /token
     * @get code
     */
    public function getToken($code='')
    {
        (new TokenGet())->goCheck();
        $wx = new UserToken($code);
        return $token = $wx->get();
    }

    /**
     * 第三方应用获取令牌
     * @url /app_token?
     * @POST ac=:ac se=:secret
     */
    public function getAppToken($ac='', $se='')
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: GET');
        (new AppTokenGet())->goCheck();
        $app = new AppToken();
        $token = $app->get($ac, $se);
        return [
            'token' => $token
        ];
    }

    public function verifyToken($token='')
    {
        // if(!$token){
        //     throw new ParameterException([
        //         'msg'=>'token不允许为空'
        //     ]);
        // }
        $valid = TokenService::verifyToken($token);
        return [
            'isValid' => $valid
        ];
    }

}