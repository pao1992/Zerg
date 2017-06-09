<?php

namespace app\api\model;

use think\Model;

class Event extends BaseModel
{
    protected function getDateAttr($value){
        return date('Y/m/d',$value);
    }
    protected function setDateAttr($value){
        return strtotime($value);
    }
}
