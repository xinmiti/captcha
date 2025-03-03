<?php
namespace Xinmiti\Captcha\Drivers;

interface DriverInterface
{
    /**
     * 设置验证码
     *
     * @param string $key
     * @param string $value
     * @param int $expire
     * @return bool
     */
    public function set($key, $value, $expire = 300);

    /**
     * 获取验证码
     *
     * @param string $key
     * @return string|null
     */
    public function get($key);

    /**
     * 删除验证码
     *
     * @param string $key
     * @return bool
     */
    public function delete($key);
} 