<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context Origin图形封装
 */
namespace Origin\Package;

class Graph
{
    /**
     * @access protected
     * @var resource $Canvas 画布
     */
    protected $Canvas;

    /**
     * @access protected
     * @var int $CanvasWidth 画布宽度
     */
    protected $CanvasWidth;

    /**
     * @access protected
     * @var int $CanvasHeight 画布高度
     */
    protected $CanvasHeight;

    /**
     * @access protected
     * @var false|int|resource $Color 主背景色
     */
    protected $Color;

    /**
     * @access protected
     * @var string $Font 字体
     */
    protected $Font;

    /**
     * @access protected
     * @var int $FontSzie 字体大小
     */
    protected $FontSize;

    /**
     * @access protected
     * @var false|int|resource $FontColor 字体颜色
     */
    protected $FontColor;

    /**
     * 设置画布
     * @access public
     * @param int $width 画布宽度
     * @param int $height 画布高度
     * @param boolean $true 是否使用真彩创建
     * @return void
     */
    function Canvas($width, $height, $true=false)
    {
        $this->CanvasWidth = $width;
        $this->CanvasHeight = $height;
        if($true)
            $this->Canvas = imagecreatetruecolor($width, $height);
        else
            $this->Canvas = imagecreate($width, $height);
        $this->Color = imagecolorallocate($this->Canvas, 255, 255, 255);
        $this->Font = replace(ROOT . "/resource/font/origin001.ttf");
        $this->FontSize = 10;
        $this->FontColor = imagecolorallocate($this->Canvas, 0, 0, 0);
    }

    /**
     * 设置画布背景色
     * @access public
     * @param int $red 设置色偏值 红（0,225）默认值 225
     * @param int $green 设置色偏值 绿（0,225）默认值 225
     * @param int $blue 设置色偏值 蓝（0,225）默认值 225
     * @return void
     */
    function setBgColor($red = 255, $green = 255, $blue = 255)
    {
        $this->Color = imagecolorallocate($this->Canvas, $red, $green, $blue);
    }

    /**
     * 设置字体
     * @access public
     * @param string $uri 设置字体文件路径
     * @return void
     */
    function setFont($uri)
    {
        if (is_file($_uri = replace(ROOT . DS . $uri)))
            $this->Font = $_uri;
    }

    /**
     * 设置字体大小
     * @access public
     * @param int $size 设置字体显示大小
     * @return void
     */
    function setFontSize($size)
    {
        if ($size > 0)
            $this->FontSize = intval($size);
    }

    /**
     * @access public
     * @param int $red 设置色偏值 红（0,225）默认值 225
     * @param int $green 设置色偏值 绿（0,225）默认值 225
     * @param int $blue 设置色偏值 蓝（0,225）默认值 225
     * @context 设置字体颜色
     */
    function setFontColor($red = 255, $green = 255, $blue = 255)
    {
        $this->FontColor = imagecolorallocate($this->Canvas, $red, $green, $blue);
    }

    /**
     * 引入文字
     * @access public
     * @param string $text
     * @param int $point_x 坐标轴x，默认值 0
     * @param int $point_y 坐标轴y，默认值 0
     * @param int|float $angle 旋转角度（0-90度） 默认值 0
     * @return void
     */
    function imText($text, $point_x = 0, $point_y = 0, $angle = 0)
    {
        imagefttext($this->Canvas, $this->FontSize, $angle, $point_x, $point_y, $this->FontColor, $this->Font, $text);
    }

    /**
     * 引入图片
     * @access public
     * @param string $uri 图片地址（相对地址）
     * @param int $point_x 坐标轴x
     * @param int $point_y 坐标轴y
     * @param int $percent 缩小比例，相对于画布大小
     * @return boolean 返回引用状态值
     */
    function imPic($uri, $point_x = 0, $point_y = 0, $percent = 100)
    {
        $_receipt = false;
        list($_width, $_height) = getimagesize($uri);
        if (is_file($_uri = replace(ROOT . DS . $uri))) {
            # 设置默认图片类型
            $_type = "jpg";
            if (strrpos(".", $uri)) {
                $_type = strtolower(substr($uri, strrpos(".", $uri) + 1));
            }
            switch ($_type) {
                case "png":
                    $_pic = imagecreatefrompng($_uri);
                    break;
                case "bmp":
                    $_pic = imagecreatefrombmp($_uri);
                    break;
                case "gif":
                    $_pic = imagecreatefromgif($_uri);
                    break;
                case "gd":
                    $_pic = imagecreatefromgd($_uri);
                    break;
                case "gd2":
                    $_pic = imagecreatefromgd2($_uri);
                    break;
                case "wbmp":
                    $_pic = imagecreatefromwbmp($_uri);
                    break;
                case "webp":
                    $_pic = imagecreatefromwebp($_uri);
                    break;
                case "xbm":
                    $_pic = imagecreatefromxbm($_uri);
                    break;
                case "xpm":
                    $_pic = imagecreatefromxpm($_uri);
                    break;
                case "jpeg":
                case "jpg":
                default:
                    $_pic = imagecreatefromjpeg($_uri);
                    break;
            }
            if (isset($_pic))
                $_receipt = imagecopyresized($this->Canvas, $_pic, $point_x, $point_y = 0, 0, 0, intval($_width * $percent / 100), intval($_height * $percent / 100), $_width, $_height);
        }
        return $_receipt;
    }

    /**
     * 设置填充颜色
     * @access public
     * @param int $red 设置色偏值 红（0,225）默认值 225
     * @param int $green 设置色偏值 绿（0,225）默认值 225
     * @param int $blue 设置色偏值 蓝（0,225）默认值 225
     * @return false|int 返回结果值或失败状态
     */
    function setColor($red = 255, $green = 255, $blue = 255)
    {
        return imagecolorallocate($this->Canvas, $red, $green, $blue);
    }

    /**
     * 画圆
     * @access public
     * @param int $point_x 定位坐标x，默认值 0
     * @param int $point_y 定位坐标y，默认值 0
     * @param int|float $radius 圆半径,初始值 5
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @param int $type 填充状态，默认值 0 不填充
     * 1.IMG_ARC_PIE 只用直线连接了起始和结束点
     * 2.IMG_ARC_CHORD 产生圆形边界，IMG_ARC_PIE 和 IMG_ARC_CHORD 是互斥的
     * 3.IMG_ARC_NOFILL 指明弧或弦只有轮廓，不填充
     * 4.MG_ARC_EDGED 指明用直线将起始和结束点与中心点相连，和 IMG_ARC_NOFILL 一起使用是画饼状图轮廓的好方法（而不用填充）
     * @return boolean 返回执行状态值
     */
    function circle($point_x = 0, $point_y = 0, $radius = 5, $color = null, $type = 0)
    {
        return $this->arc($point_x, $point_y, $radius, $radius, 0, 360, $color);
    }

    /**
     * 画弧
     * @access public
     * @param int $point_x 定位坐标x，默认值 0
     * @param int $point_y 定位坐标y，默认值 0
     * @param int $width 弧宽,默认值 5
     * @param int $height 弧高，默认值 5
     * @param int $start 起始角度，默认值 0
     * @param int $end 结束角度，默认值 360
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @param int $type 填充状态，默认值 0 不填充
     * 1.IMG_ARC_PIE 只用直线连接了起始和结束点
     * 2.IMG_ARC_CHORD 产生圆形边界，IMG_ARC_PIE 和 IMG_ARC_CHORD 是互斥的
     * 3.IMG_ARC_NOFILL 指明弧或弦只有轮廓，不填充
     * 4.MG_ARC_EDGED 指明用直线将起始和结束点与中心点相连，和 IMG_ARC_NOFILL 一起使用是画饼状图轮廓的好方法（而不用填充）
     * @return boolean 返回执行状态值
     */
    function arc($point_x = 0, $point_y = 0, $width = 5, $height = 5, $start = 0, $end = 360, $color = null, $type = 0)
    {
        $_color = $this->Color;
        if (!is_null($color) and (is_resource($color) or is_int($color)))
            $_color = $color;
        if ($type > 0)
            return imagefilledarc($this->Canvas, $point_x, $point_y, $width, $height, $start, $end, $_color, $type);
        else
            return imagearc($this->Canvas, $point_x, $point_y, $width, $height, $start, $end, $_color);
    }

    /**
     * 画椭圆
     * @access public
     * @param int $point_x 定位坐标x，默认值 0
     * @param int $point_y 定位坐标y，默认值 0
     * @param int $width 宽,默认值 5
     * @param int $height 高，默认值 5
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @param int $type 填充状态，默认值 0 不填充，1 填充
     * @return boolean 返回执行状态值
     */
    function ellipse($point_x = 0, $point_y = 0, $width = 5, $height = 5, $color = null, $type = 0)
    {
        $_color = $this->Color;
        if (!is_null($color) and (is_resource($color) or is_int($color)))
            $_color = $color;
        if ($type > 0)
            return imagefilledellipse($this->Canvas, $point_x, $point_y, $width, $height, $_color);
        else
            return imageellipse($this->Canvas, $point_x, $point_y, $width, $height, $_color);
    }

    /**
     * 画多边形
     * @access public
     * @param array $points 坐标信息数组 array(
     *     array($point_x,$point_y),
     * )
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @param int $type 填充状态，默认值 0 不填充，1 填充，2 非闭合多边划线
     * @return boolean 返回执行状态值
     */
    function polygon($points, $color = null, $type = 0)
    {
        $_color = $this->Color;
        if (!is_null($color) and (is_resource($color) or is_int($color)))
            $_color = $color;
        if ($type === 1)
            return imagefilledpolygon($this->Canvas, $points, count($points), $_color);
        elseif ($type === 2)
            return imageopenpolygon($this->Canvas, $points, count($points), $_color);
        else
            return imagepolygon($this->Canvas, $points, count($points), $_color);
    }

    /**
     * 画正方形
     * @access public
     * @param int $point_x 定位坐标x，默认值 0
     * @param int $point_y 定位坐标y，默认值 0
     * @param int $long 边长，初始值 5
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @param int $type 填充状态，默认值 0 不填充，1 填充
     * @return boolean 返回执行状态值
     */
    function square($point_x = 0, $point_y = 0, $long = 5, $color = null, $type = 0)
    {
        return $this->rectangle($point_x = 0, $point_y = 0, $long = 5, $long = 5, $color = null, $type = 0);
    }

    /**
     * 画矩形
     * @access public
     * @param int $point_x 定位坐标x，默认值 0
     * @param int $point_y 定位坐标y，默认值 0
     * @param int $width 宽 初始值 5
     * @param int $height 高 初始值 5
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @param int $type 填充状态，默认值 0 不填充，1 填充
     * @return boolean 返回执行状态值
     */
    function rectangle($point_x = 0, $point_y = 0, $width = 5, $height = 5, $color = null, $type = 0)
    {
        $_color = $this->Color;
        if (!is_null($color) and (is_resource($color) or is_int($color)))
            $_color = $color;
        if ($type > 0)
            return imagefilledrectangle($this->Canvas, $point_x, $point_y, $width, $height, $_color);
        else
            return imagerectangle($this->Canvas, $point_x, $point_y, $width, $height, $_color);
    }

    /**
     * 画直线
     * @access public
     * @param int $start_x 定位坐标x，默认值 0
     * @param int $start_y 定位坐标y，默认值 0
     * @param int $end_x 定位坐标x，默认值 5
     * @param int $end_y 定位坐标y，默认值 5
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @return boolean 返回执行状态值
     */
    function line($start_x = 0, $start_y = 0, $end_x = 5, $end_y = 5, $color = null)
    {
        $_color = $this->Color;
        if (!is_null($color) and (is_resource($color) or is_int($color)))
            $_color = $color;
        return imageline($this->Canvas, $start_x, $start_y, $end_x, $end_y, $_color);
    }

    /**
     * 画虚线，需要画布创建为真彩
     * @access public
     * @param array $style
     * @param int $start_x 定位坐标x，默认值 0
     * @param int $start_y 定位坐标y，默认值 0
     * @param int $end_x 定位坐标x，默认值 5
     * @param int $end_y 定位坐标y，默认值 5
     * @return boolean 返回执行状态值
    */
    function dotted($style, $start_x = 0, $start_y = 0, $end_x = 5, $end_y = 5)
    {
        if(imagesetstyle($this->Canvas,$style))
            return imageline($this->Canvas, 0, 0, 100, 100, IMG_COLOR_STYLED);
        else
            return false;
    }

    /**
     * 画字符串
     * @access public
     * @param string $str 输入字符串
     * @param int $font 字体参数 默认值 1 （1-5）为内部字体
     * @param int $type 排列方向 0 横向，1 竖向
     * @param int $point_x 定位坐标x，默认值 0
     * @param int $point_y 定位坐标y，默认值 0
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @return boolean 返回执行状态值
     */
    function string($str, $font = 1, $type = 0, $point_x = 0, $point_y = 0, $color = null)
    {
        $_color = $this->Color;
        if (!is_null($color) and (is_resource($color) or is_int($color)))
            $_color = $color;
        if ($type > 0)
            return imagestring($this->Canvas, $font, $point_x, $point_y, $str, $_color);
        else
            return imagestringup($this->Canvas, $font, $point_x, $point_y, $str, $_color);
    }

    /**
     * 画字符
     * @access public
     * @param string $str 输入字符串
     * @param int $font 字体参数 默认值 1 （1-5）为内部字体
     * @param int $type 排列方向 0 横向，1 竖向
     * @param int $point_x 定位坐标x，默认值 0
     * @param int $point_y 定位坐标y，默认值 0
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @return boolean 返回执行状态值
     */
    function char($str, $font = 1, $type = 0, $point_x = 0, $point_y = 0, $color = null)
    {
        $_color = $this->Color;
        if (!is_null($color) and (is_resource($color) or is_int($color)))
            $_color = $color;
        if ($type > 0)
            return imagechar($this->Canvas, $font, $point_x, $point_y, $str, $_color);
        else
            return imagecharup($this->Canvas, $font, $point_x, $point_y, $str, $_color);
    }

    /**
     * 画像素
     * @access public
     * @param int $point_x 定位坐标x，默认值 0
     * @param int $point_y 定位坐标y，默认值 0
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @return boolean 返回执行状态值
     */
    function pixel($point_x = 0, $point_y = 0, $color = null)
    {
        $_color = $this->Color;
        if (!is_null($color) and (is_resource($color) or is_int($color)))
            $_color = $color;
        return imagesetpixel($this->Canvas, $point_x, $point_y, $_color);
    }

    /**
     * 图片渲染（反转）
     * @access public
     * @param resource $pic 引入图片源 imPic返回值
     * @param int $type 反转类型
     * IMG_FILTER_NEGATE：将图像中所有颜色反转。
     * IMG_FILTER_GRAYSCALE：将图像转换为灰度的。
     * IMG_FILTER_BRIGHTNESS：改变图像的亮度。用 arg1 设定亮度级别。
     * IMG_FILTER_CONTRAST：改变图像的对比度。用 arg1 设定对比度级别。
     * IMG_FILTER_COLORIZE：与 IMG_FILTER_GRAYSCALE 类似，不过可以指定颜色。用 arg1，arg2 和 arg3 分别指定 red，blue 和 green。每种颜色范围是 0 到 255。
     * IMG_FILTER_EDGEDETECT：用边缘检测来突出图像的边缘。
     * IMG_FILTER_EMBOSS：使图像浮雕化。
     * IMG_FILTER_GAUSSIAN_BLUR：用高斯算法模糊图像。
     * IMG_FILTER_SELECTIVE_BLUR：模糊图像。
     * IMG_FILTER_MEAN_REMOVAL：用平均移除法来达到轮廓效果。
     * IMG_FILTER_SMOOTH：使图像更柔滑。用 arg1 设定柔滑级别。
     * @return boolean 返回执行状态值
    */
    function filter($pic,$type)
    {
        return imagefilter($pic,$type);
    }

    /**
     * 图片截取
     * @access public
     * @param resource $pic 引入图片源 imPic返回值
     * @param int $start_x 定位坐标x，默认值 0
     * @param int $start_y 定位坐标y，默认值 0
     * @param int $width 宽，默认值 5
     * @param int $height 高，默认值 5
     * @param int $canvas_x 图像显示位置坐标x，默认值 0
     * @param int $canvas_y 图像显示位置坐标y，默认值 0
     * @return boolean 返回执行状态值
    */
    function cut($pic, $start_x=0, $start_y=0, $width=5, $height=5, $canvas_x=0, $canvas_y=0)
    {
        return imagecopy($this->Canvas,$pic,$start_x,$start_y,$width,$height,$canvas_x,$canvas_y);
    }

    /**
     * 旋转
     * @access public
     * @param resource $pic 引入图片源 imPic返回值
     * @param float $angle 旋转角度 默认值 0.0
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @param int $transparent 是否支持透明默认值 0 不支持，1支持
     * @return boolean 返回执行状态值
    */
    function rotate($pic, $angle=0.0, $color=null, $transparent=0)
    {
        $_color = $this->Color;
        if (!is_null($color) and (is_resource($color) or is_int($color)))
            $_color = $color;
        return imagerotate($pic,$angle,$_color,$transparent);
    }

    /**
     * 设置空间填充
     * @access public
     * @param int $point_x 定位坐标x，默认值 0
     * @param int $point_y 定位坐标y，默认值 0
     * @return boolean 返回执行状态值
    */
    function fill($point_x=0, $point_y=0)
    {
        return imagefill($this->Canvas, 0, 0, $this->Color);
    }

    /**
     * 输出图像
     * @access public
     * @param string $type 图片类型
     * @param string|null $uri 存储（相对，根地址：项目根目录）路径 默认值 null
     * @return void
     */
    function output($type="jpg",$uri=null)
    {
        $_uri = null;
        if(!is_null($uri))
            $_uri = replace(ROOT.DS.$_uri);
        switch($type){
            case "png":
                imagepng($this->Canvas,$_uri);
                break;
            case "bmp":
                imagebmp($this->Canvas,$_uri);
                break;
            case "gif":
                imagegif($this->Canvas,$_uri);
                break;
            case "gd":
                imagegd($this->Canvas,$_uri);
                break;
            case "gd2":
                imagegd2($this->Canvas,$_uri);
                break;
            case "wbmp":
                imagewbmp($this->Canvas,$_uri);
                break;
            case "webp":
                imagewebp($this->Canvas,$_uri);
                break;
            case "xbm":
                imagexbm($this->Canvas,$_uri);
                break;
            default:
                imagejpeg($this->Canvas,$_uri);
                break;
        }
        imagedestroy($this->Canvas);
    }
}