<?php
namespace Xinmiti\Captcha\Drivers;

class RedisDriver implements DriverInterface
{
    protected $redis;
    protected $prefix;

    public function __construct(array $config)
    {
        $this->prefix = $config['prefix'] ?? 'captcha:';
        
        $this->redis = new \Redis();
        $this->redis->connect(
            $config['host'] ?? '127.0.0.1',
            $config['port'] ?? 6379
        );
        
        if (!empty($config['password'])) {
            $this->redis->auth($config['password']);
        }
        
        if (isset($config['database'])) {
            $this->redis->select($config['database']);
        }
    }

    public function set($key, $value, $expire = 300)
    {
        return $this->redis->setex($this->getKey($key), $expire, $value);
    }

    public function get($key)
    {
        return $this->redis->get($this->getKey($key));
    }

    public function delete($key)
    {
        return $this->redis->del($this->getKey($key)) > 0;
    }

    protected function getKey($key)
    {
        return $this->prefix . $key;
    }
} 