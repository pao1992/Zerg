<?php

namespace app\api\model;

use think\Model;

class Product extends BaseModel
{
    protected $hidden = [
        'delete_time',
        'create_time', 'update_time'];
//    public function spec()
//    {
//        return $this->hasOne('Spec', 'product_id', 'id');
//    }
//修改器
    public function setSpecAttr($value)
    {
        return json_encode($value);
    }
    public function setCategoryPathAttr($value)
    {
        return implode('_',$value);
    }
    public function setNumAttr($value)
    {
        return implode('_',$value);
    }
    public function getNumAttr($value)
    {
        return explode('_',$value);
    }
    public function getCategoryPathAttr($value)
    {
        return explode('_',$value);
    }
    public function getSpecAttr($value)
    {
        return json_decode($value,true);
    }
    public function setBookTimeAttr($value)
    {
        return strtotime($value);
    }
    public function getBookTimeAttr($value)
    {
        return date('Y/m/d',$value);
    }
//    public function getPicOriginalAttr($value, $data)
//    {
//        return $this->prefixImgUrl($value, $data);
//    }
//    public function getPicThumbAttr($value, $data)
//    {
//        return $this->prefixImgUrl($value, $data);
//    }

    public static function getAllProducts(){
        $products = self::order('create_time desc')->select();
        return $products;
    }
    public static function getProductDetail($id)
    {
        $product = self::find($id);
        return $product;
    }
//    public static function CreateOne($data){
//        self::insert($data);
//    }
}
//{
//    protected $autoWriteTimestamp = 'datetime';
//    protected $hidden = [
//        'delete_time', 'main_img_id', 'pivot', 'from', 'category_id',
//        'create_time', 'update_time'];
//
//    /**
//     * 图片属性
//     */
//    public function imgs()
//    {
//        return $this->hasMany('ProductImage', 'product_id', 'id');
//    }
//
//    public function getMainImgUrlAttr($value, $data)
//    {
//        return $this->prefixImgUrl($value, $data);
//    }
//
//
//    public function properties()
//    {
//        return $this->hasMany('ProductProperty', 'product_id', 'id');
//    }
//
//    /**
//     * 获取某分类下商品
//     * @param $categoryID
//     * @param int $page
//     * @param int $size
//     * @param bool $paginate
//     * @return \think\Paginator
//     */
//    public static function getProductsByCategoryID(
//        $categoryID, $paginate = true, $page = 1, $size = 30)
//    {
//        $query = self::
//        where('category_id', '=', $categoryID);
//        if (!$paginate)
//        {
//            return $query->select();
//        }
//        else
//        {
//            // paginate 第二参数true表示采用简洁模式，简洁模式不需要查询记录总数
//            return $query->paginate(
//                $size, true, [
//                'page' => $page
//            ]);
//        }
//    }
//
//    /**
//     * 获取商品详情
//     * @param $id
//     * @return null | Product
//     */
//    public static function getProductDetail($id)
//    {
//        //千万不能在with中加空格,否则你会崩溃的
//        //        $product = self::with(['imgs' => function($query){
//        //               $query->order('index','asc');
//        //            }])
//        //            ->with('properties,imgs.imgUrl')
//        //            ->find($id);
//        //        return $product;
//
//        $product = self::with(
//            [
//                'imgs' => function ($query)
//                {
//                    $query->with(['imgUrl'])
//                        ->order('order', 'asc');
//                }])
//            ->with('properties')
//            ->find($id);
//        return $product;
//    }
//
//    public static function getMostRecent($count)
//    {
//        $products = self::limit($count)
//            ->order('create_time desc')
//            ->select();
//        return $products;
//    }
//
//}
