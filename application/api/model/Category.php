<?php

namespace app\api\model;

use think\Model;

class Category extends BaseModel
{
    public function products()
    {
        return $this->hasMany('Product', 'category_id', 'category_id');
    }

//    public function img()
//    {
//        return $this->belongsTo('Image', 'topic_img_id', 'id');
//    }

    public static function getCategories($ids)
    {
        $categories = self::select($ids);
        return $categories;
    }
    public static function getCategory($id)
    {
        $category = self::get($id);
        return $category;
    }
}
