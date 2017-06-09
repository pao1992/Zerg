<?php
/**
 * Created by 七月.
 * Author: 七月
 * 微信公号：小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/2/23
 * Time: 2:56
 */

namespace app\api\controller\v2;


use app\api\controller\BaseController;
use think\Db;

class System extends BaseController
{
//    protected $beforeActionList = [
//        'checkPrimaryScope' => ['only' => 'test']
//    ];
public function getStore(){
    $res = Db::table('system')->find(1);
    return $res;
}

}