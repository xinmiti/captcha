<?php
namespace Xinmiti\Captcha\Drivers;

class FileDriver implements DriverInterface
{
    protected $path;

    public function __construct(array $config)
    {
        $this->path = $config['path'] ?? sys_get_temp_dir();
        if (!is_dir($this->path)) {
            mkdir($this->path, 0777, true);
        }
    }

    public function set($key, $value, $expire = 300)
    {
        $filename = $this->getFilename($key);
        $content = [
            'value' => $value,
            'expire' => time() + $expire
        ];
        
        return file_put_contents($filename, json_encode($content)) !== false;
    }

    public function get($key)
    {
        $filename = $this->getFilename($key);
        if (!file_exists($filename)) {
            return null;
        }

        $content = json_decode(file_get_contents($filename), true);
        if (!$content || time() > $content['expire']) {
            $this->delete($key);
            return null;
        }

        return $content['value'];
    }

    public function delete($key)
    {
        $filename = $this->getFilename($key);
        if (file_exists($filename)) {
            return unlink($filename);
        }
        return true;
    }

    protected function getFilename($key)
    {
        return $this->path . DIRECTORY_SEPARATOR . md5($key) . '.captcha';
    }
} 