<?php
namespace Xinmiti\Captcha\ThinkPHP;

use Xinmiti\Captcha\Captcha;
use think\Service;

class CaptchaService extends Service
{
    public function register()
    {
        $this->app->bind('captcha', function () {
            return new Captcha($this->app->config->get('captcha', []));
        });
    }

    public function boot()
    {
        // 发布配置文件
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => $this->app->getConfigPath() . 'captcha.php',
            ], 'captcha-config');
        }
    }
} 