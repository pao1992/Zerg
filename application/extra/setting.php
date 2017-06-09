<?php
/**
 * Created by 七月
 * Author: 七月
 * 微信公号: 小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/2/22
 * Time: 13:49
 */

return [
    'token_expire_in'=>7200,
    'img_prefix'=>'http://images.api.com/',
    //跨域访问
    'allow_origin' => [
        'http://localhost:8088',
        'http://192.168.1.100:8088',
        'http://192.168.1.101:8088',
        'http://192.168.1.102:8088',
        'http://192.168.1.103:8088',
        'http://192.168.1.104:8088',
        'http://192.168.1.105:8088',
        'http://192.168.1.106:8088',
        'http://192.168.1.107:8088',
        'http://192.168.1.108:8088',
        'http://192.168.1.109:8088',
        'http://192.168.1.110:8088',
        'http://infasion.iok.la',
        'http://localhost',
        'http://listudio.cn',
        'http://listudio.cn/',
        'listudio.cn',
        'listudio.cn/',
        'http://www.listudio.cn',
        'http://www.listudio.cn:8088'
    ],
    //商品组合优惠
    'combination_discount'=>20,
    //优惠券核销密码
    'coupon_pwd'=>'123456'
];
