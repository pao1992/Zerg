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
use app\api\model\Event as EventModel;
use app\api\validate\EventNew;
use app\lib\exception\SuccessMessage;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\MissException;
use think\Controller;

class Event extends BaseController
{
    /**
     * 获取全部类目列表，但不包含类目下的商品
     * Request 演示依赖注入Request对象
     * @url /category/all
     * @return array of Categories
     * @throws MissException
     */
    public function getEvents()
    {
        $events = EventModel::all();
        if(empty($events)){
           throw new MissException([
               'msg' => '还没有任何事件',
               'errorCode' => 50000
           ]);
        }
        return $events;
    }

}