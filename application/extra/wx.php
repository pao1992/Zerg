<?php
/**
 * Created by 七月
 * Author: 七月
 * 微信公号: 小楼昨夜又秋�?
 * 知乎ID: 七月在夏�?
 * Date: 2017/2/22
 * Time: 13:49
 */

return [
    //  +---------------------------------
    //  微信相关配置
    //  +---------------------------------

    // 小程序app_id
    'app_id' => 'wx0646ce2a8a052cfb',
    // 小程序app_secret
    'app_secret' => '15fc45ded73243123f0b1dfe94b9c527',
    //微信获取code跳转后的回调地址
    'author_url'=>'http://www.listudio.cn/#/author',
    // 'author_url'=>'http://192.168.1.100:8088/#/author',
    //微信获取code 的url地址
    'code_url' => "https://open.weixin.qq.com/connect/oauth2/authorize?".
        "appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=%s#wechat_redirect",
    // 微信使用code换取用户openid及access_token的url地址
    'login_url' => "https://api.weixin.qq.com/sns/oauth2/access_token?" .
    "appid=%s&secret=%s&code=%s&grant_type=authorization_code",
    //非静默状态下使用access_token和openid换取用户信息
    'userInfo_url' => "https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s"

];
