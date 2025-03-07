<?php
require __DIR__ . '/vendor/autoload.php';

use Xinmiti\Captcha\Captcha;

// 允许跨域请求
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// 创建验证码实例
$captcha = new Captcha([
    'driver' => 'file',
    'stores' => [
        'file' => [
            'path' => __DIR__ . '/runtime/captcha'
        ]
    ],
    'config' => [
        'fontFile' => __DIR__ . '/src/assets/fonts/zhttfs/1.ttf',
        'bgPath' => __DIR__ . '/src/assets/bg'
    ]
]);

$action = $_GET['action'] ?? '';
$type = (int)($_GET['type'] ?? 1);

switch ($action) {
    case 'create':
        switch ($type) {
            case 1:
                $result = $captcha->create();
                break;
            case 2:
                $result = $captcha->createChinese();
                break;
            case 3:
                $captcha->setType('slide');
                $result = $captcha->createSlide();
                break;
            case 4:
                $captcha->setType('rotate');
                $result = $captcha->createRotate();
                break;
            case 5:
                $captcha->setType('click');
                $result = $captcha->createClick();
                break;
            default:
                $result = $captcha->create();
        }
        echo json_encode($result);
        break;

    case 'verify':
        $key = $_GET['key'] ?? '';
        $code = $_GET['code'] ?? '';
        
        switch ($type) {
            case 3:
                $captcha->setType('slide');
                break;
            case 4:
                $captcha->setType('rotate');
                break;
            case 5:
                $captcha->setType('click');
                break;
        }
        
        $success = $captcha->verify($key, $code);
        echo json_encode(['success' => $success]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
} 