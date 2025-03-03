<?php
namespace Cursor\Captcha\ThinkPHP\Facade;

use think\Facade;

/**
 * @method static array create() 创建字母数字验证码
 * @method static array createChinese() 创建中文验证码
 * @method static array createSlide() 创建滑块验证码
 * @method static array createRotate() 创建旋转验证码
 * @method static bool verify(string $key, string $code) 验证验证码
 */
class Captcha extends Facade
{
    protected static function getFacadeClass()
    {
        return 'captcha';
    }
} 