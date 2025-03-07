<?php
namespace Xinmiti\Captcha\Utils;

use Intervention\Image\Image;

class ShapeDrawer
{
    /**
     * 使用贝塞尔曲线绘制三角形
     */
    public static function drawTriangle(Image $image, $x, $y, $size, $color)
    {
        $points = [
            ['M', $x, $y + $size],                    // 起点（左下角）
            ['C', $x + $size/4, $y + $size*0.8,       // 左边控制点
                  $x + $size/2, $y + $size*0.2,       // 顶点控制点
                  $x + $size/2, $y],                  // 顶点
            ['C', $x + $size/2, $y + $size*0.2,       // 顶点控制点
                  $x + $size*0.75, $y + $size*0.8,    // 右边控制点
                  $x + $size, $y + $size],            // 右下角
            ['L', $x, $y + $size]                     // 回到起点
        ];
        
        self::drawBezierPath($image, $points, $color);
    }

    /**
     * 使用贝塞尔曲线绘制菱形
     */
    public static function drawDiamond(Image $image, $x, $y, $size, $color)
    {
        $points = [
            ['M', $x + $size/2, $y],                  // 起点（顶点）
            ['C', $x + $size*0.7, $y + $size*0.3,     // 右上控制点1
                  $x + $size*0.9, $y + $size*0.4,     // 右上控制点2
                  $x + $size, $y + $size/2],          // 右点
            ['C', $x + $size*0.9, $y + $size*0.6,     // 右下控制点1
                  $x + $size*0.7, $y + $size*0.7,     // 右下控制点2
                  $x + $size/2, $y + $size],          // 底点
            ['C', $x + $size*0.3, $y + $size*0.7,     // 左下控制点1
                  $x + $size*0.1, $y + $size*0.6,     // 左下控制点2
                  $x, $y + $size/2],                  // 左点
            ['C', $x + $size*0.1, $y + $size*0.4,     // 左上控制点1
                  $x + $size*0.3, $y + $size*0.3,     // 左上控制点2
                  $x + $size/2, $y]                   // 回到起点
        ];
        
        self::drawBezierPath($image, $points, $color);
    }

    /**
     * 使用贝塞尔曲线绘制心形
     */
    public static function drawHeart(Image $image, $x, $y, $size, $color)
    {
        $points = [
            ['M', $x + $size/2, $y + $size],          // 起点（底部中心）
            ['C', $x + $size*0.1, $y + $size*0.7,     // 左下控制点1
                  $x, $y + $size*0.5,                 // 左下控制点2
                  $x, $y + $size*0.3],                // 左边点
            ['C', $x, $y + $size*0.1,                 // 左上控制点1
                  $x + $size*0.3, $y,                 // 左上控制点2
                  $x + $size/2, $y + $size*0.3],      // 顶部中心
            ['C', $x + $size*0.7, $y,                 // 右上控制点1
                  $x + $size, $y + $size*0.1,         // 右上控制点2
                  $x + $size, $y + $size*0.3],        // 右边点
            ['C', $x + $size, $y + $size*0.5,         // 右下控制点1
                  $x + $size*0.9, $y + $size*0.7,     // 右下控制点2
                  $x + $size/2, $y + $size]           // 回到起点
        ];
        
        self::drawBezierPath($image, $points, $color);
    }

    /**
     * 使用贝塞尔曲线绘制拼图形状
     */
    public static function drawPuzzle(Image $image, $x, $y, $size, $color)
    {
        $points = [
            ['M', $x, $y + $size*0.3],                // 起点
            ['C', $x - $size*0.1, $y + $size*0.3,     // 左凸起控制点1
                  $x - $size*0.1, $y + $size*0.5,     // 左凸起控制点2
                  $x, $y + $size*0.5],                // 左凸起结束点
            ['L', $x, $y + $size],                    // 左下角
            ['L', $x + $size*0.3, $y + $size],        // 底边开始
            ['C', $x + $size*0.5, $y + $size*1.1,     // 底部凸起控制点1
                  $x + $size*0.7, $y + $size*1.1,     // 底部凸起控制点2
                  $x + $size*0.7, $y + $size],        // 底部凸起结束点
            ['L', $x + $size, $y + $size],            // 右下角
            ['L', $x + $size, $y + $size*0.7],        // 右边开始
            ['C', $x + $size*1.1, $y + $size*0.5,     // 右凸起控制点1
                  $x + $size*1.1, $y + $size*0.3,     // 右凸起控制点2
                  $x + $size, $y + $size*0.3],        // 右凸起结束点
            ['L', $x + $size, $y],                    // 右上角
            ['L', $x + $size*0.7, $y],                // 顶边开始
            ['C', $x + $size*0.5, $y - $size*0.1,     // 顶部凸起控制点1
                  $x + $size*0.3, $y - $size*0.1,     // 顶部凸起控制点2
                  $x + $size*0.3, $y],                // 顶部凸起结束点
            ['L', $x, $y],                            // 左上角
            ['L', $x, $y + $size*0.3]                 // 回到起点
        ];
        
        self::drawBezierPath($image, $points, $color);
    }

    /**
     * 绘制贝塞尔曲线路径
     */
    protected static function drawBezierPath(Image $image, array $points, $color)
    {
        // 创建SVG路径
        $path = '';
        foreach ($points as $point) {
            $command = array_shift($point);
            $path .= $command . ' ' . implode(' ', $point) . ' ';
        }
        
        // 创建一个新的画布
        $canvas = clone $image;
        $canvas->fill($color);
        
        // 绘制路径
        $canvas->path($path);
        
        // 将路径应用到原图
        $image->insert($canvas, 'top-left', 0, 0);
        
        // 应用平滑效果
        $image->blur(1);
    }

    /**
     * 根据形状名称绘制对应图形
     */
    public static function drawShape(Image $image, $shape, $x, $y, $size, $color)
    {
        switch ($shape) {
            case 'triangle':
                self::drawTriangle($image, $x, $y, $size, $color);
                break;
            case 'diamond':
                self::drawDiamond($image, $x, $y, $size, $color);
                break;
            case 'heart':
                self::drawHeart($image, $x, $y, $size, $color);
                break;
            case 'puzzle':
                self::drawPuzzle($image, $x, $y, $size, $color);
                break;
        }
    }
} 