<?php
namespace Xinmiti\Captcha;

use Intervention\Image\ImageManagerStatic as Image;
use Xinmiti\Captcha\Drivers\DriverInterface;
use Xinmiti\Captcha\Exceptions\CaptchaException;
use Xinmiti\Captcha\Utils\ShapeDrawer;

class Captcha
{
    protected $config;
    protected $driver;

    public function __construct(array $config = [])
    {
        // 切换到 Imagick 驱动
        Image::configure(['driver' => 'imagick']);
        
        // 加载默认配置
        $defaultConfig = include __DIR__ . '/config/config.php';
        
        // 深度合并配置
        $this->config = array_replace_recursive($defaultConfig, $config);
        
        $this->initDriver();
    }

    /**
     * 设置验证码类型
     */
    public function setType($type)
    {
        $this->config['config']['type'] = $type;
        return $this;
    }

    protected function initDriver()
    {
        $driverClass = '\\Xinmiti\\Captcha\\Drivers\\' . ucfirst($this->config['driver']) . 'Driver';
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
        
        // 获取随机背景图
        $bgFiles = glob($this->config['config']['bgPath'] . '/*.jpg');
        if (empty($bgFiles)) {
            throw new CaptchaException('No background images found');
        }
        $bgFile = $bgFiles[array_rand($bgFiles)];
        
        // 创建背景图
        $image = Image::make($bgFile)->resize($width, $height);
        
        // 随机选择图形
        $shapes = $this->config['config']['slide']['shapes'];
        $shape = $shapes[array_rand($shapes)];
        
        // 随机生成滑块位置（x和y坐标都随机，但保持在合理范围内）
        $x = rand($blockSize, $width - $blockSize * 2);
        $y = rand($blockSize + 10, $height - $blockSize - 10); // 确保不会太靠近边缘
        
        // 创建透明背景的滑块图形
        $block = Image::canvas($blockSize, $blockSize)->opacity(0);
        ShapeDrawer::drawShape($block, $shape, 0, 0, $blockSize, '#000000');
        
        // 添加阴影和模糊效果
        $block->blur(1);
        $block->brightness(-10);
        $block->opacity(90);
        
        // 在背景图上绘制图形轮廓并添加外阴影
        $shadowImage = Image::canvas($blockSize, $blockSize)->opacity(0);
        ShapeDrawer::drawShape($shadowImage, $shape, 0, 0, $blockSize, '#ffffff');
        $shadowImage->blur(3);
        $image->insert($shadowImage, 'top-left', $x, $y);
        ShapeDrawer::drawShape($image, $shape, $x, $y, $blockSize, '#ffffff');
        
        $key = uniqid('captcha_');
        $this->driver->set($key, json_encode(['x' => $x, 'y' => $y]), $this->config['config']['expire']);
        
        return [
            'key' => $key,
            'image' => $this->outputImage($image),
            'block' => $this->outputImage($block),
            'y' => $y
        ];
    }

    /**
     * 生成旋转验证码
     */
    public function createRotate()
    {
        $width = $this->config['config']['width'];
        $height = $this->config['config']['height'];
        $blockSize = $this->config['config']['rotate']['block_size'];
        
        // 获取随机背景图
        $bgFiles = glob($this->config['config']['bgPath'] . '/*.jpg');
        if (empty($bgFiles)) {
            throw new CaptchaException('No background images found');
        }
        $bgFile = $bgFiles[array_rand($bgFiles)];
        
        // 创建背景图
        $image = Image::make($bgFile)->resize($width, $height);
        
        // 随机选择图形（移除菱形，添加拼图形状）
        $shapes = ['triangle', 'puzzle', 'heart'];
        $shape = $shapes[array_rand($shapes)];
        
        // 随机生成图形位置（居中）
        $x = ($width - $blockSize) / 2;
        $y = ($height - $blockSize) / 2;
        
        // 创建透明背景的旋转图形
        $block = Image::canvas($blockSize, $blockSize)->opacity(0);
        ShapeDrawer::drawShape($block, $shape, 0, 0, $blockSize, '#000000');
        
        // 添加阴影和模糊效果
        $block->blur(1);
        $block->brightness(-10);
        $block->opacity(90);
        
        // 随机旋转角度（与背景图形不同）
        $bgAngle = rand(0, 360);
        $blockAngle = ($bgAngle + rand(60, 300)) % 360;
        
        // 在背景图上绘制原始图形
        $bgBlock = clone $block;
        $bgBlock->rotate($bgAngle);
        $image->insert($bgBlock, 'top-left', $x, $y);
        
        // 旋转滑块图形
        $block->rotate($blockAngle);
        
        $key = uniqid('captcha_');
        $this->driver->set($key, json_encode([
            'bgAngle' => $bgAngle,
            'blockAngle' => $blockAngle
        ]), $this->config['config']['expire']);
        
        return [
            'key' => $key,
            'image' => $this->outputImage($image),
            'block' => $this->outputImage($block)
        ];
    }

    /**
     * 生成点击文字验证码
     */
    public function createClick()
    {
        $width = $this->config['config']['width'];
        $height = $this->config['config']['height'];
        
        // 获取随机背景图
        $bgFiles = glob($this->config['config']['bgPath'] . '/*.jpg');
        if (empty($bgFiles)) {
            throw new CaptchaException('No background images found');
        }
        $bgFile = $bgFiles[array_rand($bgFiles)];
        
        // 创建背景图
        $image = Image::make($bgFile)->resize($width, $height);
        
        // 生成随机文字
        $wordCount = $this->config['config']['click']['word_count'];
        $words = [];
        $positions = [];
        $usedAreas = []; // 用于记录已使用的区域
        
        // 将画布分成四个区域
        $padding = 40;
        $areaWidth = ($width - $padding * 2) / 2;
        $areaHeight = ($height - $padding * 2) / 2;
        $areas = [
            ['x' => $padding, 'y' => $padding],
            ['x' => $padding + $areaWidth, 'y' => $padding],
            ['x' => $padding, 'y' => $padding + $areaHeight],
            ['x' => $padding + $areaWidth, 'y' => $padding + $areaHeight]
        ];
        
        for ($i = 0; $i < $wordCount; $i++) {
            // 随机选择文字
            $word = mb_substr($this->config['config']['zhSet'], rand(0, mb_strlen($this->config['config']['zhSet']) - 1), 1);
            $words[] = $word;
            
            // 在当前区域内随机位置
            $area = $areas[$i];
            $fontSize = rand($this->config['config']['click']['font_size'][0], $this->config['config']['click']['font_size'][1]);
            
            // 确保文字在区域内且不重叠
            $maxAttempts = 10;
            $found = false;
            while ($maxAttempts > 0) {
                $x = $area['x'] + rand($fontSize, $areaWidth - $fontSize);
                $y = $area['y'] + rand($fontSize, $areaHeight - $fontSize);
                
                // 检查是否与已有文字重叠
                $overlap = false;
                foreach ($usedAreas as $used) {
                    $distance = sqrt(pow($x - $used['x'], 2) + pow($y - $used['y'], 2));
                    if ($distance < $fontSize * 1.5) { // 设置最小间距
                        $overlap = true;
                        break;
                    }
                }
                
                if (!$overlap) {
                    $found = true;
                    $usedAreas[] = ['x' => $x, 'y' => $y, 'size' => $fontSize];
                    break;
                }
                
                $maxAttempts--;
            }
            
            if (!$found) {
                // 如果找不到合适位置，使用区域中心点
                $x = $area['x'] + $areaWidth / 2;
                $y = $area['y'] + $areaHeight / 2;
            }
            
            // 随机颜色
            $color = $this->config['config']['click']['colors'][array_rand($this->config['config']['click']['colors'])];
            
            // 随机旋转角度
            $angle = rand($this->config['config']['click']['twist'][0], $this->config['config']['click']['twist'][1]);
            
            // 记录位置信息
            $positions[] = [
                'x' => $x,
                'y' => $y,
                'size' => $fontSize
            ];
            
            // 绘制文字边框（白色描边效果）
            for ($offset = -2; $offset <= 2; $offset++) {
                $image->text($word, $x + $offset, $y, function ($font) use ($fontSize, $angle) {
                    $font->file($this->config['config']['fontFile']);
                    $font->size($fontSize + 2);
                    $font->color('#ffffff');
                    $font->angle($angle);
                });
                $image->text($word, $x, $y + $offset, function ($font) use ($fontSize, $angle) {
                    $font->file($this->config['config']['fontFile']);
                    $font->size($fontSize + 2);
                    $font->color('#ffffff');
                    $font->angle($angle);
                });
            }
            
            // 绘制文字
            $image->text($word, $x, $y, function ($font) use ($fontSize, $color, $angle) {
                $font->file($this->config['config']['fontFile']);
                $font->size($fontSize);
                $font->color($color);
                $font->angle($angle);
            });
        }
        
        $key = uniqid('captcha_');
        $this->driver->set($key, json_encode([
            'words' => $words,
            'positions' => $positions
        ]), $this->config['config']['expire']);
        
        // 创建验证码图片
        $codeConfig = $this->config['config']['click']['code_image'];
        $codeImage = Image::canvas($codeConfig['width'], $codeConfig['height'], '#ffffff');

        // 添加干扰线
        if ($codeConfig['use_curve']) {
            for ($i = 0; $i < $codeConfig['curve_number']; $i++) {
                $x1 = rand(0, $codeConfig['width']);
                $y1 = rand(0, $codeConfig['height']);
                $x2 = rand(0, $codeConfig['width']);
                $y2 = rand(0, $codeConfig['height']);
                
                $codeImage->line($x1, $y1, $x2, $y2, function ($draw) {
                    $draw->color(rand(0, 255), rand(0, 255), rand(0, 255));
                });
            }
        }

        // 添加噪点
        if ($codeConfig['use_noise']) {
            for ($i = 0; $i < $codeConfig['noise_number']; $i++) {
                $x = rand(0, $codeConfig['width']);
                $y = rand(0, $codeConfig['height']);
                $color = sprintf('#%02x%02x%02x', rand(0, 255), rand(0, 255), rand(0, 255));
                $codeImage->pixel($color, $x, $y);
            }
        }

        $codeX = 10;
        foreach ($words as $word) {
            $codeImage->text($word, $codeX, 30, function ($font) use ($codeConfig) {
                $font->file($this->config['config']['fontFile']);
                $font->size($codeConfig['font_size']);
                $font->color('#000000');
            });
            $codeX += $codeConfig['font_size'] + 10;
        }
        
        return [
            'key' => $key,
            'image' => $this->outputImage($image),
            'code_image' => $this->outputImage($codeImage)
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

        switch ($this->config['config']['type']) {
            case 'slide':
                $savedData = json_decode($savedCode, true);
                $inputData = json_decode($code, true);
                return abs($savedData['x'] - $inputData['x']) <= $this->config['config']['slide']['offset'];
                
            case 'rotate':
                return $this->verifyRotate($savedCode, $code);
                
            case 'click':
                return $this->verifyClick($savedCode, $code);
                
            default:
                return strtolower($savedCode) === strtolower($code);
        }
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
                $color = sprintf('#%02x%02x%02x', rand(0, 255), rand(0, 255), rand(0, 255));
                $image->pixel($color, $x, $y);
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
                $font->color(sprintf('#%02x%02x%02x', rand(0, 100), rand(0, 100), rand(0, 100)));
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

    /**
     * 验证旋转角度
     */
    protected function verifyRotate($savedCode, $inputCode)
    {
        $savedData = json_decode($savedCode, true);
        $inputData = json_decode($inputCode, true);
        
        // 计算需要旋转的角度（将输入角度旋转到背景角度）
        $targetAngle = $savedData['bgAngle'];
        $currentAngle = $inputData['angle'] ?? 0;
        
        // 计算角度差（考虑360度循环）
        $angleDiff = abs($targetAngle - $currentAngle);
        $angleDiff = min($angleDiff, 360 - $angleDiff);
        
        return $angleDiff <= $this->config['config']['rotate']['offset'];
    }

    /**
     * 验证点击位置
     */
    protected function verifyClick($savedCode, $inputCode)
    {
        $savedData = json_decode($savedCode, true);
        $inputData = json_decode($inputCode, true);
        
        if (count($savedData['words']) !== count($inputData)) {
            return false;
        }
        
        // 使用配置中的公差值
        $tolerance = $this->config['config']['click']['tolerance'];
        
        foreach ($inputData as $i => $click) {
            $pos = $savedData['positions'][$i];
            $distance = sqrt(pow($click['x'] - $pos['x'], 2) + pow($click['y'] - $pos['y'], 2));
            if ($distance > $tolerance) {
                return false;
            }
        }
        
        return true;
    }
} 