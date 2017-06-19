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
use app\lib\exception\BaseException;

class Test extends BaseController
{
    public function test(){
        throw new BaseException([
            'code'=>200,
            'msg'=>'这是一个异常',
        ]);
    }
}