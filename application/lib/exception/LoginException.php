<?php
/**
 * Created by 七月
 * Author: 七月
 * Date: 2017/2/18
 * Time: 15:44
 */

namespace app\lib\exception;

/**
 * 创建成功（如果不需要返回任何消息）
 * 200 成功,201 创建成功，202需要一个异步的处理才能完成请求
 */
class LoginException extends BaseException
{
    public $code = 401;
    public $msg = '登录失败,登录尝试不成功...';
    public $errorCode = 10008;
}