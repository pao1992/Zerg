<?php
/**
 * 路由注册
 *
 * 以下代码为了尽量简单，没有使用路由分组
 * 实际上，使用路由分组可以简化定义
 * 并在一定程度上提高路由匹配的效率
 */

// 写完代码后对着路由表看，能否不看注释就知道这个接口的意义
use think\Route;

//Sample
Route::get('api/:version/sample/:key', 'api/:version.Sample/getSample');
Route::post('api/:version/sample/test3', 'api/:version.Sample/test3');

//Miss 404
//Miss 路由开启后，默认的普通模式也将无法访问
//Route::miss('api/v1.Miss/miss');

//Banner
Route::get('api/:version/banner/:id', 'api/:version.Banner/getBanner');
//Theme
// 如果要使用分组路由，建议使用闭包的方式，数组的方式不允许有同名的key
//Route::group('api/:version/theme',[
//    '' => ['api/:version.Theme/getThemes'],
//    ':t_id/product/:p_id' => ['api/:version.Theme/addThemeProduct'],
//    ':t_id/product/:p_id' => ['api/:version.Theme/addThemeProduct']
//]);

Route::group('api/:version/theme',function(){
    Route::get('', 'api/:version.Theme/getSimpleList');
    Route::get('/:id', 'api/:version.Theme/getComplexOne');
    Route::post(':t_id/product/:p_id', 'api/:version.Theme/addThemeProduct');
    Route::delete(':t_id/product/:p_id', 'api/:version.Theme/deleteThemeProduct');
});

//Route::get('api/:version/theme', 'api/:version.Theme/getThemes');
//Route::post('api/:version/theme/:t_id/product/:p_id', 'api/:version.Theme/addThemeProduct');
//Route::delete('api/:version/theme/:t_id/product/:p_id', 'api/:version.Theme/deleteThemeProduct');

//Product
Route::post('api/:version/product', 'api/:version.Product/createOne');
Route::delete('api/:version/product/:id', 'api/:version.Product/deleteOne');
Route::get('api/:version/product/by_category/paginate', 'api/:version.Product/getByCategory');
Route::get('api/:version/product/by_category', 'api/:version.Product/getAllInCategory');
Route::get('api/:version/product/:id', 'api/:version.Product/getOne',[],['id'=>'\d+']);
Route::get('api/:version/product/recent', 'api/:version.Product/getRecent');

//Category
Route::get('api/:version/category', 'api/:version.Category/getCategories');
// 正则匹配区别id和all，注意d后面的+号，没有+号将只能匹配个位数
Route::get('api/:version/category/:id', 'api/:version.Category/getCategory',[], ['id'=>'\d+']);
//Route::get('api/:version/category/:id/products', 'api/:version.Category/getCategory',[], ['id'=>'\d+']);
Route::get('api/:version/category/all', 'api/:version.Category/getAllCategories');
Route::get('api/:version/category/tree', 'api/:version.Category/getCategoryTree');
Route::get('api/:version/category/treeWithProds', 'api/:version.Category/getCateTreeWithProds');

//新增一个分类
Route::post('api/:version/category', 'api/:version.Category/createOrUpdateCategory');
//app
Route::get('api/:version/app/appid', 'api/:version.App/getAppid');
//code
Route::get('api/:version/getCode', 'api/:version.Token/getCode');
Route::get('api/:version/receiveCode', 'api/:version.Token/receiveCode');

//Token
Route::post('api/:version/token/user', 'api/:version.Token/getToken');
Route::post('api/:version/token/app', 'api/:version.Token/getAppToken');
Route::post('api/:version/token/verify', 'api/:version.Token/verifyToken');

//Address
Route::post('api/:version/address', 'api/:version.Address/createOrUpdateAddress');
Route::get('api/:version/address', 'api/:version.Address/getUserAddress');

//Order
Route::post('api/:version/order', 'api/:version.Order/createOrder');
Route::get('api/:version/order/:id', 'api/:version.Order/getDetail',[], ['id'=>'\d+']);
Route::put('api/:version/order/delivery', 'api/:version.Order/delivery');

Route::get('api/:version/order', 'api/:version.Order/getSummary');
Route::get('api/:version/order/order_status', 'api/:version.Order/getSummaryByOrderStatus');
Route::get('api/:version/order/pay_status', 'api/:version.Order/getSummaryByPayStatus');

//coupon
Route::get('api/:version/coupon/:id', 'api/:version.Coupon/getCouponById',[],['id'=>'\d+']);
Route::get('api/:version/coupon/by_date', 'api/:version.Coupon/getCouponsByDate');
Route::get('api/:version/coupon/mark', 'api/:version.Coupon/getCouponsMark');

//userCoupon
Route::get('api/:version/userCoupon', 'api/:version.UserCoupon/getByUser');
Route::post('api/:version/userCoupon/:id', 'api/:version.UserCoupon/receiveCoupon',[],['id'=>'\d+']);
Route::post('api/:version/userCoupon/check', 'api/:version.UserCoupon/check',[],['id'=>'\d+']);

//user
Route::get('api/:version/user', 'api/:version.User/getUser');
Route::post('api/:version/user', 'api/:version.User/createOne');
Route::post('api/:version/user/verify_code', 'api/:version.User/getVerifyCode');





//Pay
Route::post('api/:version/pay/pre_order', 'api/:version.Pay/getPreOrder');
Route::post('api/:version/pay/notify', 'api/:version.Pay/receiveNotify');
Route::post('api/:version/pay/re_notify', 'api/:version.Pay/redirectNotify');
Route::post('api/:version/pay/concurrency', 'api/:version.Pay/notifyConcurrency');

//Message
Route::post('api/:version/message/delivery', 'api/:version.Message/sendDeliveryMsg');
//event
Route::get('api/:version/event', 'api/:version.Event/getEvents');
//system
Route::get('api/:version/system/store', 'api/:version.System/getStore');


//return [
//        ':version/banner/[:location]' => 'api/:version.Banner/getBanner'
//];

//Route::miss(function () {
//    return [
//        'msg' => 'your required resource are not found',
//        'error_code' => 10001
//    ];
//});



