<?php
require __DIR__ . '/vendor/autoload.php';

use Xinmiti\Captcha\Captcha;

// 创建验证码实例
$captcha = new Captcha([
    'driver' => 'file',
    'stores' => [
        'file' => [
            'path' => __DIR__ . '/runtime/captcha'
        ]
    ],
    'config' => [
        'fontFile' => __DIR__ . '/src/assets/fonts/zhttfs/1.ttf'
    ]
]);

// 测试不同类型的验证码
function testCaptcha($captcha) {
    // 1. 测试字母数字验证码
    $result = $captcha->create();
    echo "字母数字验证码:\n";
    echo "Key: " . $result['key'] . "\n";
    echo "Image: " . substr($result['image'], 0, 50) . "...\n\n";

    // 2. 测试中文验证码
    $result = $captcha->createChinese();
    echo "中文验证码:\n";
    echo "Key: " . $result['key'] . "\n";
    echo "Image: " . substr($result['image'], 0, 50) . "...\n\n";

    // 3. 测试滑块验证码
    $result = $captcha->createSlide();
    echo "滑块验证码:\n";
    echo "Key: " . $result['key'] . "\n";
    echo "Image: " . substr($result['image'], 0, 50) . "...\n";
    echo "Block: " . substr($result['block'], 0, 50) . "...\n\n";

    // 4. 测试旋转验证码
    $result = $captcha->createRotate();
    echo "旋转验证码:\n";
    echo "Key: " . $result['key'] . "\n";
    echo "Image: " . substr($result['image'], 0, 50) . "...\n\n";
}

// 运行测试
testCaptcha($captcha); 