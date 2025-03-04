<!-- @format -->

# Xinmiti Captcha

一个功能强大的验证码库，支持多种验证码类型和存储驱动。

## 特性

-   支持多种验证码类型：
    -   字母数字验证码
    -   中文验证码
    -   滑块验证码
    -   旋转图片验证码
-   支持多种存储驱动：
    -   File
    -   Redis
    -   Memcache
    -   Database
    -   Cookie
-   支持 ThinkPHP 6.0/8.0+
-   高度可配置
-   简单易用

## 安装

```bash
composer require xinmiti/captcha
```

## 配置

### ThinkPHP 配置

配置文件会自动发布到 `config/captcha.php`，你可以根据需要修改配置。

```php
return [
    // 验证码存储驱动
    'driver' => 'file',

    // 更多配置...
];
```

## 使用示例

### 基本使用

```php
use Xinmiti\Captcha\Captcha;

$captcha = new Captcha();

// 创建字母数字验证码
$result = $captcha->create();
// 返回: ['key' => '...', 'image' => 'data:image/png;base64,...']

// 创建中文验证码
$result = $captcha->createChinese();
// 返回: ['key' => '...', 'image' => 'data:image/png;base64,...']

// 创建滑块验证码
$result = $captcha->createSlide();
// 返回: ['key' => '...', 'image' => 'data:image/png;base64,...', 'block' => 'data:image/png;base64,...']

// 创建旋转验证码
$result = $captcha->createRotate();
// 返回: ['key' => '...', 'image' => 'data:image/png;base64,...']

// 验证
$isValid = $captcha->verify($key, $code);
```

### 在 ThinkPHP 中使用

```php
use think\facade\Captcha;

// 创建验证码
$result = Captcha::create();

// 验证
$isValid = Captcha::verify($key, $code);
```

## 自定义配置

你可以在实例化时传入自定义配置：

```php
$config = [
    'driver' => 'redis',
    'stores' => [
        'redis' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'password' => 'your-password',
            'database' => 0
        ]
    ],
    'config' => [
        'length' => 6,
        'expire' => 600,
        // 更多配置...
    ]
];

$captcha = new Captcha($config);
```

## 许可证

MIT
