<?php
namespace Cursor\Captcha;

use Intervention\Image\ImageManagerStatic as Image;
use Cursor\Captcha\Drivers\DriverInterface;
use Cursor\Captcha\Exceptions\CaptchaException;

class Captcha
{
    protected $config;
    protected $driver;

    public function __construct(array $config = [])
    {
        $this->config = array_merge(include __DIR__ . '/config/config.php', $config);
        $this->initDriver();
    }

    protected function initDriver()
    {
        $driverClass = '\\Cursor\\Captcha\\Drivers\\' . ucfirst($this->config['driver']) . 'Driver';
        if (!class_exists($driverClass)) {
            throw new CaptchaException("Driver {$this->config['driver']} not found");
        }
        $this->driver = new $driverClass($this->config['stores'][$this->config['driver']] ?? []);
    }

    /**
     * 生成字母数字验证码
     */
    public function create()
    {
        $code = $this->generateCode();
        $image = $this->createImage($code);
        
        $key = uniqid('captcha_');
        $this->driver->set($key, $code, $this->config['config']['expire']);
        
        return [
            'key' => $key,
            'image' => $this->outputImage($image)
        ];
    }

    /**
     * 生成中文验证码
     */
    public function createChinese()
    {
        $characters = mb_str_split($this->config['config']['zhSet']);
        $code = '';
        for ($i = 0; $i < $this->config['config']['length']; $i++) {
            $code .= $characters[array_rand($characters)];
        }

        $image = $this->createImage($code, true);
        
        $key = uniqid('captcha_');
        $this->driver->set($key, $code, $this->config['config']['expire']);
        
        return [
            'key' => $key,
            'image' => $this->outputImage($image)
        ];
    }

    /**
     * 生成滑块验证码
     */
    public function createSlide()
    {
        $width = $this->config['config']['width'];
        $height = $this->config['config']['height'];
        $blockSize = $this->config['config']['slide']['block_size'];

        // 创建背景图
        $image = Image::canvas($width, $height);
        
        // 随机生成滑块位置
        $x = rand($blockSize, $width - $blockSize);
        $y = rand($blockSize, $height - $blockSize);

        // 绘制滑块
        $block = Image::canvas($blockSize, $blockSize);
        $block->circle($blockSize, $blockSize/2, $blockSize/2, function ($draw) {
            $draw->background('#000000');
        });

        // 将滑块叠加到背景图上
        $image->insert($block, 'top-left', $x, $y);

        $key = uniqid('captcha_');
        $this->driver->set($key, json_encode(['x' => $x, 'y' => $y]), $this->config['config']['expire']);

        return [
            'key' => $key,
            'image' => $this->outputImage($image),
            'block' => $this->outputImage($block)
        ];
    }

    /**
     * 生成旋转验证码
     */
    public function createRotate()
    {
        $code = $this->generateCode();
        $image = $this->createImage($code);
        
        // 随机旋转角度
        $angle = rand(
            $this->config['config']['rotate']['min_angle'],
            $this->config['config']['rotate']['max_angle']
        );
        
        $image->rotate($angle);
        
        $key = uniqid('captcha_');
        $this->driver->set($key, json_encode(['code' => $code, 'angle' => $angle]), $this->config['config']['expire']);
        
        return [
            'key' => $key,
            'image' => $this->outputImage($image)
        ];
    }

    /**
     * 验证验证码
     */
    public function verify($key, $code)
    {
        if (!$key || !$code) {
            return false;
        }

        $savedCode = $this->driver->get($key);
        if (!$savedCode) {
            return false;
        }

        $this->driver->delete($key);

        if ($this->config['config']['type'] === 'slide') {
            $savedData = json_decode($savedCode, true);
            $inputData = json_decode($code, true);
            return abs($savedData['x'] - $inputData['x']) <= $this->config['config']['slide']['offset'];
        }

        if ($this->config['config']['type'] === 'rotate') {
            $savedData = json_decode($savedCode, true);
            $inputData = json_decode($code, true);
            return $savedData['code'] === $inputData['code'] && 
                   abs($savedData['angle'] - $inputData['angle']) <= $this->config['config']['rotate']['offset'];
        }

        return strtolower($savedCode) === strtolower($code);
    }

    protected function generateCode()
    {
        $characters = str_split($this->config['config']['characters']);
        $code = '';
        for ($i = 0; $i < $this->config['config']['length']; $i++) {
            $code .= $characters[array_rand($characters)];
        }
        return $code;
    }

    protected function createImage($code, $isChinese = false)
    {
        $width = $this->config['config']['width'];
        $height = $this->config['config']['height'];
        
        $image = Image::canvas($width, $height, '#ffffff');
        
        // 添加干扰线
        if ($this->config['config']['useCurve']) {
            for ($i = 0; $i < 3; $i++) {
                $x1 = rand(0, $width);
                $y1 = rand(0, $height);
                $x2 = rand(0, $width);
                $y2 = rand(0, $height);
                
                $image->line($x1, $y1, $x2, $y2, function ($draw) {
                    $draw->color(rand(0, 255), rand(0, 255), rand(0, 255));
                });
            }
        }
        
        // 添加噪点
        if ($this->config['config']['useNoise']) {
            for ($i = 0; $i < 50; $i++) {
                $x = rand(0, $width);
                $y = rand(0, $height);
                $image->pixel(rand(0, 255), rand(0, 255), rand(0, 255), $x, $y);
            }
        }
        
        // 写入验证码
        $fontSize = $isChinese ? $height * 0.4 : $height * 0.5;
        $chars = $isChinese ? mb_str_split($code) : str_split($code);
        
        $x = ($width - (count($chars) * $fontSize)) / 2;
        foreach ($chars as $char) {
            $angle = rand(-10, 10);
            $y = $height / 2 + rand(-10, 10);
            
            $image->text($char, $x, $y, function ($font) use ($fontSize) {
                $font->file($this->config['config']['fontFile']);
                $font->size($fontSize);
                $font->color(rand(0, 100), rand(0, 100), rand(0, 100));
                $font->align('left');
                $font->valign('middle');
            });
            
            $x += $fontSize;
        }
        
        return $image;
    }

    protected function outputImage($image)
    {
        return $image->encode('data-url')->encoded;
    }
} 