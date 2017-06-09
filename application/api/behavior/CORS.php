<?php
/**
 * Created by 七月.
 * Author: 七月
 * 微信公号：小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/3/19
 * Time: 3:00
 */

namespace app\api\behavior;


use think\Response;

class CORS
{
    public function appInit(&$params)
    {
        $allow_origin = config('setting.allow_origin');
        $origin = isset($_SERVER['HTTP_ORIGIN'])? $_SERVER['HTTP_ORIGIN'] : '';
        if(in_array($origin, $allow_origin)) {
            header('Access-Control-Allow-Origin: '.$origin);
            header('Access-Control-Allow-Credentials: true');
            header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
            header('Access-Control-Allow-Methods: POST,GET,DELETE,PUT');
            if (request()->isOptions()) {
                exit();
            }
        }
    }
}